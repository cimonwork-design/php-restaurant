# CHƯƠNG 2: THIẾT KẾ KIẾN TRÚC
> **Tài liệu Báo cáo & Phân tích Chuyên sâu Kiến trúc Phần mềm**  
> **Dự án:** Hệ thống Quản lý Nhà hàng & Gọi món Trực tuyến (*Restaurant Management System*)  
> **Công nghệ:** PHP MVC, MySQL (PDO), Bootstrap 5, JWT Authentication

---

## MỤC LỤC
1. [Phần 1: Thể loại kiến trúc (Architectural Genre)](#phần-1-thể-loại-kiến-trúc-architectural-genre)
   - [1.1. Khái niệm và Định nghĩa Bản chất](#11-khái-niệm-và-định-nghĩa-bản-chất)
   - [1.2. Tiêu chí Phân loại Thể loại Kiến trúc](#12-tiêu-chí-phân-loại-thể-loại-kiến-trúc)
   - [1.3. Các Thể loại Kiến trúc Phổ biến trong Công nghệ Phần mềm](#13-các-thể-loại-kiến-trúc-phổ-biến-trong-công-nghệ-phần-mềm)
   - [1.4. Vai trò của Thể loại Kiến trúc trong Vòng đời Phần mềm](#14-vai-trò-của-thể-loại-kiến-trúc-trong-vòng-đời-phần-mềm)
2. [Phần 2: Phong cách kiến trúc (Architectural Style)](#phần-2-phong-cách-kiến-trúc-architectural-style)
   - [2.1. Khái niệm và Ba Thành tố Cốt lõi](#21-khái-niệm-và-ba-thành-tố-cốt-lõi)
   - [2.2. Các Phong cách Kiến trúc Kinh điển](#22-các-phong-cách-kiến-trúc-kinh-điển)
   - [2.3. Ý nghĩa đối với Thuộc tính Chất lượng Phần mềm (Quality Attributes)](#23-ý-nghĩa-đối-với-thuộc-tính-chất-lượng-phần-mềm-quality-attributes)
3. [Phần 3: So sánh Đối chiếu "Thể loại kiến trúc" <=> "Phong cách kiến trúc"](#phần-3-so-sánh-đối-chiếu-thể-loại-kiến-trúc--phong-cách-kiến-trúc)
   - [3.1. Bảng So sánh Đa chiều Chi tiết](#31-bảng-so-sánh-đa-chiều-chi-tiết)
   - [3.2. Mối quan hệ Tương hỗ (Bối cảnh bài toán <=> Lời giải kỹ thuật)](#32-mối-quan-hệ-tương-hỗ-bối-cảnh-bài-toán--lời-giải-kỹ-thuật)
   - [3.3. Các Sai lầm Thường gặp khi Phân biệt](#33-các-sai-lầm-thường-gặp-khi-phân-biệt)
4. [Phần 4: Xác định Thể loại & Phong cách Kiến trúc của Dự án Thực tế](#phần-4-xác-định-thể-loại--phong-cách-kiến-trúc-của-dự-án-thực-tế)
   - [4.1. Xác định Thể loại Kiến trúc của Dự án](#41-xác-định-thể-loại-kiến-trúc-của-dự-án)
   - [4.2. Xác định Phong cách Kiến trúc của Dự án](#42-xác-định-phong-cách-kiến-trúc-của-dự-án)
   - [4.3. Minh chứng Trực tiếp từ Cấu trúc Mã nguồn & Cơ sở Dữ liệu](#43-minh-chứng-trực-tiếp-từ-cấu-trúc-mã-nguồn--cơ-sở-dữ-liệu)
   - [4.4. Đánh giá Ưu điểm, Nhược điểm và Tính Phù hợp](#44-đánh-giá-ưu-điểm-nhược-điểm-và-tính-phù-hợp)
5. [Phần 5: Kịch bản và Hướng dẫn Thiết kế Slide Báo cáo Thuyết trình](#phần-5-kịch-bản-và-hướng-dẫn-thiết-kế-slide-báo-cáo-thuyết-trình)
   - [Cấu trúc 10 Slide Chuẩn hóa (Slide Outline, Bullet Points, Diagram & Speaker Notes)](#cấu-trúc-10-slide-chuẩn-hóa)

---

# PHẦN 1: THỂ LOẠI KIẾN TRÚC (ARCHITECTURAL GENRE)

## 1.1. Khái niệm và Định nghĩa Bản chất
- **Thể loại kiến trúc (Architectural Genre / Application Category)** là thuật ngữ dùng để phân loại hệ thống phần mềm dựa trên **miền ứng dụng (application domain)**, **mục đích nghiệp vụ**, bối cảnh môi trường hoạt động và đối tượng người dùng cuối mà phần mềm hướng tới phục vụ.
- Trong kỹ thuật phần mềm (theo Roger S. Pressman & Ian Sommerville), thể loại kiến trúc trả lời cho câu hỏi trung tâm:
  > **"Hệ thống phần mềm này thuộc loại hình ứng dụng nào trong thế giới thực, giải quyết bài toán nghiệp vụ gì và phục vụ trong bối cảnh nào?"**
- Thể loại kiến trúc không bị trói buộc bởi ngôn ngữ lập trình hay framework cụ thể, mà xuất phát từ **miền bài toán (Problem Space)**. Khi một phần mềm được định danh thuộc một thể loại nhất định, nó lập tức kế thừa toàn bộ các đặc trưng nghiệp vụ, quy tắc hoạt động, luồng thông tin và các kỳ vọng chất lượng tiêu biểu của miền đó.

## 1.2. Tiêu chí Phân loại Thể loại Kiến trúc
Việc phân chia thể loại kiến trúc thường dựa trên các tiêu chí cốt lõi:
1. **Mục đích nghiệp vụ (Business Function):** Hệ thống được xây dựng để xử lý tính toán khoa học, vận hành kinh doanh, điều khiển thiết bị hay giải trí?
2. **Loại hình dữ liệu và cách xử lý (Data Nature & Processing):** Dữ liệu dạng giao dịch có cấu trúc (OLTP), dữ liệu đa phương tiện phân tán, hay dữ liệu cảm biến thời gian thực?
3. **Môi trường vận hành và giao diện tương tác (Environment & Interaction):** Người dùng thao tác qua trình duyệt web, ứng dụng di động, giao diện dòng lệnh hay hệ thống chạy ngầm không có UI?
4. **Mô hình người dùng (User Demographics):** B2B (Doanh nghiệp với doanh nghiệp), B2C (Doanh nghiệp với khách hàng), G2C (Chính phủ với công dân), hay hệ thống nội bộ doanh nghiệp.

## 1.3. Các Thể loại Kiến trúc Phổ biến trong Công nghệ Phần mềm

```
                                  CÁC THỂ LOẠI KIẾN TRÚC PHẦN MỀM
  ┌───────────────────────────────────────────────┴──────────────────────────────────────────────┐
  │                                                                                              │
  ▼                                               ▼                                              ▼
Hệ thống Thông tin Quản lý           Ứng dụng Nền tảng Web / O2O                      Hệ thống Nhúng & IoT
(MIS / ERP / CRM / SCM)              (Web Applications / Portals)                     (Embedded & Real-time)
  │                                               │                                              │
  ├─ Quản lý giao dịch ACID                       ├─ Truy cập qua HTTP/HTTPS                     ├─ Ràng buộc phần cứng, bộ nhớ
  ├─ Quản lý tồn kho, bán hàng, nhân sự           ├─ Đa thiết bị, không cần cài đặt              ├─ Độ trễ phản hồi tính bằng ms
  └─ Báo cáo thống kê quản trị                    └─ Quét QR, tương tác tức thời                 └─ Ô tô, y tế, thiết bị gia dụng
```

1. **Hệ thống thông tin quản lý (Management Information Systems - MIS / Enterprise Systems / ERP):**
   - *Bản chất:* Tập trung vào việc lưu trữ, xử lý, kiểm soát và báo cáo các luồng dữ liệu nghiệp vụ của một tổ chức.
   - *Đặc điểm cốt lõi:* Tính toàn vẹn dữ liệu cao (ACID), phân quyền người dùng nhiều cấp bậc (RBAC), quy trình xử lý giao dịch thương mại (nhập kho, xuất kho, bán hàng, kiểm kê, kế toán, hóa đơn).
   - *Ví dụ:* Hệ thống quản lý nhà hàng, hệ thống quản lý bệnh viện (HIS), hệ thống quản trị nguồn lực doanh nghiệp (SAP, Odoo).
2. **Hệ thống Ứng dụng Web (Web-based Applications):**
   - *Bản chất:* Phần mềm chạy trên môi trường máy chủ web, người dùng tương tác thông qua trình duyệt mà không cần cài đặt phần mềm chuyên dụng tại máy trạm.
   - *Đặc điểm cốt lõi:* Truy cập diện rộng qua Internet/Intranet, tương thích đa nền tảng (Responsive Design), giao tiếp qua giao thức chuẩn HTTP/HTTPS/WebSocket.
   - *Ví dụ:* Cổng thông tin, website thương mại điện tử, ứng dụng web đặt bàn gọi món online.
3. **Hệ thống Nhúng và Thời gian thực (Embedded & Real-time Systems):**
   - *Bản chất:* Phần mềm được tích hợp trực tiếp vào phần cứng chuyên dụng, đòi hỏi phản hồi chính xác trong một khoảng thời gian giới hạn nghiêm ngặt.
   - *Ví dụ:* Hệ thống phanh ABS ô tô, hệ thống điều khiển lò vi sóng, thiết bị theo dõi nhịp tim y tế.
4. **Hệ thống Xử lý Dữ liệu lớn và Trí tuệ Nhân tạo (Data-Intensive & AI Systems):**
   - *Bản chất:* Thu thập, phân tích khối lượng dữ liệu khổng lồ (Volume, Velocity, Variety) hoặc thực hiện các thuật toán máy học/deep learning.
   - *Ví dụ:* Hệ thống gợi ý sản phẩm (Recommendation engine), công cụ tìm kiếm, xử lý ngôn ngữ tự nhiên.
5. **Hệ thống Phần mềm Tiện ích & Hệ thống (System Software / Utilities):**
   - *Bản chất:* Cung cấp nền tảng và dịch vụ cho các phần mềm khác vận hành.
   - *Ví dụ:* Hệ điều hành (Linux, Windows), Trình biên dịch (GCC), Hệ quản trị CSDL (MySQL, PostgreSQL).

## 1.4. Vai trò của Thể loại Kiến trúc trong Vòng đời Phần mềm
- **Định hướng yêu cầu phi chức năng:** Khi biết phần mềm thuộc thể loại MIS, kỹ sư phần mềm lập tức biết các yêu cầu quan trọng nhất là tính nhất quán dữ liệu, bảo mật phân quyền và khả năng truy vết nhật ký kiểm toán (Audit Trail).
- **Rút ngắn thời gian phân tích:** Cho phép đội ngũ phát triển áp dụng các quy trình chuẩn đã được kiểm chứng (best practices) của miền nghiệp vụ đó vào bài toán cụ thể.
- **Tiền đề chọn lựa giải pháp:** Thể loại kiến trúc đóng vai trò là "đầu vào" để kiến trúc sư lựa chọn phong cách kiến trúc phù hợp ở bước tiếp theo.

---

# PHẦN 2: PHONG CÁCH KIẾN TRÚC (ARCHITECTURAL STYLE)

## 2.1. Khái niệm và Ba Thành tố Cốt lõi
- **Phong cách kiến trúc (Architectural Style / Pattern)** là một tập hợp các nguyên tắc thiết kế, cấu trúc tổ chức và các quy ước kỹ thuật đã được chuẩn hóa, xác định cách thức các module mã nguồn được phân chia, kết nối và tương tác với nhau.
- Theo định nghĩa kinh điển của Mary Shaw và David Garlan:
  > **"Một phong cách kiến trúc xác định một họ các hệ thống về mặt cấu trúc thành phần, kết nối và các quy tắc ràng buộc chi phối việc phối hợp giữa chúng."**
- Phong cách kiến trúc trả lời cho câu hỏi:
  > **"Hệ thống được tổ chức cấu trúc mã nguồn bên trong như thế nào, chia thành những tầng/khối nào, và các khối giao tiếp với nhau bằng cơ chế gì?"**

### Ba thành tố cấu thành phong cách kiến trúc:
1. **Các thành phần tính toán (Components):** Các khối module thực thi, đối tượng xử lý, tầng logic (ví dụ: Controller, View, Model, Service, Database Server, Web Client).
2. **Cơ chế kết nối (Connectors):** Phương thức truyền thông tin và điều khiển giữa các thành phần (ví dụ: Lời gọi hàm nội bộ - Procedure Calls, HTTP/REST Requests, Truy vấn SQL qua PDO, Message Queue).
3. **Các ràng buộc cấu trúc (Constraints):** Các quy tắc bắt buộc chi phối kiến trúc (ví dụ: Tầng View không được trực tiếp can thiệp dữ liệu MySQL; Controller tiếp nhận request và điều hướng; Model chỉ làm việc với logic dữ liệu).

## 2.2. Các Phong cách Kiến trúc Kinh điển

### A. Phong cách Model - View - Controller (MVC)
- **Cơ chế hoạt động:** Tách biệt ứng dụng thành 3 thành phần độc lập:
  - **Model:** Đại diện cho cấu trúc dữ liệu, tương tác trực tiếp với cơ sở dữ liệu và hiện thực các quy tắc nghiệp vụ cốt lõi.
  - **View:** Giao diện hiển thị trực quan cho người dùng, định dạng dữ liệu nhận từ Controller thành HTML/CSS/JS.
  - **Controller:** Đóng vai trò cầu nối điều khiển, tiếp nhận yêu cầu từ người dùng (HTTP request), gọi Model để lấy/cập nhật dữ liệu, sau đó lựa chọn View tương ứng để phản hồi.
- **Ưu điểm:** Tách biệt rõ ràng mối quan tâm (Separation of Concerns), giúp lập trình viên frontend và backend có thể làm việc song song, mã nguồn dễ bảo trì và mở rộng.

### B. Phong cách Phân tầng (Layered / N-Tier Architecture)
- **Cơ chế hoạt động:** Tổ chức hệ thống thành các lớp xếp chồng lên nhau theo thứ bậc. Mỗi tầng cung cấp dịch vụ cho tầng ngay trên nó và chỉ sử dụng dịch vụ của tầng ngay dưới nó.
  - *Tầng Trình diễn (Presentation Layer):* Giao diện UI, tiếp nhận input.
  - *Tầng Nghiệp vụ (Business / Application Layer):* Xử lý luồng logic nghiệp vụ, tính toán, phân quyền.
  - *Tầng Truy xuất Dữ liệu (Data Access / Persistence Layer):* Đóng gói các câu lệnh SQL, kết nối DB.
  - *Tầng Cơ sở Dữ liệu (Database Layer):* RDBMS lưu trữ dữ liệu bền vững.

### C. Phong cách Khách - Chủ (Client - Server Architecture)
- Phân chia hệ thống thành hai thực thể logic:
  - **Client (Bên yêu cầu dịch vụ):** Gửi yêu cầu qua mạng (trình duyệt web, ứng dụng máy trạm).
  - **Server (Bên cung cấp dịch vụ):** Lắng nghe, xử lý logic và phản hồi tài nguyên cho Client.

### D. Phong cách Đơn khối (Monolithic Architecture)
- Toàn bộ các chức năng, giao diện người dùng, logic xử lý và kết nối cơ sở dữ liệu được đóng gói, biên dịch và triển khai chung trong **một khối ứng dụng duy nhất (Single Deployment Unit)**.
- *Ưu điểm:* Dễ phát triển, dễ kiểm thử toàn diện, dễ triển khai lên một máy chủ web đơn lẻ (như Apache/PHP trên XAMPP), chi phí hạ tầng ban đầu thấp.

### E. Phong cách Vi dịch vụ (Microservices Architecture)
- Chia tách hệ thống thành tập hợp các dịch vụ nhỏ, hoạt động độc lập, mỗi dịch vụ sở hữu cơ sở dữ liệu riêng và giao tiếp qua API (REST, gRPC, RabbitMQ).
- *Ưu điểm:* Độc lập mở rộng quy mô, nhưng độ phức tạp về quản trị và hạ tầng mạng rất cao.

## 2.3. Ý nghĩa đối với Thuộc tính Chất lượng Phần mềm (Quality Attributes)
Phong cách kiến trúc được lựa chọn không phải ngẫu nhiên mà để tối ưu hóa các thuộc tính phi chức năng quan trọng:
- **Tính dễ bảo trì (Maintainability):** Nhờ phân chia module rõ ràng (như trong MVC), khi giao diện thay đổi, mã nguồn xử lý cơ sở dữ liệu không bị ảnh hưởng.
- **Tính bảo mật (Security):** Tách biệt các tầng giúp thiết lập các chốt kiểm soát bảo mật tập trung (Middleware, JWT Filter, Prepared Statements chống SQL Injection).
- **Tính tái sử dụng (Reusability):** Các Base Model, Base Controller, Helper có thể dùng lại cho hàng chục tính năng khác nhau.
- **Khả năng kiểm thử (Testability):** Dễ dàng viết test case tự động (như Playwright, PHPUnit) trên từng tầng chức năng riêng biệt.

---

# PHẦN 3: SO SÁNH ĐỐI CHIẾU "THỂ LOẠI KIẾN TRÚC" <=> "PHONG CÁCH KIẾN TRÚC"

## 3.1. Bảng So sánh Đa chiều Chi tiết

| Tiêu chí So sánh | Thể loại Kiến trúc (Architectural Genre) | Phong cách Kiến trúc (Architectural Style) |
| :--- | :--- | :--- |
| **Bản chất cốt lõi** | Phân loại theo **miền ứng dụng & bài toán nghiệp vụ** của thế giới thực. | Phân loại theo **cấu trúc tổ chức mã nguồn, thành phần kỹ thuật & cách kết nối**. |
| **Câu hỏi trọng tâm** | *"Hệ thống này phục vụ cho việc gì, lĩnh vực nào?"* *(WHAT does it do? WHERE is it applied?)* | *"Hệ thống được tổ chức và liên kết bên trong ra sao?"* *(HOW is it structured? HOW do parts interact?)* |
| **Không gian tư duy** | **Không gian Bài toán (Problem Space)** | **Không gian Giải pháp (Solution Space)** |
| **Tiêu chí phân loại** | Nghiệp vụ kinh doanh, đối tượng người dùng, tính chất công việc (MIS, Web App, Nhúng, AI, Game). | Cấu trúc thành phần, dạng liên kết, ràng buộc luồng dữ liệu (MVC, Layered, Client-Server, Monolith, Microservices). |
| **Tính phụ thuộc công nghệ** | **Độc lập hoàn toàn** với công nghệ lập trình và giải pháp kỹ thuật. | **Gắn liền trực tiếp** với cách viết code, tổ chức thư mục, framework và kiến trúc runtime. |
| **Mức độ trừu tượng** | Trừu tượng mức cao (High-level Domain Concept), người dùng và khách hàng đều hiểu được. | Trừu tượng mức kỹ thuật (Technical Architectural Concept), dành riêng cho kỹ sư phần mềm. |
| **Mối quan hệ định lượng** | Một thể loại kiến trúc có thể được hiện thực hóa bằng **nhiều phong cách kiến trúc khác nhau**. | Một phong cách kiến trúc có thể được áp dụng vào **nhiều thể loại kiến trúc khác nhau**. |
| **Tính biến đổi theo thời gian** | Ổn định xuyên suốt vòng đời (Hệ thống nhà hàng thì bản chất vẫn là quản lý nhà hàng). | Có thể tái cấu trúc, chuyển dịch theo thời gian (Từ Monolith chuyển sang Microservices khi người dùng tăng vọt). |
| **Ví dụ điển hình** | Quản lý nhà hàng, Bán hàng thương mại điện tử, Điều khiển ô tô, Ngân hàng trực tuyến. | MVC, Phân tầng 3 lớp (3-Tier), Hướng sự kiện (EDA), Vi dịch vụ (Microservices), Khách-Chủ. |

## 3.2. Mối quan hệ Tương hỗ (Bối cảnh bài toán <=> Lời giải kỹ thuật)
Hai khái niệm này không hề đối lập hay triệt tiêu nhau, mà tạo thành mối quan hệ **Bối cảnh (Context) & Giải pháp (Solution)** mang tính trực giao (Orthogonal):

```
       THỂ LOẠI KIẾN TRÚC (Problem Space)
   [Hệ thống Quản lý Nhà hàng - MIS & Web App]
                        │
                        │ Định hình yêu cầu:
                        │  - Phải quản lý CRUD: Bàn, Menu, Kho, Hóa đơn
                        │  - Phải phân quyền: Admin, Quản lý, Nhân viên
                        │  - Phải có giao diện Web & Gọi món tại bàn
                        │  - Cần triển khai nhanh, chi phí thấp cho SME
                        ▼
       PHONG CÁCH KIẾN TRÚC (Solution Space)
   [Client-Server + MVC + 3-Tier Layered + Monolith]
                        │
                        │ Hiện thực hóa bằng kỹ thuật:
                        │  - Client (Bootstrap 5, JS) <-> Server (PHP Core MVC)
                        │  - Phân tách App/Core/Helpers/Views
                        │  - Đóng gói chung trong 1 repo PHP/XAMPP
```

- **Thể loại định hình Phong cách:** Khi biết hệ thống là "Ứng dụng Quản lý Nhà hàng quy mô vừa và nhỏ", kiến trúc sư nhận diện ngay đây là ứng dụng thiên về giao dịch dữ liệu (Data-centric, CRUD), cần tính toàn vẹn cao. Do đó, phong cách **MVC kết hợp Phân tầng (Layered) trên nền tảng Khách-Chủ (Client-Server)** là giải pháp tối ưu hàng đầu.
- **Phong cách hiện thực hóa Thể loại:** Phong cách MVC giúp tổ chức mã nguồn cho bài toán quản lý nhà hàng một cách khoa học: bảng `sale_order` hay `inventory_receipt` sẽ có Model tương ứng chịu trách nhiệm lưu trữ, Controller kiểm tra quyền và luồng nghiệp vụ, View hiển thị giao diện bán hàng cho nhân viên thu ngân.

## 3.3. Các Sai lầm Thường gặp khi Phân biệt
1. **Nhầm lẫn giữa "Ứng dụng Web" và "Kiến trúc Web":** Nhiều người coi "Web" là phong cách kiến trúc. Thực tế, Web là một **thể loại nền tảng ứng dụng (Web Application Genre)**, còn kiến trúc kỹ thuật của nó là phong cách **Khách - Chủ (Client-Server)** thông qua giao thức HTTP.
2. **Đồng nhất MVC là Thể loại phần mềm:** MVC là một **phong cách / mẫu kiến trúc (Architectural Pattern/Style)**, không phải là thể loại phần mềm. Ta không thể nói "Phần mềm này thuộc thể loại MVC", mà phải nói "Phần mềm này thuộc thể loại Hệ thống thông tin quản lý, được thiết kế theo phong cách MVC".

---

# PHẦN 4: XÁC ĐỊNH THỂ LOẠI & PHONG CÁCH KIẾN TRÚC CỦA DỰ ÁN THỰC TẾ
*(Dựa trên phân tích toàn diện mã nguồn dự án Restaurant Management System hiện tại)*

## 4.1. Xác định Thể loại Kiến trúc của Dự án

Dự án hiện tại của nhóm thuộc thể loại kép:
> **1. Thể loại chính:** **Hệ thống Thông tin Quản lý (Management Information System - MIS / Mini-ERP chuyên ngành F&B - Food & Beverage).**  
> **2. Thể loại nền tảng & tương tác:** **Ứng dụng Web hướng dịch vụ khách hàng (Web Application / O2O - Online to Offline Platform).**

### Luận cứ chứng minh từ thực tế hệ thống:
1. **Đặc trưng MIS / Mini-ERP trong ngành F&B:**
   Hệ thống bao quát toàn bộ chu trình nghiệp vụ khép kín của một nhà hàng ẩm thực:
   - **Quản lý Thực đơn & Định lượng chế biến:** Quản lý món ăn (`menu_item`), liên kết định lượng nguyên liệu cấu thành món ăn qua công thức (`recipe`).
   - **Quản lý Chuỗi cung ứng & Kho hàng:** Quy trình nhập kho từ nhà cung cấp (`inventory_receipt`), xuất kho chế biến (`inventory_issue`), kiểm kê và điều chỉnh sai lệch tồn kho (`stock_adjustment`), ghi nhận tự động nhật ký thẻ kho (`inventory_log`).
   - **Vận hành Bán hàng & Dịch vụ bàn ăn:** Quản lý sơ đồ bàn (`restaurant_table`), trạng thái bàn (trống/có khách), đặt bàn trước (`reservation`), tạo đơn hàng tại bàn và thanh toán hóa đơn (`sale_order`).
   - **Quản trị Tài chính & Chi phí:** Quản lý dòng tiền chi phí phát sinh (`expense`), báo cáo tổng hợp doanh thu - lợi nhuận - tồn kho (`report`).
   - **Quản trị Người dùng & Phân quyền đa cấp bậc (RBAC):** Phân chia rõ 3 vai trò tác vụ `admin`, `manager`, `user` (thu ngân, phục vụ) bảo vệ tính toàn vẹn dữ liệu (`users`).
   - **Kiểm soát rủi ro & Truy vết hệ thống:** Ghi nhận nhật ký kiểm toán hệ thống (`audit_log`) mỗi khi có thao tác nhạy cảm.

2. **Đặc trưng Ứng dụng Web O2O (Online to Offline):**
   - Tương tác thông qua nền tảng Web trên trình duyệt máy tính và thiết bị di động.
   - Hỗ trợ module **Gọi món tự phục vụ qua mã QR tại bàn (Self-ordering / QR Order)** thông qua `PublicOrderController.php` và `QrController.php`. Khách hàng dùng điện thoại quét mã QR để mở trang web đặt món trực tiếp vào bếp mà không cần cài đặt ứng dụng native.

---

## 4.2. Xác định Phong cách Kiến trúc của Dự án

Dự án hiện tại được thiết kế và hiện thực hóa thông qua sự kết hợp của 3 phong cách kiến trúc chuẩn mực:

```
                           SƠ ĐỒ KIẾN TRÚC HỆ THỐNG DỰ ÁN
  ┌────────────────────────────────────────────────────────────────────────────────────────┐
  │ 1. CLIENT TIER (Trình duyệt Web người dùng)                                            │
  │    - Giao diện người dùng: HTML5, CSS3, Bootstrap 5                                    │
  │    - Hành vi tương tác: JavaScript (DOM, Fetch API, Modal tương tác, QR Scanner)       │
  └───────────────────────────────────────────▲────────────────────────────────────────────┘
                                              │ HTTP Request / Response (JSON, HTML)
  ┌───────────────────────────────────────────▼────────────────────────────────────────────┐
  │ 2. APPLICATION SERVER TIER (Web Server Apache / PHP Runtime) - KIẾN TRÚC ĐƠN KHỐI      │
  │                                                                                        │
  │   [Entry Point & Router]: .htaccess -> index.php -> core/App.php (Front Controller)    │
  │                                           │                                            │
  │                                           ▼                                            │
  │   [Controller Layer]: app/controllers/*.php (Kế thừa core/Controller.php)              │
  │    - AuthController, InventoryReceiptController, SaleOrderController, ReportController │
  │    - Bảo vệ bảo mật: JWT::getCurrentUser(), requireRoles(['admin', 'manager'])         │
  │                                           │                                            │
  │                     ┌─────────────────────┴─────────────────────┐                      │
  │                     ▼                                           ▼                      │
  │     [Presentation / View Layer]                 [Model / Persistence Layer]            │
  │     app/views/**/*.php                          app/models/*.php (Kế thừa core/Model)  │
  │     - auth/, inventory_receipt/,                - User, Recipe, InventoryReceipt,      │
  │       sale_order/, dashboard/, etc.               SaleOrder, MenuItem, etc.            │
  └─────────────────────────────────────────────────────────▲──────────────────────────────┘
                                                            │ PDO Prepared Statements
  ┌─────────────────────────────────────────────────────────▼──────────────────────────────┐
  │ 3. DATABASE TIER (Hệ Quản trị CSDL Quan hệ)                                            │
  │    - MySQL Server (restaurant_db)                                                      │
  │    - 14 bảng quan hệ với Foreign Keys, Indexes, ràng buộc toàn vẹn dữ liệu             │
  └────────────────────────────────────────────────────────────────────────────────────────┘
```

### 1. Phong cách Khách - Chủ (Client - Server Architecture)
- **Phía Client:** Trình duyệt Web (Desktop của quản lý/thu ngân; Smartphone của khách hàng quét QR). Client chịu trách nhiệm hiển thị giao diện (Rendering), thu thập dữ liệu nhập từ biểu mẫu và gửi HTTP Request (GET, POST).
- **Phía Server:** Máy chủ Web Apache tiếp nhận request, phân tích qua PHP Engine để xử lý nghiệp vụ, giao tiếp với MySQL và phản hồi lại cho Client dưới dạng mã HTML hoặc chuỗi dữ liệu JSON (đối với các API Ajax/Fetch).

### 2. Phong cách Đơn khối (Monolithic Architecture)
- Toàn bộ source code của dự án được đóng gói thống nhất trong một cấu trúc thư mục duy nhất (`php-restaurant-main-main`).
- Mọi phân hệ chức năng (Xác thực, Kho hàng, Bán hàng, Menu, Báo cáo) đều chạy chung một tiến trình runtime PHP, sử dụng chung cấu hình kết nối (`config/database.php`, `config/jwt.php`) và chia sẻ cùng một cơ sở dữ liệu `restaurant_db`.
- Không sử dụng kiến trúc Microservices phân tán hay kiến trúc Message Broker phức tạp, giúp việc triển khai (Deployment) và chạy thử nghiệm cục bộ trên XAMPP diễn ra nhanh chóng, đơn giản.

### 3. Mô hình Model - View - Controller (MVC) kết hợp Phân tầng (Layered 3-Tier)
Hệ thống phân định ranh giới trách nhiệm cực kỳ rõ ràng giữa các tầng:

- **Bộ định tuyến trung tâm (Front Controller & Router):**
  - File `.htaccess` điều hướng mọi request về `index.php`.
  - `core/App.php` phân tích URL theo định dạng `/{controller}/{method}/{params}` để khởi tạo Controller tương ứng và gọi action thích hợp.
- **Tầng Trình diễn (Presentation Layer - Views):**
  - Đặt tại thư mục `app/views/`. Chứa các tệp view PHP kết hợp template HTML, thư viện giao diện Bootstrap 5, và các script JavaScript (`public/js/`).
  - Tầng này chỉ làm nhiệm vụ render giao diện người dùng, hoàn toàn không chứa câu lệnh truy vấn SQL trực tiếp.
- **Tầng Điều khiển & Ứng dụng (Application / Controller Layer):**
  - Đặt tại thư mục `app/controllers/`, tất cả kế thừa từ `core/Controller.php`.
  - Đảm nhiệm: Kiểm tra phiên đăng nhập qua JWT (`helpers/JWT.php`), phân quyền truy cập theo vai trò (`requireRoles()`), xác thực dữ liệu đầu vào (Validation), kích hoạt transaction và gọi Model xử lý.
- **Tầng Truy xuất Dữ liệu & Nghiệp vụ (Data Access / Model Layer):**
  - Đặt tại thư mục `app/models/`, tất cả kế thừa từ `core/Model.php`.
  - Sử dụng đối tượng **PDO (PHP Data Objects)** kết hợp **Prepared Statements** (`$stmt->prepare()`, `$stmt->execute()`) để thực hiện các thao tác CRUD an toàn, chống triệt để lỗ hổng SQL Injection.

---

## 4.3. Minh chứng Trực tiếp từ Cấu trúc Mã nguồn & Cơ sở Dữ liệu

| Thành phần Kiến trúc | Đường dẫn Tệp trong Dự án | Đoạn Mã / Minh chứng Kỹ thuật Thực tế |
| :--- | :--- | :--- |
| **Front Controller & Router** | `core/App.php` | Phân tích URL: `$url = $this->parseUrl();` biến đổi `sale_order` thành `SaleOrderController`, gọi method và truyền `$params`. |
| **Base Controller & Phân quyền** | `core/Controller.php` | Phương thức `requireRoles($roles)`: kiểm tra JWT từ Header/Cookie; chặn truy cập trái phép bằng redirect hoặc flash error. |
| **Business Logic Controller** | `app/controllers/InventoryReceiptController.php` | Điều phối nghiệp vụ tạo phiếu nhập, tính tổng tiền, bắt lỗi validation nguyên liệu và ghi dữ liệu qua Model. |
| **Base Model & PDO Wrapper** | `core/Model.php` | Quản lý kết nối DB qua `$this->db = getDB();`, đóng gói hàm `find()`, `where()`, `insert()`, `update()`, `delete()` sử dụng Prepared Statements. |
| **Entity Model** | `app/models/InventoryReceipt.php`, `app/models/SaleOrder.php` | Chứa logic chuyên biệt của từng thực thể, định nghĩa quan hệ bảng và tính toán tổng số lượng/đơn giá. |
| **Presentation (View)** | `app/views/inventory_receipt/create.php` | Form giao diện Bootstrap 5 nhập liệu phiếu nhập kho, bảng chọn nguyên liệu động bằng JS. |
| **Bảo mật Xác thực (JWT)** | `helpers/JWT.php` | Mã hóa và giải mã token JWT stateless phục vụ xác thực người dùng mà không phụ thuộc vào lưu trữ session server truyền thống. |
| **Cơ sở Dữ liệu Quan hệ** | `database/schema.sql` | 14 bảng dữ liệu chuẩn hóa với khóa ngoại: `users`, `ingredient`, `menu_item`, `recipe`, `inventory_receipt`, `sale_order`, `audit_log`, v.v. |

---

## 4.4. Đánh giá Ưu điểm, Nhược điểm và Tính Phù hợp

### Ưu điểm:
1. **Phù hợp hoàn hảo với bài toán Nhà hàng vừa và nhỏ:** Đáp ứng đầy đủ toàn bộ luồng nghiệp vụ vận hành thực tế mà không gây dư thừa tài nguyên phần cứng.
2. **Cấu trúc MVC phân tách rõ ràng:** Việc tách biệt giữa Model, View, Controller giúp nhóm dễ dàng phân công công việc (Frontend làm việc ở `app/views/`, Backend làm việc ở `app/controllers/` và `app/models/`).
3. **Chi phí triển khai cực thấp:** Hoạt động ổn định trên môi trường XAMPP / LAMP stack tiêu chuẩn, dễ bảo trì, dễ sao lưu phục hồi cơ sở dữ liệu.
4. **Bảo mật và toàn vẹn tốt:** Ứng dụng PDO Prepared Statements ngăn ngừa SQL Injection; xác thực Stateless qua JWT giúp quản lý phiên làm việc gọn nhẹ; bảng `audit_log` đảm bảo tính minh bạch khi vận hành.

### Nhược điểm & Giới hạn:
1. **Hạn chế mở rộng độc lập (Scalability):** Do là kiến trúc Monolith, khi lượng khách quét mã QR gọi món tăng đột biến, toàn bộ hệ thống (kể cả phần quản lý kho nội bộ) đều chịu chung tải trên một server.
2. **Khả năng chịu lỗi đơn điểm (Single Point of Failure):** Nếu server Apache hoặc MySQL gặp sự cố, cả hệ thống POS, quản lý kho và giao diện gọi món QR đều ngừng hoạt động đồng thời.

---

# PHẦN 5: KỊCH BẢN VÀ HƯỚNG DẪN THIẾT KẾ SLIDE BÁO CÁO THUYẾT TRÌNH

Phần này được biên soạn chi tiết nhằm giúp bạn và nhóm chuyển tải nội dung chương 2 lên slide thuyết trình (PowerPoint / Canva / Google Slides) một cách chuyên nghiệp, trực quan, kèm lời thoại thuyết trình chuẩn mực (Speaker Notes) cho từng slide.

---

## CẤU TRÚC 10 SLIDE CHUẨN HÓA

### SLIDE 1: TIÊU ĐỀ BÁO CÁO (TITLE SLIDE)
- **Nội dung hiển thị trên Slide:**
  - Tiêu đề lớn: **CHƯƠNG 2: THIẾT KẾ KIẾN TRÚC HỆ THỐNG**
  - Tiêu đề phụ: Nghiên cứu Thể loại, Phong cách Kiến trúc & Định vị Kiến trúc Dự án Quản lý Nhà hàng
  - Thông tin nhóm: Tên đề tài, Giảng viên hướng dẫn, Thành viên thực hiện.
- **Gợi ý hình ảnh/bố cục:**
  - Background chuyên nghiệp tone màu xanh navy / đen hiện đại.
  - Mockup giao diện hệ thống quản lý nhà hàng hiển thị trên laptop và smartphone.
- **Lời thoại thuyết trình (Speaker Notes):**
  > *"Kính thưa Thầy/Cô và các bạn, hôm nay nhóm chúng em xin phép đại diện trình bày Chương 2: Thiết kế Kiến trúc Hệ thống cho đề tài Hệ thống Quản lý Nhà hàng. Nội dung báo cáo hôm nay tập trung làm sáng tỏ hai khái niệm nền tảng trong công nghệ phần mềm: Thể loại kiến trúc và Phong cách kiến trúc, tiến hành so sánh đối chiếu đa chiều, và quan trọng nhất là xác định, chứng minh chính xác thể loại cũng như phong cách kiến trúc mà nhóm đã áp dụng trực tiếp trong mã nguồn dự án."*

---

### SLIDE 2: MỤC TIÊU & NỘI DUNG TRÌNH BÀY (AGENDA)
- **Nội dung hiển thị trên Slide:**
  1. Thể loại kiến trúc (Architectural Genre)
  2. Phong cách kiến trúc (Architectural Style)
  3. Bảng so sánh đối chiếu: Thể loại <=> Phong cách
  4. Phân tích & Định vị Kiến trúc Dự án Nhà hàng
  5. Đánh giá ưu nhược điểm & Kết luận
- **Gợi ý hình ảnh/bố cục:** Bố cục 5 khối icon phẳng (Flat Icons) biểu trưng cho từng phần.
- **Lời thoại thuyết trình (Speaker Notes):**
  > *"Nội dung báo cáo của nhóm gồm 4 phần trọng tâm: Đầu tiên là lý thuyết nền tảng về Thể loại kiến trúc; tiếp theo là các nguyên lý của Phong cách kiến trúc; phần thứ ba là bảng so sánh phân biệt hai khái niệm; và phần cốt lõi là phân tích thực tế kiến trúc của dự án phần mềm nhà hàng mà nhóm đã xây dựng."*

---

### SLIDE 3: THỂ LOẠI KIẾN TRÚC (ARCHITECTURAL GENRE)
- **Nội dung hiển thị trên Slide:**
  - **Khái niệm:** Phân loại hệ thống theo **miền ứng dụng (application domain)** & **bài toán nghiệp vụ** thực tế.
  - **Câu hỏi cốt lõi:** *"Phần mềm này giải quyết bài toán gì và phục vụ ai trong thực tế?"*
  - **Không gian:** Nằm trong **Problem Space** (Không gian Bài toán).
  - **Các thể loại phổ biến:**
    + *Hệ thống thông tin quản lý (MIS / ERP)*: Quản lý giao dịch, kho, dòng tiền, nhân sự.
    + *Ứng dụng Web / O2O*: Tương tác trực tuyến diện rộng qua HTTP.
    + *Hệ thống nhúng & Thời gian thực (Embedded/Real-time)*: Ràng buộc phần cứng và độ trễ.
    + *Hệ thống xử lý dữ liệu lớn & AI*: Xử lý thông tin quy mô lớn.
- **Gợi ý hình ảnh/bố cục:** Sơ đồ mindmap phân nhánh các thể loại kiến trúc phần mềm.
- **Lời thoại thuyết trình (Speaker Notes):**
  > *"Trước hết, về Thể loại kiến trúc. Khi tiếp cận một bài toán phần mềm, thể loại kiến trúc là cách chúng ta định danh phần mềm đó thuộc lĩnh vực nào trong đời sống. Thể loại kiến trúc hoàn toàn độc lập với việc chúng ta dùng ngôn ngữ lập trình nào. Nó trả lời cho câu hỏi: Phần mềm này làm gì, giải quyết nghiệp vụ gì? Ví dụ, phần mềm quản trị bệnh viện hay nhà hàng đều thuộc thể loại Hệ thống thông tin quản lý MIS, nơi yếu tố giao dịch, dữ liệu và tính chính xác được đặt lên hàng đầu."*

---

### SLIDE 4: PHONG CÁCH KIẾN TRÚC (ARCHITECTURAL STYLE)
- **Nội dung hiển thị trên Slide:**
  - **Khái niệm:** Tập hợp các nguyên tắc thiết kế chuẩn hóa, xác định cách tổ chức các module và cơ chế giao tiếp nội bộ.
  - **Câu hỏi cốt lõi:** *"Hệ thống được cấu trúc bên trong như thế nào?"*
  - **Không gian:** Nằm trong **Solution Space** (Không gian Giải pháp kỹ thuật).
  - **3 Thành tố cơ bản:**
    + *Components (Thành phần)*: Controller, Model, View, Service, Database.
    + *Connectors (Bộ kết nối)*: Procedure call, HTTP Request, PDO/SQL, Event.
    + *Constraints (Ràng buộc)*: Phân chia trách nhiệm, chiều phụ thuộc dữ liệu.
  - **Các phong cách kinh điển:** Layered (Phân tầng), MVC, Client-Server, Monolith, Microservices.
- **Gợi ý hình ảnh/bố cục:** Sơ đồ trực quan mô tả 3 khối: Components, Connectors và Constraints.
- **Lời thoại thuyết trình (Speaker Notes):**
  > *"Nếu như Thể loại kiến trúc là bài toán, thì Phong cách kiến trúc chính là Lời giải kỹ thuật. Phong cách kiến trúc định nghĩa cấu trúc bên trong của phần mềm: chia thành bao nhiêu tầng, các module giao tiếp với nhau bằng cách nào, và tuân theo những ràng buộc gì. Một phong cách kiến trúc tốt sẽ giúp hệ thống đạt được các thuộc tính chất lượng quan trọng như: dễ bảo trì, dễ mở rộng và bảo mật cao."*

---

### SLIDE 5: BẢNG SO SÁNH ĐỐI CHIẾU: THỂ LOẠI <=> PHONG CÁCH
- **Nội dung hiển thị trên Slide:**
  *(Bảng tóm tắt súc tích, làm nổi bật sự khác biệt cốt lõi)*

| Tiêu chí | Thể loại Kiến trúc | Phong cách Kiến trúc |
| :--- | :--- | :--- |
| **Bản chất** | Miền nghiệp vụ & Bài toán thực tế | Cấu trúc kỹ thuật & Cách tổ chức code |
| **Câu hỏi** | *"Hệ thống làm gì? Cho lĩnh vực nào?"* | *"Hệ thống được cấu trúc bên trong ra sao?"* |
| **Không gian** | **Problem Space** (Bài toán) | **Solution Space** (Giải pháp) |
| **Phụ thuộc công nghệ** | Độc lập hoàn toàn | Gắn liền với ngôn ngữ, framework, runtime |
| **Mối quan hệ** | 1 Thể loại có thể cài đặt bằng nhiều Phong cách (ví dụ: MIS dùng Monolith hoặc Microservices) |

- **Gợi ý hình ảnh/bố cục:** Chia đôi màn hình dạng đối chiếu (Split Screen Comparison) với màu sắc tương phản rõ rệt.
- **Lời thoại thuyết trình (Speaker Notes):**
  > *"Thưa Thầy/Cô, đây là bảng so sánh đối chiếu mấu chốt giữa hai khái niệm. Điểm khác biệt lớn nhất nằm ở không gian tư duy: Thể loại kiến trúc thuộc về Problem Space - tức không gian bài toán nghiệp vụ; trong khi Phong cách kiến trúc thuộc về Solution Space - tức không gian giải pháp công nghệ. Hai khái niệm này có mối quan hệ trực giao: Một thể loại nghiệp vụ như quản lý nhà hàng có thể được lập trình theo phong cách Monolith MVC hoặc chia nhỏ theo Microservices. Do đó, ta không thể nhầm lẫn hay đồng nhất hai khái niệm này với nhau."*

---

### SLIDE 6: ĐỊNH VỊ KIẾN TRÚC DỰ ÁN NHÀ HÀNG (PROJECT ARCHITECTURE)
- **Nội dung hiển thị trên Slide:**
  - **Tên dự án:** Hệ thống Quản lý Nhà hàng & Gọi món Trực tuyến (PHP Restaurant)
  - **1. Thể loại Kiến trúc xác định:**
    + **MIS / Mini-ERP chuyên ngành F&B:** Quản lý vòng đời khép kín: Menu, Nguyên liệu, Kho, Bàn ăn, Đơn bán hàng, Chi phí, Doanh thu.
    + **Web Application / O2O Platform:** Khách hàng quét mã QR tại bàn gọi món trực tiếp; nhân viên quản lý qua trình duyệt web.
  - **2. Phong cách Kiến trúc xác định:**
    + **Client - Server (Khách - Chủ):** Web Browser (Client) <-> Apache PHP Web Server <-> MySQL DB.
    + **Monolithic Architecture (Đơn khối):** Đóng gói toàn bộ module trong một codebase duy nhất, triển khai tập trung trên XAMPP.
    + **Model - View - Controller (MVC) & 3-Tier Layered:** Tách biệt rõ ràng tầng Routing, Presentation, Business Controller và Data Access Model.
- **Gợi ý hình ảnh/bố cục:** Huy hiệu (Badge) thể loại và phong cách kiến trúc nổi bật kèm logo PHP, MySQL, Bootstrap.
- **Lời thoại thuyết trình (Speaker Notes):**
  > *"Áp dụng vào chính dự án mà nhóm đã xây dựng: Về Thể loại kiến trúc, dự án được xác định là một Hệ thống thông tin quản lý MIS chuyên ngành F&B, đồng thời là một Ứng dụng Web O2O. Hệ thống giải quyết trọn vẹn nghiệp vụ nhà hàng từ quản lý tồn kho, công thức món ăn cho tới thanh toán hóa đơn. Về Phong cách kiến trúc, nhóm kết hợp ba phong cách chuẩn mực: Kiến trúc Khách - Chủ Client-Server, Kiến trúc Đơn khối Monolithic và Mô hình phân tầng MVC."*

---

### SLIDE 7: SƠ ĐỒ CẤU TRÚC KIẾN TRÚC MÃ NGUỒN DỰ ÁN
- **Nội dung hiển thị trên Slide:**
  - Sơ đồ 3 tầng trực quan:
    + **Presentation Tier:** `app/views/` (Bootstrap 5, HTML5, Fetch API).
    + **Application & Controller Tier:** `core/App.php` (Router) -> `app/controllers/` (`AuthController`, `InventoryReceiptController`, `SaleOrderController`, etc.) kết hợp `helpers/JWT.php`.
    + **Data Access Tier:** `app/models/` (`core/Model.php`, PDO Prepared Statements) -> `MySQL (restaurant_db)`.
  - Luồng dữ liệu (Flow): URL Request -> `.htaccess` -> `App.php` -> `Controller` -> `Model` -> `Database` -> Render `View`.
- **Gợi ý hình ảnh/bố cục:** Sơ đồ khối kiến trúc phân tầng (Architecture Flow Diagram) như đã vẽ trong Phần 4.2.
- **Lời thoại thuyết trình (Speaker Notes):**
  > *"Trên slide là sơ đồ kiến trúc mã nguồn thực tế của dự án. Khi người dùng gửi một yêu cầu từ trình duyệt, file .htaccess sẽ điều hướng về index.php và router core/App.php sẽ phân tích URL để gọi chính xác Controller tương ứng. Tầng Controller kế thừa từ core/Controller, chịu trách nhiệm xác thực người dùng qua JWT và kiểm tra quyền hạn RBAC. Sau đó, Controller gọi tầng Model để thực hiện các câu truy vấn an toàn qua PDO Prepared Statements xuống cơ sở dữ liệu MySQL, và cuối cùng dữ liệu được đổ ra giao diện View bằng Bootstrap 5."*

---

### SLIDE 8: MINH CHỨNG TỪ MÃ NGUỒN THỰC TẾ (CODEBASE EVIDENCE)
- **Nội dung hiển thị trên Slide:**
  *(Trình bày các trích đoạn code và cấu trúc thư mục thực tế)*
  - **Routing MVC:** `core/App.php` định tuyến tự động `/{controller}/{method}/{params}`.
  - **Phân quyền & Bảo mật:** `core/Controller.php` với hàm `requireRoles(['admin', 'manager'])` và `helpers/JWT.php`.
  - **Nghiệp vụ cốt lõi:** `InventoryReceiptController.php` & `SaleOrderController.php` xử lý transaction nhập kho và bán hàng.
  - **Toàn vẹn dữ liệu:** `core/Model.php` sử dụng PDO Prepared Statements ngăn chặn 100% nguy cơ SQL Injection.
- **Gợi ý hình ảnh/bố cục:** Hình ảnh chụp cây thư mục dự án và 2-3 khung code snippet nổi bật.
- **Lời thoại thuyết trình (Speaker Notes):**
  > *"Để chứng minh tính xác thực, nhóm xin dẫn chứng trực tiếp từ mã nguồn: Cấu trúc thư mục được phân chia rạch ròi thành core, app/controllers, app/models và app/views. Tất cả các thao tác tương tác dữ liệu đều đi qua tầng Model sử dụng PDO Prepared Statements, đảm bảo dữ liệu không bao giờ bị tấn công SQL Injection. Đồng thời cơ chế xác thực JWT được tích hợp trực tiếp vào Base Controller giúp bảo vệ toàn bộ các route quản trị của nhà hàng."*

---

### SLIDE 9: ĐÁNH GIÁ TÍNH PHÙ HỢP CỦA KIẾN TRÚC ĐỐI VỚI DỰ ÁN
- **Nội dung hiển thị trên Slide:**
  - **Ưu điểm vượt trội:**
    + *Phù hợp thực tiễn:* Tối ưu cho quy mô vừa và nhỏ của nhà hàng, đáp ứng tức thời mọi quy trình vận hành.
    + *Dễ bảo trì & cộng tác:* Cấu trúc MVC giúp phân tách rõ rệt công việc giữa lập trình viên Frontend và Backend.
    + *Chi phí triển khai thấp:* Vận hành mượt mà trên nền tảng Apache/MySQL tiêu chuẩn (XAMPP).
    + *Bảo mật cao:* Xác thực JWT Stateless + Phân quyền RBAC + PDO chống injection.
  - **Hạn chế & Hướng khắc phục:**
    + Khả năng mở rộng chịu tải bị giới hạn bởi tính đơn khối (Monolith).
    + *Giải pháp tương lai:* Tách module gọi món QR thành dịch vụ độc lập nếu số lượng bàn và chi nhánh mở rộng quy mô lớn.
- **Gợi ý hình ảnh/bố cục:** Bảng 2 cột: Cột Xanh (Ưu điểm đạt được) và Cột Vàng (Thách thức & Giải pháp tương lai).
- **Lời thoại thuyết trình (Speaker Notes):**
  > *"Việc lựa chọn kiến trúc Monolith MVC trên nền tảng PHP cho dự án này là một quyết định kỹ thuật hoàn toàn thực tế và phù hợp. Nó giải quyết triệt để bài toán kinh doanh của nhà hàng mà không đòi hỏi chi phí máy chủ đắt đỏ hay hạ tầng vận hành phức tạp. Khi hệ thống phát triển thành chuỗi nhà hàng lớn trong tương lai, nhóm hoàn toàn có thể tách riêng phân hệ gọi món QR thành một dịch vụ độc lập mà không phá vỡ cấu trúc logic đã được thiết kế chuẩn mực."*

---

### SLIDE 10: TỔNG KẾT & HỎI ĐÁP (Q&A)
- **Nội dung hiển thị trên Slide:**
  - **Tóm lược kết quả:**
    1. Làm rõ bản chất và sự khác biệt giữa Thể loại và Phong cách kiến trúc.
    2. Định vị chính xác dự án là **MIS / Web App** với phong cách **Client-Server, Monolith MVC 3-Tier**.
    3. Mã nguồn được tổ chức chuẩn hóa, bảo mật và vận hành ổn định.
  - **Lời cảm ơn:** Cảm ơn Thầy/Cô và các bạn đã chú ý lắng nghe.
  - **Phiên hỏi đáp:** Mời Thầy/Cô và các bạn đặt câu hỏi (Q&A Session).
- **Gợi ý hình ảnh/bố cục:** Thông tin liên hệ của nhóm, mã QR dẫn tới kho mã nguồn (Repository) và dòng chữ Q&A trang trọng.
- **Lời thoại thuyết trình (Speaker Notes):**
  > *"Tóm lại, thông qua Chương 2, nhóm không chỉ làm rõ mặt lý thuyết về Thể loại và Phong cách kiến trúc mà còn bảo vệ thành công tính đúng đắn trong việc lựa chọn kiến trúc cho phần mềm Quản lý Nhà hàng của nhóm. Em xin chân thành cảm ơn Thầy/Cô và các bạn đã lắng nghe. Nhóm em rất mong nhận được những nhận xét, đóng góp quý báu từ Thầy/Cô. Xin trân trọng cảm ơn!"*

---
*Tài liệu được biên soạn phục vụ đồ án môn học Kiến trúc Phần mềm / Kỹ thuật Phần mềm.*
