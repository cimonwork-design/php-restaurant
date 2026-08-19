# Theo dõi đối chiếu báo cáo và rà soát toàn bộ dự án

Ngày lập: 2026-05-25  
Dự án: `php-restaurant-main`  
Tài liệu tham chiếu: `C:\Users\Admin\Downloads\BaoCaoBTL_PhanTichThietKeHeThongThongTin.docx`

## 1. Mục tiêu tài liệu

File này dùng để theo dõi các việc cần kiểm tra và chỉnh sửa sau khi đối chiếu giữa báo cáo phân tích thiết kế hệ thống và source code hiện tại.

Phạm vi yêu cầu:

- Đọc báo cáo phân tích thiết kế hệ thống.
- Đánh giá chức năng trong báo cáo nhưng dự án chưa có.
- Phân loại chức năng thiếu thành: cần làm, có thể bỏ qua, hoặc cần làm sau.
- Kiểm tra toàn bộ trang hiện có trong dự án.
- Liệt kê các case chính của từng trang và đánh giá case đó đã ổn định chưa.
- Ghi rõ các lỗi/điểm sai logic đang có và hướng sửa.
- Ưu tiên sửa phần báo cáo doanh thu, chi phí nguyên liệu, chi tiết theo ngày và dashboard biểu đồ.
- Kiểm tra lại phân quyền hiện tại; nếu phân quyền chưa đủ thì thiết kế lại quyền theo vai trò.
- Kiểm tra full luồng nghiệp vụ từ nhập nguyên liệu, xuất nguyên liệu, báo cáo kho, đặt bàn, tạo đơn, thanh toán và xuất hóa đơn.
- Redesign giao diện `sale_order`, đặc biệt màn tạo đơn mới, theo hướng phù hợp bán hàng tại quầy hơn: thao tác nhanh, thực tế, dễ nhìn và đẹp hơn giao diện hiện tại.

## 2. Tóm tắt nội dung chính trong báo cáo

Báo cáo mô tả hệ thống quản lý nhà hàng từ kho nguyên liệu đến quản trị doanh thu chi phí. Các nhóm chức năng chính được nhắc tới:

- Quản lý tài khoản, nhân viên, phân quyền.
- Đăng nhập, đăng xuất, đăng ký.
- Quản lý món ăn/thực đơn.
- Quản lý đặt món và phục vụ.
- Gọi thêm món, hủy món.
- Chuyển bàn/gộp bàn.
- Quản lý bàn ăn bằng mã QR.
- Quản lý kho nguyên liệu.
- Quản lý nhà cung cấp nguyên liệu.
- Lập phiếu nhập kho.
- Xuất kho, kiểm kê, điều chỉnh kho.
- Thiết lập định lượng/công thức món ăn.
- Quản lý khuyến mãi.
- Thanh toán.
- Thống kê doanh thu theo ngày/tháng.
- Báo cáo mặt hàng bán chạy.
- Báo cáo kho nguyên liệu.
- Cơ sở dữ liệu dự kiến có các bảng như `ROLE`, `USER`, `SUPPLIER`, `INGREDIENT_CATEGORY`, `INGREDIENT`, `PURCHASE_RECEIPT`, `PURCHASE_RECEIPT_ITEM`, `STOCK_ADJUSTMENT`, `STOCK_MOVEMENT`, `EXPENSE_CATEGORY`, `EXPENSE`, `ALERT`.

Ghi chú: báo cáo có một số phần bị lệch nội dung hoặc thừa từ mẫu cũ, ví dụ mục lục có dòng "quản lý lịch chiếu phim" và phần cam đoan nhắc tới "ứng dụng dạy học lập trình". Các phần này không nên dùng làm yêu cầu chức năng cho dự án nhà hàng.

## 3. Đối chiếu báo cáo với dự án hiện tại

