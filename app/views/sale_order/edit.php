<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .order-detail-shell {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 18px;
        align-items: start;
    }

    .detail-panel,
    .cart-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    }

    .detail-panel {
        padding: 14px;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
        max-height: 430px;
        overflow: auto;
    }

    .menu-tile {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        padding: 12px;
        text-align: left;
        min-height: 108px;
        transition: border-color .15s, box-shadow .15s, transform .15s;
    }

    .menu-tile:hover:not(:disabled) {
        border-color: #2563eb;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.12);
        transform: translateY(-1px);
    }

    .menu-tile:disabled {
        opacity: .55;
        cursor: not-allowed;
    }

    .menu-code {
        color: #64748b;
        font-size: 12px;
        margin-bottom: 6px;
    }

    .menu-name {
        font-weight: 700;
        color: #111827;
        min-height: 40px;
    }

    .menu-price {
        color: #047857;
        font-weight: 700;
        margin-top: 8px;
    }

    .cart-panel {
        position: sticky;
        top: 16px;
        padding: 14px;
    }

    .cart-list {
        min-height: 210px;
        max-height: calc(100vh - 470px);
        overflow: auto;
        border: 1px solid #eef2f7;
        border-radius: 8px;
        padding: 8px;
        background: #f8fafc;
    }

    .cart-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 8px;
    }

    .qty-stepper {
        display: inline-flex;
        align-items: center;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        overflow: hidden;
    }

    .qty-stepper button {
        border: 0;
        background: #f3f4f6;
        width: 30px;
        height: 30px;
    }

    .qty-stepper button:disabled {
        opacity: .45;
    }

    .qty-stepper span {
        display: inline-block;
        width: 34px;
        text-align: center;
        font-weight: 700;
    }

    .total-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
    }

    @media (max-width: 992px) {
        .order-detail-shell {
            grid-template-columns: 1fr;
        }

        .cart-panel {
            position: static;
        }
    }
