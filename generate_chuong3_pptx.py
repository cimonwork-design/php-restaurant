import os
import sys
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.enum.shapes import MSO_SHAPE

print("Starting generation of Bao_Cao_Chuong_3_Thiet_Ke_Kien_Truc.pptx...")

prs = Presentation()
prs.slide_width = Inches(13.333)
prs.slide_height = Inches(7.5)

# Color Palette Constants
COLOR_NAVY_DARK   = RGBColor(15, 32, 67)      # #0F2043 (Deep Navy Title)
COLOR_NAVY_BLUE   = RGBColor(31, 78, 121)     # #1F4E79 (Primary Blue)
COLOR_TEAL_ACCENT = RGBColor(14, 116, 144)    # #0E7490 (Cyan/Teal Accent)
COLOR_AMBER_GOLD  = RGBColor(217, 119, 6)     # #D97706 (Amber/Gold Highlight)
COLOR_SLATE_DARK  = RGBColor(30, 41, 59)      # #1E293B (Body Text Dark)
COLOR_SLATE_MUTED = RGBColor(100, 116, 139)   # #64748B (Muted text)
COLOR_BG_LIGHT    = RGBColor(248, 250, 252)   # #F8FAFC (Card Background)
COLOR_CARD_BORDER = RGBColor(226, 232, 240)   # #E2E8F0 (Border)
COLOR_WHITE       = RGBColor(255, 255, 255)   # #FFFFFF

blank_slide_layout = prs.slide_layouts[6]

def add_header(slide, section_tag, main_title, subtitle=None):
    # Top accent bar
    top_bar = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0), Inches(0), Inches(13.333), Inches(0.1))
    top_bar.fill.solid()
    top_bar.fill.fore_color.rgb = COLOR_NAVY_BLUE
    top_bar.line.fill.background()

    # Header container
    tb = slide.shapes.add_textbox(Inches(0.8), Inches(0.35), Inches(11.7), Inches(1.1))
    tf = tb.text_frame
    tf.word_wrap = True
    tf.margin_left = tf.margin_top = tf.margin_right = tf.margin_bottom = 0

    # Section tag
    p_tag = tf.paragraphs[0]
    p_tag.space_after = Pt(2)
    r_tag = p_tag.add_run()
    r_tag.text = section_tag.upper()
    r_tag.font.name = "Arial"
    r_tag.font.size = Pt(9.5)
    r_tag.font.bold = True
    r_tag.font.color.rgb = COLOR_AMBER_GOLD

    # Main title
    p_title = tf.add_paragraph()
    p_title.space_after = Pt(2)
    r_title = p_title.add_run()
    r_title.text = main_title
    r_title.font.name = "Arial"
    r_title.font.size = Pt(19)
    r_title.font.bold = True
    r_title.font.color.rgb = COLOR_NAVY_DARK

    # Subtitle
    if subtitle:
        p_sub = tf.add_paragraph()
        r_sub = p_sub.add_run()
        r_sub.text = subtitle
        r_sub.font.name = "Arial"
        r_sub.font.size = Pt(11)
        r_sub.font.italic = True
        r_sub.font.color.rgb = COLOR_SLATE_MUTED

def add_footer(slide, slide_num):
    tb = slide.shapes.add_textbox(Inches(0.8), Inches(7.0), Inches(11.7), Inches(0.35))
    tf = tb.text_frame
    tf.margin_left = tf.margin_top = tf.margin_right = tf.margin_bottom = 0
    p = tf.paragraphs[0]
    
    r1 = p.add_run()
    r1.text = "Học phần: Kiến trúc và Thiết kế Phần mềm — Báo cáo Chương 3: Thiết kế Kiến trúc | Dự án: php-restaurant"
    r1.font.name = "Arial"
    r1.font.size = Pt(8.5)
    r1.font.color.rgb = COLOR_SLATE_MUTED

    r2 = p.add_run()
    r2.text = f"             Slide {slide_num:02d} / 20"
    r2.font.name = "Arial"
    r2.font.size = Pt(8.5)
    r2.font.bold = True
    r2.font.color.rgb = COLOR_NAVY_BLUE

def add_speaker_note(slide, note_text):
    notes_slide = slide.notes_slide
    text_frame = notes_slide.notes_text_frame
    text_frame.text = note_text

def add_bullet_point(tf, bold_title, text_content, level=0, space_after=6):
    p = tf.add_paragraph() if len(tf.paragraphs[0].text) > 0 else tf.paragraphs[0]
    p.level = level
    p.space_after = Pt(space_after)
    p.line_spacing = 1.15
    
    if bold_title:
        r_bold = p.add_run()
        r_bold.text = bold_title + " "
        r_bold.font.name = "Arial"
        r_bold.font.bold = True
        r_bold.font.size = Pt(11.5) if level == 0 else Pt(10.5)
        r_bold.font.color.rgb = COLOR_NAVY_BLUE if level == 0 else COLOR_SLATE_DARK
        
    r_text = p.add_run()
    r_text.text = text_content
    r_text.font.name = "Arial"
    r_text.font.size = Pt(11) if level == 0 else Pt(10)
    r_text.font.color.rgb = COLOR_SLATE_DARK

def create_card(slide, left, top, width, height, bg_color=COLOR_BG_LIGHT, border_color=COLOR_CARD_BORDER):
    card = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, left, top, width, height)
    card.fill.solid()
    card.fill.fore_color.rgb = bg_color
    card.line.color.rgb = border_color
    card.line.width = Pt(1)
    return card

def add_image_card(slide, left, top, width, height, img_path, caption):
    card = create_card(slide, left, top, width, height, bg_color=COLOR_WHITE, border_color=COLOR_CARD_BORDER)
    
    if os.path.exists(img_path):
        try:
            # Add picture inside card area
            img_top = top + Inches(0.15)
            img_height = height - Inches(0.7)
            img_left = left + Inches(0.15)
            img_width = width - Inches(0.3)
            
            pic = slide.shapes.add_picture(img_path, img_left, img_top, width=img_width)
            # Center vertically if needed
            if pic.height > img_height:
                pic.height = int(img_height)
            
            # Caption
            cap_top = top + height - Inches(0.45)
            cap_tb = slide.shapes.add_textbox(left + Inches(0.1), cap_top, width - Inches(0.2), Inches(0.4))
            cap_tf = cap_tb.text_frame
            cap_tf.word_wrap = True
            cap_p = cap_tf.paragraphs[0]
            cap_p.alignment = PP_ALIGN.CENTER
            cap_run = cap_p.add_run()
            cap_run.text = f"Minh họa: {caption}"
            cap_run.font.name = "Arial"
            cap_run.font.size = Pt(8.5)
            cap_run.font.italic = True
            cap_run.font.color.rgb = COLOR_SLATE_MUTED
        except Exception as e:
            print(f"Error adding picture {img_path}: {e}")

# ==========================================
# SLIDE 1: TRANG TIÊU ĐỀ BÁO CÁO (TITLE)
# ==========================================
s1 = prs.slides.add_slide(blank_slide_layout)
# Dark gradient background fill
bg = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0), Inches(0), Inches(13.333), Inches(7.5))
bg.fill.solid()
bg.fill.fore_color.rgb = COLOR_NAVY_DARK
bg.line.fill.background()

# Gold accent line
accent = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(1.2), Inches(1.4), Inches(2.5), Inches(0.08))
accent.fill.solid()
accent.fill.fore_color.rgb = COLOR_AMBER_GOLD
accent.line.fill.background()

# Title text frame
tb1 = s1.shapes.add_textbox(Inches(1.2), Inches(1.7), Inches(10.8), Inches(4.5))
tf1 = tb1.text_frame
tf1.word_wrap = True

p = tf1.paragraphs[0]
r = p.add_run()
r.text = "HỌC PHẦN: KIẾN TRÚC VÀ THIẾT KẾ PHẦN MỀM"
r.font.name = "Arial"
r.font.size = Pt(13)
r.font.bold = True
r.font.color.rgb = COLOR_AMBER_GOLD

p2 = tf1.add_paragraph()
p2.space_before = Pt(10)
p2.space_after = Pt(12)
r2 = p2.add_run()
r2.text = "CHƯƠNG 3: THIẾT KẾ KIẾN TRÚC"
r2.font.name = "Arial"
r2.font.size = Pt(32)
r2.font.bold = True
r2.font.color.rgb = COLOR_WHITE

p3 = tf1.add_paragraph()
p3.space_after = Pt(24)
r3 = p3.add_run()
r3.text = "1. Đánh giá các kiến trúc thay thế (3.2.3)\n2. Phân tích phổ trong thiết kế kiến trúc (3.3.1)\n3. Một số mẫu thiết kế kiến trúc phổ biến & hiện đại (3.3.2)"
r3.font.name = "Arial"
r3.font.size = Pt(15)
r3.font.color.rgb = RGBColor(203, 213, 225) # Slate 300

p4 = tf1.add_paragraph()
r4 = p4.add_run()
r4.text = "Dự án thực nghiệm: Hệ thống Quản lý Nhà hàng & Gọi món Trực tuyến (cimonwork-design/php-restaurant)\nCông nghệ: PHP Native MVC, MySQL PDO, JWT Authentication, RBAC 6 vai trò"
r4.font.name = "Arial"
r4.font.size = Pt(11)
r4.font.italic = True
r4.font.color.rgb = COLOR_AMBER_GOLD

add_speaker_note(
    s1,
    "Kính thưa Thầy/Cô và các bạn sinh viên. Hôm nay, nhóm em xin phép được trình bày báo cáo chuyên sâu về Chương 3: "
    "Thiết kế Kiến trúc Phần mềm. Trọng tâm của bài báo cáo tập trung làm sáng tỏ ba bài toán cốt lõi trong kỹ thuật phần mềm: "
    "Thứ nhất là phương pháp luận đánh giá các phương án kiến trúc thay thế; thứ hai là lý thuyết phân tích phổ mẫu thiết kế; "
    "và thứ ba là các phong cách kiến trúc hiện đại tiêu biểu. Toàn bộ lý thuyết này sẽ được nhóm em soi chiếu, minh chứng "
    "và liên hệ trực tiếp vào dự án thực tế của nhóm – đó là Hệ thống Quản lý Nhà hàng và Gọi món Trực tuyến php-restaurant. "
    "Kính mời Thầy/Cô cùng theo dõi nội dung chi tiết ngay sau đây."
)

# ==========================================
# SLIDE 2: MỤC LỤC & LỘ TRÌNH (AGENDA)
# ==========================================
s2 = prs.slides.add_slide(blank_slide_layout)
add_header(s2, "TỔNG QUAN BÁO CÁO", "LỘ TRÌNH NỘI DUNG BÁO CÁO (AGENDA)", "Cấu trúc 3 phần trọng tâm nghiên cứu kết hợp đối chiếu thực tiễn mã nguồn")
add_footer(s2, 2)

# 3 Columns for 3 parts
c_w = Inches(3.64)
c_gap = Inches(0.38)
c_top = Inches(1.8)
c_h = Inches(4.8)

