import os
import sys

cur_dir = os.path.dirname(os.path.abspath(__file__))
if cur_dir not in sys.path:
    sys.path.insert(0, cur_dir)

from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx_helpers import (
    add_heading_with_spacing, add_body_p, add_bullet_p,
    add_callout, style_table, add_image_safe
)

print("Starting generation of CHUONG_3_THIET_KE_KIEN_TRUC.docx...")

doc = Document()

# Set standard page margins (1 inch = 72pt)
for section in doc.sections:
    section.top_margin = Inches(1.0)
    section.bottom_margin = Inches(1.0)
    section.left_margin = Inches(1.0)
    section.right_margin = Inches(1.0)

# ==========================================
# COVER PAGE / HEADER
# ==========================================
p_inst = doc.add_paragraph()
p_inst.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_inst.paragraph_format.space_before = Pt(0)
p_inst.paragraph_format.space_after = Pt(2)
r = p_inst.add_run("BỘ GIÁO DỤC VÀ ĐÀO TẠO — TRƯỜNG ĐẠI HỌC CÔNG NGHỆ\nKHOA CÔNG NGHỆ THÔNG TIN — BỘ MÔN KỸ THUẬT PHẦN MỀM\n")
r.bold = True
r.font.name = "Arial"
r.font.size = Pt(11)
r.font.color.rgb = RGBColor(0x33, 0x33, 0x33)

p_rule = doc.add_paragraph()
p_rule.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_rule.paragraph_format.space_before = Pt(0)
p_rule.paragraph_format.space_after = Pt(36)
r_rule = p_rule.add_run("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━")
r_rule.font.name = "Arial"
r_rule.font.color.rgb = RGBColor(0x1F, 0x4E, 0x79)

p_report = doc.add_paragraph()
p_report.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_report.paragraph_format.space_before = Pt(12)
p_report.paragraph_format.space_after = Pt(6)
r_rep = p_report.add_run("BÁO CÁO NGHIÊN CỨU & THUYẾT MINH CHUYÊN SÂU\nHỌC PHẦN: KIẾN TRÚC VÀ THIẾT KẾ PHẦN MỀM")
r_rep.bold = True
r_rep.font.name = "Arial"
r_rep.font.size = Pt(13)
r_rep.font.color.rgb = RGBColor(0x2E, 0x75, 0xB6)

p_title = doc.add_paragraph()
p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_title.paragraph_format.space_before = Pt(12)
p_title.paragraph_format.space_after = Pt(12)
r_title = p_title.add_run("CHƯƠNG 3: THIẾT KẾ KIẾN TRÚC PHẦN MỀM\n")
r_title.bold = True
r_title.font.name = "Arial"
r_title.font.size = Pt(22)
r_title.font.color.rgb = RGBColor(0x0F, 0x2A, 0x4A)

r_sub = p_title.add_run(
    "Nghiên Cứu Chuyên Sâu 3 Chủ Đề:\n"
    "1. Đánh Giá Các Kiến Trúc Thay Thế (Mục 3.2.3)\n"
    "2. Phân Tích Phổ Trong Thiết Kế Kiến Trúc (Mục 3.3.1)\n"
    "3. Một Số Mẫu Thiết Kế Kiến Trúc Phổ Biến & Hiện Đại (Mục 3.3.2)\n"
    "Soi Chiếu và Minh Chứng Trực Tiếp Vào Dự Án: Hệ Thống Quản Lý Nhà Hàng (cimonwork-design/php-restaurant)"
)
r_sub.italic = True
r_sub.font.name = "Arial"
r_sub.font.size = Pt(11)
r_sub.font.color.rgb = RGBColor(0x47, 0x55, 0x69)

add_callout(
    doc,
    "Tài liệu này được biên soạn bám sát giáo trình chuẩn quốc tế của SEI (Software Engineering Institute), "
    "POSA (Pattern-Oriented Software Architecture của Frank Buschmann), Martin Fowler và Roger S. Pressman. "
    "Mọi cơ sở lý thuyết đều được đối chiếu trực tiếp với mã nguồn và cơ sở dữ liệu thực tế của dự án php-restaurant "
    "(PHP Native MVC, PDO MySQL, JWT Authentication, RBAC 6 vai trò, Quản lý kho nguyên liệu và Đặt món QR).",
    "THÔNG ĐIỆP HỌC THUẬT & PHẠM VI NGHIÊN CỨU"
)

p_meta = doc.add_paragraph()
p_meta.paragraph_format.space_before = Pt(24)
p_meta.paragraph_format.space_after = Pt(24)
p_meta.alignment = WD_ALIGN_PARAGRAPH.CENTER
r_m = p_meta.add_run("Dự án nghiên cứu thực nghiệm: cimonwork-design/php-restaurant\nNăm học: 2026 – 2027")
r_m.font.name = "Arial"
r_m.font.size = Pt(10)
r_m.font.color.rgb = RGBColor(0x66, 0x66, 0x66)

doc.add_page_break()

# ==========================================
# PHẦN 1: ĐÁNH GIÁ CÁC KIẾN TRÚC THAY THẾ (3.2.3)
# ==========================================
add_heading_with_spacing(doc, "PHẦN 1: ĐÁNH GIÁ CÁC KIẾN TRÚC THAY THẾ (MỤC 3.2.3)", level=1)

add_heading_with_spacing(doc, "1.1. Bản chất, Khái niệm và Mục tiêu của việc Đánh giá Kiến trúc Thay thế", level=2)

add_body_p(
    doc,
    "Trong kỹ thuật phần mềm chuẩn mực, thiết kế kiến trúc là giai đoạn đưa ra những quyết định kỹ thuật nền tảng có chi phí "
    "sửa đổi đắt đỏ nhất trong toàn bộ vòng đời phát triển phần mềm. Theo giáo trình kinh điển của Roger S. Pressman và bộ chuẩn "
    "của Viện Kỹ thuật Phần mềm Mỹ (Software Engineering Institute - SEI), đánh giá kiến trúc thay thế (Evaluating Alternative "
    "Architectural Designs) được định nghĩa là quá trình kiểm định, so sánh có hệ thống giữa các phương án cấu trúc ứng viên nhằm "
    "tìm ra giải pháp thỏa hiệp tối ưu nhất giữa các mục tiêu kinh doanh và các thuộc tính chất lượng phần mềm trong phạm vi ràng buộc "
    "nghiêm ngặt về ngân sách, năng lực nhân sự và tiến độ thời gian."
)

