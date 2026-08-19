<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .order-toolbar,
    .order-table-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    }

    .order-toolbar {
        padding: 14px;
        margin-bottom: 14px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
    }

    .status-open { background: #fff7ed; color: #c2410c; }
    .status-served { background: #eff6ff; color: #1d4ed8; }
    .status-paid { background: #ecfdf5; color: #047857; }
    .status-cancel { background: #fef2f2; color: #b91c1c; }

    .action-set {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .action-set .btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    .orders-table th:last-child,
    .orders-table td:last-child {
        text-align: right;
    }

    .orders-table th:nth-child(6),
    .orders-table td:nth-child(6) {
        text-align: right;
    }

    .summary-strip {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .summary-box {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
    }

    @media (max-width: 768px) {
        .summary-strip {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>
        <?php
        $statusMap = [
            'open' => ['label' => 'Đang mở', 'class' => 'status-open'],
            'served' => ['label' => 'Chờ thanh toán', 'class' => 'status-served'],
            'paid' => ['label' => 'Đã thanh toán', 'class' => 'status-paid'],
            'cancel' => ['label' => 'Đã hủy', 'class' => 'status-cancel'],
        ];
        $counts = ['open' => 0, 'served' => 0, 'paid' => 0, 'cancel' => 0];
        foreach ($items as $order) {
            if (isset($counts[$order['status']])) $counts[$order['status']]++;
        }
        ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-1"><i class="bi bi-receipt me-2"></i>Đơn hàng tại quầy</h3>
                <div class="text-muted">Theo dõi, hoàn thành, thanh toán và in hóa đơn.</div>
            </div>
            <a href="<?php echo BASE_URL; ?>/sale_order/create" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Tạo đơn mới
            </a>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : ($flash['type'] === 'warning' ? 'alert-warning' : 'alert-danger'); ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="summary-strip">
            <div class="summary-box"><div class="text-muted small">Đang mở</div><strong><?php echo $counts['open']; ?></strong></div>
            <div class="summary-box"><div class="text-muted small">Chờ thanh toán</div><strong><?php echo $counts['served']; ?></strong></div>
            <div class="summary-box"><div class="text-muted small">Đã thanh toán</div><strong><?php echo $counts['paid']; ?></strong></div>
            <div class="summary-box"><div class="text-muted small">Đã hủy</div><strong><?php echo $counts['cancel']; ?></strong></div>
        </div>

        <div class="order-toolbar">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <?php foreach ($statusMap as $key => $meta): ?>
                            <option value="<?php echo $key; ?>" <?php echo (($filters['status'] ?? '') === $key) ? 'selected' : ''; ?>>
                                <?php echo $meta['label']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Tìm mã đơn hoặc bàn</label>
                    <input type="search" name="q" class="form-control" value="<?php echo htmlspecialchars($filters['q'] ?? ''); ?>" placeholder="VD: 12 hoặc B01">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>Lọc</button>
                    <a class="btn btn-outline-secondary" href="<?php echo BASE_URL; ?>/sale_order">Đặt lại</a>
                </div>
            </form>
        </div>

        <div class="order-table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 orders-table">
                    <thead class="table-light">
                        <tr>
                            <th>Đơn</th>
                            <th>Bàn</th>
                            <th>Thời gian</th>
                            <th>Số món</th>
                            <th>Trạng thái</th>
                            <th>Tổng tiền</th>
                            <th style="min-width: 360px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $it): ?>
                                <?php $status = $statusMap[$it['status']] ?? ['label' => $it['status'], 'class' => 'status-open']; ?>
                                <tr>
                                    <td><strong>#<?php echo (int)$it['id']; ?></strong></td>
                                    <td><?php echo $it['table_number'] ? 'Bàn ' . htmlspecialchars($it['table_number']) : '-'; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($it['order_time'])); ?></td>
                                    <td><?php echo (int)($it['item_count'] ?? 0); ?></td>
                                    <td><span class="status-pill <?php echo $status['class']; ?>"><?php echo $status['label']; ?></span></td>
                                    <td><strong><?php echo number_format($it['total_amount'], 0, ',', '.'); ?>đ</strong></td>
                                    <td>
                                        <div class="action-set">
                                            <a class="btn btn-sm btn-outline-secondary" href="<?php echo BASE_URL; ?>/sale_order/edit/<?php echo (int)$it['id']; ?>">
                                                <i class="bi bi-eye"></i> Xem
                                            </a>
                                            <?php if ($it['status'] === 'open'): ?>
                                                <a class="btn btn-sm btn-outline-info" href="<?php echo BASE_URL; ?>/sale_order/addItem/<?php echo (int)$it['id']; ?>">
                                                    <i class="bi bi-plus"></i> Thêm món
                                                </a>
                                                <a class="btn btn-sm btn-outline-success" href="<?php echo BASE_URL; ?>/sale_order/complete/<?php echo (int)$it['id']; ?>" onclick="return confirm('Hoàn thành phục vụ và trừ kho cho đơn #<?php echo (int)$it['id']; ?>?')">
                                                    <i class="bi bi-check2-all"></i> Hoàn thành
                                                </a>
                                                <a class="btn btn-sm btn-success" href="<?php echo BASE_URL; ?>/sale_order/pay/<?php echo (int)$it['id']; ?>" onclick="return confirm('Thanh toán đơn #<?php echo (int)$it['id']; ?>?')">
                                                    <i class="bi bi-cash"></i> Thanh toán
                                                </a>
                                                <a class="btn btn-sm btn-outline-danger" href="<?php echo BASE_URL; ?>/sale_order/cancel/<?php echo (int)$it['id']; ?>" onclick="return confirm('Hủy đơn #<?php echo (int)$it['id']; ?>?')">
                                                    <i class="bi bi-x-circle"></i> Hủy
                                                </a>
                                            <?php elseif ($it['status'] === 'served'): ?>
                                                <a class="btn btn-sm btn-success" href="<?php echo BASE_URL; ?>/sale_order/pay/<?php echo (int)$it['id']; ?>" onclick="return confirm('Thanh toán đơn #<?php echo (int)$it['id']; ?>?')">
                                                    <i class="bi bi-cash"></i> Thanh toán
                                                </a>
                                            <?php elseif ($it['status'] === 'paid'): ?>
                                                <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_URL; ?>/sale_order/invoice/<?php echo (int)$it['id']; ?>">
                                                    <i class="bi bi-printer"></i> In hóa đơn
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <div class="mt-2">Không có đơn hàng phù hợp.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
        if (isset($pagination)) {
            $paginationVar = $pagination;
            $baseUrlVar = $baseUrl ?? (BASE_URL . '/sale_order');
            $pagination = $paginationVar;
            $baseUrl = $baseUrlVar;
            include __DIR__ . '/../layouts/pagination.php';
        }
        ?>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