# Col 1
create_card(s2, Inches(0.8), c_top, c_w, c_h)
tb = s2.shapes.add_textbox(Inches(1.0), c_top + Inches(0.2), c_w - Inches(0.4), c_h - Inches(0.4))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "PHẦN 1", "ĐÁNH GIÁ KIẾN TRÚC THAY THẾ (3.2.3)", space_after=12)
add_bullet_point(tf, "1.1", "Bản chất & Cây thuộc tính chất lượng (ISO 25010)", level=1)
add_bullet_point(tf, "1.2", "Khung phương pháp ATAM, CBAM, SAAM", level=1)
add_bullet_point(tf, "1.3", "Quy trình 5 bước thực thi đánh giá kiến trúc", level=1)
add_bullet_point(tf, "1.4", "Đánh giá 4 kiến trúc ứng viên dự án nhà hàng", level=1)
add_bullet_point(tf, "1.4", "Ma trận Trade-off & Hồ sơ quyết định ADR-001", level=1)

# Col 2
create_card(s2, Inches(0.8) + c_w + c_gap, c_top, c_w, c_h)
tb = s2.shapes.add_textbox(Inches(1.0) + c_w + c_gap, c_top + Inches(0.2), c_w - Inches(0.4), c_h - Inches(0.4))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "PHẦN 2", "PHÂN TÍCH PHỔ TRONG THIẾT KẾ (3.3.1)", space_after=12)
add_bullet_point(tf, "2.1", "Khái niệm The Pattern Spectrum (POSA)", level=1)
add_bullet_point(tf, "2.2", "Tầng Vĩ mô: Mẫu Kiến trúc (Architectural)", level=1)
add_bullet_point(tf, "2.2", "Tầng Trung mô & Vi mô: Mẫu Thiết kế & Idioms", level=1)
add_bullet_point(tf, "2.3", "Mối quan hệ tương hỗ & Ánh xạ Top-Down", level=1)
add_bullet_point(tf, "2.4", "Bản đồ phổ mẫu chiếu vào source code PHP", level=1)

# Col 3
create_card(s2, Inches(0.8) + (c_w + c_gap)*2, c_top, c_w, c_h)
tb = s2.shapes.add_textbox(Inches(1.0) + (c_w + c_gap)*2, c_top + Inches(0.2), c_w - Inches(0.4), c_h - Inches(0.4))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "PHẦN 3", "CÁC MẪU KIẾN TRÚC HIỆN ĐẠI (3.3.2)", space_after=12)
add_bullet_point(tf, "3.1", "Bối cảnh tiến hóa Cloud-Native & Phân tán", level=1)
add_bullet_point(tf, "3.2", "5 Mẫu hiện đại: Clean, Microservices, EDA...", level=1)
add_bullet_point(tf, "3.3", "Ma trận so sánh đa chiều 5 mẫu kiến trúc", level=1)
add_bullet_point(tf, "3.4", "Điểm nghẽn chuỗi 50 nhà hàng & Target Arch", level=1)
add_bullet_point(tf, "3.4", "Lộ trình chuyển đổi: Strangler Fig Pattern", level=1)

add_speaker_note(
    s2,
    "Bài thuyết trình của nhóm được chia làm 3 phần lớn với bố cục phân cấp logic từ 1.1 đến 3.4. "
    "Phần đầu tiên sẽ giải quyết bài toán ra quyết định kỹ thuật: Làm thế nào để chọn được kiến trúc phù hợp nhất giữa vô vàn giải pháp? "
    "Phần thứ hai đi sâu vào lý thuyết phân tích phổ của Buschmann để hiểu rõ cách các mẫu phần mềm kết nối từ cấp độ vĩ mô xuống từng dòng code PHP. "
    "Và phần thứ ba sẽ mở rộng sang các kiến trúc hiện đại như Microservices hay Event-Driven, đồng thời vạch ra lộ trình tiến hóa kiến trúc cho hệ thống nhà hàng của chúng em."
)

# ==========================================
# SLIDE 3: 1.1. BẢN CHẤT & THUỘC TÍNH CHẤT LƯỢNG ISO 25010
# ==========================================
s3 = prs.slides.add_slide(blank_slide_layout)
add_header(s3, "PHẦN 1: ĐÁNH GIÁ CÁC KIẾN TRÚC THAY THẾ (MỤC 3.2.3)", "1.1. BẢN CHẤT, MỤC TIÊU & NGUYÊN LÝ ĐÁNH ĐỔI (TRADE-OFFS)", "Cân bằng các thuộc tính chất lượng phần mềm theo tiêu chuẩn quốc tế ISO/IEC 25010")
add_footer(s3, 3)

# Left Column (Text Card)
create_card(s3, Inches(0.8), Inches(1.8), Inches(6.8), Inches(4.8))
tb = s3.shapes.add_textbox(Inches(1.0), Inches(1.95), Inches(6.4), Inches(4.5))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "Bản chất cốt lõi:", "Không tồn tại kiến trúc hoàn hảo tuyệt đối cho mọi bài toán. Mọi thiết kế kiến trúc đều là sự thỏa hiệp có chủ đích giữa các thuộc tính chất lượng đối nghịch nhau.")
add_bullet_point(tf, "Nguyên lý Trade-offs:", "Gia tăng Bảo mật & Phân tầng sâu sẽ làm tăng độ trễ (Latency). Mở rộng sang Microservices sẽ hy sinh tính nhất quán ACID tức thời và tăng chi phí hạ tầng.")
add_bullet_point(tf, "Mục tiêu tối thượng:", "Giảm thiểu tối đa rủi ro kỹ thuật trước giai đoạn code; tối ưu tổng chi phí sở hữu (TCO); rút ngắn thời gian ra mắt thị trường (Time-to-Market).")
add_bullet_point(tf, "Cây thuộc tính ISO/IEC 25010:", "Định lượng hóa yêu cầu phi chức năng qua 8 đặc tính: Hiệu năng, Tương thích, Khả dụng, Độ tin cậy, Bảo mật, Khả năng bảo trì, Khả chuyển và Tính hoàn thiện chức năng.")

# Right Column (Image Card)
add_image_card(s3, Inches(7.8), Inches(1.8), Inches(4.7), Inches(4.8), "images_chuong3/iso_25010.jpg", "Cây Thuộc tính Chất lượng Phần mềm Tiêu chuẩn Quốc tế ISO/IEC 25010")

add_speaker_note(
    s3,
    "Bước vào mục 1.1, câu hỏi đầu tiên đặt ra là: Tại sao chúng ta phải đánh giá các kiến trúc thay thế? "
    "Trong kỹ thuật phần mềm, một trong những chân lý quan trọng nhất là: Không có một kiến trúc nào là tốt nhất về mọi mặt. "
    "Nếu bạn muốn hệ thống có hiệu năng cực cao và bảo mật tối đa, bạn sẽ phải đánh đổi bằng chi phí phát triển lớn và thời gian ra mắt sản phẩm bị kéo dài. "
    "Nếu bạn chọn kiến trúc vi dịch vụ để mở rộng quy mô, bạn buộc phải đánh đổi tính nhất quán dữ liệu tức thời và chấp nhận tính phức tạp của hệ thống phân tán. "
    "Vì vậy, mục tiêu tối thượng của việc đánh giá kiến trúc là lượng hóa các thuộc tính chất lượng theo chuẩn ISO 25010 nhằm tìm ra điểm cân bằng tối ưu cho bài toán cụ thể."
)

# ==========================================
# SLIDE 4: 1.2. CÁC PHƯƠNG PHÁP ĐÁNH GIÁ CHUẨN HÓA: ATAM & CBAM
# ==========================================
s4 = prs.slides.add_slide(blank_slide_layout)
add_header(s4, "PHẦN 1: ĐÁNH GIÁ CÁC KIẾN TRÚC THAY THẾ (MỤC 3.2.3)", "1.2. CÁC PHƯƠNG PHÁP & KHUNG ĐÁNH GIÁ CHUẨN HÓA", "Khung đánh giá kiến trúc ATAM và CBAM của Viện Kỹ thuật Phần mềm Mỹ (SEI)")
add_footer(s4, 4)

create_card(s4, Inches(0.8), Inches(1.8), Inches(6.8), Inches(4.8))
tb = s4.shapes.add_textbox(Inches(1.0), Inches(1.95), Inches(6.4), Inches(4.5))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "1. Phương pháp ATAM (SEI):", "Architecture Tradeoff Analysis Method — Tiêu chuẩn vàng quốc tế để đánh giá kiến trúc phần mềm trước khi triển khai.")
add_bullet_point(tf, "Quy trình 4 pha, 9 bước:", "Trình bày mục tiêu nghiệp vụ -> Xây dựng Cây thuộc tính chất lượng -> Đánh giá kịch bản ưu tiên -> Nhận diện rủi ro kỹ thuật.", level=1)
add_bullet_point(tf, "3 Khái niệm hạt nhân ATAM:", "", level=1)
add_bullet_point(tf, "• Scenarios:", "Kịch bản vận hành thực tế (Use-case, Growth, Exploratory).", level=2)
add_bullet_point(tf, "• Sensitivity Points:", "Điểm nhạy cảm mà thay đổi nhỏ ảnh hưởng lớn đến chất lượng.", level=2)
add_bullet_point(tf, "• Tradeoff Points:", "Điểm đánh đổi mà quyết định ảnh hưởng trái chiều tới 2 thuộc tính.", level=2)
add_bullet_point(tf, "2. Phương pháp CBAM:", "Cost-Benefit Analysis Method — Đưa yếu tố kinh tế lượng vào kiến trúc: Tính toán ROI = Lợi ích nghiệp vụ / Chi phí đầu tư hạ tầng.")
add_bullet_point(tf, "3. Ma trận Quyết định có Trọng số:", "Gán trọng số % cho tiêu chí và chấm điểm định lượng 1-10 các ứng viên.")

add_image_card(s4, Inches(7.8), Inches(1.8), Inches(4.7), Inches(4.8), "images_chuong3/architecture_activities.jpg", "Quy trình Các Hoạt động Đánh giá và Phân tích Kiến trúc Phần mềm")

add_speaker_note(
    s4,
    "Để việc so sánh kiến trúc không bị rơi vào tranh luận cảm tính, các kỹ sư phần mềm trên thế giới sử dụng phương pháp ATAM do Viện SEI phát triển. "
    "ATAM hoạt động dựa trên các 'Kịch bản' (Scenarios) cụ thể để tìm ra hai yếu tố then chốt: 'Điểm nhạy cảm' – tức là điểm mà một thay đổi nhỏ sẽ làm thay đổi lớn hiệu năng hệ thống, "
    "và 'Điểm đánh đổi' – nơi mà hai thuộc tính chất lượng xung đột nhau. Bên cạnh đó, phương pháp CBAM sẽ giúp ban lãnh đạo trả lời câu hỏi: Bỏ thêm tiền nâng cấp kiến trúc thì mang lại bao nhiêu giá trị nghiệp vụ? "
    "Đây là những công cụ tư duy sắc bén của một kiến trúc sư phần mềm chuyên nghiệp."
)

# ==========================================
# SLIDE 5: 1.3. QUY TRÌNH 5 BƯỚC THỰC THI ĐÁNH GIÁ KIẾN TRÚC
# ==========================================
s5 = prs.slides.add_slide(blank_slide_layout)
add_header(s5, "PHẦN 1: ĐÁNH GIÁ CÁC KIẾN TRÚC THAY THẾ (MỤC 3.2.3)", "1.3. QUY TRÌNH 5 BƯỚC THỰC THI ĐÁNH GIÁ KIẾN TRÚC", "Chu trình khép kín từ trích xuất yêu cầu đến ban hành Hồ sơ Quyết định Kiến trúc (ADR)")
add_footer(s5, 5)

step_w = Inches(11.7)
step_h = Inches(0.85)
step_gap = Inches(0.12)
s_top = Inches(1.8)

