const fs = require('fs');

// ==============================================================================
// 1. D:/restaurant-microservices/backend/DOCUMENTATION.md
// ==============================================================================
const backendDoc = `# ⚙️ TÀI LIỆU BACKEND: HỆ THỐNG VI DỊCH VỤ NHÀ HÀNG (SPRING BOOT & NODE GATEWAY)

> **Mô hình**: Microservices Architecture (Kiến trúc Vi dịch vụ)  
> **Ngôn ngữ & Nền tảng**: Java 17 · Spring Boot 3.x · Spring Cloud · Node.js 18+ · Express  
> **Giao tiếp**: Đồng bộ (OpenFeign REST) · Bất đồng bộ (RabbitMQ Broker) · Service Discovery (Netflix Eureka) · Spring Cloud Gateway  
> **Cơ sở dữ liệu**: MySQL 8.0 (Mô hình Database-per-Service: 7 Database độc lập trên Docker)  
> **Vị trí thư mục**: \`D:\\restaurant-microservices\\backend\`

---

## 1. Kiến Trúc Tổng Thể & Nguyên Lý Thiết Kế

Hệ thống Backend được thiết kế theo chuẩn **Kiến trúc Hướng dịch vụ (SOA / Microservices)** cấp doanh nghiệp, đảm bảo tính độc lập, khả năng chịu tải phân tán và khả năng mở rộng không giới hạn:

\`\`\`
                                  [ Trình duyệt / POS / Khách quét QR ]
                                                    │
                                                    ▼
                                   ┌─────────────────────────────────┐
                                   │     API GATEWAY (Port 8080)     │
                                   │  - Xác thực JWT & Phân quyền    │
                                   │  - Điều phối Routing / Reverse  │
                                   └────────────────┬────────────────┘
                                                    │
                   ┌────────────────────────────────┼────────────────────────────────┐
                   │                                │                                │
                   ▼                                ▼                                ▼
         ┌───────────────────┐            ┌───────────────────┐            ┌───────────────────┐
         │    Auth Service   │            │   Table Service   │            │   Order Service   │
         │    (Port 8081)    │            │    (Port 8086)    │            │    (Port 8085)    │
         └─────────┬─────────┘            └─────────┬─────────┘            └─────────┬─────────┘
                   │                                │                                │
                   ▼                                ▼                                ▼
              [auth_db]                        [table_db]                       [order_db]
             (MySQL:3307)                     (MySQL:3312)                     (MySQL:3311)
                   │                                │                                │
                   └────────────────────────────────┼────────────────────────────────┘
                                                    │
                                                    ▼ (Asynchronous Event-Driven Messaging)
                                         ┌─────────────────────┐
                                         │   RabbitMQ Broker   │
                                         │ (Events / Exchanges)│
                                         └──────────┬──────────┘
                                                    │
                           ┌────────────────────────┴────────────────────────┐
                           ▼                                                 ▼
                 ┌───────────────────┐                             ┌───────────────────┐
                 │ Inventory Service │                             │  Report Service   │
                 │    (Port 8084)    │                             │    (Port 8087)    │
                 └─────────┬─────────┘                             └─────────┬─────────┘
                           ▼                                                 ▼
                    [inventory_db]                                     [report_db]
                     (MySQL:3310)                                     (MySQL:3313)
\`\`\`

### Các nguyên lý kỹ thuật cốt lõi:
1. **Database-per-Service**: Mỗi microservice sở hữu toàn quyền một database riêng biệt. Tuyệt đối không có service nào được phép truy vấn trực tiếp vào database của service khác.
2. **Event-Driven Architecture (EDA)**: Sử dụng RabbitMQ để xử lý các nghiệp vụ liên dịch vụ mà không gây nghẽn luồng người dùng (ví dụ: Thanh toán đơn xong bắn event -> Kho tự trừ nguyên liệu theo định mức -> Báo cáo tài chính ghi nhận doanh thu).
3. **High Availability Gateway**: Cung cấp Gateway hợp nhất tại Port 8080, định tuyến request và kiểm tra xác thực JWT tập trung.

---

## 2. Danh Mục Các Microservices & Database Schema

| Service | Port | Database | Cổng Docker DB | Nhiệm vụ chính |
|---|---|---|---|---|
| **service-discovery** | 8761 | - | - | Eureka Server quản lý danh bạ service động, tự phát hiện instance |
| **api-gateway** | 8080 | - | - | Định tuyến tập trung, kiểm tra JWT, Rate limiting, CORS |
| **auth-service** | 8081 | \`auth_db\` | 3307 | Đăng nhập, cấp phát JWT Access & Refresh Token, mã hóa mật khẩu |
| **user-service** | 8082 | \`user_db\` | 3308 | Quản lý tài khoản nhân viên, phân quyền vai trò (RBAC) |
| **menu-service** | 8083 | \`menu_db\` | 3309 | Quản lý món ăn, danh mục, công thức định lượng nguyên liệu (BOM) |
| **inventory-service** | 8084 | \`inventory_db\` | 3310 | Quản lý kho, tồn kho khả dụng, phiếu nhập xuất kho |
| **order-service** | 8085 | \`order_db\` | 3311 | Quản lý đơn hàng POS, màn hình bếp KDS, thanh toán, ca làm việc |
| **table-service** | 8086 | \`table_db\` | 3312 | Sơ đồ bàn ăn, trạng thái bàn, sinh mã QR bàn, chuyển bàn |
| **report-service** | 8087 | \`report_db\` | 3313 | Báo cáo doanh thu, chi phí, lợi nhuận gộp, cảnh báo tồn kho |

---

## 3. Danh Mục Chi Tiết Các RESTful API Endpoint

### 3.1. Xác Thực (\`auth-service\` & \`user-service\`)
- \`POST /api/auth/login\`: Đăng nhập, trả về Bearer JWT token và thông tin người dùng.
- \`POST /api/auth/register\`: Đăng ký tài khoản người dùng mới.
- \`GET /api/users\`: Lấy danh sách nhân viên (phân trang, lọc theo trạng thái).
- \`POST /api/users\`: Tạo mới tài khoản nhân viên.
- \`PUT /api/users/:id\`: Cập nhật vai trò, trạng thái hoạt động (ACTIVE/LOCKED).
- \`DELETE /api/users/:id\`: Xóa tài khoản nhân viên.

### 3.2. Thực Đơn & Công Thức BOM (\`menu-service\`)
- \`GET /api/menu\`: Lấy danh sách món ăn kèm giá niêm yết, phân trang và tìm kiếm.
- \`POST /api/menu\`: Thêm món ăn mới vào thực đơn.
- \`PUT /api/menu/:id\`: Sửa thông tin món ăn (tên, giá bán, hình ảnh, trạng thái).
- \`DELETE /api/menu/:id\`: Xóa món ăn khỏi thực đơn.
- \`GET /api/recipes?menuId=:id\`: Lấy công thức định lượng (BOM) chi tiết của món ăn.
- \`POST /api/recipes\`: Lưu định lượng nguyên liệu cho món ăn (lưu trực tiếp vào CSDL MySQL).

### 3.3. Sơ Đồ Bàn & Đặt Món QR (\`table-service\`)
- \`GET /api/tables\`: Lấy toàn bộ danh sách bàn theo khu vực kèm thông tin đơn hàng đang phục vụ trực tiếp.
- \`POST /api/tables\`: Thêm bàn ăn mới vào sơ đồ nhà hàng.
- \`PUT /api/tables/:id/status\`: Đổi trạng thái bàn (\`FREE\`, \`OCCUPIED\`, \`RESERVED\`).
- \`POST /api/tables/:id/transfer\`: **Chuyển bàn nguyên tử**: chuyển toàn bộ đơn hàng từ bàn A sang bàn B, đổi trạng thái bàn tức thời.
- \`POST /api/tables/:id/merge\`: Ghép 2 bàn ăn đi chung đoàn thành một hóa đơn duy nhất.
- \`POST /api/qr/:tableId/generate\`: Kích hoạt và cấp phát mã \`order_token\` mới cho bàn ăn.
- \`DELETE /api/qr/:tableId/clear\`: Hủy hiệu lực mã QR khi khách thanh toán xong.
- \`GET /api/public-order/start?token=:token\`: API công khai cho khách quét QR xem thực đơn tại bàn.
- \`POST /api/public-order/submit\`: Khách gửi đơn đặt món tự phục vụ từ điện thoại.

### 3.4. Bán Hàng & Màn Hình Bếp KDS (\`order-service\`)
- \`GET /api/orders\`: Lấy sổ danh sách đơn hàng (lọc theo trạng thái \`OPEN\`, \`PAID\`, \`CANCEL\`).
- \`POST /api/orders\`: Mở đơn hàng mới tại quầy POS.
- \`POST /api/orders/:id/add-items\`: Gọi thêm món vào đơn hàng đang mở.
- \`PUT /api/orders/:orderId/items/:itemId/status\`: **KDS Endpoint**: Cập nhật trạng thái chế biến của từng món ăn (\`ORDERED\` -> \`COOKING\` -> \`COOKED\` -> \`SERVED\`).
- \`PUT /api/orders/:orderId/items/status\`: **KDS Bulk Endpoint**: Cập nhật hàng loạt toàn bộ vé ("Nấu tất cả", "Nấu xong hết", "Ra món hết").
- \`POST /api/orders/:id/pay\`: Thanh toán đơn hàng, đổi trạng thái bàn về \`FREE\`.
- \`POST /api/orders/:id/cancel\`: Hủy đơn hàng và giải phóng bàn ăn.

### 3.5. Kho & Nguyên Liệu (\`inventory-service\`)
- \`GET /api/ingredients\`: Danh sách nguyên liệu tồn kho, đơn vị tính và giá vốn nhập trung bình.
- \`POST /api/ingredients\`: Thêm nguyên liệu kho mới.
- \`PUT /api/ingredients/:id\`: Cập nhật ngưỡng tồn kho an toàn (min stock).
- \`GET /api/inventory/receipts\`: Danh sách phiếu nhập kho từ nhà cung cấp.
- \`POST /api/inventory/receipts\`: Tạo phiếu nhập kho và tự động cộng dồn số lượng tồn.

### 3.6. Báo Cáo Tài Chính & Phân Tích (\`report-service\`)
- \`GET /api/dashboard\`: Tổng hợp số liệu KPI bán hàng trong ngày (doanh thu, lợi nhuận, đơn hàng, cảnh báo kho).
- \`GET /api/reports/revenue\`: Báo cáo doanh thu, chi phí vận hành và lợi nhuận ròng tổng hợp theo ngày.
- \`GET /api/reports/revenue/:day\`: Drill-down chi tiết danh sách đơn hàng phát sinh trong một ngày cụ thể.
- \`GET /api/reports/stock\`: Báo cáo phân tầng mức độ an toàn của tồn kho (\`CRITICAL\`, \`WARNING\`, \`NORMAL\`).

---

## 4. Hướng Dẫn Khởi Chạy Hệ Thống Backend

### 4.1. Khởi động Cụm Database & Message Broker (Docker)
Chạy lệnh tại thư mục \`D:\\restaurant-microservices\\backend\`:
\`\`\`bash
docker compose up -d
\`\`\`
Kiểm tra đảm bảo 7 container MySQL và RabbitMQ đều ở trạng thái \`healthy\` hoặc \`running\`.

### 4.2. Khởi chạy Cổng API Gateway Hợp Nhất (Node.js High-Performance Bridge)
Gateway hiệu năng cao kết nối đồng thời với 7 database MySQL:
\`\`\`bash
node c:\\xampp\\htdocs\\php-restaurant-main-main\\server.js
\`\`\`
- Cổng phục vụ: \`http://localhost:8080/api\`
- Đảm bảo 100% dữ liệu thực từ MySQL, không sử dụng mock data.
`;

