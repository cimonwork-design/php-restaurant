<?php

/**
 * Ingredient Controller - CRUD for ingredients
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class IngredientController extends Controller
{

    private $model;

    public function __construct()
    {
        $this->model = $this->model('Ingredient');
    }

    // List
    public function index()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $db = getDB();
        $baseSql = "
            SELECT 
                i.id,
                i.code,
                i.name,
                i.category,
                i.unit,
                i.purchase_price,
                i.min_stock,
                i.main_supplier,
                i.description,
                COALESCE(SUM(il.qty_change), 0) as current_qty
            FROM ingredient i
            LEFT JOIN inventory_log il ON i.id = il.ingredient_id
            GROUP BY i.id, i.code, i.name, i.category, i.unit, i.purchase_price, i.min_stock, i.main_supplier, i.description
            ORDER BY i.id DESC
        ";

        // Use Model::paginate via a temporary Model instance
        $model = $this->model('Ingredient');
        $result = $model->paginate($baseSql, [], $page, $per);

        $this->view('ingredient/index', [
            'items' => $result['data'],
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/ingredient',
            'user' => $user
        ]);
    }

    // Show create form
    public function create()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $catModel = $this->model('IngredientCategory');
        $categories = $catModel->all('name', 'ASC');

        $this->view('ingredient/create', ['categories' => $categories, 'user' => $user]);
    }

    // Store new ingredient
    public function store()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $data = $this->getPost();

        $required = ['code', 'name'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('ingredient/create');
            return;
        }

        // Prevent duplicate code
        if ($this->model->findByCode($data['code'])) {
            setFlash('error', 'MÃ£ nguyÃªn liá»‡u Ä‘Ã£ tá»“n táº¡i');
            $this->redirect('ingredient/create');
            return;
        }

        // Normalize fields
        $insert = [
            'code' => $data['code'],
            'name' => $data['name'],
            'category' => $data['category'] ?? null,
            'unit' => $data['unit'] ?? null,
            'purchase_price' => $data['purchase_price'] ?? null,
            'min_stock' => $data['min_stock'] ?? 0,
            'description' => $data['description'] ?? null,
            'main_supplier' => $data['main_supplier'] ?? null
        ];

        $this->model->insert($insert);
        setFlash('success', 'Táº¡o nguyÃªn liá»‡u thÃ nh cÃ´ng');
        $this->redirect('ingredient');
    }

    // Show edit form
    public function edit($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('ingredient');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'NguyÃªn liá»‡u khÃ´ng tá»“n táº¡i');
            $this->redirect('ingredient');
            return;
        }

        $catModel = $this->model('IngredientCategory');
        $categories = $catModel->all('name', 'ASC');

        $this->view('ingredient/edit', ['item' => $item, 'categories' => $categories]);
    }

    // Update ingredient
    public function update($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('ingredient');
            return;
        }

        $data = $this->getPost();
        $required = ['code', 'name'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('ingredient/edit/' . $id);
            return;
        }

        // Prevent duplicate code for other records
        $existing = $this->model->findByCode($data['code']);
        if ($existing && $existing['id'] != $id) {
            setFlash('error', 'MÃ£ nguyÃªn liá»‡u Ä‘Ã£ tá»“n táº¡i');
            $this->redirect('ingredient/edit/' . $id);
            return;
        }

        $update = [
            'code' => $data['code'],
            'name' => $data['name'],
            'category' => $data['category'] ?? null,
            'unit' => $data['unit'] ?? null,
            'purchase_price' => $data['purchase_price'] ?? null,
            'min_stock' => $data['min_stock'] ?? 0,
            'description' => $data['description'] ?? null,
            'main_supplier' => $data['main_supplier'] ?? null
        ];

        $this->model->update($id, $update);
        setFlash('success', 'Cáº­p nháº­t nguyÃªn liá»‡u thÃ nh cÃ´ng');
        $this->redirect('ingredient');
    }

    // Delete
    public function delete($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('ingredient');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'XÃ³a nguyÃªn liá»‡u thÃ nh cÃ´ng');
        $this->redirect('ingredient');
    }
}


