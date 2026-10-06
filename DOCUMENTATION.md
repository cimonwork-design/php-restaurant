# 📘 TÀI LIỆU DỰ ÁN: HỆ THỐNG QUẢN LÝ NHÀ HÀNG (PHP MVC MONOLITH)

> **Mô hình**: Monolithic Architecture (Kiến trúc nguyên khối MVC)  
> **Ngôn ngữ & Công nghệ**: PHP 7.4+ / 8.x · MySQL 5.7+ / 8.0 · Apache (XAMPP) · Bootstrap 5 · Vanilla JS (ES6) · PDO Prepared Statements · JWT  
> **Vị trí thư mục**: `C:\xampp\htdocs\php-restaurant-main-main`

---

## 1. Giới Thiệu Tổng Quan

Dự án **Restaurant Management System (PHP MVC)** là hệ thống quản lý nhà hàng truyền thống xây dựng theo mô hình **Model - View - Controller (MVC)** thuần, chạy trên máy chủ Web Apache (thông qua môi trường XAMPP).

Hệ thống đóng gói toàn bộ các phân hệ nghiệp vụ nhà hàng (Thực đơn, Bàn ăn, Bán hàng, Kho nguyên liệu, Định mức công thức và Chi phí) trong một cơ sở mã nguồn (codebase) duy nhất và dùng chung một cơ sở dữ liệu quan hệ MySQL.

---

## 2. Cấu Trúc Thư Mục Dự Án

```
C:\xampp\htdocs\php-restaurant-main-main\
├── app/
│   ├── Controllers/             # Xử lý logic điều hướng và request HTTP
│   │   ├── AuthController.php       # Đăng nhập, đăng ký, cấp JWT token
│   │   ├── DashboardController.php  # Thống kê tổng quan doanh thu, tồn kho
│   │   ├── ExpenseController.php    # Quản lý các khoản chi tiêu vận hành
│   │   ├── IngredientController.php # Quản lý danh mục nguyên liệu kho
│   │   ├── InventoryController.php  # Nhập kho, xuất kho nguyên liệu
│   │   ├── MenuController.php       # Danh sách món ăn, giá bán, danh mục
│   │   ├── OrderController.php      # Tạo đơn hàng, mở bàn, in hóa đơn
│   │   ├── RecipeController.php     # Công thức định lượng món ăn (BOM)
│   │   ├── TableController.php      # Sơ đồ bàn ăn, trạng thái bàn
│   │   └── UserController.php       # Quản lý tài khoản nhân viên và phân quyền
│   ├── Models/                  # Tương tác cơ sở dữ liệu qua PDO
│   │   ├── AuditLog.php             # Ghi nhật ký thao tác hệ thống
│   │   ├── Expense.php              # Bảng chi phí
│   │   ├── Ingredient.php           # Bảng nguyên liệu
│   │   ├── MenuItem.php             # Bảng món ăn
│   │   ├── Recipe.php               # Bảng định lượng nguyên vật liệu
│   │   ├── SaleOrder.php            # Bảng đơn hàng
│   │   ├── SaleOrderDetail.php      # Bảng chi tiết món trong đơn hàng
│   │   ├── StockMovement.php        # Lịch sử biến động tồn kho (nhập/xuất)
│   │   ├── Table.php                # Bảng bàn ăn
│   │   └── User.php                 # Bảng người dùng
│   └── Views/                   # Giao diện người dùng (HTML + PHP + Bootstrap 5)
│       ├── layouts/                 # Header, Footer, Sidebar, Navigation
│       ├── auth/                    # Màn hình đăng nhập, đăng ký
│       ├── dashboard/               # Bảng điều khiển kinh doanh
│       ├── menu/                    # Quản lý thực đơn
│       ├── orders/                  # Tạo đơn, thanh toán, in bill
│       ├── tables/                  # Sơ đồ phòng bàn
│       ├── inventory/               # Phiếu nhập xuất kho
│       └── expenses/                # Sổ quỹ chi tiêu
├── config/                      # Cấu hình kết nối Database, JWT, Constants
│   ├── config.php
│   └── database.php
├── core/                        # Nhân Framework MVC tự xây dựng
│   ├── App.php                      # Bộ định tuyến URL (Router / Dispatcher)
│   ├── Controller.php               # Controller cha cung cấp hàm render view, json
│   ├── Database.php                 # Quản lý kết nối PDO Singleton
│   └── Model.php                    # Model cha cung cấp CRUD cơ bản
├── database/                    # File SQL khởi tạo cấu trúc bảng và dữ liệu mẫu
│   └── restaurant_db.sql
├── public/                      # Thư mục tài nguyên công khai (CSS, JS, Hình ảnh)
├── .htaccess                    # Rewrite URL thân thiện (Routing qua index.php)
├── index.php                    # Điểm vào duy nhất (Single Entry Point) của ứng dụng
└── README.md
```

---

## 3. Các Phân Hệ Chức Năng Chính