fs.writeFileSync('D:/restaurant-microservices/backend/DOCUMENTATION.md', backendDoc, 'utf8');
console.log('Successfully written D:/restaurant-microservices/backend/DOCUMENTATION.md');

// ==============================================================================
// 2. D:/restaurant-microservices/frontend/DOCUMENTATION.md
// ==============================================================================
const frontendDoc = `# 💻 TÀI LIỆU FRONTEND: ỨNG DỤNG WEB NHÀ HÀNG HIỆN ĐẠI (REACT 18 + TYPESCRIPT)

> **Công nghệ**: React 18 · TypeScript · Vite · Tailwind CSS · TanStack React Query v5 · Zustand · Lucide Icons · Sonner Toast · React Router v6  
> **Kiến trúc**: Single Page Application (SPA) · Component-driven Architecture  
> **Vị trí thư mục**: \`D:\\restaurant-microservices\\frontend\`

---

## 1. Cấu Trúc Mã Nguồn Frontend

\`\`\`
D:\\restaurant-microservices\\frontend\\src\\
├── api/                         # Khai báo các hàm gọi API RESTful (Axios Client)
│   ├── axios-instance.ts            # Cấu hình Axios, Interceptor đính kèm Bearer JWT Token
│   ├── auth.api.ts                  # API đăng nhập, đăng ký
│   ├── menu.api.ts                  # API thực đơn, công thức BOM
│   ├── order.api.ts                 # API bán hàng POS, cập nhật trạng thái bếp KDS
│   ├── table.api.ts                 # API sơ đồ bàn, chuyển bàn, ghép bàn, mã QR
│   ├── ingredient.api.ts            # API nguyên liệu kho
│   ├── inventory.api.ts             # API phiếu nhập xuất kho
│   ├── report.api.ts                # API dashboard và báo cáo tài chính
│   └── shift.api.ts                 # API quản lý ca làm việc
├── components/                  # Các thành phần giao diện tái sử dụng
│   ├── layout/                      # Khung giao diện chính
│   │   ├── app-layout.tsx               # Layout chuẩn có Sidebar & Header
│   │   ├── app-sidebar.tsx              # Sidebar phân tầng 6 nhóm chức năng khoa học
│   │   └── protected-route.tsx          # Kiểm tra JWT Token trước khi cho phép vào trang
│   ├── shared/                      # Component tiện ích dùng chung
│   │   ├── error-boundary.tsx           # Bắt lỗi runtime JavaScript, chống trắng màn hình
│   │   ├── page-header.tsx              # Tiêu đề trang chuẩn
│   │   ├── loading-spinner.tsx          # Hiệu ứng xoay khi tải dữ liệu
│   │   ├── empty-state.tsx              # Hiển thị khi danh sách trống
│   │   └── confirm-dialog.tsx           # Hộp thoại xác nhận thao tác nguy hiểm (Xóa, Hủy)
│   └── ui/                          # Thư viện UI nguyên tử (Buttons, Inputs, Dialogs, Tables)
├── hooks/                       # Custom React Hooks tích hợp TanStack React Query
│   ├── use-auth.ts                  # Hook đăng nhập, đăng xuất
│   ├── use-menu.ts                  # Hook dữ liệu thực đơn, định mức BOM
│   ├── use-kitchen.ts               # Hook điều phối màn hình bếp KDS (lưu realtime vào MySQL)
│   ├── use-tables.ts                # Hook sơ đồ bàn và đơn hàng trực tiếp trên bàn
│   ├── use-orders.ts                # Hook sổ đơn hàng
│   ├── use-ingredients.ts           # Hook danh mục nguyên liệu
│   └── use-reports.ts               # Hook báo cáo doanh thu & tồn kho
├── pages/                       # Toàn bộ màn hình chức năng của hệ thống
│   ├── auth/                        # Trang Đăng nhập (\`/login\`), Đăng ký (\`/register\`)
│   ├── dashboard.tsx                # Bảng điều khiển kinh doanh tổng thể (\`/dashboard\`)
│   ├── order/                       # Bán hàng POS (\`/orders/create\`), Danh sách đơn (\`/orders\`)
│   ├── kitchen/                     # Màn hình Bếp & Quầy Bar KDS (\`/kitchen\`)
│   ├── table/                       # Sơ đồ bàn ăn tương tác thời gian thực (\`/tables\`)
│   ├── menu/                        # Thực đơn & Định Lượng BOM tích hợp (\`/menu\`)
│   ├── ingredient/                  # Danh mục nguyên liệu kho (\`/ingredients\`)
│   ├── inventory/                   # Phiếu nhập xuất kho (\`/inventory/receipts\`)
│   ├── qr/                          # Quản lý mã QR bàn ăn (\`/qr\`)
│   ├── shift/                       # Mở ca, chốt ca, kiểm tiền (\`/shifts\`)
│   ├── expense/                     # Sổ chi phí vận hành (\`/expenses\`)
│   ├── report/                      # Báo cáo doanh thu (\`/reports/revenue\`), Tồn kho (\`/reports/stock\`)
│   ├── user/                        # Quản trị nhân viên & phân quyền (\`/users\`)
│   ├── public/                      # Khách quét QR tự gọi món (\`/public/order\`)
│   └── not-found.tsx                # Trang lỗi 404 cao cấp khi vào sai đường dẫn
├── stores/                      # Quản lý State toàn cục bằng Zustand
│   └── auth-store.ts                # Lưu trữ JWT Token và thông tin User đăng nhập
├── types/                       # Định nghĩa TypeScript Type & Interface nghiêm ngặt
├── App.tsx                      # Cấu hình Routing của toàn bộ ứng dụng
└── main.tsx                     # Điểm khởi chạy React, kích hoạt Future Flags v7 & Error Boundary
\`\`\`

---

## 2. Chi Tiết Các Màn Hình Chức Năng Đột Phá

### 2.1. Sơ Đồ Bàn Ăn Thời Gian Thực (\`/tables\`)
- **Hiển thị trực quan theo khu vực**: Tầng 1, Tầng 2, Sân vườn, Phòng VIP.
- **Thẻ đơn hàng trực tiếp (Live Order Card)**: Bàn có khách hiển thị ngay: Mã đơn hàng (🧾 \`#ORD\`), Tên khách (👤), Tóm tắt các món đang ăn và Số tiền tạm tính.
- **Chuyển bàn / Ghép bàn nguyên tử**: Chuyển giao đơn hàng tức thời sang bàn mới chỉ với 1 click, tự động cập nhật trạng thái bàn nguồn và bàn đích trong database.

### 2.2. Màn Hình Bếp & Quầy Bar KDS (\`/kitchen\`)
- **Hiển thị lệnh gọi món thời gian thực**: Phân loại theo phân khu (Bếp Nóng, Bếp Khai vị & Lẩu, Quầy Bar).
- **Bộ đếm thời gian theo màu**: Xanh (<10 phút), Vàng (10-20 phút), Đỏ nhấp nháy (>20 phút cảnh báo trễ món).
- **Điều phối trạng thái nấu**:
  - Chạm chuyển từng món: *Chờ nấu $\\rightarrow$ Đang nấu $\\rightarrow$ Đã xong $\\rightarrow$ Đã ra món*.
  - Nút tác vụ nhanh: *"Nấu tất cả"*, *"Nấu xong hết"*, *"Ra món hết"*.
  - **Lưu cố định vào MySQL**: Trạng thái được cập nhật trực tiếp vào \`order_db.sale_order_detail\`, bảo lưu 100% khi chuyển trang hoặc tải lại.

### 2.3. Thực Đơn & Công Thức Định Mức BOM (\`/menu\`)
- **Tích hợp 2 trong 1**: Quản lý giá bán món ăn và ma trận Định mức nguyên vật liệu tiêu hao (Food Cost) trên cùng một giao diện.
- **Chỉnh sửa định mức trực tiếp (Inline Editable)**:
  - Bộ điều khiển số lượng \`-\` và \`+\` bước nhảy 0.05.
  - Ô nhập số thập phân trực tiếp.
  - Nút icon ✏️ sửa nhanh qua popup.
- **Tự động tính toán Food Cost**: Tự động nhân đơn giá nhập nguyên liệu x định lượng để hiển thị Giá vốn BOM và tỷ lệ % Food Cost ngay lập tức.
- **Auto-Save vào MySQL**: Tự động lưu vào cơ sở dữ liệu khi thay đổi, ghi nhớ Tab và Món ăn đang chọn khi điều hướng trang.

### 2.4. Bán Hàng Tại Quầy POS (\`/orders/create\`)
- Giao diện cảm ứng chạm nhanh, tìm kiếm món ăn theo mã hoặc tên.
- Tự động kiểm tra đối soát tồn kho nguyên liệu trước khi nhận đơn.
- Hỗ trợ thêm ghi chú khẩu vị cho từng món ăn (ít cay, không đường,...).

### 2.5. Khách Tự Gọi Món Qua Mã QR Bàn (\`/public/order\`)
- Khách dùng điện thoại quét mã QR dán tại bàn để mở thực đơn điện tử.
- Chọn món và gửi lệnh trực tiếp vào hệ thống mà không cần cài đặt ứng dụng.
- Đơn hàng tự động đồng bộ sang màn hình POS của thu ngân và màn hình Bếp KDS.

### 2.6. Khả Năng Phòng Ngừa Lỗi Toàn Diện
- **Error Boundary**: Bọc toàn bộ ứng dụng, hiển thị giao diện báo lỗi thân thiện kèm nút *"Tải lại trang"* và *"Về trang chủ"* khi có ngoại lệ phát sinh.
- **Trang 404 Not Found**: Định tuyến các liên kết sai hoặc trang không tồn tại về trang 404 có các nút điều hướng nhanh.
- **React Router Future Flags**: Bật sẵn \`v7_startTransition\` và \`v7_relativeSplatPath\`, làm sạch hoàn toàn warning trong console.

---

## 3. Hướng Dẫn Khởi Chạy Frontend

1. Di chuyển vào thư mục frontend:
   \`\`\`bash
   cd D:\\restaurant-microservices\\frontend
   \`\`\`
2. Cài đặt các gói phụ thuộc (nếu chưa cài):
   \`\`\`bash
   npm install
   \`\`\`
3. Khởi chạy máy chủ phát triển (Vite Dev Server):
   \`\`\`bash
   npm run dev
   \`\`\`
4. Mở trình duyệt truy cập:
   \`\`\`
   http://localhost:5174/
   \`\`\`
   - **Tài khoản đăng nhập**: \`admin\` / \`admin123\`
`;

