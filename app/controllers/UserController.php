<?php

/**
 * User Controller - admin-only user management
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';
require_once BASE_PATH . '/helpers/FormValidation.php';

class UserController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = $this->model('User');
    }

    private function requireAdmin()
    {
        $user = JWT::getCurrentUser();
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            $this->redirect('dashboard');
            return false;
        }
        return $user;
    }

    public function index()
    {
        $user = $this->requireAdmin();
        if (!$user) return;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $baseSql = "SELECT * FROM users ORDER BY id DESC";
        $result = $this->model->paginate($baseSql, [], $page, $per);

        $this->view('user/index', [
            'items' => $result['data'],
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/user',
            'user' => $user
        ]);
    }

    public function create()
    {
        $user = $this->requireAdmin();
        if (!$user) return;

        $this->view('user/create', ['user' => $user]);
    }

    public function store()
    {
        $user = $this->requireAdmin();
        if (!$user) return;

        $data = $this->getPost();
        $required = ['username', 'password', 'role'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', formMessage('required'));
            $this->redirect('user/create');
            return;
        }

        if (!preg_match('/^[A-Za-z0-9._-]+$/', $data['username']) || strlen($data['username']) < 3 || strlen($data['username']) > 60) {
            setFlash('error', formMessage('username_format'));
            $this->redirect('user/create');
            return;
        }
        if (strlen($data['password']) < 6) {
            setFlash('error', formMessage('password_length'));
            $this->redirect('user/create');
            return;
        }
        if (strlen($data['fullname'] ?? '') > 100) {
            setFlash('error', formMessage('fullname_length'));
            $this->redirect('user/create');
            return;
        }
        if (!in_array($data['role'], ['admin', 'manager', 'user'], true)) {
            setFlash('error', formMessage('role_invalid'));
            $this->redirect('user/create');
            return;
        }

        // prevent duplicate username
        if ($this->model->findByUsername($data['username'])) {
            setFlash('error', formMessage('username_exists'));
            $this->redirect('user/create');
            return;
        }

        $insert = [
            'username' => $data['username'],
            'password' => $data['password'],
            'fullname' => $data['fullname'] ?? null,
            'role' => $data['role'],
            'active' => isset($data['active']) ? 1 : 0
        ];

        $this->model->createUser($insert);
        setFlash('success', 'Tạo người dùng thành công');
        $this->redirect('user');
    }

    public function edit($id = null)
    {
        $user = $this->requireAdmin();
        if (!$user) return;

        if (!$id) {
            $this->redirect('user');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Người dùng không tồn tại');
            $this->redirect('user');
            return;
        }

        $this->view('user/edit', ['item' => $item, 'user' => $user]);
    }

    public function update($id = null)
    {
        $user = $this->requireAdmin();
        if (!$user) return;

        if (!$id) {
            $this->redirect('user');
            return;
        }

        $data = $this->getPost();
        $required = ['username', 'role'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', formMessage('required'));
            $this->redirect('user/edit/' . $id);
            return;
        }

        if (!preg_match('/^[A-Za-z0-9._-]+$/', $data['username']) || strlen($data['username']) < 3 || strlen($data['username']) > 60) {
            setFlash('error', formMessage('username_format'));
            $this->redirect('user/edit/' . $id);
            return;
        }
        if (!empty($data['password']) && strlen($data['password']) < 6) {
            setFlash('error', formMessage('password_length'));
            $this->redirect('user/edit/' . $id);
            return;
        }
        if (strlen($data['fullname'] ?? '') > 100) {
            setFlash('error', formMessage('fullname_length'));
            $this->redirect('user/edit/' . $id);
            return;
        }
        if (!in_array($data['role'], ['admin', 'manager', 'user'], true)) {
            setFlash('error', formMessage('role_invalid'));
            $this->redirect('user/edit/' . $id);
            return;
        }

        // prevent duplicate username for other users
        $existing = $this->model->findByUsername($data['username']);
        if ($existing && $existing['id'] != $id) {
            setFlash('error', formMessage('username_exists'));
            $this->redirect('user/edit/' . $id);
            return;
        }

        $update = [
            'username' => $data['username'],
            'password' => $data['password'] ?? null,
            'fullname' => $data['fullname'] ?? null,
            'role' => $data['role'],
            'active' => isset($data['active']) ? 1 : 0
        ];

        $this->model->updateUser($id, $update);
        setFlash('success', 'Cập nhật người dùng thành công');
        $this->redirect('user');
    }

    public function delete($id = null)
    {
        $user = $this->requireAdmin();
        if (!$user) return;

        if (!$id) {
            $this->redirect('user');
            return;
        }

        // prevent deleting self
        if ($user['id'] == $id) {
            setFlash('error', 'Bạn không thể xóa chính mình');
            $this->redirect('user');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'Xóa người dùng thành công');
        $this->redirect('user');
    }
}