| Nhóm chức năng trong báo cáo | Hiện trạng trong source | Đánh giá | Hướng xử lý |
| --- | --- | --- | --- |
| Đăng nhập/đăng xuất | Có `AuthController`, JWT, cookie, session | Cần kiểm tra thêm bảo mật | Giữ, kiểm tra active user, CSRF, role |
| Đăng ký | Có trang đăng ký công khai và cho chọn role | Rủi ro cao | Nên tắt đăng ký công khai hoặc không cho tự chọn `admin/manager` |
| Quản lý tài khoản/phân quyền | Có `UserController`, admin-only ở controller | Tương đối có | Bổ sung kiểm tra role cho các module quan trọng |
| Quản lý món ăn | Có CRUD `menu_item` | Cơ bản ổn | Kiểm tra validate, xóa khi có order/recipe |
| Quản lý nguyên liệu | Có CRUD `ingredient`, tồn kho tính qua `inventory_log` | Cơ bản có | Cần thống nhất đơn vị số lượng kiểu decimal |
| Loại nguyên liệu | Có `ingredient_category`, nhưng `ingredient.category` là text | Chưa chuẩn | Cân nhắc chuyển sang FK hoặc giữ nếu không cần đúng 3NF |
| Công thức/định lượng món | Có `recipe` | Cần kiểm tra | Bắt buộc để tính chi phí nguyên liệu và xuất kho tự động |
| Nhà cung cấp | Chỉ có field text `supplier/main_supplier`, chưa có bảng `supplier` | Thiếu so với báo cáo | Có thể bỏ qua nếu demo không yêu cầu quản lý nhà cung cấp riêng; cần làm nếu muốn khớp báo cáo |
| Nhập kho | Có `inventory_receipt` và detail | Có nhưng cần sửa case | Khi hoàn thành phiếu nhập chưa cập nhật `purchase_price`; không chặn phiếu không có detail |
| Xuất kho | Có `inventory_issue`, detail, complete | Có nhưng lỗi trạng thái | `complete()` chưa set `status = completed`, có thể complete lặp và trừ kho nhiều lần |
| Nhật ký kho | Có `inventory_log` | Có | Cần chống log trùng khi complete lặp |
| Kiểm kê/điều chỉnh kho | Có bảng `stock_adjustment`, chưa thấy trang CRUD riêng | Thiếu một phần | Có thể làm sau nếu chưa cần demo; báo cáo có nhắc nên nên bổ sung nếu muốn sát tài liệu |
| Đặt bàn | Có controller/view `reservation` nhưng sidebar chưa hiển thị | Có nhưng schema chính thiếu bảng | Cần thêm vào `schema.sql` hoặc migration; cân nhắc đưa vào sidebar |
| QR đặt món | Có `QrController` và `PublicOrderController` | Có nhưng phụ thuộc migration | Cần đưa cột QR vào schema chính; kiểm tra tồn kho khi khách đặt |
| Đơn hàng nội bộ | Có `sale_order` | Có nhưng nhiều case sai | Cần sửa tính tiền, trạng thái bàn, tồn kho, chiết khấu/VAT |
| Gọi thêm món | Có `addItem/saveAddItems` | Chưa ổn | Không kiểm tra tồn kho; không gộp món trùng; không xét discount/VAT |
| Hủy món ăn từng dòng | Báo cáo có, source chưa có rõ | Thiếu | Cần làm nếu muốn khớp use case đặt món/phục vụ |
| Chuyển bàn/gộp bàn | Báo cáo có, source chưa có | Thiếu | Có thể bỏ qua nếu demo đơn giản; cần làm nếu báo cáo yêu cầu trình bày use case này |
| Khuyến mãi | Báo cáo có, source chưa có module | Thiếu | Có thể bỏ qua nếu không dùng; nếu giữ trong báo cáo thì cần module hoặc tối thiểu discount hợp lệ |
| Thanh toán | Có `pay()` đổi status paid và giải phóng bàn | Còn thiếu | Cần tính lại tổng tiền theo discount/VAT, chặn thanh toán sai trạng thái |
| Chi phí cố định | Có `expense` | Có | Cần validate amount/date, phân loại chi phí nếu muốn sát báo cáo |
| Báo cáo doanh thu | Có `ReportController@index` | Có lỗi | Phải sửa chi phí nguyên liệu, lọc status, chi tiết theo ngày |
| Báo cáo kho | Có `stock_report` | Tương đối có | Kiểm tra tồn kho âm, cảnh báo, nút nhập bù |
| Báo cáo bán chạy | Báo cáo có, source chưa có riêng | Thiếu | Nên làm sau, vì hữu ích và đúng báo cáo |
| Dashboard biểu đồ | Hiện chỉ card/list, chưa có chart | Thiếu theo yêu cầu mới | Thêm Chart.js hoặc thư viện tương đương |
| Alert/cảnh báo | Báo cáo có bảng `ALERT`, source chỉ hiển thị cảnh báo tồn kho | Thiếu bảng riêng | Có thể bỏ qua nếu chỉ cần cảnh báo trực tiếp từ tồn kho |

## 4. Các lỗi logic ưu tiên cần sửa

### P0 - Cần sửa trước

- Báo cáo doanh thu tính sai chi phí nguyên liệu: query hiện tại dùng `SUM(r.qty * i.purchase_price)` nhưng chưa nhân `sod.qty`. Đúng phải là `SUM(sod.qty * r.qty * i.purchase_price)`.
- Báo cáo doanh thu đang tính tất cả đơn theo ngày, chưa lọc rõ chỉ đơn `paid` hoặc trạng thái doanh thu hợp lệ. Nên thống nhất doanh thu chỉ tính đơn đã thanh toán.
- Báo cáo doanh thu chưa có chi tiết khi bấm vào một ngày. Cần thêm endpoint/controller hoặc query trong `index()` để hiển thị danh sách đơn trong ngày, từng món, doanh thu, chi phí nguyên liệu, chi phí cố định, lãi.
- `InventoryIssueController::complete()` chưa đổi `status` sang `completed`. Người dùng có thể bấm hoàn thành nhiều lần và trừ kho nhiều lần.
- `SaleOrderController::complete()` tự tạo phiếu xuất kho nhưng phiếu xuất được tạo không set `status = completed`, đồng thời không có cơ chế chống tạo phiếu xuất trùng nếu bấm hoàn thành lại.
- `SaleOrderController::store()` và `update()` lưu `discount`, `vat_rate` nhưng `total_amount` chỉ là tổng dòng món, chưa áp dụng giảm giá/VAT.
- `PublicOrderController::submit()` tạo đơn QR nhưng không kiểm tra tồn kho theo công thức.
- Schema chính thiếu các cột đang được code QR dùng: `restaurant_table.order_token`, `sale_order.source`, `sale_order.customer_name`, `sale_order.customer_phone`. Nếu chỉ import `schema.sql`, QR order có thể lỗi nếu chưa chạy migration.
- Schema chính thiếu bảng `reservation` trong khi source có module đặt bàn.