add_body_p(
    doc,
    "Một chân lý mang tính nguyên lý trong kiến trúc phần mềm mà mọi kỹ sư cần quán triệt là: Không tồn tại một kiến trúc hoàn hảo "
    "tuyệt đối hay vượt trội trên mọi khía cạnh. Mọi kiến trúc phần mềm chỉ là một tập hợp các sự đánh đổi có chủ đích (Trade-offs). "
    "Nếu hệ thống được tối ưu hóa cho mục tiêu mở rộng cực hạn (như chuyển toàn bộ sang Microservices), hệ thống sẽ phải trả giá bằng "
    "tính phức tạp vận hành tăng vọt, chi phí hạ tầng lớn và mất đi tính nhất quán dữ liệu tức thời (phải chấp nhận Eventual Consistency). "
    "Ngược lại, nếu chọn kiến trúc đơn khối (Monolith), hệ thống đạt tính toàn vẹn giao dịch ACID hoàn hảo và chi phí cực thấp, nhưng "
    "khả năng co giãn độc lập của từng module sẽ bị giới hạn."
)

add_bullet_p(doc, "Giảm thiểu rủi ro kỹ thuật trước khi bước vào giai đoạn viết mã hàng loạt, ngăn chặn nguy cơ phải đập đi xây lại hệ thống.", "Mục tiêu 1 - Kiểm soát rủi ro: ")
add_bullet_p(doc, "Tối ưu hóa tổng chi phí sở hữu (Total Cost of Ownership - TCO) bao gồm chi phí mua sắm hạ tầng đám mây và chi phí bảo trì.", "Mục tiêu 2 - Tối ưu kinh tế: ")
add_bullet_p(doc, "Bảo đảm đáp ứng trọn vẹn các thuộc tính chất lượng (Quality Attributes) theo tiêu chuẩn quốc tế ISO/IEC 25010.", "Mục tiêu 3 - Cam kết chất lượng: ")

add_image_safe(doc, "images_chuong3/iso_25010.jpg", "Cây Thuộc tính Chất lượng Phần mềm Tiêu chuẩn Quốc tế ISO/IEC 25010", width_in=5.6)

add_heading_with_spacing(doc, "1.2. Các Phương pháp và Khung Đánh giá Kiến trúc Chuẩn hóa", level=2)

add_body_p(
    doc,
    "Để việc so sánh kiến trúc không bị trượt vào những cuộc tranh luận cảm tính, kỹ thuật phần mềm thế giới đã chuẩn hóa các khung phương pháp đánh giá định lượng và bán định lượng uy tín:"
)

add_bullet_p(
    doc,
    "Phương pháp Phân tích Đánh đổi Kiến trúc do Viện Kỹ thuật Phần mềm Mỹ (SEI) phát triển. Đây là phương pháp phổ biến và toàn diện nhất thế giới. "
    "ATAM hoạt động dựa trên 4 pha và 9 bước, xoay quanh các khái niệm then chốt: Kịch bản kiểm thử (Scenarios: Use-case, Growth, Exploratory), "
    "Điểm nhạy cảm (Sensitivity Points - yếu tố cấu hình mà thay đổi nhỏ sẽ tác động lớn đến chất lượng), và Điểm đánh đổi (Tradeoff Points - vị trí mà hai thuộc tính chất lượng xung đột nhau).",
    "1. Khung đánh giá ATAM (Architecture Tradeoff Analysis Method): "
)

add_bullet_p(
    doc,
    "Phương pháp Phân tích Chi phí - Lợi ích. Mở rộng từ ATAM, CBAM đưa bài toán kinh tế lượng vào kiến trúc: Tính toán tỷ suất sinh lời (ROI) "
    "giữa giá trị nghiệp vụ gia tăng so với chi phí tiền bạc cần bỏ ra để hiện thực hóa kiến trúc đó, giúp doanh nghiệp tránh bẫy đầu tư thừa (Over-engineering).",
    "2. Khung đánh giá CBAM (Cost-Benefit Analysis Method): "
)

add_bullet_p(
    doc,
    "Phương pháp Phân tích Kiến trúc Phần mềm tập trung chủ yếu vào việc đánh giá khả năng sửa đổi (Modifiability) và mở rộng tính năng mới thông qua phân tích ma trận tác động.",
    "3. Khung đánh giá SAAM (Software Architecture Analysis Method): "
)

add_bullet_p(
    doc,
    "Phương pháp lập bảng tiêu chí, gán trọng số phần trăm dựa trên độ ưu tiên thực tế của dự án và chấm điểm định lượng từng phương án ứng viên để đưa ra quyết định minh bạch.",
    "4. Kỹ thuật Ma trận Quyết định có Trọng số (Weighted Scoring Matrix): "
)

add_image_safe(doc, "images_chuong3/architecture_activities.jpg", "Quy trình Các Hoạt động Đánh giá và Phân tích Kiến trúc Phần mềm", width_in=5.0)

add_heading_with_spacing(doc, "1.3. Quy trình 5 Bước Thực thi Đánh giá Kiến trúc trong Kỹ thuật Phần mềm", level=2)

add_body_p(doc, "Một quy trình đánh giá kiến trúc chuẩn mực trong doanh nghiệp được tiến hành qua 5 bước nghiêm ngặt:")
add_bullet_p(doc, "Lọc ra các yêu cầu phi chức năng có tính quyết định sống còn đối với kiến trúc hệ thống thông qua phỏng vấn Product Owner và các bên liên quan.", "Bước 1 - Khai phá ASRs: ")
add_bullet_p(doc, "Thiết kế sơ bộ từ 2 đến 4 giải pháp cấu trúc khả thi (Ví dụ: Monolith MVC, Microservices, Serverless, Jamstack).", "Bước 2 - Đề xuất Kiến trúc Ứng viên: ")
add_bullet_p(doc, "Xây dựng kịch bản vận hành bình thường, kịch bản tải đột biến giờ cao điểm, kịch bản lỗi mạng và kịch bản nâng cấp phiên bản.", "Bước 3 - Thiết lập Kịch bản Kiểm thử: ")
add_bullet_p(doc, "Họp hội đồng kỹ thuật, đối chiếu từng phương án với hệ thống kịch bản, lập ma trận chấm điểm và nhận diện các điểm nghẽn rủi ro.", "Bước 4 - Chấm điểm & Phân tích Đánh đổi: ")
add_bullet_p(doc, "Ban hành văn bản đóng băng quyết định kiến trúc chính thức, lưu trữ làm tài sản kỹ thuật và làm căn cứ triển khai cho đội ngũ lập trình.", "Bước 5 - Lập Hồ sơ Quyết định Kiến trúc (ADR): ")

