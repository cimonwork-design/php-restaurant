<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .receipt-header-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .receipt-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .receipt-info-label {
        font-size: 0.85rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .receipt-info-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 0.5rem;
    }

    .receipt-form-section {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #667eea;
        color: #667eea;
    }

    .receipt-row {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 6px;
        margin-bottom: 0.75rem;
        transition: background 0.2s;
    }

    .receipt-row:hover {
        background: #e9ecef;
    }

    .receipt-row input,
    .receipt-row select {
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .receipt-row.row-duplicate {
        background: #fff1f2 !important;
        border: 1px solid #fda4af;
    }

    .receipt-total-section {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        border-left: 4px solid #16a34a;
        padding: 1rem;
        border-radius: 6px;
        margin-top: 1rem;
    }

    .total-label {
        font-size: 0.95rem;
        color: #666;
    }

    .total-amount {
        font-size: 2rem;
        font-weight: 700;
        color: #16a34a;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 2rem;
        transition: all 0.2s;
    }

    .btn-submit:hover:not(:disabled) {
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .validation-summary {
        border-left: 4px solid #dc3545;
    }
</style>

<div class="main-content">
    <div class="container-fluid mt-4 mb-5">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0" style="font-weight: 700;">
                    <i class="bi bi-box-arrow-in-right"></i> Tạo phiếu nhập kho
                </h1>
                <p class="text-muted small mt-1">Ghi nhận nguyên liệu từ nhà cung cấp vào kho hệ thống</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/inventory_receipt" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <!-- Thông báo lỗi & Flash messages -->
        <div id="clientErrorBox" class="alert alert-danger validation-summary d-none alert-dismissible fade show" role="alert">
            <h6 class="alert-heading fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill"></i> Vui lòng kiểm tra lại các thông tin sau:</h6>
            <ul id="clientErrorList" class="mb-0 ps-3"></ul>
            <button type="button" class="btn-close" onclick="document.getElementById('clientErrorBox').classList.add('d-none')"></button>
        </div>

        <?php
        $flash = getFlash();
        if ($flash):
        ?>
            <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show <?php echo $flash['type'] === 'error' ? 'validation-summary' : ''; ?>" role="alert">
                <i class="bi bi-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <div><?php echo $flash['message']; ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php
        $oldSupplier = $oldInput['supplier'] ?? '';
        $oldReceiptDate = $oldInput['receipt_date'] ?? date('Y-m-d');
        $oldNote = $oldInput['note'] ?? '';
        ?>

        <form method="post" action="<?php echo BASE_URL; ?>/inventory_receipt/store" id="receiptForm" novalidate>
            <!-- Thông tin nhập kho -->
            <div class="receipt-form-section">
                <div class="section-title"><i class="bi bi-info-circle"></i> Thông tin phiếu nhập</div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="supplier" class="form-label"><strong>Nhà cung cấp</strong></label>
                            <input type="text" id="supplier" name="supplier" class="form-control" 
                                   placeholder="VD: Công ty ABC, Nhà cung cấp XYZ (tối đa 100 ký tự)" 
                                   maxlength="100"
                                   value="<?php echo htmlspecialchars($oldSupplier); ?>">
                            <div class="form-text text-muted small">Tên hoặc đơn vị cung ứng nguyên liệu (tùy chọn, 2 - 100 ký tự).</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="receipt_date" class="form-label"><strong>Ngày nhập <span class="text-danger">*</span></strong></label>
                            <input type="date" id="receipt_date" name="receipt_date" class="form-control" 
                                   value="<?php echo htmlspecialchars($oldReceiptDate); ?>" 
                                   max="<?php echo date('Y-m-d'); ?>"
                                   required>
                            <div class="form-text text-muted small">Ngày nhập kho không được lớn hơn ngày hiện tại.</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="note" class="form-label">Ghi chú (tùy chọn)</label>
                    <textarea id="note" name="note" class="form-control" rows="2" 
                              maxlength="500"
                              placeholder="Ghi chú thêm về phiếu nhập (tối đa 500 ký tự)..."><?php echo htmlspecialchars($oldNote); ?></textarea>
                    <div class="form-text text-muted small"><span id="noteCharCount"><?php echo mb_strlen($oldNote); ?></span>/500 ký tự</div>
                </div>
            </div>

            <!-- Chi tiết nguyên liệu -->
            <div class="receipt-form-section">
                <div class="section-title d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-check"></i> Chi tiết nguyên liệu nhập kho <span class="text-danger">*</span></span>
                    <span class="badge bg-primary" id="badge-item-count">0 nguyên liệu</span>
                </div>

                <!-- Header hàng -->
                <div class="row fw-bold small text-muted mb-2 px-2 d-none d-md-flex">
                    <div class="col-md-4">Nguyên liệu <span class="text-danger">*</span></div>
                    <div class="col-md-2 text-end">Số lượng <span class="text-danger">*</span></div>
                    <div class="col-md-3 text-end">Đơn giá (đ) <span class="text-danger">*</span></div>
                    <div class="col-md-2 text-end">Thành tiền (đ)</div>
                    <div class="col-md-1 text-center">Xóa</div>
                </div>

                <div id="receipt-items"></div>

                <button type="button" id="add-item" class="btn btn-outline-primary btn-sm mt-3">
                    <i class="bi bi-plus-circle"></i> Thêm dòng nguyên liệu
                </button>
            </div>

            <!-- Tổng tiền -->
            <div class="receipt-total-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="total-label">Tổng cộng giá trị phiếu nhập</div>
                        <div class="total-amount" id="receipt-total">0 đ</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-success" id="item-count">0 mục</div>
                        <small class="text-muted">Đã bao gồm đơn giá x số lượng</small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary btn-submit" id="btnSubmitReceipt">
                    <span id="btnSubmitText"><i class="bi bi-check-circle"></i> Tạo phiếu nhập</span>
                    <span id="btnSubmitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
                <a href="<?php echo BASE_URL; ?>/inventory_receipt" class="btn btn-outline-secondary" id="btnCancel">
                    <i class="bi bi-x-circle"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Template cho dòng nhập kho -->
<template id="receipt-row-template">
    <div class="receipt-row row align-items-center">
        <div class="col-md-4 mb-2 mb-md-0">
            <label class="form-label small d-md-none fw-bold">Nguyên liệu:</label>
            <select name="ingredient_id[]" class="form-select form-select-sm ingredient-select" required>
                <option value="">-- Chọn nguyên liệu --</option>
                <?php foreach ($ingredients as $ing): ?>
                    <option value="<?php echo $ing['id']; ?>" data-code="<?php echo htmlspecialchars($ing['code']); ?>" data-unit="<?php echo htmlspecialchars($ing['unit'] ?? ''); ?>" data-price="<?php echo $ing['purchase_price'] ?? 0; ?>">
                        <?php echo htmlspecialchars($ing['code'] . ' - ' . $ing['name'] . (!empty($ing['unit']) ? ' (' . $ing['unit'] . ')' : '')); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <label class="form-label small d-md-none fw-bold">Số lượng:</label>
            <input type="number" name="qty[]" class="form-control form-control-sm text-end qty-input" min="0.001" max="99999" value="1" step="any" placeholder="Số lượng" required>
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <label class="form-label small d-md-none fw-bold">Đơn giá:</label>
            <input type="number" name="unit_price[]" class="form-control form-control-sm text-end price-input" min="0" max="1000000000" value="0" step="any" placeholder="Đơn giá" required>
        </div>
        <div class="col-md-2 mb-2 mb-md-0 text-md-end">
            <label class="form-label small d-md-none fw-bold">Thành tiền:</label>
            <div class="subtotal fw-semibold text-primary">0 đ</div>
        </div>
        <div class="col-md-1 text-center">
            <button type="button" class="btn btn-outline-danger btn-sm remove-item" title="Xóa dòng này">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</template>

<script>
    (function() {
        const ingredients = <?php echo json_encode($ingredients); ?>;
        const container = document.getElementById('receipt-items');
        const template = document.getElementById('receipt-row-template').content;
        const addBtn = document.getElementById('add-item');
        const form = document.getElementById('receiptForm');
        const noteInput = document.getElementById('note');
        const noteCharCount = document.getElementById('noteCharCount');
        const clientErrorBox = document.getElementById('clientErrorBox');
        const clientErrorList = document.getElementById('clientErrorList');
        const btnSubmit = document.getElementById('btnSubmitReceipt');
        const btnSubmitText = document.getElementById('btnSubmitText');
        const btnSubmitSpinner = document.getElementById('btnSubmitSpinner');

        const fromRestock = <?php echo isset($fromRestock) && $fromRestock ? 'true' : 'false'; ?>;
        const quickIngredient = <?php echo isset($quickIngredient) && $quickIngredient ? json_encode($quickIngredient) : 'null'; ?>;
        const quickQty = <?php echo isset($quickQty) && $quickQty ? (float)$quickQty : 'null'; ?>;
        const oldInput = <?php echo isset($oldInput) && $oldInput ? json_encode($oldInput) : 'null'; ?>;

        // Note counter
        if (noteInput && noteCharCount) {
            noteInput.addEventListener('input', () => {
                noteCharCount.textContent = noteInput.value.length;
            });
        }

        function formatVND(num) {
            return new Intl.NumberFormat('vi-VN', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }).format(num) + ' đ';
        }

        function buildOptions(select, selectedId) {
            select.innerHTML = '<option value="">-- Chọn nguyên liệu --</option>';
            ingredients.forEach(i => {
                const opt = document.createElement('option');
                opt.value = i.id;
                const unitStr = i.unit ? ` (${i.unit})` : '';
                opt.textContent = `${i.code} - ${i.name}${unitStr}`;
                opt.setAttribute('data-price', i.purchase_price || 0);
                if (selectedId && String(selectedId) === String(i.id)) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        }

        function updateRowSubtotal(row) {
            const q = parseFloat(row.querySelector('.qty-input').value) || 0;
            const p = parseFloat(row.querySelector('.price-input').value) || 0;
            const sub = Math.max(0, q * p);
            const subEl = row.querySelector('.subtotal');
            if (subEl) subEl.textContent = formatVND(sub);
            updateTotal();
            checkDuplicates();
        }

        function updateTotal() {
            let total = 0;
            const rows = container.querySelectorAll('.receipt-row');
            rows.forEach(r => {
                const q = parseFloat(r.querySelector('.qty-input').value) || 0;
                const p = parseFloat(r.querySelector('.price-input').value) || 0;
                if (q > 0 && p >= 0) {
                    total += (q * p);
                }
            });
            const totalEl = document.getElementById('receipt-total');
            if (totalEl) totalEl.textContent = formatVND(total);
            updateCounts();
        }

        function updateCounts() {
            const rowCount = container.querySelectorAll('.receipt-row').length;
            const countEl = document.getElementById('item-count');
            const badgeEl = document.getElementById('badge-item-count');
            if (countEl) countEl.textContent = `${rowCount} mục`;
            if (badgeEl) badgeEl.textContent = `${rowCount} nguyên liệu`;
        }

        function checkDuplicates() {
            const rows = container.querySelectorAll('.receipt-row');
            const seen = {};
            let hasDup = false;

            rows.forEach(r => {
                r.classList.remove('row-duplicate');
                const sel = r.querySelector('.ingredient-select');
                const val = sel.value;
                if (val) {
                    if (seen[val]) {
                        r.classList.add('row-duplicate');
                        seen[val].classList.add('row-duplicate');
                        hasDup = true;
                    } else {
                        seen[val] = r;
                    }
                }
            });
            return hasDup;
        }

        function addRow(selectedId = '', qty = 1, price = 0) {
            const node = document.importNode(template, true);
            const row = node.querySelector('.receipt-row');
            const select = row.querySelector('.ingredient-select');
            const qtyInput = row.querySelector('.qty-input');
            const priceInput = row.querySelector('.price-input');
            const removeBtn = row.querySelector('.remove-item');

            buildOptions(select, selectedId);
            qtyInput.value = qty;
            priceInput.value = price;

            removeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                row.remove();
                updateTotal();
            });

            select.addEventListener('change', function() {
                const selectedOpt = this.options[this.selectedIndex];
                const ingId = this.value;
                const ing = ingredients.find(i => String(i.id) === String(ingId));
                if (ing && ing.purchase_price !== null && ing.purchase_price !== undefined) {
                    priceInput.value = ing.purchase_price;
                } else {
                    priceInput.value = 0;
                }
                updateRowSubtotal(row);
            });

            qtyInput.addEventListener('input', () => updateRowSubtotal(row));
            priceInput.addEventListener('input', () => updateRowSubtotal(row));

            container.appendChild(row);

            if (selectedId) {
                updateRowSubtotal(row);
            }
            updateCounts();
        }

        addBtn.addEventListener('click', (e) => {
            e.preventDefault();
            addRow('', 1, 0);
        });

        // Khởi tạo các dòng dựa trên Old Input / Restock / Quick / Default
        if (oldInput && oldInput.ingredient_id && Array.isArray(oldInput.ingredient_id) && oldInput.ingredient_id.length > 0) {
            for (let i = 0; i < oldInput.ingredient_id.length; i++) {
                const ingId = oldInput.ingredient_id[i] || '';
                const q = oldInput.qty && oldInput.qty[i] !== undefined ? oldInput.qty[i] : 1;
                const p = oldInput.unit_price && oldInput.unit_price[i] !== undefined ? oldInput.unit_price[i] : 0;
                addRow(ingId, q, p);
            }
        } else if (fromRestock) {
            const cart = JSON.parse(sessionStorage.getItem('restockCart') || '[]');
            if (cart.length > 0) {
                cart.forEach(item => {
                    const ing = ingredients.find(i => String(i.id) === String(item.ingredient_id));
                    addRow(item.ingredient_id, item.qty || 1, ing ? (ing.purchase_price || 0) : 0);
                });
                sessionStorage.removeItem('restockCart');
            } else {
                addRow('', 1, 0);
            }
        } else if (quickIngredient) {
            addRow(quickIngredient.id, quickQty || 1, quickIngredient.purchase_price || 0);
        } else {
            addRow('', 1, 0);
        }

        updateTotal();

        // Client-side Validation & Form Submission
        form.addEventListener('submit', function(e) {
            const errors = [];
            clientErrorBox.classList.add('d-none');
            clientErrorList.innerHTML = '';

            // 1. Validate Supplier
            const supplierVal = document.getElementById('supplier').value.trim();
            const supplierRaw = document.getElementById('supplier').value;
            if (supplierRaw.length > 0 && supplierVal.length === 0) {
                errors.push('Tên nhà cung cấp không được chỉ chứa khoảng trắng.');
            } else if (supplierVal.length > 0) {
                if (supplierVal.length < 2) {
                    errors.push('Tên nhà cung cấp phải có tối thiểu 2 ký tự.');
                } else if (supplierVal.length > 100) {
                    errors.push('Tên nhà cung cấp không được vượt quá 100 ký tự.');
                }
            }

            // 2. Validate Receipt Date
            const dateInput = document.getElementById('receipt_date');
            const dateVal = dateInput.value.trim();
            if (!dateVal) {
                errors.push('Ngày nhập kho là bắt buộc, không được để trống.');
            } else {
                const today = '<?php echo date('Y-m-d'); ?>';
                if (dateVal > today) {
                    errors.push(`Ngày nhập kho không được lớn hơn ngày hiện tại (${today}).`);
                } else if (dateVal < '2020-01-01') {
                    errors.push('Ngày nhập kho không được nhỏ hơn ngày 01/01/2020.');
                }
            }

            // 3. Validate Note
            if (noteInput && noteInput.value.length > 500) {
                errors.push('Ghi chú không được vượt quá 500 ký tự.');
            }

            // 4. Validate Row Items
            const rows = container.querySelectorAll('.receipt-row');
            if (rows.length === 0) {
                errors.push('Phiếu nhập kho phải có ít nhất một dòng nguyên liệu.');
            }

            const seenIngredients = {};
            rows.forEach((r, idx) => {
                const rowNum = idx + 1;
                const sel = r.querySelector('.ingredient-select');
                const qtyIn = r.querySelector('.qty-input');
                const priceIn = r.querySelector('.price-input');

                const ingId = sel.value;
                const qVal = qtyIn.value.trim();
                const pVal = priceIn.value.trim();

                if (!ingId) {
                    errors.push(`Dòng ${rowNum}: Vui lòng chọn nguyên liệu.`);
                } else {
                    const ingName = sel.options[sel.selectedIndex].textContent.trim();
                    if (seenIngredients[ingId]) {
                        errors.push(`Dòng ${rowNum}: Nguyên liệu "${ingName}" bị trùng lặp với dòng ${seenIngredients[ingId]}.`);
                    } else {
                        seenIngredients[ingId] = rowNum;
                    }
                }

                if (qVal === '' || isNaN(qVal)) {
                    errors.push(`Dòng ${rowNum}: Số lượng không hợp lệ.`);
                } else {
                    const qNum = parseFloat(qVal);
                    if (qNum <= 0) {
                        errors.push(`Dòng ${rowNum}: Số lượng nhập phải lớn hơn 0.`);
                    } else if (qNum > 99999) {
                        errors.push(`Dòng ${rowNum}: Số lượng không được vượt quá 99.999.`);
                    } else {
                        const dot = qVal.indexOf('.');
                        if (dot !== -1 && qVal.substring(dot + 1).length > 3) {
                            errors.push(`Dòng ${rowNum}: Số lượng chỉ cho phép tối đa 3 chữ số thập phân.`);
                        }
                    }
                }

                if (pVal === '' || isNaN(pVal)) {
                    errors.push(`Dòng ${rowNum}: Đơn giá không hợp lệ.`);
                } else {
                    const pNum = parseFloat(pVal);
                    if (pNum < 0) {
                        errors.push(`Dòng ${rowNum}: Đơn giá không được âm.`);
                    } else if (pNum > 1000000000) {
                        errors.push(`Dòng ${rowNum}: Đơn giá không được vượt quá 1.000.000.000 đ.`);
                    }
                }
            });

            // Nếu có lỗi client-side
            if (errors.length > 0) {
                e.preventDefault();
                errors.forEach(err => {
                    const li = document.createElement('li');
                    li.textContent = err;
                    clientErrorList.appendChild(li);
                });
                clientErrorBox.classList.remove('d-none');
                clientErrorBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
                return false;
            }

            // Chống double submit
            setTimeout(() => {
                btnSubmit.disabled = true;
                btnSubmitText.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang lưu phiếu...';
                btnSubmitSpinner.classList.remove('d-none');
            }, 10);
            sessionStorage.removeItem('restockCart');
        });
    })();
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>