### P1 - Cần sửa sau P0

- Xóa món ăn/nguyên liệu/người dùng/bàn có thể lỗi khóa ngoại nếu đã có dữ liệu liên quan. Cần chuyển sang chặn xóa với thông báo rõ hoặc soft-delete.
- Các thao tác POST dạng CRUD chưa có CSRF token.
- Các module ngoài `UserController` chủ yếu chỉ kiểm tra đã đăng nhập, chưa kiểm tra role. Báo cáo có phân quyền nên cần xác định quyền cho admin/manager/user.
- Đăng ký công khai cho phép chọn role, có thể tự tạo tài khoản admin. Nên bỏ chọn role ở register hoặc tắt register công khai.
- `InventoryReceiptController::complete()` có chống complete lặp, nhưng chưa cập nhật giá nhập mới vào `ingredient.purchase_price` nếu muốn báo cáo chi phí dùng giá thực nhập gần nhất.
- `InventoryReceiptController::store/update()` cho phép tạo phiếu không có nguyên liệu hợp lệ.
- `InventoryIssueController::store/update()` cho phép tạo phiếu xuất không có nguyên liệu hợp lệ và chưa kiểm tra tồn kho âm.
- `SaleOrderController::update()` thay bàn nhưng chưa giải phóng bàn cũ/cập nhật bàn mới đúng trạng thái.
- `SaleOrderController::delete()` xóa đơn nhưng không xử lý lại trạng thái bàn và phiếu xuất liên quan.
- `SaleOrderController::cancel()` nếu đơn đã `served` và đã trừ kho thì không hoàn kho.
- `saveAddItems()` không kiểm tra tồn kho, không gộp món trùng, tin vào price/name từ client.

### P2 - Có thể làm nếu muốn khớp báo cáo hơn

- Thêm module nhà cung cấp riêng (`supplier`) thay vì lưu text trong phiếu nhập/nguyên liệu.
- Thêm module khuyến mãi riêng và liên kết vào thanh toán.
- Thêm chức năng chuyển bàn/gộp bàn.
- Thêm hủy món từng dòng trong đơn.
- Thêm báo cáo mặt hàng bán chạy.
- Thêm quản lý phân loại chi phí (`expense_category`).
- Thêm bảng cảnh báo (`alert`) nếu muốn lưu lịch sử cảnh báo tồn kho.

## 5. Audit từng trang và case cần kiểm tra

### Auth - `app/views/auth/login.php`, `register.php`

Cases:

- Mở login khi chưa đăng nhập: ổn.
- Đăng nhập thiếu username/password: đã trả JSON lỗi.
- Sai username/password: đã trả JSON lỗi, có audit.
- Đăng nhập user inactive: cần kiểm tra model, nếu chưa chặn thì phải sửa.
- Remember me: có cookie JWT.
- Logout: có xóa session/cookie.
- Register public: đang cho chọn role, không ổn về bảo mật.

Việc cần làm:

- Kiểm tra `User::authenticate()` có chặn `active = 0` không.
- Không cho public register tự chọn role cao.
- Cân nhắc ẩn/tắt register nếu báo cáo chỉ yêu cầu admin tạo nhân viên.

### Dashboard - `app/views/dashboard/index.php`

Cases:

- Hiển thị số lượng nguyên liệu/món/bàn/user/phiếu nhập/phiếu xuất/chi phí: ổn cơ bản.
- Doanh thu hôm nay: hiện tính mọi đơn trong ngày, nên lọc `paid`.
- Hoạt động gần đây: ổn cơ bản.
- Biểu đồ dashboard: chưa có.

Việc cần làm:

- Thêm dữ liệu biểu đồ doanh thu 7/30 ngày, chi phí nguyên liệu, chi phí cố định.
- Thêm Chart.js bằng CDN hoặc asset local. Với XAMPP demo offline nên ưu tiên asset local nếu có thể.
- Biểu đồ đề xuất: line chart doanh thu/lợi nhuận theo ngày, doughnut chart trạng thái bàn hoặc cơ cấu chi phí.

### Người dùng - `app/views/user/*`

Cases:

- Chỉ admin vào được: controller đã chặn.
- Tạo user thiếu username/password/role: có validate.
- Trùng username: có chặn.
- Sửa user: có chặn trùng username.
- Xóa chính mình: có chặn.
- Xóa user đã liên quan order/receipt/expense: có thể lỗi hoặc mất lịch sử nếu FK không đầy đủ.