add_heading_with_spacing(doc, "1.4. Đánh giá Kiến trúc Thực tế trong Dự án php-restaurant", level=2)

add_body_p(
    doc,
    "Dự án cimonwork-design/php-restaurant là hệ thống quản lý nhà hàng ăn uống phục vụ quy trình khép kín: Khách hàng quét mã QR tại bàn gọi món; "
    "Bếp nhận đơn chế biến; Thu ngân chốt bill thanh toán; Thủ kho quản lý phiếu nhập kho nguyên liệu (InventoryReceipt); và Quản lý xem báo cáo doanh thu. "
    "Đặc thù nghiệp vụ đặt ra yêu cầu giao dịch ACID cực kỳ khắt khe: Khi lập phiếu nhập kho gồm hàng chục nguyên liệu, thông tin phiếu nhập "
    "và số lượng tồn kho (stock_quantity) của bảng ingredient bắt buộc phải cập nhật đồng bộ trong một Transaction duy nhất; nếu có bất kỳ lỗi nào xảy ra "
    "thì toàn bộ thao tác phải được rollback hoàn toàn để không gây sai lệch sổ sách kế toán."
)

add_body_p(doc, "Nhóm phát triển đã đưa ra 4 phương án ứng viên để tiến hành đánh giá đối chiếu:")
add_bullet_p(doc, "Toàn bộ module chạy chung một tiến trình PHP trên máy chủ Apache/MySQL, phân chia 3 tầng Presentation - Application - Data.", "Phương án A (Monolithic 3-Tier MVC - PHP): ")
add_bullet_p(doc, "Chia nhỏ thành 5 dịch vụ độc lập: Auth, Order QR, Kitchen KDS, Inventory, Payment. Giao tiếp qua REST API và Kafka.", "Phương án B (Microservices Phân tán): ")
add_bullet_p(doc, "Backend phân rã thành các hàm AWS Lambda FaaS, cơ sở dữ liệu NoSQL DynamoDB, giao diện tĩnh lưu trên S3.", "Phương án C (Serverless Cloud FaaS): ")
add_bullet_p(doc, "Giao diện React/Vue độc lập (Client-side Rendering) gọi về hệ thống máy chủ cung cấp RESTful API.", "Phương án D (Single Page Application + REST API): ")

add_image_safe(doc, "images_chuong3/tradeoff_cap_theorem.png", "Phân tích Đánh đổi Kiến trúc & Định lý CAP (Consistency vs Availability vs Partition Tolerance)", width_in=5.4)

# Bảng Ma trận Trade-off
headers_tradeoff = [
    "Tiêu chí Đánh giá (Quality Attributes)", "Trọng số", "PA A: Monolith MVC", "PA B: Microservices", "PA C: Serverless", "PA D: SPA + API"
]
widths_tradeoff = [2.2, 0.6, 1.0, 1.0, 0.9, 0.8]
data_tradeoff = [
    ["1. Chi phí Triển khai & Hạ tầng", "20%", "9.5 (Chạy mượt XAMPP/VPS)", "3.0 (Chi phí K8s/Cloud rất cao)", "6.0 (Chi phí tính theo request)", "7.5 (Cần 2 hosting FE/BE)"],
    ["2. Tốc độ Phát triển (Time-to-Market)", "20%", "9.0 (Cấu trúc gọn, code nhanh)", "3.5 (Tốn thời gian dựng RPC/CI)", "5.5 (Khó khăn debug local)", "7.0 (Phải làm 2 repo riêng)"],
    ["3. Toàn vẹn Giao dịch ACID Kho", "20%", "9.5 (MySQL Transaction an toàn)", "4.0 (Distributed Saga phức tạp)", "5.0 (NoSQL khó bảo đảm ACID)", "9.0 (Backend SQL xử lý ACID)"],
    ["4. Khả năng Mở rộng (Scalability)", "10%", "5.0 (Scale theo chiều dọc)", "9.5 (Scale độc lập từng service)", "9.5 (Auto-scale tự động vô hạn)", "7.0 (Scale độc lập Backend)"],
    ["5. Độ phức tạp Vận hành & DevOps", "10%", "9.0 (Bảo trì đơn giản, 1 DB duy nhất)", "2.5 (Cần chuyên gia K8s/DevOps)", "5.0 (Phụ thuộc nhà cung cấp Cloud)", "6.5 (Cần quản lý CORS/2 build)"],
    ["6. Khả năng Bảo trì Module", "10%", "8.0 (Chuẩn MVC mạch lạc)", "9.0 (Cô lập mã nguồn tuyệt đối)", "6.0 (Phân mảnh hàng chục hàm)", "8.5 (Tách biệt UI và Logic)"],
    ["7. An toàn & Phân quyền Bảo mật", "10%", "8.5 (JWT Stateless + Session + PDO)", "8.0 (Phức tạp mTLS giữa service)", "7.5 (Cần cấu hình IAM policies)", "8.0 (JWT Bearer Token chuẩn)"],
    ["TỔNG ĐIỂM CHUNG CUỘC CÓ TRỌNG SỐ", "100%", "8.60 (LỰA CHỌN TỐI ƯU)", "4.90", "6.10", "7.60"]
]

tbl_tradeoff = doc.add_table(rows=1, cols=6)
style_table(tbl_tradeoff, widths_tradeoff, headers_tradeoff, data_tradeoff)

doc.add_paragraph().paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "MÃ HỒ SƠ: ADR-001 | TRẠNG THÁI: ĐÃ PHÊ DUYỆT (ACCEPTED)\n"
    "• Bối cảnh: Dự án cần hệ thống quản lý vận hành ổn định trong mạng nội bộ LAN của nhà hàng ngay cả khi mất kết nối Internet "
    "quốc tế, chi phí triển khai ban đầu bằng 0 hoặc rất thấp, đội ngũ phát triển tinh gọn 2-4 thành viên.\n"
    "• Quyết định: Lựa chọn Phương án A - Monolithic 3-Tier MVC trên nền tảng PHP Native và MySQL PDO.\n"
    "• Biện minh: Đảm bảo giao dịch ACID tuyệt đối khi tạo phiếu nhập kho (InventoryReceipt); chạy ổn định trên gói XAMPP nội bộ; "
    "tối ưu hóa 100% thời gian cho tính năng nghiệp vụ thay vì cấu hình hạ tầng.\n"
    "• Ngưỡng chuyển đổi kiến trúc (Trigger Points): Chỉ tái cấu trúc sang Microservices khi chuỗi vượt quá 15 chi nhánh hoặc lưu lượng "
    "gọi món quét QR giờ cao điểm vượt quá 3.000 requests/giây (RPS).",
    "HỒ SƠ QUYẾT ĐỊNH KIẾN TRÚC CHÍNH THỨC (ADR-001)"
)

