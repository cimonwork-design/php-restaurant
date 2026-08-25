<?php

/**
 * SaleOrder Controller - manage sale orders and details
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';
require_once BASE_PATH . '/helpers/FormValidation.php';

class SaleOrderController extends Controller
{
    private $model;
    private $menuModel;
    private $tableModel;
    private $recipeModel;

    public function __construct()
    {
        $this->model = $this->model('SaleOrder');
        $this->menuModel = $this->model('MenuItem');
        $this->tableModel = $this->model('RestaurantTable');
        $this->recipeModel = $this->model('Recipe');
    }

    public function index()
    {
        $user = $this->requireAuth();
        if (!$user) return;

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        $status = $_GET['status'] ?? '';
        $keyword = trim($_GET['q'] ?? '');

        $where = [];
        $params = [];
        if (in_array($status, ['open', 'served', 'paid', 'cancel'], true)) {
            $where[] = 'o.status = ?';
            $params[] = $status;
        }
        if ($keyword !== '') {
            $where[] = '(o.id = ? OR t.number LIKE ?)';
            $params[] = (int)$keyword;
            $params[] = '%' . $keyword . '%';
        }

        $baseSql = "
            SELECT o.*, t.number AS table_number, COALESCE(items.item_count, 0) AS item_count
            FROM sale_order o
            LEFT JOIN restaurant_table t ON o.table_id = t.id
            LEFT JOIN (
                SELECT sale_order_id, COUNT(*) AS item_count
                FROM sale_order_detail
                WHERE status != 'canceled'
                GROUP BY sale_order_id
            ) items ON items.sale_order_id = o.id
        ";
        if ($where) {
            $baseSql .= ' WHERE ' . implode(' AND ', $where);
        }
        $baseSql .= ' ORDER BY o.order_time DESC';

        $result = $this->model->paginate($baseSql, $params, $page, $per);

        $query = [];
        if ($status !== '') $query['status'] = $status;
        if ($keyword !== '') $query['q'] = $keyword;

        $this->view('sale_order/index', [
            'items' => $result['data'],
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/sale_order' . ($query ? '?' . http_build_query($query) : ''),
            'user' => $user,
            'filters' => ['status' => $status, 'q' => $keyword]
        ]);
    }

    public function create()
    {
        $user = $this->requireAuth();
        if (!$user) return;

        $tables = $this->tableModel->all('number', 'ASC');
        $menuItems = $this->menuModel->all('name', 'ASC');
        $this->view('sale_order/create', ['tables' => $tables, 'menuItems' => $menuItems, 'user' => $user]);
    }

    private function normalizeDatetime($value)
    {
        if (!$value) return date('Y-m-d H:i:s');
        if (strpos($value, 'T') !== false) {
            return str_replace('T', ' ', $value) . ':00';
        }
        return $value;
    }

    private function calculateGrandTotal($subtotal, $discount, $vatRate)
    {
        $subtotal = max(0, (float)$subtotal);
        $discount = max(0, (float)$discount);
        $vatRate = max(0, (float)$vatRate);
        $afterDiscount = max(0, $subtotal - $discount);
        return $afterDiscount + ($afterDiscount * $vatRate / 100);
    }

    private function buildDetailsFromQty($qtys)
    {
        $details = [];
        $subtotal = 0;

        foreach ($qtys as $menuId => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) continue;

            $menu = $this->menuModel->find($menuId);
            if (!$menu) continue;

            $price = (float)$menu['price'];
            $details[] = [
                'menu_id' => (int)$menuId,
                'qty' => $qty,
                'price' => $price,
                'name' => $menu['name']
            ];
            $subtotal += $price * $qty;
        }

        return [$details, $subtotal];
    }

    private function collectInventoryWarnings($details)
    {
        $inventoryWarnings = [];
        foreach ($details as $d) {
            $check = $this->recipeModel->checkInventoryForMenu($d['menu_id'], $d['qty']);
            if (!$check['sufficient']) {
                $inventoryWarnings[] = [
                    'menu_name' => $d['name'],
                    'menu_qty' => $d['qty'],
                    'missing' => $check['missing']
                ];
            }
        }
        return $inventoryWarnings;
    }

    private function validateOrderInput($qtys, $orderTime, $discount, $vatRate, $subtotal)
    {
        foreach ($qtys as $menuId => $qty) {
            if (!filter_var($qty, FILTER_VALIDATE_INT) || (int)$qty <= 0) {
                return formMessage('quantity_invalid');
            }
            if (!$this->menuModel->find((int)$menuId)) {
                return formMessage('menu_not_found');
            }
        }

        if ($orderTime !== '' && strtotime($orderTime) === false) {
            return formMessage('order_time_invalid');
        }
        if ($discount < 0 || $discount >= $subtotal) {
            return formMessage('discount_invalid');
        }
        if ($vatRate < 0 || $vatRate > 100) {
            return formMessage('vat_invalid');
        }

        return null;
    }

    private function validateRawOrderItems($qtys)
    {
        foreach ($qtys as $menuId => $qty) {
            if (!filter_var($qty, FILTER_VALIDATE_INT) || (int)$qty <= 0) {
                return formMessage('quantity_invalid');
            }
            if (!$this->menuModel->find((int)$menuId)) {
                return formMessage('menu_not_found');
            }
        }

        return null;
    }

    private function getOrderSubtotal($orderId)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT COALESCE(SUM(qty * price), 0) AS subtotal FROM sale_order_detail WHERE sale_order_id = ? AND status != 'canceled'");
        $stmt->execute([$orderId]);
        return (float)($stmt->fetch()['subtotal'] ?? 0);
    }

    private function hasAutoIssueForOrder($orderId)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM inventory_issue WHERE issue_type = 'sale' AND note = ? LIMIT 1");
        $stmt->execute(["Auto-generated from sale order #$orderId"]);
        return (bool)$stmt->fetch();
    }

    private function getExistingOrderDetailsForInventory($orderId)
    {
        $details = [];
        foreach ($this->model->getDetails($orderId) as $row) {
            $details[] = [
                'menu_id' => (int)$row['menu_id'],
                'qty' => (int)$row['qty'],
                'name' => $row['menu_name']
            ];
        }
        return $details;
    }

    public function store()
    {
        $user = $this->requireAuth();
        if (!$user) return;

        $data = $this->getPost();
        if (empty($data['table_id'])) {
            setFlash('error', formMessage('table_required'));
            $this->redirect('sale_order/create');
            return;
        }
        $rawItemsError = $this->validateRawOrderItems($data['qty'] ?? []);
        if ($rawItemsError) {
            setFlash('error', $rawItemsError);
            $this->redirect('sale_order/create');
            return;
        }
        [$details, $subtotal] = $this->buildDetailsFromQty($data['qty'] ?? []);

        if (empty($details)) {
            setFlash('error', formMessage('order_empty'));
            $this->redirect('sale_order/create');
            return;
        }

        $inventoryWarnings = $this->collectInventoryWarnings($details);
        if ($inventoryWarnings) {
            $_SESSION['inventory_warnings'] = $inventoryWarnings;
            setFlash('warning', 'Một số nguyên liệu không đủ. Vui lòng kiểm tra lại.');
            $this->redirect('sale_order/create');
            return;
        }

        if (!empty($data['table_id'])) {
            $table = $this->tableModel->find((int)$data['table_id']);
            if (!$table) {
                setFlash('error', formMessage('table_invalid'));
                $this->redirect('sale_order/create');
                return;
            }
            if ($table['status'] !== 'free') {
                setFlash('error', formMessage('table_not_free'));
                $this->redirect('sale_order/create');
                return;
            }
        }

        $discount = (float)($data['discount'] ?? 0);
        $vatRate = (float)($data['vat_rate'] ?? 0);
        $inputError = $this->validateOrderInput($data['qty'] ?? [], $data['order_time'] ?? '', $discount, $vatRate, $subtotal);
        if ($inputError) {
            setFlash('error', $inputError);
            $this->redirect('sale_order/create');
            return;
        }
        $total = $this->calculateGrandTotal($subtotal, $discount, $vatRate);
        $orderTime = $this->normalizeDatetime($data['order_time'] ?? '');

        $db = getDB();
        $db->beginTransaction();
        try {
            $orderId = $this->model->insert([
                'table_id' => !empty($data['table_id']) ? $data['table_id'] : null,
                'waiter_id' => $user['id'] ?? null,
                'cashier_id' => null,
                'order_time' => $orderTime,
                'status' => 'open',
                'discount' => $discount,
                'vat_rate' => $vatRate,
                'total_amount' => $total
            ]);

            $stmt = $db->prepare('INSERT INTO sale_order_detail (sale_order_id, menu_id, qty, price) VALUES (?, ?, ?, ?)');
            foreach ($details as $d) {
                $stmt->execute([$orderId, $d['menu_id'], $d['qty'], $d['price']]);
            }

            if (!empty($data['table_id'])) {
                $this->tableModel->update($data['table_id'], ['status' => 'occupied']);
                logAudit('update', 'restaurant_table', "Table #{$data['table_id']} occupied by order #{$orderId}");
            }

            $db->commit();
            setFlash('success', 'Tạo đơn hàng thành công');
            $this->redirect('sale_order');
        } catch (Exception $e) {
            $db->rollBack();
            error_log($e->getMessage());
            setFlash('error', 'Không thể tạo đơn hàng');
            $this->redirect('sale_order/create');
        }
    }

    public function edit($id = null)
    {
        $user = $this->requireAuth();
        if (!$user) return;

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }

        $details = $this->model->getDetails($id);
        $detailMap = [];
        foreach ($details as $d) {
            $detailMap[$d['menu_id']] = $d['qty'];
        }

        $tables = $this->tableModel->all('number', 'ASC');
        $menuItems = $this->menuModel->all('name', 'ASC');

        $this->view('sale_order/edit', [
            'item' => $item,
            'details' => $details,
            'detailMap' => $detailMap,
            'tables' => $tables,
            'menuItems' => $menuItems,
            'subtotal' => $this->getOrderSubtotal($id),
            'user' => $user
        ]);
    }

    public function update($id = null)
    {
        $user = $this->requireAuth();
        if (!$user) return;

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        $existingOrder = $this->model->find($id);
        if (!$existingOrder) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }
        if ($existingOrder['status'] !== 'open') {
            setFlash('error', 'Chỉ có thể chỉnh sửa đơn đang mở');
            $this->redirect('sale_order');
            return;
        }

        $data = $this->getPost();
        $rawItemsError = $this->validateRawOrderItems($data['qty'] ?? []);
        if ($rawItemsError) {
            setFlash('error', $rawItemsError);
            $this->redirect('sale_order/edit/' . $id);
            return;
        }
        [$details, $subtotal] = $this->buildDetailsFromQty($data['qty'] ?? []);
        if (empty($details)) {
            setFlash('error', 'Vui lòng chọn ít nhất một món');
            $this->redirect('sale_order/edit/' . $id);
            return;
        }

        $inventoryWarnings = $this->collectInventoryWarnings($details);
        if ($inventoryWarnings) {
            $_SESSION['inventory_warnings'] = $inventoryWarnings;
            setFlash('warning', 'Một số nguyên liệu không đủ. Vui lòng kiểm tra lại.');
            $this->redirect('sale_order/edit/' . $id);
            return;
        }

        $discount = (float)($data['discount'] ?? 0);
        $vatRate = (float)($data['vat_rate'] ?? 0);
        $inputError = $this->validateOrderInput($data['qty'] ?? [], $data['order_time'] ?? '', $discount, $vatRate, $subtotal);
        if ($inputError) {
            setFlash('error', $inputError);
            $this->redirect('sale_order/edit/' . $id);
            return;
        }
        $total = $this->calculateGrandTotal($subtotal, $discount, $vatRate);
        $newTableId = !empty($data['table_id']) ? $data['table_id'] : null;
        if ($newTableId) {
            $table = $this->tableModel->find((int)$newTableId);
            if (!$table) {
                setFlash('error', formMessage('table_invalid'));
                $this->redirect('sale_order/edit/' . $id);
                return;
            }
            if ($table['status'] !== 'free' && $table['id'] != ($existingOrder['table_id'] ?? null)) {
                setFlash('error', formMessage('table_not_free'));
                $this->redirect('sale_order/edit/' . $id);
                return;
            }
        }

        $db = getDB();
        $db->beginTransaction();
        try {
            $db->prepare('DELETE FROM sale_order_detail WHERE sale_order_id = ?')->execute([$id]);
            $stmt = $db->prepare('INSERT INTO sale_order_detail (sale_order_id, menu_id, qty, price) VALUES (?, ?, ?, ?)');
            foreach ($details as $d) {
                $stmt->execute([$id, $d['menu_id'], $d['qty'], $d['price']]);
            }

            $this->model->update($id, [
                'table_id' => $newTableId,
                'waiter_id' => $user['id'] ?? null,
                'order_time' => $this->normalizeDatetime($data['order_time'] ?? ''),
                'status' => 'open',
                'discount' => $discount,
                'vat_rate' => $vatRate,
                'total_amount' => $total
            ]);

            if (!empty($existingOrder['table_id']) && $existingOrder['table_id'] != $newTableId) {
                $this->tableModel->update($existingOrder['table_id'], ['status' => 'free']);
            }
            if (!empty($newTableId)) {
                $this->tableModel->update($newTableId, ['status' => 'occupied']);
            }

            $db->commit();
            setFlash('success', 'Cập nhật đơn hàng thành công');
            $this->redirect('sale_order');
        } catch (Exception $e) {
            $db->rollBack();
            error_log($e->getMessage());
            setFlash('error', 'Không thể cập nhật đơn hàng');
            $this->redirect('sale_order/edit/' . $id);
        }
    }

    public function delete($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        $order = $this->model->find($id);
        if ($order && in_array($order['status'], ['served', 'paid'], true)) {
            setFlash('error', 'Không thể xóa đơn đã phục vụ hoặc đã thanh toán');
            $this->redirect('sale_order');
            return;
        }

        $db = getDB();
        $db->prepare('DELETE FROM sale_order_detail WHERE sale_order_id = ?')->execute([$id]);
        $this->model->delete($id);
        if ($order && !empty($order['table_id'])) {
            $this->tableModel->update($order['table_id'], ['status' => 'free']);
        }

        setFlash('success', 'Xóa đơn hàng thành công');
        $this->redirect('sale_order');
    }

    public function complete($id = null)
    {
        $user = $this->requireAuth();
        if (!$user) return;

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        $order = $this->model->find($id);
        if (!$order) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }
        if ($order['status'] !== 'open') {
            setFlash('error', 'Chỉ có thể hoàn thành đơn đang mở');
            $this->redirect('sale_order');
            return;
        }
        if ($this->hasAutoIssueForOrder($id)) {
            setFlash('warning', 'Đơn này đã được xuất kho trước đó');
            $this->redirect('sale_order');
            return;
        }

        $inventoryWarnings = $this->collectInventoryWarnings($this->getExistingOrderDetailsForInventory($id));
        if ($inventoryWarnings) {
            $_SESSION['inventory_warnings'] = $inventoryWarnings;
            setFlash('warning', 'Một số nguyên liệu không đủ để hoàn thành đơn.');
            $this->redirect('sale_order');
            return;
        }

        $this->createInventoryIssueFromOrder($id, $user);
        $this->model->update($id, ['status' => 'served']);
        logAudit('update', 'sale_order', "Marked order #$id as served");

        setFlash('success', 'Đơn hàng đã hoàn thành phục vụ và đã trừ kho.');
        $this->redirect('sale_order');
    }

    public function pay($id = null)
    {
        $user = $this->requireAuth();
        if (!$user) return;

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        $order = $this->model->find($id);
        if (!$order) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }
        if (!in_array($order['status'], ['open', 'served'], true)) {
            setFlash('error', 'Trạng thái đơn không hợp lệ để thanh toán');
            $this->redirect('sale_order');
            return;
        }

        if ($order['status'] === 'open') {
            if ($this->hasAutoIssueForOrder($id)) {
                setFlash('warning', 'Đơn này đã được xuất kho trước đó');
                $this->redirect('sale_order');
                return;
            }
            $inventoryWarnings = $this->collectInventoryWarnings($this->getExistingOrderDetailsForInventory($id));
            if ($inventoryWarnings) {
                $_SESSION['inventory_warnings'] = $inventoryWarnings;
                setFlash('warning', 'Một số nguyên liệu không đủ để thanh toán đơn.');
                $this->redirect('sale_order');
                return;
            }
            $this->createInventoryIssueFromOrder($id, $user);
        }

        $this->model->update($id, [
            'status' => 'paid',
            'cashier_id' => $user['id']
        ]);

        if (!empty($order['table_id'])) {
            $this->tableModel->update($order['table_id'], ['status' => 'free']);
            logAudit('update', 'restaurant_table', "Table #{$order['table_id']} freed after payment for order #$id");
        }

        logAudit('update', 'sale_order', "Paid order #$id - amount: " . $order['total_amount']);
        setFlash('success', 'Thanh toán đơn hàng thành công');
        $this->redirect('sale_order/invoice/' . $id);
    }

    public function cancel($id = null)
    {
        $user = $this->requireAuth();
        if (!$user) return;

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        $order = $this->model->find($id);
        if (!$order) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }
        if ($order['status'] !== 'open') {
            setFlash('error', 'Chỉ có thể hủy đơn đang mở');
            $this->redirect('sale_order');
            return;
        }

        $this->model->update($id, ['status' => 'cancel']);
        if (!empty($order['table_id'])) {
            $this->tableModel->update($order['table_id'], ['status' => 'free']);
        }

        logAudit('update', 'sale_order', "Canceled order #$id");
        setFlash('success', 'Đơn hàng đã bị hủy');
        $this->redirect('sale_order');
    }

    public function addItem($id = null)
    {
        $user = $this->requireAuth();
        if (!$user) return;

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        $order = $this->model->find($id);
        if (!$order) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }
        if ($order['status'] !== 'open') {
            setFlash('error', 'Chỉ có thể thêm món vào đơn đang mở');
            $this->redirect('sale_order');
            return;
        }

        $menuItems = $this->menuModel->all('name', 'ASC');
        $this->view('sale_order/addItem', [
            'order' => $order,
            'menuItems' => $menuItems,
            'user' => $user
        ]);
    }

    public function saveAddItems($id = null)
    {
        $user = $this->requireAuth();
        if (!$user) return;

        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('sale_order');
            return;
        }

        $order = $this->model->find($id);
        if (!$order || $order['status'] !== 'open') {
            setFlash('error', 'Chỉ có thể thêm món vào đơn đang mở');
            $this->redirect('sale_order');
            return;
        }

        $items = json_decode($_POST['items'] ?? '[]', true);
        $qtys = [];
        foreach ($items ?: [] as $item) {
            $menuId = (int)($item['menu_id'] ?? 0);
            $qty = (int)($item['qty'] ?? 0);
            if ($menuId > 0 && $qty > 0) {
                $qtys[$menuId] = ($qtys[$menuId] ?? 0) + $qty;
            }
        }

        [$details] = $this->buildDetailsFromQty($qtys);
        if (empty($details)) {
            setFlash('error', 'Không có món ăn hợp lệ');
            $this->redirect('sale_order/addItem/' . $id);
            return;
        }

        $inventoryWarnings = $this->collectInventoryWarnings($details);
        if ($inventoryWarnings) {
            $_SESSION['inventory_warnings'] = $inventoryWarnings;
            setFlash('warning', 'Một số nguyên liệu không đủ. Vui lòng kiểm tra lại.');
            $this->redirect('sale_order/addItem/' . $id);
            return;
        }

        $db = getDB();
        $stmt = $db->prepare('INSERT INTO sale_order_detail (sale_order_id, menu_id, qty, price, status) VALUES (?, ?, ?, ?, ?)');
        foreach ($details as $item) {
            $stmt->execute([$id, $item['menu_id'], $item['qty'], $item['price'], 'ordered']);
        }

        $newSubtotal = $this->getOrderSubtotal($id);
        $newTotal = $this->calculateGrandTotal($newSubtotal, $order['discount'] ?? 0, $order['vat_rate'] ?? 0);
        $this->model->update($id, ['total_amount' => $newTotal]);

        logAudit('update', 'sale_order', "Added items to order #$id");
        setFlash('success', 'Đã thêm món ăn vào đơn hàng');
        $this->redirect('sale_order');
    }

    private function createInventoryIssueFromOrder($orderId, $user)
    {
        try {
            $db = getDB();

            $stmt = $db->prepare('SELECT sale_order_detail.*, recipe.ingredient_id, recipe.qty as ingredient_qty
                                  FROM sale_order_detail
                                  LEFT JOIN recipe ON sale_order_detail.menu_id = recipe.menu_id
                                  WHERE sale_order_detail.sale_order_id = ? AND sale_order_detail.status != "canceled"');
            $stmt->execute([$orderId]);
            $details = $stmt->fetchAll();

            $ingredientMap = [];
            foreach ($details as $detail) {
                if (!$detail['ingredient_id'] || !$detail['ingredient_qty']) continue;
                $ingId = $detail['ingredient_id'];
                $neededQty = $detail['ingredient_qty'] * $detail['qty'];
                $ingredientMap[$ingId] = ($ingredientMap[$ingId] ?? 0) + $neededQty;
            }

            if (!$ingredientMap) return;

            $stmtIssue = $db->prepare('INSERT INTO inventory_issue (created_by, issue_type, issue_date, status, note) VALUES (?, ?, ?, ?, ?)');
            $stmtIssue->execute([$user['id'] ?? null, 'sale', date('Y-m-d'), 'completed', "Auto-generated from sale order #$orderId"]);
            $issueId = $db->lastInsertId();

            $stmtDetail = $db->prepare('INSERT INTO inventory_issue_detail (issue_id, ingredient_id, qty) VALUES (?, ?, ?)');
            $stmtLog = $db->prepare('INSERT INTO inventory_log (ingredient_id, qty_change, type, related_id, created_by, created_at) VALUES (?, ?, ?, ?, ?, NOW())');

            foreach ($ingredientMap as $ingId => $qty) {
                $stmtDetail->execute([$issueId, $ingId, $qty]);
                $stmtLog->execute([$ingId, -$qty, 'issue', $issueId, $user['id'] ?? null]);
            }

            logAudit('create', 'inventory_issue', "Auto-created issue #$issueId from order #$orderId");
        } catch (Exception $e) {
            error_log("Error creating inventory issue from order: " . $e->getMessage());
            throw $e;
        }
    }

    public function invoice($id = null)
    {
        $user = $this->requireAuth();
        if (!$user) return;

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        $db = getDB();
        $stmt = $db->prepare("
            SELECT so.*, t.number AS table_number, waiter.fullname AS waiter_name, cashier.fullname AS cashier_name
            FROM sale_order so
            LEFT JOIN restaurant_table t ON t.id = so.table_id
            LEFT JOIN users waiter ON waiter.id = so.waiter_id
            LEFT JOIN users cashier ON cashier.id = so.cashier_id
            WHERE so.id = ?
        ");
        $stmt->execute([$id]);
        $order = $stmt->fetch();

        if (!$order) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }

        $this->view('sale_order/invoice', [
            'user' => $user,
            'order' => $order,
            'details' => $this->model->getDetails($id),
            'subtotal' => $this->getOrderSubtotal($id)
        ]);
    }
}
