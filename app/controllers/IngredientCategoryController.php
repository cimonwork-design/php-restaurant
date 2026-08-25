<?php

/**
 * IngredientCategory Controller - CRUD for ingredient categories
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';
require_once BASE_PATH . '/helpers/FormValidation.php';

class IngredientCategoryController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = $this->model('IngredientCategory');
    }

    public function index()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $baseSql = "SELECT * FROM ingredient_category ORDER BY name ASC";
        $result = $this->model->paginate($baseSql, [], $page, $per);

        $this->view('ingredient_category/index', [
            'items' => $result['data'],
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/ingredient_category',
            'user' => $user
        ]);
    }

    public function create()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $this->view('ingredient_category/create');
    }

    public function store()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $data = $this->getPost();
        $required = ['name'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', formMessage('name_required'));
            $this->redirect('ingredient_category/create');
            return;
        }
        if (strlen($data['name']) > 100) {
            setFlash('error', formMessage('category_length'));
            $this->redirect('ingredient_category/create');
            return;
        }

        if ($this->model->findByName($data['name'])) {
            setFlash('error', 'Tên danh mục đã tồn tại.');
            $this->redirect('ingredient_category/create');
            return;
        }

        $this->model->insert(['name' => $data['name'], 'description' => $data['description'] ?? null]);
        setFlash('success', 'Táº¡o loáº¡i nguyÃªn liá»‡u thÃ nh cÃ´ng');
        $this->redirect('ingredient_category');
    }

    public function edit($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('ingredient_category');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Loáº¡i nguyÃªn liá»‡u khÃ´ng tá»“n táº¡i');
            $this->redirect('ingredient_category');
            return;
        }

        $this->view('ingredient_category/edit', ['item' => $item]);
    }

    public function update($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('ingredient_category');
            return;
        }

        $data = $this->getPost();
        $required = ['name'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', formMessage('name_required'));
            $this->redirect('ingredient_category/edit/' . $id);
            return;
        }
        if (strlen($data['name']) > 100) {
            setFlash('error', formMessage('category_length'));
            $this->redirect('ingredient_category/edit/' . $id);
            return;
        }

        $existing = $this->model->findByName($data['name']);
        if ($existing && $existing['id'] != $id) {
            setFlash('error', 'Tên danh mục đã tồn tại.');
            $this->redirect('ingredient_category/edit/' . $id);
            return;
        }

        $this->model->update($id, ['name' => $data['name'], 'description' => $data['description'] ?? null]);
        setFlash('success', 'Cáº­p nháº­t loáº¡i nguyÃªn liá»‡u thÃ nh cÃ´ng');
        $this->redirect('ingredient_category');
    }

    public function delete($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('ingredient_category');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'XÃ³a loáº¡i nguyÃªn liá»‡u thÃ nh cÃ´ng');
        $this->redirect('ingredient_category');
    }
}