steps = [
    ("BƯỚC 1", "Khai phá Yêu cầu Kiến trúc Trọng yếu (ASRs)", "Lọc ra các yêu cầu phi chức năng chi phối trực tiếp đến khung sườn hệ thống thông qua phỏng vấn Product Owner và các bên liên quan."),
    ("BƯỚC 2", "Xây dựng Danh mục Kiến trúc Ứng viên (Candidates)", "Đề xuất từ 2 đến 4 giải pháp cấu trúc khả dĩ (Monolith, Microservices, Serverless, Jamstack/SPA) kèm công nghệ tương ứng."),
    ("BƯỚC 3", "Thiết lập Hệ thống Kịch bản Đánh giá (Scenarios)", "Xây dựng kịch bản vận hành bình thường, kịch bản tải đột biến giờ cao điểm, kịch bản lỗi mạng và kịch bản nâng cấp phiên bản."),
    ("BƯỚC 4", "Chấm điểm, Phân tích Đánh đổi & Nhận diện Điểm mù", "Họp hội đồng kỹ thuật, đối chiếu từng phương án với hệ thống kịch bản, lập ma trận trọng số và phân tích Trade-off points."),
    ("BƯỚC 5", "Ban hành Hồ sơ Quyết định Kiến trúc (ADR Record)", "Đóng băng quyết định thiết kế chính thức, ghi nhận lý do lựa chọn, các rủi ro chấp nhận và lưu trữ làm tài sản kỹ thuật lâu dài.")
]

for idx, (b_tag, b_title, b_desc) in enumerate(steps):
    c_y = s_top + (step_h + step_gap) * idx
    create_card(s5, Inches(0.8), c_y, step_w, step_h)
    
    # Tag box
    tag_b = s5.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0.9), c_y + Inches(0.12), Inches(1.3), Inches(0.6))
    tag_b.fill.solid()
    tag_b.fill.fore_color.rgb = COLOR_NAVY_BLUE if idx < 4 else COLOR_AMBER_GOLD
    tag_b.line.fill.background()
    tag_tf = tag_b.text_frame
    tag_p = tag_tf.paragraphs[0]
    tag_p.alignment = PP_ALIGN.CENTER
    tag_r = tag_p.add_run()
    tag_r.text = b_tag
    tag_r.font.name = "Arial"
    tag_r.font.size = Pt(11)
    tag_r.font.bold = True
    tag_r.font.color.rgb = COLOR_WHITE
    
    # Text
    tb = s5.shapes.add_textbox(Inches(2.35), c_y + Inches(0.08), Inches(10.0), Inches(0.7))
    tf = tb.text_frame
    tf.word_wrap = True
    p1 = tf.paragraphs[0]
    r1 = p1.add_run()
    r1.text = b_title + " — "
    r1.font.name = "Arial"
    r1.font.bold = True
    r1.font.size = Pt(11.5)
    r1.font.color.rgb = COLOR_NAVY_DARK
    
    r2 = p1.add_run()
    r2.text = b_desc
    r2.font.name = "Arial"
    r2.font.size = Pt(10)
    r2.font.color.rgb = COLOR_SLATE_DARK

add_speaker_note(
    s5,
    "Trong thực tế dự án, quy trình đánh giá kiến trúc được chuẩn hóa thành 5 bước tuần tự như trên slide. "
    "Bắt đầu từ việc trích xuất các ASRs – tức là các yêu cầu kiến trúc trọng yếu. Sau đó nhóm sẽ đề xuất các kiến trúc ứng viên, "
    "đưa vào các kịch bản kiểm thử giả định, chấm điểm ma trận và quan trọng nhất là bước số 5: Ban hành tài liệu ADR. "
    "Tài liệu ADR chính là 'bản cam kết kỹ thuật' giải thích vì sao nhóm chọn kiến trúc này mà không chọn kiến trúc khác, "
    "giúp các lập trình viên vào sau hiểu được lý do thiết kế mà không tùy tiện phá vỡ cấu trúc."
)

# ==========================================
# SLIDE 6: 1.4. ĐÁNH GIÁ KIẾN TRÚC DỰ ÁN QUẢN LÝ NHÀ HÀNG
# ==========================================
s6 = prs.slides.add_slide(blank_slide_layout)
add_header(s6, "PHẦN 1: ĐÁNH GIÁ CÁC KIẾN TRÚC THAY THẾ (MỤC 3.2.3)", "1.4. ĐÁNH GIÁ KIẾN TRÚC THỰC TẾ TRONG DỰ ÁN NHÀ HÀNG", "Phân tích bối cảnh nghiệp vụ php-restaurant và đề xuất 4 phương án ứng viên")
add_footer(s6, 6)

create_card(s6, Inches(0.8), Inches(1.8), Inches(6.8), Inches(4.8))
tb = s6.shapes.add_textbox(Inches(1.0), Inches(1.95), Inches(6.4), Inches(4.5))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "Bối cảnh nghiệp vụ php-restaurant:", "Quản lý chu trình nhà hàng khép kín: Khách quét mã QR tại bàn gọi món; Bếp nhận đơn chế biến; Thu ngân POS chốt bill; Thủ kho nhập nguyên liệu.")
add_bullet_point(tf, "Yêu cầu ACID sống còn:", "Tạo phiếu nhập kho (InventoryReceipt) gồm hàng chục nguyên liệu bắt buộc phải cập nhật đồng bộ bảng phiếu nhập và bảng tồn kho trong 1 Transaction; lỗi là phải Rollback 100%.")
add_bullet_point(tf, "So sánh 4 Phương án Kiến trúc Ứng viên:", "")
add_bullet_point(tf, "• Phương án A (Monolith MVC - PHP):", "PHP Native + MySQL PDO + Bootstrap 5. Triển khai mượt mà trên XAMPP nội bộ, chi phí 0đ, ACID an toàn tuyệt đối.", level=1)
add_bullet_point(tf, "• Phương án B (Microservices):", "Tách 5 dịch vụ (Auth, QR Order, KDS, Inventory, Billing). Scale tốt nhưng chi phí K8s rất đắt đỏ, Saga phức tạp.", level=1)
add_bullet_point(tf, "• Phương án C (Serverless FaaS):", "AWS Lambda + DynamoDB NoSQL. Co giãn tự động nhưng NoSQL khó bảo đảm ACID nhiều bảng, phụ thuộc vendor.", level=1)
add_bullet_point(tf, "• Phương án D (SPA + REST API):", "React/Vue + PHP Backend. UI mượt nhưng tốn thời gian xây dựng 2 repository riêng biệt.", level=1)

add_image_card(s6, Inches(7.8), Inches(1.8), Inches(4.7), Inches(4.8), "images_chuong3/tradeoff_cap_theorem.png", "Phân tích Đánh đổi Kiến trúc & Định lý CAP giữa Monolith và Phân tán")

add_speaker_note(
    s6,
    "Áp dụng vào thực tế dự án của nhóm: Chúng em xây dựng hệ thống quản lý nhà hàng php-restaurant. "
    "Nghiệp vụ của nhà hàng đòi hỏi tính toàn vẹn dữ liệu cực kỳ khắt khe: Khi thủ kho tạo một phiếu nhập kho gồm 20 nguyên liệu, "
    "hệ thống bắt buộc phải cập nhật đồng bộ bảng phiếu nhập và bảng tồn kho trong một Transaction duy nhất; nếu có lỗi phải hoàn tác ngay lập tức. "
    "Đứng trước bài toán này, nhóm đã đặt lên bàn cân 4 phương án kiến trúc: Monolithic MVC, Microservices, Serverless và mô hình SPA tách rời API. "
    "Mỗi phương án đều có thế mạnh riêng, nhưng phải xem xét kỹ mức độ phù hợp với quy mô thực tế của dự án."
)

# ==========================================
# SLIDE 7: 1.4. MA TRẬN ĐÁNH ĐỔI & QUYẾT ĐỊNH ADR DỰ ÁN
# ==========================================
s7 = prs.slides.add_slide(blank_slide_layout)
add_header(s7, "PHẦN 1: ĐÁNH GIÁ CÁC KIẾN TRÚC THAY THẾ (MỤC 3.2.3)", "1.4. MA TRẬN ĐÁNH ĐỔI & QUYẾT ĐỊNH KIẾN TRÚC (ADR-001)", "Kết quả chấm điểm định lượng 7 thuộc tính chất lượng và Hồ sơ Quyết định phê duyệt")
add_footer(s7, 7)

# Table on left (7.5 inches)
table_shape = s7.shapes.add_table(9, 6, Inches(0.8), Inches(1.8), Inches(7.2), Inches(4.8))
table = table_shape.table

# Set widths
col_w = [Inches(2.2), Inches(0.7), Inches(1.1), Inches(1.1), Inches(1.05), Inches(1.05)]
for i, w in enumerate(col_w):
    table.columns[i].width = w

table_data = [
    ["Tiêu chí Đánh giá", "Trọng số", "Monolith MVC", "Microservices", "Serverless", "SPA + API"],
    ["1. Chi phí Triển khai / Hạ tầng", "20%", "9.5 (Rất rẻ)", "3.0 (Rất đắt)", "6.0 (Vừa phải)", "7.5 (Tương đối)"],
    ["2. Tốc độ Ra mắt (Time-to-market)", "20%", "9.0 (Rất nhanh)", "3.5 (Lâu)", "5.5 (Vừa phải)", "7.0 (Khá)"],
    ["3. Toàn vẹn ACID (Phiếu nhập kho)", "20%", "9.5 (Tuyệt đối)", "4.0 (Khó / Saga)", "5.0 (NoSQL hạn chế)", "9.0 (Tốt)"],
    ["4. Khả năng Mở rộng (Scale)", "10%", "5.0 (Theo khối)", "9.5 (Độc lập)", "9.5 (Tự động)", "7.0 (Khá)"],
    ["5. Độ phức tạp Vận hành DevOps", "10%", "9.0 (Đơn giản)", "2.5 (Rất phức tạp)", "5.0 (Phụ thuộc)", "6.5 (Vừa)"],
    ["6. Khả năng Bảo trì Module", "10%", "8.0 (MVC chuẩn)", "9.0 (Cô lập cao)", "6.0 (Phân mảnh)", "8.5 (Tách UI)"],
    ["7. Bảo mật & Phân quyền RBAC", "10%", "8.5 (JWT+PDO)", "8.0 (Khá)", "7.5 (Khá)", "8.0 (Tốt)"],
    ["TỔNG ĐIỂM CHUNG CUỘC", "100%", "8.60 (TỐI ƯU)", "4.90", "6.10", "7.60"]
]

for r_idx, row in enumerate(table_data):
    for c_idx, val in enumerate(row):
        cell = table.cell(r_idx, c_idx)
        cell.text = val
        p = cell.text_frame.paragraphs[0]
        p.alignment = PP_ALIGN.LEFT if c_idx == 0 else PP_ALIGN.CENTER
        run = p.runs[0]
        run.font.name = "Arial"
        if r_idx == 0:
            cell.fill.solid()
            cell.fill.fore_color.rgb = COLOR_NAVY_BLUE
            run.font.size = Pt(9)
            run.font.bold = True
            run.font.color.rgb = COLOR_WHITE
        elif r_idx == 8:
            cell.fill.solid()
            cell.fill.fore_color.rgb = RGBColor(235, 243, 250)
            run.font.size = Pt(9.5)
            run.font.bold = True
            run.font.color.rgb = COLOR_NAVY_DARK if c_idx == 2 else COLOR_SLATE_DARK
        else:
            cell.fill.solid()
            cell.fill.fore_color.rgb = COLOR_BG_LIGHT if r_idx % 2 == 1 else COLOR_WHITE
            run.font.size = Pt(8)
            run.font.color.rgb = COLOR_SLATE_DARK

