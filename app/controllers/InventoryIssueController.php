<?php

/**
 * InventoryIssue Controller - manage inventory issues (xuất kho) and details
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class InventoryIssueController extends Controller
{
    private $model;
    private $ingredientModel;

    public function __construct()
    {
        $this->model = $this->model('InventoryIssue');
        $this->ingredientModel = $this->model('Ingredient');
    }

    public function index()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $baseSql = "SELECT ii.*, u.fullname as creator FROM inventory_issue ii LEFT JOIN users u ON ii.created_by = u.id ORDER BY ii.issue_date DESC, ii.id DESC";
        $result = $this->model->paginate($baseSql, [], $page, $per);

        $this->view('inventory_issue/index', [
            'items' => $result['data'],
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/inventory_issue',
            'user' => $user
        ]);
    }

    public function create()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $ingredients = $this->ingredientModel->all('name', 'ASC');
        $this->view('inventory_issue/create', ['ingredients' => $ingredients]);
    }

    public function store()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $data = $this->getPost();
        $required = ['issue_date'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('inventory_issue/create');
            return;
        }

        $issue = [
            'created_by' => $user['id'] ?? null,
            'issue_type' => $data['issue_type'] ?? 'manual',
            'issue_date' => $data['issue_date'],
            'note' => $data['note'] ?? null
        ];

        // insert details arrays: ingredient_id[], qty[]
        $ingredientIds = $data['ingredient_id'] ?? [];
        $qtys = $data['qty'] ?? [];
        $validItems = [];
        for ($i = 0; $i < count($ingredientIds); $i++) {
            $ing = $ingredientIds[$i];
            $q = isset($qtys[$i]) ? (float)$qtys[$i] : 0;
            if (!empty($ing) && $q > 0) {
                $validItems[] = ['ingredient_id' => $ing, 'qty' => $q];
            }
        }
        if (empty($validItems)) {
            setFlash('error', 'Vui lòng thêm ít nhất một nguyên liệu hợp lệ');
            $this->redirect('inventory_issue/create');
            return;
        }

        $issueId = $this->model->insert($issue);

        $db = getDB();
        $stmt = $db->prepare('INSERT INTO inventory_issue_detail (issue_id, ingredient_id, qty) VALUES (?, ?, ?)');

        foreach ($validItems as $item) {
            $stmt->execute([$issueId, $item['ingredient_id'], $item['qty']]);
        }

        setFlash('success', 'Tạo phiếu xuất kho thành công');
        $this->redirect('inventory_issue');
    }

    public function edit($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('inventory_issue');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Phiếu không tồn tại');
            $this->redirect('inventory_issue');
            return;
        }

        $details = $this->model->getDetails($id);
        $ingredients = $this->ingredientModel->all('name', 'ASC');

        $this->view('inventory_issue/edit', ['item' => $item, 'details' => $details, 'ingredients' => $ingredients]);
    }

    public function update($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('inventory_issue');
            return;
        }

        $data = $this->getPost();

        $existing = $this->model->find($id);
        if ($existing && $existing['status'] === 'completed') {
            setFlash('error', 'Không thể sửa phiếu xuất đã hoàn thành');
            $this->redirect('inventory_issue');
            return;
        }

        $required = ['issue_date'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('inventory_issue/edit/' . $id);
            return;
        }

        $issue = [
            'issue_type' => $data['issue_type'] ?? 'manual',
            'issue_date' => $data['issue_date'],
            'note' => $data['note'] ?? null
        ];

        $this->model->update($id, $issue);

        // replace details
        $db = getDB();
        $del = $db->prepare('DELETE FROM inventory_issue_detail WHERE issue_id = ?');
        $del->execute([$id]);

        $ingredientIds = $data['ingredient_id'] ?? [];
        $qtys = $data['qty'] ?? [];

        $stmt = $db->prepare('INSERT INTO inventory_issue_detail (issue_id, ingredient_id, qty) VALUES (?, ?, ?)');
        for ($i = 0; $i < count($ingredientIds); $i++) {
            $ing = $ingredientIds[$i];
            $q = isset($qtys[$i]) ? (int)$qtys[$i] : 0;
            if (empty($ing) || $q <= 0) continue;
            $stmt->execute([$id, $ing, $q]);
        }

        setFlash('success', 'Cập nhật phiếu xuất thành công');
        $this->redirect('inventory_issue');
    }

    public function delete($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('inventory_issue');
            return;
        }

        $issue = $this->model->find($id);
        if ($issue && $issue['status'] === 'completed') {
            setFlash('error', 'Không thể xóa phiếu xuất đã hoàn thành');
            $this->redirect('inventory_issue');
            return;
        }

        $db = getDB();
        $del = $db->prepare('DELETE FROM inventory_issue_detail WHERE issue_id = ?');
        $del->execute([$id]);

        $this->model->delete($id);
        setFlash('success', 'Xóa phiếu xuất thành công');
        $this->redirect('inventory_issue');
    }

    /**
     * Complete issue and deduct from inventory (create inventory_log entries)
     */
    public function complete($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('inventory_issue');
            return;
        }

        $db = getDB();

        // Get issue
        $issue = $this->model->find($id);
        if (!$issue) {
            setFlash('error', 'Phiếu không tồn tại');
            $this->redirect('inventory_issue');
            return;
        }

        if ($issue['status'] === 'completed') {
            setFlash('warning', 'Phiếu xuất này đã hoàn thành rồi');
            $this->redirect('inventory_issue');
            return;
        }

        // Get all details
        $details = $this->model->getDetails($id);

        foreach ($details as $detail) {
            $stmtStock = $db->prepare("SELECT COALESCE(SUM(qty_change), 0) AS current_qty FROM inventory_log WHERE ingredient_id = ?");
            $stmtStock->execute([$detail['ingredient_id']]);
            $currentQty = (float)($stmtStock->fetch()['current_qty'] ?? 0);
            if ($currentQty < (float)$detail['qty']) {
                setFlash('error', 'Không đủ tồn kho để hoàn thành phiếu xuất');
                $this->redirect('inventory_issue');
                return;
            }
        }

        // Create inventory_log entries for each item (negative for issue)
        $stmtLog = $db->prepare('INSERT INTO inventory_log (ingredient_id, qty_change, type, related_id, note, created_by) VALUES (?, ?, ?, ?, ?, ?)');

        $issueTypeMap = [
            'sale' => 'issue',
            'manual' => 'issue',
            'waste' => 'expire'
        ];
        $logType = $issueTypeMap[$issue['issue_type']] ?? 'issue';

        foreach ($details as $detail) {
            $stmtLog->execute([
                $detail['ingredient_id'],
                -(float)$detail['qty'],  // qty_change (negative for issue)
                $logType,              // type (issue or expire)
                $id,                   // related_id (issue id)
                'Xuất kho từ phiếu #' . $id . ' (' . $issue['issue_type'] . ')',
                $user['id'] ?? null
            ]);
        }

        $this->model->update($id, ['status' => 'completed']);

        setFlash('success', 'Hoàn thành phiếu xuất kho - Số lượng đã được cập nhật');
        $this->redirect('inventory_issue');
    }
}
