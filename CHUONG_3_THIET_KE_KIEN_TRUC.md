# CHƯƠNG 3: THIẾT KẾ KIẾN TRÚC
> **Tài liệu Báo cáo & Phân tích Chuyên sâu Kiến trúc Phần mềm**  
> **Học phần:** Kiến trúc và Thiết kế Phần mềm (*Software Architecture and Design*)  
> **Dự án thực tế:** Hệ thống Quản lý Nhà hàng & Gọi món Trực tuyến (*Restaurant Management System - `cimonwork-design/php-restaurant`*)  
> **Nền tảng công nghệ:** PHP Native MVC, MySQL (PDO), Bootstrap 5, JWT Authentication, RBAC 6 vai trò  
> **Tài liệu tham chiếu chuẩn:** SEI (Software Engineering Institute), POSA (Pattern-Oriented Software Architecture), Martin Fowler, Roger S. Pressman

---

## MỤC LỤC TỔNG QUAN

- [CHƯƠNG 3: THIẾT KẾ KIẾN TRÚC](#chương-3-thiết-kế-kiến-trúc)
  - [MỤC LỤC TỔNG QUAN](#mục-lục-tổng-quan)
- [PHẦN 1: ĐÁNH GIÁ CÁC KIẾN TRÚC THAY THẾ (MỤC 3.2.3)](#phần-1-đánh-giá-các-kiến-trúc-thay-thế-mục-323)
  - [1.1. Bản chất, Khái niệm và Mục tiêu của việc Đánh giá Kiến trúc Thay thế](#11-bản-chất-khái-niệm-và-mục-tiêu-của-việc-đánh-giá-kiến-trúc-thay-thế)
    - [1.1.1. Khái niệm cốt lõi](#111-khái-niệm-cốt-lõi)
    - [1.1.2. Nguyên lý Đánh đổi (Architecture Trade-offs)](#112-nguyên-lý-đánh-đổi-architecture-trade-offs)
    - [1.1.3. Cây Thuộc tính Chất lượng Phần mềm (Quality Attributes - ISO/IEC 25010)](#113-cây-thuộc-tính-chất-lượng-phần-mềm-quality-attributes---isoiec-25010)
  - [1.2. Các Phương pháp và Khung Đánh giá Kiến trúc Chuẩn hóa](#12-các-phương-pháp-và-khung-đánh-giá-kiến-trúc-chuẩn-hóa)
    - [1.2.1. Phương pháp ATAM (Architecture Tradeoff Analysis Method - SEI)](#121-phương-pháp-atam-architecture-tradeoff-analysis-method---sei)
    - [1.2.2. Phương pháp CBAM (Cost-Benefit Analysis Method)](#122-phương-pháp-cbam-cost-benefit-analysis-method)
    - [1.2.3. Phương pháp SAAM (Software Architecture Analysis Method)](#123-phương-pháp-saam-software-architecture-analysis-method)
    - [1.2.4. Kỹ thuật Ma trận Quyết định có Trọng số (Weighted Scoring Matrix)](#124-kỹ-thuật-ma-trận-quyết-định-có-trọng-số-weighted-scoring-matrix)
  - [1.3. Quy trình 5 Bước Thực thi Đánh giá Kiến trúc trong Kỹ thuật Phần mềm](#13-quy-trình-5-bước-thực-thi-đánh-giá-kiến-trúc-trong-kỹ-thuật-phần-mềm)
  - [1.4. Đánh giá Kiến trúc Thực tế trong Dự án `php-restaurant`](#14-đánh-giá-kiến-trúc-thực-tế-trong-dự-án-php-restaurant)
    - [1.4.1. Bối cảnh bài toán và Yêu cầu Kiến trúc Trọng yếu (ASRs)](#141-bối-cảnh-bài-toán-và-yêu-cầu-kiến-trúc-trọng-yếu-asrs)
    - [1.4.2. Đề xuất 4 Phương án Kiến trúc Ứng viên](#142-đề-xuất-4-phương-án-kiến-trúc-ứng-viên)
    - [1.4.3. Bảng Ma trận Đánh giá & Chấm điểm Đánh đổi (Trade-off Matrix)](#143-bảng-ma-trận-đánh-giá--chấm-điểm-đánh-đổi-trade-off-matrix)
    - [1.4.4. Hồ sơ Quyết định Kiến trúc (Architectural Decision Record - ADR-001)](#144-hồ-sơ-quyết-định-kiến-trúc-architectural-decision-record---adr-001)
    - [1.4.5. Ngưỡng Kích hoạt Chuyển đổi Kiến trúc (Architectural Trigger Points)](#145-ngưỡng-kích-hoạt-chuyển-đổi-kiến-trúc-architectural-trigger-points)
- [PHẦN 2: PHÂN TÍCH PHỔ TRONG THIẾT KẾ KIẾN TRÚC (MỤC 3.3.1)](#phần-2-phân-tích-phổ-trong-thiết-kế-kiến-trúc-mục-331)
  - [2.1. Khái niệm và Định nghĩa "Phổ Mẫu Thiết Kế" (The Pattern Spectrum)](#21-khái-niệm-và-định-nghĩa-phổ-mẫu-thiết-kế-the-pattern-spectrum)
    - [2.1.1. Nguồn gốc học thuật (POSA - Buschmann et al.)](#211-nguồn-gốc-học-thuật-posa---buschmann-et-al)
    - [2.1.2. Hai trục tọa độ của Phổ Mẫu](#212-hai-trục-tọa-độ-của-phổ-mẫu)
  - [2.2. Ba Tầng Bậc Cốt lõi trên Dải Phổ Mẫu](#22-ba-tầng-bậc-cốt-lõi-trên-dải-phổ-mẫu)
    - [2.2.1. Tầng Vĩ mô: Mẫu Kiến trúc (Architectural Patterns)](#221-tầng-vĩ-mô-mẫu-kiến-trúc-architectural-patterns)
    - [2.2.2. Tầng Trung mô: Mẫu Thiết kế (Design Patterns - GoF)](#222-tầng-trung-mô-mẫu-thiết-kế-design-patterns---gof)
    - [2.2.3. Tầng Vi mô: Mẫu Cài đặt / Thành ngữ Ngôn ngữ (Idioms / Implementation Patterns)](#223-tầng-vi-mô-mẫu-cài-đặt--thành-ngữ-ngôn-ngữ-idioms--implementation-patterns)
  - [2.3. Mối Quan hệ Tương hỗ và Cơ chế Ánh xạ Top-Down Dọc theo Dải Phổ](#23-mối-quan-hệ-tương-hỗ-và-cơ-chế-ánh-xạ-top-down-dọc-theo-dải-phổ)
    - [2.3.1. Dòng chảy từ Chiến lược đến Thực thi (Top-Down Alignment)](#231-dòng-chảy-từ-chiến-lược-đến-thực-thi-top-down-alignment)
    - [2.3.2. Nguy cơ Xói mòn Kiến trúc (Architectural Erosion) & Lạm dụng Mẫu (Over-Engineering)](#232-nguy-cơ-xói-mòn-kiến-trúc-architectural-erosion--lạm-dụng-mẫu-over-engineering)
  - [2.4. Bản đồ Phổ Mẫu Trực tiếp trong Dự án `php-restaurant`](#24-bản-đồ-phổ-mẫu-trực-tiếp-trong-dự-án-php-restaurant)
    - [2.4.1. Tầng Kiến trúc: Monolithic 3-Tier Model-View-Controller](#241-tầng-kiến-trúc-monolithic-3-tier-model-view-controller)
    - [2.4.2. Tầng Thiết kế (Design Patterns ứng dụng trong dự án)](#242-tầng-thiết-kế-design-patterns-ứng-dụng-trong-dự-án)
    - [2.4.3. Tầng Cài đặt (PHP Language Idioms trong mã nguồn)](#243-tầng-cài-đặt-php-language-idioms-trong-mã-nguồn)
    - [2.4.4. Bảng Tổng hợp Đối chiếu Bản đồ Phổ Mẫu Dự án](#244-bảng-tổng-hợp-đối-chiếu-bản-đồ-phổ-mẫu-dự-án)
- [PHẦN 3: MỘT SỐ MẪU THIẾT KẾ KIẾN TRÚC PHỔ BIẾN (HIỆN ĐẠI) (MỤC 3.3.2)](#phần-3-một-số-mẫu-thiết-kế-kiến-trúc-phổ-biến-hiện-đại-mục-332)
  - [3.1. Bối cảnh và Động lực Tiến hóa của Kiến trúc Hiện đại](#31-bối-cảnh-và-động-lực-tiến-hóa-của-kiến-trúc-hiện-đại)
  - [3.2. Phân tích Chuyên sâu 5 Mẫu Kiến trúc Hiện đại](#32-phân-tích-chuyên-sâu-5-mẫu-kiến-trúc-hiện-đại)
    - [3.2.1. Kiến trúc Đa tầng Hiện đại & Kiến trúc Sạch (Clean / Onion / Hexagonal Architecture)](#321-kiến-trúc-đa-tầng-hiện-đại--kiến-trúc-sạch-clean--onion--hexagonal-architecture)
    - [3.2.2. Kiến trúc Vi dịch vụ (Microservices Architecture)](#322-kiến-trúc-vi-dịch-vụ-microservices-architecture)
    - [3.2.3. Kiến trúc Hướng sự kiện (Event-Driven Architecture - EDA)](#323-kiến-trúc-hướng-sự-kiện-event-driven-architecture---eda)
    - [3.2.4. Kiến trúc Không máy chủ (Serverless Architecture - FaaS & BaaS)](#324-kiến-trúc-không-máy-chủ-serverless-architecture---faas--baas)
    - [3.2.5. Kiến trúc Dựa trên Không gian Bộ nhớ (Space-Based / In-Memory Architecture)](#325-kiến-trúc-dựa-trên-không-gian-bộ-nhớ-space-based--in-memory-architecture)
  - [3.3. Bảng Ma trận So sánh Toàn diện 5 Mẫu Kiến trúc Hiện đại](#33-bảng-ma-trận-so-sánh-toàn-diện-5-mẫu-kiến-trúc-hiện-đại)
  - [3.4. Định hướng Ứng dụng & Lộ trình Tiến hóa Kiến trúc cho Dự án `php-restaurant`](#34-định-hướng-ứng-dụng--lộ-trình-tiến-hóa-kiến-trúc-cho-dự-án-php-restaurant)
    - [3.4.1. Nhận diện các Nút thắt Cổ chai của Monolith Hiện tại khi Chuỗi Nhà hàng Mở rộng](#341-nhận-diện-các-nút-thắt-cổ-chai-của-monolith-hiện-tại-khi-chuỗi-nhà-hàng-mở-rộng)
    - [3.4.2. Bản Thiết kế Kiến trúc Đích (Target Architecture): Mô hình Lai Microservices + EDA](#342-bản-thiết-kế-kiến-trúc-đích-target-architecture-mô-hình-lai-microservices--eda)
    - [3.4.3. Chiến lược Di chuyển Không Gián đoạn Kinh doanh: Strangler Fig Pattern](#343-chiến-lược-di-chuyển-không-gián-đoạn-kinh-doanh-strangler-fig-pattern)
- [PHẦN 4: KỊCH BẢN VÀ HƯỚNG DẪN THUYẾT TRÌNH SLIDE (20 SLIDES CHUẨN HÓA)](#phần-4-kịch-bản-và-hướng-dẫn-thuyết-trình-slide-20-slides-chuẩn-hóa)

---

# PHẦN 1: ĐÁNH GIÁ CÁC KIẾN TRÚC THAY THẾ (MỤC 3.2.3)

## 1.1. Bản chất, Khái niệm và Mục tiêu của việc Đánh giá Kiến trúc Thay thế

### 1.1.1. Khái niệm cốt lõi
- **Đánh giá kiến trúc thay thế (Evaluating Alternative Architectural Designs)** là quá trình phân tích kỹ thuật, so sánh định tính và định lượng giữa các phương án cấu trúc hệ thống ứng viên nhằm xác định giải pháp thỏa mãn tối ưu nhất các mục tiêu nghiệp vụ và thuộc tính chất lượng phần mềm trong phạm vi ràng buộc về ngân sách, công nghệ và thời gian.
- Trong kỹ thuật phần mềm chuẩn mực (theo *Roger S. Pressman* và *Len Bass - SEI*):
  > *"Không có một kiến trúc nào là hoàn hảo tuyệt đối hay vượt trội trên mọi khía cạnh. Mọi kiến trúc phần mềm chỉ là một tập hợp các sự thỏa hiệp có chủ đích nhằm giải quyết một bối cảnh bài toán cụ thể."*

### 1.1.2. Nguyên lý Đánh đổi (Architecture Trade-offs)
- Trong thiết kế hệ thống, các thuộc tính chất lượng thường có tính đối nghịch và triệt tiêu lẫn nhau:
  + Tăng **Bảo mật (Security)** (mã hóa sâu, xác thực đa yếu tố, kiểm tra quyền liên tục) thường làm giảm **Hiệu năng (Performance)** và tăng độ trễ (Latency).
  + Tăng **Khả năng mở rộng (Scalability)** (chuyển sang vi dịch vụ phân tán Microservices) sẽ làm giảm **Tính nhất quán dữ liệu (Data Consistency)** (phải chấp nhận tính nhất quán cuối cùng - Eventual Consistency theo định lý CAP) và làm tăng vọt **Độ phức tạp vận hành (Operational Complexity)**.
  + Tối ưu **Khả năng sửa đổi (Modifiability)** (chia tách nhiều tầng trừu tượng, áp dụng Clean Architecture) đòi hỏi chi phí phát triển ban đầu cao hơn và kéo dài thời gian ra mắt sản phẩm (**Time-to-Market**).
- Bản chất của việc đánh giá là tìm ra "điểm cân bằng vàng" (Pareto Optimum) giữa các thuộc tính này cho dự án.

### 1.1.3. Cây Thuộc tính Chất lượng Phần mềm (Quality Attributes - ISO/IEC 25010)
Để việc đánh giá không mang tính cảm tính, hệ thống cần dựa trên các thuộc tính chất lượng đã được quốc tế hóa theo chuẩn ISO/IEC 25010:
1. **Hiệu năng (Performance Efficiency):** Thời gian phản hồi (Response time), thông lượng (Throughput), mức tiêu thụ tài nguyên CPU/RAM.
2. **Khả năng tương thích & mở rộng (Scalability / Portability):** Khả năng nâng cấp theo chiều dọc (Vertical Scale) và chiều ngang (Horizontal Scale).
3. **Độ tin cậy & Sẵn sàng (Reliability & Availability):** Tỷ lệ thời gian hoạt động (Uptime 99.9%), khả năng chịu lỗi đơn điểm (Fault Tolerance).
4. **Bảo mật (Security):** Tính bảo mật (Confidentiality), toàn vẹn dữ liệu (Integrity), xác thực (Authentication), phân quyền (Authorization) và tính bất khả phủ nhận (Non-repudiation).
5. **Khả năng bảo trì (Maintainability):** Tính module (Modularity), khả năng tái sử dụng (Reusability), khả năng kiểm thử (Testability), khả năng sửa đổi (Modifiability).
6. **Chi phí & Thời gian (Cost & Feasibility):** Chi phí hạ tầng điện toán đám mây, chi phí bản quyền, năng lực đội ngũ phát triển và thời gian triển khai sản phẩm.

---

## 1.2. Các Phương pháp và Khung Đánh giá Kiến trúc Chuẩn hóa

### 1.2.1. Phương pháp ATAM (Architecture Tradeoff Analysis Method - SEI)
- Được phát triển bởi Viện Kỹ thuật Phần mềm Mỹ (Software Engineering Institute - SEI), ATAM là tiêu chuẩn vàng trên toàn cầu để đánh giá kiến trúc phần mềm trước khi tiến hành viết mã hàng loạt.
- **Quy trình 4 Pha và 9 Bước của ATAM:**
  1. *Pha 1 (Trình bày):* Trình bày phương pháp ATAM; Trình bày các mục tiêu nghiệp vụ của doanh nghiệp; Trình bày phương án kiến trúc đề xuất.
  2. *Pha 2 (Khám phá và Phân tích):* Xác định các phương pháp tiếp cận kiến trúc; Xây dựng cây thuộc tính chất lượng (Quality Attribute Tree); Phân tích các phương pháp tiếp cận kiến trúc.
  3. *Pha 3 (Thử nghiệm):* Động não và ưu tiên các kịch bản thực tế (Brainstorm and Prioritize Scenarios); Phân tích sâu các phương pháp kiến trúc theo kịch bản ưu tiên hàng đầu.
  4. *Pha 4 (Báo cáo kết quả):* Trình bày kết luận đánh giá, chỉ ra các điểm rủi ro và khuyến nghị cải tiến.
- **Các khái niệm then chốt trong ATAM:**
  + **Kịch bản (Scenarios):** Bao gồm *Use Case Scenarios* (luồng vận hành hàng ngày), *Growth Scenarios* (kịch bản tăng trưởng tải đột biến hoặc mở rộng nghiệp vụ tương lai), và *Exploratory Scenarios* (kịch bản kiểm tra giới hạn chịu đựng cực hạn của hệ thống khi hạ tầng sụp đổ).
  + **Điểm nhạy cảm (Sensitivity Points):** Thuộc tính kiến trúc mà sự thay đổi nhỏ của nó sẽ ảnh hưởng trực tiếp và mãnh liệt đến một thuộc tính chất lượng (Ví dụ: kích thước buffer kết nối PDO nhạy cảm với thông lượng giao dịch MySQL).
  + **Điểm đánh đổi (Tradeoff Points):** Thuộc tính kiến trúc ảnh hưởng đến nhiều thuộc tính chất lượng khác nhau theo các chiều hướng trái ngược (Ví dụ: cơ chế mã hóa mật khẩu Bcrypt với cost factor = 12: an toàn bảo mật tăng nhưng tiêu tốn tài nguyên CPU server).
  + **Rủi ro (Risks) & Không rủi ro (Non-risks):** Các quyết định thiết kế có khả năng gây lỗi hệ thống hoặc các quyết định đã được chứng minh an toàn trong thực tiễn.

```
                           QUY TRÌNH ĐÁNH GIÁ KIẾN TRÚC ATAM (SEI)
   ┌───────────────────────┐      ┌────────────────────────┐      ┌───────────────────────┐
   │ 1. Trình bày Mục tiêu │      │ 2. Xây dựng Cây        │      │ 3. Đánh giá Kịch bản  │
   │    Nghiệp vụ & Kiến   │ ───► │    Thuộc tính          │ ───► │    Thực tế & Phân tích│
   │    trúc Đề xuất       │      │    Chất lượng          │      │    Trade-off Points   │
   └───────────────────────┘      └────────────────────────┘      └───────────┬───────────┘
                                                                              │
                                  ┌────────────────────────┐                  ▼
                                  │ 5. Lập Hồ sơ Quyết định│      ┌───────────────────────┐
                                  │    Kiến trúc (ADR) &   │ ◄─── │ 4. Nhận diện Rủi ro & │
                                  │    Lộ trình Cải tiến   │      │    Điểm Nhạy cảm      │
                                  └────────────────────────┘      └───────────────────────┘
```

### 1.2.2. Phương pháp CBAM (Cost-Benefit Analysis Method)
- Mở rộng từ ATAM, CBAM lượng hóa việc đánh giá kiến trúc dựa trên bài toán kinh tế:
  $$\text{Lợi ích ròng (ROI)} = \frac{\text{Giá trị nghiệp vụ gia tăng từ thuộc tính chất lượng}}{\text{Tổng chi phí đầu tư hiện thực hóa kiến trúc}}$$
- CBAM giúp ban lãnh đạo và kiến trúc sư lựa chọn kiến trúc có tỷ suất sinh lời cao nhất, tránh bẫy đầu tư quá mức (Over-engineering) cho những thuộc tính kỹ thuật không đem lại giá trị tài chính tương xứng.

### 1.2.3. Phương pháp SAAM (Software Architecture Analysis Method)
- Là tiền thân của ATAM, SAAM tập trung chủ yếu vào việc đánh giá **Khả năng sửa đổi (Modifiability)** và tính khả thi chức năng thông qua phân tích mức độ tác động của các kịch bản nghiệp vụ mới đến cấu trúc module hiện tại.

### 1.2.4. Kỹ thuật Ma trận Quyết định có Trọng số (Weighted Scoring Matrix)
- Là công cụ bán định lượng được sử dụng phổ biến nhất trong thực tế doanh nghiệp.
- Quy trình:
  1. Liệt kê các tiêu chí chất lượng quan trọng ($C_1, C_2, ..., C_n$).
  2. Gán trọng số tương đối cho từng tiêu chí ($W_i$, với $\sum W_i = 100\%$).
  3. Đánh giá từng phương án kiến trúc ứng viên theo thang điểm từ 1 đến 10 ($S_{ij}$).
  4. Tính tổng điểm có trọng số: $\text{Total Score}_j = \sum (W_i \times S_{ij})$. Phương án có điểm cao nhất là phương án được ưu tiên lựa chọn.

---

## 1.3. Quy trình 5 Bước Thực thi Đánh giá Kiến trúc trong Kỹ thuật Phần mềm

1. **Bước 1: Khai phá Yêu cầu Kiến trúc Trọng yếu (Architecturally Significant Requirements - ASRs):** Phỏng vấn các bên liên quan (Product Owner, Khách hàng, Vận hành, Kỹ sư an ninh) để lọc ra các yêu cầu phi chức năng chi phối kiến trúc.
2. **Bước 2: Xây dựng Danh mục Kiến trúc Ứng viên (Candidate Architectures):** Đề xuất từ 2 đến 4 giải pháp kiến trúc khả thi, vẽ sơ đồ khối tổng quan và xác định công nghệ thực thi.
3. **Bước 3: Thiết lập Kịch bản Đánh giá Đa chiều:** Xây dựng kịch bản vận hành bình thường, kịch bản tải đột biến giờ cao điểm, kịch bản mất kết nối cơ sở dữ liệu, kịch bản nâng cấp tính năng mới.
4. **Bước 4: Chấm điểm, Phân tích Đánh đổi và Xác định Điểm mù Kỹ thuật:** Họp hội đồng kiến trúc sư, sử dụng ma trận trọng số và phương pháp ATAM để mổ xẻ ưu nhược điểm từng phương án.
5. **Bước 5: Ban hành Hồ sơ Quyết định Kiến trúc (ADR - Architectural Decision Record):** Đóng băng lựa chọn kiến trúc chính thức, ghi nhận rõ nguyên nhân lựa chọn, các phương án bị loại bỏ, các rủi ro đã chấp nhận và các ngưỡng kích hoạt thay đổi kiến trúc trong tương lai.

---

## 1.4. Đánh giá Kiến trúc Thực tế trong Dự án `php-restaurant`

### 1.4.1. Bối cảnh bài toán và Yêu cầu Kiến trúc Trọng yếu (ASRs)
- **Dự án `cimonwork-design/php-restaurant`** là hệ thống phần mềm quản lý nhà hàng ăn uống phục vụ hai nhóm đối tượng chính:
  + *Nội bộ nhà hàng:* Nhân viên phục vụ gọi món tại bàn, Thu ngân chốt bill thanh toán, Bếp nhận đơn chế biến, Thủ kho quản lý phiếu nhập kho nguyên liệu (`InventoryReceipt`), Quản lý nhà hàng xem báo cáo doanh thu (`audit_log`, thống kê).
  + *Khách hàng:* Quét mã QR tại bàn để xem thực đơn điện tử, gọi món trực tiếp từ trình duyệt smartphone mà không cần cài ứng dụng.
- **Ràng buộc & ASRs đặc thù:**
  1. *ASR-1 (Toàn vẹn dữ liệu kho & hóa đơn):* Yêu cầu giao dịch ACID nghiêm ngặt khi tạo phiếu nhập kho (`InventoryReceiptController.php`), cập nhật tồn kho nguyên liệu (`ingredient`), và khấu trừ công thức chế biến (`recipe`).
  2. *ASR-2 (Chi phí triển khai & Bảo trì tối thiểu):* Vận hành trên máy chủ nội bộ hoặc hosting tiêu chuẩn (XAMPP / LAMP Stack), không yêu cầu cụm Kubernetes hay dịch vụ Cloud tốn kém hàng tháng.
  3. *ASR-3 (Tốc độ đưa vào sử dụng - Time-to-market):* Triển khai nhanh chóng trong thời gian ngắn cho các nhà hàng vừa và nhỏ.
  4. *ASR-4 (Kiểm soát bảo mật phân quyền):* Xác thực Stateless JWT kết hợp phân quyền chặt chẽ 6 vai trò (Admin, Manager, Staff, Cashier, Chef, Waiter).

### 1.4.2. Đề xuất 4 Phương án Kiến trúc Ứng viên

1. **Phương án A: Monolithic 3-Tier kết hợp MVC (PHP Native PDO + Bootstrap 5 + MySQL Server)**
   - Toàn bộ mã nguồn đóng gói chung một repository, chạy chung tiến trình Apache/PHP.
   - Phân chia 3 tầng: Presentation (`app/views/`), Controller (`app/controllers/`), Data Access Model (`app/models/` & `core/Model.php`).
2. **Phương án B: Microservices Architecture (Spring Boot / Node.js + Kafka + PostgreSQL per service)**
   - Chia nhỏ hệ thống thành 5 dịch vụ độc lập: *Auth Service*, *Table & QR Order Service*, *Kitchen KDS Service*, *Inventory Service*, *Billing & Payment Service*.
   - Giao tiếp qua REST API Gateway và Message Broker Kafka. Mỗi service sở hữu database riêng biệt.
3. **Phương án C: Serverless Architecture (AWS Lambda / Firebase Cloud Functions + DynamoDB)**
   - Backend phân rã thành các hàm FaaS phi trạng thái.
   - Frontend tĩnh lưu trữ trên S3/Cloudflare Pages. Cơ sở dữ liệu NoSQL phân tán.
4. **Phương án D: Single Page Application (SPA React/Vue) + Headless REST API Backend (PHP/Laravel)**
   - Tách rời hoàn toàn giao diện Frontend (Client-side Rendering) và Backend API (JSON response).

### 1.4.3. Bảng Ma trận Đánh giá & Chấm điểm Đánh đổi (Trade-off Matrix)

Thang điểm từ 1 đến 10 (10 là tối ưu nhất cho dự án):

| Tiêu chí Đánh giá (Quality Attributes) | Trọng số ($W$) | PA A: Monolith MVC (PHP) | PA B: Microservices (Phân tán) | PA C: Serverless (Cloud FaaS) | PA D: SPA + REST API |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **1. Chi phí Triển khai & Hạ tầng (Infrastructure Cost)** | 20% | **9.5** *(Chạy mượt trên XAMPP/VPS rẻ)* | **3.0** *(Chi phí cụm server/K8s rất cao)* | **6.0** *(Chi phí cloud theo request)* | **7.5** *(Cần 2 hosting riêng cho FE/BE)* |
| **2. Tốc độ Phát triển & Ra mắt (Time-to-Market)** | 20% | **9.0** *(Cấu trúc đơn giản, code nhanh)* | **3.5** *(Tốn thời gian setup infra/RPC)* | **5.5** *(Phức tạp khi debug local)* | **7.0** *(Tốn thời gian xây 2 repo)* |
| **3. Toàn vẹn Giao dịch ACID (Nhập kho/Hóa đơn)** | 20% | **9.5** *(MySQL Transaction đơn giản, an toàn)* | **4.0** *(Distributed Transaction/Saga rất phức tạp)* | **5.0** *(NoSQL không hỗ trợ multi-table ACID chuẩn)* | **9.0** *(Backend RDBMS xử lý ACID)* |
| **4. Khả năng Mở rộng Quy mô (Scalability)** | 10% | **5.0** *(Mở rộng đơn khối theo chiều dọc)* | **9.5** *(Mở rộng độc lập từng service hoàn hảo)* | **9.5** *(Auto-scale tự động vô hạn)* | **7.0** *(Scale backend API độc lập)* |
| **5. Độ phức tạp Vận hành & DevOps (Operability)** | 10% | **9.0** *(Bảo trì đơn giản, sao lưu 1 CSDL duy nhất)* | **2.5** *(Đòi hỏi chuyên gia DevOps, CI/CD phức tạp)* | **5.0** *(Phụ thuộc hoàn toàn nhà cung cấp Cloud)* | **6.5** *(Phải quản lý CORS, 2 pipeline build)* |
| **6. Khả năng Bảo trì & Phân tách Module (Modularity)** | 10% | **8.0** *(Quy chuẩn MVC tách bạch rõ ràng)* | **9.0** *(Cô lập mã nguồn tối đa)* | **6.0** *(Phân mảnh hàng chục Cloud Functions)* | **8.5** *(Tách biệt hoàn toàn UI và Core API)* |
| **7. An toàn & Phân quyền Bảo mật (Security & RBAC)** | 10% | **8.5** *(JWT Stateless + Session + PDO Prepared)* | **8.0** *(Phức tạp trong xác thực giữa các service)* | **7.5** *(Cần cấu hình IAM policies phức tạp)* | **8.0** *(Xác thực Bearer Token an toàn)* |
| **TỔNG ĐIỂM CÓ TRỌNG SỐ (TOTAL SCORE)** | **100%** | **8.60** *(LỰA CHỌN TỐI ƯU)* | **4.90** | **6.10** | **7.60** |

### 1.4.4. Hồ sơ Quyết định Kiến trúc (Architectural Decision Record - ADR-001)

> **Mã hồ sơ:** ADR-001  
> **Tiêu đề:** Lựa chọn Kiến trúc Monolithic 3-Tier MVC cho Hệ thống Quản lý Nhà hàng `php-restaurant`  
> **Trạng thái:** ĐÃ PHÊ DUYỆT (ACCEPTED)  
> **Kiến trúc sư phụ trách:** Nhóm Phát triển Dự án  
> **Ngày phê duyệt:** Cập nhật theo giai đoạn thiết kế hiện tại  

- **Bối cảnh (Context):** Dự án cần một hệ thống quản lý nhà hàng hoạt động ổn định, tin cậy cao, có khả năng vận hành tại chỗ (on-premises) trong mạng LAN của nhà hàng ngay cả khi mất kết nối Internet quốc tế, chi phí đầu tư bằng 0 hoặc rất thấp, đội ngũ phát triển tinh gọn từ 2-4 thành viên.
- **Quyết định (Decision):** Chính thức lựa chọn **Phương án A: Monolithic 3-Tier kết hợp kiến trúc MVC trên nền tảng PHP Native và MySQL PDO**.
- **Biện minh (Rationale):**
  1. *Tính toàn vẹn giao dịch vượt trội:* Việc tạo phiếu nhập kho (`InventoryReceipt`) cần ghi nhận đồng thời thông tin nhà cung cấp, ngày nhập, tổng tiền, danh sách hàng chục chi tiết nguyên liệu (`inventory_receipt_detail`), và cập nhật trường `stock_quantity` của bảng `ingredient`. Cơ chế `beginTransaction()`, `commit()`, `rollBack()` của MySQL PDO xử lý bài toán này nhanh, an toàn và loại trừ hoàn toàn rủi ro sai lệch dữ liệu.
  2. *Triển khai nội bộ không phụ thuộc Cloud:* Kiến trúc Monolith PHP có thể chạy trực tiếp trên máy chủ đặt tại quầy thu ngân qua gói XAMPP. Nhà hàng vẫn order món và in hóa đơn bình thường dù cáp quang Internet bị gián đoạn.
  3. *Tối ưu hóa nhân lực và chi phí:* Nhóm phát triển không mất hàng tháng trời để cấu hình Docker, Kubernetes, Service Mesh, Kafka như trong Microservices; tập trung 100% thời gian hiện thực hóa các tính năng nghiệp vụ nhà hàng.
- **Hệ quả & Rủi ro chấp nhận (Consequences & Accepted Risks):** Chấp nhận khả năng mở rộng độc lập bị giới hạn. Toàn bộ các module chia sẻ chung tài nguyên CPU/RAM của server duy nhất.

### 1.4.5. Ngưỡng Kích hoạt Chuyển đổi Kiến trúc (Architectural Trigger Points)
Hệ thống không cố định kiến trúc mãi mãi. Nhóm đặt ra các "ngưỡng cảnh báo kỹ thuật" (Trigger Points), khi vượt qua các ngưỡng này, dự án sẽ kích hoạt lộ trình tái cấu trúc sang kiến trúc phân tán:
1. *Ngưỡng 1 (Số chi nhánh):* Số lượng nhà hàng trong chuỗi vượt quá **15 chi nhánh** kết nối chung một máy chủ.
2. *Ngưỡng 2 (Tải đỉnh điểm):* Lượng khách hàng quét QR gọi món đồng thời vào giờ cao điểm vượt quá **3.000 requests/giây (RPS)** gây nghẽn kết nối MySQL Connection Pool.
3. *Ngưỡng 3 (Quy mô nhóm):* Đội ngũ kỹ sư phần mềm vượt quá **15 lập trình viên**, việc cùng phát triển trên một repository monolithic gây xung đột mã nguồn (Merge Conflicts) liên tục.

---

# PHẦN 2: PHÂN TÍCH PHỔ TRONG THIẾT KẾ KIẾN TRÚC (MỤC 3.3.1)

## 2.1. Khái niệm và Định nghĩa "Phổ Mẫu Thiết Kế" (The Pattern Spectrum)

### 2.1.1. Nguồn gốc học thuật (POSA - Buschmann et al.)
- Thuật ngữ **"Phổ Mẫu" (The Pattern Spectrum)** được định nghĩa kinh điển trong công trình nghiên cứu nổi tiếng thế giới của Frank Buschmann, Regine Meunier, Hans Rohnert, Peter Sommerlad, và Michael Stal thuộc nhóm tác giả bộ sách *Pattern-Oriented Software Architecture (POSA) - Volume 1: A System of Patterns*.
- Các tác giả chỉ ra rằng trong công nghệ phần mềm, không thể gom chung tất cả các mẫu (patterns) vào một rổ duy nhất. Một giải pháp thiết kế có thể chi phối toàn bộ hệ thống trị giá hàng triệu USD, nhưng cũng có thể chỉ là một mẹo nhỏ để quản lý bộ nhớ của một con trỏ trong một ngôn ngữ lập trình cụ thể.
- Do đó, khái niệm **"Phổ Mẫu"** ra đời như một hệ tọa độ phân loại liên tục, giúp kỹ sư định vị chính xác vị trí, quy mô và mức độ trừu tượng của từng giải pháp phần mềm.

### 2.1.2. Hai trục tọa độ của Phổ Mẫu
1. **Trục hoành - Mức độ trừu tượng (Level of Abstraction):**
   - Từ mức **Khái niệm chiến lược (Conceptual/High Abstraction)** không phụ thuộc công nghệ đến mức **Hiện thực hóa chi tiết (Concrete/Low Abstraction)** gắn chặt với từng từ khóa cú pháp của ngôn ngữ lập trình.
2. **Trục tung - Quy mô và Phạm vi ảnh hưởng (Scale and Scope of Impact):**
   - Từ phạm vi **Toàn hệ thống (System-wide / Subsystems)** ảnh hưởng đến hàng trăm module đến phạm vi **Cục bộ (Component / Class level)** và phạm vi **Dòng lệnh mã nguồn (Statement / Language idiom level)**.

```
                           SƠ ĐỒ PHỔ MẪU THIẾT KẾ (THE PATTERN SPECTRUM)
   ▲ Phạm vi ảnh hưởng & Mức độ trừu tượng
   │
   │  ┌─────────────────────────────────────────────────────────────┐
   │  │ 1. MẪU KIẾN TRÚC (ARCHITECTURAL PATTERNS) - CẤP ĐỘ VĨ MÔ    │
   │  │    - Phạm vi: Toàn bộ hệ thống, cấu trúc khung sườn          │
   │  │    - Đại diện: MVC, Layered, Microservices, Pipes & Filters │
   │  └──────────────────────────────┬──────────────────────────────┘
   │                                 │ Định hướng cấu trúc
   │  ┌──────────────────────────────▼──────────────────────────────┐
   │  │ 2. MẪU THIẾT KẾ (DESIGN PATTERNS - GoF) - CẤP ĐỘ TRUNG MÔ   │
   │  │    - Phạm vi: Cấu trúc lớp, đối tượng và module con          │
   │  │    - Đại diện: Factory, Strategy, Observer, Singleton, RBAC │
   │  └──────────────────────────────┬──────────────────────────────┘
   │                                 │ Hiện thực hóa bằng cú pháp
   │  ┌──────────────────────────────▼──────────────────────────────┐
   │  │ 3. THÀNH NGỮ NGÔN NGỮ (IDIOMS) - CẤP ĐỘ VI MÔ               │
   │  │    - Phạm vi: Dòng lệnh, đặc tính cú pháp của ngôn ngữ cụ thể│
   │  │    - Đại diện: PDO Prepared Statements, JWT Bearer, Closures│
   │  └─────────────────────────────────────────────────────────────┘
   └──────────────────────────────────────────────────────────────────► Độ cụ thể / Ngôn ngữ
```

---

## 2.2. Ba Tầng Bậc Cốt lõi trên Dải Phổ Mẫu

### 2.2.1. Tầng Vĩ mô: Mẫu Kiến trúc (Architectural Patterns)
- **Định nghĩa:** Là mẫu thiết kế cấp cao nhất, xác định khung cấu trúc cơ bản và ngôn ngữ tổ chức của toàn bộ một ứng dụng phần mềm.
- **Đặc trưng:**
  + Định nghĩa các hệ thống con (Subsystems), trách nhiệm cốt lõi của từng hệ thống con.
  + Đặt ra các cơ chế kết nối (Connectors) và các quy tắc, ràng buộc bắt buộc (Constraints) chi phối mối quan hệ giữa chúng.
  + Hoàn toàn độc lập với ngôn ngữ lập trình cụ thể (Dù viết bằng Java, PHP, C# hay Python thì mẫu MVC hoặc Microservices vẫn giữ nguyên bản chất cấu trúc).
- **Ví dụ kinh điển:** Model-View-Controller (MVC), Layered Architecture (Kiến trúc phân tầng), Pipes and Filters, Blackboard, Broker, Event-Driven Architecture.

### 2.2.2. Tầng Trung mô: Mẫu Thiết kế (Design Patterns - GoF)
- **Định nghĩa:** Là mẫu thiết kế cấp trung gian, cung cấp giải pháp tối ưu cho việc tổ chức cấu trúc lớp (Classes), giao diện (Interfaces) và sự tương tác giữa các đối tượng nhằm giải quyết một bài toán thiết kế cụ thể trong một thành phần phần mềm.
- **Đặc trưng:**
  + Không định hình toàn bộ cấu trúc phần mềm; chỉ áp dụng cục bộ bên trong một module hoặc hệ thống con.
  + Thường được phân loại theo bộ 23 mẫu kinh điển của Gang of Four (GoF):
    * *Creational Patterns (Mẫu khởi tạo):* Factory Method, Abstract Factory, Singleton, Builder, Prototype.
    * *Structural Patterns (Mẫu cấu trúc):* Adapter, Composite, Decorator, Facade, Proxy.
    * *Behavioral Patterns (Mẫu hành vi):* Strategy, Observer, Command, Template Method, State, Chain of Responsibility.
  + Độc lập với ngôn ngữ nhưng phụ thuộc vào mô hình lập trình (đặc biệt là Lập trình hướng đối tượng - OOP).

### 2.2.3. Tầng Vi mô: Mẫu Cài đặt / Thành ngữ Ngôn ngữ (Idioms / Implementation Patterns)
- **Định nghĩa:** Là mẫu ở cấp độ thấp nhất trên phổ, gắn chặt với các đặc trưng, quy ước và cú pháp đặc thù của một ngôn ngữ lập trình cụ thể.
- **Đặc trưng:**
  + Hướng dẫn cách thức viết code an toàn, tối ưu hóa bộ nhớ, tăng tốc độ xử lý hoặc làm sáng tỏ ngữ nghĩa dựa trên các cơ chế sẵn có của ngôn ngữ đó.
  + Không thể dịch nguyên xi từ ngôn ngữ này sang ngôn ngữ khác nếu ngôn ngữ đích không có cơ chế tương đương.
- **Ví dụ tiêu biểu:**
  + Trong **C++:** Mẫu *RAII (Resource Acquisition Is Initialization)* để tự động giải phóng bộ nhớ khi ra khỏi scope.
  + Trong **Python:** Mẫu *List Comprehension*, *Context Manager (`with` statement)*, *Decorators (`@`)*.
  + Trong **PHP:** Mẫu *PDO Prepared Statements*, *Magic Methods (`__get`, `__set`, `__construct`)*, *Null Coalescing Operator (`??`)*, *Type Hinting*.

---

## 2.3. Mối Quan hệ Tương hỗ và Cơ chế Ánh xạ Top-Down Dọc theo Dải Phổ

### 2.3.1. Dòng chảy từ Chiến lược đến Thực thi (Top-Down Alignment)
- Dải phổ mẫu không phải là ba ngăn chứa tách biệt, mà là một **dòng chảy liên tục mang tính định hướng từ trên xuống (Top-down)**:
  1. *Cấp độ Kiến trúc đưa ra Chiến lược:* Lựa chọn mẫu kiến trúc MVC để phân tách ranh giới giữa giao diện người dùng và dữ liệu.
  2. *Cấp độ Thiết kế cụ thể hóa Chiến thuật:* 
     - Để điều hướng yêu cầu của người dùng, Controller áp dụng mẫu **Front Controller** kết hợp **Template Method** (`core/Controller.php`).
     - Để Model truy xuất bảng cơ sở dữ liệu linh hoạt, áp dụng mẫu **Table Data Gateway** hoặc **Active Record** (`core/Model.php`).
     - Để kiểm tra quyền truy cập của 6 đối tượng người dùng, áp dụng mẫu **Role-Based Access Control (RBAC)**.
  3. *Cấp độ Cài đặt biến thành Dòng lệnh chuẩn mực:*
     - Lớp Model hiện thực hóa truy vấn dữ liệu thông qua cơ chế **PDO Prepared Statements** của PHP để bảo đảm an toàn trước lỗi SQL Injection.
     - Lớp Controller lưu giữ thông báo lỗi qua cơ chế **Session Flash Message** đặc thù của ứng dụng web.

### 2.3.2. Nguy cơ Xói mòn Kiến trúc (Architectural Erosion) & Lạm dụng Mẫu (Over-Engineering)
- Việc phân tích phổ giúp phát hiện và ngăn chặn hai căn bệnh kỹ thuật phổ biến:
  + **Xói mòn kiến trúc (Architectural Decay / Erosion):** Lập trình viên vi phạm quy tắc tầng bậc trên phổ. Ví dụ: Viết trực tiếp câu lệnh truy vấn SQL bằng idiom PHP `$pdo->query(...)` ngay bên trong file giao diện View (`app/views/inventory_receipt/create.php`). Điều này phá vỡ hoàn toàn ràng buộc của Architectural Pattern (MVC), biến hệ thống thành một khối mã nguồn "mì ăn liền" (Spaghetti code) không thể kiểm thử.
  + **Lạm dụng mẫu (Over-Engineering / Pattern Mania):** Áp dụng vô tội vạ các Design Pattern phức tạp (như Abstract Factory, Visitor) cho các tác vụ CRUD đơn giản, gây cồng kềnh bộ mã nguồn và lãng phí tài nguyên CPU.

---

## 2.4. Bản đồ Phổ Mẫu Trực tiếp trong Dự án `php-restaurant`

### 2.4.1. Tầng Kiến trúc: Monolithic 3-Tier Model-View-Controller
- Dự án `php-restaurant` định hình cấu trúc vĩ mô bằng phong cách Monolith MVC:
  + **Router & Front Controller:** Điểm vào duy nhất `index.php` tiếp nhận mọi request HTTP thông qua điều hướng `.htaccess`, chuyển giao quyền phân tích URL cho lớp `core/App.php`.
  + **Tầng Trình diễn (Presentation Tier - View):** Đặt tại `app/views/`, hoàn toàn chỉ đảm nhiệm việc hiển thị HTML/Bootstrap và nhận tương tác của người dùng.
  + **Tầng Ứng dụng & Điều khiển (Application Tier - Controller):** Đặt tại `app/controllers/`, kế thừa từ `core/Controller.php` để xử lý logic điều phối nghiệp vụ.
  + **Tầng Dữ liệu & Nghiệp vụ cốt lõi (Data Tier - Model):** Đặt tại `app/models/`, kế thừa từ `core/Model.php`, quản lý các thao tác trên CSDL MySQL `restaurant_db`.

### 2.4.2. Tầng Thiết kế (Design Patterns ứng dụng trong dự án)
Dự án áp dụng nhiều mẫu thiết kế GoF chuẩn mực:
1. **Front Controller Pattern:** Lớp `core/App.php` hoạt động như một bộ điều phối trung tâm, phân tích chuỗi URI dạng `http://localhost/restaurant/{controller}/{method}/{params}` để tự động nạp tệp Controller tương ứng, khởi tạo đối tượng và gọi hàm action.
2. **Template Method Pattern:** Lớp cha `core/Controller.php` cung cấp sẵn khung xương xử lý chuẩn:
   - Phương thức `view($view, $data)`: Nạp tự động tệp giao diện và truyền dữ liệu biến từ Controller sang View.
   - Phương thức `model($model)`: Khởi tạo và trả về instance của lớp Model cần dùng.
   - Phương thức `requireRoles($roles)`: Rào chắn bảo vệ kiểm tra quyền truy cập trước khi Controller con thực thi action.
3. **Table Data Gateway / Data Access Pattern:** Lớp `core/Model.php` đóng gói toàn bộ logic tương tác CSDL bảng, cung cấp các hàm nền tảng `find($id)`, `where($column, $value)`, `insert($data)`, `update($id, $data)`, `delete($id)`. Các Model con như `InventoryReceipt.php`, `SaleOrder.php`, `User.php` kế thừa và mở rộng các nghiệp vụ nghiệp vụ chuyên biệt.
4. **Strategy Pattern (Chiến lược thanh toán):** Xử lý đơn hàng bán lẻ tại bàn (`SaleOrderController.php`), hệ thống cho phép linh hoạt lựa chọn chiến lược thanh toán qua tham số `payment_method`: Tiền mặt (Cash), Chuyển khoản ngân hàng qua mã VietQR, hoặc Quẹt thẻ POS.
5. **Role-Based Access Control Pattern (RBAC):** Mô hình hóa quyền truy cập hệ thống nhà hàng theo 6 vai trò: `admin`, `manager`, `staff`, `cashier`, `chef`, `waiter`. Mỗi endpoint Controller đều được gán nhãn vai trò được phép truy cập (Ví dụ: chức năng tạo phiếu nhập kho chỉ dành cho `['admin', 'manager']`).

### 2.4.3. Tầng Cài đặt (PHP Language Idioms trong mã nguồn)
Tại tầng thấp nhất của phổ, mã nguồn dự án tận dụng triệt để các thành ngữ tối ưu của PHP:
1. **PDO Prepared Statements Idiom:**
   ```php
   // Minh chứng từ core/Model.php
   $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :val LIMIT 1");
   $stmt->execute([':val' => $value]);
   return $stmt->fetch(PDO::FETCH_ASSOC);
   ```
   *Ý nghĩa:* Tách rời mã SQL và dữ liệu đầu vào của người dùng, vô hiệu hóa hoàn toàn mọi cuộc tấn công bằng SQL Injection.
2. **Flash Message Session Idiom:**
   ```php
   // Controller lưu thông báo trước khi Redirect
   $_SESSION['flash_success'] = "Tạo phiếu nhập kho thành công!";
   header("Location: " . BASE_URL . "/inventory_receipt/index");
   exit;
   ```
   *Ý nghĩa:* Giải quyết bài toán truyền thông điệp trạng thái giữa 2 chu kỳ Request-Response phi trạng thái của giao thức HTTP theo chuẩn mẫu PRG (Post/Redirect/Get).
3. **Stateless JWT Token Bearer Idiom:**
   ```php
   // Trích xuất Bearer token từ Authorization Header trong helpers/JWT.php
   $headers = getallheaders();
   if (isset($headers['Authorization']) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
       $jwt = $matches[1];
       $payload = JWT::decode($jwt, JWT_SECRET_KEY);
   }
   ```
   *Ý nghĩa:* Thực thi cơ chế xác thực người dùng hiện đại, gọn nhẹ, không gây phình to bộ nhớ RAM của server PHP Session.
4. **Bcrypt Hashing Idiom:** Sử dụng hàm gốc `password_hash($password, PASSWORD_BCRYPT)` và `password_verify($password, $hash)` để mã hóa mật khẩu 1 chiều kết hợp Salt ngẫu nhiên trong bảng `users`.

### 2.4.4. Bảng Tổng hợp Đối chiếu Bản đồ Phổ Mẫu Dự án

| Tầng Phổ (Spectrum Layer) | Tên Mẫu Ứng dụng | File Mã nguồn trong Dự án | Trách nhiệm Kỹ thuật Cụ thể |
| :--- | :--- | :--- | :--- |
| **Mẫu Kiến trúc (Macro)** | **Monolithic 3-Tier MVC** | `core/App.php`, `core/Controller.php`, `core/Model.php` | Định hình khung xương toàn bộ hệ thống, phân chia rõ ranh giới giữa View, Controller và Model. |
| **Mẫu Thiết kế (Meso)** | **Front Controller** | `index.php`, `.htaccess`, `core/App.php` | Điểm tiếp nhận request tập trung, phân tích định dạng URL để gọi đúng Controller/Action. |
| **Mẫu Thiết kế (Meso)** | **Template Method** | `core/Controller.php` | Định nghĩa sẵn các hàm dùng chung: `view()`, `model()`, `jsonResponse()`, `requireRoles()`. |
| **Mẫu Thiết kế (Meso)** | **Table Data Gateway** | `core/Model.php`, `app/models/InventoryReceipt.php` | Đóng gói câu lệnh CRUD CSDL, cách ly logic SQL khỏi Controller. |
| **Mẫu Thiết kế (Meso)** | **RBAC (Access Control)** | `core/Controller.php`, `helpers/JWT.php` | Kiểm soát phân quyền người dùng theo 6 nhóm vai trò trong hệ thống nhà hàng. |
| **Mẫu Thiết kế (Meso)** | **Strategy Pattern** | `app/controllers/SaleOrderController.php` | Hoán đổi linh hoạt thuật toán tính tiền và cổng thanh toán (Tiền mặt, QR, Thẻ). |
| **Thành ngữ Ngôn ngữ (Micro)** | **PDO Prepared Statements** | `core/Model.php`, `config/database.php` | Chống SQL Injection, tối ưu hóa bộ nhớ đệm thực thi câu lệnh SQL. |
| **Thành ngữ Ngôn ngữ (Micro)** | **Flash Session PRG** | `app/controllers/InventoryReceiptController.php` | Truyền dữ liệu thông báo trạng thái qua mô hình Post/Redirect/Get. |
| **Thành ngữ Ngôn ngữ (Micro)** | **JWT Token Processing** | `helpers/JWT.php`, `config/jwt.php` | Đóng gói Payload và giải mã chữ ký điện tử HMAC-SHA256 trên HTTP Header. |

---

# PHẦN 3: MỘT SỐ MẪU THIẾT KẾ KIẾN TRÚC PHỔ BIẾN (HIỆN ĐẠI) (MỤC 3.3.2)

## 3.1. Bối cảnh và Động lực Tiến hóa của Kiến trúc Hiện đại

- Trong 15 năm qua, ngành kỹ thuật phần mềm đã chứng kiến cuộc cách mạng chuyển dịch kiến trúc mạnh mẽ nhất trong lịch sử:
  + **Giai đoạn 1 (Truyền thống - Monolith):** Toàn bộ ứng dụng chạy chung một tiến trình duy nhất trên một máy chủ vật lý.
  + **Giai đoạn 2 (Dịch vụ phân tán - SOA / Microservices):** Phân chia hệ thống thành các đơn vị dịch vụ độc lập, triển khai trên môi trường ảo hóa hoặc Container (Docker, Kubernetes).
  + **Giai đoạn 3 (Điện toán đám mây & Hướng sự kiện - Cloud-Native & Event-Driven):** Ứng dụng tận dụng tối đa hạ tầng đám mây (AWS, GCP, Azure), phi trạng thái (Stateless), tự động co giãn không giới hạn (Auto-scaling), xử lý luồng sự kiện bất đồng bộ thời gian thực.
- **Động lực thúc đẩy tiến hóa:**
  1. *Khối lượng người dùng tăng vọt:* Các ứng dụng hiện đại phải phục vụ hàng triệu người dùng đồng thời thay vì vài trăm người dùng như hệ thống cũ.
  2. *Yêu cầu tính sẵn sàng cực cao (High Availability 99.99%):* Không được phép gián đoạn hoạt động toàn bộ hệ thống khi một module con bị lỗi.
  3. *Tốc độ bàn giao tính năng (Continuous Delivery - CI/CD):* Đội ngũ phát triển cần đẩy mã nguồn mới lên môi trường Production hàng chục lần mỗi ngày mà không cần restart toàn bộ hệ thống (Zero Downtime Deployment).

---

## 3.2. Phân tích Chuyên sâu 5 Mẫu Kiến trúc Hiện đại

### 3.2.1. Kiến trúc Đa tầng Hiện đại & Kiến trúc Sạch (Clean / Onion / Hexagonal Architecture)
- **Tác giả khởi xướng:** Alistair Cockburn (Hexagonal - 2005), Jeffrey Palermo (Onion - 2008), Robert C. Martin (Uncle Bob - Clean Architecture - 2012).
- **Nguyên lý cốt lõi:** **Nguyên lý Đảo ngược Phụ thuộc (Dependency Inversion Principle - DIP)**.
  + Mọi phụ thuộc trong mã nguồn chỉ được phép trỏ từ **ngoài vào trong**.
  + Vòng tròn trung tâm là **Entities (Quy tắc nghiệp vụ doanh nghiệp)** và **Use Cases (Quy tắc nghiệp vụ ứng dụng)**. Lõi này hoàn toàn tinh khiết, không chứa bất kỳ thư viện ngoài, không dính líu đến Framework (PHP, Spring, Express), không phụ thuộc vào UI và Database.
  + Các tầng bên ngoài (Controllers, Gateways, Presenters, Database, Web Server) chỉ là các "chi tiết cài đặt" (Plugins) kết nối vào lõi thông qua các Cổng giao tiếp (**Ports & Adapters**).
- **Ưu điểm vượt trội:** Kiểm thử độc lập 100% không cần bật Database; dễ dàng thay đổi công nghệ CSDL (chuyển từ MySQL sang MongoDB mà không sửa 1 dòng code nghiệp vụ).
- **Nhược điểm:** Đòi hỏi số lượng Interface và lớp trung gian lớn; chi phí thời gian thiết kế ban đầu cao.

### 3.2.2. Kiến trúc Vi dịch vụ (Microservices Architecture)
- **Bản chất:** Hệ thống được chia nhỏ thành tập hợp các dịch vụ nhỏ, độc lập, có thể triển khai riêng rẽ, mỗi dịch vụ tập trung vào một năng lực nghiệp vụ duy nhất (Bounded Context theo Domain-Driven Design - DDD).
- **Đặc trưng kiến trúc bắt buộc:**
  + **Database per Service:** Mỗi microservice sở hữu CSDL riêng biệt, cấm tuyệt đối việc service A truy vấn trực tiếp vào CSDL của service B.
  + **API Gateway:** Đóng vai trò là điểm tiếp nhận duy nhất cho Client, đảm nhận định tuyến, xác thực bảo mật, giới hạn tần suất gọi (Rate Limiting) và cân bằng tải.
  + **Giao tiếp liên dịch vụ:** Qua HTTP/REST, gRPC (đồng bộ) hoặc Message Broker như RabbitMQ/Kafka (bất đồng bộ).
  + **Khả năng phục hồi (Resilience):** Ứng dụng các mẫu *Circuit Breaker (Netflix Hystrix, Resilience4j)* để ngăn chặn sụp đổ dây chuyền khi một dịch vụ phụ gặp sự cố.
- **Ưu điểm:** Khả năng mở rộng độc lập từng phần (Ví dụ: service QR Order có thể scale lên 20 pods mà service Kế toán vẫn giữ 1 pod); linh hoạt công nghệ (service này viết bằng Go, service kia viết bằng Node.js).
- **Nhược điểm:** Cực kỳ phức tạp trong việc gỡ lỗi phân tán (cần OpenTelemetry/Jaeger); chi phí hạ tầng và DevOps rất cao; phải giải quyết bài toán giao dịch phân tán qua mẫu Saga.

### 3.2.3. Kiến trúc Hướng sự kiện (Event-Driven Architecture - EDA)
- **Bản chất:** Các thành phần trong hệ thống không giao tiếp bằng lời gọi trực tiếp (Request/Response) mà tương tác thông qua việc phát sinh (Produce), chuyển tiếp (Broker) và lắng nghe tiêu thụ (Consume) các **Sự kiện nghiệp vụ (Events)**.
- **Cấu trúc 3 khối cốt lõi:**
  1. *Event Producer (Bên phát sự kiện):* Phát tín hiệu khi có sự kiện nghiệp vụ xảy ra trong hệ thống (Ví dụ: `OrderPlacedEvent` khi khách quét QR đặt món thành công).
  2. *Event Broker (Hạ tầng trung gian truyền tải):* Nền tảng lưu trữ và phân phối sự kiện tốc độ cao (Apache Kafka, Apache Pulsar, RabbitMQ).
  3. *Event Consumer (Bên tiêu thụ sự kiện):* Các dịch vụ đăng ký lắng nghe sự kiện để kích hoạt hành động tiếp theo (Bếp nhận đơn in phiếu, Kho trừ nguyên liệu, Kế toán ghi sổ).
- **Mô hình kiến trúc nâng cao:**
  + **Event Sourcing:** Thay vì chỉ lưu trạng thái hiện tại của bản ghi, hệ thống lưu toàn bộ lịch sử các sự kiện đã xảy ra từ trước đến nay dưới dạng một chuỗi Append-only bất biến.
  + **CQRS (Command Query Responsibility Segregation):** Tách riêng hoàn toàn mô hình xử lý ghi dữ liệu (Commands) và mô hình tối ưu đọc dữ liệu (Queries).
- **Ưu điểm:** Khớp nối lỏng lẻo tối đa (Loose Coupling); khả năng chịu tải cực lớn; hỗ trợ xử lý thời gian thực.
- **Nhược điểm:** Phức tạp trong việc đảm bảo thứ tự sự kiện; phải chấp nhận tính nhất quán dữ liệu cuối cùng (Eventual Consistency).

### 3.2.4. Kiến trúc Không máy chủ (Serverless Architecture - FaaS & BaaS)
- **Bản chất:** Mô hình thực thi đám mây trong đó nhà cung cấp dịch vụ đám mây (Cloud Provider như AWS, Google Cloud, Cloudflare) quản lý toàn bộ việc cấp phát, vận hành, bảo mật và co giãn của máy chủ. Lập trình viên chỉ tập trung viết mã logic dưới dạng các hàm độc lập (**Function as a Service - FaaS**).
- **Đặc trưng nổi bật:**
  + **Tính phí theo thực tế (Pay-per-use):** Chỉ trả tiền cho từng mili-giây mà hàm thực thi và dung lượng RAM tiêu thụ. Không có người dùng = Chi phí bằng 0 đồng.
  + **Co giãn tức thời (Elastic Auto-scaling):** Tự động mở rộng từ 0 lên 10.000 thực thể hàm đồng thời trong tích tắc để đáp ứng nhu cầu đột biến.
  + **Phi trạng thái (Stateless):** Mỗi hàm thực thi độc lập, trạng thái được lưu trữ tại CSDL hoặc Storage ngoài.
- **Ưu điểm:** Không cần quản trị máy chủ (Zero Server Management); khả năng mở rộng tự động vô hạn; chi phí cực thấp cho các dịch vụ có lưu lượng truy cập thất thường.
- **Nhược điểm:** Vấn đề **Cold Start** (độ trễ khởi động lại hàm sau thời gian nhàn rỗi); thời gian thực thi tối đa bị giới hạn (thường tối đa 15 phút); rủi ro phụ thuộc nền tảng đám mây (Vendor Lock-in).

### 3.2.5. Kiến trúc Dựa trên Không gian Bộ nhớ (Space-Based / In-Memory Architecture)
- **Bản chất:** Giải quyết triệt để nút thắt cổ chai lớn nhất của các ứng dụng web là **Ổ cứng và CSDL quan hệ (Database Bottleneck)** bằng cách phân tán toàn bộ dữ liệu đang giao dịch trực tiếp trên bộ nhớ RAM của một cụm máy chủ (**In-Memory Data Grid - IMDG**).
- **Thành phần chính:**
  + *Processing Unit:* Các node máy chủ tính toán nhỏ chứa đồng thời cả mã xử lý và bộ nhớ đệm dữ liệu.
  + *Virtual Space / Data Grid:* Không gian bộ nhớ ảo đồng bộ dữ liệu giữa các node (Redis Cluster, Hazelcast, Apache Ignite).
  + *Data Pump:* Tiến trình chạy ngầm bất đồng bộ ghi dữ liệu từ RAM xuống ổ đĩa CSDL quan hệ một cách tuần tự.
- **Ưu điểm:** Tốc độ giao dịch siêu nhanh với độ trễ tính bằng micro-giây; thông lượng xử lý hàng triệu giao dịch/giây.
- **Nhược điểm:** Chi phí phần cứng RAM cực kỳ đắt đỏ; nguy cơ mất dữ liệu nếu toàn bộ cụm máy chủ bị mất điện đột ngột trước khi Data Pump kịp ghi xuống đĩa.

---

## 3.3. Bảng Ma trận So sánh Toàn diện 5 Mẫu Kiến trúc Hiện đại

| Tiêu chí So sánh | Clean / Hexagonal | Microservices | Event-Driven (EDA) | Serverless (FaaS) | Space-Based (RAM) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Bản chất Cốt lõi** | Đảo ngược phụ thuộc, độc lập Framework | Phân chia dịch vụ theo Bounded Context | Giao tiếp bất đồng bộ qua luồng Event | Mã hàm phi trạng thái, Cloud quản lý máy chủ | Phân tán dữ liệu trên RAM, xóa nghẽn DB |
| **Khả năng Mở rộng (Scalability)** | Trung bình *(Mở rộng theo khối ứng dụng)* | Rất cao *(Mở rộng độc lập từng dịch vụ)* | Cực cao *(Xử lý thông lượng bất đồng bộ)* | Tự động vô hạn *(Co giãn từ 0 đến N pods)* | Cực cao *(Mở rộng tuyến tính trên cụm RAM)* |
| **Tính Nhất quán Dữ liệu (Consistency)** | Rất cao *(ACID trong phạm vi DB)* | Chấp nhận Eventual Consistency *(Saga)* | Chấp nhận Eventual Consistency | Phụ thuộc CSDL backend (DynamoDB/SQL) | Rất cao *(Đồng bộ in-memory tức thời)* |
| **Độ Phức tạp Vận hành (DevOps)** | Thấp - Trung bình *(Vận hành như Monolith)* | Cực cao *(K8s, CI/CD, Tracing, Service Mesh)* | Cao *(Vận hành cụm Kafka/RabbitMQ)* | Thấp *(Cloud provider gánh toàn bộ hạ tầng)* | Rất cao *(Quản lý phân mảnh bộ nhớ RAM)* |
| **Chi phí Hạ tầng Ban đầu** | Thấp | Rất cao | Trung bình - Cao | Rất thấp *(Pay-per-use, chi phí 0đ lúc rảnh)* | Cực kỳ đắt đỏ *(Dung lượng RAM quy mô lớn)* |
| **Độ Trễ Phản hồi (Latency)** | Cực thấp *(Lời gọi hàm trong bộ nhớ)* | Trung bình *(Tốn độ trễ mạng Network HOP)* | Thấp - Trung bình *(Xử lý bất đồng bộ)* | Trung bình *(Bị ảnh hưởng bởi Cold Start)* | Siêu thấp *(Tính bằng Micro-giây)* |
| **Phù hợp nhất với Loại hình Dự án** | Dự án có logic nghiệp vụ lõi phức tạp, cần kiểm thử sâu | Hệ thống quy mô lớn, nhiều nhóm dev độc lập | Ứng dụng IoT, thông báo thời gian thực, tài chính | Ứng dụng tác vụ xử lý ảnh, webhook, tải thất thường | Sàn giao dịch chứng khoán, bán vé concert, game online |

---

## 3.4. Định hướng Ứng dụng & Lộ trình Tiến hóa Kiến trúc cho Dự án `php-restaurant`

### 3.4.1. Nhận diện các Nút thắt Cổ chai của Monolith Hiện tại khi Chuỗi Nhà hàng Mở rộng
Khi dự án `php-restaurant` được nhân rộng từ 1 nhà hàng thử nghiệm lên chuỗi **50 nhà hàng** phục vụ đồng loạt trên toàn quốc, kiến trúc Monolith PHP trên XAMPP hiện tại sẽ bộc lộ các giới hạn nghiêm trọng:
1. **Nghẽn cổ chai giờ cao điểm (Peak-hour Bottleneck):** Vào khung giờ 11h30 - 13h00 và 18h30 - 20h30, hàng ngàn thực khách tại 50 nhà hàng đồng thời quét mã QR trên smartphone để tải menu và đặt món. Lượng truy cập đọc (Read) tăng đột biến gấp 50 lần chiếm dụng hết kết nối MySQL (`max_connections`) và tài nguyên tiến trình Apache, khiến các nhân viên thu ngân và thủ kho không thể thực hiện giao dịch ghi phiếu nhập kho (`InventoryReceipt`) hay in hóa đơn thanh toán.
2. **Khủng hoảng điểm chết đơn độc (Single Point of Failure):** Nếu một lập trình viên vô tình đưa lên một đoạn mã lỗi gây tràn bộ nhớ tại module báo cáo doanh thu (`AuditLog`), toàn bộ máy chủ web bị crash, làm tê liệt cả hệ thống hiển thị bếp và hệ thống đặt món QR của toàn bộ 50 chi nhánh.
3. **Thiếu khả năng tương tác thời gian thực (Lack of Real-time Capabilities):** Hiện tại, khi khách gọi món qua QR hoặc khi bếp làm xong món, hệ thống phải dùng cơ chế Polling (gọi Ajax định kỳ mỗi 5 giây) để kiểm tra dữ liệu mới, gây lãng phí băng thông và làm quá tải CSDL MySQL.

### 3.4.2. Bản Thiết kế Kiến trúc Đích (Target Architecture): Mô hình Lai Microservices + EDA
Để giải quyết triệt để các hạn chế trên mà vẫn bảo toàn tính toàn vẹn của dữ liệu nghiệp vụ, nhóm kiến trúc sư đề xuất **Kiến trúc Đích (Target Architecture)** theo mô hình kết hợp: **Microservices phân tán tích hợp Hướng sự kiện (Event-Driven Architecture)**.

```
                  BẢN THIẾT KẾ KIẾN TRÚC ĐÍCH DỰ ÁN PHP-RESTAURANT (TARGET ARCHITECTURE)
  ┌────────────────────────────────────────────────────────────────────────────────────────┐
  │ CLIENTS: [Smartphone Khách QR Order]  |  [Tablet Phục Vụ]  |  [Màn Hình Bếp KDS]       │
  └───────────────────────────────────────────┬────────────────────────────────────────────┘
                                              │ HTTPS / WSS
  ┌───────────────────────────────────────────▼────────────────────────────────────────────┐
  │ API GATEWAY (Kong / NGINX Ingress): Rate Limiting, SSL, JWT Authentication Check       │
  └───────┬───────────────────────────┬────────────────────────────┬───────────────────────┘
          │                           │                            │
  ┌───────▼─────────────┐     ┌───────▼─────────────┐      ┌───────▼───────────────────────┐
  │ 1. ORDER & TABLE    │     │ 2. KITCHEN DISPLAY  │      │ 3. INVENTORY & RECEIPT        │
  │    SERVICE (Node/Go)│     │    SERVICE (KDS)    │      │    SERVICE (PHP Core / Go)    │
  │  - Quét QR gọi món  │     │  - WebSocket Server │      │  - Nhập kho (InventoryReceipt)│
  │  - Quản lý trạng    │     │  - Đẩy món tức thời │      │  - Quản lý tồn kho định lượng │
  │    thái bàn ăn      │     │    vào màn hình bếp │      │  - Cảnh báo cạn nguyên liệu   │
  │  - Cache: Redis     │     │  - DB: MongoDB      │      │  - DB: PostgreSQL (ACID)      │
  └───────┬─────────────┘     └───────▲─────────────┘      └───────▲───────────────────────┘
          │                           │                            │
          │ Phát sự kiện:             │ Lắng nghe:                 │ Lắng nghe:
          │ OrderCreatedEvent         │ OrderCreatedEvent          │ OrderCompletedEvent
          ▼                           │                            │ (Trừ kho theo recipe)
  ┌───────────────────────────────────┴────────────────────────────┴───────────────────────┐
  │ EVENT BROKER (APACHE KAFKA / RABBITMQ CLUSTER): Phân phối luồng sự kiện bất đồng bộ    │
  └───────────────────────────────────┬────────────────────────────────────────────────────┘
                                      │
                              ┌───────▼─────────────┐
                              │ 4. BILLING & PAYMENT│
                              │    SERVICE          │
                              │  - Cổng VietQR/MoMo │
                              │  - Outbox Pattern   │
                              │  - DB: PostgreSQL   │
                              └─────────────────────┘
```

- **Phân tách các Vi dịch vụ chuyên biệt:**
  1. *Order & Table Service (Node.js / Go + Redis):* Chuyên trách phục vụ khách quét QR gọi món và đặt bàn. Nhờ lưu cache thực đơn trên Redis và cơ chế Non-blocking I/O, service này có thể đáp ứng trên **10.000 RPS** với độ trễ dưới 20ms.
  2. *Kitchen Display Service - KDS (WebSocket Service):* Duy trì kết nối hai chiều liên tục với màn hình tablet của đầu bếp trong khu bếp. Khi có đơn mới, màn hình lập tức nhảy chuông và hiển thị món mà không cần tải lại trang.
  3. *Inventory & Receipt Service (Kế thừa lõi PHP MVC nâng cấp):* Đảm nhận toàn bộ nghiệp vụ kiểm kho, lập phiếu nhập kho nguyên liệu (`InventoryReceiptController.php`), quản lý công thức món (`recipe`). Sử dụng CSDL quan hệ PostgreSQL với giao dịch ACID bảo đảm không sai lệch một gam nguyên liệu.
  4. *Billing & Payment Service:* Tích hợp tự động với ngân hàng qua VietQR, ví điện tử MoMo, ZaloPay. Sử dụng mẫu thiết kế **Transactional Outbox Pattern** để đảm bảo trạng thái thanh toán và trừ kho luôn nhất quán ngay cả khi mất mạng.

### 3.4.3. Chiến lược Di chuyển Không Gián đoạn Kinh doanh: Strangler Fig Pattern
- Để chuyển đổi từ hệ thống Monolith cũ sang Kiến trúc Đích, nhóm không sử dụng phương pháp "Đập đi xây lại từ đầu" (Big Bang Rewrite - vốn tiềm ẩn rủi ro phá sản dự án tới 80%), mà áp dụng **Strangler Fig Pattern (Mẫu cây đa siết)** của Martin Fowler:
  1. *Giai đoạn 1 (Thiết lập rào chắn):* Đặt một API Gateway (NGINX / Kong) đứng trước hệ thống Monolith PHP hiện tại. Toàn bộ lượng truy cập ban đầu vẫn được Gateway chuyển tiếp thẳng vào Monolith.
  2. *Giai đoạn 2 (Tách nhánh đầu tiên - QR Order Service):* Xây dựng microservice *Order & Table Service* mới bằng Node.js. Cấu hình API Gateway chuyển hướng toàn bộ các request liên quan đến QR Menu và Gọi món sang service mới; các chức năng quản lý kho, nhập kho, thu ngân vẫn chạy trên Monolith cũ.
  3. *Giai đoạn 3 (Tách nhánh KDS & Thanh toán):* Tiếp tục tách module Bếp KDS và module Cổng thanh toán ra khỏi Monolith, kết nối dữ liệu qua Kafka Event Broker.
  4. *Giai đoạn 4 (Triệt tiêu Monolith cũ):* Khi toàn bộ các module đã được tách thành công thành các microservice độc lập, khối Monolith cũ chính thức được "khai tử" (Retire) mà không gây ra bất kỳ một giây gián đoạn kinh doanh nào cho chuỗi nhà hàng.

---

# PHẦN 4: KỊCH BẢN VÀ HƯỚNG DẪN THUYẾT TRÌNH SLIDE (20 SLIDES CHUẨN HÓA)

Phần này cung cấp toàn bộ nội dung hiển thị, cấu trúc phân cấp (1.1, 1.2... 2.1, 2.2... 3.1, 3.2...), gợi ý hình ảnh minh họa thật lấy từ web, và lời thoại thuyết trình chuẩn mực (**Speaker Notes**) cho 20 slide báo cáo.

---

### SLIDE 01: TRANG TIÊU ĐỀ BÁO CÁO (TITLE SLIDE)
- **Tiêu đề chính:** BÁO CÁO CHUYÊN SÂU: THIẾT KẾ KIẾN TRÚC PHẦN MỀM
- **Tiêu đề phụ:** Đánh Giá Kiến Trúc Thay Thế | Phân Tích Phổ Mẫu Thiết Kế | Các Mẫu Kiến Trúc Hiện Đại & Thực Tiễn Dự Án Nhà Hàng
- **Thông tin đề tài:** Học phần Kiến trúc & Thiết kế Phần mềm | Dự án: `cimonwork-design/php-restaurant`
- **Hình ảnh minh họa từ web:** Logo trường/khoa CNTT kết hợp hình ảnh đồ họa trừu tượng về Software Architecture Blueprint.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Kính thưa Thầy/Cô và các bạn sinh viên. Hôm nay, nhóm em xin phép được trình bày báo cáo chuyên sâu về Chương 3: Thiết kế Kiến trúc Phần mềm. Trọng tâm của bài báo cáo tập trung làm sáng tỏ ba bài toán cốt lõi trong kỹ thuật phần mềm: Thứ nhất là phương pháp luận đánh giá các phương án kiến trúc thay thế; thứ hai là lý thuyết phân tích phổ mẫu thiết kế; và thứ ba là các phong cách kiến trúc hiện đại tiêu biểu. Đặc biệt, toàn bộ các cơ sở lý thuyết này sẽ được nhóm em soi chiếu, minh chứng và liên hệ trực tiếp vào dự án thực tế của nhóm – đó là Hệ thống Quản lý Nhà hàng và Gọi món Trực tuyến php-restaurant. Kính mời Thầy/Cô cùng theo dõi nội dung chi tiết ngay sau đây."

---

### SLIDE 02: MỤC LỤC & LỘ TRÌNH BÁO CÁO (AGENDA)
- **Tiêu đề slide:** LỘ TRÌNH NỘI DUNG BÁO CÁO (AGENDA)
- **Nội dung hiển thị:**
  + **Phần 1: Đánh giá các kiến trúc thay thế (Mục 3.2.3)**
    * 1.1. Bản chất & Cây thuộc tính chất lượng (ISO/IEC 25010)
    * 1.2. Các khung phương pháp chuẩn hóa: ATAM, CBAM, SAAM
    * 1.3. Quy trình 5 bước thực thi đánh giá kiến trúc
    * 1.4. Đánh giá thực tế trong dự án `php-restaurant` & Hồ sơ ADR
  + **Phần 2: Phân tích phổ trong thiết kế kiến trúc (Mục 3.3.1)**
    * 2.1. Khái niệm The Pattern Spectrum (POSA)
    * 2.2. Ba tầng bậc: Mẫu kiến trúc, Mẫu thiết kế, Thành ngữ ngôn ngữ
    * 2.3. Mối quan hệ tương hỗ & Cơ chế ánh xạ Top-Down
    * 2.4. Bản đồ phổ mẫu trực tiếp trong source code `php-restaurant`
  + **Phần 3: Một số mẫu thiết kế kiến trúc phổ biến hiện đại (Mục 3.3.2)**
    * 3.1. Bối cảnh tiến hóa Cloud-Native & Phân tán
    * 3.2. Phân tích 5 mẫu hiện đại: Clean, Microservices, EDA, Serverless, Space-Based
    * 3.3. Ma trận so sánh toàn diện 5 mẫu kiến trúc
    * 3.4. Định hướng Target Architecture & Strangler Fig Pattern cho dự án
- **Hình ảnh minh họa từ web:** Sơ đồ lộ trình quy trình kỹ thuật phần mềm (Software Engineering Roadmap).
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Bài thuyết trình của nhóm được chia làm 3 phần lớn với bố cục phân cấp logic từ 1.1 đến 3.4. Phần đầu tiên sẽ giải quyết bài toán ra quyết định kỹ thuật: Làm thế nào để chọn được kiến trúc phù hợp nhất giữa vô vàn giải pháp? Phần thứ hai đi sâu vào lý thuyết phân tích phổ của Buschmann để hiểu rõ cách các mẫu phần mềm kết nối từ cấp độ vĩ mô xuống từng dòng code PHP. Và phần thứ ba sẽ mở rộng sang các kiến trúc hiện đại như Microservices hay Event-Driven, đồng thời vạch ra lộ trình tiến hóa kiến trúc cho hệ thống nhà hàng của chúng em."

---

### SLIDE 03: 1.1. BẢN CHẤT & MỤC TIÊU ĐÁNH GIÁ KIẾN TRÚC THAY THẾ
- **Tiêu đề slide:** 1.1. BẢN CHẤT & NGUYÊN LÝ ĐÁNH ĐỔI (TRADE-OFFS)
- **Nội dung hiển thị:**
  + **Bản chất kiến trúc:** Không có kiến trúc hoàn hảo cho mọi bài toán; kiến trúc là một tập hợp các sự đánh đổi có chủ đích (Trade-offs).
  + **Mục tiêu đánh giá:**
    * Giảm thiểu rủi ro kỹ thuật trước khi bước vào giai đoạn viết mã hàng loạt.
    * Tối ưu hóa tổng chi phí đầu tư (TCO) và rút ngắn thời gian ra mắt thị trường (Time-to-Market).
    * Bảo đảm hệ thống đáp ứng trọn vẹn các yêu cầu phi chức năng (Non-functional Requirements).
  + **Cây thuộc tính chất lượng (ISO/IEC 25010):**
    * *Hiệu năng (Performance Efficiency):* Thời gian phản hồi, thông lượng giao dịch.
    * *Khả năng mở rộng (Scalability):* Co giãn tài nguyên theo tải người dùng.
    * *Bảo mật & Toàn vẹn (Security & Integrity):* Ngăn chặn xâm nhập, bảo vệ dữ liệu ACID.
    * *Khả năng bảo trì (Maintainability):* Tính module hóa, độ phức tạp khi sửa lỗi.
- **Hình ảnh minh họa từ web:** Sơ đồ Cây Thuộc tính Chất lượng Phần mềm Chuẩn Quốc tế ISO/IEC 25010.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Bước vào mục 1.1, câu hỏi đầu tiên đặt ra là: Tại sao chúng ta phải đánh giá các kiến trúc thay thế? Trong kỹ thuật phần mềm, một trong những chân lý quan trọng nhất là: Không có một kiến trúc nào là tốt nhất về mọi mặt. Nếu bạn muốn hệ thống có hiệu năng cực cao và bảo mật tối đa, bạn sẽ phải đánh đổi bằng chi phí phát triển lớn và thời gian ra mắt sản phẩm bị kéo dài. Nếu bạn chọn kiến trúc vi dịch vụ để mở rộng quy mô, bạn buộc phải đánh đổi tính nhất quán dữ liệu tức thời và chấp nhận tính phức tạp của hệ thống phân tán. Vì vậy, mục tiêu tối thượng của việc đánh giá kiến trúc là lượng hóa các thuộc tính chất lượng theo chuẩn ISO 25010 nhằm tìm ra điểm cân bằng tối ưu cho bài toán cụ thể."

---

### SLIDE 04: 1.2. CÁC PHƯƠNG PHÁP ĐÁNH GIÁ CHUẨN HÓA: ATAM & CBAM
- **Tiêu đề slide:** 1.2. CÁC KHUNG PHƯƠNG PHÁP ĐÁNH GIÁ: ATAM & CBAM (SEI)
- **Nội dung hiển thị:**
  + **Phương pháp ATAM (Architecture Tradeoff Analysis Method):**
    * Do Viện Kỹ thuật Phần mềm Mỹ (SEI) chuẩn hóa; là phương pháp đánh giá kiến trúc uy tín nhất thế giới.
    * Quy trình 4 pha, 9 bước: Phân tích mục tiêu nghiệp vụ $\rightarrow$ Xây dựng Quality Attribute Tree $\rightarrow$ Đánh giá kịch bản $\rightarrow$ Nhận diện rủi ro.
    * *3 Thành phần hạt nhân:* Kịch bản (Scenarios), Điểm nhạy cảm (Sensitivity Points), Điểm đánh đổi (Tradeoff Points).
  + **Phương pháp CBAM (Cost-Benefit Analysis Method):**
    * Bổ sung bài toán kinh tế lượng vào ATAM: Tính toán tỷ suất sinh lời ROI của từng quyết định kiến trúc.
  + **Ma trận Quyết định có Trọng số (Weighted Scoring Matrix):**
    * Lập bảng tiêu chí đánh giá, gán trọng số phần trăm ($W_i$) và chấm điểm phương án ứng viên để đưa ra kết luận định lượng.
- **Hình ảnh minh họa từ web:** Sơ đồ Quy trình 4 Pha & 9 Bước của Phương pháp Đánh giá Kiến trúc ATAM (SEI).
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Để việc so sánh kiến trúc không bị rơi vào tranh luận cảm tính, các kỹ sư phần mềm trên thế giới sử dụng phương pháp ATAM do Viện SEI phát triển. ATAM hoạt động dựa trên các 'Kịch bản' (Scenarios) cụ thể để tìm ra hai yếu tố then chốt: 'Điểm nhạy cảm' – tức là điểm mà một thay đổi nhỏ sẽ làm thay đổi lớn hiệu năng hệ thống, và 'Điểm đánh đổi' – nơi mà hai thuộc tính chất lượng xung đột nhau. Bên cạnh đó, phương pháp CBAM sẽ giúp ban lãnh đạo trả lời câu hỏi: Bỏ thêm 10.000 USD nâng cấp kiến trúc thì mang lại bao nhiêu giá trị nghiệp vụ? Đây là những công cụ tư duy sắc bén của một kiến trúc sư phần mềm chuyên nghiệp."

---

### SLIDE 05: 1.3. QUY TRÌNH 5 BƯỚC THỰC THI ĐÁNH GIÁ KIẾN TRÚC
- **Tiêu đề slide:** 1.3. QUY TRÌNH 5 BƯỚC THỰC THI ĐÁNH GIÁ KIẾN TRÚC
- **Nội dung hiển thị:**
  + **Bước 1: Trích xuất ASRs (Architecturally Significant Requirements)**
    * Sàng lọc các yêu cầu nghiệp vụ chi phối trực tiếp đến khung xương hệ thống.
  + **Bước 2: Xây dựng Danh mục Kiến trúc Ứng viên (Candidate Architectures)**
    * Đề xuất từ 2 đến 4 giải pháp kiến trúc khả dĩ (Monolith, Microservices, Serverless, Jamstack).
  + **Bước 3: Thiết lập Hệ thống Kịch bản Đánh giá (Scenarios)**
    * Xây dựng kịch bản tải bình thường, kịch bản quá tải giờ cao điểm, kịch bản lỗi phần cứng.
  + **Bước 4: Chấm điểm & Phân tích Đánh đổi (Trade-off Analysis)**
    * Áp dụng ma trận trọng số, phân tích ưu nhược điểm kỹ thuật và nhận diện các nút thắt cổ chai.
  + **Bước 5: Ban hành Hồ sơ Quyết định Kiến trúc (ADR - Architectural Decision Record)**
    * Đóng băng quyết định thiết kế, ghi nhận lý do lựa chọn và lưu trữ làm tài sản kỹ thuật của dự án.
- **Hình ảnh minh họa từ web:** Sơ đồ luồng tiến trình 5 bước đánh giá và lựa chọn giải pháp kiến trúc.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Trong thực tế dự án, quy trình đánh giá kiến trúc được chuẩn hóa thành 5 bước tuần tự như trên slide. Bắt đầu từ việc trích xuất các ASRs – tức là các yêu cầu kiến trúc trọng yếu. Sau đó nhóm sẽ đề xuất các kiến trúc ứng viên, đưa vào các kịch bản kiểm thử giả định, chấm điểm ma trận và quan trọng nhất là bước số 5: Ban hành tài liệu ADR. Tài liệu ADR chính là 'bản cam kết kỹ thuật' giải thích vì sao nhóm chọn kiến trúc này mà không chọn kiến trúc khác, giúp các lập trình viên vào sau hiểu được lý do thiết kế mà không tùy tiện phá vỡ cấu trúc."

---

### SLIDE 06: 1.4. ĐÁNH GIÁ KIẾN TRÚC DỰ ÁN QUẢN LÝ NHÀ HÀNG
- **Tiêu đề slide:** 1.4. ĐÁNH GIÁ KIẾN TRÚC THỰC TẾ: BÀI TOÁN & 4 PHƯƠNG ÁN ỨNG VIÊN
- **Nội dung hiển thị:**
  + **Bối cảnh dự án `php-restaurant`:**
    * Vận hành nhà hàng vừa và nhỏ: Quản lý kho, gọi món tại bàn qua QR, POS thu ngân, phân quyền 6 vai trò.
    * Ràng buộc cốt lõi: Chi phí hạ tầng tối thiểu, triển khai mượt mà trên môi trường nội bộ XAMPP/LAMP, bảo đảm toàn vẹn giao dịch ACID khi tạo phiếu nhập kho (`InventoryReceipt`).
  + **So sánh 4 Phương án Kiến trúc Ứng viên:**
    * *Phương án A (Monolithic MVC):* PHP Native + MySQL PDO + Bootstrap 5. Triển khai nhanh, giao dịch an toàn.
    * *Phương án B (Microservices):* Chia nhỏ 5 service độc lập, giao tiếp REST/Kafka. Khả năng mở rộng tuyệt vời nhưng chi phí hạ tầng quá lớn.
    * *Phương án C (Serverless FaaS):* AWS Lambda + DynamoDB NoSQL. Co giãn linh hoạt nhưng không tối ưu cho CSDL quan hệ nhiều bảng.
    * *Phương án D (SPA + Headless REST API):* React/Vue + PHP Backend. Trải nghiệm UI hiện đại nhưng tốn thời gian xây dựng 2 repository riêng biệt.
- **Hình ảnh minh họa từ web:** Sơ đồ so sánh tổng quan giữa Monolithic Architecture và Microservices Architecture.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Áp dụng vào thực tế dự án của nhóm: Chúng em xây dựng hệ thống quản lý nhà hàng php-restaurant. Nghiệp vụ của nhà hàng đòi hỏi tính toàn vẹn dữ liệu cực kỳ khắt khe: Khi thủ kho tạo một phiếu nhập kho gồm 20 nguyên liệu, hệ thống bắt buộc phải cập nhật đồng bộ bảng phiếu nhập và bảng tồn kho trong một Transaction duy nhất; nếu có lỗi phải hoàn tác ngay lập tức. Đứng trước bài toán này, nhóm đã đặt lên bàn cân 4 phương án kiến trúc: Monolithic MVC, Microservices, Serverless và mô hình SPA tách rời API. Mỗi phương án đều có thế mạnh riêng, nhưng phải xem xét kỹ mức độ phù hợp với quy mô thực tế của dự án."

---

### SLIDE 07: 1.4. MA TRẬN ĐÁNH ĐỔI & HỒ SƠ QUYẾT ĐỊNH ADR DỰ ÁN
- **Tiêu đề slide:** 1.4. MA TRẬN ĐÁNH ĐỔI & QUYẾT ĐỊNH KIẾN TRÚC (ADR-001)
- **Nội dung hiển thị:**
  + **Bảng Ma trận Chấm điểm Đánh đổi (Trích xuất từ báo cáo):**
    * Chi phí hạ tầng (20%): Monolith (9.5) vs Microservices (3.0)
    * Tốc độ ra mắt thị trường (20%): Monolith (9.0) vs Microservices (3.5)
    * Toàn vẹn giao dịch ACID kho (20%): Monolith (9.5) vs Microservices (4.0)
    * Khả năng mở rộng quy mô (10%): Monolith (5.0) vs Microservices (9.5)
    * **Tổng điểm chung cuộc:** Monolith MVC đạt **8.60/10** (Vượt trội so với Microservices 4.90 và Serverless 6.10).
  + **Nội dung Hồ sơ Quyết định Kiến trúc (ADR-001):**
    * *Quyết định:* Phê duyệt kiến trúc Monolithic 3-Tier MVC cho giai đoạn hiện tại.
    * *Ngưỡng chuyển đổi (Trigger Points):* Chỉ tái cấu trúc sang Microservices khi chuỗi vượt quá **15 chi nhánh** hoặc lưu lượng giờ cao điểm vượt quá **3.000 RPS**.
- **Hình ảnh minh họa từ web:** Biểu đồ Radar/Bar so sánh điểm số đánh đổi thuộc tính chất lượng giữa các kiến trúc.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Nhìn vào bảng ma trận đánh đổi trên slide, Thầy/Cô có thể thấy lý do vì sao nhóm lựa chọn kiến trúc Monolith MVC. Với 3 tiêu chí quan trọng nhất chiếm 60% trọng số là: Chi phí hạ tầng, Thời gian phát triển và Tính toàn vẹn ACID của phiếu nhập kho, Monolith PHP đạt điểm số gần như tuyệt đối (9.0 đến 9.5). Trong khi đó, Microservices mặc dù rất mạnh về khả năng mở rộng (9.5 điểm) nhưng lại nhận điểm rất thấp ở chi phí và độ phức tạp vận hành. Kết quả tổng điểm 8.60 đã khẳng định Monolith MVC là lựa chọn tối ưu và kinh tế nhất cho dự án hiện nay. Quyết định này đã được chính thức chuẩn hóa trong hồ sơ ADR-001 của nhóm."

---

### SLIDE 08: 2.1. KHÁI NIỆM "PHỔ MẪU THIẾT KẾ" (THE PATTERN SPECTRUM)
- **Tiêu đề slide:** 2.1. KHÁI NIỆM "PHỔ MẪU THIẾT KẾ" (THE PATTERN SPECTRUM)
- **Nội dung hiển thị:**
  + **Nguồn gốc học thuật:**
    * Khởi xướng trong bộ sách kinh điển *Pattern-Oriented Software Architecture (POSA)* của Buschmann, Meunier, Rohnert, Sommerlad, Stal.
  + **Định nghĩa "Phổ Mẫu":**
    * Một dải phân loại liên tục các mẫu phần mềm dựa trên mối tương quan giữa hai trục:
      - *Trục hoành (Level of Abstraction):* Từ mức trừu tượng hóa cao (khái niệm) đến mức cụ thể hóa (cú pháp).
      - *Trục tung (Scale and Scope):* Từ phạm vi toàn hệ thống xuống các module con và từng dòng lệnh.
  + **Ý nghĩa kỹ thuật:**
    * Giúp kỹ sư định vị chính xác vai trò của từng giải pháp thiết kế, tránh nhầm lẫn giữa cấu trúc tổng thể và mẹo lập trình chi tiết.
- **Hình ảnh minh họa từ web:** Sơ đồ Tháp Phân cấp The Pattern Spectrum trong Kỹ thuật Phần mềm (Buschmann et al.).
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Chuyển sang Phần 2, nhóm xin trình bày về một chủ đề rất sâu sắc trong lý thuyết kiến trúc phần mềm: 'Phân tích phổ trong thiết kế kiến trúc'. Khái niệm Phổ Mẫu (The Pattern Spectrum) được các tác giả cuốn sách POSA đưa ra nhằm giải quyết một sự nhầm lẫn phổ biến của các lập trình viên: Đó là đánh đồng giữa Mẫu Kiến trúc (Architectural Pattern) và Mẫu Thiết kế (Design Pattern). Phổ mẫu thiết lập một dải liên tục: Ở trên đỉnh cao nhất là các mẫu vĩ mô bao quát toàn hệ thống; ở giữa là các mẫu phối hợp đối tượng; và ở đáy là các thành ngữ cú pháp ngôn ngữ. Hiểu được phổ mẫu sẽ giúp chúng ta có cái nhìn toàn cảnh từ chiến lược đến chiến thuật."

---

### SLIDE 09: 2.2. BA TẦNG BẬC TRÊN DẢI PHỔ: MẪU KIẾN TRÚC (MACRO)
- **Tiêu đề slide:** 2.2. BA TẦNG BẬC TRÊN DẢI PHỔ: TẦNG 1 - MẪU KIẾN TRÚC (MACRO)
- **Nội dung hiển thị:**
  + **Vị trí trên phổ:** Cấp độ cao nhất (Macro-level) – Mức độ trừu tượng cao nhất & Phạm vi ảnh hưởng toàn hệ thống.
  + **Đặc trưng bản chất:**
    * Xác định cấu trúc khung xương (Skeleton) cơ bản của ứng dụng.
    * Phân định các hệ thống con (Subsystems), nhiệm vụ của từng phần và các ràng buộc giao tiếp bắt buộc (Constraints).
    * Hoàn toàn độc lập với ngôn ngữ lập trình cụ thể.
  + **Các đại diện kinh điển:**
    * *Model-View-Controller (MVC):* Tách rời Giao diện - Điều khiển - Dữ liệu.
    * *Layered Architecture:* Phân tầng đơn hướng (Presentation $\rightarrow$ Business $\rightarrow$ Data).
    * *Pipes and Filters, Broker, Event-Driven, Microservices.*
- **Hình ảnh minh họa từ web:** Sơ đồ Cấu trúc Chuẩn của Architectural Pattern (Mô hình MVC Process Diagram).
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Tầng đầu tiên trên đỉnh của phổ mẫu chính là Mẫu Kiến trúc (Architectural Patterns). Đây là những bản vẽ tổng mặt bằng của cả một tòa nhà phần mềm. Nó quy định hệ thống được chia thành mấy khối lớn, ví dụ mô hình MVC quy định tách thành Model, View và Controller. Điểm mấu chốt của tầng này là nó mang tính độc lập hoàn toàn với công nghệ. Dù quý vị lập trình bằng PHP, Java Spring Boot hay ASP.NET Core, thì các ràng buộc kiến trúc của mô hình MVC hay Layered vẫn hoàn toàn giữ nguyên giá trị."

---

### SLIDE 10: 2.2. BA TẦNG BẬC: MẪU THIẾT KẾ (MESO) & THÀNH NGỮ (MICRO)
- **Tiêu đề slide:** 2.2. BA TẦNG BẬC TRÊN DẢI PHỔ: TẦNG 2 & TẦNG 3 (MESO & MICRO)
- **Nội dung hiển thị:**
  + **Tầng 2: Mẫu Thiết kế (Design Patterns - Meso-level)**
    * *Phạm vi:* Cấu trúc lớp (Classes) và quan hệ giữa các đối tượng trong một module cụ thể.
    * *23 Mẫu GoF (Gang of Four):*
      - Creational (Khởi tạo): Factory Method, Singleton, Builder.
      - Structural (Cấu trúc): Adapter, Facade, Composite.
      - Behavioral (Hành vi): Strategy, Observer, Template Method.
    * *Đặc tính:* Độc lập ngôn ngữ nhưng gắn chặt với mô hình hướng đối tượng (OOP).
  + **Tầng 3: Mẫu Cài đặt / Thành ngữ Ngôn ngữ (Idioms - Micro-level)**
    * *Phạm vi:* Cấp độ dòng lệnh (Statement-level) bên trong một hàm hoặc phương thức.
    * *Đặc tính:* Gắn chặt trực tiếp với cú pháp và đặc tính của một ngôn ngữ lập trình cụ thể.
    * *Đại diện:* RAII (C++), Duck Typing (Python), PDO Prepared Statements & Magic Methods (PHP).
- **Hình ảnh minh họa từ web:** Bản đồ Phân loại 23 Mẫu Thiết kế GoF (Creational, Structural, Behavioral Design Patterns).
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Đi xuống tầng trung gian là Mẫu Thiết kế (Design Patterns). Nếu Architectural Pattern là bản vẽ toàn bộ ngôi nhà, thì Design Pattern là thiết kế chi tiết của một căn phòng – ví dụ như cách lắp đặt hệ thống điện nước hay cách bố trí nội thất. Đó là 23 mẫu GoF nổi tiếng như Singleton, Factory, hay Strategy. Cuối cùng, ở đáy phổ là Thành ngữ Ngôn ngữ (Idioms). Đây là những mẹo lập trình tinh tế gắn liền với từng ngôn ngữ cụ thể, ví dụ như trong PHP, việc sử dụng hàm PDO Prepared Statements để truyền tham số an toàn chính là một Idiom kinh điển giúp chống lại lỗ hổng SQL Injection."

---

### SLIDE 11: 2.3. MỐI QUAN HỆ TƯƠNG HỖ DỌC THEO DẢI PHỔ
- **Tiêu đề slide:** 2.3. MỐI QUAN HỆ TƯƠNG HỖ & CƠ CHẾ ÁNH XẠ TOP-DOWN
- **Nội dung hiển thị:**
  + **Cơ chế Ánh xạ Top-Down (Từ chiến lược đến dòng code):**
    * *Architectural Pattern* (Chọn mô hình MVC)  
      $\Downarrow$ định hướng
    * *Design Pattern* (Controller dùng Front Controller + Template Method; Model dùng Table Data Gateway; Auth dùng RBAC)  
      $\Downarrow$ hiện thực hóa
    * *Language Idiom* (PDO Prepared Statements, Session Flash Message, JWT Token Bearer).
  + **Phòng ngừa các rủi ro kiến trúc kinh điển:**
    * *Chống xói mòn kiến trúc (Architectural Erosion):* Cấm viết code SQL trực tiếp (Idiom) trong tệp View giao diện, ngăn chặn phá vỡ phân tầng MVC.
    * *Chống lạm dụng mẫu (Over-engineering):* Không cài cắm các Design Pattern phức tạp khi bài toán nghiệp vụ chỉ là CRUD cơ bản.
- **Hình ảnh minh họa từ web:** Sơ đồ Cây Phân rã Top-Down từ Architectural Pattern xuống Design Patterns và Idioms.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Một hệ thống phần mềm chất lượng cao là hệ thống có sự nhất quán tuyệt đối dọc theo dải phổ mẫu từ trên xuống dưới. Khi chúng ta đã quyết định áp dụng Architectural Pattern là MVC, thì ở tầng thiết kế, Controller bắt buộc phải tuân theo Template Method và Model phải tuân theo Table Data Gateway. Ở tầng cài đặt, lập trình viên không bao giờ được phép viết câu lệnh SQL trực tiếp vào tệp View HTML. Nếu lập trình viên vi phạm điều này, hiện tượng 'xói mòn kiến trúc' sẽ xảy ra, làm cho toàn bộ mô hình kiến trúc ban đầu bị sụp đổ chỉ sau một thời gian ngắn bảo trì."

---

### SLIDE 12: 2.4. BẢN ĐỒ PHỔ MẪU TRONG DỰ ÁN `PHP-RESTAURANT`
- **Tiêu đề slide:** 2.4. BẢN ĐỒ PHỔ MẪU CHIẾU VÀO SOURCE CODE `PHP-RESTAURANT`
- **Nội dung hiển thị:**
  + **Tầng 1 - Mẫu Kiến trúc (Macro):**
    * `core/App.php` + `core/Controller.php` + `core/Model.php`: Thực thi chuẩn mực kiến trúc Monolithic 3-Tier MVC.
  + **Tầng 2 - Mẫu Thiết kế (Meso):**
    * *Front Controller:* `core/App.php` đón nhận URL `/{controller}/{method}/{params}`.
    * *Template Method:* `core/Controller.php` cung cấp sẵn `view()`, `model()`, `requireRoles()`.
    * *Table Data Gateway:* `core/Model.php` đóng gói các hàm CRUD (`find`, `where`, `insert`, `update`).
    * *RBAC Pattern:* Phân quyền 6 vai trò bảo vệ các endpoint nhạy cảm (Tạo phiếu nhập kho, Sửa thực đơn).
    * *Strategy Pattern:* Hoán đổi phương thức thanh toán trong `SaleOrderController.php`.
  + **Tầng 3 - Thành ngữ Ngôn ngữ (Micro - PHP Idioms):**
    * *PDO Prepared Statements:* `$stmt = $this->db->prepare(...)` ngăn ngừa triệt để SQL Injection.
    * *Flash Session PRG:* Truyền thông báo trạng thái qua Post/Redirect/Get.
    * *JWT Bearer Extraction:* Đóng gói xác thực người dùng trong `helpers/JWT.php`.
- **Hình ảnh minh họa từ web:** Sơ đồ Cấu trúc Thư mục & Dòng chảy Dữ liệu Thực tế của Dự án `php-restaurant`.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Trên slide 12, nhóm em xin minh chứng trực tiếp bản đồ phổ mẫu qua chính cấu trúc mã nguồn của dự án php-restaurant. Ở tầng vĩ mô, chúng em có bộ khung core/App, core/Controller và core/Model tạo nên khung sườn MVC. Ở tầng trung mô, chúng em ứng dụng Front Controller để định tuyến URL, Template Method để tái sử dụng mã lệnh kiểm tra quyền requireRoles(), và RBAC để phân chia quyền lực cho 6 nhóm người dùng. Xuống tới từng dòng code PHP ở tầng vi mô, toàn bộ các truy vấn CSDL đều tuân thủ nghiêm ngặt Idiom PDO Prepared Statements. Nhờ có sự phân tầng mạch lạc này, dự án của chúng em đạt độ tin cậy và khả năng bảo trì rất cao."

---

### SLIDE 13: 3.1. BỐI CẢNH TIẾN HÓA CỦA KIẾN TRÚC HIỆN ĐẠI
- **Tiêu đề slide:** 3.1. BỐI CẢNH & ĐỘNG LỰC TIẾN HÓA CỦA KIẾN TRÚC HIỆN ĐẠI
- **Nội dung hiển thị:**
  + **Hành trình tiến hóa 3 giai đoạn:**
    * *Giai đoạn 1 (Truyền thống):* Monolithic Systems chạy trên Single Server.
    * *Giai đoạn 2 (Phân tán):* Service-Oriented Architecture (SOA) & Microservices Containerization (Docker, K8s).
    * *Giai đoạn 3 (Cloud-Native):* Event-Driven, Serverless FaaS, In-Memory Data Grid.
  + **3 Động lực công nghệ cốt lõi:**
    * *Khả năng co giãn đàn hồi (Elastic Scalability):* Tự động tăng giảm tài nguyên theo lưu lượng thực tế.
    * *Tính sẵn sàng cao (High Availability 99.99%):* Cô lập vùng lỗi, hệ thống không bao giờ sập toàn diện.
    * *Triển khai liên tục (CI/CD & Zero Downtime):* Đưa tính năng mới lên Production trong vài phút mà không cần bảo trì ngừng hệ thống.
- **Hình ảnh minh họa từ web:** Sơ đồ Lịch sử Tiến hóa Kiến trúc Phần mềm: Monolithic $\rightarrow$ SOA $\rightarrow$ Microservices $\rightarrow$ Cloud-Native Serverless.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Bước sang Phần 3, chúng ta cùng nhìn vào bức tranh toàn cảnh của các mẫu thiết kế kiến trúc hiện đại. Trong kỷ nguyên số hóa, phần mềm không còn chỉ phục vụ vài trăm nhân viên nội bộ, mà phải phục vụ hàng triệu người dùng cùng lúc trên Internet. Điều này đã thúc đẩy sự chuyển dịch mạnh mẽ từ các kiến trúc nguyên khối sang kiến trúc phân tán và Cloud-Native. Ba động lực sống còn thúc đẩy sự tiến hóa này chính là: Khả năng co giãn đàn hồi không giới hạn, tính sẵn sàng 99.99% không gián đoạn, và khả năng triển khai liên tục CI/CD mà không cần tắt server."

---

### SLIDE 14: 3.2.1. KIẾN TRÚC ĐA TẦNG HIỆN ĐẠI & KIẾN TRÚC SẠCH
- **Tiêu đề slide:** 3.2.1. CLEAN ARCHITECTURE & HEXAGONAL (PORTS & ADAPTERS)
- **Nội dung hiển thị:**
  + **Tác giả:** Robert C. Martin (Uncle Bob - 2012) & Alistair Cockburn.
  + **Quy tắc phụ thuộc hướng tâm (The Dependency Rule):**
    * Mọi quan hệ phụ thuộc mã nguồn chỉ được phép trỏ **từ ngoài vào trong**.
    * Lõi trung tâm: **Entities & Use Cases** (Nghiệp vụ thuần túy).
    * Vòng ngoài: Controllers, Gateways, UI, Database, Web Framework.
  + **Đặc tính ưu việt:**
    * *Độc lập Framework:* Không bị trói buộc vào Laravel, Spring hay Express.
    * *Độc lập CSDL:* Dễ dàng chuyển đổi từ MySQL sang MongoDB mà không sửa đổi một dòng code nghiệp vụ lõi.
    * *Khả năng kiểm thử tối đa (Testability):* Viết Unit Test cho toàn bộ logic nghiệp vụ mà không cần bật Database.
- **Hình ảnh minh họa từ web:** Sơ đồ Vòng tròn Đồng tâm Clean Architecture Kinh điển của Robert C. Martin (Uncle Bob).
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Mẫu kiến trúc hiện đại đầu tiên mà nhóm muốn giới thiệu là Clean Architecture của tác giả Robert C. Martin. Bản chất của Clean Architecture được gói gọn trong một quy tắc vàng: 'Quy tắc phụ thuộc một chiều từ ngoài vào trong'. Trái tim của ứng dụng là các thực thể Entities và các ca sử dụng Use Cases. Chúng hoàn toàn độc lập, không hề biết đến sự tồn tại của cơ sở dữ liệu hay giao diện web bên ngoài. CSDL hay Framework chỉ đóng vai trò như các cổng cắm (Adapters) vào hệ thống. Nhờ đó, chúng ta có thể kiểm thử toàn bộ logic nghiệp vụ một cách độc lập và thay thế công nghệ bên ngoài mà không làm ảnh hưởng đến lõi phần mềm."

---

### SLIDE 15: 3.2.2. KIẾN TRÚC VI DỊCH VỤ (MICROSERVICES)
- **Tiêu đề slide:** 3.2.2. KIẾN TRÚC VI DỊCH VỤ (MICROSERVICES ARCHITECTURE)
- **Nội dung hiển thị:**
  + **Bản chất kiến trúc:**
    * Chia nhỏ hệ thống thành tập hợp các dịch vụ nhỏ, tự chủ, phân chia theo ranh giới nghiệp vụ (Bounded Context theo DDD).
  + **Các nguyên tắc thiết kế bắt buộc:**
    * **Database-per-Service:** Mỗi service sở hữu CSDL độc lập riêng biệt.
    * **API Gateway:** Điểm đón nhận duy nhất cho Client, đảm nhận định tuyến, xác thực và cân bằng tải.
    * **Giao tiếp liên dịch vụ:** Giao tiếp nhẹ qua REST/gRPC (đồng bộ) hoặc Message Queue (bất đồng bộ).
    * **Khả năng chịu lỗi (Resilience):** Áp dụng mẫu Circuit Breaker ngăn chặn sụp đổ dây chuyền.
  + **Thách thức:** Quản lý giao dịch phân tán (Saga Pattern), độ trễ mạng và độ phức tạp vận hành cụm Container.
- **Hình ảnh minh họa từ web:** Sơ đồ Kiến trúc Microservices Chuẩn mực với API Gateway, Services và Database riêng biệt.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Mẫu kiến trúc thứ hai – và cũng là mẫu kiến trúc phổ biến nhất trong các doanh nghiệp công nghệ lớn hiện nay – chính là Microservices. Thay vì gom tất cả mã nguồn vào một khối duy nhất, Microservices xé nhỏ ứng dụng thành các dịch vụ độc lập. Điểm mấu chốt của Microservices là nguyên tắc 'Database-per-Service': Mỗi dịch vụ sở hữu cơ sở dữ liệu riêng, cấm tuyệt đối việc truy cập chéo. Để điều phối, hệ thống cần một API Gateway làm cửa ngõ duy nhất. Mặc dù Microservices cho phép mở rộng quy mô độc lập tuyệt vời, nhưng nó đòi hỏi năng lực DevOps rất cao để quản lý hàng chục container phân tán."

---

### SLIDE 16: 3.2.3. KIẾN TRÚC HƯỚNG SỰ KIỆN (EDA) & KHÔNG MÁY CHỦ (SERVERLESS)
- **Tiêu đề slide:** 3.2.3. EVENT-DRIVEN ARCHITECTURE (EDA) & SERVERLESS (FAAS)
- **Nội dung hiển thị:**
  + **Kiến trúc Hướng sự kiện (Event-Driven Architecture - EDA):**
    * Tương tác bất đồng bộ thông qua các Sự kiện nghiệp vụ (Events).
    * *3 Khối cốt lõi:* Event Producer $\rightarrow$ Event Broker (Apache Kafka / RabbitMQ) $\rightarrow$ Event Consumers.
    * *Mô hình nâng cao:* **Event Sourcing** (Lưu vết chuỗi sự kiện bất biến) & **CQRS** (Tách rời luồng ghi Command và luồng đọc Query).
  + **Kiến trúc Không máy chủ (Serverless Architecture - FaaS & BaaS):**
    * Lập trình viên chỉ viết mã hàm logic; Cloud Provider tự động cấp phát tài nguyên và quản lý máy chủ.
    * *Đặc tính đột phá:* Tự động co giãn từ 0 đến hàng ngàn instances; chi phí tính theo thời gian thực thi (Pay-per-use).
    * *Thách thức:* Hiện tượng Cold Start và phụ thuộc nhà cung cấp đám mây (Vendor Lock-in).
- **Hình ảnh minh họa từ web:** Sơ đồ Kiến trúc Hướng sự kiện (Pub/Sub Event Broker) & Mô hình Serverless FaaS Cloud.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Tiếp theo là hai mô hình đại diện cho xu hướng điện toán đám mây hiện đại: Event-Driven và Serverless. Trong kiến trúc hướng sự kiện, các thành phần không cần phải đợi nhau trả lời; khi có một sự kiện như 'Khách đặt món', một tin nhắn được bắn vào hàng đợi Kafka và các dịch vụ khác như Bếp hay Kho sẽ tự động đón nhận xử lý bất đồng bộ. Điều này giúp hệ thống đạt thông lượng cực kỳ khủng khiếp. Trong khi đó, Serverless đưa sự tự động hóa lên đỉnh cao: Quý vị không cần thuê máy chủ hàng tháng, chỉ khi nào có khách bấm gọi món thì một hàm FaaS trên đám mây mới khởi chạy và tính tiền trong vài mili-giây, giúp tiết kiệm chi phí tối đa cho doanh nghiệp."

---

### SLIDE 17: 3.3. MA TRẬN SO SÁNH TOÀN DIỆN CÁC MẪU HIỆN ĐẠI
- **Tiêu đề slide:** 3.3. MA TRẬN SO SÁNH TOÀN DIỆN 5 MẪU KIẾN TRÚC HIỆN ĐẠI
- **Nội dung hiển thị:**
  + **Bảng Ma trận So sánh Đa chiều (Tóm tắt từ nghiên cứu):**
    * *Clean Architecture:* Độ phức tạp thấp-vừa; Nhất quán ACID tuyệt đối; Chi phí thấp; Phù hợp ứng dụng nghiệp vụ lõi sâu.
    * *Microservices:* Khả năng mở rộng rất cao; Phức tạp DevOps cực cao; Nhất quán Eventual Consistency; Phù hợp doanh nghiệp lớn.
    * *Event-Driven (EDA):* Thông lượng cực lớn; Phù hợp hệ thống thời gian thực, IoT, xử lý luồng dữ liệu.
    * *Serverless (FaaS):* Co giãn tự động vô hạn; Chi phí ban đầu 0đ; Bị ảnh hưởng Cold Start; Phù hợp tác vụ đột biến.
    * *Space-Based (RAM):* Tốc độ micro-giây; Chi phí phần cứng RAM rất đắt; Phù hợp sàn chứng khoán, bán vé cao điểm.
- **Hình ảnh minh họa từ web:** Bảng Ma trận So sánh Tính năng & Thuộc tính Kỹ thuật giữa các Kiến trúc Hiện đại.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Slide 17 tổng hợp lại một bức tranh so sánh đa chiều giữa 5 phong cách kiến trúc hiện đại. Một lần nữa, chúng ta thấy rõ nguyên lý đánh đổi: Không có giải pháp nào hoàn hảo về mọi mặt. Nếu quý vị cần tốc độ phản hồi tính bằng micro-giây, Space-Based là số một nhưng chi phí mua RAM sẽ vô cùng đắt đỏ. Nếu quý vị muốn tối ưu hóa chi phí và nhân lực vận hành, Serverless hoặc Clean Architecture sẽ là lựa chọn khôn ngoan. Bảng ma trận này đóng vai trò như một kim chỉ nam giúp các kỹ sư công nghệ lựa chọn đúng vũ khí cho bài toán của mình."

---

### SLIDE 18: 3.4. ĐIỂM NGHẼN & THIẾT KẾ KIẾN TRÚC ĐÍCH CHO NHÀ HÀNG
- **Tiêu đề slide:** 3.4. ĐỊNH HƯỚNG ỨNG DỤNG: THIẾT KẾ KIẾN TRÚC ĐÍCH CHO DỰ ÁN
- **Nội dung hiển thị:**
  + **Nhận diện điểm nghẽn của Monolith hiện tại khi mở rộng chuỗi 50 nhà hàng:**
    * Giờ cao điểm (11h30 & 18h30): Hàng ngàn khách quét QR cùng lúc làm cạn kiệt MySQL connection pool, làm tê liệt chức năng POS và kiểm kho.
    * Thiếu kết nối thời gian thực: Dùng cơ chế Polling 5s gây lãng phí băng thông và tải server.
  + **Bản Thiết kế Kiến trúc Đích (Target Architecture - Mô hình Lai Microservices + EDA):**
    * *API Gateway (Kong):* Tiếp nhận và phân phối lưu lượng truy cập an toàn.
    * *Order & Table Service (Node.js/Go + Redis):* Chuyên trách phục vụ khách quét QR gọi món, chịu tải > 10.000 RPS.
    * *Kitchen Display Service (KDS):* Kết nối WebSocket thời gian thực đẩy món vào bếp tức thì.
    * *Inventory & Receipt Service (PHP Core):* Quản lý phiếu nhập kho (`InventoryReceipt`) và định lượng tồn kho với CSDL PostgreSQL ACID.
    * *Event Broker (Apache Kafka):* Điều phối các sự kiện `OrderPlaced`, `InventoryDeducted`.
- **Hình ảnh minh họa từ web:** Sơ đồ Kiến trúc Đích Đề xuất (Target Architecture Diagram) cho Hệ thống Chuỗi Nhà hàng.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Áp dụng những kiến thức hiện đại trên vào tương lai của dự án php-restaurant: Giả sử chuỗi nhà hàng của chúng em phát triển lên 50 chi nhánh. Vào giờ cao điểm trưa, hàng ngàn thực khách quét QR cùng lúc sẽ làm sập máy chủ MySQL của hệ thống Monolith hiện tại. Do đó, nhóm em đã thiết kế sẵn một Kiến trúc Đích (Target Architecture) kết hợp giữa Microservices và Event-Driven. Chúng em tách riêng module QR Order chạy bằng Node.js và Redis để gánh toàn bộ lưu lượng tải của khách hàng; module Bếp sử dụng WebSocket để báo món tức thời; trong khi module Nhập kho nguyên liệu cốt lõi vẫn kế thừa logic PHP vững chắc với CSDL PostgreSQL. Toàn bộ các dịch vụ được kết nối mượt mà qua luồng sự kiện của Apache Kafka."

---

### SLIDE 19: 3.4. LỘ TRÌNH CHUYỂN ĐỔI: STRANGLER FIG PATTERN
- **Tiêu đề slide:** 3.4. LỘ TRÌNH CHUYỂN ĐỔI AN TOÀN: STRANGLER FIG PATTERN
- **Nội dung hiển thị:**
  + **Mẫu Cây Đa Siết (Strangler Fig Pattern - Martin Fowler):**
    * Chiến lược chuyển đổi từng bước từ Monolith sang Microservices mà không làm gián đoạn kinh doanh của nhà hàng.
  + **Lộ trình 4 giai đoạn cụ thể:**
    * *Giai đoạn 1 (Thiết lập Gateway):* Đặt API Gateway đứng trước hệ thống Monolith PHP hiện tại; 100% traffic vẫn đi vào Monolith.
    * *Giai đoạn 2 (Tách module QR Order):* Viết mới dịch vụ QR Menu & Order bằng Node.js. Chuyển hướng traffic gọi món sang service mới; phần kho và kế toán giữ nguyên trên Monolith.
    * *Giai đoạn 3 (Tách module Bếp KDS & Thanh toán):* Tách tiếp màn hình bếp và tích hợp cổng VietQR qua Event Broker.
    * *Giai đoạn 4 (Triệt tiêu Monolith cũ):* Khi toàn bộ chức năng đã được chuyển đổi hoàn toàn sang các vi dịch vụ, Monolith cũ được gỡ bỏ an toàn.
- **Hình ảnh minh họa từ web:** Sơ đồ Tiến trình Chuyển đổi Kiến trúc bằng Strangler Fig Pattern của Martin Fowler.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Một sai lầm chết người mà nhiều dự án mắc phải là 'đập đi xây lại' toàn bộ hệ thống cũ – điều này dễ dẫn đến thảm họa ngừng trệ kinh doanh. Nhóm em lựa chọn chiến lược Strangler Fig Pattern – tức là 'Mẫu cây đa siết' do Martin Fowler đề xuất. Chúng em sẽ trồng một 'cây đa mới' bằng cách đặt một API Gateway phía trước, sau đó bóc tách từng cành nhánh – bắt đầu từ tính năng quét QR gọi món. Khi tính năng mới chạy ổn định, chúng em chuyển hướng dần lưu lượng sang đó, giữ nguyên hệ thống cũ cho các nghiệp vụ nội bộ. Cứ như vậy, hệ thống mới sẽ lớn dần và thay thế hoàn toàn hệ thống cũ một cách êm đẹp, bảo đảm chuỗi nhà hàng luôn mở cửa kinh doanh 24/7."

---

### SLIDE 20: TỔNG KẾT BÀI HỌC & HỎI ĐÁP (CONCLUSION & Q&A)
- **Tiêu đề slide:** TỔNG KẾT BÀI HỌC & HỎI ĐÁP (CONCLUSION & Q&A)
- **Nội dung hiển thị:**
  + **3 Thông điệp Cốt lõi của Báo cáo:**
    1. *Đánh giá kiến trúc là quá trình liên tục:* Không có kiến trúc tốt nhất, chỉ có kiến trúc phù hợp nhất với giai đoạn phát triển của doanh nghiệp (Từ Monolith MVC $\rightarrow$ Hybrid Microservices khi đạt ngưỡng tải).
    2. *Phổ mẫu bảo đảm tính nhất quán:* Tư duy theo dải phổ giúp gắn kết chặt chẽ giữa Chiến lược kiến trúc (Macro) $\rightarrow$ Thiết kế hướng đối tượng (Meso) $\rightarrow$ Dòng lệnh an toàn (Micro Idiom).
    3. *Kiến trúc phải phục vụ kinh doanh:* Mọi quyết định kỹ thuật phải hướng tới tối ưu hóa giá trị vận hành và trải nghiệm khách hàng của nhà hàng.
  + **Phần Hỏi & Đáp (Q&A):**
    * Xin trân trọng cảm ơn Thầy/Cô và các bạn đã chú ý lắng nghe!
    * Nhóm sẵn sàng giải đáp các câu hỏi phản biện.
- **Hình ảnh minh họa từ web:** Hình ảnh biểu tượng Thank You & Question Mark chuyên nghiệp.
- **Lời thoại thuyết trình (Speaker Notes):**
  > "Kính thưa Thầy/Cô, thông điệp cuối cùng mà nhóm muốn đúc kết qua bài báo cáo hôm nay chính là: Kiến trúc phần mềm không phải là một mô hình tĩnh trên trang giấy, mà là một sinh thể sống đồng hành cùng sự phát triển của doanh nghiệp. Việc chúng em chọn Monolith MVC cho dự án php-restaurant ở thời điểm hiện tại là hoàn toàn đúng đắn về mặt kỹ thuật và chi phí; đồng thời việc chúng em chuẩn bị sẵn lộ trình Strangler Fig để tiến hóa sang Microservices thể hiện tầm nhìn dài hạn của người kỹ sư phần mềm. Nhóm xin chân thành cảm ơn Thầy/Cô và các bạn đã lắng nghe. Chúng em rất mong nhận được những nhận xét và câu hỏi đóng góp quý báu từ Thầy/Cô!"
