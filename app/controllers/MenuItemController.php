<?php

/**
 * MenuItem Controller - CRUD for menu items
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';
require_once BASE_PATH . '/helpers/FormValidation.php';

class MenuItemController extends Controller
{

    private $model;

    public function __construct()
    {
        $this->model = $this->model('MenuItem');
    }

    public function index()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $baseSql = "SELECT * FROM menu_item ORDER BY id DESC";
        $result = $this->model->paginate($baseSql, [], $page, $per);

        $this->view('menu_item/index', [
            'items' => $result['data'],
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/menu_item',
            'user' => $user
        ]);
    }

    public function create()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $this->view('menu_item/create');
    }

    public function store()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $data = $this->getPost();
        $required = ['code', 'name', 'price'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', formMessage('required'));
            $this->redirect('menu_item/create');
            return;
        }

        if (strlen($data['code']) > 50) {
            setFlash('error', formMessage('code_length'));
            $this->redirect('menu_item/create');
            return;
        }
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $data['code'])) {
            setFlash('error', formMessage('code_format'));
            $this->redirect('menu_item/create');
            return;
        }
        if (strlen($data['name']) > 100) {
            setFlash('error', formMessage('name_length'));
            $this->redirect('menu_item/create');
            return;
        }
        if ($data['price'] === '' || $data['price'] === null) {
            setFlash('error', formMessage('price_required'));
            $this->redirect('menu_item/create');
            return;
        }
        if (!is_numeric($data['price']) || (float)$data['price'] <= 0) {
            setFlash('error', formMessage('price_invalid'));
            $this->redirect('menu_item/create');
            return;
        }

        if ($this->model->findByCode($data['code'])) {
            setFlash('error', formMessage('code_exists'));
            $this->redirect('menu_item/create');
            return;
        }

        $insert = [
            'code' => $data['code'],
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null
        ];

        $this->model->insert($insert);
        setFlash('success', 'Táº¡o mÃ³n Äƒn thÃ nh cÃ´ng');
        $this->redirect('menu_item');
    }

    public function edit($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('menu_item');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'MÃ³n khÃ´ng tá»“n táº¡i');
            $this->redirect('menu_item');
            return;
        }

        $this->view('menu_item/edit', ['item' => $item]);
    }

    public function update($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('menu_item');
            return;
        }

        $data = $this->getPost();
        $required = ['code', 'name', 'price'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', formMessage('required'));
            $this->redirect('menu_item/edit/' . $id);
            return;
        }

        if (strlen($data['code']) > 50 || !preg_match('/^[A-Za-z0-9_-]+$/', $data['code'])) {
            setFlash('error', strlen($data['code']) > 50 ? formMessage('code_length') : formMessage('code_format'));
            $this->redirect('menu_item/edit/' . $id);
            return;
        }
        if (strlen($data['name']) > 100) {
            setFlash('error', formMessage('name_length'));
            $this->redirect('menu_item/edit/' . $id);
            return;
        }
        if ($data['price'] === '' || !is_numeric($data['price']) || (float)$data['price'] <= 0) {
            setFlash('error', $data['price'] === '' ? formMessage('price_required') : formMessage('price_invalid'));
            $this->redirect('menu_item/edit/' . $id);
            return;
        }

        $existing = $this->model->findByCode($data['code']);
        if ($existing && $existing['id'] != $id) {
            setFlash('error', formMessage('code_exists'));
            $this->redirect('menu_item/edit/' . $id);
            return;
        }

        $update = [
            'code' => $data['code'],
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null
        ];

        $this->model->update($id, $update);
        setFlash('success', 'Cáº­p nháº­t mÃ³n Äƒn thÃ nh cÃ´ng');
        $this->redirect('menu_item');
    }

    public function delete($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('menu_item');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'XÃ³a mÃ³n Äƒn thÃ nh cÃ´ng');
        $this->redirect('menu_item');
    }
}