</style>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>
        <?php $editable = $item['status'] === 'open'; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-1"><i class="bi bi-receipt me-2"></i>Chi tiết đơn #<?php echo (int)$item['id']; ?></h3>
                <div class="text-muted">Giao diện chi tiết theo kiểu bán hàng tại quầy.</div>
            </div>
            <div class="d-flex gap-2">
                <?php if ($item['status'] === 'paid'): ?>
                    <a href="<?php echo BASE_URL; ?>/sale_order/invoice/<?php echo (int)$item['id']; ?>" class="btn btn-outline-primary">
                        <i class="bi bi-printer"></i> In hóa đơn
                    </a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/sale_order" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : ($flash['type'] === 'warning' ? 'alert-warning' : 'alert-danger'); ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <?php if (!$editable): ?>
            <div class="alert alert-warning">
                Đơn đã đổi trạng thái nên chỉ xem chi tiết. Chỉ đơn đang mở mới được chỉnh sửa món.
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo BASE_URL; ?>/sale_order/update/<?php echo (int)$item['id']; ?>" id="orderDetailForm">
            <div class="order-detail-shell">
                <div>
                    <div class="detail-panel mb-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Bàn</label>
                                <select name="table_id" class="form-select" <?php echo $editable ? '' : 'disabled'; ?>>
                                    <option value="">Không chọn</option>
                                    <?php foreach ($tables as $table): ?>
                                        <option value="<?php echo (int)$table['id']; ?>" <?php echo $table['id'] == $item['table_id'] ? 'selected' : ''; ?>>
                                            Bàn <?php echo htmlspecialchars($table['number']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Thời gian</label>
                                <input type="datetime-local" name="order_time" class="form-control" value="<?php echo str_replace(' ', 'T', substr($item['order_time'], 0, 16)); ?>" <?php echo $editable ? '' : 'disabled'; ?>>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Trạng thái</label>
                                <div>
                                    <span class="badge bg-<?php echo $item['status'] === 'paid' ? 'success' : ($item['status'] === 'served' ? 'primary' : ($item['status'] === 'cancel' ? 'danger' : 'warning text-dark')); ?>">
                                        <?php echo htmlspecialchars($item['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tìm món</label>
                                <input type="search" id="menuSearch" class="form-control" placeholder="Tên hoặc mã món" <?php echo $editable ? '' : 'disabled'; ?>>
                            </div>
                        </div>
                    </div>

                    <div class="detail-panel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <strong>Thực đơn</strong>
                            <span class="text-muted small"><?php echo count($menuItems); ?> món</span>
                        </div>
                        <div class="menu-grid" id="menuGrid"></div>
                    </div>
                </div>

                <div class="cart-panel">
                    <h5 class="mb-3"><i class="bi bi-basket2 me-2"></i>Món trong đơn</h5>
                    <div class="cart-list mb-3" id="cartList"></div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Giảm giá</label>
                            <input type="number" min="0" step="1000" name="discount" id="discountInput" class="form-control" value="<?php echo htmlspecialchars($item['discount'] ?? 0); ?>" <?php echo $editable ? '' : 'disabled'; ?>>
                        </div>
                        <div class="col-6">
                            <label class="form-label">VAT (%)</label>
                            <input type="number" min="0" step="0.01" name="vat_rate" id="vatInput" class="form-control" value="<?php echo htmlspecialchars($item['vat_rate'] ?? 0); ?>" <?php echo $editable ? '' : 'disabled'; ?>>
                        </div>
                    </div>

                    <div class="border-top pt-2">
                        <div class="total-line">
                            <span>Tạm tính</span>
                            <strong id="subtotalText">0đ</strong>
                        </div>
                        <div class="total-line">
                            <span>VAT</span>
                            <strong id="vatText">0đ</strong>
                        </div>
                        <div class="total-line fs-5 border-top mt-2 pt-2">
                            <span>Tổng</span>
                            <strong class="text-primary" id="grandText">0đ</strong>
                        </div>
                    </div>

                    <?php if ($editable): ?>
                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-save"></i> Lưu thay đổi
                        </button>
                    <?php else: ?>
                        <a class="btn btn-primary w-100 mt-3" href="<?php echo BASE_URL; ?>/sale_order/invoice/<?php echo (int)$item['id']; ?>">
                            <i class="bi bi-printer"></i> Xem hóa đơn
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const editable = <?php echo $editable ? 'true' : 'false'; ?>;
    const menuItems = <?php echo json_encode($menuItems, JSON_UNESCAPED_UNICODE); ?>;
    const initial = <?php echo json_encode($detailMap ?? [], JSON_UNESCAPED_UNICODE); ?>;
    const cart = new Map();
    const menuGrid = document.getElementById('menuGrid');
    const cartList = document.getElementById('cartList');
    const searchInput = document.getElementById('menuSearch');
    const discountInput = document.getElementById('discountInput');
    const vatInput = document.getElementById('vatInput');
    const form = document.getElementById('orderDetailForm');

    const money = value => new Intl.NumberFormat('vi-VN').format(Math.max(0, value || 0)) + 'đ';

    Object.keys(initial).forEach(id => {
        const item = menuItems.find(menu => Number(menu.id) === Number(id));
        if (item) cart.set(Number(id), { ...item, qty: Number(initial[id]) || 1 });
    });

    function renderMenu() {
        const q = (searchInput?.value || '').trim().toLowerCase();
        const filtered = menuItems.filter(item => {
            return !q ||
                String(item.name || '').toLowerCase().includes(q) ||
                String(item.code || '').toLowerCase().includes(q);
        });

        menuGrid.innerHTML = filtered.map(item => `
            <button type="button" class="menu-tile" data-id="${item.id}" ${editable ? '' : 'disabled'}>
                <div class="menu-code">${item.code || ''}</div>
                <div class="menu-name">${item.name || ''}</div>
                <div class="menu-price">${money(Number(item.price))}</div>
            </button>
        `).join('') || '<div class="text-muted p-3">Không tìm thấy món.</div>';

        menuGrid.querySelectorAll('.menu-tile').forEach(tile => {
            tile.addEventListener('click', () => addToCart(Number(tile.dataset.id)));
        });
    }

    function addToCart(id) {
        if (!editable) return;
        const item = menuItems.find(menu => Number(menu.id) === id);
        if (!item) return;
        const current = cart.get(id) || { ...item, qty: 0 };
        current.qty += 1;
        cart.set(id, current);
        renderCart();
    }

    function changeQty(id, delta) {
        if (!editable) return;
        const item = cart.get(id);
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) cart.delete(id);
        else cart.set(id, item);
        renderCart();
    }

    function renderCart() {
        if (cart.size === 0) {
            cartList.innerHTML = '<div class="text-muted text-center py-5">Chưa có món trong đơn</div>';
        } else {
            cartList.innerHTML = Array.from(cart.values()).map(item => `
                <div class="cart-row">
                    <div>
                        <strong>${item.name}</strong>
                        <div class="small text-muted">${money(Number(item.price))}</div>
                        <div class="small text-success">${money(Number(item.price) * item.qty)}</div>
                    </div>
                    <div class="text-end">
                        <div class="qty-stepper mb-2">
                            <button type="button" data-action="minus" data-id="${item.id}" ${editable ? '' : 'disabled'}>-</button>
                            <span>${item.qty}</span>
                            <button type="button" data-action="plus" data-id="${item.id}" ${editable ? '' : 'disabled'}>+</button>
                        </div>
                        ${editable ? `<button type="button" class="btn btn-sm btn-outline-danger" data-action="remove" data-id="${item.id}">
                            <i class="bi bi-trash"></i>
                        </button>` : ''}
                    </div>
                </div>
            `).join('');
        }

        cartList.querySelectorAll('button[data-action]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = Number(btn.dataset.id);
                if (btn.dataset.action === 'plus') changeQty(id, 1);
                if (btn.dataset.action === 'minus') changeQty(id, -1);
                if (btn.dataset.action === 'remove') {
                    cart.delete(id);
                    renderCart();
                }
            });
        });
        renderTotals();
    }

    function renderTotals() {
        const subtotal = Array.from(cart.values()).reduce((sum, item) => sum + Number(item.price) * item.qty, 0);
        const discount = Math.max(0, Number(discountInput.value) || 0);
        const vatRate = Math.max(0, Number(vatInput.value) || 0);
        const afterDiscount = Math.max(0, subtotal - discount);
        const vatAmount = afterDiscount * vatRate / 100;
        document.getElementById('subtotalText').textContent = money(subtotal);
        document.getElementById('vatText').textContent = money(vatAmount);
        document.getElementById('grandText').textContent = money(afterDiscount + vatAmount);
    }

    form.addEventListener('submit', event => {
        form.querySelectorAll('.compiled-qty').forEach(input => input.remove());
        if (!editable) {
            event.preventDefault();
            return;
        }
        if (cart.size === 0) {
            event.preventDefault();
            alert('Đơn cần có ít nhất một món');
            return;
        }
        cart.forEach(item => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `qty[${item.id}]`;
            input.value = item.qty;
            input.className = 'compiled-qty';
            form.appendChild(input);
        });
    });

    if (searchInput) searchInput.addEventListener('input', renderMenu);
    if (discountInput) discountInput.addEventListener('input', renderTotals);
    if (vatInput) vatInput.addEventListener('input', renderTotals);
    renderMenu();
    renderCart();
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