doc.add_page_break()

# ==========================================
# PHẦN 2: PHÂN TÍCH PHỔ TRONG THIẾT KẾ KIẾN TRÚC (3.3.1)
# ==========================================
add_heading_with_spacing(doc, "PHẦN 2: PHÂN TÍCH PHỔ TRONG THIẾT KẾ KIẾN TRÚC (MỤC 3.3.1)", level=1)

add_heading_with_spacing(doc, "2.1. Khái niệm và Định nghĩa Phổ Mẫu Thiết Kế (The Pattern Spectrum)", level=2)

add_body_p(
    doc,
    "Khái niệm 'Phổ Mẫu' (The Pattern Spectrum) được khởi xướng bởi nhóm tác giả Frank Buschmann, Regine Meunier, Hans Rohnert, "
    "Peter Sommerlad, và Michael Stal trong bộ sách kinh điển Pattern-Oriented Software Architecture (POSA). Trong thực tế kỹ thuật, "
    "các lập trình viên thường mắc sai lầm nghiêm trọng là đánh đồng mọi giải pháp thiết kế vào cùng một khái niệm 'pattern' chung chung. "
    "Họ không phân biệt được sự khác nhau giữa việc chia hệ thống thành 3 tầng (Kiến trúc vĩ mô) với việc sử dụng mẫu Factory để tạo đối tượng "
    "(Thiết kế trung mô), hay việc sử dụng con trỏ an toàn trong C++ (Thành ngữ vi mô)."
)

add_body_p(
    doc,
    "Phổ Mẫu thiết lập một hệ tọa độ liên tục dựa trên hai trục đánh giá cốt lõi: "
    "Trục hoành biểu thị Mức độ Trừu tượng (Level of Abstraction) - đi từ khái niệm chiến lược độc lập công nghệ đến cú pháp cụ thể; "
    "và Trục tung biểu thị Quy mô và Phạm vi Ảnh hưởng (Scale & Scope) - đi từ toàn bộ hệ thống (System-wide) xuống các module, cấu trúc lớp "
    "và từng dòng lệnh cụ thể. Việc phân tích phổ giúp người kỹ sư định vị chính xác vị trí của từng giải pháp kỹ thuật, bảo đảm tính nhất quán "
    "trong toàn bộ vòng đời phần mềm."
)

add_heading_with_spacing(doc, "2.2. Ba Tầng Bậc Cốt lõi trên Dải Phổ Mẫu", level=2)

add_bullet_p(
    doc,
    "Nằm ở đỉnh cao nhất của phổ, định hình khung sườn cơ bản của toàn bộ hệ thống phần mềm. Xác định các hệ thống con (Subsystems), "
    "nhiệm vụ của từng khối và các ràng buộc giao tiếp bắt buộc (Constraints). Mẫu kiến trúc hoàn toàn độc lập với ngôn ngữ lập trình. "
    "Ví dụ tiêu biểu: Model-View-Controller (MVC), Layered Architecture, Pipes & Filters, Broker, Event-Driven Architecture, Microservices.",
    "Tầng Vĩ mô - Mẫu Kiến trúc (Architectural Patterns): "
)

add_bullet_p(
    doc,
    "Nằm ở tầng trung gian của phổ, cung cấp giải pháp tối ưu cho việc tổ chức cấu trúc lớp (Classes), giao diện (Interfaces) và cơ chế phối hợp "
    "giữa các đối tượng trong một module con mà không định hình toàn bộ hệ thống. Thường được phân loại theo 23 mẫu kinh điển của Gang of Four (GoF): "
    "Creational (Factory, Singleton), Structural (Adapter, Facade), Behavioral (Strategy, Observer, Template Method).",
    "Tầng Trung mô - Mẫu Thiết kế (Design Patterns - GoF): "
)

add_bullet_p(
    doc,
    "Nằm ở đáy của phổ, là các mẫu thiết kế ở cấp độ dòng lệnh gắn chặt với các đặc trưng cú pháp và cơ chế đặc thù của một ngôn ngữ lập trình cụ thể. "
    "Ví dụ: Cú pháp RAII trong C++, Duck Typing trong Python, cú pháp PDO Prepared Statements và Magic Methods trong PHP.",
    "Tầng Vi mô - Thành ngữ Ngôn ngữ (Idioms / Implementation Patterns): "
)

add_image_safe(doc, "images_chuong3/mvc_process.png", "Mô hình Mẫu Kiến trúc Vĩ mô Model-View-Controller (MVC Process Flow)", width_in=5.2)

add_heading_with_spacing(doc, "2.3. Mối Quan hệ Tương hỗ và Cơ chế Ánh xạ Top-Down Dọc theo Dải Phổ", level=2)

add_body_p(
    doc,
    "Dải phổ mẫu không phải là các ngăn chứa rời rạc, mà vận hành như một dòng chảy chuyển giao liên tục theo cơ chế Top-Down (từ trên xuống): "
    "Khi tầng vĩ mô quyết định sử dụng Architectural Pattern là MVC, tầng trung mô lập tức đưa ra chiến thuật: Controller áp dụng Front Controller "
    "kết hợp Template Method, Model áp dụng Table Data Gateway, phân quyền áp dụng RBAC. Xuống đến tầng vi mô, các chiến thuật này được hiện thực hóa "
    "bằng các idiom an toàn của PHP như PDO Prepared Statements và Session Flash Message."
)

add_body_p(
    doc,
    "Phân tích phổ là vũ khí đắc lực để phòng chống hiện tượng Xói mòn Kiến trúc (Architectural Erosion). Hiện tượng này xảy ra khi lập trình viên "
    "tùy tiện dùng idiom cấp thấp để giải quyết công việc ngắn hạn nhưng phá vỡ ràng buộc cấp cao (Ví dụ: viết trực tiếp câu lệnh truy vấn SQL "
    "ngay bên trong file View giao diện). Hành động này biến hệ thống thành khối mã nguồn 'mì ăn liền' không thể kiểm thử và không thể bảo trì."
)