Việc cần làm:

- Chặn xóa user đã có dữ liệu liên quan hoặc chuyển sang `active = 0`.
- Không lưu password null khi edit bỏ trống.

### Nguyên liệu - `app/views/ingredient/*`

Cases:

- Danh sách nguyên liệu kèm tồn kho: có query tổng `inventory_log`.
- Tạo/sửa/xóa nguyên liệu: có CRUD.
- Mã nguyên liệu trùng: cần kiểm tra controller đã chặn.
- Xóa nguyên liệu đang có recipe/log/detail: có thể lỗi FK.
- Đơn vị và số lượng: schema detail dùng `INT`, recipe dùng `DECIMAL`; có thể lệch với nguyên liệu tính theo kg/lít.

Việc cần làm:

- Chặn xóa nguyên liệu đã phát sinh dữ liệu.
- Chuyển `inventory_receipt_detail.qty` và `inventory_issue_detail.qty` sang `DECIMAL(10,3)` nếu cần định lượng thực tế.
- Nếu muốn chuẩn với báo cáo, liên kết `category_id` thay vì text `category`.

### Loại nguyên liệu - `app/views/ingredient_category/*`

Cases:

- CRUD category: có.
- Trùng tên: cần chặn.
- Xóa category đang được nguyên liệu dùng: hiện không có FK vì nguyên liệu lưu text.

Việc cần làm:

- Quyết định giữ category dạng text hay migrate sang FK.

### Món ăn - `app/views/menu_item/*`

Cases:

- CRUD món ăn: có.
- Mã món trùng: cần kiểm tra đã chặn.
- Giá âm/bằng 0: cần validate.
- Xóa món đã có công thức/đơn hàng: có thể lỗi hoặc làm mất dữ liệu.

Việc cần làm:

- Validate giá > 0.
- Chặn xóa món đã phát sinh recipe/order detail hoặc dùng trạng thái ngừng bán.

### Công thức - `app/views/recipe/*`

Cases:

- Chọn món để xem công thức: có.
- Thêm/sửa/xóa nguyên liệu trong công thức: có.
- Một nguyên liệu bị thêm trùng trong cùng món: cần chặn hoặc gộp.
- Định lượng <= 0: cần validate.

Việc cần làm:

- Chặn trùng `menu_id + ingredient_id`.
- Validate `qty > 0`.
- Đây là dữ liệu bắt buộc để tính chi phí nguyên liệu và kiểm tồn kho.

### Phiếu nhập kho - `app/views/inventory_receipt/*`

Cases:

- Tạo phiếu pending: có.
- Thêm nhiều dòng nguyên liệu: có.
- Hoàn thành phiếu để cộng tồn kho: có.
- Bấm hoàn thành lại: đã chặn theo status.
- Cập nhật phiếu đã completed: hiện có thể sửa detail sau khi đã cộng kho, gây lệch log.
- Xóa phiếu đã completed: hiện có thể xóa receipt/detail nhưng log vẫn còn, gây lệch dữ liệu.

Việc cần làm:

- Không cho sửa/xóa phiếu đã completed, hoặc phải đảo log trước khi sửa/xóa.
- Cập nhật `ingredient.purchase_price` theo giá nhập nếu chọn dùng giá nhập mới nhất cho báo cáo.
- Chặn tạo phiếu không có detail hợp lệ.

### Phiếu xuất kho - `app/views/inventory_issue/*`

Cases:

- Tạo phiếu xuất pending/manual/waste/sale: có.
- Hoàn thành phiếu để trừ kho: có.
- Bấm hoàn thành lại: hiện chưa chặn, lỗi nghiêm trọng.
- Sửa/xóa phiếu đã completed: có thể làm lệch log.
- Xuất quá tồn kho: cần kiểm tra, hiện chưa chặn rõ.

Việc cần làm:

- Set `status = completed` khi complete.
- Chặn complete lặp.
- Chặn sửa/xóa phiếu đã completed hoặc đảo log đúng.
- Kiểm tra tồn kho trước khi xuất.

### Bàn ăn - `app/views/restaurant_table/*`

Cases:

- CRUD bàn: có.
- Trùng số bàn: cần chặn.
- Xóa bàn đang có đơn/đặt chỗ: có thể lỗi FK hoặc mất logic.
- Trạng thái bàn free/occupied/reserved: có nhưng chưa đồng bộ hoàn toàn với reservation/order.

Việc cần làm:

- Chặn xóa bàn đã phát sinh dữ liệu.
- Đồng bộ trạng thái khi đặt bàn, tạo order, thanh toán, hủy order.

### QR đặt món - `app/views/qr/*`, `app/views/public_order/*`

Cases:

- Sinh token QR cho bàn: có.
- Xóa token QR: có.
- Khách mở link theo token: có.
- Khách gửi order: có.
- Tải QR: phụ thuộc network tới `api.qrserver.com`.
- Schema mới import chưa có cột QR nếu không chạy migration.
- Khách đặt món không kiểm tồn kho.

Việc cần làm:

- Đưa cột QR vào `schema.sql` hoặc tài liệu setup bắt buộc chạy migration.
- Kiểm tồn kho khi public order submit.
- Không tin price từ client; hiện public order lấy price DB là đúng.
- Cân nhắc sinh QR local bằng thư viện nếu không muốn phụ thuộc internet.

### Đơn hàng - `app/views/sale_order/*`

Cases:

- Tạo đơn nội bộ: có.
- Chọn nhiều món: có.
- Kiểm tồn kho trước khi tạo: có dựa trên recipe/log.
- Tổng tiền: chưa áp dụng discount/VAT.
- Sửa đơn: có nhưng không kiểm tồn kho lại và không xử lý bàn cũ.
- Thêm món vào đơn mở: có nhưng không kiểm tồn kho, không gộp món trùng.
- Hoàn thành phục vụ: có, tự tạo xuất kho.
- Thanh toán: có, set paid và free bàn.
- Hủy đơn: có, nhưng không hoàn kho nếu đã xuất.
- Xóa đơn: có, nhưng không xử lý bàn/kho/liên kết.

Việc cần làm:

- Chuẩn hóa trạng thái: `open -> served -> paid`, `open/served -> cancel` có quy tắc rõ.
- Chặn thao tác không hợp lệ theo trạng thái.
- Tính tổng tiền đúng: subtotal, discount, VAT, grand total.
- Tự tạo phiếu xuất kho một lần duy nhất.
- Cân nhắc xuất kho khi `paid` thay vì `served`, hoặc giữ `served` nhưng phải xử lý cancel/return.
- Thêm hủy món từng dòng nếu muốn sát báo cáo.
- Thêm chuyển bàn/gộp bàn nếu muốn sát báo cáo.

### Chi phí - `app/views/expense/*`

Cases:

- CRUD chi phí: có.
- Thiếu type/amount/date: cần kiểm tra validate.
- Amount <= 0: cần validate.
- Báo cáo lấy chi phí theo `expense_date`: đúng hướng.

Việc cần làm:

- Validate amount > 0.
- Cân nhắc bảng `expense_category` nếu muốn sát báo cáo.

### Báo cáo doanh thu - `app/views/report/index.php`

Cases:

- Lọc theo khoảng ngày: có.
- Tổng doanh thu theo ngày: có nhưng cần lọc status.
- Chi phí cố định theo ngày: có.
- Chi phí nguyên liệu theo ngày: có nhưng sai công thức vì thiếu `sod.qty`.
- Tổng/lợi nhuận: phụ thuộc lỗi chi phí.
- Chi tiết một ngày: chưa có.
- Phân trang theo ngày: có.

Việc cần làm:

- Sửa query chi phí nguyên liệu:

```sql
SUM(sod.qty * r.qty * i.purchase_price) AS ingredient_cost
```

- Thống nhất chỉ tính đơn `paid` trong doanh thu.
- Thêm cột hoặc nút bấm ngày để mở chi tiết ngày.
- Chi tiết ngày cần có: danh sách đơn, bàn, trạng thái, món, số lượng, doanh thu đơn, chi phí nguyên liệu đơn, chi phí cố định trong ngày, lãi ngày.
- Nếu muốn chính xác theo giá nhập lịch sử, cần lưu giá vốn tại thời điểm xuất/đơn, vì dùng `ingredient.purchase_price` hiện tại sẽ làm thay đổi báo cáo quá khứ.

### Báo cáo kho - `app/views/report/stock_report.php`

Cases:

- Tồn kho = tổng `inventory_log.qty_change`: có.
- Cảnh báo hết/sắp hết: có.
- Gợi ý nhập bù: có.
- Lịch sử xuất kho gần đây: có.
- Phân trang issue gần đây: có.

Việc cần làm:

- Kiểm tra log trùng do complete xuất kho.
- Thêm link xem lịch sử nhập/xuất theo nguyên liệu nếu cần.
- Nếu có `stock_adjustment`, cần đưa vào màn hình điều chỉnh tồn kho thay vì chỉ bảng DB.

### Đặt bàn - `app/views/reservation/*`

Cases:

- CRUD đặt bàn: có.
- Kiểm tra trùng thời gian: có.
- End time <= start time: có chặn.
- Sidebar chưa có link.
- Schema chính thiếu bảng `reservation`.
- Không thấy đồng bộ tự động trạng thái bàn `reserved`.

Việc cần làm:

- Thêm bảng `reservation` vào `schema.sql`/migration.
- Thêm menu sidebar nếu dùng chức năng này.
- Cập nhật trạng thái bàn theo reservation nếu cần demo.

## 6. Kế hoạch thực hiện đề xuất

### Giai đoạn 1 - Sửa lỗi nghiệp vụ cốt lõi

- [x] Sửa báo cáo doanh thu: chi phí nguyên liệu nhân số lượng món, lọc đơn `paid`.
- [x] Thêm chi tiết báo cáo theo ngày khi bấm vào ngày.
- [x] Sửa `InventoryIssueController::complete()` chống complete lặp và set status.
- [x] Chống tạo phiếu xuất kho trùng từ `SaleOrderController::complete()`.
- [x] Chuẩn hóa tính tiền đơn hàng với subtotal/discount/VAT/total.
- [x] Kiểm tồn kho khi thêm món và QR order.

