<?php

/**
 * Expense Controller - CRUD for expenses
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class ExpenseController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = $this->model('Expense');
    }

    public function index()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $baseSql = "SELECT e.*, u.fullname as creator FROM expense e LEFT JOIN users u ON e.created_by = u.id ORDER BY e.expense_date DESC, e.id DESC";
        $result = $this->model->paginate($baseSql, [], $page, $per);

        $this->view('expense/index', [
            'items' => $result['data'],
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/expense',
            'user' => $user
        ]);
    }

    public function create()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $this->view('expense/create', ['user' => $user]);
    }

    public function store()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $data = $this->getPost();
        $required = ['expense_type', 'amount', 'expense_date'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('expense/create');
            return;
        }

        $insert = [
            'expense_type' => $data['expense_type'],
            'amount' => (float)$data['amount'],
            'description' => $data['description'] ?? null,
            'created_by' => $user['id'] ?? null,
            'expense_date' => $data['expense_date']
        ];

        $this->model->insert($insert);
        setFlash('success', 'Táº¡o chi phÃ­ thÃ nh cÃ´ng');
        $this->redirect('expense');
    }

    public function edit($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('expense');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Chi phÃ­ khÃ´ng tá»“n táº¡i');
            $this->redirect('expense');
            return;
        }

        $this->view('expense/edit', ['item' => $item, 'user' => $user]);
    }

    public function update($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('expense');
            return;
        }

        $data = $this->getPost();
        $required = ['expense_type', 'amount', 'expense_date'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('expense/edit/' . $id);
            return;
        }

        $update = [
            'expense_type' => $data['expense_type'],
            'amount' => (float)$data['amount'],
            'description' => $data['description'] ?? null,
            'expense_date' => $data['expense_date']
        ];

        $this->model->update($id, $update);
        setFlash('success', 'Cáº­p nháº­t chi phÃ­ thÃ nh cÃ´ng');
        $this->redirect('expense');
    }

    public function delete($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('expense');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'XÃ³a chi phÃ­ thÃ nh cÃ´ng');
        $this->redirect('expense');
    }
}


