<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<?php
$flash = getFlash();
$selectedMenuId = !empty($selectedMenu['id']) ? (int)$selectedMenu['id'] : 0;
$ingredientMap = [];

foreach ($ingredients as $ing) {
    $ingredientMap[] = [
        'id' => (int)$ing['id'],
        'code' => $ing['code'] ?? '',
        'name' => $ing['name'] ?? '',
        'unit' => $ing['unit'] ?? '',
        'purchase_price' => (float)($ing['purchase_price'] ?? 0),
    ];
}

$initialRecipe = [];
foreach (($existingRecipe ?? []) as $row) {
    $initialRecipe[] = [
        'id' => (int)$row['ingredient_id'],
        'qty' => (float)$row['qty'],
    ];
}
?>

<style>
    .recipe-page {
        background: #f8f9fa;
        min-height: 100vh;
    }

    .recipe-shell {
        display: grid;
        grid-template-columns: minmax(320px, 0.95fr) minmax(420px, 1.35fr);
        gap: 1rem;
    }

    .recipe-panel {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
    }

    .recipe-panel-header {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .ingredient-list {
        max-height: 620px;
        overflow: auto;
    }

    .ingredient-item {
        width: 100%;
        border: 0;
        border-bottom: 1px solid #f1f3f5;
        background: #fff;
        padding: .9rem 1rem;
        text-align: left;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        transition: background .15s ease;
    }

    .ingredient-item:hover {
        background: #f8fbff;
    }

    .ingredient-item.is-selected {
        background: #eef6ff;
    }

    .recipe-table th {
        color: #6c757d;
        font-size: .86rem;
        white-space: nowrap;
    }

    .recipe-table td {
        vertical-align: middle;
    }

    .qty-input {
        min-width: 110px;
    }

    .summary-box {
        border-top: 1px solid #e9ecef;
        background: #fbfcfd;
        padding: 1rem;
    }

    @media (max-width: 1200px) {
        .recipe-shell {
            grid-template-columns: 1fr;
        }

        .ingredient-list {
            max-height: 360px;
        }
    }
</style>

<div class="main-content recipe-page">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
                <p class="text-muted mb-1">Công thức món ăn</p>
                <h3 class="mb-0 fw-bold">Thiết lập định lượng nguyên liệu</h3>
            </div>
            <a href="<?php echo $selectedMenuId ? BASE_URL . '/recipe?menu_id=' . $selectedMenuId : BASE_URL . '/recipe'; ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?> shadow-sm">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/recipe/store" method="post" id="recipeForm">
            <div class="recipe-panel mb-3">
                <div class="recipe-panel-header">
                    <label class="form-label fw-semibold">Món ăn</label>
                    <?php if (!empty($selectedMenu)): ?>
                        <input type="hidden" name="menu_id" value="<?php echo $selectedMenuId; ?>">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <div class="fs-5 fw-bold"><?php echo htmlspecialchars($selectedMenu['name']); ?></div>
                                <div class="text-muted small">
                                    Giá bán: <?php echo number_format((float)($selectedMenu['price'] ?? 0), 0, ',', '.'); ?> đ
                                </div>
                            </div>
                            <a href="<?php echo BASE_URL; ?>/recipe/create" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-repeat"></i> Chọn món khác
                            </a>
                        </div>
                    <?php else: ?>
                        <select class="form-select" name="menu_id" id="menuSelect" required>
                            <option value="">Chọn món cần lập công thức</option>
                            <?php foreach ($menuItems as $m): ?>
                                <option value="<?php echo (int)$m['id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
            </div>

            <div class="recipe-shell">
                <div class="recipe-panel">
                    <div class="recipe-panel-header">
                        <label class="form-label fw-semibold mb-2">Tìm và thêm nguyên liệu</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control" id="ingredientSearch" placeholder="Nhập tên hoặc mã nguyên liệu">
                        </div>
                    </div>
                    <div class="ingredient-list" id="ingredientList"></div>
                </div>

                <div class="recipe-panel">
                    <div class="recipe-panel-header d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <div class="fw-semibold">Định lượng cho một phần</div>
                            <div class="text-muted small">Có thể thêm nhiều nguyên liệu trước khi lưu.</div>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="clearRecipeBtn">
                            <i class="bi bi-trash"></i> Xóa hết
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover recipe-table mb-0">
                            <thead>
                                <tr>
                                    <th>Nguyên liệu</th>
                                    <th class="text-end">Số lượng</th>
                                    <th>Đơn vị</th>
                                    <th class="text-end">Giá vốn</th>
                                    <th class="text-end">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="recipeRows"></tbody>
                        </table>
                    </div>

                    <div class="summary-box">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Số nguyên liệu</span>
                            <strong id="ingredientCount">0</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Giá vốn ước tính / phần</span>
                            <strong class="text-danger" id="estimatedCost">0 đ</strong>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo $selectedMenuId ? BASE_URL . '/recipe?menu_id=' . $selectedMenuId : BASE_URL . '/recipe'; ?>" class="btn btn-outline-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu công thức
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const ingredients = <?php echo json_encode($ingredientMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const initialRecipe = <?php echo json_encode($initialRecipe, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const recipe = new Map();

    function formatMoney(value) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(value || 0)) + ' đ';
    }

    function formatQty(value) {
        return Number(value || 0).toLocaleString('vi-VN', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 3
        });
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getIngredient(id) {
        return ingredients.find(item => Number(item.id) === Number(id));
    }

    function addIngredient(id, qty = 1) {
        const ingredient = getIngredient(id);
        if (!ingredient) {
            return;
        }

        const current = recipe.get(Number(id));
        recipe.set(Number(id), current ? current + Number(qty) : Number(qty));
        renderRecipe();
        renderIngredients();
    }

    function removeIngredient(id) {
        recipe.delete(Number(id));
        renderRecipe();
        renderIngredients();
    }

    function updateQty(id, value) {
        const qty = Number(value);
        if (qty > 0) {
            recipe.set(Number(id), qty);
        } else {
            recipe.delete(Number(id));
        }
        renderRecipe();
        renderIngredients();
    }

    function renderIngredients() {
        const keyword = (document.getElementById('ingredientSearch').value || '').toLowerCase().trim();
        const list = document.getElementById('ingredientList');
        const filtered = ingredients.filter(item => {
            return !keyword ||
                item.name.toLowerCase().includes(keyword) ||
                String(item.code || '').toLowerCase().includes(keyword);
        });

        if (!filtered.length) {
            list.innerHTML = '<div class="text-center text-muted py-5">Không tìm thấy nguyên liệu.</div>';
            return;
        }

        list.innerHTML = filtered.map(item => {
            const selected = recipe.has(Number(item.id));
            return `
                <button type="button" class="ingredient-item ${selected ? 'is-selected' : ''}" onclick="addIngredient(${item.id})">
                    <span>
                        <span class="fw-semibold d-block">${escapeHtml(item.name)}</span>
                        <span class="text-muted small">${escapeHtml(item.code || '')} · ${formatMoney(item.purchase_price)} / ${escapeHtml(item.unit)}</span>
                    </span>
                    <span class="badge ${selected ? 'bg-primary' : 'bg-light text-dark'}">${selected ? 'Đã thêm' : 'Thêm'}</span>
                </button>
            `;
        }).join('');
    }

    function renderRecipe() {
        const rows = document.getElementById('recipeRows');
        const entries = Array.from(recipe.entries())
            .map(([id, qty]) => ({ ingredient: getIngredient(id), qty }))
            .filter(row => row.ingredient)
            .sort((a, b) => a.ingredient.name.localeCompare(b.ingredient.name, 'vi'));

        if (!entries.length) {
            rows.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-basket" style="font-size: 2rem;"></i>
                        <div class="mt-2">Chưa có nguyên liệu nào trong công thức.</div>
                    </td>
                </tr>
            `;
        } else {
            rows.innerHTML = entries.map(row => {
                const item = row.ingredient;
                const cost = Number(row.qty) * Number(item.purchase_price || 0);
                return `
                    <tr>
                        <td>
                            <input type="hidden" name="ingredient_id[]" value="${item.id}">
                            <div class="fw-semibold">${escapeHtml(item.name)}</div>
                            <div class="text-muted small">${escapeHtml(item.code || '')}</div>
                        </td>
                        <td class="text-end">
                            <input class="form-control form-control-sm qty-input text-end ms-auto" type="number" step="0.001" min="0.001" name="qty[]" value="${row.qty}" onchange="updateQty(${item.id}, this.value)" required>
                        </td>
                        <td>${escapeHtml(item.unit)}</td>
                        <td class="text-end fw-semibold">${formatMoney(cost)}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeIngredient(${item.id})">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        const totalCost = entries.reduce((sum, row) => sum + Number(row.qty) * Number(row.ingredient.purchase_price || 0), 0);
        document.getElementById('ingredientCount').textContent = entries.length;
        document.getElementById('estimatedCost').textContent = formatMoney(totalCost);
    }

    document.addEventListener('DOMContentLoaded', function() {
        initialRecipe.forEach(row => addIngredient(row.id, row.qty));
        renderIngredients();
        renderRecipe();

        document.getElementById('ingredientSearch').addEventListener('input', renderIngredients);
        document.getElementById('clearRecipeBtn').addEventListener('click', function() {
            if (recipe.size === 0 || confirm('Xóa toàn bộ nguyên liệu khỏi công thức hiện tại?')) {
                recipe.clear();
                renderRecipe();
                renderIngredients();
            }
        });

        document.getElementById('recipeForm').addEventListener('submit', function(event) {
            if (recipe.size === 0) {
                event.preventDefault();
                alert('Vui lòng thêm ít nhất một nguyên liệu vào công thức.');
            }
        });
    });
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
