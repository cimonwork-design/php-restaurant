<?php
/**
 * Báo cáo kho - tình trạng tồn kho, cảnh báo và lịch sử xuất kho.
 */
include BASE_PATH . '/app/views/layouts/header.php';
include BASE_PATH . '/app/views/layouts/sidebar.php';

$stockGroups = [
    'critical' => [
        'label' => 'Hết hàng',
        'items' => $critical ?? [],
        'count' => $total_critical ?? 0,
        'icon' => 'bi-exclamation-circle',
        'badge' => 'bg-danger',
        'button' => 'btn-outline-danger',
        'note' => 'Số lượng bằng 0 hoặc âm',
    ],
    'warning' => [
        'label' => 'Sắp hết',
        'items' => $warning ?? [],
        'count' => $total_warning ?? 0,
        'icon' => 'bi-lightning-charge',
        'badge' => 'bg-warning text-dark',
        'button' => 'btn-outline-warning',
        'note' => 'Dưới mức tối thiểu',
    ],
    'normal' => [
        'label' => 'Bình thường',
        'items' => $normal ?? [],
        'count' => $total_normal ?? 0,
        'icon' => 'bi-check-circle',
        'badge' => 'bg-success',
        'button' => 'btn-outline-success',
        'note' => 'Đủ hàng',
    ],
];
?>