### Giai đoạn 2 - Ổn định dữ liệu và schema

- [x] Cập nhật `schema.sql` với cột QR/order metadata và bảng `reservation`.
- [x] Chặn sửa/xóa phiếu nhập/xuất đã completed hoặc xử lý đảo log.
- [ ] Chặn xóa dữ liệu đã phát sinh liên kết.
- [ ] Validate số tiền/số lượng/giá/định lượng > 0.
- [ ] Rà lại encoding tiếng Việt trong source nếu cần hiển thị đẹp.

### Giai đoạn 3 - Dashboard và báo cáo nâng cao

- [x] Thêm Chart.js cho dashboard.
- [x] Biểu đồ doanh thu/lợi nhuận 7 hoặc 30 ngày.
- [x] Biểu đồ cơ cấu chi phí: nguyên liệu vs chi phí cố định.
- [ ] Biểu đồ trạng thái bàn hoặc tồn kho cảnh báo.
- [ ] Thêm báo cáo món bán chạy.

### Giai đoạn 4 - Chức năng theo báo cáo nhưng có thể làm sau

- [ ] Module nhà cung cấp riêng.
- [ ] Module khuyến mãi riêng.
- [ ] Chuyển bàn/gộp bàn.
- [ ] Hủy món từng dòng.
- [ ] Điều chỉnh/kiểm kê kho có giao diện riêng.
- [ ] Bảng cảnh báo/lịch sử cảnh báo.

## 7. Tiêu chí kiểm thử sau khi sửa

### Case doanh thu và chi phí nguyên liệu

1. Tạo nguyên liệu A có giá `10,000`.
2. Tạo món M có công thức dùng `2` đơn vị A.
3. Nhập kho A đủ số lượng và complete phiếu nhập.
4. Tạo đơn bán `3` món M, hoàn thành và thanh toán.
5. Báo cáo ngày đó phải hiển thị:
   - Doanh thu = `3 * giá bán M` sau discount/VAT nếu có.
   - Chi phí nguyên liệu = `3 * 2 * 10,000`.
   - Số đơn = 1 nếu lọc đơn `paid`.
6. Bấm vào ngày đó phải thấy đơn vừa tạo và dòng chi phí tương ứng.

### Case chống xuất kho trùng

1. Tạo đơn có món dùng nguyên liệu.
2. Bấm hoàn thành đơn một lần: tồn kho giảm đúng.
3. Reload/bấm lại hoàn thành: hệ thống phải chặn, tồn kho không giảm thêm.

### Case phiếu xuất thủ công

1. Tạo phiếu xuất pending.
2. Complete lần đầu: status chuyển `completed`, log âm được tạo.
3. Complete lần hai: bị chặn.
4. Không cho sửa/xóa phiếu completed nếu chưa có cơ chế đảo log.

### Case QR order

1. Sinh QR cho bàn.
2. Mở link public, chọn món.
3. Nếu tồn kho đủ: tạo đơn thành công, bàn occupied.
4. Nếu tồn kho thiếu: báo lỗi rõ, không tạo đơn.

### Case dashboard

1. Có đơn paid trong 7 ngày gần nhất.
2. Dashboard hiển thị chart có dữ liệu, không trắng.
3. Doanh thu hôm nay chỉ tính đơn paid.

## 8. Quyết định chức năng có thể bỏ qua nếu cần rút gọn

Các chức năng sau có trong báo cáo nhưng có thể bỏ qua nếu mục tiêu là demo quản lý nhà hàng mức cơ bản và không cần khớp 100% use case:

- Nhà cung cấp dạng bảng riêng: hiện có thể dùng text `supplier/main_supplier`.
- Khuyến mãi riêng: hiện có `discount` trong đơn, có thể coi là đủ nếu không trình bày module khuyến mãi.
- Chuyển bàn/gộp bàn: hữu ích nhưng không bắt buộc cho luồng kho-doanh thu.
- Bảng `ALERT`: có thể thay bằng cảnh báo trực tiếp trong báo cáo kho.
- `EXPENSE_CATEGORY`: có thể dùng `expense_type` text.

Các chức năng không nên bỏ qua vì ảnh hưởng trực tiếp tới báo cáo và demo:

- Công thức món ăn.
- Nhập kho và xuất kho có log đúng.
- Tạo đơn, hoàn thành, thanh toán đúng trạng thái.
- Báo cáo doanh thu/chi phí nguyên liệu/lợi nhuận chính xác.
- Chi tiết doanh thu theo ngày.
- Dashboard có biểu đồ.

## 9. Bổ sung yêu cầu kiểm tra phân quyền

Hiện trạng sơ bộ:

- Dự án có `role` trong bảng `users` với các vai trò `admin`, `manager`, `user`.
- `UserController` đã có `requireAdmin()`, chỉ admin được vào quản lý người dùng.
- Sidebar chỉ ẩn/hiện menu người dùng theo role admin.
- Phần lớn controller khác chỉ kiểm tra đã đăng nhập bằng `JWT::getCurrentUser()`, chưa kiểm tra quyền theo vai trò.
- Public register đang cho tự chọn role, đây là lỗi phân quyền nghiêm trọng nếu dùng thật.

Kết luận:

- Dự án đã có nền tảng role nhưng chưa có phân quyền đầy đủ.
- Cần làm lại phân quyền theo ma trận quyền rõ ràng, không chỉ ẩn menu ở giao diện.
- Phải chặn quyền ở controller/action, vì người dùng vẫn có thể truy cập URL trực tiếp.

Ma trận quyền đề xuất:

| Module/chức năng | Admin | Manager | User/Nhân viên |
| --- | --- | --- | --- |
| Dashboard | Xem tất cả | Xem vận hành/báo cáo | Xem giới hạn nếu cần |
| Người dùng | CRUD, khóa/mở tài khoản | Không | Không |
| Nguyên liệu, loại nguyên liệu | CRUD | CRUD | Xem |
| Công thức món | CRUD | CRUD | Xem |
| Món ăn | CRUD | CRUD | Xem/tạo đơn |
| Phiếu nhập kho | CRUD, complete | CRUD, complete | Xem hoặc tạo theo phân công |
| Phiếu xuất kho | CRUD, complete | CRUD, complete | Tạo/xem phiếu liên quan |
| Báo cáo kho | Xem | Xem | Không hoặc xem giới hạn |
| Bàn ăn/QR | CRUD | CRUD | Xem, dùng để tạo đơn |
| Đặt bàn | CRUD | CRUD | Tạo/sửa đặt bàn |
| Đơn hàng tại quầy | Toàn quyền | Toàn quyền | Tạo đơn, thêm món, hoàn thành, thanh toán nếu được phân công |
| Chi phí | CRUD | CRUD | Không |
| Báo cáo doanh thu | Xem | Xem | Không |
| Cấu hình/schema/migration | Admin | Không | Không |

Việc cần làm:

- [x] Tạo helper phân quyền dùng chung, ví dụ `requireRole([...])` hoặc `authorize($permission)`.
- [x] Áp dụng kiểm tra quyền ở mọi action quan trọng: create/store/edit/update/delete/complete/pay/cancel/report.
- [x] Tắt public register hoặc chỉ cho đăng ký role `user`; role cao chỉ admin tạo.
- [x] Kiểm tra user `active = 0` không được đăng nhập.
- [x] Ẩn menu theo quyền nhưng vẫn phải chặn ở controller.
- [ ] Ghi thông báo rõ khi không đủ quyền thay vì redirect im lặng.

Tiêu chí kiểm thử phân quyền:

1. User thường truy cập `/user`: bị chặn.
2. User thường gọi trực tiếp `/expense/delete/1`: bị chặn.
3. User thường gọi trực tiếp `/report`: bị chặn nếu không có quyền báo cáo.
4. Manager vào quản lý kho và báo cáo: được phép nếu theo ma trận.
5. Tài khoản inactive đăng nhập: thất bại.
6. Public register không thể tạo admin/manager.

## 10. Bổ sung yêu cầu kiểm tra full luồng nghiệp vụ

Luồng cần kiểm tra từ đầu đến cuối:

1. Tạo nguyên liệu và loại nguyên liệu.
2. Nhập kho bằng phiếu nhập.
3. Hoàn thành phiếu nhập để cộng tồn kho.
4. Tạo món ăn và công thức định lượng.
5. Kiểm tra báo cáo kho hiển thị tồn đúng.
6. Tạo bàn hoặc đặt bàn trước.
7. Tạo đơn hàng tại quầy hoặc từ QR.
8. Kiểm tra tồn kho trước khi nhận đơn.
9. Hoàn thành phục vụ để xuất kho hoặc xuất kho khi thanh toán, tùy quyết định nghiệp vụ.
10. Thanh toán đơn.
11. Xuất hóa đơn/biên lai.
12. Kiểm tra báo cáo kho sau bán hàng.
13. Kiểm tra báo cáo doanh thu, chi phí nguyên liệu, chi phí cố định và lợi nhuận.
14. Bấm vào ngày trong báo cáo doanh thu để xem chi tiết đơn và chi phí.

Các điểm phải xác nhận:

- Tồn kho ban đầu đúng sau nhập kho.
- Tồn kho giảm đúng theo `recipe.qty * số lượng món`.
- Không xuất kho trùng khi bấm hoàn thành nhiều lần.
- Không cho bán nếu thiếu nguyên liệu, hoặc có cảnh báo rõ nếu cho phép bán âm.
- Doanh thu chỉ ghi nhận khi đơn đã thanh toán.
- Chi phí nguyên liệu trong báo cáo khớp với công thức và số lượng bán.
- Bàn chuyển trạng thái đúng: free, occupied, reserved.
- Đơn hủy không làm sai tồn kho/bàn/doanh thu.
- Phiếu nhập/xuất đã completed không được sửa/xóa gây lệch log nếu chưa có cơ chế đảo log.