# Right Box: ADR Summary
create_card(s7, Inches(8.3), Inches(1.8), Inches(4.2), Inches(4.8), bg_color=COLOR_WHITE, border_color=COLOR_NAVY_BLUE)
tb = s7.shapes.add_textbox(Inches(8.5), Inches(1.95), Inches(3.8), Inches(4.5))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "HỒ SƠ ADR-001 (ACCEPTED)", "", space_after=8)
add_bullet_point(tf, "• Quyết định kiến trúc:", "Phê duyệt Monolithic 3-Tier MVC trên nền tảng PHP Native và MySQL PDO cho hệ thống nhà hàng.", level=1)
add_bullet_point(tf, "• Biện minh then chốt:", "Bảo đảm toàn vẹn giao dịch ACID tuyệt đối khi tạo phiếu nhập kho; chạy ổn định trên mạng LAN XAMPP ngay cả khi mất cáp quang quốc tế; chi phí hạ tầng 0đ.", level=1)
add_bullet_point(tf, "• Ngưỡng chuyển đổi (Trigger Points):", "", level=1)
add_bullet_point(tf, "- Trigger 1:", "Số chi nhánh chuỗi vượt quá 15 nhà hàng.", level=2)
add_bullet_point(tf, "- Trigger 2:", "Tải đặt món QR giờ cao điểm > 3.000 RPS.", level=2)
add_bullet_point(tf, "- Trigger 3:", "Quy mô nhóm phát triển vượt quá 15 lập trình viên.", level=2)

add_speaker_note(
    s7,
    "Nhìn vào bảng ma trận đánh đổi trên slide, Thầy/Cô có thể thấy lý do vì sao nhóm lựa chọn kiến trúc Monolith MVC. "
    "Với 3 tiêu chí quan trọng nhất chiếm 60% trọng số là: Chi phí hạ tầng, Thời gian phát triển và Tính toàn vẹn ACID của phiếu nhập kho, "
    "Monolith PHP đạt điểm số gần như tuyệt đối (9.0 đến 9.5). Trong khi đó, Microservices mặc dù rất mạnh về khả năng mở rộng (9.5 điểm) "
    "nhưng lại nhận điểm rất thấp ở chi phí và độ phức tạp vận hành. Kết quả tổng điểm 8.60 đã khẳng định Monolith MVC là lựa chọn tối ưu "
    "và kinh tế nhất cho dự án hiện nay. Quyết định này đã được chính thức chuẩn hóa trong hồ sơ ADR-001 của nhóm."
)

# ==========================================
# SLIDE 8: 2.1. KHÁI NIỆM "PHỔ MẪU THIẾT KẾ" (POSA)
# ==========================================
s8 = prs.slides.add_slide(blank_slide_layout)
add_header(s8, "PHẦN 2: PHÂN TÍCH PHỔ TRONG THIẾT KẾ KIẾN TRÚC (MỤC 3.3.1)", "2.1. KHÁI NIỆM & ĐỊNH NGHĨA PHỔ MẪU THIẾT KẾ", "Nguồn gốc học thuật từ bộ sách kinh điển POSA (Frank Buschmann et al.)")
add_footer(s8, 8)

create_card(s8, Inches(0.8), Inches(1.8), Inches(6.8), Inches(4.8))
tb = s8.shapes.add_textbox(Inches(1.0), Inches(1.95), Inches(6.4), Inches(4.5))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "Nguồn gốc học thuật:", "Khởi xướng trong công trình nổi tiếng thế giới 'Pattern-Oriented Software Architecture (POSA)' của Buschmann, Meunier, Rohnert, Sommerlad, Stal.")
add_bullet_point(tf, "Bản chất sai lầm phổ biến:", "Lập trình viên thường đánh đồng tất cả các giải pháp vào một khái niệm 'Pattern' chung chung, không phân biệt được đâu là quyết định cấu trúc vĩ mô và đâu là mẹo cú pháp vi mô.")
add_bullet_point(tf, "Định nghĩa 'Phổ Mẫu' (Pattern Spectrum):", "Dải phân loại liên tục các giải pháp thiết kế dựa trên hai trục tọa độ cốt lõi:")
add_bullet_point(tf, "• Trục hoành (Level of Abstraction):", "Từ mức trừu tượng hóa chiến lược (độc lập ngôn ngữ) đến mức hiện thực hóa cụ thể (gắn với cú pháp).", level=1)
add_bullet_point(tf, "• Trục tung (Scale and Scope):", "Từ quy mô toàn hệ thống (System-wide) đến cấp độ module, lớp/đối tượng, và từng dòng lệnh.", level=1)
add_bullet_point(tf, "Ý nghĩa thực tiễn:", "Định vị chính xác vai trò của từng giải pháp; bảo đảm tính nhất quán từ bản vẽ kiến trúc đến từng dòng code triển khai.")

# Right visual box: Diagram of the 3-layer spectrum pyramid
create_card(s8, Inches(7.8), Inches(1.8), Inches(4.7), Inches(4.8), bg_color=COLOR_WHITE)
tb_pyr = s8.shapes.add_textbox(Inches(8.0), Inches(2.0), Inches(4.3), Inches(4.4))
tf_pyr = tb_pyr.text_frame
tf_pyr.word_wrap = True
p = tf_pyr.paragraphs[0]
p.alignment = PP_ALIGN.CENTER
r = p.add_run()
r.text = "SƠ ĐỒ PHỔ MẪU (THE PATTERN SPECTRUM)\n"
r.font.name = "Arial"
r.font.bold = True
r.font.size = Pt(11.5)
r.font.color.rgb = COLOR_NAVY_BLUE

# Layer 1
b1 = s8.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(8.1), Inches(2.5), Inches(4.1), Inches(1.0))
b1.fill.solid()
b1.fill.fore_color.rgb = COLOR_NAVY_DARK
b1.line.fill.background()
b1.text_frame.paragraphs[0].text = "TẦNG 1: MẪU KIẾN TRÚC (MACRO)\nQuy mô: Toàn hệ thống | Độc lập công nghệ\nVí dụ: MVC, Layered, Microservices, Broker"
b1.text_frame.paragraphs[0].alignment = PP_ALIGN.CENTER
b1.text_frame.paragraphs[0].font.size = Pt(9.5)
b1.text_frame.paragraphs[0].font.color.rgb = COLOR_WHITE

# Layer 2
b2 = s8.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(8.1), Inches(3.7), Inches(4.1), Inches(1.0))
b2.fill.solid()
b2.fill.fore_color.rgb = COLOR_NAVY_BLUE
b2.line.fill.background()
b2.text_frame.paragraphs[0].text = "TẦNG 2: MẪU THIẾT KẾ (MESO - GoF)\nQuy mô: Cấu trúc lớp & module con | Hướng đối tượng\nVí dụ: Factory, Strategy, Observer, Template Method"
b2.text_frame.paragraphs[0].alignment = PP_ALIGN.CENTER
b2.text_frame.paragraphs[0].font.size = Pt(9.5)
b2.text_frame.paragraphs[0].font.color.rgb = COLOR_WHITE

# Layer 3
b3 = s8.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(8.1), Inches(4.9), Inches(4.1), Inches(1.0))
b3.fill.solid()
b3.fill.fore_color.rgb = COLOR_TEAL_ACCENT
b3.line.fill.background()
b3.text_frame.paragraphs[0].text = "TẦNG 3: THÀNH NGỮ NGÔN NGỮ (MICRO - IDIOMS)\nQuy mô: Dòng lệnh | Gắn liền cú pháp ngôn ngữ\nVí dụ: PDO Prepared Statements, Session Flash, RAII"
b3.text_frame.paragraphs[0].alignment = PP_ALIGN.CENTER
b3.text_frame.paragraphs[0].font.size = Pt(9.5)
b3.text_frame.paragraphs[0].font.color.rgb = COLOR_WHITE

add_speaker_note(
    s8,
    "Chuyển sang Phần 2, nhóm xin trình bày về một chủ đề rất sâu sắc trong lý thuyết kiến trúc phần mềm: 'Phân tích phổ trong thiết kế kiến trúc'. "
    "Khái niệm Phổ Mẫu (The Pattern Spectrum) được các tác giả cuốn sách POSA đưa ra nhằm giải quyết một sự nhầm lẫn phổ biến của các lập trình viên: "
    "Đó là đánh đồng giữa Mẫu Kiến trúc (Architectural Pattern) và Mẫu Thiết kế (Design Pattern). Phổ mẫu thiết lập một dải liên tục: "
    "Ở trên đỉnh cao nhất là các mẫu vĩ mô bao quát toàn hệ thống; ở giữa là các mẫu phối hợp đối tượng; và ở đáy là các thành ngữ cú pháp ngôn ngữ. "
    "Hiểu được phổ mẫu sẽ giúp chúng ta có cái nhìn toàn cảnh từ chiến lược đến chiến thuật."
)

# ==========================================
# SLIDE 9: 2.2. BA TẦNG BẬC TRÊN DẢI PHỔ: MẪU KIẾN TRÚC (MACRO)
# ==========================================
s9 = prs.slides.add_slide(blank_slide_layout)
add_header(s9, "PHẦN 2: PHÂN TÍCH PHỔ TRONG THIẾT KẾ KIẾN TRÚC (MỤC 3.3.1)", "2.2. TẦNG VĨ MÔ TRÊN DẢI PHỔ: MẪU KIẾN TRÚC (MACRO)", "Định hình khung xương và cơ chế tương tác cơ bản của toàn bộ hệ thống")
add_footer(s9, 9)

create_card(s9, Inches(0.8), Inches(1.8), Inches(6.8), Inches(4.8))
tb = s9.shapes.add_textbox(Inches(1.0), Inches(1.95), Inches(6.4), Inches(4.5))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "Vị trí trên phổ:", "Cấp độ cao nhất (Macro-level) – Mức độ trừu tượng cao nhất và phạm vi tác động bao trùm toàn bộ hệ thống.")
add_bullet_point(tf, "Trách nhiệm định hình:", "Xác định khung cấu trúc cơ bản (Skeleton); phân định các hệ thống con (Subsystems); đặt ra các cơ chế kết nối (Connectors) và các ràng buộc bắt buộc (Constraints).")
add_bullet_point(tf, "Tính độc lập công nghệ:", "Mẫu kiến trúc không bị trói buộc bởi ngôn ngữ lập trình cụ thể (Dù viết bằng PHP, Java Spring, C# hay Go thì bản chất cấu trúc phân tầng vẫn giữ nguyên).")
add_bullet_point(tf, "Các đại diện kinh điển:", "")
add_bullet_point(tf, "• Model-View-Controller (MVC):", "Phân định rành mạch giữa Giao diện (View) - Điều phối (Controller) - Dữ liệu (Model).", level=1)
add_bullet_point(tf, "• Layered Architecture:", "Phân tầng logic nghiêm ngặt từ Presentation -> Application -> Domain -> Data.", level=1)
add_bullet_point(tf, "• Pipes & Filters, Broker, Microservices, Event-Driven.", "", level=1)

add_image_card(s9, Inches(7.8), Inches(1.8), Inches(4.7), Inches(4.8), "images_chuong3/mvc_process.png", "Mô hình Dòng chảy Mẫu Kiến trúc Vĩ mô Model-View-Controller")

