<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<?php
$flash = getFlash();
$itemCount = count($items ?? []);
$estimatedCost = 0;
foreach (($items ?? []) as $it) {
    $estimatedCost += (float)($it['qty'] ?? 0) * (float)($it['purchase_price'] ?? 0);
}
$menuPrice = (float)($menu['price'] ?? 0);
$grossMargin = $menuPrice > 0 ? $menuPrice - $estimatedCost : 0;
?>

<style>
    .recipe-summary-card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        background: #fff;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        height: 100%;
    }

    .recipe-summary-card .value {
        font-size: 1.6rem;
        font-weight: 800;
    }

    .recipe-table-card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
    }

    .recipe-table th {
        background: #f8f9fa;
        color: #6c757d;
        font-size: .88rem;
        white-space: nowrap;
    }

    .recipe-table td {
        vertical-align: middle;
    }
</style>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
                <p class="text-muted mb-1">Công thức món ăn</p>
                <h3 class="mb-0 fw-bold">
                    <?php echo isset($menu) ? htmlspecialchars($menu['name']) : 'Chọn món để xem công thức'; ?>
                </h3>
            </div>
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <?php if (!empty($menu_id)): ?>
                    <a href="<?php echo BASE_URL; ?>/recipe/create?menu_id=<?php echo (int)$menu_id; ?>" class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> Chỉnh sửa công thức
                    </a>
                    <a href="<?php echo BASE_URL; ?>/recipe" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-repeat"></i> Chọn món khác
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/recipe" class="btn btn-primary">
                        <i class="bi bi-search"></i> Chọn món
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'warning' ? 'warning' : 'danger'); ?> shadow-sm">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($menu_id)): ?>
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="recipe-summary-card">
                        <div class="text-muted">Nguyên liệu</div>
                        <div class="value"><?php echo $itemCount; ?></div>
                        <small class="text-muted">Trong một phần món</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="recipe-summary-card">
                        <div class="text-muted">Giá vốn ước tính</div>
                        <div class="value text-danger"><?php echo number_format($estimatedCost, 0, ',', '.'); ?> đ</div>
                        <small class="text-muted">Theo giá nhập hiện tại</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="recipe-summary-card">
                        <div class="text-muted">Giá bán</div>
                        <div class="value text-primary"><?php echo number_format($menuPrice, 0, ',', '.'); ?> đ</div>
                        <small class="text-muted">Từ danh mục món ăn</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="recipe-summary-card">
                        <div class="text-muted">Lãi gộp tham khảo</div>
                        <div class="value text-success"><?php echo number_format($grossMargin, 0, ',', '.'); ?> đ</div>
                        <small class="text-muted">Chưa tính chi phí cố định</small>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="recipe-table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 recipe-table">
                    <thead>
                        <tr>
                            <th>Nguyên liệu</th>
                            <th>Đơn vị</th>
                            <th class="text-end">Định lượng</th>
                            <th class="text-end">Giá nhập</th>
                            <th class="text-end">Giá vốn / phần</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $it): ?>
                                <?php $lineCost = (float)($it['qty'] ?? 0) * (float)($it['purchase_price'] ?? 0); ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($it['ingredient_name'] ?? ''); ?></div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($it['menu_name'] ?? ''); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($it['unit'] ?? ''); ?></td>
                                    <td class="text-end fw-semibold"><?php echo rtrim(rtrim(number_format((float)$it['qty'], 3, ',', '.'), '0'), ','); ?></td>
                                    <td class="text-end"><?php echo number_format((float)($it['purchase_price'] ?? 0), 0, ',', '.'); ?> đ</td>
                                    <td class="text-end fw-semibold text-danger"><?php echo number_format($lineCost, 0, ',', '.'); ?> đ</td>
                                    <td class="text-end">
                                        <a href="<?php echo BASE_URL; ?>/recipe/delete/<?php echo (int)$it['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa nguyên liệu này khỏi công thức?')">
                                            <i class="bi bi-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-plus" style="font-size: 2rem;"></i>
                                    <div class="mt-2 mb-3">Món này chưa có công thức.</div>
                                    <?php if (!empty($menu_id)): ?>
                                        <a href="<?php echo BASE_URL; ?>/recipe/create?menu_id=<?php echo (int)$menu_id; ?>" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Thiết lập công thức
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($items)): ?>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Tổng giá vốn ước tính</th>
                                <th class="text-end text-danger"><?php echo number_format($estimatedCost, 0, ',', '.'); ?> đ</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
