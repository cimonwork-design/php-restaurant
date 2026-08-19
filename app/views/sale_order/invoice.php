<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .invoice-wrap {
        max-width: 760px;
        margin: 24px auto;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 28px;
    }

    .invoice-title {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 2px solid #111827;
        padding-bottom: 16px;
        margin-bottom: 20px;
    }

    .invoice-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px 24px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .invoice-total {
        max-width: 360px;
        margin-left: auto;
    }

    @media print {
        .sidebar,
        .navbar,
        .no-print {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
            background: #fff !important;
        }

        .content-area {
            padding: 0 !important;
        }

        .invoice-wrap {
            max-width: none;
            margin: 0;
            border: 0;
            padding: 0;
        }
    }
</style>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <div class="no-print d-flex justify-content-between align-items-center mb-3">
            <h3><i class="bi bi-printer me-2"></i>Hóa đơn #<?php echo (int)$order['id']; ?></h3>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>In hóa đơn
                </button>
                <a class="btn btn-secondary" href="<?php echo BASE_URL; ?>/sale_order">Quay lại</a>
            </div>
        </div>

        <div class="invoice-wrap">
            <div class="invoice-title">
                <div>
                    <h2 class="mb-1">Nhà hàng</h2>
                    <div class="text-muted">Hóa đơn bán hàng</div>
                </div>
                <div class="text-end">
                    <h4 class="mb-1">#<?php echo (int)$order['id']; ?></h4>
                    <span class="badge <?php echo $order['status'] === 'paid' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                        <?php echo htmlspecialchars($order['status']); ?>
                    </span>
                </div>
            </div>

            <?php if ($order['status'] !== 'paid'): ?>
                <div class="alert alert-warning no-print">
                    Đơn này chưa thanh toán. Hóa đơn hiện là bản tạm tính.
                </div>
            <?php endif; ?>

            <div class="invoice-meta">
                <div><strong>Thời gian:</strong> <?php echo date('d/m/Y H:i', strtotime($order['order_time'])); ?></div>
                <div><strong>Bàn:</strong> <?php echo htmlspecialchars($order['table_number'] ?? '-'); ?></div>
                <div><strong>Nhân viên tạo:</strong> <?php echo htmlspecialchars($order['waiter_name'] ?? '-'); ?></div>
                <div><strong>Thu ngân:</strong> <?php echo htmlspecialchars($order['cashier_name'] ?? '-'); ?></div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Món</th>
                            <th style="text-align:right;">SL</th>
                            <th style="text-align:right;">Đơn giá</th>
                            <th style="text-align:right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $detail): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($detail['menu_name']); ?></td>
                                <td style="text-align:right;"><?php echo (int)$detail['qty']; ?></td>
                                <td style="text-align:right;"><?php echo number_format($detail['price'], 0, ',', '.'); ?>đ</td>
                                <td style="text-align:right;"><?php echo number_format($detail['qty'] * $detail['price'], 0, ',', '.'); ?>đ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php
            $discount = (float)($order['discount'] ?? 0);
            $vatRate = (float)($order['vat_rate'] ?? 0);
            $afterDiscount = max(0, (float)$subtotal - $discount);
            $vatAmount = $afterDiscount * $vatRate / 100;
            ?>
            <div class="invoice-total">
                <div class="d-flex justify-content-between py-1">
                    <span>Tạm tính</span>
                    <strong><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span>Giảm giá</span>
                    <strong><?php echo number_format($discount, 0, ',', '.'); ?>đ</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span>VAT (<?php echo number_format($vatRate, 2); ?>%)</span>
                    <strong><?php echo number_format($vatAmount, 0, ',', '.'); ?>đ</strong>
                </div>
                <div class="d-flex justify-content-between border-top mt-2 pt-2 fs-5">
                    <span>Tổng thanh toán</span>
                    <strong><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</strong>
                </div>
            </div>

            <div class="text-center text-muted mt-4">
                Cảm ơn quý khách!
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