add_speaker_note(
    s9,
    "Tầng đầu tiên trên đỉnh của phổ mẫu chính là Mẫu Kiến trúc (Architectural Patterns). Đây là những bản vẽ tổng mặt bằng của cả một tòa nhà phần mềm. "
    "Nó quy định hệ thống được chia thành mấy khối lớn, ví dụ mô hình MVC quy định tách thành Model, View và Controller. "
    "Điểm mấu chốt của tầng này là nó mang tính độc lập hoàn toàn với công nghệ. Dù quý vị lập trình bằng PHP, Java Spring Boot hay ASP.NET Core, "
    "thì các ràng buộc kiến trúc của mô hình MVC hay Layered vẫn hoàn toàn giữ nguyên giá trị."
)

# ==========================================
# SLIDE 10: 2.2. BA TẦNG BẬC: MẪU THIẾT KẾ (MESO) & THÀNH NGỮ (MICRO)
# ==========================================
s10 = prs.slides.add_slide(blank_slide_layout)
add_header(s10, "PHẦN 2: PHÂN TÍCH PHỔ TRONG THIẾT KẾ KIẾN TRÚC (MỤC 3.3.1)", "2.2. TẦNG TRUNG MÔ & VI MÔ TRÊN DẢI PHỔ: DESIGN PATTERNS & IDIOMS", "Tinh chỉnh cấu trúc lớp hướng đối tượng và hiện thực hóa bằng cú pháp ngôn ngữ")
add_footer(s10, 10)

# 2 Cards: Left for Design Patterns, Right for Idioms
w_half = Inches(5.65)
create_card(s10, Inches(0.8), Inches(1.8), w_half, Inches(4.8))
tb1 = s10.shapes.add_textbox(Inches(1.0), Inches(1.95), w_half - Inches(0.4), Inches(4.5))
tf1 = tb1.text_frame
tf1.word_wrap = True
add_bullet_point(tf1, "TẦNG TRUNG MÔ: MẪU THIẾT KẾ (MESO)", "Phạm vi: Cấu trúc lớp và quan hệ đối tượng trong một module cụ thể.", space_after=8)
add_bullet_point(tf1, "Đặc trưng:", "Giải quyết các bài toán tái sử dụng mã nguồn và khớp nối mềm dẻo giữa các đối tượng; độc lập ngôn ngữ nhưng gắn với OOP.", level=1)
add_bullet_point(tf1, "23 Mẫu kinh điển Gang of Four (GoF):", "", level=1)
add_bullet_point(tf1, "• Creational (Khởi tạo):", "Factory Method, Singleton, Builder, Prototype.", level=2)
add_bullet_point(tf1, "• Structural (Cấu trúc):", "Adapter, Facade, Composite, Proxy, Decorator.", level=2)
add_bullet_point(tf1, "• Behavioral (Hành vi):", "Strategy, Observer, Template Method, State.", level=2)
add_bullet_point(tf1, "Ứng dụng nhà hàng:", "Template Method (Controller base), Strategy (Thanh toán), Table Data Gateway (Model base).", level=1)

create_card(s10, Inches(6.85), Inches(1.8), w_half, Inches(4.8))
tb2 = s10.shapes.add_textbox(Inches(7.05), Inches(1.95), w_half - Inches(0.4), Inches(4.5))
tf2 = tb2.text_frame
tf2.word_wrap = True
add_bullet_point(tf2, "TẦNG VI MÔ: THÀNH NGỮ NGÔN NGỮ (MICRO)", "Phạm vi: Dòng lệnh, biểu thức cú pháp cụ thể của ngôn ngữ.", space_after=8)
add_bullet_point(tf2, "Đặc trưng:", "Gắn chặt với cú pháp đặc thù; hướng dẫn viết mã an toàn bộ nhớ, tối ưu hiệu năng và trong sáng ngữ nghĩa.", level=1)
add_bullet_point(tf2, "Ví dụ đa ngôn ngữ:", "", level=1)
add_bullet_point(tf2, "• C++:", "Cú pháp RAII tự động giải phóng bộ nhớ khi con trỏ ra khỏi scope.", level=2)
add_bullet_point(tf2, "• Python:", "List Comprehension, Context Manager (with), Decorator (@).", level=2)
add_bullet_point(tf2, "• PHP:", "PDO Prepared Statements ($stmt->execute([:val])), Magic Methods, Flash Session PRG.", level=2)
add_bullet_point(tf2, "Ý nghĩa an ninh:", "Sử dụng đúng Idiom giúp triệt tiêu 100% lỗ hổng bảo mật cấp thấp như SQL Injection, XSS.", level=1)

add_speaker_note(
    s10,
    "Đi xuống tầng trung gian là Mẫu Thiết kế (Design Patterns). Nếu Architectural Pattern là bản vẽ toàn bộ ngôi nhà, "
    "thì Design Pattern là thiết kế chi tiết của một căn phòng – ví dụ như cách lắp đặt hệ thống điện nước hay cách bố trí nội thất. "
    "Đó là 23 mẫu GoF nổi tiếng như Singleton, Factory, hay Strategy. Cuối cùng, ở đáy phổ là Thành ngữ Ngôn ngữ (Idioms). "
    "Đây là những mẹo lập trình tinh tế gắn liền với từng ngôn ngữ cụ thể, ví dụ như trong PHP, việc sử dụng hàm PDO Prepared Statements "
    "để truyền tham số an toàn chính là một Idiom kinh điển giúp chống lại lỗ hổng SQL Injection."
)

# ==========================================
# SLIDE 11: 2.3. MỐI QUAN HỆ TƯƠNG HỖ DỌC THEO DẢI PHỔ
# ==========================================
s11 = prs.slides.add_slide(blank_slide_layout)
add_header(s11, "PHẦN 2: PHÂN TÍCH PHỔ TRONG THIẾT KẾ KIẾN TRÚC (MỤC 3.3.1)", "2.3. MỐI QUAN HỆ TƯƠNG HỖ & CƠ CHẾ ÁNH XẠ TOP-DOWN", "Dòng chảy từ chiến lược vĩ mô đến dòng lệnh và phòng chống xói mòn kiến trúc")
add_footer(s11, 11)

create_card(s11, Inches(0.8), Inches(1.8), Inches(11.7), Inches(4.8))
tb = s11.shapes.add_textbox(Inches(1.1), Inches(2.0), Inches(11.1), Inches(4.4))
tf = tb.text_frame
tf.word_wrap = True

add_bullet_point(tf, "Cơ chế Ánh xạ Top-Down (Từ Chiến lược đến Hiện thực):", "Các tầng trên phổ không tồn tại độc lập mà tương hỗ chặt chẽ:")
add_bullet_point(tf, "1. Architectural Pattern (Chiến lược):", "Lựa chọn MVC định hình khung sườn phân tách Giao diện, Điều khiển và Dữ liệu.", level=1)
add_bullet_point(tf, "2. Design Patterns (Chiến thuật):", "Controller dùng Front Controller + Template Method; Model dùng Table Data Gateway; Auth dùng RBAC.", level=1)
add_bullet_point(tf, "3. Language Idioms (Thực thi):", "Hiện thực hóa bằng PDO Prepared Statements an toàn, Session Flash và JWT Bearer Token.", level=1)
add_bullet_point(tf, "Phòng chống Xói mòn Kiến trúc (Architectural Decay / Erosion):", "Hiện tượng lập trình viên vi phạm quy tắc tầng bậc trên phổ, tùy tiện dùng idiom cấp thấp phá vỡ ràng buộc cấp cao (Ví dụ: viết code truy vấn SQL trực tiếp trong file View giao diện). Hành vi này biến hệ thống thành khối mã nguồn 'mì ăn liền' không thể kiểm thử.", space_after=8)
add_bullet_point(tf, "Phòng chống Lạm dụng Mẫu (Over-Engineering / Pattern Mania):", "Áp dụng các mẫu GoF quá phức tạp (Abstract Factory, Visitor) cho các tác vụ CRUD đơn giản gây cồng kềnh bộ mã nguồn và lãng phí tài nguyên CPU.", space_after=8)

add_speaker_note(
    s11,
    "Một hệ thống phần mềm chất lượng cao là hệ thống có sự nhất quán tuyệt đối dọc theo dải phổ mẫu từ trên xuống dưới. "
    "Khi chúng ta đã quyết định áp dụng Architectural Pattern là MVC, thì ở tầng thiết kế, Controller bắt buộc phải tuân theo Template Method "
    "và Model phải tuân theo Table Data Gateway. Ở tầng cài đặt, lập trình viên không bao giờ được phép viết câu lệnh SQL trực tiếp vào tệp View HTML. "
    "Nếu lập trình viên vi phạm điều này, hiện tượng 'xói mòn kiến trúc' sẽ xảy ra, làm cho toàn bộ mô hình kiến trúc ban đầu bị sụp đổ chỉ sau một thời gian ngắn bảo trì."
)

# ==========================================
# SLIDE 12: 2.4. BẢN ĐỒ PHỔ MẪU TRONG DỰ ÁN PHP-RESTAURANT
# ==========================================
s12 = prs.slides.add_slide(blank_slide_layout)
add_header(s12, "PHẦN 2: PHÂN TÍCH PHỔ TRONG THIẾT KẾ KIẾN TRÚC (MỤC 3.3.1)", "2.4. BẢN ĐỒ PHỔ MẪU CHIẾU VÀO SOURCE CODE PHP-RESTAURANT", "Minh chứng thực nghiệm 3 tầng phổ tương ứng với từng file mã nguồn trong dự án")
add_footer(s12, 12)

# Spectrum Table
table_shape = s12.shapes.add_table(10, 4, Inches(0.8), Inches(1.8), Inches(11.7), Inches(4.8))
table = table_shape.table
col_w = [Inches(2.2), Inches(2.3), Inches(3.2), Inches(4.0)]
for i, w in enumerate(col_w):
    table.columns[i].width = w

table_data = [
    ["Tầng Phổ Mẫu", "Tên Mẫu Áp Dụng", "Tệp Mã Nguồn Minh Chứng", "Trách Nhiệm Kỹ Thuật Trong Dự Án"],
    ["Tầng Kiến trúc (Macro)", "Monolithic 3-Tier MVC", "core/App.php, core/Controller.php, core/Model.php", "Định hình khung xương hệ thống, phân ranh giới View, Controller và Model."],
    ["Tầng Thiết kế (Meso)", "Front Controller Pattern", "index.php, .htaccess, core/App.php", "Điểm tiếp nhận tập trung, điều hướng URL /{controller}/{method}/{params}."],
    ["Tầng Thiết kế (Meso)", "Template Method Pattern", "core/Controller.php", "Cung cấp khung xương tái sử dụng: view(), model(), jsonResponse(), requireRoles()."],
    ["Tầng Thiết kế (Meso)", "Table Data Gateway Pattern", "core/Model.php, app/models/InventoryReceipt.php", "Đóng gói các thao tác CRUD CSDL (find, where, insert, update), cách ly SQL."],
    ["Tầng Thiết kế (Meso)", "RBAC (Phân quyền 6 vai trò)", "core/Controller.php, helpers/JWT.php", "Kiểm soát quyền truy cập chặt chẽ cho Admin, Manager, Staff, Cashier, Chef, Waiter."],
    ["Tầng Thiết kế (Meso)", "Strategy Pattern (Thanh toán)", "app/controllers/SaleOrderController.php", "Hoán đổi linh hoạt phương thức thanh toán: Tiền mặt, Chuyển khoản QR, Quẹt thẻ POS."],
    ["Thành ngữ (Micro)", "PDO Prepared Statements", "core/Model.php, config/database.php", "Tách rời câu lệnh SQL và tham số dữ liệu (:val), chống triệt để SQL Injection."],
    ["Thành ngữ (Micro)", "Flash Session PRG", "app/controllers/InventoryReceiptController.php", "Lưu thông báo trạng thái qua Session và redirect (Post/Redirect/Get) an toàn."],
    ["Thành ngữ (Micro)", "Stateless Bearer JWT", "helpers/JWT.php, config/jwt.php", "Giải mã chữ ký điện tử HMAC-SHA256 trên HTTP Header để xác thực không tốn RAM."]
]