### 3.1. Xác Thực & Phân Quyền (Authentication & RBAC)
- Xác thực tài khoản bằng mật khẩu mã hóa `password_hash()` (Bcrypt).
- Quản lý phiên làm việc bằng Session kết hợp JSON Web Token (JWT).
- Phân quyền theo 3 vai trò:
  - **ADMIN**: Toàn quyền cấu hình hệ thống, người dùng, xem báo cáo doanh thu tài chính.
  - **MANAGER**: Quản lý kho, duyệt phiếu nhập/xuất kho, quản lý thực đơn và bàn ăn.
  - **USER / STAFF**: Nhân viên phục vụ gọi món tại bàn, nhân viên thu ngân mở đơn.

### 3.2. Quản Lý Thực Đơn & Công Thức Định Mức (Menu & Recipe BOM)
- Danh mục món ăn, giá niêm yết, tình trạng phục vụ.
- Thiết lập công thức chế biến (BOM - Bill of Materials): Một món ăn cần tiêu hao bao nhiêu đơn vị nguyên liệu kho (ví dụ: 1 phần Bò Wagyu cần 0.25 kg thịt và 0.02 hộp nấm Truffle).

### 3.3. Quản Lý Sơ Đồ Bàn & Đặt Bàn (Tables & Reservations)
- Quản lý bàn ăn theo tầng và khu vực (Phòng VIP, Trong nhà, Ngoài trời).
- Trạng thái bàn: Bàn trống (FREE), Có khách (OCCUPIED), Đã đặt trước (RESERVED).

### 3.4. Bán Hàng & Gọi Món (POS & Sales Orders)
- Mở bàn, chọn món từ thực đơn đưa vào đơn hàng.
- Lưu chi tiết đơn hàng (`sale_order_detail`), tính tiền tạm tính, thuế VAT, giảm giá.
- Thanh toán và in hóa đơn thanh toán cho khách hàng.

### 3.5. Kho & Biến Động Tồn Kho (Inventory & Stock Movements)
- Quản lý danh mục nguyên liệu thô, đơn vị tính (kg, g, lít, chai, hộp).
- Lập phiếu nhập kho từ nhà cung cấp, cập nhật đơn giá vốn nhập.
- Tự động trừ kho theo định mức khi đơn hàng hoàn tất hoặc xuất kho thủ công (hủy hỏng, hao hụt).

### 3.6. Quản Lý Chi Phí & Báo Cáo Doanh Thu (Expenses & Reports)
- Ghi chép chi phí điện nước, mặt bằng, nhân công, marketing.
- Báo cáo tổng hợp doanh thu - chi phí = lợi nhuận gộp theo chu kỳ ngày/tháng.

---

## 4. Hướng Dẫn Cài Đặt & Vận Hành Trên XAMPP

### 4.1. Yêu Cầu Môi Trường
- XAMPP phiên bản 7.4, 8.0, 8.1 hoặc 8.2 (đã tích hợp Apache & MySQL).
- Đã bật module `mod_rewrite` trong file cấu hình `httpd.conf` của Apache.

### 4.2. Các Bước Cài Đặt
1. **Sao chép mã nguồn**:
   Đặt thư mục dự án vào đường dẫn:
   ```
   C:\xampp\htdocs\php-restaurant-main-main
   ```
2. **Khởi tạo Database**:
   - Mở trình duyệt truy cập: `http://localhost/phpmyadmin`
   - Tạo mới một cơ sở dữ liệu có tên: `restaurant_db` với bảng mã `utf8mb4_unicode_ci`.
   - Nhấn **Import** và chọn file SQL tại:
     ```
     C:\xampp\htdocs\php-restaurant-main-main\database\restaurant_db.sql
     ```
3. **Cấu hình kết nối**:
   Mở file `config/database.php` và kiểm tra các thông số:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_PORT', '3306');
   define('DB_NAME', 'restaurant_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
4. **Truy cập ứng dụng**:
   Mở trình duyệt truy cập:
   ```
   http://localhost/php-restaurant-main-main/
   ```
   - **Tài khoản quản trị mặc định**: `admin@restaurant.com` / `admin123`

---

## 5. Đánh Giá Ưu Điểm & Giới Hạn Của Kiến Trúc Monolith

| Tiêu Chí | Đặc Điểm Của Hệ Thống PHP Monolith |
|---|---|
| **Ưu điểm** | - Dễ triển khai: Chỉ cần cài đặt XAMPP là chạy được toàn bộ hệ thống.<br>- Đơn giản khi phát triển nhóm nhỏ: Codebase tập trung một nơi.<br>- Truy vấn JOIN bảng trực tiếp: Dễ dàng liên kết món ăn, đơn hàng và kho trong 1 câu SQL. |
| **Hạn chế** | - **Điểm nghẽn chịu tải (Bottleneck)**: Khi lượng khách quét mã QR gọi món tăng vọt, toàn bộ hệ thống (kể cả thu ngân và kế toán) đều bị chậm theo.<br>- **Độ phụ thuộc cao (Tight Coupling)**: Một lỗi cú pháp ở module kho có thể làm sập luôn module bán hàng.<br>- **Khó mở rộng công nghệ**: Bị bó buộc hoàn toàn vào PHP và một máy chủ MySQL duy nhất. |

> 💡 **Tiến Hóa Kiến Trúc**: Để giải quyết triệt để các hạn chế trên, hệ thống đã được thiết kế lại và nâng cấp sang phiên bản **Microservices Architecture (Spring Boot & React)** (xem tài liệu tại thư mục `D:\restaurant-microservices`).
