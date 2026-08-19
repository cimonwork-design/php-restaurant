<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .main-content {
        margin-left: 260px;
        background: #f8f9fa;
    }

    .report-header {
        background: white;
        padding: 2rem;
        margin-bottom: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-left: 4px solid;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .stat-card.revenue {
        border-left-color: #10b981;
    }

    .stat-card.expense {
        border-left-color: #ef4444;
    }

    .stat-card.profit {
        border-left-color: #667eea;
    }

    .stat-card.orders {
        border-left-color: #f59e0b;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #212529;
    }

    .stat-icon {
        float: right;
        font-size: 2rem;
        opacity: 0.2;
    }

    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .filter-form {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filter-group {
        flex: 0 1 auto;
    }

    .filter-group label {
        font-size: 0.9rem;
        font-weight: 500;
        display: block;
        margin-bottom: 0.5rem;
        color: #495057;
    }

    .filter-group input {
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    .table-section {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .table {
        margin-bottom: 0;
    }

    .table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .table thead th {
        border: none;
        font-weight: 600;
        padding: 1rem 0.75rem;
    }

    .table tbody td {
        padding: 1rem 0.75rem;
        border-color: #e9ecef;
    }

    .table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: background 0.2s;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .table tfoot {
        background: #f8f9fa;
        border-top: 2px solid #dee2e6;
    }

    .table tfoot td {
        font-weight: 600;
        color: #212529;
    }

    .text-success {
        color: #10b981 !important;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .text-primary {
        color: #667eea !important;
    }

    .text-warning {
        color: #f59e0b !important;
    }

    .report-row {
        cursor: pointer;
    }

    .report-row:hover {
        background: #eef6ff !important;
    }
</style>

<div class="main-content">
    <div class="container-fluid mt-4 mb-5">
        <!-- Page Header -->
        <div class="report-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0" style="font-weight: 700;">
                        <i class="bi bi-bar-chart"></i> Báo cáo doanh thu
                    </h1>
                    <p class="text-muted small mt-1">Thống kê doanh thu, chi phí và lợi nhuận</p>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="stats-grid">
            <div class="stat-card revenue">
                <i class="bi bi-cash-flow stat-icon"></i>
                <div class="stat-label"><i class="bi bi-arrow-up-circle"></i> Tổng doanh thu</div>
                <div class="stat-value text-success"><?php echo number_format($totals['revenue'], 0, ',', '.'); ?>đ</div>
                <small class="text-muted"><?php echo count($period); ?> ngày</small>
            </div>

            <div class="stat-card expense">
                <i class="bi bi-inbox-fill stat-icon"></i>
                <div class="stat-label"><i class="bi bi-basket"></i> Chi phí nguyên liệu</div>
                <div class="stat-value text-danger"><?php echo number_format($totals['ingredient_cost'], 0, ',', '.'); ?>đ</div>
                <small class="text-muted">Từ đơn hàng</small>
            </div>

            <div class="stat-card profit">
                <i class="bi bi-graph-up stat-icon"></i>
                <div class="stat-label"><i class="bi bi-check-circle"></i> Lợi nhuận (trước cơ định)</div>
                <div class="stat-value text-primary"><?php echo number_format($totals['revenue'] - $totals['ingredient_cost'], 0, ',', '.'); ?>đ</div>
                <small class="text-muted">Doanh thu - Chi phí NL</small>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card expense">
                <i class="bi bi-wrench stat-icon"></i>
                <div class="stat-label"><i class="bi bi-wrench"></i> Chi phí cố định</div>
                <div class="stat-value text-danger"><?php echo number_format($totals['fixed_expense'], 0, ',', '.'); ?>đ</div>
                <small class="text-muted">Từ trang chi phí</small>
            </div>

            <div class="stat-card expense">
                <i class="bi bi-calculator stat-icon"></i>
                <div class="stat-label"><i class="bi bi-list"></i> Tổng chi phí</div>
                <div class="stat-value text-danger"><?php echo number_format($totals['total_expense'], 0, ',', '.'); ?>đ</div>
                <small class="text-muted">Cơ định + Nguyên liệu</small>
            </div>

            <div class="stat-card profit">
                <i class="bi bi-graph-up-arrow stat-icon"></i>
                <div class="stat-label"><i class="bi bi-check-circle"></i> Tổng báo cáo</div>
                <div class="stat-value text-primary"><?php echo number_format($totals['net'], 0, ',', '.'); ?>đ</div>
                <small class="text-muted">Doanh thu - Tổng chi phí</small>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="get" class="filter-form">
                <div class="filter-group">
                    <label for="start_date">Từ ngày</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start); ?>">
                </div>
                <div class="filter-group">
                    <label for="end_date">Đến ngày</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end); ?>">
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="table-section">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 12%;">Ngày</th>
                            <th style="width: 16%; text-align: right;">Doanh thu</th>
                            <th style="width: 13%; text-align: right;">Chi phí cố định</th>
                            <th style="width: 13%; text-align: right;">Chi phí NL</th>
                            <th style="width: 13%; text-align: right;">Tổng chi phí</th>
                            <th style="width: 16%; text-align: right;">Lợi nhuận</th>
                            <th style="width: 10%; text-align: center;">Số đơn</th>
                            <th style="width: 10%; text-align: right;">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($period as $d):
                            $row = $map[$d];
                            $total_expense = $row['fixed_expense'] + $row['ingredient_cost'];
                            $net = $row['revenue'] - $total_expense;
                        ?>
                            <?php $detailUrl = BASE_URL . '/report?start_date=' . urlencode($start) . '&end_date=' . urlencode($end) . '&day=' . urlencode($d); ?>
                            <tr class="report-row" data-href="<?php echo htmlspecialchars($detailUrl); ?>">
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/report?start_date=<?php echo urlencode($start); ?>&end_date=<?php echo urlencode($end); ?>&day=<?php echo urlencode($d); ?>"
                                        class="text-decoration-none fw-bold">
                                        <?php echo date('d/m/Y', strtotime($d)); ?>
                                    </a>
                                    <br>
                                    <small class="text-muted"><?php
                                                                $dayOfWeek = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
                                                                echo $dayOfWeek[date('w', strtotime($d))];
                                                                ?></small>
                                </td>
                                <td style="text-align: right;" class="text-success">
                                    <strong><?php echo number_format($row['revenue'], 0, ',', '.'); ?>đ</strong>
                                </td>
                                <td style="text-align: right;" class="text-danger">
                                    <strong><?php echo number_format($row['fixed_expense'], 0, ',', '.'); ?>đ</strong>
                                </td>
                                <td style="text-align: right;" class="text-danger">
                                    <strong><?php echo number_format($row['ingredient_cost'], 0, ',', '.'); ?>đ</strong>
                                </td>
                                <td style="text-align: right;" class="text-danger">
                                    <strong><?php echo number_format($total_expense, 0, ',', '.'); ?>đ</strong>
                                </td>
                                <td style="text-align: right;" class="text-primary">
                                    <strong><?php echo number_format($net, 0, ',', '.'); ?>đ</strong>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge bg-warning text-dark"><?php echo intval($row['orders']); ?> đơn</span>
                                </td>
                                <td style="text-align: right;">
                                    <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars($detailUrl); ?>">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <strong>Tổng cộng</strong>
                            </td>
                            <td style="text-align: right;" class="text-success">
                                <strong><?php echo number_format($totals['revenue'], 0, ',', '.'); ?>đ</strong>
                            </td>
                            <td style="text-align: right;" class="text-danger">
                                <strong><?php echo number_format($totals['fixed_expense'], 0, ',', '.'); ?>đ</strong>
                            </td>
                            <td style="text-align: right;" class="text-danger">
                                <strong><?php echo number_format($totals['ingredient_cost'], 0, ',', '.'); ?>đ</strong>
                            </td>
                            <td style="text-align: right;" class="text-danger">
                                <strong><?php echo number_format($totals['total_expense'], 0, ',', '.'); ?>đ</strong>
                            </td>
                            <td style="text-align: right;" class="text-primary">
                                <strong><?php echo number_format($totals['net'], 0, ',', '.'); ?>đ</strong>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-info"><?php echo intval($totals['orders']); ?> đơn</span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php
            if (isset($pagination)) {
                $paginationVar = $pagination;
                $baseUrlVar = $baseUrl ?? (BASE_URL . '/report');
                $pagination = $paginationVar;
                $baseUrl = $baseUrlVar;
                include __DIR__ . '/../layouts/pagination.php';
            }
            ?>
        </div>

        <?php if (!empty($selectedDay) && is_array($dayDetails)): ?>
            <?php
            $detailRevenue = 0;
            $detailIngredient = 0;
            foreach ($dayDetails['orders'] as $order) {
                $detailRevenue += (float)$order['total_amount'];
                $detailIngredient += (float)$order['ingredient_cost'];
            }
            $detailFixed = (float)$dayDetails['fixed_expense'];
            ?>
            <div class="table-section mt-4 d-none" id="dayDetailInline">
                <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1"><i class="bi bi-calendar-day"></i> Chi tiết ngày <?php echo date('d/m/Y', strtotime($selectedDay)); ?></h5>
                        <small class="text-muted">
                            Doanh thu: <?php echo number_format($detailRevenue, 0, ',', '.'); ?>đ |
                            Chi phí NL: <?php echo number_format($detailIngredient, 0, ',', '.'); ?>đ |
                            Chi phí cố định: <?php echo number_format($detailFixed, 0, ',', '.'); ?>đ |
                            Lãi: <?php echo number_format($detailRevenue - $detailIngredient - $detailFixed, 0, ',', '.'); ?>đ
                        </small>
                    </div>
                    <a class="btn btn-sm btn-outline-secondary" href="<?php echo BASE_URL; ?>/report?start_date=<?php echo urlencode($start); ?>&end_date=<?php echo urlencode($end); ?>">
                        Đóng chi tiết
                    </a>
                </div>

                <?php if (!empty($dayDetails['orders'])): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Đơn</th>
                                    <th>Bàn</th>
                                    <th>Giờ</th>
                                    <th>Món bán</th>
                                    <th style="text-align:right;">Doanh thu</th>
                                    <th style="text-align:right;">Chi phí NL</th>
                                    <th style="text-align:right;">Lãi gộp</th>
                                    <th style="text-align:center;">Hóa đơn</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dayDetails['orders'] as $order): ?>
                                    <?php
                                    $orderItems = $dayDetails['items'][$order['id']] ?? [];
                                    $orderCost = (float)$order['ingredient_cost'];
                                    $orderRevenue = (float)$order['total_amount'];
                                    ?>
                                    <tr>
                                        <td><strong>#<?php echo (int)$order['id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['table_number'] ?? '-'); ?></td>
                                        <td><?php echo date('H:i', strtotime($order['order_time'])); ?></td>
                                        <td>
                                            <?php foreach ($orderItems as $item): ?>
                                                <div>
                                                    <?php echo htmlspecialchars($item['menu_name']); ?>
                                                    <span class="text-muted">x<?php echo (int)$item['qty']; ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </td>
                                        <td style="text-align:right;" class="text-success">
                                            <strong><?php echo number_format($orderRevenue, 0, ',', '.'); ?>đ</strong>
                                        </td>
                                        <td style="text-align:right;" class="text-danger">
                                            <strong><?php echo number_format($orderCost, 0, ',', '.'); ?>đ</strong>
                                        </td>
                                        <td style="text-align:right;" class="text-primary">
                                            <strong><?php echo number_format($orderRevenue - $orderCost, 0, ',', '.'); ?>đ</strong>
                                        </td>
                                        <td style="text-align:center;">
                                            <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_URL; ?>/sale_order/invoice/<?php echo (int)$order['id']; ?>">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-muted">Không có đơn đã thanh toán trong ngày này.</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($selectedDay) && is_array($dayDetails)): ?>
    <div class="modal fade" id="dayDetailModal" tabindex="-1" aria-labelledby="dayDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dayDetailModalLabel">
                        <i class="bi bi-calendar-day"></i> Chi tiết ngày <?php echo date('d/m/Y', strtotime($selectedDay)); ?>
                    </h5>
                    <a class="btn-close" aria-label="Đóng" href="<?php echo BASE_URL; ?>/report?start_date=<?php echo urlencode($start); ?>&end_date=<?php echo urlencode($end); ?>"></a>
                </div>
                <div class="modal-body p-0" id="dayDetailModalBody"></div>
                <div class="modal-footer">
                    <a class="btn btn-outline-secondary" href="<?php echo BASE_URL; ?>/report?start_date=<?php echo urlencode($start); ?>&end_date=<?php echo urlencode($end); ?>">Đóng</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    document.querySelectorAll('.report-row').forEach(row => {
        row.addEventListener('click', event => {
            if (event.target.closest('a, button')) return;
            window.location.href = row.dataset.href;
        });
    });

    <?php if (!empty($selectedDay) && is_array($dayDetails)): ?>
        window.addEventListener('load', () => {
            const inlineDetail = document.getElementById('dayDetailInline');
            const modalBody = document.getElementById('dayDetailModalBody');
            if (inlineDetail && modalBody && window.bootstrap) {
                modalBody.innerHTML = inlineDetail.innerHTML;
                const detailModal = new bootstrap.Modal(document.getElementById('dayDetailModal'));
                detailModal.show();
            }
        });
    <?php endif; ?>
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
