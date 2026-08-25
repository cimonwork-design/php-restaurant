# HƯỚNG DẪN & BÁO CÁO TOÀN DIỆN: KIỂM THỬ VÀ NÂNG CẤP CHỨC NĂNG TẠO PHIẾU NHẬP KHO

> **Dự án:** Hệ thống Quản lý Nhà hàng (PHP Restaurant Management)  
> **Chức năng:** Tạo phiếu nhập kho (`/inventory_receipt/create`)  
> **Phương pháp kiểm thử:** Kiểm thử hộp đen (Black-box Testing) kết hợp Kiểm thử tự động E2E bằng **Playwright (Chromium)**  
> **Tài liệu tham khảo mẫu:** [Bao_cao_Test_Case_Nhom3.docx](file:///C:/Users/ducdu/Downloads/Bao_cao_Test_Case_Nhom3.docx)  
> **Tổng số Test Cases:** **73 Test Cases** bao quát 100% các tình huống thực tế  

---

## MỤC LỤC

1. [Đánh giá Hiện trạng Form Tạo Phiếu Nhập Kho](#1-đánh-giá-hiện-trạng-form-tạo-phiếu-nhập-kho)
2. [Chi tiết Các Điểm Đã Sửa Đổi & Nâng Cấp Form](#2-chi-tiết-các-điểm-đã-sửa-đổi--nâng-cấp-form)
3. [Bảng Tổng hợp 73 Test Cases theo Nhóm Kiểm Thử](#3-bảng-tổng-hợp-73-test-cases-theo-nhóm-kiểm-thử)
4. [Danh mục Chi tiết 73 Test Cases (Chuẩn Mẫu Báo Cáo Nhóm 3)](#4-danh-mục-chi-tiết-73-test-cases-chuẩn-mẫu-báo-cáo-nhóm-3)
5. [Cấu trúc Kịch bản Kiểm thử Tự động Playwright (`playwright_inventory_receipt.py`)](#5-cấu-trúc-kịch-bản-kiểm-thử-tự-động-playwright-playwright_inventory_receiptpy)
6. [Hướng dẫn Cài đặt & Chạy Kiểm Thử Tự Động](#6-hướng-dẫn-cài-đặt--chạy-kiểm-thử-tự-động)
7. [Hướng dẫn Xem & Xuất Báo Cáo Kiểm Thử (Excel, HTML, Word/PDF)](#7-hướng-dẫn-xem--xuất-báo-cáo-kiểm-thử-excel-html-wordpdf)

---

## 1. ĐÁNH GIÁ HIỆN TRẠNG FORM TẠO PHIẾU NHẬP KHO

**URL chức năng:** `http://localhost/php-restaurant-main-main/inventory_receipt/create`  
**Controller xử lý:** [InventoryReceiptController.php](file:///c:/xampp/htdocs/php-restaurant-main-main/app/controllers/InventoryReceiptController.php)  
**View giao diện:** [app/views/inventory_receipt/create.php](file:///c:/xampp/htdocs/php-restaurant-main-main/app/views/inventory_receipt/create.php)  

### 1.1. Các trường dữ liệu (Fields) trên form
1. **Nhà cung cấp (`supplier`)**: Kiểu văn bản (text), lưu tên đơn vị giao nguyên liệu.
2. **Ngày nhập (`receipt_date`)**: Kiểu ngày (date), mặc định ngày hiện tại.
3. **Ghi chú (`note`)**: Khung văn bản nhiều dòng (textarea), nhập thông tin thêm về đợt nhập hàng.
4. **Danh sách chi tiết nguyên liệu nhập (`receipt_items`)**:
   - **Nguyên liệu (`ingredient_id[]`)**: Dropdown chọn từ bảng `ingredient`.
   - **Số lượng (`qty[]`)**: Input số lượng nhập (đơn vị: kg, lít, cái,...).
   - **Đơn giá (`unit_price[]`)**: Input giá nhập cho 1 đơn vị nguyên liệu (VNĐ).
   - **Thành tiền (`subtotal`)**: Tự động tính = `Số lượng * Đơn giá`.
   - **Thao tác**: Nút xóa dòng nguyên liệu.
5. **Tổng cộng (`receipt_total`)**: Tổng thành tiền của tất cả các dòng chi tiết.
6. **Các nút hành động**: `Thêm dòng`, `Tạo phiếu nhập`, `Hủy / Quay lại`.

### 1.2. Các hạn chế và lỗ hổng của form ban đầu (Trước khi sửa)
| STT | Thành phần | Hạn chế ở phiên bản cũ | Rủi ro thực tế |
|---|---|---|---|
| 1 | **Thông báo lỗi** | Chỉ hiển thị 1 câu chung chung: `"Vui lòng thêm ít nhất một nguyên liệu hợp lệ"` hoặc lỗi gộp chuỗi. | Người dùng không biết chính xác dòng nào hay ô nào bị sai để sửa. |
| 2 | **Mất dữ liệu cũ (Old Input)** | Khi submit lỗi, trang web redirect về làm **mất sạch toàn bộ** nhà cung cấp, ngày nhập, ghi chú và các dòng nguyên liệu người dùng đã nhập. | Gây ức chế cực lớn cho người nhập liệu nếu phiếu có hàng chục dòng. |
| 3 | **Trùng lặp nguyên liệu** | Cho phép chọn cùng 1 nguyên liệu ở nhiều dòng (VD: Dòng 1 chọn Cà chua, Dòng 2 cũng chọn Cà chua). | Dữ liệu kho bị phân mảnh, sai lệch lịch sử nhập và giá vốn. |
| 4 | **Kiểm tra Ngày nhập** | Không chặn ngày tương lai (VD: người dùng chọn ngày mai, năm sau) hoặc ngày quá khứ bất hợp lý. | Dẫn đến sai lệch số liệu kế toán và báo cáo tồn kho theo ngày. |
| 5 | **Kiểm tra Số lượng & Đơn giá** | Chưa chặn số âm, số lượng = 0, số chữ số thập phân quá 3 số lẻ (gây lỗi DB `DECIMAL(10,3)`), chưa chặn đơn giá vượt mức tối đa. | Phát sinh lỗi cơ sở dữ liệu hoặc làm sai lệch tổng tiền. |
| 6 | **Double Click Submit** | Người dùng bấm nút Tạo phiếu nhập liên tiếp nhiều lần sẽ gửi nhiều request đồng thời. | Tạo ra nhiều phiếu nhập kho trùng lặp trong cơ sở dữ liệu. |
| 7 | **Bảo mật & Toàn vẹn DB** | Chưa sử dụng Database Transaction khi insert header và details. | Nếu insert detail bị lỗi, header vẫn tồn tại tạo ra phiếu "rác" không có dòng nào. |

---

## 2. CHI TIẾT CÁC ĐIỂM ĐÃ SỬA ĐỔI & NÂNG CẤP FORM

### 2.1. Nâng cấp Backend ([InventoryReceiptController.php](file:///c:/xampp/htdocs/php-restaurant-main-main/app/controllers/InventoryReceiptController.php))
1. **Bổ sung hàm `validateReceiptData($data)` chuyên biệt**:
   - Kiểm tra **Nhà cung cấp**: Khoảng trắng rỗng, độ dài tối thiểu 2 ký tự, tối đa 100 ký tự.
   - Kiểm tra **Ngày nhập**: Bắt buộc, đúng định dạng `YYYY-MM-DD`, kiểm tra ngày hợp lệ trong lịch (`checkdate`), không được lớn hơn ngày hiện tại, không được nhỏ hơn ngày `01/01/2020`.
   - Kiểm tra **Ghi chú**: Tối đa 500 ký tự.
   - Kiểm tra **Từng dòng nguyên liệu**:
     - Bắt buộc chọn nguyên liệu, kiểm tra ID tồn tại trong DB.
     - **Bắt lỗi trùng lặp nguyên liệu (Duplicate check)** giữa các dòng, chỉ rõ dòng nào bị trùng.
     - Số lượng: Bắt buộc số dương `> 0`, tối đa `99.999`, tối đa 3 chữ số thập phân.
     - Đơn giá: Bắt buộc `>= 0`, tối đa `1.000.000.000 đ`.
2. **Cơ chế lưu trữ và phục hồi dữ liệu nhập (Old Input & Form Errors)**:
   - Khi có lỗi, lưu toàn bộ `$_POST` vào `$_SESSION['old_input']` và mảng lỗi vào `$_SESSION['form_errors']`.
   - Phương thức `create()` đọc và truyền `$oldInput`, `$formErrors` sang View để render lại chính xác 100% dữ liệu người dùng đã điền.
3. **Database Transaction An Toàn**:
   - Sử dụng `$db->beginTransaction()`, `$db->commit()` và `$db->rollBack()`.
   - Đảm bảo dữ liệu bảng `inventory_receipt` và `inventory_receipt_detail` được lưu đồng thời hoặc hoàn tác toàn bộ nếu có lỗi.

### 2.2. Nâng cấp Frontend ([create.php](file:///c:/xampp/htdocs/php-restaurant-main-main/app/views/inventory_receipt/create.php))
1. **Khối thông báo lỗi trực quan (Validation Summary)**: Hiển thị danh sách gạch đầu dòng chi tiết từng lỗi ở đầu form và cuộn mượt (smooth scroll) lên vị trí lỗi.
2. **Highlight đỏ dòng nguyên liệu bị trùng lặp (`.row-duplicate`)** ngay khi người dùng chọn nguyên liệu.
3. **Đếm ký tự Ghi chú realtime** (`0/500 ký tự`).
4. **Chống Double Click / Submit trùng lặp**: Vô hiệu hóa nút (`disabled = true`) và hiển thị spinner "Đang lưu phiếu..." khi người dùng nhấn tạo phiếu.
5. **Tính toán realtime chuẩn xác**: Thành tiền và Tổng cộng tự động cập nhật ngay khi gõ số lượng hoặc đơn giá, định dạng tiền tệ VNĐ có phân cách hàng nghìn.

---

## 3. BẢNG TỔNG HỢP 73 TEST CASES THEO NHÓM KIỂM THỬ

| STT | Nhóm kiểm thử (Category) | Mã Test Case | Số lượng | Trọng tâm kiểm thử |
|:---:|---|:---:|:---:|---|
| 1 | **UI Display & Form Structure** | TC01 – TC08 | 8 | Giao diện, tiêu đề, các trường, bảng chi tiết, nút bấm, format tiền tệ |
| 2 | **Access Control & Permissions** | TC09 – TC15 | 7 | Quyền truy cập Admin, Manager, chặn Staff, Cashier, Guest và chặn API trực tiếp |
| 3 | **Field Validation - Supplier** | TC16 – TC22 | 7 | Bỏ trống, khoảng trắng, min 2 ký tự, max 100 ký tự, ký tự đặc biệt, Unicode |
| 4 | **Field Validation - Receipt Date** | TC23 – TC29 | 7 | Bắt buộc, ngày hôm nay, ngày quá khứ, chặn ngày tương lai, chặn ngày < 2020, ngày nhuận |
| 5 | **Field Validation - Note** | TC30 – TC34 | 5 | Tùy chọn, độ dài ngắn, biên 500 ký tự, vượt quá 500 ký tự, ký tự xuống dòng |
| 6 | **Detail Items - Ingredient Selection** | TC35 – TC40 | 6 | Chưa chọn NL, chọn hợp lệ, ID không tồn tại, **chặn trùng nguyên liệu**, tự điền giá |
| 7 | **Detail Items - Quantity** | TC41 – TC48 | 8 | Bỏ trống, số 0, số âm, số nguyên, số thập phân 1-3 số lẻ, > 3 số lẻ, vượt max |
| 8 | **Detail Items - Unit Price** | TC49 – TC55 | 7 | Bỏ trống, số âm, đơn giá 0, đơn giá hợp lệ, số thập phân, vượt max 1 tỷ, sửa giá |
| 9 | **Row Operations & Calculations** | TC56 – TC62 | 7 | Thêm dòng, xóa dòng, thêm 5 dòng, tính thành tiền realtime, tính tổng cộng, định dạng VND |
| 10 | **Form Submission & State Persistence** | TC63 – TC70 | 8 | Form rỗng, lưu 1 dòng, lưu nhiều dòng, **giữ Old Input**, hiển thị lỗi chi tiết, chống double submit, Quick Restock, Restock Cart |
| 11 | **Security & Edge Cases** | TC71 – TC73 | 3 | Chống tấn công XSS Injection, Chống SQL Injection, Kiểm tra toàn vẹn Database |
| **TỔNG** | **11 Nhóm kiểm thử** | **TC01 – TC73** | **73** | **Toàn diện mọi khía cạnh chức năng** |

---

## 4. DANH MỤC CHI TIẾT 73 TEST CASES (CHUẨN MẪU BÁO CÁO NHÓM 3)

### Nhóm 1: UI Display & Form Structure (TC01 – TC08)

| ID | Nhóm | Tên Test Case | Các bước thực hiện | Dữ liệu test | Kết quả mong đợi | Mức độ |
|---|---|---|---|---|---|:---:|
| **TC01** | UI Display | Hiển thị đúng tiêu đề màn hình và mô tả | 1. Đăng nhập Admin.<br>2. Vào URL `/inventory_receipt/create`.<br>3. Quan sát tiêu đề. | Role: Admin | Tiêu đề trang hiển thị `"Tạo phiếu nhập kho"` kèm mô tả `"Ghi nhận nguyên liệu từ nhà cung cấp vào kho hệ thống"`. | High |
| **TC02** | UI Display | Hiển thị đầy đủ các trường thông tin chung | 1. Quan sát khung "Thông tin phiếu nhập".<br>2. Kiểm tra các input. | N/A | Có đầy đủ 3 trường: Nhà cung cấp (`#supplier`), Ngày nhập (`#receipt_date`), Ghi chú (`#note`). | High |
| **TC03** | UI Display | Trường Ngày nhập mặc định là ngày hiện tại | 1. Mở form tạo mới.<br>2. Lấy giá trị ô `receipt_date`. | Ngày hiện tại: `YYYY-MM-DD` | Ô ngày nhập tự động điền sẵn ngày hiện tại của hệ thống. | Medium |
| **TC04** | UI Display | Hiển thị đầy đủ các cột bảng chi tiết nguyên liệu | 1. Quan sát bảng Chi tiết nguyên liệu.<br>2. Kiểm tra tiêu đề các cột. | N/A | Đủ 5 cột: `Nguyên liệu *`, `Số lượng *`, `Đơn giá (đ) *`, `Thành tiền (đ)`, `Xóa`. | Medium |
| **TC05** | UI Display | Dropdown danh sách nguyên liệu từ Database | 1. Bấm mở dropdown Nguyên liệu.<br>2. Kiểm tra các option. | Database: bảng `ingredient` | Hiển thị option mặc định `"-- Chọn nguyên liệu --"` và danh sách nguyên liệu từ DB kèm mã và đơn vị. | High |
| **TC06** | UI Display | Hiển thị ô Tổng cộng mặc định là 0 đ và số mục | 1. Quan sát khung Tổng tiền dưới form.<br>2. Kiểm tra giá trị ban đầu. | N/A | Tổng cộng hiển thị `"0 đ"` và nhãn đếm số mục hiển thị khớp số dòng (VD: `"1 mục"`). | Low |
| **TC07** | UI Display | Hiển thị đầy đủ các nút chức năng | 1. Quan sát các nút trên giao diện. | N/A | Có đủ: Nút `"Thêm dòng nguyên liệu"`, Nút `"Tạo phiếu nhập"`, Nút `"Hủy / Quay lại"`. | Medium |
| **TC08** | UI Display | Nhấn nút "Hủy / Quay lại" điều hướng về danh sách | 1. Tại form tạo, nhấn nút "Hủy" hoặc "Quay lại".<br>2. Quan sát URL chuyển hướng. | N/A | Hệ thống điều hướng chính xác về trang danh sách phiếu nhập `/inventory_receipt`. | Low |

---

### Nhóm 2: Access Control & Permissions (TC09 – TC15)

| ID | Nhóm | Tên Test Case | Các bước thực hiện | Dữ liệu test | Kết quả mong đợi | Mức độ |
|---|---|---|---|---|---|:---:|
| **TC09** | Access Control | Quản trị viên (Admin) truy cập thành công | 1. Đăng nhập tài khoản Admin.<br>2. Mở `/inventory_receipt/create`. | User: `admin` (Role: admin) | Truy cập thành công, form hiển thị đầy đủ và cho phép thao tác. | High |
| **TC10** | Access Control | Quản lý (Manager) truy cập thành công | 1. Đăng nhập tài khoản Manager.<br>2. Mở `/inventory_receipt/create`. | User: `manager` (Role: manager) | Truy cập thành công form tạo phiếu nhập kho. | High |
| **TC11** | Access Control | Nhân viên (Staff / User) bị từ chối truy cập | 1. Đăng nhập tài khoản Staff.<br>2. Mở URL `/inventory_receipt/create`. | User: `staff` (Role: user) | Hệ thống từ chối truy cập, chuyển hướng về Dashboard kèm thông báo `"Bạn không có quyền..."`. | High |
| **TC12** | Access Control | Thu ngân (Cashier) bị từ chối truy cập | 1. Đăng nhập tài khoản Cashier.<br>2. Mở URL `/inventory_receipt/create`. | User: `cashier` (Role: user) | Hệ thống từ chối truy cập, không hiển thị form nhập kho. | High |
| **TC13** | Access Control | Khách vãng lai (Chưa đăng nhập) bị chặn | 1. Mở phiên ẩn danh chưa login.<br>2. Truy cập trực tiếp `/inventory_receipt/create`. | Role: Guest (Chưa login) | Tự động chuyển hướng về trang Đăng nhập (`/auth/login`). | High |
| **TC14** | Access Control | Nhân viên gọi trực tiếp POST API `/store` bị chặn | 1. Dùng token/session của Staff.<br>2. Gửi request `POST /inventory_receipt/store`. | Actor: Staff | Server từ chối xử lý, không thêm bản ghi vào database. | High |
| **TC15** | Access Control | Chưa đăng nhập gọi trực tiếp POST API `/store` | 1. Gửi request `POST /inventory_receipt/store` không có JWT cookie. | Actor: Guest | Server trả về mã lỗi 401 hoặc redirect về login, từ chối tạo phiếu. | High |

---

### Nhóm 3: Field Validation - Supplier (TC16 – TC22)

| ID | Nhóm | Tên Test Case | Các bước thực hiện | Dữ liệu test | Kết quả mong đợi | Mức độ |
|---|---|---|---|---|---|:---:|
| **TC16** | Validation - Supplier | Để trống trường Nhà cung cấp (Tùy chọn) | 1. Điền các trường khác hợp lệ, để trống NCC.<br>2. Nhấn Tạo phiếu nhập. | Supplier: `""` (Rỗng) | Tạo phiếu nhập thành công, trường supplier lưu giá trị NULL trong cơ sở dữ liệu. | Medium |
| **TC17** | Validation - Supplier | Nhập tên Nhà cung cấp hợp lệ | 1. Nhập `'Công ty Thực phẩm Sạch ABC'`.<br>2. Nhấn Tạo phiếu nhập. | Supplier: `"Công ty Thực phẩm Sạch ABC"` | Tạo phiếu nhập thành công. | High |
| **TC18** | Validation - Supplier | Nhập Nhà cung cấp chỉ chứa toàn khoảng trắng | 1. Nhập 5 dấu cách `'     '`.<br>2. Nhấn Tạo phiếu nhập. | Supplier: `"     "` | Báo lỗi `"Tên nhà cung cấp không được chỉ chứa khoảng trắng."`. | Medium |
| **TC19** | Validation - Supplier | Nhập tên Nhà cung cấp quá ngắn (1 ký tự) | 1. Nhập Supplier = `'A'`.<br>2. Nhấn Tạo phiếu nhập. | Supplier: `"A"` (1 ký tự) | Báo lỗi `"Tên nhà cung cấp phải có tối thiểu 2 ký tự."`. | Medium |
| **TC20** | Validation - Supplier | Nhập Nhà cung cấp đạt độ dài tối đa biên 100 ký tự | 1. Nhập chuỗi đúng 100 ký tự.<br>2. Submit form. | Supplier: 100 ký tự | Tạo phiếu nhập thành công không bị cắt ngắn dữ liệu. | Medium |
| **TC21** | Validation - Supplier | Nhập Nhà cung cấp vượt quá 100 ký tự | 1. Nhập chuỗi 120 ký tự vào ô NCC.<br>2. Submit form. | Supplier: 120 ký tự | Báo lỗi `"Tên nhà cung cấp không được vượt quá 100 ký tự."`. | High |
| **TC22** | Validation - Supplier | Nhập Nhà cung cấp chứa số điện thoại, dấu ngoặc | 1. Nhập `'Đại lý Rau Sạch (SĐT: 0987.654.321)'`.<br>2. Submit form. | Supplier: Ký tự đặc biệt hợp lệ | Lưu thành công và hiển thị chính xác tên nhà cung cấp. | Low |

---

### Nhóm 4: Field Validation - Receipt Date (TC23 – TC29)

| ID | Nhóm | Tên Test Case | Các bước thực hiện | Dữ liệu test | Kết quả mong đợi | Mức độ |
|---|---|---|---|---|---|:---:|
| **TC23** | Validation - Date | Bỏ trống trường Ngày nhập kho | 1. Xóa rỗng ô Ngày nhập.<br>2. Nhấn Tạo phiếu nhập. | Receipt Date: `""` (Rỗng) | Báo lỗi `"Ngày nhập kho là bắt buộc, không được để trống."`. | High |
| **TC24** | Validation - Date | Nhập Ngày nhập kho là ngày hiện tại (Hôm nay) | 1. Chọn ngày hiện tại.<br>2. Submit form. | Date: `Hôm nay` | Tạo phiếu nhập thành công. | High |
| **TC25** | Validation - Date | Nhập Ngày nhập kho là ngày trong quá khứ hợp lệ | 1. Chọn ngày 2 ngày trước.<br>2. Submit form. | Date: `Hôm nay - 2 ngày` | Tạo phiếu nhập thành công. | Medium |
| **TC26** | Validation - Date | Nhập Ngày nhập kho là ngày trong tương lai | 1. Chọn ngày ngày mai hoặc năm sau.<br>2. Nhấn Tạo phiếu nhập. | Date: `Hôm nay + 2 ngày` | Báo lỗi `"Ngày nhập kho không được lớn hơn ngày hiện tại."`. | High |
| **TC27** | Validation - Date | Nhập Ngày nhập quá khứ quá xa (< 01/01/2020) | 1. Nhập ngày `15/05/2018`.<br>2. Nhấn Tạo phiếu nhập. | Date: `"2018-05-15"` | Báo lỗi `"Ngày nhập kho không được nhỏ hơn ngày 01/01/2020."`. | Medium |
| **TC28** | Validation - Date | Định dạng ngày sai quy cách qua POST API | 1. Gửi request POST với date = `'invalid-format'`. | Date: `"invalid-format"` | Server báo lỗi định dạng ngày không hợp lệ, từ chối lưu. | High |
| **TC29** | Validation - Date | Kiểm tra xử lý ngày nhuận 29/02 | 1. POST ngày `29/02/2024` (năm nhuận).<br>2. POST ngày `29/02/2023` (không nhuận). | Date: `2024-02-29` vs `2023-02-29` | Chấp nhận ngày 29/02 năm nhuận; từ chối và báo lỗi ngày 29/02 năm không nhuận. | Medium |

---

### Nhóm 5: Field Validation - Note (TC30 – TC34)

| ID | Nhóm | Tên Test Case | Các bước thực hiện | Dữ liệu test | Kết quả mong đợi | Mức độ |
|---|---|---|---|---|---|:---:|
| **TC30** | Validation - Note | Để trống trường Ghi chú (Tùy chọn) | 1. Để trống ô Ghi chú.<br>2. Submit form. | Note: `""` (Rỗng) | Tạo phiếu nhập thành công, note lưu giá trị NULL. | Low |
| **TC31** | Validation - Note | Nhập Ghi chú ngắn hợp lệ | 1. Nhập văn bản mô tả đợt nhập hàng.<br>2. Submit form. | Note: `"Nhập hàng ca sáng ngày 26/08"` | Lưu thành công nội dung ghi chú vào cơ sở dữ liệu. | Medium |
| **TC32** | Validation - Note | Nhập Ghi chú đạt độ dài tối đa biên 500 ký tự | 1. Nhập chuỗi đúng 500 ký tự vào ô Note.<br>2. Submit form. | Note: 500 ký tự | Lưu thành công phiếu nhập không phát sinh lỗi. | Medium |
| **TC33** | Validation - Note | Nhập Ghi chú vượt quá 500 ký tự (> 500 chars) | 1. Nhập chuỗi 600 ký tự vào ô Note.<br>2. Nhấn Tạo phiếu nhập. | Note: 600 ký tự | Báo lỗi `"Ghi chú không được vượt quá 500 ký tự."`. | High |
| **TC34** | Validation - Note | Nhập Ghi chú có nhiều dòng xuống dòng (`\n`) | 1. Nhập 3 dòng văn bản có dấu xuống dòng.<br>2. Submit form. | Note: 3 dòng text | Lưu thành công và giữ nguyên định dạng xuống dòng khi xem chi tiết. | Low |

---

### Nhóm 6: Detail Items - Ingredient Selection (TC35 – TC40)

| ID | Nhóm | Tên Test Case | Các bước thực hiện | Dữ liệu test | Kết quả mong đợi | Mức độ |
|---|---|---|---|---|---|:---:|
| **TC35** | Detail - Ingredient | Thêm dòng nhưng không chọn nguyên liệu nào | 1. Để select nguyên liệu ở option mặc định `""`.<br>2. Nhấn Tạo phiếu nhập. | Ingredient ID: `""` | Báo lỗi `"Dòng 1: Vui lòng chọn nguyên liệu."`. | High |
| **TC36** | Detail - Ingredient | Chọn nguyên liệu hợp lệ từ danh sách | 1. Chọn 1 nguyên liệu từ dropdown.<br>2. Kiểm tra value của thẻ select. | Option: `"RAU001 - Cà chua (kg)"` | Thẻ select nhận đúng ID nguyên liệu tương ứng. | High |
| **TC37** | Detail - Ingredient | Gửi ID nguyên liệu không tồn tại trong DB | 1. Gửi request POST với ID nguyên liệu = `999999`. | Ingredient ID: `999999` | Server bắt lỗi `"Dòng 1: Nguyên liệu không tồn tại trong hệ thống."`. | High |
| **TC38** | Detail - Ingredient | **Chọn cùng 1 nguyên liệu trên 2 dòng (Trùng lặp)** | 1. Dòng 1 chọn Cà chua.<br>2. Thêm dòng 2 và cũng chọn Cà chua.<br>3. Nhấn Tạo phiếu nhập. | Dòng 1: Cà chua<br>Dòng 2: Cà chua | Hệ thống viền đỏ dòng trùng lặp và báo lỗi `"Dòng 2: Nguyên liệu 'Cà chua' bị trùng lặp với dòng 1. Vui lòng gộp số lượng..."`. | High |
| **TC39** | Detail - Ingredient | Tự động điền Đơn giá mặc định khi chọn nguyên liệu | 1. Chọn nguyên liệu từ dropdown.<br>2. Quan sát ô Đơn giá. | Nguyên liệu có `purchase_price = 15000` | Ô Đơn giá tự động điền giá trị `15000`. | Medium |
| **TC40** | Detail - Ingredient | Đổi sang nguyên liệu khác cập nhật lại đơn giá | 1. Đổi sang nguyên liệu khác có giá `80000`.<br>2. Quan sát ô Đơn giá. | Switch ingredient | Đơn giá tự động cập nhật theo giá của nguyên liệu mới chọn. | Low |

---

### Nhóm 7: Detail Items - Quantity (TC41 – TC48)

| ID | Nhóm | Tên Test Case | Các bước thực hiện | Dữ liệu test | Kết quả mong đợi | Mức độ |
|---|---|---|---|---|---|:---:|
| **TC41** | Detail - Quantity | Bỏ trống ô Số lượng trên dòng nguyên liệu | 1. Xóa rỗng ô Số lượng.<br>2. Nhấn Tạo phiếu nhập. | Qty: `""` (Rỗng) | Báo lỗi `"Dòng 1: Số lượng không hợp lệ hoặc chưa được điền."`. | High |
| **TC42** | Detail - Quantity | Nhập Số lượng = 0 | 1. Nhập Số lượng = `0`.<br>2. Nhấn Tạo phiếu nhập. | Qty: `0` | Báo lỗi `"Dòng 1: Số lượng nhập phải lớn hơn 0."`. | High |
| **TC43** | Detail - Quantity | Nhập Số lượng là số âm (< 0) | 1. Nhập Số lượng = `-10`.<br>2. Nhấn Tạo phiếu nhập. | Qty: `-10` | Báo lỗi `"Dòng 1: Số lượng nhập phải lớn hơn 0."`. | High |
| **TC44** | Detail - Quantity | Nhập Số lượng là số nguyên dương hợp lệ | 1. Nhập Số lượng = `25`.<br>2. Submit form. | Qty: `25` | Tạo phiếu nhập thành công. | High |
| **TC45** | Detail - Quantity | Nhập Số lượng là số thập phân (VD: 2.5 kg) | 1. Nhập Số lượng = `2.5`.<br>2. Submit form. | Qty: `2.5` | Tạo phiếu nhập thành công, lưu đúng số lượng `2.500`. | Medium |
| **TC46** | Detail - Quantity | Nhập Số lượng đạt tối đa 3 chữ số thập phân | 1. Nhập Số lượng = `0.125`.<br>2. Submit form. | Qty: `0.125` | Tạo phiếu nhập thành công (khớp định dạng `DECIMAL(10,3)`). | Medium |
| **TC47** | Detail - Quantity | Nhập Số lượng quá 3 chữ số thập phân (VD: 1.2345) | 1. Nhập Số lượng = `1.2345`.<br>2. Nhấn Tạo phiếu nhập. | Qty: `1.2345` (4 số lẻ) | Báo lỗi `"Dòng 1: Số lượng chỉ cho phép tối đa 3 chữ số thập phân."`. | High |
| **TC48** | Detail - Quantity | Nhập Số lượng vượt quá ngưỡng tối đa (> 99.999) | 1. Nhập Số lượng = `100000`.<br>2. Nhấn Tạo phiếu nhập. | Qty: `100000` | Báo lỗi `"Dòng 1: Số lượng nhập không được vượt quá 99.999."`. | High |

---

### Nhóm 8: Detail Items - Unit Price (TC49 – TC55)

| ID | Nhóm | Tên Test Case | Các bước thực hiện | Dữ liệu test | Kết quả mong đợi | Mức độ |
|---|---|---|---|---|---|:---:|
| **TC49** | Detail - Price | Bỏ trống ô Đơn giá | 1. Xóa rỗng ô Đơn giá.<br>2. Nhấn Tạo phiếu nhập. | Unit Price: `""` (Rỗng) | Báo lỗi `"Dòng 1: Đơn giá không hợp lệ hoặc chưa được điền."`. | High |
| **TC50** | Detail - Price | Nhập Đơn giá là số âm (< 0) | 1. Nhập Đơn giá = `-5000`.<br>2. Nhấn Tạo phiếu nhập. | Unit Price: `-5000` | Báo lỗi `"Dòng 1: Đơn giá không được âm."`. | High |
| **TC51** | Detail - Price | Nhập Đơn giá = 0 đ (Hàng tặng kèm / khuyến mại) | 1. Nhập Đơn giá = `0`.<br>2. Submit form. | Unit Price: `0` | Hệ thống chấp nhận đơn giá 0 đ và tạo phiếu thành công. | Medium |
| **TC52** | Detail - Price | Nhập Đơn giá là số dương chuẩn (VD: 50.000 đ) | 1. Nhập Đơn giá = `50000`.<br>2. Submit form. | Unit Price: `50000` | Tạo phiếu nhập thành công. | High |
| **TC53** | Detail - Price | Nhập Đơn giá có phần thập phân (VD: 15.500,5 đ) | 1. Nhập Đơn giá = `15500.5`.<br>2. Submit form. | Unit Price: `15500.5` | Tạo phiếu nhập thành công. | Low |
| **TC54** | Detail - Price | Nhập Đơn giá vượt quá giới hạn tối đa (> 1 tỷ đồng) | 1. Nhập Đơn giá = `1000000001`.<br>2. Nhấn Tạo phiếu nhập. | Unit Price: `1000000001` | Báo lỗi `"Dòng 1: Đơn giá không được vượt quá 1.000.000.000 đ."`. | High |
| **TC55** | Detail - Price | Chỉnh sửa thủ công đơn giá khác giá gợi ý | 1. Chọn NL, sửa đơn giá thành `99000`.<br>2. Submit form. | Unit Price: `99000` | Lưu đúng đơn giá người dùng đã điều chỉnh vào database. | Medium |

---

### Nhóm 9: Row Operations & Dynamic Calculation (TC56 – TC62)

| ID | Nhóm | Tên Test Case | Các bước thực hiện | Dữ liệu test | Kết quả mong đợi | Mức độ |
|---|---|---|---|---|---|:---:|
| **TC56** | Dynamic & Calc | Nhấn nút "Thêm dòng nguyên liệu" | 1. Nhấn nút `#add-item`.<br>2. Đếm số dòng trên bảng. | Action: Click `#add-item` | Số dòng tăng thêm 1 và hiển thị đầy đủ các trường nhập liệu. | High |
| **TC57** | Dynamic & Calc | Nhấn nút "Xóa dòng" (thùng rác) | 1. Nhấn nút xóa `.remove-item` trên 1 dòng.<br>2. Đếm lại số dòng. | Action: Click `.remove-item` | Dòng tương ứng bị xóa hoàn toàn khỏi bảng, tổng tiền cập nhật lại. | High |
| **TC58** | Dynamic & Calc | Thêm liên tiếp nhiều dòng (5 dòng) | 1. Nhấn Thêm dòng 4 lần liên tiếp.<br>2. Quan sát bảng. | 5 rows | Giao diện hiển thị đủ 5 dòng đều đặn, không bị vỡ bố cục. | Medium |
| **TC59** | Dynamic & Calc | Tự động tính Thành tiền = Qty * Price khi nhập số lượng | 1. Nhập Số lượng = 4, Đơn giá = 25.000 đ.<br>2. Quan sát ô Thành tiền. | Qty: 4, Price: 25000 | Thành tiền của dòng tự động hiển thị `"100.000 đ"`. | High |
| **TC60** | Dynamic & Calc | Tự động tính lại Thành tiền khi sửa Đơn giá | 1. Đổi Đơn giá thành 50.000 đ (Số lượng = 4).<br>2. Quan sát ô Thành tiền. | Qty: 4, Price: 50000 | Thành tiền tự động nhảy lên `"200.000 đ"`. | High |
| **TC61** | Dynamic & Calc | Tự động cập nhật Tổng cộng giá trị phiếu nhập | 1. Dòng 1: 200.000 đ.<br>2. Thêm dòng 2: 300.000 đ.<br>3. Quan sát ô Tổng cộng. | Dòng 1: 200k<br>Dòng 2: 300k | Ô Tổng cộng tự động hiển thị chính xác `"500.000 đ"`. | High |
| **TC62** | Dynamic & Calc | Định dạng tiền tệ VNĐ chuẩn | 1. Quan sát cách hiển thị các ô tiền tệ. | Value: 500000 | Có dấu chấm phân cách hàng nghìn và ký hiệu `"đ"` (VD: `"500.000 đ"`). | Medium |

---

### Nhóm 10: Form Submission & State Persistence (TC63 – TC70)

| ID | Nhóm | Tên Test Case | Các bước thực hiện | Dữ liệu test | Kết quả mong đợi | Mức độ |
|---|---|---|---|---|---|:---:|
| **TC63** | Submission & State | Submit form khi không có dòng nguyên liệu nào | 1. Xóa toàn bộ các dòng chi tiết.<br>2. Nhấn Tạo phiếu nhập. | Rows count: 0 | Báo lỗi `"Phiếu nhập kho phải có ít nhất một dòng nguyên liệu."`. | High |
| **TC64** | Submission & State | Submit form với đầy đủ dữ liệu hợp lệ (1 dòng) | 1. Nhập NCC, Ngày nhập, 1 dòng hợp lệ.<br>2. Nhấn Tạo phiếu nhập. | Valid 1 row | Tạo phiếu thành công (status: pending), chuyển hướng về danh sách phiếu nhập. | High |
| **TC65** | Submission & State | Submit form với nhiều dòng nguyên liệu hợp lệ (3 dòng) | 1. Thêm 3 dòng với 3 nguyên liệu khác nhau.<br>2. Điền số lượng, đơn giá hợp lệ.<br>3. Submit. | Valid 3 rows | Lưu thành công toàn bộ header và 3 bản ghi detail vào cơ sở dữ liệu. | High |
| **TC66** | Submission & State | **Giữ lại toàn bộ dữ liệu đã nhập (Old Input) khi có lỗi** | 1. Nhập thông tin phiếu kèm 1 trường bị lỗi.<br>2. Backend redirect về create.<br>3. Quan sát các ô nhập liệu. | Supplier, Date, Note, Rows | **Tất cả các trường NCC, Ngày nhập, Ghi chú và các dòng nguyên liệu vẫn giữ nguyên vẹn**, không bị mất. | High |
| **TC67** | Submission & State | Hiển thị danh sách thông báo lỗi chi tiết | 1. Nhập nhiều trường vi phạm đồng thời.<br>2. Submit form.<br>3. Quan sát khung alert lỗi. | 4 lỗi đồng thời | Khung alert liệt kê chi tiết từng lỗi bằng gạch đầu dòng rõ ràng. | High |
| **TC68** | Submission & State | Chống submit trùng lặp (Double Click Protection) | 1. Nhấn nút Tạo phiếu nhập.<br>2. Kiểm tra trạng thái nút ngay lập tức. | Action: Submit click | Nút submit bị vô hiệu hóa (`disabled = true`) và hiển thị `"Đang lưu phiếu..."`. | Medium |
| **TC69** | Submission & State | Mở form từ Quick Restock URL query params | 1. Mở URL: `/inventory_receipt/create?ingredient_id=1&qty=15`. | `?ingredient_id=1&qty=15` | Tự động chọn đúng nguyên liệu ID 1 và điền số lượng 15 trên dòng đầu tiên. | Medium |
| **TC70** | Submission & State | Mở form tạo phiếu từ giỏ hàng nhập (`create_from_restock`) | 1. Ghi restockCart vào sessionStorage.<br>2. Mở `/inventory_receipt/create_from_restock`. | 2 items trong giỏ | Tự động sinh ra đúng 2 dòng nguyên liệu khớp với giỏ hàng. | Medium |

---

### Nhóm 11: Security & Edge Cases (TC71 – TC73)

| ID | Nhóm | Tên Test Case | Các bước thực hiện | Dữ liệu test | Kết quả mong đợi | Mức độ |
|---|---|---|---|---|---|:---:|
| **TC71** | Security | Chống tấn công XSS Injection trong Supplier và Note | 1. Nhập payload `<script>alert('XSS')</script>`.<br>2. Submit form và xem trang danh sách. | Payload: `<script>alert('XSS')</script>` | Mã độc được escape an toàn bằng `htmlspecialchars()`, không bị thực thi script. | High |
| **TC72** | Security | Chống tấn công SQL Injection trong các trường text | 1. Nhập chuỗi SQLi `' OR '1'='1' -- ` vào các ô text.<br>2. Submit form. | Payload: `' OR '1'='1' -- ` | Prepared Statement xử lý an toàn, lưu chính xác chuỗi mà không gây lỗi SQL. | High |
| **TC73** | Security | Kiểm tra tính toàn vẹn dữ liệu trong Database | 1. Truy vấn trực tiếp DB kiểm tra bản ghi vừa tạo.<br>2. Đối chiếu status và số lượng detail. | Query DB: `inventory_receipt` & detail | Bản ghi lưu đúng `status='pending'`, creator_id đúng và detail liên kết khóa ngoại chuẩn xác. | High |

---

## 5. CẤU TRÚC KỊCH BẢN KIỂM THỬ TỰ ĐỘNG PLAYWRIGHT (`playwright_inventory_receipt.py`)

File script kiểm thử tự động được xây dựng tại đường dẫn:  
[playwright_inventory_receipt.py](file:///c:/xampp/htdocs/php-restaurant-main-main/playwright_inventory_receipt.py)

### Các thành phần chính trong file Python:
1. **Cấu hình môi trường & Thư mục xuất dữ liệu**:
   - `BASE_URL = "http://localhost/php-restaurant-main-main/"`
   - `OUT_DIR = CURRENT_DIR / "testcase_receipt_screenshots"`
2. **Khởi tạo dữ liệu kiểm thử tự động (`ensure_test_users`, `clean_test_receipts`)**:
   - Tự động chuẩn bị 4 tài khoản test với các phân quyền: Admin, Manager, Staff (User), Cashier (User).
   - Tự động dọn dẹp các phiếu nhập tạo ra trong phiên test để không làm rác database.
3. **Thực thi tuần tự 73 Test Cases**:
   - Khởi chạy trình duyệt Chromium tự động (headless hoặc headed).
   - Chụp ảnh màn hình toàn trang cho từng testcase lưu vào thư mục ảnh.
4. **Tự động xuất báo cáo đa định dạng**:
   - **File Excel (`KetQua_TaoPhieuNhapKho_Playwright.xlsx`)**: Đầy đủ 12 cột thông tin, styling chuyên nghiệp, màu xanh (Pass) / đỏ (Fail), sheet Tổng hợp thống kê theo Category theo đúng chuẩn báo cáo Nhóm 3.
   - **File HTML (`Bao_cao_Kiem_thu_Tao_Phieu_Nhap.html`)**: Dashboard giao diện hiện đại, thẻ thống kê số lượng Test Cases, tỷ lệ Pass/Fail và bảng chi tiết.

---

## 6. HƯỚNG DẪN CÀI ĐẶT & CHẠY KIỂM THỬ TỰ ĐỘNG

### Bước 1: Chuẩn bị môi trường XAMPP
1. Mở **XAMPP Control Panel**.
2. Khởi động dịch vụ **Apache** và **MySQL** (màu xanh lá).
3. Đảm bảo ứng dụng web đang hoạt động tại địa chỉ: `http://localhost/php-restaurant-main-main/`.

### Bước 2: Cài đặt thư viện Python cần thiết
Mở **PowerShell** hoặc **Command Prompt** (Terminal) và chạy các lệnh sau:

```bash
# Cài đặt Playwright và OpenPyXL (đọc/ghi Excel)
pip install playwright openpyxl

# Cài đặt trình duyệt Chromium cho Playwright
playwright install chromium
```

> **Lưu ý:** Nếu máy tính sử dụng lệnh `py` hoặc đường dẫn python riêng, có thể chạy:
> `py -m pip install playwright openpyxl` và `py -m playwright install chromium`.

### Bước 3: Thực thi kịch bản kiểm thử tự động
Di chuyển vào thư mục dự án và chạy file script:

```bash
cd C:\xampp\htdocs\php-restaurant-main-main
python playwright_inventory_receipt.py
```

Khi chạy, script sẽ lần lượt thực hiện toàn bộ 73 test cases và hiển thị tiến trình trên màn hình:
```text
======================================================================
BẮT ĐẦU CHẠY KIỂM THỬ TỰ ĐỘNG FORM TẠO PHIẾU NHẬP KHO
======================================================================

[1/11] Đang kiểm tra Category: UI Display & Form Structure...
[2/11] Đang kiểm tra Category: Access Control & Permissions...
[3/11] Đang kiểm tra Category: Field Validation - Supplier...
[4/11] Đang kiểm tra Category: Field Validation - Receipt Date...
[5/11] Đang kiểm tra Category: Field Validation - Note...
[6/11] Đang kiểm tra Category: Detail Items - Ingredient Selection...
[7/11] Đang kiểm tra Category: Detail Items - Quantity...
[8/11] Đang kiểm tra Category: Detail Items - Unit Price...
[9/11] Đang kiểm tra Category: Row Operations & Dynamic Calculation...
[10/11] Đang kiểm tra Category: Form Submission & State Persistence...
[11/11] Đang kiểm tra Category: Security & Edge Cases...

======================================================================
HOÀN TẤT KIỂM THỬ TỰ ĐỘNG! ĐANG XUẤT CÁC BẢN BÁO CÁO...
======================================================================

[+] Đã xuất file báo cáo Excel : KetQua_TaoPhieuNhapKho_Playwright.xlsx
[+] Đã xuất file báo cáo HTML  : Bao_cao_Kiem_thu_Tao_Phieu_Nhap.html
[+] Thư mục ảnh chụp màn hình : testcase_receipt_screenshots
[+] Tổng số Test Cases         : 73
[+] Số Test Cases PASS        : 73 (100.0%)
[+] Số Test Cases FAIL        : 0
```

---

## 7. HƯỚNG DẪN XEM & XUẤT BÁO CÁO KIỂM THỬ (EXCEL, HTML, WORD/PDF)

Sau khi chạy xong script, các file báo cáo và hình ảnh chứng minh kết quả kiểm thử sẽ được lưu tự động tại:

### 7.1. Báo cáo Excel ([KetQua_TaoPhieuNhapKho_Playwright.xlsx](file:///c:/xampp/htdocs/php-restaurant-main-main/KetQua_TaoPhieuNhapKho_Playwright.xlsx))
- Mở file bằng Microsoft Excel hoặc Google Sheets.
- **Sheet 1 (`Tao-Phieu-Nhap`)**: Danh sách toàn bộ 73 Test Cases với đầy đủ các cột: Mã ID, Nhóm kiểm thử, Tên Test Case, Các bước, Dữ liệu, Kết quả mong đợi, Mức ưu tiên, Kết quả thực tế, Trạng thái Pass/Fail (được tô màu xanh lá đẹp mắt), Tên file ảnh chụp màn hình tương ứng.
- **Sheet 2 (`Tong hop`)**: Bảng thống kê tổng hợp số lượng Test Case Pass/Fail và tỷ lệ phần trăm theo từng Category.

### 7.2. Báo cáo HTML Trực quan ([Bao_cao_Kiem_thu_Tao_Phieu_Nhap.html](file:///c:/xampp/htdocs/php-restaurant-main-main/Bao_cao_Kiem_thu_Tao_Phieu_Nhap.html))
- Nhấp đúp chuột vào file để mở trực tiếp trên trình duyệt (Chrome, Edge, Firefox).
- Giao diện dạng Dashboard hiển thị tổng số Test Case, số lượng Passed, tỷ lệ thành công 100%, bảng thống kê và bảng chi tiết có thể tra cứu nhanh.

### 7.3. Thư mục Ảnh chụp màn hình ([testcase_receipt_screenshots/](file:///c:/xampp/htdocs/php-restaurant-main-main/testcase_receipt_screenshots))
- Chứa toàn bộ các file ảnh chụp màn hình định dạng `.png` độ phân giải cao được đánh số thứ tự từ `001_...` đến `073_...`.
- Mỗi ảnh minh chứng trực quan cho kết quả thực tế của từng bước kiểm thử.

### 7.4. Cách xuất sang Báo cáo Word (.docx) / PDF để nộp bài
1. **Từ file HTML**: Mở file `Bao_cao_Kiem_thu_Tao_Phieu_Nhap.html` trên trình duyệt -> Nhấn `Ctrl + P` -> Chọn máy in là `Save as PDF` để xuất ra file PDF báo cáo đẹp mắt.
2. **Từ file Excel**: Có thể copy bảng dữ liệu từ Sheet 1 và Sheet 2 dán trực tiếp vào file báo cáo Microsoft Word (`.docx`) theo đúng cấu trúc mẫu báo cáo của nhóm 3.
3. **Từ file Markdown này**: Có thể sử dụng VS Code hoặc công cụ Markdown Viewer để xuất sang PDF/Word trực tiếp.

---
*Báo cáo được hoàn thành và kiểm tra tính toàn vẹn vào ngày 26/08/2026.*