for r_idx, row in enumerate(table_data):
    for c_idx, val in enumerate(row):
        cell = table.cell(r_idx, c_idx)
        cell.text = val
        p = cell.text_frame.paragraphs[0]
        p.alignment = PP_ALIGN.LEFT
        run = p.runs[0]
        run.font.name = "Arial"
        if r_idx == 0:
            cell.fill.solid()
            cell.fill.fore_color.rgb = COLOR_NAVY_BLUE
            run.font.size = Pt(9.5)
            run.font.bold = True
            run.font.color.rgb = COLOR_WHITE
        else:
            cell.fill.solid()
            cell.fill.fore_color.rgb = COLOR_BG_LIGHT if r_idx % 2 == 1 else COLOR_WHITE
            run.font.size = Pt(8.5)
            run.font.color.rgb = COLOR_NAVY_DARK if c_idx == 1 else COLOR_SLATE_DARK
            if c_idx == 0:
                run.font.bold = True

add_speaker_note(
    s12,
    "Trên slide 12, nhóm em xin minh chứng trực tiếp bản đồ phổ mẫu qua chính cấu trúc mã nguồn của dự án php-restaurant. "
    "Ở tầng vĩ mô, chúng em có bộ khung core/App, core/Controller và core/Model tạo nên khung sườn MVC. "
    "Ở tầng trung mô, chúng em ứng dụng Front Controller để định tuyến URL, Template Method để tái sử dụng mã lệnh kiểm tra quyền requireRoles(), "
    "và RBAC để phân chia quyền lực cho 6 nhóm người dùng. Xuống tới từng dòng code PHP ở tầng vi mô, toàn bộ các truy vấn CSDL đều tuân thủ nghiêm ngặt "
    "Idiom PDO Prepared Statements. Nhờ có sự phân tầng mạch lạc này, dự án của chúng em đạt độ tin cậy và khả năng bảo trì rất cao."
)

# ==========================================
# SLIDE 13: 3.1. BỐI CẢNH TIẾN HÓA KIẾN TRÚC HIỆN ĐẠI
# ==========================================
s13 = prs.slides.add_slide(blank_slide_layout)
add_header(s13, "PHẦN 3: MỘT SỐ MẪU THIẾT KẾ KIẾN TRÚC PHỔ BIẾN (MỤC 3.3.2)", "3.1. BỐI CẢNH & ĐỘNG LỰC TIẾN HÓA CỦA KIẾN TRÚC HIỆN ĐẠI", "Sự chuyển dịch lịch sử từ Monolith truyền thống sang Cloud-Native phân tán")
add_footer(s13, 13)

create_card(s13, Inches(0.8), Inches(1.8), Inches(11.7), Inches(4.8))
tb = s13.shapes.add_textbox(Inches(1.1), Inches(2.0), Inches(11.1), Inches(4.4))
tf = tb.text_frame
tf.word_wrap = True

add_bullet_point(tf, "Hành trình tiến hóa 3 giai đoạn của Kiến trúc Phần mềm:", "Sự chuyển dịch diễn ra mạnh mẽ trong 15 năm qua:")
add_bullet_point(tf, "• Giai đoạn 1 (Truyền thống - Monolith):", "Toàn bộ ứng dụng chạy chung một tiến trình duy nhất trên một máy chủ vật lý.", level=1)
add_bullet_point(tf, "• Giai đoạn 2 (Dịch vụ phân tán - SOA / Microservices):", "Phân chia dịch vụ độc lập, đóng gói Container (Docker, Kubernetes).", level=1)
add_bullet_point(tf, "• Giai đoạn 3 (Cloud-Native & Hướng sự kiện):", "Ứng dụng tận dụng tối đa Cloud (AWS, GCP), Serverless FaaS, Kafka Event Streaming.", level=1)
add_bullet_point(tf, "3 Động lực công nghệ sống còn thúc đẩy tiến hóa:", "", space_after=4)
add_bullet_point(tf, "1. Nhu cầu Co giãn Đàn hồi (Elastic Scalability):", "Phục vụ hàng trăm ngàn người dùng đồng thời, tự động mở rộng và thu hẹp tài nguyên theo lưu lượng thực tế.", level=1)
add_bullet_point(tf, "2. Tính Sẵn sàng Cao (High Availability 99.99%):", "Cô lập vùng lỗi (Fault Isolation); sự cố ở một dịch vụ con không làm tê liệt toàn bộ hệ thống doanh nghiệp.", level=1)
add_bullet_point(tf, "3. Triển khai Liên tục (CI/CD & Zero Downtime):", "Phát hành tính năng mới lên môi trường Production hàng ngày mà không cần dừng hệ thống để bảo trì.", level=1)

add_speaker_note(
    s13,
    "Bước sang Phần 3, chúng ta cùng nhìn vào bức tranh toàn cảnh của các mẫu thiết kế kiến trúc hiện đại. "
    "Trong kỷ nguyên số hóa, phần mềm không còn chỉ phục vụ vài trăm nhân viên nội bộ, mà phải phục vụ hàng triệu người dùng cùng lúc trên Internet. "
    "Điều này đã thúc đẩy sự chuyển dịch mạnh mẽ từ các kiến trúc nguyên khối sang kiến trúc phân tán và Cloud-Native. "
    "Ba động lực sống còn thúc đẩy sự tiến hóa này chính là: Khả năng co giãn đàn hồi không giới hạn, tính sẵn sàng 99.99% không gián đoạn, "
    "và khả năng triển khai liên tục CI/CD mà không cần tắt server."
)

# ==========================================
# SLIDE 14: 3.2.1. CLEAN ARCHITECTURE & HEXAGONAL
# ==========================================
s14 = prs.slides.add_slide(blank_slide_layout)
add_header(s14, "PHẦN 3: MỘT SỐ MẪU THIẾT KẾ KIẾN TRÚC PHỔ BIẾN (MỤC 3.3.2)", "3.2.1. KIẾN TRÚC ĐA TẦNG HIỆN ĐẠI & KIẾN TRÚC SẠCH (CLEAN ARCHITECTURE)", "Nguyên lý Đảo ngược Phụ thuộc và mô hình Ports & Adapters của Robert C. Martin (Uncle Bob)")
add_footer(s14, 14)

create_card(s14, Inches(0.8), Inches(1.8), Inches(6.8), Inches(4.8))
tb = s14.shapes.add_textbox(Inches(1.0), Inches(1.95), Inches(6.4), Inches(4.5))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "Tác giả khởi xướng:", "Robert C. Martin (Uncle Bob - 2012) & Alistair Cockburn (Hexagonal Architecture - 2005).")
add_bullet_point(tf, "Quy tắc Phụ thuộc Hướng tâm (The Dependency Rule):", "Mọi quan hệ phụ thuộc mã nguồn chỉ được phép trỏ TỪ NGOÀI VÀO TRONG.")
add_bullet_point(tf, "Cấu trúc các vòng tròn đồng tâm:", "")
add_bullet_point(tf, "• Lõi trung tâm (Entities & Use Cases):", "Chứa 100% quy tắc nghiệp vụ thuần túy, hoàn toàn độc lập với Framework, UI và Database.", level=1)
add_bullet_point(tf, "• Vòng ngoài (Interface Adapters & Frameworks):", "Controllers, Gateways, MySQL, Web Server đóng vai trò như các Plugins kết nối vào lõi qua Ports.", level=1)
add_bullet_point(tf, "Ưu điểm vượt trội:", "Khả năng kiểm thử độc lập 100% (Testable) không cần bật Database; dễ dàng thay đổi công nghệ CSDL mà không sửa 1 dòng code nghiệp vụ.")

add_image_card(s14, Inches(7.8), Inches(1.8), Inches(4.7), Inches(4.8), "images_chuong3/clean_architecture.jpg", "Sơ đồ Vòng tròn Đồng tâm Clean Architecture của Robert C. Martin (Uncle Bob)")

add_speaker_note(
    s14,
    "Mẫu kiến trúc hiện đại đầu tiên mà nhóm muốn giới thiệu là Clean Architecture của tác giả Robert C. Martin. "
    "Bản chất của Clean Architecture được gói gọn trong một quy tắc vàng: 'Quy tắc phụ thuộc một chiều từ ngoài vào trong'. "
    "Trái tim của ứng dụng là các thực thể Entities và các ca sử dụng Use Cases. Chúng hoàn toàn độc lập, không hề biết đến sự tồn tại "
    "của cơ sở dữ liệu hay giao diện web bên ngoài. CSDL hay Framework chỉ đóng vai trò như các cổng cắm (Adapters) vào hệ thống. "
    "Nhờ đó, chúng ta có thể kiểm thử toàn bộ logic nghiệp vụ một cách độc lập và thay thế công nghệ bên ngoài mà không làm ảnh hưởng đến lõi phần mềm."
)

# ==========================================
# SLIDE 15: 3.2.2. KIẾN TRÚC VI DỊCH VỤ (MICROSERVICES)
# ==========================================
s15 = prs.slides.add_slide(blank_slide_layout)
add_header(s15, "PHẦN 3: MỘT SỐ MẪU THIẾT KẾ KIẾN TRÚC PHỔ BIẾN (MỤC 3.3.2)", "3.2.2. KIẾN TRÚC VI DỊCH VỤ (MICROSERVICES ARCHITECTURE)", "Mô hình phân tán tự chủ theo Bounded Context, Database-per-Service và API Gateway")
add_footer(s15, 15)

create_card(s15, Inches(0.8), Inches(1.8), Inches(6.8), Inches(4.8))
tb = s15.shapes.add_textbox(Inches(1.0), Inches(1.95), Inches(6.4), Inches(4.5))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "Bản chất kiến trúc:", "Chia nhỏ hệ thống thành tập hợp các dịch vụ độc lập, có thể triển khai riêng rẽ, mỗi dịch vụ tập trung vào một năng lực nghiệp vụ duy nhất (Bounded Context theo DDD).")
add_bullet_point(tf, "Các nguyên tắc thiết kế bắt buộc:", "")
add_bullet_point(tf, "• Database-per-Service:", "Mỗi microservice sở hữu CSDL riêng biệt, cấm tuyệt đối việc truy vấn trực tiếp chéo database.", level=1)
add_bullet_point(tf, "• API Gateway:", "Điểm tiếp nhận tập trung duy nhất cho mọi Client, đảm nhận định tuyến, xác thực và cân bằng tải.", level=1)
add_bullet_point(tf, "• Giao tiếp liên dịch vụ:", "Qua REST/gRPC (đồng bộ) hoặc Message Broker như RabbitMQ/Kafka (bất đồng bộ).", level=1)
add_bullet_point(tf, "• Mẫu Circuit Breaker:", "Ngăn chặn sụp đổ dây chuyền khi một dịch vụ phụ gặp sự cố quá tải.", level=1)
add_bullet_point(tf, "Ưu / Nhược điểm:", "Co giãn độc lập tuyệt vời; nhưng cực kỳ phức tạp trong gỡ lỗi phân tán và quản lý giao dịch phân tán (Saga Pattern).")

