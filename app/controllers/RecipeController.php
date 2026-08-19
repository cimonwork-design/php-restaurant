<?php

/**
 * Recipe Controller - CRUD for recipe (menu -> ingredient -> qty)
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class RecipeController extends Controller
{

    private $model;

    public function __construct()
    {
        $this->model = $this->model('Recipe');
    }

    public function index($menu_id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        // Allow menu_id via URL segment or GET param
        if (!$menu_id && isset($_GET['menu_id'])) {
            $menu_id = intval($_GET['menu_id']);
        }

        // If no menu selected yet, show menu list with search
        $menuModel = $this->model('MenuItem');
        if (!$menu_id) {
            $q = isset($_GET['q']) ? trim($_GET['q']) : '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

            if ($q !== '') {
                $baseSql = "SELECT * FROM menu_item WHERE name LIKE :q ORDER BY name ASC";
                $params = ['q' => '%' . $q . '%'];
            } else {
                $baseSql = "SELECT * FROM menu_item ORDER BY name ASC";
                $params = [];
            }

            $result = $menuModel->paginate($baseSql, $params, $page, $per);
            $menuItems = $result['data'];
            $this->view('recipe/select_menu', [
                'menuItems' => $menuItems,
                'user' => $user,
                'q' => $q,
                'pagination' => $result['pagination'],
                'baseUrl' => BASE_URL . '/recipe' . ($q !== '' ? ('?q=' . urlencode($q)) : '')
            ]);
            return;
        }

        // Menu selected: fetch recipes only for that menu
        // Pagination for recipe items of selected menu
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $baseSql = "SELECT r.*, m.name AS menu_name, i.name AS ingredient_name
                   , i.unit, i.purchase_price
            FROM recipe r
            LEFT JOIN menu_item m ON r.menu_id = m.id
            LEFT JOIN ingredient i ON r.ingredient_id = i.id
            WHERE r.menu_id = " . intval($menu_id) . "
            ORDER BY i.name ASC";

        $result = $this->model->paginate($baseSql, [], $page, $per);
        $items = $result['data'];
        $menu = $menuModel->find($menu_id);

        $this->view('recipe/index', [
            'items' => $items,
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/recipe?menu_id=' . intval($menu_id),
            'user' => $user,
            'menu' => $menu,
            'menu_id' => $menu_id
        ]);
    }

    public function create($menu_id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $menuModel = $this->model('MenuItem');
        $ingredientModel = $this->model('Ingredient');

        $menuItems = $menuModel->all('name', 'ASC');
        $ingredients = $ingredientModel->all('name', 'ASC');

        // Support menu_id from URL segment or GET
        if (!$menu_id && isset($_GET['menu_id'])) {
            $menu_id = intval($_GET['menu_id']);
        }

        $selectedMenu = null;
        $existingRecipe = [];
        if ($menu_id) {
            $selectedMenu = $menuModel->find($menu_id);
            $existingRecipe = $this->model->getIngredientsByMenu($menu_id);
        }

        $this->view('recipe/create', [
            'menuItems' => $menuItems,
            'ingredients' => $ingredients,
            'selectedMenu' => $selectedMenu,
            'existingRecipe' => $existingRecipe,
            'user' => $user
        ]);
    }

    public function store()
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        $menuId = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;
        $ingredientIds = $_POST['ingredient_id'] ?? [];
        $quantities = $_POST['qty'] ?? [];

        if ($menuId <= 0) {
            setFlash('error', 'Vui lòng chọn món ăn cần thiết lập công thức.');
            $this->redirect('recipe/create');
            return;
        }

        if (!is_array($ingredientIds)) {
            $ingredientIds = [$ingredientIds];
        }
        if (!is_array($quantities)) {
            $quantities = [$quantities];
        }

        $recipeItems = $this->normalizeRecipeItems($ingredientIds, $quantities);
        if (empty($recipeItems)) {
            setFlash('error', 'Công thức cần có ít nhất một nguyên liệu với số lượng lớn hơn 0.');
            $this->redirect('recipe/create?menu_id=' . $menuId);
            return;
        }

        $this->model->replaceForMenu($menuId, $recipeItems);
        setFlash('success', 'Đã lưu công thức món ăn.');
        $this->redirect('recipe?menu_id=' . $menuId);
    }

    public function edit($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('recipe');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Công thức không tồn tại.');
            $this->redirect('recipe');
            return;
        }

        $this->redirect('recipe/create?menu_id=' . intval($item['menu_id']));
    }

    public function update($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('recipe');
            return;
        }

        $menuId = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;
        $ingredientIds = $_POST['ingredient_id'] ?? [];
        $quantities = $_POST['qty'] ?? [];

        if ($menuId <= 0) {
            setFlash('error', 'Vui lòng chọn món ăn cần thiết lập công thức.');
            $this->redirect('recipe');
            return;
        }

        if (!is_array($ingredientIds)) {
            $ingredientIds = [$ingredientIds];
        }
        if (!is_array($quantities)) {
            $quantities = [$quantities];
        }

        $recipeItems = $this->normalizeRecipeItems($ingredientIds, $quantities);
        if (empty($recipeItems)) {
            setFlash('error', 'Công thức cần có ít nhất một nguyên liệu với số lượng lớn hơn 0.');
            $this->redirect('recipe/create?menu_id=' . $menuId);
            return;
        }

        $this->model->replaceForMenu($menuId, $recipeItems);
        setFlash('success', 'Đã cập nhật công thức món ăn.');
        $this->redirect('recipe?menu_id=' . $menuId);
    }

    public function delete($id = null)
    {
        $user = $this->requireRoles(['admin', 'manager']);
        if (!$user) return;

        if (!$id) {
            $this->redirect('recipe');
            return;
        }

        $item = $this->model->find($id);
        $this->model->delete($id);
        setFlash('success', 'Đã xóa nguyên liệu khỏi công thức.');
        $this->redirect(!empty($item['menu_id']) ? 'recipe?menu_id=' . intval($item['menu_id']) : 'recipe');
    }

    private function normalizeRecipeItems(array $ingredientIds, array $quantities)
    {
        $items = [];

        foreach ($ingredientIds as $index => $ingredientId) {
            $ingredientId = (int)$ingredientId;
            $qty = isset($quantities[$index]) ? (float)$quantities[$index] : 0;

            if ($ingredientId <= 0 || $qty <= 0) {
                continue;
            }

            if (!isset($items[$ingredientId])) {
                $items[$ingredientId] = 0;
            }
            $items[$ingredientId] += $qty;
        }

        return $items;
    }
}


