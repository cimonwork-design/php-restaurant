<?php

/**
 * InventoryReceipt Controller - manage inventory receipts and details
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class InventoryReceiptController extends Controller
{
    private $model;
    private $ingredientModel;

    public function __construct()
    {
        $this->model = $this->model('InventoryReceipt');
        $this->ingredientModel = $this->model('Ingredient');
    }

    public function index()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $baseSql = "SELECT ir.*, u.fullname as creator FROM inventory_receipt ir LEFT JOIN users u ON ir.created_by = u.id ORDER BY ir.receipt_date DESC, ir.id DESC";
        $result = $this->model->paginate($baseSql, [], $page, $per);

        $this->view('inventory_receipt/index', [
            'items' => $result['data'],
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/inventory_receipt',
            'user' => $user
        ]);
    }

    public function create()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $ingredients = $this->ingredientModel->all('name', 'ASC');

        // Quick restock from stock_report
        $quickIngredientId = isset($_GET['ingredient_id']) ? (int)$_GET['ingredient_id'] : null;
        $quickQty = isset($_GET['qty']) ? (int)$_GET['qty'] : null;
        $quickIngredient = null;
        if ($quickIngredientId) {
            $quickIngredient = $this->ingredientModel->find($quickIngredientId);
        }

        $this->view('inventory_receipt/create', [
            'ingredients' => $ingredients,
            'quickIngredient' => $quickIngredient,
            'quickQty' => $quickQty
        ]);
    }

    /**
     * Create receipt from restock cart (multiple items)
     */
    public function create_from_restock()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $ingredients = $this->ingredientModel->all('name', 'ASC');

        // Restock cart is in sessionStorage (client-side), we'll pass it to view
        // View will pre-populate form from JavaScript/POST

        $this->view('inventory_receipt/create', [
            'ingredients' => $ingredients,
            'fromRestock' => true,
            'quickIngredient' => null,
            'quickQty' => null
        ]);
    }

    public function store()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $data = $this->getPost();
        $required = ['receipt_date'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('inventory_receipt/create');
            return;
        }

        $ingredientIds = $data['ingredient_id'] ?? [];
        $qtys = $data['qty'] ?? [];
        $unitPrices = $data['unit_price'] ?? [];
        $validItems = [];
        for ($i = 0; $i < count($ingredientIds); $i++) {
            $ing = $ingredientIds[$i];
            $q = isset($qtys[$i]) ? (float)$qtys[$i] : 0;
            $p = isset($unitPrices[$i]) ? (float)$unitPrices[$i] : 0;
            if (!empty($ing) && $q > 0) {
                $validItems[] = ['ingredient_id' => $ing, 'qty' => $q, 'unit_price' => $p];
            }
        }
        if (empty($validItems)) {
            setFlash('error', 'Vui lòng thêm ít nhất một nguyên liệu hợp lệ');
            $this->redirect('inventory_receipt/create');
            return;
        }

        $receipt = [
            'created_by' => $user['id'] ?? null,
            'supplier' => $data['supplier'] ?? null,
            'receipt_date' => $data['receipt_date'],
            'status' => 'pending',
            'note' => $data['note'] ?? null
        ];

        $receiptId = $this->model->insert($receipt);

        $db = getDB();
        $stmt = $db->prepare('INSERT INTO inventory_receipt_detail (receipt_id, ingredient_id, qty, unit_price) VALUES (?, ?, ?, ?)');

        foreach ($validItems as $item) {
            $stmt->execute([$receiptId, $item['ingredient_id'], $item['qty'], $item['unit_price']]);
        }

        setFlash('success', 'Tạo phiếu nhập kho thành công');
        $this->redirect('inventory_receipt');
    }

    public function edit($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('inventory_receipt');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Phiếu không tồn tại');
            $this->redirect('inventory_receipt');
            return;
        }
        if ($item['status'] === 'completed') {
            setFlash('error', 'Không thể sửa phiếu nhập đã hoàn thành');
            $this->redirect('inventory_receipt');
            return;
        }

        $details = $this->model->getDetails($id);
        $ingredients = $this->ingredientModel->all('name', 'ASC');

        $this->view('inventory_receipt/edit', ['item' => $item, 'details' => $details, 'ingredients' => $ingredients]);
    }

    public function update($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('inventory_receipt');
            return;
        }

        $data = $this->getPost();
        $existing = $this->model->find($id);
        if ($existing && $existing['status'] === 'completed') {
            setFlash('error', 'Không thể sửa phiếu nhập đã hoàn thành');
            $this->redirect('inventory_receipt');
            return;
        }

        $required = ['receipt_date'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('inventory_receipt/edit/' . $id);
            return;
        }

        $receipt = [
            'supplier' => $data['supplier'] ?? null,
            'receipt_date' => $data['receipt_date'],
            'note' => $data['note'] ?? null
        ];

        $this->model->update($id, $receipt);

        // replace details
        $db = getDB();
        $del = $db->prepare('DELETE FROM inventory_receipt_detail WHERE receipt_id = ?');
        $del->execute([$id]);

        $ingredientIds = $data['ingredient_id'] ?? [];
        $qtys = $data['qty'] ?? [];
        $unitPrices = $data['unit_price'] ?? [];

        $stmt = $db->prepare('INSERT INTO inventory_receipt_detail (receipt_id, ingredient_id, qty, unit_price) VALUES (?, ?, ?, ?)');
        for ($i = 0; $i < count($ingredientIds); $i++) {
            $ing = $ingredientIds[$i];
            $q = isset($qtys[$i]) ? (int)$qtys[$i] : 0;
            $p = isset($unitPrices[$i]) ? (float)$unitPrices[$i] : 0;
            if (empty($ing) || $q <= 0) continue;
            $stmt->execute([$id, $ing, $q, $p]);
        }

        setFlash('success', 'Cập nhật phiếu nhập thành công');
        $this->redirect('inventory_receipt');
    }

    public function delete($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('inventory_receipt');
            return;
        }

        $receipt = $this->model->find($id);
        if ($receipt && $receipt['status'] === 'completed') {
            setFlash('error', 'Không thể xóa phiếu nhập đã hoàn thành');
            $this->redirect('inventory_receipt');
            return;
        }

        $db = getDB();
        $del = $db->prepare('DELETE FROM inventory_receipt_detail WHERE receipt_id = ?');
        $del->execute([$id]);

        $this->model->delete($id);
        setFlash('success', 'Xóa phiếu nhập thành công');
        $this->redirect('inventory_receipt');
    }

    /**
     * Complete receipt and add to inventory (create inventory_log entries)
     */
    public function complete($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('inventory_receipt');
            return;
        }

        $db = getDB();

        // Get receipt
        $receipt = $this->model->find($id);
        if (!$receipt) {
            setFlash('error', 'Phiếu không tồn tại');
            $this->redirect('inventory_receipt');
            return;
        }

        // If already completed, skip
        if ($receipt['status'] === 'completed') {
            setFlash('warning', 'Phiếu này đã hoàn thành rồi');
            $this->redirect('inventory_receipt');
            return;
        }

        // Get all details
        $details = $this->model->getDetails($id);

        // Create inventory_log entries for each item
        $stmtLog = $db->prepare('INSERT INTO inventory_log (ingredient_id, qty_change, type, related_id, note, created_by) VALUES (?, ?, ?, ?, ?, ?)');
        $stmtPrice = $db->prepare('UPDATE ingredient SET purchase_price = ? WHERE id = ? AND ? > 0');

        foreach ($details as $detail) {
            $stmtLog->execute([
                $detail['ingredient_id'],
                (float)$detail['qty'],  // qty_change (positive for receipt)
                'receipt',            // type
                $id,                  // related_id (receipt id)
                'Nhập kho từ phiếu #' . $id,
                $user['id'] ?? null
            ]);
            $stmtPrice->execute([(float)$detail['unit_price'], $detail['ingredient_id'], (float)$detail['unit_price']]);
        }

        // Update receipt status to completed
        $this->model->update($id, ['status' => 'completed']);

        setFlash('success', 'Hoàn thành phiếu nhập kho - Số lượng đã được cập nhật');
        $this->redirect('inventory_receipt');
    }
}