add_heading_with_spacing(doc, "2.4. Bản đồ Phổ Mẫu Trực tiếp trong Dự án php-restaurant", level=2)

add_body_p(
    doc,
    "Dự án cimonwork-design/php-restaurant là minh chứng sống động cho việc ứng dụng chuẩn hóa toàn bộ dải phổ mẫu vào thực tế mã nguồn. "
    "Từng dòng code trong thư mục core/ và app/ đều có vị trí định danh chính xác trên phổ:"
)

# Bảng Mapping Phổ Mẫu
headers_spectrum = ["Tầng Phổ Mẫu", "Tên Mẫu Ứng dụng", "Tệp Mã nguồn Minh chứng", "Trách nhiệm Kỹ thuật Cụ thể trong Dự án"]
widths_spectrum = [1.5, 1.6, 1.8, 1.6]
data_spectrum = [
    ["Mẫu Kiến trúc (Macro)", "Monolithic 3-Tier MVC", "core/App.php, core/Controller.php, core/Model.php", "Định hình cấu trúc toàn bộ hệ thống, phân chia ranh giới rành mạch giữa Presentation, Application và Data."],
    ["Mẫu Thiết kế (Meso)", "Front Controller Pattern", "index.php, .htaccess, core/App.php", "Điểm tiếp nhận tập trung, phân tích định dạng URL /{controller}/{method}/{params} để gọi đúng hàm xử lý."],
    ["Mẫu Thiết kế (Meso)", "Template Method Pattern", "core/Controller.php", "Cung cấp khung xương tái sử dụng: view(), model(), jsonResponse(), requireRoles()."],
    ["Mẫu Thiết kế (Meso)", "Table Data Gateway Pattern", "core/Model.php, app/models/InventoryReceipt.php", "Đóng gói các thao tác CRUD cơ sở (find, where, insert, update), cách ly mã SQL khỏi tầng điều khiển."],
    ["Mẫu Thiết kế (Meso)", "RBAC (Phân quyền 6 vai trò)", "core/Controller.php, helpers/JWT.php", "Kiểm soát quyền truy cập chặt chẽ cho Admin, Manager, Staff, Cashier, Chef, Waiter."],
    ["Mẫu Thiết kế (Meso)", "Strategy Pattern (Thanh toán)", "app/controllers/SaleOrderController.php", "Hoán đổi linh hoạt giữa các thuật toán thanh toán: Tiền mặt, Chuyển khoản QR ngân hàng, Quẹt thẻ POS."],
    ["Thành ngữ (Micro)", "PDO Prepared Statements", "core/Model.php, config/database.php", "Tách rời câu lệnh SQL và tham số dữ liệu (:val), triệt tiêu 100% nguy cơ tấn công SQL Injection."],
    ["Thành ngữ (Micro)", "Flash Session PRG", "app/controllers/InventoryReceiptController.php", "Lưu thông điệp trạng thái qua Session và chuyển hướng trang (Post/Redirect/Get) an toàn."],
    ["Thành ngữ (Micro)", "Stateless Bearer JWT", "helpers/JWT.php, config/jwt.php", "Giải mã chữ ký điện tử HMAC-SHA256 trên HTTP Header để xác thực người dùng mà không tốn RAM server."]
]

tbl_spectrum = doc.add_table(rows=1, cols=4)
style_table(tbl_spectrum, widths_spectrum, headers_spectrum, data_spectrum)

doc.add_paragraph().paragraph_format.space_after = Pt(6)

doc.add_page_break()

# ==========================================
# PHẦN 3: MỘT SỐ MẪU THIẾT KẾ KIẾN TRÚC HIỆN ĐẠI (3.3.2)
# ==========================================
add_heading_with_spacing(doc, "PHẦN 3: MỘT SỐ MẪU THIẾT KẾ KIẾN TRÚC PHỔ BIẾN (HIỆN ĐẠI) (MỤC 3.3.2)", level=1)

add_heading_with_spacing(doc, "3.1. Bối cảnh và Động lực Tiến hóa của Kiến trúc Phần mềm Hiện đại", level=2)

add_body_p(
    doc,
    "Sự bùng nổ của thiết bị di động, điện toán đám mây và dữ liệu lớn đã đưa ngành công nghệ phần mềm bước vào kỷ nguyên Cloud-Native. "
    "Các ứng dụng hiện đại không còn giới hạn phục vụ nội bộ vài trăm người, mà phải đối mặt với hàng chục ngàn người dùng truy cập đồng thời, "
    "đòi hỏi tính sẵn sàng 99.99% (High Availability) và khả năng bàn giao tính năng liên tục (CI/CD) mà không phải dừng hệ thống (Zero Downtime). "
    "Chính những áp lực này đã thúc đẩy sự ra đời và thống trị của 5 mẫu thiết kế kiến trúc hiện đại tiêu biểu."
)

add_heading_with_spacing(doc, "3.2. Phân tích Chuyên sâu 5 Mẫu Thiết kế Kiến trúc Hiện đại", level=2)

add_heading_with_spacing(doc, "3.2.1. Kiến trúc Đa tầng Hiện đại & Kiến trúc Sạch (Clean / Hexagonal Architecture)", level=3)
add_body_p(
    doc,
    "Được khởi xướng bởi Robert C. Martin (Uncle Bob) và Alistair Cockburn, Clean Architecture lấy Nguyên lý Đảo ngược Phụ thuộc (Dependency Inversion) "
    "làm kim chỉ nam. Trọng tâm của kiến trúc là quy tắc: 'Mọi phụ thuộc mã nguồn chỉ được phép trỏ từ ngoài vào trong'. Lõi trung tâm là Entities "
    "(Quy tắc nghiệp vụ doanh nghiệp) và Use Cases (Nghiệp vụ ứng dụng). Lõi này hoàn toàn tinh khiết, không phụ thuộc vào Framework, không biết UI là gì "
    "và không quan tâm CSDL là MySQL hay MongoDB. Các thành phần bên ngoài kết nối vào lõi thông qua các Cổng giao tiếp (Ports & Adapters). "
    "Kiến trúc này mang lại khả năng viết Unit Test độc lập 100% mà không cần kết nối cơ sở dữ liệu."
)
add_image_safe(doc, "images_chuong3/clean_architecture.jpg", "Sơ đồ Vòng tròn Đồng tâm Clean Architecture của Robert C. Martin (Uncle Bob)", width_in=5.2)

