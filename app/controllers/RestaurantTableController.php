<?php

/**
 * RestaurantTable Controller - CRUD for restaurant tables
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';
require_once BASE_PATH . '/helpers/FormValidation.php';

class RestaurantTableController extends Controller
{

    private $model;

    public function __construct()
    {
        $this->model = $this->model('RestaurantTable');
    }

    public function index()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $baseSql = "SELECT * FROM restaurant_table ORDER BY id DESC";
        $result = $this->model->paginate($baseSql, [], $page, $per);

        $this->view('restaurant_table/index', [
            'items' => $result['data'],
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/restaurant_table',
            'user' => $user
        ]);
    }

    public function create()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $this->view('restaurant_table/create');
    }

    public function store()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $data = $this->getPost();
        $required = ['number'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', 'Số bàn không được để trống.');
            $this->redirect('restaurant_table/create');
            return;
        }

        if (strlen($data['number']) > 10) {
            setFlash('error', 'Số bàn không được vượt quá 10 ký tự.');
            $this->redirect('restaurant_table/create');
            return;
        }
        if (!in_array($data['status'] ?? 'free', ['free', 'occupied', 'reserved'], true)) {
            setFlash('error', formMessage('status_invalid'));
            $this->redirect('restaurant_table/create');
            return;
        }

        if ($this->model->findByNumber($data['number'])) {
            setFlash('error', 'Số bàn đã tồn tại.');
            $this->redirect('restaurant_table/create');
            return;
        }

        $insert = [
            'number' => $data['number'],
            'status' => $data['status'] ?? 'free'
        ];

        $this->model->insert($insert);
        setFlash('success', 'Táº¡o bÃ n thÃ nh cÃ´ng');
        $this->redirect('restaurant_table');
    }

    public function edit($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('restaurant_table');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'BÃ n khÃ´ng tá»“n táº¡i');
            $this->redirect('restaurant_table');
            return;
        }

        $this->view('restaurant_table/edit', ['item' => $item]);
    }

    public function update($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('restaurant_table');
            return;
        }

        $data = $this->getPost();
        $required = ['number'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', 'Số bàn không được để trống.');
            $this->redirect('restaurant_table/edit/' . $id);
            return;
        }

        if (strlen($data['number']) > 10) {
            setFlash('error', 'Số bàn không được vượt quá 10 ký tự.');
            $this->redirect('restaurant_table/edit/' . $id);
            return;
        }
        if (!in_array($data['status'] ?? 'free', ['free', 'occupied', 'reserved'], true)) {
            setFlash('error', formMessage('status_invalid'));
            $this->redirect('restaurant_table/edit/' . $id);
            return;
        }

        // Check duplicate number
        $existing = $this->model->findByNumber($data['number']);
        if ($existing && $existing['id'] != $id) {
            setFlash('error', 'Số bàn đã tồn tại.');
            $this->redirect('restaurant_table/edit/' . $id);
            return;
        }

        $update = [
            'number' => $data['number'],
            'status' => $data['status'] ?? 'free'
        ];

        $this->model->update($id, $update);
        setFlash('success', 'Cáº­p nháº­t bÃ n thÃ nh cÃ´ng');
        $this->redirect('restaurant_table');
    }

    public function delete($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('restaurant_table');
            return;
        }

        $table = $this->model->find($id);
        if (!$table) {
            setFlash('error', formMessage('not_found'));
            $this->redirect('restaurant_table');
            return;
        }
        if ($table['status'] !== 'free') {
            setFlash('error', 'Chỉ được xóa bàn đang trống.');
            $this->redirect('restaurant_table');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'XÃ³a bÃ n thÃ nh cÃ´ng');
        $this->redirect('restaurant_table');
    }
}


