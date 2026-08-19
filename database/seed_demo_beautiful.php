<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once __DIR__ . '/../config/database.php';

$db = getDB();
$adminId = 1;

function scalarValue($db, $sql, $params = [])
{
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

function upsertIngredient($db, $item)
{
    $id = scalarValue($db, 'SELECT id FROM ingredient WHERE code = ?', [$item['code']]);
    if ($id) {
        $stmt = $db->prepare('UPDATE ingredient SET name=?, category=?, unit=?, purchase_price=?, min_stock=?, description=?, main_supplier=? WHERE id=?');
        $stmt->execute([$item['name'], $item['category'], $item['unit'], $item['price'], $item['min_stock'], $item['description'], $item['supplier'], $id]);
        return (int)$id;
    }
    $stmt = $db->prepare('INSERT INTO ingredient (code, name, category, unit, purchase_price, min_stock, description, main_supplier) VALUES (?,?,?,?,?,?,?,?)');
    $stmt->execute([$item['code'], $item['name'], $item['category'], $item['unit'], $item['price'], $item['min_stock'], $item['description'], $item['supplier']]);
    return (int)$db->lastInsertId();
}

function upsertMenu($db, $item)
{
    $id = scalarValue($db, 'SELECT id FROM menu_item WHERE code = ?', [$item['code']]);
    if ($id) {
        $stmt = $db->prepare('UPDATE menu_item SET name=?, price=?, description=? WHERE id=?');
        $stmt->execute([$item['name'], $item['price'], $item['description'], $id]);
        return (int)$id;
    }
    $stmt = $db->prepare('INSERT INTO menu_item (code, name, price, description) VALUES (?,?,?,?)');
    $stmt->execute([$item['code'], $item['name'], $item['price'], $item['description']]);
    return (int)$db->lastInsertId();
}

function ensureTable($db, $number)
{
    $id = scalarValue($db, 'SELECT id FROM restaurant_table WHERE number = ?', [$number]);
    if ($id) return (int)$id;
    $stmt = $db->prepare("INSERT INTO restaurant_table (number, status) VALUES (?, 'free')");
    $stmt->execute([$number]);
    return (int)$db->lastInsertId();
}

function addReceiptLog($db, $ingredientId, $qty, $userId)
{
    $exists = scalarValue($db, "SELECT id FROM inventory_log WHERE ingredient_id=? AND type='receipt' AND note='DEMO_SEED_STOCK' LIMIT 1", [$ingredientId]);
    if ($exists) return;
    $stmt = $db->prepare("INSERT INTO inventory_log (ingredient_id, qty_change, type, related_id, note, created_by) VALUES (?, ?, 'receipt', 0, 'DEMO_SEED_STOCK', ?)");
    $stmt->execute([$ingredientId, $qty, $userId]);
}

function createOrder($db, $tableId, $items, $menuByCode, $recipeByMenuId, $ingredientIds, $date, $status, $discount, $vat, $userId)
{
    $marker = 'DEMO_SEED_' . date('Ymd', strtotime($date)) . '_' . $tableId . '_' . substr(md5(json_encode($items) . $date . $status), 0, 8);
    $exists = scalarValue($db, 'SELECT id FROM sale_order WHERE customer_name = ?', [$marker]);
    if ($exists) return;

    $subtotal = 0;
    foreach ($items as $code => $qty) {
        $subtotal += $menuByCode[$code]['price'] * $qty;
    }
    $afterDiscount = max(0, $subtotal - $discount);
    $total = $afterDiscount + ($afterDiscount * $vat / 100);

    $stmt = $db->prepare('INSERT INTO sale_order (table_id, waiter_id, cashier_id, order_time, status, discount, vat_rate, total_amount, source, customer_name) VALUES (?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([$tableId, $userId, $status === 'paid' ? $userId : null, $date, $status, $discount, $vat, $total, 'internal', $marker]);
    $orderId = (int)$db->lastInsertId();

    $detailStmt = $db->prepare("INSERT INTO sale_order_detail (sale_order_id, menu_id, qty, price, status) VALUES (?, ?, ?, ?, 'ordered')");
    foreach ($items as $code => $qty) {
        $menu = $menuByCode[$code];
        $detailStmt->execute([$orderId, $menu['id'], $qty, $menu['price']]);
    }

    if (in_array($status, ['served', 'paid'], true)) {
        $issueStmt = $db->prepare("INSERT INTO inventory_issue (created_by, issue_type, issue_date, status, note) VALUES (?, 'sale', ?, 'completed', ?)");
        $issueStmt->execute([$userId, date('Y-m-d', strtotime($date)), "DEMO_SEED_ORDER_#$orderId"]);
        $issueId = (int)$db->lastInsertId();

        $issueDetailStmt = $db->prepare('INSERT INTO inventory_issue_detail (issue_id, ingredient_id, qty) VALUES (?, ?, ?)');
        $logStmt = $db->prepare("INSERT INTO inventory_log (ingredient_id, qty_change, type, related_id, note, created_by) VALUES (?, ?, 'issue', ?, ?, ?)");
        $ingredientTotals = [];
        foreach ($items as $code => $qty) {
            $menuId = $menuByCode[$code]['id'];
            foreach ($recipeByMenuId[$menuId] ?? [] as $ingredientId => $recipeQty) {
                $ingredientTotals[$ingredientId] = ($ingredientTotals[$ingredientId] ?? 0) + ($recipeQty * $qty);
            }
        }
        foreach ($ingredientTotals as $ingredientId => $qty) {
            $issueDetailStmt->execute([$issueId, $ingredientId, $qty]);
            $logStmt->execute([$ingredientId, -$qty, $issueId, "DEMO_SEED_ORDER_#$orderId", $userId]);
        }
    }

    $db->prepare('UPDATE restaurant_table SET status = ? WHERE id = ?')->execute([$status === 'paid' || $status === 'cancel' ? 'free' : 'occupied', $tableId]);
}

$categories = [
    ['Rau củ tươi', 'Nhóm rau củ dùng hằng ngày'],
    ['Thịt cá hải sản', 'Nguồn đạm chính'],
    ['Gia vị - sốt', 'Gia vị và sốt bếp nóng'],
    ['Gạo mì bún', 'Tinh bột và nguyên liệu nền'],
    ['Đồ uống', 'Nguyên liệu pha chế'],
];
foreach ($categories as $cat) {
    $stmt = $db->prepare('INSERT IGNORE INTO ingredient_category (name, description) VALUES (?, ?)');
    $stmt->execute($cat);
}

$ingredients = [
    ['code' => 'DEMO-RICE', 'name' => 'Gạo Jasmine', 'category' => 'Gạo mì bún', 'unit' => 'kg', 'price' => 22000, 'min_stock' => 20, 'description' => 'Gạo thơm nấu cơm suất', 'supplier' => 'Nông sản An Phú'],
    ['code' => 'DEMO-CHICKEN', 'name' => 'Đùi gà rút xương', 'category' => 'Thịt cá hải sản', 'unit' => 'kg', 'price' => 85000, 'min_stock' => 8, 'description' => 'Gà tươi dùng cho món nướng và cơm gà', 'supplier' => 'Thực phẩm GreenFarm'],
    ['code' => 'DEMO-BEEF', 'name' => 'Bò thăn Úc', 'category' => 'Thịt cá hải sản', 'unit' => 'kg', 'price' => 240000, 'min_stock' => 5, 'description' => 'Bò mềm cho phở và áp chảo', 'supplier' => 'Meat House'],
    ['code' => 'DEMO-SHRIMP', 'name' => 'Tôm sú bóc vỏ', 'category' => 'Thịt cá hải sản', 'unit' => 'kg', 'price' => 180000, 'min_stock' => 5, 'description' => 'Tôm tươi sơ chế', 'supplier' => 'Hải sản Biển Xanh'],
    ['code' => 'DEMO-SALMON', 'name' => 'Cá hồi phi lê', 'category' => 'Thịt cá hải sản', 'unit' => 'kg', 'price' => 320000, 'min_stock' => 3, 'description' => 'Cá hồi Na Uy phi lê', 'supplier' => 'Hải sản Biển Xanh'],
    ['code' => 'DEMO-NOODLE', 'name' => 'Bánh phở tươi', 'category' => 'Gạo mì bún', 'unit' => 'kg', 'price' => 28000, 'min_stock' => 15, 'description' => 'Bánh phở dùng trong ngày', 'supplier' => 'Lò phở Minh Tâm'],
    ['code' => 'DEMO-VEG', 'name' => 'Rau ăn kèm tổng hợp', 'category' => 'Rau củ tươi', 'unit' => 'kg', 'price' => 30000, 'min_stock' => 10, 'description' => 'Xà lách, rau thơm, dưa leo', 'supplier' => 'Rau sạch Đà Lạt'],
    ['code' => 'DEMO-TOMATO', 'name' => 'Cà chua Đà Lạt', 'category' => 'Rau củ tươi', 'unit' => 'kg', 'price' => 26000, 'min_stock' => 8, 'description' => 'Cà chua làm sốt và salad', 'supplier' => 'Rau sạch Đà Lạt'],
    ['code' => 'DEMO-OIL', 'name' => 'Dầu ăn cao cấp', 'category' => 'Gia vị - sốt', 'unit' => 'lít', 'price' => 42000, 'min_stock' => 12, 'description' => 'Dầu chiên xào', 'supplier' => 'Gia vị Việt'],
    ['code' => 'DEMO-SPICE', 'name' => 'Gia vị bếp tổng hợp', 'category' => 'Gia vị - sốt', 'unit' => 'kg', 'price' => 65000, 'min_stock' => 6, 'description' => 'Muối, đường, tiêu, bột nêm quy đổi', 'supplier' => 'Gia vị Việt'],
    ['code' => 'DEMO-CHEESE', 'name' => 'Phô mai Mozzarella', 'category' => 'Gia vị - sốt', 'unit' => 'kg', 'price' => 155000, 'min_stock' => 4, 'description' => 'Phô mai cho mì Ý và món nướng', 'supplier' => 'Dairy Pro'],
    ['code' => 'DEMO-TEA', 'name' => 'Trà ô long', 'category' => 'Đồ uống', 'unit' => 'kg', 'price' => 120000, 'min_stock' => 2, 'description' => 'Trà pha lạnh', 'supplier' => 'Trà Việt'],
    ['code' => 'DEMO-MILK', 'name' => 'Sữa tươi thanh trùng', 'category' => 'Đồ uống', 'unit' => 'lít', 'price' => 34000, 'min_stock' => 8, 'description' => 'Sữa dùng pha chế', 'supplier' => 'Dairy Pro'],
];

$ingredientIds = [];
foreach ($ingredients as $ingredient) {
    $ingredientIds[$ingredient['code']] = upsertIngredient($db, $ingredient);
    addReceiptLog($db, $ingredientIds[$ingredient['code']], 220, $adminId);
}

$menus = [
    ['code' => 'DEMO-COMGA', 'name' => 'Cơm gà nướng mật ong', 'price' => 89000, 'description' => 'Đùi gà nướng mật ong, cơm Jasmine, salad'],
    ['code' => 'DEMO-PHOBO', 'name' => 'Phở bò thăn đặc biệt', 'price' => 79000, 'description' => 'Bò thăn mềm, nước dùng thơm, rau ăn kèm'],
    ['code' => 'DEMO-TOMBO', 'name' => 'Tôm sốt bơ tỏi', 'price' => 135000, 'description' => 'Tôm sú áp chảo sốt bơ tỏi'],
    ['code' => 'DEMO-SALMON', 'name' => 'Cá hồi áp chảo sốt chanh', 'price' => 169000, 'description' => 'Cá hồi áp chảo ăn kèm rau củ'],
    ['code' => 'DEMO-PASTA', 'name' => 'Mì Ý bò phô mai', 'price' => 99000, 'description' => 'Mì Ý sốt bò bằm và phô mai'],
    ['code' => 'DEMO-SALAD', 'name' => 'Salad gà rau củ', 'price' => 69000, 'description' => 'Gà xé, rau sạch, sốt nhẹ'],
    ['code' => 'DEMO-TEA', 'name' => 'Trà ô long sữa', 'price' => 39000, 'description' => 'Trà ô long, sữa tươi, đá'],
    ['code' => 'DEMO-RICEBEEF', 'name' => 'Cơm bò áp chảo', 'price' => 119000, 'description' => 'Bò áp chảo, cơm nóng, salad'],
];

$menuByCode = [];
foreach ($menus as $menu) {
    $menu['id'] = upsertMenu($db, $menu);
    $menuByCode[$menu['code']] = $menu;
}

$recipes = [
    'DEMO-COMGA' => ['DEMO-RICE' => 0.18, 'DEMO-CHICKEN' => 0.25, 'DEMO-VEG' => 0.08, 'DEMO-OIL' => 0.03, 'DEMO-SPICE' => 0.03],
    'DEMO-PHOBO' => ['DEMO-NOODLE' => 0.22, 'DEMO-BEEF' => 0.12, 'DEMO-VEG' => 0.06, 'DEMO-SPICE' => 0.04],
    'DEMO-TOMBO' => ['DEMO-SHRIMP' => 0.22, 'DEMO-OIL' => 0.03, 'DEMO-SPICE' => 0.03, 'DEMO-VEG' => 0.05],
    'DEMO-SALMON' => ['DEMO-SALMON' => 0.20, 'DEMO-VEG' => 0.10, 'DEMO-OIL' => 0.02, 'DEMO-SPICE' => 0.03],
    'DEMO-PASTA' => ['DEMO-NOODLE' => 0.18, 'DEMO-BEEF' => 0.08, 'DEMO-TOMATO' => 0.12, 'DEMO-CHEESE' => 0.04, 'DEMO-SPICE' => 0.02],
    'DEMO-SALAD' => ['DEMO-CHICKEN' => 0.12, 'DEMO-VEG' => 0.18, 'DEMO-TOMATO' => 0.06, 'DEMO-SPICE' => 0.015],
    'DEMO-TEA' => ['DEMO-TEA' => 0.025, 'DEMO-MILK' => 0.18],
    'DEMO-RICEBEEF' => ['DEMO-RICE' => 0.18, 'DEMO-BEEF' => 0.16, 'DEMO-VEG' => 0.08, 'DEMO-OIL' => 0.02, 'DEMO-SPICE' => 0.025],
];

$recipeByMenuId = [];
foreach ($recipes as $menuCode => $recipeItems) {
    $menuId = $menuByCode[$menuCode]['id'];
    $db->prepare('DELETE FROM recipe WHERE menu_id = ?')->execute([$menuId]);
    $stmt = $db->prepare('INSERT INTO recipe (menu_id, ingredient_id, qty) VALUES (?, ?, ?)');
    foreach ($recipeItems as $ingredientCode => $qty) {
        $ingredientId = $ingredientIds[$ingredientCode];
        $stmt->execute([$menuId, $ingredientId, $qty]);
        $recipeByMenuId[$menuId][$ingredientId] = $qty;
    }
}

$tableIds = [];
for ($i = 1; $i <= 10; $i++) {
    $tableIds[$i] = ensureTable($db, 'B' . str_pad((string)$i, 2, '0', STR_PAD_LEFT));
}

$today = max(strtotime(date('Y-m-d')), strtotime('2026-05-25'));
$dailyOrders = [
    [['DEMO-COMGA' => 2, 'DEMO-TEA' => 2], ['DEMO-PHOBO' => 3, 'DEMO-SALAD' => 1], ['DEMO-TOMBO' => 2, 'DEMO-RICEBEEF' => 1]],
    [['DEMO-SALMON' => 2, 'DEMO-TEA' => 3], ['DEMO-PASTA' => 2, 'DEMO-SALAD' => 2], ['DEMO-COMGA' => 4]],
    [['DEMO-PHOBO' => 5], ['DEMO-RICEBEEF' => 2, 'DEMO-TEA' => 2], ['DEMO-TOMBO' => 1, 'DEMO-SALMON' => 1]],
    [['DEMO-PASTA' => 3, 'DEMO-TEA' => 4], ['DEMO-COMGA' => 2, 'DEMO-SALAD' => 2], ['DEMO-PHOBO' => 2]],
    [['DEMO-SALMON' => 1, 'DEMO-RICEBEEF' => 2], ['DEMO-TOMBO' => 3], ['DEMO-COMGA' => 2, 'DEMO-TEA' => 2]],
    [['DEMO-SALAD' => 3, 'DEMO-TEA' => 3], ['DEMO-PHOBO' => 2, 'DEMO-COMGA' => 1], ['DEMO-PASTA' => 2]],
    [['DEMO-COMGA' => 3, 'DEMO-TEA' => 2], ['DEMO-SALMON' => 2], ['DEMO-RICEBEEF' => 2, 'DEMO-TOMBO' => 1], ['DEMO-PHOBO' => 2]],
];

for ($offset = 6; $offset >= 0; $offset--) {
    $dayIndex = 6 - $offset;
    $date = date('Y-m-d', strtotime("-{$offset} days", $today));
    foreach ($dailyOrders[$dayIndex] as $idx => $items) {
        $status = 'paid';
        if ($offset === 0 && $idx === 3) $status = 'open';
        if ($offset === 0 && $idx === 2) $status = 'served';
        $time = $date . ' ' . str_pad((string)(10 + $idx * 3), 2, '0', STR_PAD_LEFT) . ':' . str_pad((string)(12 + $idx * 7), 2, '0', STR_PAD_LEFT) . ':00';
        createOrder($db, $tableIds[($idx % 8) + 1], $items, $menuByCode, $recipeByMenuId, $ingredientIds, $time, $status, $idx === 1 ? 10000 : 0, 8, $adminId);
    }

    $expenseMarker = 'DEMO_SEED_EXPENSE_' . $date;
    $exists = scalarValue($db, 'SELECT id FROM expense WHERE description = ?', [$expenseMarker]);
    if (!$exists) {
        $stmt = $db->prepare('INSERT INTO expense (expense_type, amount, description, created_by, expense_date) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute(['Vận hành ngày', 350000 + ($dayIndex * 25000), $expenseMarker, $adminId, $date]);
    }
}

echo "Demo seed completed for 7 recent days.\n";