add_heading_with_spacing(doc, "3.2.2. Kiến trúc Vi dịch vụ (Microservices Architecture)", level=3)
add_body_p(
    doc,
    "Microservices chia nhỏ ứng dụng thành tập hợp các dịch vụ độc lập, tự chủ, phân chia theo ranh giới nghiệp vụ (Bounded Context). "
    "Hai nguyên tắc sống còn của Microservices là: (1) Database per Service - mỗi dịch vụ sở hữu cơ sở dữ liệu riêng, cấm truy cập chéo; "
    "và (2) API Gateway - điểm tiếp nhận tập trung duy nhất cho mọi thiết bị khách, đảm nhận cân bằng tải, định tuyến và kiểm tra xác thực. "
    "Microservices cho phép co giãn đàn hồi độc lập từng dịch vụ và triển khai tính năng mới độc lập mà không ảnh hưởng tới toàn hệ thống."
)
add_image_safe(doc, "images_chuong3/microservices_arch.png", "Kiến trúc Vi dịch vụ (Microservices) với API Gateway và Database per Service", width_in=5.4)

add_heading_with_spacing(doc, "3.2.3. Kiến trúc Hướng sự kiện (Event-Driven Architecture - EDA)", level=3)
add_body_p(
    doc,
    "Trong kiến trúc EDA, các thành phần không gọi trực tiếp nhau theo cơ chế Request/Response đồng bộ, mà giao tiếp thông qua việc phát sinh "
    "và tiêu thụ các Sự kiện nghiệp vụ (Events) qua một Event Broker tốc độ cao (như Apache Kafka, RabbitMQ). Kiến trúc này giúp hệ thống đạt độ "
    "khớp nối lỏng lẻo tối đa (Loose Coupling) và thông lượng xử lý cực lớn. Hai mô hình con đỉnh cao của EDA là Event Sourcing (lưu trữ toàn bộ "
    "chuỗi biến động trạng thái dưới dạng luồng sự kiện bất biến) và CQRS (tách rời hoàn toàn luồng ghi dữ liệu và luồng đọc dữ liệu)."
)
add_image_safe(doc, "images_chuong3/event_driven_queue.png", "Kiến trúc Hướng sự kiện (Event-Driven) và Hàng đợi Bất đồng bộ (Message Queue)", width_in=5.4)

add_heading_with_spacing(doc, "3.2.4. Kiến trúc Không máy chủ (Serverless Architecture - FaaS & BaaS)", level=3)
add_body_p(
    doc,
    "Serverless giải phóng hoàn toàn lập trình viên khỏi gánh nặng quản trị máy chủ. Kỹ sư chỉ tập trung viết mã logic dưới dạng các hàm độc lập "
    "(Function as a Service - FaaS như AWS Lambda, Cloudflare Workers). Nền tảng đám mây tự động co giãn từ 0 đến hàng ngàn instances trong tích tắc "
    "và tính tiền chính xác theo từng mili-giây hàm chạy (Pay-per-use). Không có người dùng đồng nghĩa với chi phí hạ tầng bằng 0 đồng."
)

add_heading_with_spacing(doc, "3.2.5. Kiến trúc Dựa trên Không gian Bộ nhớ (Space-Based / In-Memory Architecture)", level=3)
add_body_p(
    doc,
    "Space-Based Architecture xóa bỏ hoàn toàn nút thắt cổ chai lớn nhất của các ứng dụng web truyền thống là Ổ cứng và Cơ sở dữ liệu quan hệ "
    "bằng cách phân tán toàn bộ dữ liệu đang giao dịch trực tiếp trên bộ nhớ RAM của một cụm máy chủ (In-Memory Data Grid như Redis Cluster, Hazelcast). "
    "Hệ thống đạt tốc độ phản hồi tính bằng micro-giây, cực kỳ phù hợp cho các sàn giao dịch chứng khoán hoặc hệ thống đặt vé flash-sale cao điểm."
)
add_image_safe(doc, "images_chuong3/inmemory_cache.png", "Kiến trúc Bộ nhớ Đệm Phân tán In-Memory Xóa bỏ Điểm nghẽn CSDL", width_in=5.0)

add_heading_with_spacing(doc, "3.3. Bảng Ma trận So sánh Toàn diện 5 Mẫu Kiến trúc Hiện đại", level=2)

# Bảng So sánh 5 Mẫu Hiện đại
headers_modern = ["Tiêu chí Kỹ thuật", "Clean Architecture", "Microservices", "Event-Driven (EDA)", "Serverless (FaaS)", "Space-Based (RAM)"]
widths_modern = [1.6, 1.0, 1.0, 1.0, 1.0, 0.9]
data_modern = [
    ["Bản chất Cốt lõi", "Đảo ngược phụ thuộc, độc lập Framework", "Phân tán dịch vụ theo Bounded Context", "Trao đổi sự kiện bất đồng bộ qua Broker", "Mã hàm phi trạng thái, Cloud quản lý máy chủ", "Phân tán dữ liệu trên RAM, xóa nghẽn DB"],
    ["Khả năng Mở rộng (Scale)", "Trung bình (Scale khối ứng dụng)", "Rất cao (Scale độc lập từng service)", "Cực cao (Xử lý thông lượng stream)", "Tự động vô hạn (Co giãn từ 0 đến N pods)", "Cực cao (Mở rộng cụm RAM tuyến tính)"],
    ["Nhất quán Dữ liệu (Consistency)", "Rất cao (ACID trong phạm vi DB)", "Eventual Consistency (Saga)", "Eventual Consistency (Saga)", "Phụ thuộc CSDL đám mây (DynamoDB/SQL)", "Rất cao (Đồng bộ trên RAM tức thời)"],
    ["Độ Phức tạp Vận hành (DevOps)", "Thấp - Vừa (Triển khai như Monolith)", "Cực cao (K8s, CI/CD, Tracing)", "Cao (Vận hành cụm Kafka/RabbitMQ)", "Thấp (Nhà cung cấp Cloud quản lý)", "Rất cao (Quản trị bộ nhớ RAM phân tán)"],
    ["Độ Trễ Phản hồi (Latency)", "Cực thấp (Lời gọi hàm in-memory)", "Trung bình (Tốn độ trễ mạng Network HOP)", "Thấp - Vừa (Xử lý bất đồng bộ)", "Trung bình (Bị ảnh hưởng Cold Start)", "Siêu thấp (Tính bằng Micro-giây)"],
    ["Phù hợp nhất với Loại hình", "Ứng dụng nghiệp vụ lõi sâu, cần test kỹ", "Doanh nghiệp lớn, nhiều nhóm dev độc lập", "Hệ thống thời gian thực, IoT, tài chính", "Tác vụ nền, webhook, tải thất thường", "Sàn chứng khoán, bán vé concert flash-sale"]
]

