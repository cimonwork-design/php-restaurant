<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .pos-shell {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 18px;
        align-items: start;
    }

    .pos-toolbar,
    .pos-panel,
    .cart-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    }

    .pos-toolbar {
        padding: 14px;
        margin-bottom: 14px;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
        padding: 14px;
        max-height: calc(100vh - 270px);
        overflow: auto;
    }

    .menu-tile {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        padding: 12px;
        text-align: left;
        min-height: 112px;
        transition: border-color .15s, box-shadow .15s, transform .15s;
    }

    .menu-tile:hover {
        border-color: #2563eb;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.12);
        transform: translateY(-1px);
    }

    .menu-code {
        color: #64748b;
        font-size: 12px;
        margin-bottom: 6px;
    }

    .menu-name {
        font-weight: 700;
        color: #111827;
        min-height: 42px;
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
        min-height: 180px;
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
        .pos-shell {
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

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-1"><i class="bi bi-shop-window me-2"></i>Tạo đơn tại quầy</h3>
                <div class="text-muted">Chọn bàn, bấm món để thêm vào giỏ và tạo đơn.</div>
            </div>
            <a href="<?php echo BASE_URL; ?>/sale_order" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'warning' ? 'warning' : 'danger'); ?> alert-dismissible fade show" role="alert">
                <?php echo $flash['message']; ?>
                <?php if ($flash['type'] === 'warning' && isset($_SESSION['inventory_warnings'])): ?>
                    <hr>
                    <strong>Nguyên liệu thiếu:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($_SESSION['inventory_warnings'] as $warning): ?>
                            <li>
                                <?php echo htmlspecialchars($warning['menu_name']); ?> x<?php echo (int)$warning['menu_qty']; ?>
                                <?php foreach ($warning['missing'] as $missing): ?>
                                    <div class="small text-danger">
                                        <?php echo htmlspecialchars($missing['ingredient_name']); ?>:
                                        cần <?php echo number_format($missing['needed'], 2); ?> <?php echo htmlspecialchars($missing['unit']); ?>,
                                        còn <?php echo number_format($missing['available'], 2); ?> <?php echo htmlspecialchars($missing['unit']); ?>
                                    </div>
                                <?php endforeach; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php unset($_SESSION['inventory_warnings']); ?>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo BASE_URL; ?>/sale_order/store" id="posForm">
            <div class="pos-shell">
                <div>
                    <div class="pos-toolbar">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Bàn</label>
                                <select name="table_id" class="form-select" required>
                                    <option value="">Chọn bàn trống</option>
                                    <?php foreach ($tables as $table): ?>
                                        <?php if ($table['status'] === 'free'): ?>
                                            <option value="<?php echo (int)$table['id']; ?>">Bàn <?php echo htmlspecialchars($table['number']); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Thời gian</label>
                                <input type="datetime-local" name="order_time" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tìm món</label>
                                <input type="search" id="menuSearch" class="form-control" placeholder="Tên hoặc mã món">
                            </div>
                        </div>
                    </div>

                    <div class="pos-panel">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <strong>Thực đơn</strong>
                            <span class="text-muted small"><?php echo count($menuItems); ?> món</span>
                        </div>
                        <div class="menu-grid" id="menuGrid"></div>
                    </div>
                </div>

                <div class="cart-panel">
                    <h5 class="mb-3"><i class="bi bi-basket2 me-2"></i>Giỏ hàng</h5>
                    <div class="cart-list mb-3" id="cartList">
                        <div class="text-muted text-center py-5">Chưa chọn món</div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Giảm giá</label>
                            <input type="number" min="0" step="1000" name="discount" id="discountInput" class="form-control" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">VAT (%)</label>
                            <input type="number" min="0" step="0.01" name="vat_rate" id="vatInput" class="form-control" value="0">
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

                    <button type="submit" class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-check-circle me-1"></i>Tạo đơn
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const menuItems = <?php echo json_encode($menuItems, JSON_UNESCAPED_UNICODE); ?>;
    const cart = new Map();
    const menuGrid = document.getElementById('menuGrid');
    const cartList = document.getElementById('cartList');
    const searchInput = document.getElementById('menuSearch');
    const discountInput = document.getElementById('discountInput');
    const vatInput = document.getElementById('vatInput');
    const form = document.getElementById('posForm');

    const money = value => new Intl.NumberFormat('vi-VN').format(Math.max(0, value || 0)) + 'đ';

    function renderMenu() {
        const q = searchInput.value.trim().toLowerCase();
        const filtered = menuItems.filter(item => {
            return !q ||
                String(item.name || '').toLowerCase().includes(q) ||
                String(item.code || '').toLowerCase().includes(q);
        });

        menuGrid.innerHTML = filtered.map(item => `
            <button type="button" class="menu-tile" data-id="${item.id}">
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
        const item = menuItems.find(menu => Number(menu.id) === id);
        if (!item) return;
        const current = cart.get(id) || {...item, qty: 0};
        current.qty += 1;
        cart.set(id, current);
        renderCart();
    }

    function changeQty(id, delta) {
        const item = cart.get(id);
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) {
            cart.delete(id);
        } else {
            cart.set(id, item);
        }
        renderCart();
    }

    function renderCart() {
        if (cart.size === 0) {
            cartList.innerHTML = '<div class="text-muted text-center py-5">Chưa chọn món</div>';
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
                            <button type="button" data-action="minus" data-id="${item.id}">-</button>
                            <span>${item.qty}</span>
                            <button type="button" data-action="plus" data-id="${item.id}">+</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-action="remove" data-id="${item.id}">
                            <i class="bi bi-trash"></i>
                        </button>
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
        const grand = afterDiscount + vatAmount;
        document.getElementById('subtotalText').textContent = money(subtotal);
        document.getElementById('vatText').textContent = money(vatAmount);
        document.getElementById('grandText').textContent = money(grand);
    }

    form.addEventListener('submit', event => {
        form.querySelectorAll('.compiled-qty').forEach(input => input.remove());
        if (cart.size === 0) {
            event.preventDefault();
            alert('Vui lòng chọn ít nhất một món');
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

    searchInput.addEventListener('input', renderMenu);
    discountInput.addEventListener('input', renderTotals);
    vatInput.addEventListener('input', renderTotals);
    renderMenu();
    renderCart();
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