<style>
    .main-content {
        margin-left: 260px;
        padding: 2rem;
        background: #f8f9fa;
        min-height: 100vh;
    }

    .stock-header {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .stock-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: .5rem;
    }

    .stat-card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 1.25rem;
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }

    .stat-card .stat-number {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }

    .stock-tabs .nav-link {
        border: 0;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        color: #6c757d;
        font-weight: 600;
        padding: 1rem 1.25rem;
    }

    .stock-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: #fff;
    }

    .stock-table-card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }

    .stock-table th {
        color: #495057;
        font-size: .88rem;
        white-space: nowrap;
        background: #f8f9fa;
    }

    .stock-table td {
        vertical-align: middle;
    }

    .stock-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.25rem;
        padding: .35rem .6rem;
        border-radius: 999px;
        font-weight: 700;
    }

    .modal-table td,
    .modal-table th {
        vertical-align: middle;
    }

    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
            padding: 1rem;
        }

        .stock-header {
            flex-direction: column;
        }

        .stock-actions {
            justify-content: flex-start;
        }
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        <div class="stock-header">
            <div>
                <h1 class="h2 mb-1 fw-bold">
                    <i class="bi bi-graph-up"></i> Báo cáo kho
                </h1>
                <p class="text-muted mb-0">Theo dõi tồn kho, cảnh báo và lịch sử quản lý nguyên liệu.</p>
            </div>
            <div class="stock-actions">
                <button type="button" class="btn btn-success" id="restockCartBtn" style="display: none;" onclick="goToCreateReceipt()">
                    <i class="bi bi-check-circle"></i> Xác nhận bổ sung (<span id="cartCount">0</span>)
                </button>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#issueHistoryModal">
                    <i class="bi bi-clock-history"></i> Xem lịch sử
                </button>
                <a href="<?php echo BASE_URL; ?>/inventory_receipt" class="btn btn-outline-info">
                    <i class="bi bi-box-arrow-in-right"></i> Nhập kho
                </a>
                <a href="<?php echo BASE_URL; ?>/report/add_stock_out" class="btn btn-warning">
                    <i class="bi bi-box-seam"></i> Thêm xuất kho
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <?php foreach ($stockGroups as $key => $group): ?>
                <?php
                $toneClass = $key === 'critical' ? 'text-danger' : ($key === 'warning' ? 'text-warning' : 'text-success');
                ?>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi <?php echo $group['icon']; ?> <?php echo $toneClass; ?>" style="font-size: 2rem;"></i>
                            <div>
                                <div class="text-muted fw-semibold"><?php echo $group['label']; ?></div>
                                <div class="stat-number <?php echo $toneClass; ?>"><?php echo (int)$group['count']; ?></div>
                                <small class="text-muted"><?php echo $group['note']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="stock-table-card">
            <ul class="nav nav-tabs stock-tabs" id="stockTabs" role="tablist">
                <?php $first = true; ?>
                <?php foreach ($stockGroups as $key => $group): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $first ? 'active' : ''; ?>" id="<?php echo $key; ?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo $key; ?>" type="button" role="tab">
                            <i class="bi <?php echo $group['icon']; ?>"></i>
                            <?php echo $group['label']; ?>
                            <span class="badge <?php echo $group['badge']; ?> ms-2"><?php echo (int)$group['count']; ?></span>
                        </button>
                    </li>
                    <?php $first = false; ?>
                <?php endforeach; ?>
            </ul>

            <div class="tab-content" id="stockTabsContent">
                <?php $first = true; ?>
                <?php foreach ($stockGroups as $key => $group): ?>
                    <div class="tab-pane fade <?php echo $first ? 'show active' : ''; ?>" id="<?php echo $key; ?>" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 stock-table">
                                <thead>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Tên nguyên liệu</th>
                                        <th>Đơn vị</th>
                                        <th class="text-end">Số lượng</th>
                                        <th class="text-end">Mức tối thiểu</th>
                                        <th class="text-end">Giá nhập</th>
                                        <th class="text-end">Giá trị kho</th>
                                        <th class="text-end">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($group['items'])): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5">
                                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                                <div class="mt-2">Không có nguyên liệu trong nhóm này.</div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($group['items'] as $ing): ?>
                                            <?php
                                            $currentQty = (float)($ing['current_qty'] ?? 0);
                                            $qtyTone = $currentQty <= 0 ? 'bg-danger-subtle text-danger' : ($currentQty <= (float)($ing['min_stock'] ?? 0) ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success');
                                            ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($ing['code'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($ing['name'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($ing['unit'] ?? ''); ?></td>
                                                <td class="text-end">
                                                    <span class="stock-pill <?php echo $qtyTone; ?>"><?php echo rtrim(rtrim(number_format($currentQty, 3, ',', '.'), '0'), ','); ?></span>
                                                </td>
                                                <td class="text-end"><?php echo rtrim(rtrim(number_format((float)($ing['min_stock'] ?? 0), 3, ',', '.'), '0'), ','); ?></td>
                                                <td class="text-end"><?php echo number_format((float)($ing['purchase_price'] ?? 0), 0, ',', '.'); ?> đ</td>
                                                <td class="text-end fw-semibold"><?php echo number_format((float)($ing['cost'] ?? 0), 0, ',', '.'); ?> đ</td>
                                                <td class="text-end">
                                                    <?php if ($key === 'normal'): ?>
                                                        <button class="btn btn-sm btn-outline-success" disabled><i class="bi bi-check-circle"></i> Đủ hàng</button>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm <?php echo $group['button']; ?>" disabled><i class="bi <?php echo $group['icon']; ?>"></i> <?php echo $group['label']; ?></button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-sm btn-outline-primary ms-1" onclick="addRestock(<?php echo (int)$ing['id']; ?>, <?php echo htmlspecialchars(json_encode($ing['name'] ?? '', JSON_UNESCAPED_UNICODE), ENT_QUOTES); ?>)">
                                                        <i class="bi bi-plus-circle"></i> Bổ sung
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $first = false; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="issueHistoryModal" tabindex="-1" aria-labelledby="issueHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="issueHistoryModalLabel">
                        <i class="bi bi-clock-history"></i> Lịch sử xuất kho
                    </h5>
                    <small class="text-muted">Các phiếu xuất trong 10 ngày gần đây</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($recent_issues)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover modal-table">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Loại xuất</th>
                                    <th>Người tạo</th>
                                    <th>Chi tiết</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_issues as $issue): ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo date('d/m/Y', strtotime($issue['issue_date'])); ?></td>
                                        <td><span class="badge bg-info"><?php echo htmlspecialchars($issue['issue_type'] ?? ''); ?></span></td>
                                        <td><?php echo htmlspecialchars($issue['created_by'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($issue['details'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($issue['note'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (isset($pagination)): ?>
                        <?php
                        $paginationVar = $pagination;
                        $baseUrlVar = $baseUrl ?? (BASE_URL . '/report/stock_report');
                        $pagination = $paginationVar;
                        $baseUrl = $baseUrlVar;
                        include BASE_PATH . '/app/views/layouts/pagination.php';
                        ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <div class="mt-2">Chưa có lịch sử xuất kho gần đây.</div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <a href="<?php echo BASE_URL; ?>/inventory_issue" class="btn btn-outline-secondary">Mở trang phiếu xuất</a>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        updateCartUI();
    });

    function updateCartUI() {
        const cart = JSON.parse(sessionStorage.getItem('restockCart') || '[]');
        const btn = document.getElementById('restockCartBtn');
        const count = document.getElementById('cartCount');

        if (!btn || !count) {
            return;
        }

        if (cart.length > 0) {
            btn.style.display = 'inline-block';
            count.textContent = cart.length;
        } else {
            btn.style.display = 'none';
            count.textContent = '0';
        }
    }

    function addRestock(ingredientId, ingredientName) {
        const qty = prompt(`Nhập số lượng muốn bổ sung cho "${ingredientName}":`, '');
        const numericQty = parseFloat(qty);

        if (!qty || isNaN(numericQty) || numericQty <= 0) {
            return;
        }

        const cart = JSON.parse(sessionStorage.getItem('restockCart') || '[]');
        const existingIndex = cart.findIndex(item => item.ingredient_id == ingredientId);

        if (existingIndex >= 0) {
            cart[existingIndex].qty = parseFloat(cart[existingIndex].qty) + numericQty;
            alert(`Đã cập nhật số lượng cho "${ingredientName}". Tổng: ${cart[existingIndex].qty}`);
        } else {
            cart.push({
                ingredient_id: ingredientId,
                ingredient_name: ingredientName,
                qty: numericQty
            });
            alert(`Đã thêm "${ingredientName}" với số lượng ${numericQty} vào danh sách bổ sung`);
        }

        sessionStorage.setItem('restockCart', JSON.stringify(cart));
        updateCartUI();
    }

    function goToCreateReceipt() {
        const cart = JSON.parse(sessionStorage.getItem('restockCart') || '[]');
        if (cart.length === 0) {
            alert('Danh sách bổ sung trống');
            return;
        }

        if (confirm(`Bạn muốn tạo phiếu nhập kho với ${cart.length} nguyên liệu đã chọn?`)) {
            window.location.href = `<?php echo BASE_URL; ?>/inventory_receipt/create_from_restock`;
        }
    }
</script>

<?php include BASE_PATH . '/app/views/layouts/footer.php'; ?>
