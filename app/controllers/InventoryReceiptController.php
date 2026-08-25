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
        $quickQty = isset($_GET['qty']) ? (float)$_GET['qty'] : null;
        $quickIngredient = null;
        if ($quickIngredientId) {
            $quickIngredient = $this->ingredientModel->find($quickIngredientId);
        }

        // Lấy old input và form errors nếu có từ session
        $oldInput = $_SESSION['old_input'] ?? null;
        $formErrors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['old_input'], $_SESSION['form_errors']);

        $this->view('inventory_receipt/create', [
            'ingredients' => $ingredients,
            'quickIngredient' => $quickIngredient,
            'quickQty' => $quickQty,
            'oldInput' => $oldInput,
            'formErrors' => $formErrors
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

        $oldInput = $_SESSION['old_input'] ?? null;
        $formErrors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['old_input'], $_SESSION['form_errors']);

        $this->view('inventory_receipt/create', [
            'ingredients' => $ingredients,
            'fromRestock' => true,
            'quickIngredient' => null,
            'quickQty' => null,
            'oldInput' => $oldInput,
            'formErrors' => $formErrors
        ]);
    }

    /**
     * Validate chi tiết dữ liệu phiếu nhập kho
     */
    private function validateReceiptData($data)
    {
        $errors = [];

        // 1. Validate Nhà cung cấp (supplier)
        $supplier = isset($data['supplier']) ? trim($data['supplier']) : '';
        if (isset($data['supplier']) && $data['supplier'] !== '' && $supplier === '') {
            $errors[] = 'Tên nhà cung cấp không được chỉ chứa khoảng trắng.';
        } elseif ($supplier !== '') {
            $len = mb_strlen($supplier, 'UTF-8');
            if ($len < 2) {
                $errors[] = 'Tên nhà cung cấp phải có tối thiểu 2 ký tự.';
            } elseif ($len > 100) {
                $errors[] = 'Tên nhà cung cấp không được vượt quá 100 ký tự.';
            }
        }

        // 2. Validate Ngày nhập (receipt_date)
        $receiptDate = isset($data['receipt_date']) ? trim($data['receipt_date']) : '';
        if (empty($receiptDate)) {
            $errors[] = 'Ngày nhập kho là bắt buộc, không được để trống.';
        } else {
            // Kiểm tra định dạng YYYY-MM-DD
            if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $receiptDate, $matches)) {
                $errors[] = 'Định dạng ngày nhập không hợp lệ (định dạng chuẩn: YYYY-MM-DD).';
            } else {
                $year = (int)$matches[1];
                $month = (int)$matches[2];
                $day = (int)$matches[3];
                if (!checkdate($month, $day, $year)) {
                    $errors[] = 'Ngày nhập không hợp lệ trong lịch (VD: ngày không tồn tại hoặc sai năm nhuận).';
                } else {
                    $currentDate = date('Y-m-d');
                    if ($receiptDate > $currentDate) {
                        $errors[] = 'Ngày nhập kho không được lớn hơn ngày hiện tại (' . date('d/m/Y') . ').';
                    } elseif ($receiptDate < '2020-01-01') {
                        $errors[] = 'Ngày nhập kho không được nhỏ hơn ngày 01/01/2020.';
                    }
                }
            }
        }

        // 3. Validate Ghi chú (note)
        $note = isset($data['note']) ? trim($data['note']) : '';
        if ($note !== '') {
            if (mb_strlen($note, 'UTF-8') > 500) {
                $errors[] = 'Ghi chú không được vượt quá 500 ký tự.';
            }
        }

        // 4. Validate Danh sách chi tiết nguyên liệu (Detail Items)
        if (isset($data['ingredient_id']) && !is_array($data['ingredient_id'])) {
            $data['ingredient_id'] = [$data['ingredient_id']];
        }
        if (isset($data['qty']) && !is_array($data['qty'])) {
            $data['qty'] = [$data['qty']];
        }
        if (isset($data['unit_price']) && !is_array($data['unit_price'])) {
            $data['unit_price'] = [$data['unit_price']];
        }

        $ingredientIds = $data['ingredient_id'] ?? [];
        $qtys = $data['qty'] ?? [];
        $unitPrices = $data['unit_price'] ?? [];

        if (!is_array($ingredientIds) || count($ingredientIds) === 0) {
            $errors[] = 'Phiếu nhập kho phải có ít nhất một dòng nguyên liệu.';
            return ['errors' => $errors, 'items' => []];
        }

        $allIngredients = $this->ingredientModel->all();
        $ingredientMap = [];
        foreach ($allIngredients as $ing) {
            $ingredientMap[$ing['id']] = $ing;
        }

        $validItems = [];
        $seenIngredients = [];
        $hasAnyValidRow = false;

        $rowCount = count($ingredientIds);
        for ($i = 0; $i < $rowCount; $i++) {
            $rowNum = $i + 1;
            $ingId = isset($ingredientIds[$i]) ? trim($ingredientIds[$i]) : '';
            $rawQty = isset($qtys[$i]) ? trim((string)$qtys[$i]) : '';
            $rawPrice = isset($unitPrices[$i]) ? trim((string)$unitPrices[$i]) : '';

            // Kiểm tra nguyên liệu
            if ($ingId === '') {
                $errors[] = "Dòng {$rowNum}: Vui lòng chọn nguyên liệu.";
            } elseif (!isset($ingredientMap[$ingId])) {
                $errors[] = "Dòng {$rowNum}: Nguyên liệu (ID: {$ingId}) không tồn tại trong hệ thống.";
            } else {
                $ingName = $ingredientMap[$ingId]['name'] ?? "ID $ingId";
                // Kiểm tra trùng lặp nguyên liệu giữa các dòng
                if (isset($seenIngredients[$ingId])) {
                    $prevRow = $seenIngredients[$ingId];
                    $errors[] = "Dòng {$rowNum}: Nguyên liệu '{$ingName}' bị trùng lặp với dòng {$prevRow}. Vui lòng gộp số lượng hoặc chọn nguyên liệu khác.";
                } else {
                    $seenIngredients[$ingId] = $rowNum;
                }
            }

            // Kiểm tra số lượng
            if ($rawQty === '' || !is_numeric($rawQty)) {
                $errors[] = "Dòng {$rowNum}: Số lượng nhập không hợp lệ hoặc chưa được điền.";
            } else {
                $qty = (float)$rawQty;
                if ($qty <= 0) {
                    $errors[] = "Dòng {$rowNum}: Số lượng nhập phải lớn hơn 0.";
                } elseif ($qty > 99999) {
                    $errors[] = "Dòng {$rowNum}: Số lượng nhập không được vượt quá 99.999.";
                } else {
                    // Kiểm tra số chữ số thập phân (tối đa 3 chữ số)
                    $dotPos = strpos($rawQty, '.');
                    if ($dotPos !== false) {
                        $decPart = substr($rawQty, $dotPos + 1);
                        if (strlen($decPart) > 3) {
                            $errors[] = "Dòng {$rowNum}: Số lượng chỉ cho phép tối đa 3 chữ số thập phân.";
                        }
                    }
                }
            }

            // Kiểm tra đơn giá
            if ($rawPrice === '' || !is_numeric($rawPrice)) {
                $errors[] = "Dòng {$rowNum}: Đơn giá không hợp lệ hoặc chưa được điền.";
            } else {
                $price = (float)$rawPrice;
                if ($price < 0) {
                    $errors[] = "Dòng {$rowNum}: Đơn giá không được âm.";
                } elseif ($price > 1000000000) {
                    $errors[] = "Dòng {$rowNum}: Đơn giá không được vượt quá 1.000.000.000 đ.";
                }
            }

            if (!empty($ingId) && isset($ingredientMap[$ingId]) && is_numeric($rawQty) && (float)$rawQty > 0 && is_numeric($rawPrice) && (float)$rawPrice >= 0) {
                $validItems[] = [
                    'ingredient_id' => (int)$ingId,
                    'qty' => (float)$rawQty,
                    'unit_price' => (float)$rawPrice
                ];
                $hasAnyValidRow = true;
            }
        }

        if (!$hasAnyValidRow && empty($errors)) {
            $errors[] = 'Vui lòng nhập ít nhất một dòng nguyên liệu hợp lệ với số lượng lớn hơn 0.';
        }

        return [
            'errors' => $errors,
            'items' => $validItems
        ];
    }

    public function store()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $data = $this->getPost();

        // Validate toàn diện
        $validation = $this->validateReceiptData($data);
        $errors = $validation['errors'];
        $validItems = $validation['items'];

        if (!empty($errors)) {
            if ($this->isAjax()) {
                $this->json([
                    'success' => false,
                    'message' => implode(' | ', $errors),
                    'errors' => $errors
                ], 422);
                return;
            }

            $_SESSION['old_input'] = $data;
            $_SESSION['form_errors'] = $errors;
            setFlash('error', 'Có ' . count($errors) . ' lỗi cần kiểm tra lại:<br>• ' . implode('<br>• ', $errors));
            $this->redirect('inventory_receipt/create');
            return;
        }

        // Thực hiện lưu vào cơ sở dữ liệu với Transaction
        $db = getDB();
        $db->beginTransaction();

        try {
            $receipt = [
                'created_by' => $user['id'] ?? null,
                'supplier' => !empty(trim($data['supplier'] ?? '')) ? trim($data['supplier']) : null,
                'receipt_date' => $data['receipt_date'],
                'status' => 'pending',
                'note' => !empty(trim($data['note'] ?? '')) ? trim($data['note']) : null
            ];

            $receiptId = $this->model->insert($receipt);

            if (!$receiptId) {
                throw new Exception("Không thể khởi tạo bản ghi phiếu nhập kho.");
            }

            $stmt = $db->prepare('INSERT INTO inventory_receipt_detail (receipt_id, ingredient_id, qty, unit_price) VALUES (?, ?, ?, ?)');

            foreach ($validItems as $item) {
                $stmt->execute([$receiptId, $item['ingredient_id'], $item['qty'], $item['unit_price']]);
            }

            $db->commit();

            logAudit('create', 'inventory_receipt', "Tạo phiếu nhập kho #{$receiptId} thành công");

            if ($this->isAjax()) {
                $this->json([
                    'success' => true,
                    'message' => "Tạo phiếu nhập kho #{$receiptId} thành công",
                    'receipt_id' => $receiptId
                ]);
                return;
            }

            setFlash('success', "Tạo phiếu nhập kho #{$receiptId} thành công (Trạng thái: Chờ duyệt)");
            $this->redirect('inventory_receipt');
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Lỗi tạo phiếu nhập kho: " . $e->getMessage());

            if ($this->isAjax()) {
                $this->json([
                    'success' => false,
                    'message' => 'Lỗi hệ thống khi tạo phiếu nhập: ' . $e->getMessage()
                ], 500);
                return;
            }

            $_SESSION['old_input'] = $data;
            setFlash('error', 'Có lỗi xảy ra trong quá trình lưu dữ liệu: ' . $e->getMessage());
            $this->redirect('inventory_receipt/create');
        }
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