add_image_card(s15, Inches(7.8), Inches(1.8), Inches(4.7), Inches(4.8), "images_chuong3/microservices_arch.png", "Kiến trúc Vi dịch vụ (Microservices) với API Gateway và Database per Service")

add_speaker_note(
    s15,
    "Mẫu kiến trúc thứ hai – và cũng là mẫu kiến trúc phổ biến nhất trong các doanh nghiệp công nghệ lớn hiện nay – chính là Microservices. "
    "Thay vì gom tất cả mã nguồn vào một khối duy nhất, Microservices xé nhỏ ứng dụng thành các dịch vụ độc lập. Điểm mấu chốt của Microservices là nguyên tắc "
    "'Database-per-Service': Mỗi dịch vụ sở hữu cơ sở dữ liệu riêng, cấm tuyệt đối việc truy cập chéo. Để điều phối, hệ thống cần một API Gateway làm cửa ngõ duy nhất. "
    "Mặc dù Microservices cho phép mở rộng quy mô độc lập tuyệt vời, nhưng nó đòi hỏi năng lực DevOps rất cao để quản lý hàng chục container phân tán."
)

# ==========================================
# SLIDE 16: 3.2.3. KIẾN TRÚC HƯỚNG SỰ KIỆN (EDA) & SERVERLESS
# ==========================================
s16 = prs.slides.add_slide(blank_slide_layout)
add_header(s16, "PHẦN 3: MỘT SỐ MẪU THIẾT KẾ KIẾN TRÚC PHỔ BIẾN (MỤC 3.3.2)", "3.2.3. EVENT-DRIVEN ARCHITECTURE (EDA) & SERVERLESS (FAAS)", "Xử lý luồng sự kiện bất đồng bộ và điện toán đám mây phi máy chủ")
add_footer(s16, 16)

create_card(s16, Inches(0.8), Inches(1.8), Inches(6.8), Inches(4.8))
tb = s16.shapes.add_textbox(Inches(1.0), Inches(1.95), Inches(6.4), Inches(4.5))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "1. Kiến trúc Hướng sự kiện (EDA):", "Tương tác bất đồng bộ thông qua các Sự kiện nghiệp vụ (Events) qua Event Broker (Apache Kafka, RabbitMQ).")
add_bullet_point(tf, "• Khớp nối lỏng lẻo (Loose Coupling):", "Producer chỉ bắn sự kiện vào hàng đợi, Consumer tự do đón nhận xử lý mà không cần đợi nhau.", level=1)
add_bullet_point(tf, "• Event Sourcing & CQRS:", "Lưu vết toàn bộ biến động trạng thái dưới dạng luồng sự kiện bất biến; tách rời luồng ghi và luồng đọc.", level=1)
add_bullet_point(tf, "2. Kiến trúc Không máy chủ (Serverless):", "Lập trình viên chỉ viết mã hàm logic (FaaS - AWS Lambda, Cloudflare Workers); nhà cung cấp Cloud quản trị 100% hạ tầng.")
add_bullet_point(tf, "• Tính phí theo thực tế (Pay-per-use):", "Tính tiền theo từng mili-giây hàm chạy; không có truy cập = Chi phí bằng 0 đồng.", level=1)
add_bullet_point(tf, "• Tự động co giãn đàn hồi:", "Mở rộng từ 0 lên 10.000 instances tức thời.", level=1)
add_bullet_point(tf, "• Thách thức:", "Độ trễ khởi động lại hàm (Cold Start) và rủi ro phụ thuộc nền tảng đám mây (Vendor Lock-in).", level=1)

add_image_card(s16, Inches(7.8), Inches(1.8), Inches(4.7), Inches(4.8), "images_chuong3/event_driven_queue.png", "Kiến trúc Hướng sự kiện (Event-Driven) và Hàng đợi Bất đồng bộ (Message Queue)")

add_speaker_note(
    s16,
    "Tiếp theo là hai mô hình đại diện cho xu hướng điện toán đám mây hiện đại: Event-Driven và Serverless. "
    "Trong kiến trúc hướng sự kiện, các thành phần không cần phải đợi nhau trả lời; khi có một sự kiện như 'Khách đặt món', "
    "một tin nhắn được bắn vào hàng đợi Kafka và các dịch vụ khác như Bếp hay Kho sẽ tự động đón nhận xử lý bất đồng bộ. "
    "Điều này giúp hệ thống đạt thông lượng cực kỳ khủng khiếp. Trong khi đó, Serverless đưa sự tự động hóa lên đỉnh cao: "
    "Quý vị không cần thuê máy chủ hàng tháng, chỉ khi nào có khách bấm gọi món thì một hàm FaaS trên đám mây mới khởi chạy "
    "và tính tiền trong vài mili-giây, giúp tiết kiệm chi phí tối đa cho doanh nghiệp."
)

# ==========================================
# SLIDE 17: 3.3. MA TRẬN SO SÁNH 5 MẪU HIỆN ĐẠI
# ==========================================
s17 = prs.slides.add_slide(blank_slide_layout)
add_header(s17, "PHẦN 3: MỘT SỐ MẪU THIẾT KẾ KIẾN TRÚC PHỔ BIẾN (MỤC 3.3.2)", "3.3. MA TRẬN SO SÁNH TOÀN DIỆN 5 MẪU KIẾN TRÚC HIỆN ĐẠI", "Bảng đối chiếu đa chiều về độ phức tạp, khả năng mở rộng, tính nhất quán và chi phí")
add_footer(s17, 17)

table_shape = s17.shapes.add_table(7, 6, Inches(0.8), Inches(1.8), Inches(11.7), Inches(4.8))
table = table_shape.table
col_w = [Inches(1.8), Inches(2.0), Inches(2.0), Inches(2.0), Inches(2.0), Inches(1.9)]
for i, w in enumerate(col_w):
    table.columns[i].width = w

table_data = [
    ["Tiêu chí So Sánh", "Clean Architecture", "Microservices", "Event-Driven (EDA)", "Serverless (FaaS)", "Space-Based (RAM)"],
    ["Bản chất Cốt lõi", "Đảo ngược phụ thuộc, độc lập Framework", "Phân tán theo Bounded Context", "Giao tiếp bất đồng bộ qua Broker", "Mã hàm phi trạng thái, Cloud quản lý", "Phân tán trên RAM, xóa nghẽn DB"],
    ["Khả năng Mở rộng", "Trung bình (Theo khối ứng dụng)", "Rất cao (Độc lập từng dịch vụ)", "Cực cao (Xử lý stream lớn)", "Tự động vô hạn (0 đến N instances)", "Cực cao (Mở rộng cụm RAM)"],
    ["Nhất quán Dữ liệu", "Rất cao (ACID CSDL chuẩn)", "Eventual Consistency (Saga)", "Eventual Consistency (Saga)", "Phụ thuộc CSDL (DynamoDB/SQL)", "Rất cao (Đồng bộ trên RAM tức thời)"],
    ["Độ Phức tạp DevOps", "Thấp - Vừa (Như Monolith)", "Cực cao (K8s, Tracing, Mesh)", "Cao (Quản trị Kafka/RabbitMQ)", "Thấp (Cloud provider gánh)", "Rất cao (Quản trị RAM phân tán)"],
    ["Độ Trễ Phản Hồi", "Cực thấp (Lời gọi hàm RAM)", "Trung bình (Độ trễ mạng Network)", "Thấp - Vừa (Bất đồng bộ)", "Trung bình (Bị ảnh hưởng Cold Start)", "Siêu thấp (Tính bằng Micro-giây)"],
    ["Phù hợp nhất với", "Nghiệp vụ lõi phức tạp, cần test sâu", "Doanh nghiệp lớn, nhiều nhóm dev", "Thời gian thực, IoT, tài chính", "Tác vụ nền, tải đột biến thất thường", "Sàn chứng khoán, bán vé concert flash-sale"]
]

for r_idx, row in enumerate(table_data):
    for c_idx, val in enumerate(row):
        cell = table.cell(r_idx, c_idx)
        cell.text = val
        p = cell.text_frame.paragraphs[0]
        p.alignment = PP_ALIGN.LEFT if c_idx == 0 else PP_ALIGN.CENTER
        run = p.runs[0]
        run.font.name = "Arial"
        if r_idx == 0:
            cell.fill.solid()
            cell.fill.fore_color.rgb = COLOR_NAVY_BLUE
            run.font.size = Pt(9.5)
            run.font.bold = True
            run.font.color.rgb = COLOR_WHITE
        else:
            cell.fill.solid()
            cell.fill.fore_color.rgb = COLOR_BG_LIGHT if r_idx % 2 == 1 else COLOR_WHITE
            run.font.size = Pt(8.5)
            run.font.color.rgb = COLOR_SLATE_DARK
            if c_idx == 0:
                run.font.bold = True

add_speaker_note(
    s17,
    "Slide 17 tổng hợp lại một bức tranh so sánh đa chiều giữa 5 phong cách kiến trúc hiện đại. Một lần nữa, chúng ta thấy rõ nguyên lý đánh đổi: "
    "Không có giải pháp nào hoàn hảo về mọi mặt. Nếu quý vị cần tốc độ phản hồi tính bằng micro-giây, Space-Based là số một nhưng chi phí mua RAM sẽ vô cùng đắt đỏ. "
    "Nếu quý vị muốn tối ưu hóa chi phí và nhân lực vận hành, Serverless hoặc Clean Architecture sẽ là lựa chọn khôn ngoan. "
    "Bảng ma trận này đóng vai trò như một kim chỉ nam giúp các kỹ sư công nghệ lựa chọn đúng vũ khí cho bài toán của mình."
)

# ==========================================
# SLIDE 18: 3.4. ĐIỂM NGHẼN & THIẾT KẾ KIẾN TRÚC ĐÍCH CHO NHÀ HÀNG
# ==========================================
s18 = prs.slides.add_slide(blank_slide_layout)
add_header(s18, "PHẦN 3: MỘT SỐ MẪU THIẾT KẾ KIẾN TRÚC PHỔ BIẾN (MỤC 3.3.2)", "3.4. ĐỊNH HƯỚNG ỨNG DỤNG: THIẾT KẾ KIẾN TRÚC ĐÍCH CHO DỰ ÁN", "Nhận diện điểm nghẽn chuỗi 50 nhà hàng và Bản thiết kế Target Architecture Hybrid Microservices + EDA")
add_footer(s18, 18)