tbl_modern = doc.add_table(rows=1, cols=6)
style_table(tbl_modern, widths_modern, headers_modern, data_modern)

doc.add_paragraph().paragraph_format.space_after = Pt(6)

add_heading_with_spacing(doc, "3.4. Định hướng Ứng dụng & Lộ trình Tiến hóa Kiến trúc cho Dự án php-restaurant", level=2)

add_body_p(
    doc,
    "Khi chuỗi nhà hàng php-restaurant nhân rộng lên 50 chi nhánh, kiến trúc Monolith PHP hiện tại sẽ gặp điểm nghẽn nghiêm trọng vào giờ cao điểm "
    "(11h30 và 18h30): Hàng ngàn thực khách quét QR gọi món cùng lúc làm cạn kiệt MySQL connection pool, gây tê liệt chức năng thu ngân POS và nhập kho. "
    "Do đó, nhóm kiến trúc sư đề xuất Kiến trúc Đích (Target Architecture) theo mô hình lai: Microservices kết hợp Event-Driven (EDA)."
)

add_bullet_p(doc, "Xây dựng bằng Node.js/Go + Redis Cache. Chuyên trách phục vụ khách quét QR gọi món tại bàn, chịu tải > 10.000 RPS với độ trễ < 20ms.", "1. Order & Table Service: ")
add_bullet_p(doc, "Kết nối WebSocket hai chiều với máy tính bảng trong bếp, nhảy chuông báo món tức thời mà không cần Polling tải lại trang.", "2. Kitchen Display Service (KDS): ")
add_bullet_p(doc, "Kế thừa mã nguồn PHP MVC nâng cấp, quản lý phiếu nhập kho (InventoryReceipt) và công thức món (recipe) với CSDL PostgreSQL ACID.", "3. Inventory & Receipt Service: ")
add_bullet_p(doc, "Tích hợp cổng VietQR, MoMo, VNPay với Outbox Pattern chống mất mát trạng thái giao dịch.", "4. Payment & Billing Service: ")
add_bullet_p(doc, "Hạ tầng Apache Kafka điều phối luồng sự kiện OrderCreatedEvent, PaymentCompletedEvent, InventoryDepletedEvent.", "5. Event Broker: ")

add_image_safe(doc, "images_chuong3/microservices_app_layer.png", "Sơ đồ Kiến trúc Đích Đề xuất cho Hệ thống Chuỗi Nhà hàng (Target Architecture)", width_in=5.4)

add_callout(
    doc,
    "CHIẾN LƯỢC CHUYỂN ĐỔI KHÔNG GIÁN ĐOẠN KINH DOANH: STRANGLER FIG PATTERN (MARTIN FOWLER)\n"
    "Nhóm tuyệt đối không sử dụng phương pháp 'Đập đi xây lại từ đầu' (Big Bang Rewrite - có tỷ lệ thất bại lên tới 80%). "
    "Thay vào đó, nhóm áp dụng Mẫu Cây Đa Siết (Strangler Fig Pattern):\n"
    "1. Giai đoạn 1: Đặt một API Gateway (Kong / NGINX) đứng trước hệ thống Monolith PHP hiện tại. Toàn bộ traffic vẫn đi vào Monolith.\n"
    "2. Giai đoạn 2: Tách riêng module QR Menu & Order sang service mới (Node.js). Cấu hình API Gateway chuyển hướng lưu lượng gọi món sang service mới; "
    "các chức năng nhập kho và kế toán vẫn chạy trên Monolith cũ.\n"
    "3. Giai đoạn 3: Tiếp tục tách module Bếp KDS và module Cổng thanh toán kết nối qua Kafka Event Broker.\n"
    "4. Giai đoạn 4: Khi toàn bộ các module đã hoạt động ổn định trên hạ tầng phân tán mới, khối Monolith cũ chính thức được giải phóng an toàn.",
    "LỘ TRÌNH TIẾN HÓA KIẾN TRÚC NHÀ HÀNG"
)

add_image_safe(doc, "images_chuong3/api_gateway_proxy.png", "Cơ chế API Gateway & Reverse Proxy Điều phối Dòng chảy Chuyển đổi Kiến trúc", width_in=4.8)

# ==========================================
# PHẦN 4: KỊCH BẢN VÀ HƯỚNG DẪN THUYẾT TRÌNH SLIDE (20 SLIDES)
# ==========================================
add_heading_with_spacing(doc, "PHẦN 4: KỊCH BẢN VÀ HƯỚNG DẪN THUYẾT TRÌNH SLIDE (20 SLIDES CHUẨN HÓA)", level=1)

add_body_p(
    doc,
    "Phần này cung cấp toàn bộ nội dung hiển thị, cấu trúc phân cấp (1.1, 1.2... 2.1, 2.2... 3.1, 3.2...), gợi ý hình ảnh minh họa thật lấy từ web, "
    "và lời thoại thuyết trình chuẩn mực (Speaker Notes) cho 20 slide báo cáo của nhóm."
)

