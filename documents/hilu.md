# Tong hop toan bo luong va use case cua du an PHP Restaurant

## 1) Pham vi va nguon doi chieu

Tai lieu nay duoc tong hop truc tiep tu code hien co trong:
- app/controllers/*.php
- app/models/*.php
- core/App.php
- database/schema.sql va cac migration trong database/

Route chung cua he thong:
- /{controller}/{method}/{params}
- Vi du: /sale_order/edit/5, /report/stock_report, /public_order/start?token=...

## 2) Actors (chu the su dung)

- Admin: quan ly user, toan bo module.
- Manager: van hanh kho, bao cao, ban, don hang.
- Staff/Waiter/Cashier: tao don, phuc vu, thanh toan, cap nhat kho theo vai tro van hanh.
- Customer cong khai (qua QR): dat mon khong can dang nhap.

## 3) Danh muc use case theo module/controller

### UC-AUTH - Xac thuc va tai khoan (AuthController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhieu nhanh loi/chuyen huong | Route |
|---|---|---|---|---|---|---|
| UC-AUTH-01 | Mo man hinh dang nhap | Guest | Chua login | Hien thi form login | Neu da login thi redirect dashboard | GET /auth/login |
| UC-AUTH-02 | Dang nhap | Guest | Gui POST JSON username/password | Kiem tra user ton tai + dung mat khau + active, tao JWT, tra JSON token | Sai username/password -> 401; thieu du lieu -> 400; sai method -> 405 | POST /auth/doLogin |
| UC-AUTH-03 | Dang xuat | User da login | Co session/cookie | Huy session, xoa cookie, redirect login | Khong co | GET /auth/logout |
| UC-AUTH-04 | Verify token | User/API client | Co JWT | Tra ve thong tin user neu token hop le | Token khong hop le/het han -> 401 | GET/POST /auth/verify |
| UC-AUTH-05 | Refresh token | User/API client | Co JWT hop le | Tao token moi va tra JSON | Token khong hop le -> 401 | GET/POST /auth/refresh |
| UC-AUTH-06 | Mo man hinh dang ky | Guest | Chua login | Hien thi form register | Neu da login thi redirect dashboard | GET /auth/register |
| UC-AUTH-07 | Dang ky tai khoan | Guest | POST JSON day du | Validate fullname/username/password/role, tao user moi (hash password) | Thieu du lieu, password < 6, role sai, username trung, loi server | POST /auth/doRegister |

### UC-DASH - Dashboard tong quan (DashboardController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-DASH-01 | Xem dashboard | User da login | JWT hop le | Lay thong ke tong (ingredient/menu/table/user/receipt/issue/expense), doanh thu hom nay, don gan day, chi phi gan day | Chua login -> redirect auth/login | GET /dashboard |

### UC-USER - Quan ly nguoi dung (UserController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-USER-01 | Xem danh sach user | Admin | role=admin | Phan trang danh sach users | Khong phai admin -> redirect dashboard | GET /user |
| UC-USER-02 | Mo form tao user | Admin | role=admin | Hien thi form tao | Khong phai admin -> redirect dashboard | GET /user/create |
| UC-USER-03 | Tao user | Admin | role=admin + du lieu hop le | Validate, check duplicate username, createUser (hash password) | Thieu du lieu/trung username | POST /user/store |
| UC-USER-04 | Mo form sua user | Admin | role=admin + id ton tai | Hien thi thong tin user | id khong ton tai | GET /user/edit/{id} |
| UC-USER-05 | Cap nhat user | Admin | role=admin + id ton tai | Validate, check duplicate username, updateUser (hash password neu co) | Thieu du lieu/trung username | POST /user/update/{id} |
| UC-USER-06 | Xoa user | Admin | role=admin + id ton tai | Xoa user | Chan xoa chinh minh | GET /user/delete/{id} |

### UC-INGCAT - Quan ly loai nguyen lieu (IngredientCategoryController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-INGCAT-01 | Xem danh sach loai | User da login | JWT hop le | Lay danh sach phan trang | Chua login -> redirect | GET /ingredient_category |
| UC-INGCAT-02 | Mo form tao loai | User da login | JWT hop le | Hien thi form tao | Chua login | GET /ingredient_category/create |
| UC-INGCAT-03 | Tao loai | User da login | POST co name | Validate + check duplicate name + insert | Thieu name/trung name | POST /ingredient_category/store |
| UC-INGCAT-04 | Mo form sua loai | User da login | id ton tai | Hien thi form sua | id khong ton tai | GET /ingredient_category/edit/{id} |
| UC-INGCAT-05 | Cap nhat loai | User da login | id ton tai + data hop le | Validate + check duplicate + update | Thieu name/trung name | POST /ingredient_category/update/{id} |
| UC-INGCAT-06 | Xoa loai | User da login | id ton tai | Xoa ban ghi | id sai | GET /ingredient_category/delete/{id} |

### UC-ING - Quan ly nguyen lieu (IngredientController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-ING-01 | Xem danh sach nguyen lieu + ton hien tai | User da login | JWT hop le | Join inventory_log de tinh current_qty (SUM qty_change), phan trang | Chua login | GET /ingredient |
| UC-ING-02 | Mo form tao nguyen lieu | User da login | JWT hop le | Tai danh sach category va hien thi form | Chua login | GET /ingredient/create |
| UC-ING-03 | Tao nguyen lieu | User da login | POST co code, name | Validate + check duplicate code + insert | Thieu du lieu/trung code | POST /ingredient/store |
| UC-ING-04 | Mo form sua nguyen lieu | User da login | id ton tai | Hien thi du lieu + category | id khong ton tai | GET /ingredient/edit/{id} |
| UC-ING-05 | Cap nhat nguyen lieu | User da login | id ton tai + data hop le | Validate + check duplicate code + update | Thieu du lieu/trung code | POST /ingredient/update/{id} |
| UC-ING-06 | Xoa nguyen lieu | User da login | id ton tai | Xoa ban ghi | id sai | GET /ingredient/delete/{id} |

### UC-MENU - Quan ly mon an (MenuItemController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-MENU-01 | Xem danh sach mon | User da login | JWT hop le | Phan trang menu_item | Chua login | GET /menu_item |
| UC-MENU-02 | Mo form tao mon | User da login | JWT hop le | Hien thi form | Chua login | GET /menu_item/create |
| UC-MENU-03 | Tao mon | User da login | POST co code/name/price | Validate + check duplicate code + insert | Thieu/trung code | POST /menu_item/store |
| UC-MENU-04 | Mo form sua mon | User da login | id ton tai | Hien thi du lieu mon | id khong ton tai | GET /menu_item/edit/{id} |
| UC-MENU-05 | Cap nhat mon | User da login | id ton tai + data hop le | Validate + check duplicate code + update | Thieu/trung code | POST /menu_item/update/{id} |
| UC-MENU-06 | Xoa mon | User da login | id ton tai | Xoa ban ghi | id sai | GET /menu_item/delete/{id} |

### UC-RECIPE - Quan ly cong thuc (RecipeController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-RECIPE-01 | Chon menu de xem cong thuc | User da login | JWT hop le | Neu chua co menu_id: hien select_menu, co search va pagination | Chua login | GET /recipe |
| UC-RECIPE-02 | Xem cong thuc theo menu | User da login | JWT hop le + menu_id | Lay cong thuc theo menu_id va phan trang | menu_id sai co the danh sach rong | GET /recipe?menu_id={id} |
| UC-RECIPE-03 | Mo form tao cong thuc | User da login | JWT hop le | Tai menu + ingredient de chon, ho tro menu_id preselect | Chua login | GET /recipe/create |
| UC-RECIPE-04 | Tao cong thuc | User da login | POST co menu_id, ingredient_id, qty | Validate + insert + redirect ve danh sach cong thuc cua menu | Thieu du lieu | POST /recipe/store |
| UC-RECIPE-05 | Mo form sua cong thuc | User da login | id ton tai | Hien thi recipe + menu + ingredient | id khong ton tai | GET /recipe/edit/{id} |
| UC-RECIPE-06 | Cap nhat cong thuc | User da login | id ton tai + data hop le | Validate + update + redirect theo menu | Thieu du lieu | POST /recipe/update/{id} |
| UC-RECIPE-07 | Xoa cong thuc | User da login | id ton tai | Xoa recipe | id sai | GET /recipe/delete/{id} |

### UC-TABLE - Quan ly ban an (RestaurantTableController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-TABLE-01 | Xem danh sach ban | User da login | JWT hop le | Lay danh sach phan trang | Chua login | GET /restaurant_table |
| UC-TABLE-02 | Mo form tao ban | User da login | JWT hop le | Hien thi form | Chua login | GET /restaurant_table/create |
| UC-TABLE-03 | Tao ban | User da login | POST co number | Validate + check duplicate number + insert (status mac dinh free) | Thieu/trung number | POST /restaurant_table/store |
| UC-TABLE-04 | Mo form sua ban | User da login | id ton tai | Hien thi form sua | id khong ton tai | GET /restaurant_table/edit/{id} |
| UC-TABLE-05 | Cap nhat ban | User da login | id ton tai + data hop le | Validate + duplicate check + update number/status | Thieu/trung number | POST /restaurant_table/update/{id} |
| UC-TABLE-06 | Xoa ban | User da login | id ton tai | Xoa ban ghi | id sai | GET /restaurant_table/delete/{id} |

### UC-SALE - Quan ly don hang noi bo (SaleOrderController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-SALE-01 | Xem danh sach don | User da login | JWT hop le | Lay danh sach don + so ban, phan trang | Chua login | GET /sale_order |
| UC-SALE-02 | Mo form tao don | User da login | JWT hop le | Tai danh sach ban + mon | Chua login | GET /sale_order/create |
| UC-SALE-03 | Tao don moi | User da login | POST qty[] co it nhat 1 mon | Build chi tiet don, tinh tong tien, check ton kho qua Recipe::checkInventoryForMenu, insert sale_order + details, doi trang thai ban occupied | Khong chon mon, khong du ton kho -> warning + redirect | POST /sale_order/store |
| UC-SALE-04 | Mo form sua don | User da login | id ton tai | Lay thong tin don + details + map qty | id khong ton tai | GET /sale_order/edit/{id} |
| UC-SALE-05 | Cap nhat don | User da login | id ton tai + data hop le | Replace toan bo details, cap nhat order header | Khong chon mon | POST /sale_order/update/{id} |
| UC-SALE-06 | Xoa don | User da login | id ton tai | Xoa details truoc, xoa order sau | id sai | GET /sale_order/delete/{id} |
| UC-SALE-07 | Danh dau phuc vu xong | User da login | id ton tai | Update order.status=served, auto tao inventory_issue tu recipe, auto ghi inventory_log am | id sai | GET /sale_order/complete/{id} |
| UC-SALE-08 | Thanh toan don | User da login | id ton tai | Update status=paid, gan cashier_id, giai phong ban (free) | id sai | GET /sale_order/pay/{id} |
| UC-SALE-09 | Huy don | User da login | id ton tai va chua paid | Update status=cancel, giai phong ban | Don da paid thi khong cho huy | GET /sale_order/cancel/{id} |
| UC-SALE-10 | Mo form them mon vao don | User da login | id ton tai + order.status=open | Tai menu de them mon | Don khong ton tai hoac status khac open | GET /sale_order/addItem/{id} |
| UC-SALE-11 | Luu mon them vao don | User da login | POST items JSON hop le | Insert them sale_order_detail status=ordered, cong them total_amount | Khong co item/loi parse/loi DB | POST /sale_order/saveAddItems/{id} |

### UC-PORDER - Dat mon cong khai qua QR (PublicOrderController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-PORDER-01 | Mo trang dat mon qua token | Customer | Co token trong query | Validate token theo restaurant_table.order_token, hien menu theo ban | Thieu token/token sai -> trang error | GET /public_order/start?token=... |
| UC-PORDER-02 | Gui don qua QR | Customer | Token hop le + chon mon | Tao sale_order(source='qr', status='open', customer info), tao details(status='ordered'), set ban occupied, commit transaction | Token sai, khong chon mon, loi DB -> rollback + error | POST /public_order/submit |

### UC-QR - Quan ly QR cho ban (QrController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-QR-01 | Xem trang QR | User da login | JWT hop le | Hien danh sach ban va token hien tai | Chua login | GET /qr |
| UC-QR-02 | Sinh token QR cho ban | User da login | id ban ton tai | Tao token random, update order_token cho ban | id thieu/sai -> redirect | GET /qr/generate/{id} |
| UC-QR-03 | Xoa token QR cua ban | User da login | id ban ton tai | Set order_token = null | id thieu/sai -> redirect | GET /qr/clear/{id} |
| UC-QR-04 | Tai file QR PNG | User (khong bat buoc JWT trong method) | Ban ton tai + co order_token | Tao link public_order/start?token=..., goi api.qrserver.com lay PNG va tra file attachment | Ban khong ton tai/chua co token/khong tai duoc PNG | GET /qr/download/{id} |

### UC-RECEIPT - Nhap kho (InventoryReceiptController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-RECEIPT-01 | Xem danh sach phieu nhap | User da login | JWT hop le | Lay danh sach phan trang + creator | Chua login | GET /inventory_receipt |
| UC-RECEIPT-02 | Mo form tao phieu nhap | User da login | JWT hop le | Tai ingredients; ho tro quickIngredient/quickQty tu query | Chua login | GET /inventory_receipt/create |
| UC-RECEIPT-03 | Mo form tao tu restock cart | User da login | JWT hop le | Mo cung view create voi co fromRestock=true | Chua login | GET /inventory_receipt/create_from_restock |
| UC-RECEIPT-04 | Tao phieu nhap pending | User da login | POST co receipt_date | Insert inventory_receipt(status='pending') + insert details | Thieu receipt_date | POST /inventory_receipt/store |
| UC-RECEIPT-05 | Mo form sua phieu nhap | User da login | id ton tai | Hien receipt + details + ingredients | id sai | GET /inventory_receipt/edit/{id} |
| UC-RECEIPT-06 | Cap nhat phieu nhap | User da login | id ton tai + data hop le | Update header + replace details | Thieu receipt_date | POST /inventory_receipt/update/{id} |
| UC-RECEIPT-07 | Xoa phieu nhap | User da login | id ton tai | Xoa details, xoa phieu | id sai | GET /inventory_receipt/delete/{id} |
| UC-RECEIPT-08 | Hoan thanh phieu nhap | User da login | id ton tai + status chua completed | Ghi inventory_log type='receipt' qty duong cho tung detail, update status='completed' | id sai/da completed | GET /inventory_receipt/complete/{id} |

### UC-ISSUE - Xuat kho (InventoryIssueController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-ISSUE-01 | Xem danh sach phieu xuat | User da login | JWT hop le | Lay danh sach phan trang + creator | Chua login | GET /inventory_issue |
| UC-ISSUE-02 | Mo form tao phieu xuat | User da login | JWT hop le | Tai danh sach ingredients | Chua login | GET /inventory_issue/create |
| UC-ISSUE-03 | Tao phieu xuat pending | User da login | POST co issue_date | Insert inventory_issue (mac dinh pending) + details | Thieu issue_date | POST /inventory_issue/store |
| UC-ISSUE-04 | Mo form sua phieu xuat | User da login | id ton tai | Hien issue + details + ingredients | id sai | GET /inventory_issue/edit/{id} |
| UC-ISSUE-05 | Cap nhat phieu xuat | User da login | id ton tai + data hop le | Update header + replace details | Thieu issue_date | POST /inventory_issue/update/{id} |
| UC-ISSUE-06 | Xoa phieu xuat | User da login | id ton tai | Xoa details va phieu | id sai | GET /inventory_issue/delete/{id} |
| UC-ISSUE-07 | Hoan thanh phieu xuat | User da login | id ton tai | Ghi inventory_log am cho tung detail (type issue/expire theo issue_type), thong bao hoan thanh | id sai | GET /inventory_issue/complete/{id} |
| UC-ISSUE-08 | Luu y ky thuat cua luong complete issue | User da login | - | Method complete hien KHONG update cot inventory_issue.status sang completed | Co the gay lech du lieu status va ton kho | GET /inventory_issue/complete/{id} |

### UC-REPORT - Bao cao va thao tac kho tu bao cao (ReportController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-REPORT-01 | Bao cao doanh thu/chi phi theo ngay | User da login | JWT hop le | Lay range ngay (mac dinh 7 ngay), tong hop revenue tu sale_order, fixed expense tu expense, ingredient cost tu recipe*purchasing, tinh net, phan trang theo ngay | Chua login | GET /report |
| UC-REPORT-02 | Bao cao ton kho | User da login | JWT hop le | Tinh current_qty tu inventory_log, phan loai critical/warning/normal, hien issue gan day (10 ngay) | Chua login | GET /report/stock_report |
| UC-REPORT-03 | Mo form xuat kho thu cong | User da login | JWT hop le | Tai danh sach nguyen lieu de nhap items[] | Chua login | GET /report/add_stock_out |
| UC-REPORT-04 | Luu xuat kho thu cong ngay lap tuc | User da login | POST items[] hop le | Tao inventory_issue status='completed', tao detail, ghi inventory_log am, log audit | Neu item rong thi co the khong co dong detail | POST /report/save_stock_out |

### UC-RES - Dat cho (ReservationController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-RES-01 | Xem danh sach dat cho | User da login | JWT hop le | Lay reservation phan trang | Chua login | GET /reservation |
| UC-RES-02 | Mo form tao dat cho | User da login | JWT hop le | Tai danh sach ban va hien form | Chua login | GET /reservation/create |
| UC-RES-03 | Tao dat cho | User da login | POST du table/customer/start/end | Chuan hoa datetime, validate end > start, check overlap (status != cancelled), insert reservation | Trung lich, thieu du lieu, sai time | POST /reservation/store |
| UC-RES-04 | Mo form sua dat cho | User da login | id ton tai | Hien data dat cho + danh sach ban | id sai | GET /reservation/edit/{id} |
| UC-RES-05 | Cap nhat dat cho | User da login | id ton tai + data hop le | Validate, check overlap (exclude current), update reservation | Trung lich, thieu du lieu, sai time | POST /reservation/update/{id} |
| UC-RES-06 | Xoa dat cho | User da login | id ton tai | Xoa reservation | id sai | GET /reservation/delete/{id} |

### UC-EXP - Chi phi (ExpenseController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-EXP-01 | Xem danh sach chi phi | User da login | JWT hop le | Lay danh sach phan trang + creator | Chua login | GET /expense |
| UC-EXP-02 | Mo form tao chi phi | User da login | JWT hop le | Hien thi form | Chua login | GET /expense/create |
| UC-EXP-03 | Tao chi phi | User da login | POST expense_type/amount/expense_date | Validate va insert | Thieu du lieu | POST /expense/store |
| UC-EXP-04 | Mo form sua chi phi | User da login | id ton tai | Hien thi thong tin chi phi | id sai | GET /expense/edit/{id} |
| UC-EXP-05 | Cap nhat chi phi | User da login | id ton tai + data hop le | Validate va update | Thieu du lieu | POST /expense/update/{id} |
| UC-EXP-06 | Xoa chi phi | User da login | id ton tai | Xoa ban ghi | id sai | GET /expense/delete/{id} |

### UC-HOME - Trang goc (HomeController)

| UC ID | Use case | Actor | Tien dieu kien | Luong chinh | Nhanh loi | Route |
|---|---|---|---|---|---|---|
| UC-HOME-01 | Truy cap trang goc | Moi actor | Khong | Redirect den auth/login | Khong co | GET / |

## 4) Luong nghiep vu end-to-end quan trong

### Flow F1 - Don noi bo tu tao den thanh toan

1. Nhan vien tao don o /sale_order/create va submit /sale_order/store.
2. He thong kiem tra ton kho theo cong thuc tung mon; neu thieu thi canh bao va khong tao don.
3. Neu tao thanh cong: luu sale_order + sale_order_detail, doi trang thai ban sang occupied.
4. Khi phuc vu xong: /sale_order/complete/{id} -> status don sang served.
5. Trong buoc complete: he thong auto tao inventory_issue tu cong thuc mon va ghi inventory_log am.
6. Thu ngan thanh toan /sale_order/pay/{id} -> status sang paid, gan cashier_id, tra ban ve free.

### Flow F2 - Huy don noi bo

1. Tu don dang mo/phuc vu, goi /sale_order/cancel/{id}.
2. Neu don da paid thi khong cho huy.
3. Neu huy hop le: status=cancel va giai phong ban.

### Flow F3 - Khach dat mon qua QR

1. Nhan vien tao token QR cho ban qua /qr/generate/{id}.
2. Khach quet QR vao /public_order/start?token=...
3. Khach chon mon + thong tin lien he, submit /public_order/submit.
4. He thong tao sale_order source='qr', tao details status='ordered', doi ban occupied.

### Flow F4 - Nhap kho co kiem soat pending/completed

1. Tao phieu nhap /inventory_receipt/store (status mac dinh pending).
2. Chinh sua neu can qua /inventory_receipt/update/{id}.
3. Hoan thanh /inventory_receipt/complete/{id}.
4. He thong moi ghi inventory_log type receipt va update status completed.

### Flow F5 - Xuat kho thu cong theo phieu

1. Tao phieu xuat pending qua /inventory_issue/store.
2. Hoan thanh qua /inventory_issue/complete/{id}.
3. He thong ghi inventory_log am voi type issue hoac expire.
4. Luu y: code hien tai khong update status issue sang completed trong method complete.

### Flow F6 - Xuat kho nhanh tu bao cao

1. Mo /report/add_stock_out.
2. Submit /report/save_stock_out voi items[].
3. He thong tao inventory_issue status='completed', tao details va ghi inventory_log ngay lap tuc.

### Flow F7 - Bao cao loi nhuan theo ngay

1. Nguoi dung mo /report?start_date=...&end_date=...
2. He thong tong hop doanh thu (sale_order), chi phi co dinh (expense), chi phi nguyen lieu (recipe * purchase_price).
3. Tra ve bang tong hop theo ngay + tong ket period.

### Flow F8 - Dat cho ban

1. Tao dat cho tai /reservation/store.
2. Validate start/end va overlap theo table.
3. Luu status reservation (pending/confirmed/cancelled/completed theo du lieu).
4. CRUD tiep theo qua edit/update/delete.

## 5) Ma tran trang thai (state matrix)

### 5.1 Sale order

- Trang thai DB: open, served, paid, cancel.
- Chuyen trang thai theo code:
	- open -> served (complete)
	- open -> cancel (cancel)
	- served -> paid (pay)
	- open -> paid (pay duoc goi truc tiep)
	- served -> cancel (khong bi chan boi code)
	- paid -> cancel (bi chan)

### 5.2 Sale order detail

- Trang thai DB: ordered, cooked, served, canceled.
- Code hien tai chu yeu tao moi voi status='ordered'.
- Chua thay luong cap nhat cooked/served/canceled trong controller hien co.

### 5.3 Inventory receipt

- Trang thai DB: pending, completed.
- store => pending.
- complete => ghi inventory_log receipt + set completed.

### 5.4 Inventory issue

- Trang thai DB: pending, completed.
- store => pending.
- report/save_stock_out => tao luon completed.
- inventory_issue/complete => ghi inventory_log, nhung chua update status completed (khac voi ten use case).
- sale_order/complete -> auto tao issue (khong truyen status, nen dung default pending) nhung da ghi inventory_log ngay.

### 5.5 Restaurant table

- Trang thai DB: free, occupied, reserved.
- Chuyen theo code:
	- Tao/sua ban: set tuy chon.
	- Tao don noi bo hoac don QR: occupied.
	- Pay/cancel don: free.
- Chua thay auto set reserved/free theo lich reservation trong controller.

### 5.6 Reservation

- Model xu ly overlap voi gia dinh status co cancelled.
- Trang thai du kien dung trong code: pending, confirmed, cancelled, completed.
- Schema.sql hien tai chua chua bang reservation (can doi chieu migration/thuc te DB).

### 5.7 User active

- users.active: boolean.
- authenticate chi cho phep dang nhap neu active = true.

## 6) Danh sach route tong hop theo module

### Auth
- GET /auth/login
- POST /auth/doLogin
- GET /auth/logout
- GET|POST /auth/verify
- GET|POST /auth/refresh
- GET /auth/register
- POST /auth/doRegister

### Dashboard/Home
- GET /
- GET /dashboard

### User
- GET /user
- GET /user/create
- POST /user/store
- GET /user/edit/{id}
- POST /user/update/{id}
- GET /user/delete/{id}

### Ingredient Category
- GET /ingredient_category
- GET /ingredient_category/create
- POST /ingredient_category/store
- GET /ingredient_category/edit/{id}
- POST /ingredient_category/update/{id}
- GET /ingredient_category/delete/{id}

### Ingredient
- GET /ingredient
- GET /ingredient/create
- POST /ingredient/store
- GET /ingredient/edit/{id}
- POST /ingredient/update/{id}
- GET /ingredient/delete/{id}

### Menu Item
- GET /menu_item
- GET /menu_item/create
- POST /menu_item/store
- GET /menu_item/edit/{id}
- POST /menu_item/update/{id}
- GET /menu_item/delete/{id}

### Recipe
- GET /recipe
- GET /recipe?menu_id={id}
- GET /recipe/create
- POST /recipe/store
- GET /recipe/edit/{id}
- POST /recipe/update/{id}
- GET /recipe/delete/{id}

### Restaurant Table
- GET /restaurant_table
- GET /restaurant_table/create
- POST /restaurant_table/store
- GET /restaurant_table/edit/{id}
- POST /restaurant_table/update/{id}
- GET /restaurant_table/delete/{id}

### Sale Order
- GET /sale_order
- GET /sale_order/create
- POST /sale_order/store
- GET /sale_order/edit/{id}
- POST /sale_order/update/{id}
- GET /sale_order/delete/{id}
- GET /sale_order/complete/{id}
- GET /sale_order/pay/{id}
- GET /sale_order/cancel/{id}
- GET /sale_order/addItem/{id}
- POST /sale_order/saveAddItems/{id}

### Public QR Order
- GET /public_order/start?token={token}
- POST /public_order/submit

### QR
- GET /qr
- GET /qr/generate/{id}
- GET /qr/clear/{id}
- GET /qr/download/{id}

### Inventory Receipt
- GET /inventory_receipt
- GET /inventory_receipt/create
- GET /inventory_receipt/create_from_restock
- POST /inventory_receipt/store
- GET /inventory_receipt/edit/{id}
- POST /inventory_receipt/update/{id}
- GET /inventory_receipt/delete/{id}
- GET /inventory_receipt/complete/{id}

### Inventory Issue
- GET /inventory_issue
- GET /inventory_issue/create
- POST /inventory_issue/store
- GET /inventory_issue/edit/{id}
- POST /inventory_issue/update/{id}
- GET /inventory_issue/delete/{id}
- GET /inventory_issue/complete/{id}

### Report
- GET /report
- GET /report/stock_report
- GET /report/add_stock_out
- POST /report/save_stock_out

### Reservation
- GET /reservation
- GET /reservation/create
- POST /reservation/store
- GET /reservation/edit/{id}
- POST /reservation/update/{id}
- GET /reservation/delete/{id}

### Expense
- GET /expense
- GET /expense/create
- POST /expense/store
- GET /expense/edit/{id}
- POST /expense/update/{id}
- GET /expense/delete/{id}

## 7) Ghi chu doi chieu quan trong (de tranh nham khi phan tich)

- Co su khong dong nhat giua trang thai inventory_issue va hanh vi tru ton kho:
	- sale_order/complete va inventory_issue/complete deu ghi inventory_log tru ton,
	- nhung status issue co the van pending neu khong update cot status.
- schema.sql hien tai khong thay CREATE TABLE reservation, nhung module reservation dang duoc code va su dung.
- sale_order_detail co enum nhieu trang thai, nhung code hien tai chu yeu dung ordered.

