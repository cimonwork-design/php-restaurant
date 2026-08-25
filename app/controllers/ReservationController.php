<?php

/**
 * Reservation Controller - CRUD for reservations
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';
require_once BASE_PATH . '/helpers/FormValidation.php';

class ReservationController extends Controller
{
    private $model;
    private $tableModel;

    public function __construct()
    {
        $this->model = $this->model('Reservation');
        $this->tableModel = $this->model('RestaurantTable');
    }

    public function index()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $baseSql = "SELECT * FROM reservation ORDER BY start_time DESC";
        $result = $this->model->paginate($baseSql, [], $page, $per);

        $this->view('reservation/index', [
            'items' => $result['data'],
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/reservation',
            'user' => $user
        ]);
    }

    public function create()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $tables = $this->tableModel->all('number', 'ASC');
        $this->view('reservation/create', ['tables' => $tables]);
    }

    private function normalizeDatetime($value)
    {
        // convert HTML datetime-local format to MySQL DATETIME
        if (strpos($value, 'T') !== false) {
            return str_replace('T', ' ', $value) . ':00';
        }
        return $value;
    }

    public function store()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $data = $this->getPost();
        $required = ['table_id', 'customer_name', 'start_time', 'end_time'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', formMessage('required'));
            $this->redirect('reservation/create');
            return;
        }

        $table = $this->tableModel->find((int)$data['table_id']);
        if (!$table) {
            setFlash('error', formMessage('table_invalid'));
            $this->redirect('reservation/create');
            return;
        }
        if (($table['status'] ?? 'free') === 'occupied') {
            setFlash('error', formMessage('table_not_free'));
            $this->redirect('reservation/create');
            return;
        }
        if (strlen($data['customer_name']) > 100) {
            setFlash('error', formMessage('customer_length'));
            $this->redirect('reservation/create');
            return;
        }
        if (!filter_var($data['party_size'] ?? null, FILTER_VALIDATE_INT) || (int)$data['party_size'] < 1) {
            setFlash('error', formMessage('party_invalid'));
            $this->redirect('reservation/create');
            return;
        }
        if (!in_array($data['status'] ?? 'pending', ['pending', 'confirmed', 'cancelled'], true)) {
            setFlash('error', formMessage('status_invalid'));
            $this->redirect('reservation/create');
            return;
        }
        if (strtotime($data['start_time']) === false || strtotime($data['end_time']) === false) {
            setFlash('error', formMessage('datetime_invalid'));
            $this->redirect('reservation/create');
            return;
        }

        $start = $this->normalizeDatetime($data['start_time']);
        $end = $this->normalizeDatetime($data['end_time']);

        if (strtotime($end) <= strtotime($start)) {
            setFlash('error', formMessage('end_before_start'));
            $this->redirect('reservation/create');
            return;
        }

        // check overlap
        $overlaps = $this->model->findOverlapping($data['table_id'], $start, $end);
        if (!empty($overlaps)) {
            setFlash('error', formMessage('reservation_overlap'));
            $this->redirect('reservation/create');
            return;
        }

        $insert = [
            'table_id' => $data['table_id'],
            'customer_name' => $data['customer_name'],
            'party_size' => $data['party_size'] ?? 1,
            'start_time' => $start,
            'end_time' => $end,
            'status' => $data['status'] ?? 'pending',
            'created_by' => $user['id'] ?? null
        ];

        $this->model->insert($insert);
        setFlash('success', 'Táº¡o Ä‘áº·t chá»— thÃ nh cÃ´ng');
        $this->redirect('reservation');
    }

    public function edit($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('reservation');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Äáº·t chá»— khÃ´ng tá»“n táº¡i');
            $this->redirect('reservation');
            return;
        }

        $tables = $this->tableModel->all('number', 'ASC');
        $this->view('reservation/edit', ['item' => $item, 'tables' => $tables]);
    }

    public function update($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('reservation');
            return;
        }

        $data = $this->getPost();
        $required = ['table_id', 'customer_name', 'start_time', 'end_time'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', formMessage('required'));
            $this->redirect('reservation/edit/' . $id);
            return;
        }

        if (!$this->model->find($id)) {
            setFlash('error', formMessage('not_found'));
            $this->redirect('reservation');
            return;
        }
        if (!$this->tableModel->find((int)$data['table_id'])) {
            setFlash('error', formMessage('table_invalid'));
            $this->redirect('reservation/edit/' . $id);
            return;
        }
        if (strlen($data['customer_name']) > 100) {
            setFlash('error', formMessage('customer_length'));
            $this->redirect('reservation/edit/' . $id);
            return;
        }
        if (!filter_var($data['party_size'] ?? null, FILTER_VALIDATE_INT) || (int)$data['party_size'] < 1) {
            setFlash('error', formMessage('party_invalid'));
            $this->redirect('reservation/edit/' . $id);
            return;
        }
        if (!in_array($data['status'] ?? 'pending', ['pending', 'confirmed', 'cancelled'], true)) {
            setFlash('error', formMessage('status_invalid'));
            $this->redirect('reservation/edit/' . $id);
            return;
        }
        if (strtotime($data['start_time']) === false || strtotime($data['end_time']) === false) {
            setFlash('error', formMessage('datetime_invalid'));
            $this->redirect('reservation/edit/' . $id);
            return;
        }

        $start = $this->normalizeDatetime($data['start_time']);
        $end = $this->normalizeDatetime($data['end_time']);

        if (strtotime($end) <= strtotime($start)) {
            setFlash('error', formMessage('end_before_start'));
            $this->redirect('reservation/edit/' . $id);
            return;
        }

        // check overlap excluding current
        $overlaps = $this->model->findOverlapping($data['table_id'], $start, $end, $id);
        if (!empty($overlaps)) {
            setFlash('error', formMessage('reservation_overlap'));
            $this->redirect('reservation/edit/' . $id);
            return;
        }

        $update = [
            'table_id' => $data['table_id'],
            'customer_name' => $data['customer_name'],
            'party_size' => $data['party_size'] ?? 1,
            'start_time' => $start,
            'end_time' => $end,
            'status' => $data['status'] ?? 'pending'
        ];

        $this->model->update($id, $update);
        setFlash('success', 'Cáº­p nháº­t Ä‘áº·t chá»— thÃ nh cÃ´ng');
        $this->redirect('reservation');
    }

    public function delete($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('reservation');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'XÃ³a Ä‘áº·t chá»— thÃ nh cÃ´ng');
        $this->redirect('reservation');
    }
}