slides_summary_data = [
    ["Slide 01", "Trang Tiêu Đề Báo Cáo", "Chương 3: Thiết kế Kiến trúc Phần mềm | Đánh giá kiến trúc thay thế - Phân tích phổ - Kiến trúc hiện đại | Dự án php-restaurant."],
    ["Slide 02", "Lộ Trình Báo Cáo (Agenda)", "Cấu trúc 3 phần lớn gồm 12 tiểu mục phân cấp logic từ 1.1 đến 3.4 kèm minh chứng mã nguồn thực tế."],
    ["Slide 03", "1.1. Bản Chất & Thuộc Tính Chất Lượng", "Nguyên lý Trade-offs; Giảm thiểu rủi ro kỹ thuật; Cây thuộc tính chất lượng phần mềm theo chuẩn ISO/IEC 25010."],
    ["Slide 04", "1.2. Khung Phương Pháp ATAM & CBAM", "Quy trình 4 pha 9 bước của ATAM (SEI); Kịch bản (Scenarios), Điểm nhạy cảm (Sensitivity), Điểm đánh đổi (Tradeoff); CBAM & ROI."],
    ["Slide 05", "1.3. Quy Trình 5 Bước Đánh Giá Kiến Trúc", "Khai phá ASRs -> Đề xuất ứng viên -> Xây dựng kịch bản -> Chấm điểm ma trận trọng số -> Ban hành hồ sơ quyết định ADR."],
    ["Slide 06", "1.4. Đánh Giá Kiến Trúc Dự Án Nhà Hàng", "Bối cảnh dự án php-restaurant; Yêu cầu ACID nghiêm ngặt khi tạo phiếu nhập kho; Đề xuất 4 kiến trúc ứng viên."],
    ["Slide 07", "1.4. Ma Trận Đánh Đổi & Quyết Định ADR-001", "Bảng chấm điểm 7 tiêu chí: Monolith MVC đạt 8.60/10 điểm tối ưu; Phê duyệt ADR-001 và thiết lập các Trigger Points."],
    ["Slide 08", "2.1. Khái Niệm The Pattern Spectrum", "Nguồn gốc bộ sách POSA (Buschmann et al.); Hai trục tọa độ: Mức độ trừu tượng vs Quy mô phạm vi ảnh hưởng."],
    ["Slide 09", "2.2. Tầng Vĩ Mô: Mẫu Kiến Trúc (Macro)", "Định hình khung xương toàn hệ thống; Độc lập ngôn ngữ lập trình; Đại diện: MVC, Layered, Pipes & Filters, Broker."],
    ["Slide 10", "2.2. Tầng Trung Mô & Vi Mô (Meso & Micro)", "Tầng 2: 23 Mẫu thiết kế GoF (Creational, Structural, Behavioral); Tầng 3: Thành ngữ ngôn ngữ (PHP PDO Prepared Statements)."],
    ["Slide 11", "2.3. Mối Quan Hệ Tương Hỗ Dọc Phổ", "Cơ chế ánh xạ Top-Down (Chiến lược kiến trúc -> Thiết kế lớp -> Cú pháp); Phòng chống xói mòn kiến trúc & over-engineering."],
    ["Slide 12", "2.4. Bản Đồ Phổ Mẫu Trong Dự Án php-restaurant", "Chiếu trực tiếp 3 tầng vào source code: Monolith MVC -> Front Controller, RBAC, Strategy -> PDO Prepared Statements, JWT."],
    ["Slide 13", "3.1. Bối Cảnh Tiến Hóa Kiến Trúc Hiện Đại", "Hành trình 3 giai đoạn (Monolith -> SOA/Microservices -> Cloud-Native); 3 động lực: Elastic scale, High availability, CI/CD."],
    ["Slide 14", "3.2.1. Clean Architecture & Hexagonal", "Quy tắc phụ thuộc hướng tâm của Uncle Bob; Lõi Entities & Use Cases độc lập Framework và CSDL; Ports & Adapters."],
    ["Slide 15", "3.2.2. Kiến Trúc Vi Dịch Vụ (Microservices)", "Cấu trúc phân tán theo Bounded Context; Database per Service; API Gateway; Giao tiếp gRPC/REST; Mẫu Circuit Breaker."],
    ["Slide 16", "3.2.3. Kiến Trúc Hướng Sự Kiện & Serverless", "EDA: Event Producer -> Kafka Broker -> Consumers; Event Sourcing & CQRS; Serverless FaaS (AWS Lambda) tính phí theo ms."],
    ["Slide 17", "3.3. Ma Trận So Sánh 5 Mẫu Hiện Đại", "Bảng so sánh đa chiều 5 mẫu kiến trúc hiện đại theo độ phức tạp, khả năng mở rộng, tính nhất quán, độ trễ và chi phí."],
    ["Slide 18", "3.4. Điểm Nghẽn & Kiến Trúc Đích Cho Nhà Hàng", "Phân tích điểm nghẽn giờ cao điểm của chuỗi 50 nhà hàng; Thiết kế Target Architecture (Hybrid Microservices + EDA)."],
    ["Slide 19", "3.4. Lộ Trình Chuyển Đổi: Strangler Fig Pattern", "Mẫu Cây Đa Siết của Martin Fowler: 4 giai đoạn di chuyển từng bước an toàn không gián đoạn kinh doanh của chuỗi nhà hàng."],
    ["Slide 20", "Tổng Kết Bài Học & Hỏi Đáp (Q&A)", "3 thông điệp đúc kết: Đánh giá kiến trúc liên tục; Phổ mẫu đảm bảo tính nhất quán; Kiến trúc phải phụ vụ bài toán kinh doanh."]
]

headers_sl = ["Slide #", "Tiêu đề Slide", "Nội dung Trọng tâm & Minh họa"]
widths_sl = [1.0, 2.2, 3.3]
tbl_sl = doc.add_table(rows=1, cols=3)
style_table(tbl_sl, widths_sl, headers_sl, slides_summary_data)

doc.add_paragraph().paragraph_format.space_after = Pt(12)

# ==========================================
# TÀI LIỆU THAM KHẢO
# ==========================================
add_heading_with_spacing(doc, "TÀI LIỆU THAM KHẢO CHUẨN MỰC", level=1)
add_bullet_p(doc, "Roger S. Pressman, Bruce R. Maxim (2020), Software Engineering: A Practitioner's Approach, 9th Edition, McGraw-Hill Education.")
add_bullet_p(doc, "Frank Buschmann, Regine Meunier, Hans Rohnert, Peter Sommerlad, Michael Stal (1996), Pattern-Oriented Software Architecture (POSA): A System of Patterns, Volume 1, John Wiley & Sons.")
add_bullet_p(doc, "Len Bass, Paul Clements, Rick Kazman (2021), Software Architecture in Practice, 4th Edition, Addison-Wesley Professional (SEI Series in Software Engineering).")
add_bullet_p(doc, "Robert C. Martin (Uncle Bob) (2017), Clean Architecture: A Craftsman's Guide to Software Structure and Design, Prentice Hall.")
add_bullet_p(doc, "Martin Fowler (2002), Patterns of Enterprise Application Architecture, Addison-Wesley Professional.")
add_bullet_p(doc, "International Organization for Standardization (2011), ISO/IEC 25010: Systems and software engineering — Systems and software Quality Requirements and Evaluation (SQuaRE) — System and software quality models.")
add_bullet_p(doc, "Mã nguồn dự án thực nghiệm: GitHub cimonwork-design/php-restaurant (PHP MVC, MySQL PDO, JWT Authentication).")

doc_output_path = "CHUONG_3_THIET_KE_KIEN_TRUC.docx"
doc.save(doc_output_path)
print(f"SUCCESS: Generated {doc_output_path} ({os.path.getsize(doc_output_path)} bytes)")
