<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .menu-recipe-card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .menu-recipe-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        color: inherit;
        text-decoration: none;
        border-bottom: 1px solid #f1f3f5;
    }

    .menu-recipe-row:hover {
        background: #f8fbff;
    }
</style>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
                <p class="text-muted mb-1">Công thức món ăn</p>
                <h3 class="mb-0 fw-bold">Chọn món cần thiết lập công thức</h3>
            </div>
            <a href="<?php echo BASE_URL; ?>/recipe/create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tạo công thức mới
            </a>
        </div>

        <div class="menu-recipe-card mb-3">
            <div class="p-3 border-bottom">
                <form method="get" action="<?php echo BASE_URL; ?>/recipe" id="menu-search-form">
                    <div class="row g-2">
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="search" name="q" id="menu-search" class="form-control" placeholder="Tìm theo tên món..." value="<?php echo isset($q) ? htmlspecialchars($q) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-primary flex-fill">Tìm</button>
                            <a href="<?php echo BASE_URL; ?>/recipe" class="btn btn-outline-secondary">Đặt lại</a>
                        </div>
                    </div>
                </form>
            </div>

            <div id="menu-list">
                <?php if (!empty($menuItems)): ?>
                    <?php foreach ($menuItems as $m): ?>
                        <a href="<?php echo BASE_URL; ?>/recipe?menu_id=<?php echo (int)$m['id']; ?>" class="menu-recipe-row menu-item-row">
                            <span>
                                <span class="fw-semibold d-block"><?php echo htmlspecialchars($m['name']); ?></span>
                                <span class="text-muted small">Giá bán: <?php echo number_format((float)($m['price'] ?? 0), 0, ',', '.'); ?> đ</span>
                            </span>
                            <span class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-journal-text"></i> Xem công thức
                            </span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-5">Không tìm thấy món nào.</div>
                <?php endif; ?>
            </div>
        </div>

        <?php
        if (isset($pagination)) {
            $paginationVar = $pagination;
            $baseUrlVar = $baseUrl ?? (BASE_URL . '/recipe');
            $pagination = $paginationVar;
            $baseUrl = $baseUrlVar;
            include __DIR__ . '/../layouts/pagination.php';
        }
        ?>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
    (function() {
        const input = document.getElementById('menu-search');
        const list = document.getElementById('menu-list');
        const rows = list ? Array.from(list.querySelectorAll('.menu-item-row')) : [];

        if (!input) {
            return;
        }

        input.addEventListener('input', function(e) {
            const keyword = (e.target.value || '').toLowerCase().trim();
            rows.forEach(function(row) {
                row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none';
            });
        });
    })();
</script>