## 11. Bổ sung yêu cầu xuất hóa đơn

Hiện source có thanh toán đơn (`pay`) nhưng chưa thấy màn hóa đơn/biên lai đúng nghĩa.

Yêu cầu cần bổ sung:

- [x] Thêm trang xem hóa đơn cho một đơn hàng, ví dụ `/sale_order/invoice/{id}`.
- [x] Hóa đơn cần hiển thị: mã đơn, thời gian, bàn, nhân viên, danh sách món, số lượng, đơn giá, thành tiền, giảm giá, VAT, tổng thanh toán.
- [x] Chỉ đơn `paid` mới được xuất hóa đơn chính thức.
- [x] Có nút in hóa đơn bằng `window.print()`.
- [x] Sau khi thanh toán, chuyển tới trang hóa đơn hoặc hiển thị nút "In hóa đơn".
- [x] Báo cáo doanh thu chi tiết ngày nên liên kết được tới hóa đơn từng đơn.

Tiêu chí kiểm thử:

1. Đơn chưa thanh toán: không cho in hóa đơn chính thức hoặc chỉ cho xem tạm tính.
2. Đơn đã thanh toán: hóa đơn hiển thị đúng tổng tiền.
3. Discount/VAT nếu có phải khớp với `sale_order.total_amount`.
4. In hóa đơn không hiển thị sidebar/navbar thừa.

## 12. Bổ sung yêu cầu redesign `sale_order`

Mục tiêu:

- Biến trang đơn hàng thành giao diện bán hàng tại quầy/POS thực tế hơn.
- Giảm thao tác khi tạo đơn.
- Cho nhân viên nhìn nhanh bàn, món, giỏ hàng, tổng tiền và thanh toán.
- Giao diện đẹp hơn nhưng vẫn phù hợp hệ thống quản lý nhà hàng, không làm kiểu landing page.

### Trang danh sách đơn hàng `app/views/sale_order/index.php`

Yêu cầu giao diện:

- Bố cục dạng dashboard vận hành: bộ lọc trạng thái, tìm theo mã đơn/bàn/ngày.
- Badge trạng thái rõ: `open`, `served`, `paid`, `cancel`.
- Hiển thị nhanh: mã đơn, bàn, thời gian, nhân viên, tổng tiền, số món, trạng thái.
- Action theo trạng thái:
  - `open`: thêm món, hoàn thành phục vụ, hủy.
  - `served`: thanh toán, hủy nếu được phép.
  - `paid`: xem/in hóa đơn.
  - `cancel`: chỉ xem.
- Tránh để quá nhiều nút giống nhau trên một dòng; dùng nhóm action rõ.

### Trang tạo đơn mới `app/views/sale_order/create.php`

Yêu cầu giao diện POS:

- Layout 2 hoặc 3 vùng:
  - Bên trái: chọn bàn, thông tin đơn, bộ lọc/tìm món.
  - Trung tâm: lưới món ăn có tên, giá, trạng thái còn/thiếu nguyên liệu nếu có.
  - Bên phải: giỏ hàng/tạm tính, số lượng, discount, VAT, tổng thanh toán.
- Có ô tìm món nhanh theo tên/mã.
- Có filter nhóm món nếu sau này có category.
- Món được bấm để thêm vào giỏ, không phải nhập số lượng trong bảng dài.
- Giỏ hàng cho tăng/giảm số lượng, xóa dòng.
- Tổng tiền cập nhật ngay phía client.
- Hiển thị cảnh báo nếu món thiếu nguyên liệu.
- Nút chính rõ: "Tạo đơn", "Tạo và thanh toán" nếu sau này hỗ trợ.
- Không để form quá cơ bản kiểu danh sách input dài như hiện tại.

### Trang sửa đơn/thêm món

Yêu cầu:

- Dùng cùng phong cách POS với trang tạo đơn.
- Với đơn `open`, cho thêm/xóa/sửa số lượng.
- Với đơn `served/paid/cancel`, khóa chỉnh sửa hoặc chỉ cho xem.
- Khi thêm món phải kiểm tồn kho và cập nhật tổng tiền đúng.

### Trang hóa đơn

Yêu cầu:

- Thiết kế riêng cho in: nền trắng, khổ hẹp kiểu receipt hoặc khổ A4 tùy chọn.
- Không hiển thị sidebar khi in.
- Có thông tin nhà hàng, mã hóa đơn, ngày giờ, bàn, nhân viên, món, tổng tiền.

### Tiêu chí kiểm thử UI `sale_order`

1. Nhân viên tạo đơn bằng chuột trong vài thao tác: chọn bàn, bấm món, chỉnh số lượng, tạo đơn.
2. Tổng tiền trên UI khớp tổng tiền lưu DB.
3. Mobile/tablet vẫn dùng được ở mức cơ bản.
4. Không có text tràn nút/card.
5. Trạng thái đơn quyết định đúng nút nào được hiển thị.
6. Sau khi thanh toán, bàn được giải phóng và hóa đơn in được.