fs.writeFileSync('D:/restaurant-microservices/frontend/DOCUMENTATION.md', frontendDoc, 'utf8');
console.log('Successfully written D:/restaurant-microservices/frontend/DOCUMENTATION.md');

// ==============================================================================
// 3. D:/restaurant-microservices/DOCUMENTATION.md (Master Architecture Overview)
// ==============================================================================
const masterDoc = `# 🏆 TÀI LIỆU KIẾN TRÚC TỔNG THỂ: DỰ ÁN HỆ THỐNG QUẢN LÝ NHÀ HÀNG

---

## 1. Bức Tranh Toàn Cảnh Dự Án

Dự án cung cấp một giải pháp chuyển đổi số toàn diện cho chuỗi nhà hàng ẩm thực cao cấp, bao gồm hai giai đoạn tiến hóa kiến trúc phần mềm:

1. **Hệ thống Nguyên Khối Truyền Thống (PHP MVC Monolith)**:
   - Thư mục: \`C:\\xampp\\htdocs\\php-restaurant-main-main\`
   - Phục vụ nghiên cứu, đối chiếu mô hình kiến trúc và phân tích sự cần thiết của việc phân rã dịch vụ.
   - Tài liệu chi tiết: Xem file [\`DOCUMENTATION.md\` trong dự án PHP](\`file:///c:/xampp/htdocs/php-restaurant-main-main/DOCUMENTATION.md\`).

2. **Hệ thống Vi Dịch Vụ Hiện Đại (Cloud-Native Microservices)**:
   - Thư mục: \`D:\\restaurant-microservices\`
   - Ứng dụng các chuẩn thiết kế tiên tiến: Database-per-Service, Event-Driven Architecture qua RabbitMQ, Service Discovery Eureka, API Gateway và Single Page Application (React 18 + TypeScript).
   - Tài liệu chi tiết Backend: Xem file [\`backend/DOCUMENTATION.md\`](\`file:///D:/restaurant-microservices/backend/DOCUMENTATION.md\`).
   - Tài liệu chi tiết Frontend: Xem file [\`frontend/DOCUMENTATION.md\`](\`file:///D:/restaurant-microservices/frontend/DOCUMENTATION.md\`).

---

## 2. Bảng So Sánh Kiến Trúc Chi Tiết

| Tiêu Chí So Sánh | Hệ Thống Cũ (PHP MVC Monolith) | Hệ Thống Mới (Microservices Architecture) |
|---|---|---|
| **Cấu trúc mã nguồn** | 1 Codebase duy nhất đóng gói toàn bộ chức năng | 8 Microservices độc lập, phân chia rõ ràng theo từng Domain nghiệp vụ |
| **Cơ sở dữ liệu** | 1 MySQL Database tập trung (\`restaurant_db\`) | **Database-per-Service**: 7 MySQL Databases độc lập trên Docker (Port 3307 - 3313) |
| **Giao tiếp liên dịch vụ** | Gọi hàm nội bộ PHP, JOIN trực tiếp giữa các bảng | **RESTful API (OpenFeign)** đồng bộ + **RabbitMQ Broker** bất đồng bộ |
| **Giao diện người dùng** | Server-side Rendering (HTML + PHP nạp lại toàn trang) | **Single Page Application (React 18 + Vite + TypeScript)**, trải nghiệm người dùng siêu mượt |
| **Màn hình Bếp (KDS)** | Không có hoặc phải F5 tải lại trang liên tục | **KDS Realtime**: Đầu bếp chạm đổi trạng thái, lưu cố định vào CSDL MySQL tức thời |
| **Đặt món tự phục vụ** | Chưa hỗ trợ gọi món qua QR tại bàn | **QR Table Self-Ordering**: Tự động sinh mã token bàn, khách quét QR đặt món trên điện thoại |
| **Quản lý Định mức (BOM)**| Nhập thủ công, không phân tích Food Cost | **Tự động tính toán Food Cost %**, sửa định mức trực tiếp (Inline Editing), auto-save MySQL |
| **Tính sẵn sàng & Chịu tải**| Khi lượng truy cập tăng vọt, toàn bộ hệ thống bị nghẽn | Độc lập chịu tải: POS và Bếp vẫn hoạt động trơn tru dù khách quét QR gọi món ồ ạt |

---

## 3. Bản Đồ Điều Hướng Tài Liệu

- 📘 **Tài liệu Chi tiết Phân Hệ PHP Monolith**: \`C:\\xampp\\htdocs\\php-restaurant-main-main\\DOCUMENTATION.md\`
- ⚙️ **Tài liệu Chi tiết Phân Hệ Backend Microservices**: \`D:\\restaurant-microservices\\backend\\DOCUMENTATION.md\`
- 💻 **Tài liệu Chi tiết Phân Hệ Frontend Web App**: \`D:\\restaurant-microservices\\frontend\\DOCUMENTATION.md\`
- 🌐 **Tài liệu Kiến trúc Tổng Thể**: \`D:\\restaurant-microservices\\DOCUMENTATION.md\`
`;

fs.writeFileSync('D:/restaurant-microservices/DOCUMENTATION.md', masterDoc, 'utf8');
console.log('Successfully written D:/restaurant-microservices/DOCUMENTATION.md');
console.log('ALL PROJECT DOCUMENTATION GENERATED SUCCESSFULLY!');