create_card(s18, Inches(0.8), Inches(1.8), Inches(6.8), Inches(4.8))
tb = s18.shapes.add_textbox(Inches(1.0), Inches(1.95), Inches(6.4), Inches(4.5))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "Điểm nghẽn khi mở rộng chuỗi 50 nhà hàng:", "Vào giờ cao điểm (11h30 & 18h30), hàng ngàn khách quét QR cùng lúc làm cạn kiệt MySQL connections và tiến trình Apache, khiến quầy thu ngân và kho bị tê liệt.")
add_bullet_point(tf, "Bản Thiết kế Kiến trúc Đích (Target Architecture):", "Mô hình lai phân tán Microservices kết hợp Event-Driven (EDA):")
add_bullet_point(tf, "• API Gateway (Kong):", "Đón nhận, rate-limiting và bảo vệ an toàn lưu lượng truy cập.", level=1)
add_bullet_point(tf, "• 1. Order & Table Service (Node.js + Redis):", "Xử lý khách quét QR gọi món tốc độ cao, chịu tải > 10.000 RPS với độ trễ < 20ms.", level=1)
add_bullet_point(tf, "• 2. Kitchen Display Service (KDS):", "Kết nối WebSocket hai chiều đẩy đơn vào màn hình bếp tức thì.", level=1)
add_bullet_point(tf, "• 3. Inventory Service (PHP Core):", "Kế thừa nghiệp vụ phiếu nhập kho (InventoryReceipt) và công thức (recipe) với CSDL PostgreSQL ACID.", level=1)
add_bullet_point(tf, "• 4. Event Broker (Apache Kafka):", "Điều phối luồng sự kiện OrderCreated, PaymentSuccess, InventoryDepleted.", level=1)

add_image_card(s18, Inches(7.8), Inches(1.8), Inches(4.7), Inches(4.8), "images_chuong3/microservices_app_layer.png", "Sơ đồ Kiến trúc Đích Đề xuất cho Hệ thống Chuỗi Nhà hàng")

add_speaker_note(
    s18,
    "Áp dụng những kiến thức hiện đại trên vào tương lai của dự án php-restaurant: Giả sử chuỗi nhà hàng của chúng em phát triển lên 50 chi nhánh. "
    "Vào giờ cao điểm trưa, hàng ngàn thực khách quét QR cùng lúc sẽ làm sập máy chủ MySQL của hệ thống Monolith hiện tại. "
    "Do đó, nhóm em đã thiết kế sẵn một Kiến trúc Đích (Target Architecture) kết hợp giữa Microservices và Event-Driven. "
    "Chúng em tách riêng module QR Order chạy bằng Node.js và Redis để gánh toàn bộ lưu lượng tải của khách hàng; "
    "module Bếp sử dụng WebSocket để báo món tức thời; trong khi module Nhập kho nguyên liệu cốt lõi vẫn kế thừa logic PHP vững chắc với CSDL PostgreSQL. "
    "Toàn bộ các dịch vụ được kết nối mượt mà qua luồng sự kiện của Apache Kafka."
)

# ==========================================
# SLIDE 19: 3.4. LỘ TRÌNH CHUYỂN ĐỔI: STRANGLER FIG PATTERN
# ==========================================
s19 = prs.slides.add_slide(blank_slide_layout)
add_header(s19, "PHẦN 3: MỘT SỐ MẪU THIẾT KẾ KIẾN TRÚC PHỔ BIẾN (MỤC 3.3.2)", "3.4. LỘ TRÌNH CHUYỂN ĐỔI AN TOÀN: STRANGLER FIG PATTERN", "Chiến lược di chuyển từng bước của Martin Fowler bảo đảm nhà hàng hoạt động 24/7 không gián đoạn")
add_footer(s19, 19)

create_card(s19, Inches(0.8), Inches(1.8), Inches(6.8), Inches(4.8))
tb = s19.shapes.add_textbox(Inches(1.0), Inches(1.95), Inches(6.4), Inches(4.5))
tf = tb.text_frame
tf.word_wrap = True
add_bullet_point(tf, "Mẫu Cây Đa Siết (Strangler Fig Pattern):", "Nguyên lý của Martin Fowler: Tuyệt đối tránh 'Đập đi xây lại' (Big Bang Rewrite); thay vào đó trồng một hệ thống mới bao bọc dần hệ thống cũ cho đến khi cũ biến mất.")
add_bullet_point(tf, "Lộ trình 4 Giai đoạn Di chuyển Thực tế:", "")
add_bullet_point(tf, "• Giai đoạn 1 (Thiết lập Gateway):", "Đặt API Gateway (Kong / NGINX) đứng trước Monolith PHP hiện tại; 100% traffic ban đầu vẫn đi vào Monolith cũ.", level=1)
add_bullet_point(tf, "• Giai đoạn 2 (Tách module QR Order):", "Xây dựng service Order mới (Node.js). Cấu hình Gateway bẻ hướng toàn bộ luồng gọi món QR sang service mới; kho và kế toán giữ nguyên.", level=1)
add_bullet_point(tf, "• Giai đoạn 3 (Tách module Bếp KDS & Thanh toán):", "Tách màn hình bếp WebSocket và cổng VietQR, tích hợp đồng bộ qua Kafka.", level=1)
add_bullet_point(tf, "• Giai đoạn 4 (Triệt tiêu Monolith cũ):", "Khi toàn bộ module đã hoạt động độc lập ổn định, Monolith cũ chính thức được giải phóng mà chuỗi nhà hàng không mất 1 giây gián đoạn kinh doanh.", level=1)

add_image_card(s19, Inches(7.8), Inches(1.8), Inches(4.7), Inches(4.8), "images_chuong3/api_gateway_proxy.png", "Cơ chế API Gateway Điều phối Dòng chảy Chuyển đổi Kiến trúc")

add_speaker_note(
    s19,
    "Một sai lầm chết người mà nhiều dự án mắc phải là 'đập đi xây lại' toàn bộ hệ thống cũ – điều này dễ dẫn đến thảm họa ngừng trệ kinh doanh. "
    "Nhóm em lựa chọn chiến lược Strangler Fig Pattern – tức là 'Mẫu cây đa siết' do Martin Fowler đề xuất. Chúng em sẽ trồng một 'cây đa mới' "
    "bằng cách đặt một API Gateway phía trước, sau đó bóc tách từng cành nhánh – bắt đầu từ tính năng quét QR gọi món. "
    "Khi tính năng mới chạy ổn định, chúng em chuyển hướng dần lưu lượng sang đó, giữ nguyên hệ thống cũ cho các nghiệp vụ nội bộ. "
    "Cứ như vậy, hệ thống mới sẽ lớn dần và thay thế hoàn toàn hệ thống cũ một cách êm đẹp, bảo đảm chuỗi nhà hàng luôn mở cửa kinh doanh 24/7."
)

# ==========================================
# SLIDE 20: TỔNG KẾT BÀI HỌC & HỎI ĐÁP (CONCLUSION & Q&A)
# ==========================================
s20 = prs.slides.add_slide(blank_slide_layout)
bg = s20.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0), Inches(0), Inches(13.333), Inches(7.5))
bg.fill.solid()
bg.fill.fore_color.rgb = COLOR_NAVY_DARK
bg.line.fill.background()

# Gold accent line
accent = s20.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(1.2), Inches(1.2), Inches(2.5), Inches(0.08))
accent.fill.solid()
accent.fill.fore_color.rgb = COLOR_AMBER_GOLD
accent.line.fill.background()

tb20 = s20.shapes.add_textbox(Inches(1.2), Inches(1.5), Inches(10.8), Inches(5.0))
tf20 = tb20.text_frame
tf20.word_wrap = True

p = tf20.paragraphs[0]
r = p.add_run()
r.text = "TỔNG KẾT BÀI HỌC & THÔNG ĐIỆP ĐÚC KẾT"
r.font.name = "Arial"
r.font.size = Pt(24)
r.font.bold = True
r.font.color.rgb = COLOR_WHITE

p1 = tf20.add_paragraph()
p1.space_before = Pt(16)
r1 = p1.add_run()
r1.text = "1. Đánh giá kiến trúc là một quá trình liên tục (Continuous Evaluation):\n"
r1.font.name = "Arial"
r1.font.bold = True
r1.font.size = Pt(13.5)
r1.font.color.rgb = COLOR_AMBER_GOLD
r1_sub = p1.add_run()
r1_sub.text = "Không có kiến trúc tốt nhất, chỉ có kiến trúc phù hợp nhất với giai đoạn phát triển của doanh nghiệp. Monolith MVC là lựa chọn tối ưu hiện tại; Hybrid Microservices là đích đến tương lai."
r1_sub.font.name = "Arial"
r1_sub.font.size = Pt(11.5)
r1_sub.font.color.rgb = RGBColor(226, 232, 240)

p2 = tf20.add_paragraph()
p2.space_before = Pt(12)
r2 = p2.add_run()
r2.text = "2. Phổ mẫu thiết kế bảo đảm tính nhất quán kỹ thuật (Spectrum Alignment):\n"
r2.font.name = "Arial"
r2.font.bold = True
r2.font.size = Pt(13.5)
r2.font.color.rgb = COLOR_AMBER_GOLD
r2_sub = p2.add_run()
r2_sub.text = "Tư duy theo dải phổ giúp gắn kết chặt chẽ giữa Chiến lược kiến trúc (Macro) -> Thiết kế hướng đối tượng (Meso) -> Cú pháp an toàn (Micro Idiom), phòng chống triệt để xói mòn kiến trúc."
r2_sub.font.name = "Arial"
r2_sub.font.size = Pt(11.5)
r2_sub.font.color.rgb = RGBColor(226, 232, 240)

p3 = tf20.add_paragraph()
p3.space_before = Pt(12)
r3 = p3.add_run()
r3.text = "3. Kiến trúc phần mềm phải phục vụ giá trị kinh doanh (Business Value First):\n"
r3.font.name = "Arial"
r3.font.bold = True
r3.font.size = Pt(13.5)
r3.font.color.rgb = COLOR_AMBER_GOLD
r3_sub = p3.add_run()
r3_sub.text = "Mọi quyết định kỹ thuật phải hướng tới việc nâng cao hiệu quả vận hành của nhà hàng và trải nghiệm gọi món của thực khách."
r3_sub.font.name = "Arial"
r3_sub.font.size = Pt(11.5)
r3_sub.font.color.rgb = RGBColor(226, 232, 240)

p4 = tf20.add_paragraph()
p4.space_before = Pt(24)
p4.alignment = PP_ALIGN.CENTER
r4 = p4.add_run()
r4.text = "XIN TRÂN TRỌNG CẢM ƠN THẦY/CÔ VÀ CÁC BẠN ĐÃ LẮNG NGHE!\nQ&A — NHÓM XIN SẴN SÀNG LẮNG NGHE CÂU HỎI VÀ Ý KIẾN ĐÓNG GÓP"
r4.font.name = "Arial"
r4.font.bold = True
r4.font.size = Pt(14)
r4.font.color.rgb = COLOR_WHITE

add_speaker_note(
    s20,
    "Kính thưa Thầy/Cô, thông điệp cuối cùng mà nhóm muốn đúc kết qua bài báo cáo hôm nay chính là: "
    "Kiến trúc phần mềm không phải là một mô hình tĩnh trên trang giấy, mà là một sinh thể sống đồng hành cùng sự phát triển của doanh nghiệp. "
    "Việc chúng em chọn Monolith MVC cho dự án php-restaurant ở thời điểm hiện tại là hoàn toàn đúng đắn về mặt kỹ thuật và chi phí; "
    "đồng thời việc chúng em chuẩn bị sẵn lộ trình Strangler Fig để tiến hóa sang Microservices thể hiện tầm nhìn dài hạn của người kỹ sư phần mềm. "
    "Nhóm xin chân thành cảm ơn Thầy/Cô và các bạn đã lắng nghe. Chúng em rất mong nhận được những nhận xét và câu hỏi đóng góp quý báu từ Thầy/Cô!"
)

pptx_output_path = "Bao_Cao_Chuong_3_Thiet_Ke_Kien_Truc.pptx"
prs.save(pptx_output_path)
print(f"SUCCESS: Generated {pptx_output_path} ({os.path.getsize(pptx_output_path)} bytes) with 20 slides!")
