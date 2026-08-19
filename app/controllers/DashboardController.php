<?php

/**
 * Dashboard Controller
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class DashboardController extends Controller
{

    public function index()
    {
        // Check authentication
        $user = JWT::getCurrentUser();

        if (!$user) {
            $this->redirect('auth/login');
            return;
        }
        // load models
        $ingredientModel = $this->model('Ingredient');
        $menuModel = $this->model('MenuItem');
        $tableModel = $this->model('RestaurantTable');
        $orderModel = $this->model('SaleOrder');
        $receiptModel = $this->model('InventoryReceipt');
        $issueModel = $this->model('InventoryIssue');
        $expenseModel = $this->model('Expense');
        $userModel = $this->model('User');

        // counts
        $counts = [
            'ingredients' => $ingredientModel->count(),
            'menu_items' => $menuModel->count(),
            'tables' => $tableModel->count(),
            'users' => $userModel->count(),
            'receipts' => $receiptModel->count(),
            'issues' => $issueModel->count(),
            'expenses' => $expenseModel->count()
        ];

        $db = getDB();
        // today's orders count and today's revenue (paid orders only)
        $today = date('Y-m-d');
        $stmt = $db->prepare("SELECT COUNT(*) as cnt, IFNULL(SUM(total_amount),0) as revenue FROM sale_order WHERE DATE(order_time) = ? AND status = 'paid'");
        $stmt->execute([$today]);
        $row = $stmt->fetch();
        $todayOrders = (int)($row['cnt'] ?? 0);
        $todayRevenue = (float)($row['revenue'] ?? 0.0);

        $chartStart = date('Y-m-d', strtotime('-6 days'));
        $chartEnd = date('Y-m-d');
        $stmt = $db->prepare("
            SELECT DATE(so.order_time) AS day,
                   COALESCE(SUM(so.total_amount), 0) AS revenue,
                   COALESCE(SUM(order_cost.ingredient_cost), 0) AS ingredient_cost
            FROM sale_order so
            LEFT JOIN (
                SELECT sod.sale_order_id, SUM(sod.qty * r.qty * i.purchase_price) AS ingredient_cost
                FROM sale_order_detail sod
                JOIN recipe r ON r.menu_id = sod.menu_id
                JOIN ingredient i ON i.id = r.ingredient_id
                WHERE sod.status != 'canceled'
                GROUP BY sod.sale_order_id
            ) order_cost ON order_cost.sale_order_id = so.id
            WHERE DATE(so.order_time) BETWEEN ? AND ? AND so.status = 'paid'
            GROUP BY DATE(so.order_time)
        ");
        $stmt->execute([$chartStart, $chartEnd]);
        $salesRows = [];
        foreach ($stmt->fetchAll() as $r) {
            $salesRows[$r['day']] = $r;
        }

        $stmt = $db->prepare("SELECT expense_date AS day, COALESCE(SUM(amount), 0) AS fixed_expense FROM expense WHERE expense_date BETWEEN ? AND ? GROUP BY expense_date");
        $stmt->execute([$chartStart, $chartEnd]);
        $expenseRows = [];
        foreach ($stmt->fetchAll() as $r) {
            $expenseRows[$r['day']] = $r;
        }

        $chart = ['labels' => [], 'revenue' => [], 'ingredient_cost' => [], 'fixed_expense' => [], 'profit' => []];
        for ($ts = strtotime($chartStart); $ts <= strtotime($chartEnd); $ts = strtotime('+1 day', $ts)) {
            $day = date('Y-m-d', $ts);
            $revenue = (float)($salesRows[$day]['revenue'] ?? 0);
            $ingredientCost = (float)($salesRows[$day]['ingredient_cost'] ?? 0);
            $fixedExpense = (float)($expenseRows[$day]['fixed_expense'] ?? 0);
            $chart['labels'][] = date('d/m', $ts);
            $chart['revenue'][] = $revenue;
            $chart['ingredient_cost'][] = $ingredientCost;
            $chart['fixed_expense'][] = $fixedExpense;
            $chart['profit'][] = $revenue - $ingredientCost - $fixedExpense;
        }

        // recent activity: latest 5 orders and latest 5 expenses
        $recentOrders = $orderModel->query("SELECT o.id, o.order_time, o.total_amount, t.number as table_number FROM sale_order o LEFT JOIN restaurant_table t ON o.table_id = t.id ORDER BY o.order_time DESC LIMIT 5");
        $recentExpenses = $expenseModel->query("SELECT * FROM expense ORDER BY expense_date DESC LIMIT 5");

        $this->view('dashboard/index', [
            'user' => $user,
            'counts' => $counts,
            'todayOrders' => $todayOrders,
            'todayRevenue' => $todayRevenue,
            'recentOrders' => $recentOrders,
            'recentExpenses' => $recentExpenses,
            'chart' => $chart
        ]);
    }
}
