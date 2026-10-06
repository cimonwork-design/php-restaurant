const mysql = require('D:/restaurant-microservices/backend-server/node_modules/mysql2/promise');

async function seedAll() {
  console.log('Seeding all 7 databases with UTF-8 data...');

  // 1. AUTH DB
  const authConn = await mysql.createConnection({
    host: '127.0.0.1', port: 3307, user: 'root', password: 'root123', database: 'auth_db', charset: 'utf8mb4'
  });
  await authConn.query('SET NAMES utf8mb4');
  await authConn.query(`
    INSERT INTO users (id, username, password, fullname, role, active) VALUES
    (1, 'admin', '$2a$10$7R9j0YlZp5.8W2dF6g8o2.i5kQ8V2v9tJ6k0q3x5e2b8y5c2e1f4.', 'Nguyễn Quản Trị (Tổng Giám Đốc)', 'ADMIN', 1),
    (2, 'manager', '$2a$10$7R9j0YlZp5.8W2dF6g8o2.i5kQ8V2v9tJ6k0q3x5e2b8y5c2e1f4.', 'Lê Quản Lý (Giám Sát Vận Hành)', 'MANAGER', 1),
    (3, 'waiter', '$2a$10$7R9j0YlZp5.8W2dF6g8o2.i5kQ8V2v9tJ6k0q3x5e2b8y5c2e1f4.', 'Trần Tuấn Anh (Tổ Trưởng Phục Vụ)', 'USER', 1),
    (4, 'chef', '$2a$10$7R9j0YlZp5.8W2dF6g8o2.i5kQ8V2v9tJ6k0q3x5e2b8y5c2e1f4.', 'Phạm Minh Tuấn (Bếp Trưởng)', 'USER', 1),
    (5, 'cashier', '$2a$10$7R9j0YlZp5.8W2dF6g8o2.i5kQ8V2v9tJ6k0q3x5e2b8y5c2e1f4.', 'Vũ Thu Ngân (Thu Ngân Trưởng)', 'USER', 1)
    ON DUPLICATE KEY UPDATE fullname=VALUES(fullname), role=VALUES(role), active=VALUES(active)
  `);
  await authConn.end();
  console.log('auth_db seeded');

  // 2. USER DB
  const userConn = await mysql.createConnection({
    host: '127.0.0.1', port: 3308, user: 'root', password: 'root123', database: 'user_db', charset: 'utf8mb4'
  });
  await userConn.query('SET NAMES utf8mb4');
  await userConn.query(`
    INSERT INTO users (id, username, password, fullname, role, active) VALUES
    (1, 'admin', '$2a$10$7R9j0YlZp5.8W2dF6g8o2.i5kQ8V2v9tJ6k0q3x5e2b8y5c2e1f4.', 'Nguyễn Quản Trị (Tổng Giám Đốc)', 'ADMIN', 1),
    (2, 'manager', '$2a$10$7R9j0YlZp5.8W2dF6g8o2.i5kQ8V2v9tJ6k0q3x5e2b8y5c2e1f4.', 'Lê Quản Lý (Giám Sát Vận Hành)', 'MANAGER', 1),
    (3, 'waiter', '$2a$10$7R9j0YlZp5.8W2dF6g8o2.i5kQ8V2v9tJ6k0q3x5e2b8y5c2e1f4.', 'Trần Tuấn Anh (Tổ Trưởng Phục Vụ)', 'USER', 1),
    (4, 'chef', '$2a$10$7R9j0YlZp5.8W2dF6g8o2.i5kQ8V2v9tJ6k0q3x5e2b8y5c2e1f4.', 'Phạm Minh Tuấn (Bếp Trưởng)', 'USER', 1),
    (5, 'cashier', '$2a$10$7R9j0YlZp5.8W2dF6g8o2.i5kQ8V2v9tJ6k0q3x5e2b8y5c2e1f4.', 'Vũ Thu Ngân (Thu Ngân Trưởng)', 'USER', 1)
    ON DUPLICATE KEY UPDATE fullname=VALUES(fullname), role=VALUES(role), active=VALUES(active)
  `);
  await userConn.end();
  console.log('user_db seeded');

  // 3. MENU DB
  const menuConn = await mysql.createConnection({
    host: '127.0.0.1', port: 3309, user: 'root', password: 'root123', database: 'menu_db', charset: 'utf8mb4'
  });
  await menuConn.query('SET NAMES utf8mb4');
  await menuConn.query(`
    INSERT INTO menu_item (id, code, name, price, category, description, image_url, active) VALUES
    (1, 'WAGYU-A5', 'Bò Wagyu A5 Nướng Sốt Nấm Truffle', 850000.00, 'Món chính', 'Thịt bò Wagyu nhập khẩu Nhật Bản nướng than hoa, sốt nấm Truffle đen thơm lừng.', 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop', 1),
    (2, 'KING-CRAB', 'Cua Hoàng Đế Hấp Rượu Vang Trắng', 1850000.00, 'Hải sản cao cấp', 'Cua King Crab tươi sống hấp rượu vang trắng Bordeaux, bơ tỏi và thảo mộc.', 'https://images.unsplash.com/photo-1559742811-822873691df8?w=600&auto=format&fit=crop', 1),
    (3, 'SALMON-LEMON', 'Cá Hồi Na Uy Áp Chảo Sốt Bơ Chanh', 360000.00, 'Món chính', 'Phi lê cá hồi Na Uy áp chảo da giòn, sốt bơ chanh vàng kiểu Pháp.', 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=600&auto=format&fit=crop', 1),
    (4, 'SOUP-ROYAL', 'Súp Bào Ngư Vi Cá Hoàng Gia', 490000.00, 'Khai vị', 'Bào ngư hảo hạng hầm vi cá và nấm đông cô trong nước thượng canh 12 giờ.', 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=600&auto=format&fit=crop', 1),
    (5, 'LOBSTER-SALAD', 'Salad Tôm Hùm Sốt Chanh Leo', 280000.00, 'Khai vị', 'Tôm hùm baby luộc cùng xà lách Romaine hữu cơ và sốt chanh leo chua thanh.', 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&auto=format&fit=crop', 1),
    (6, 'WINE-MARGAUX', 'Rượu Vang Chateau Margaux 2018', 3200000.00, 'Đồ uống & Rượu', 'Rượu vang đỏ Grand Cru Classe Bordeaux hảo hạng, hương hoa violet và gỗ sồi.', 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=600&auto=format&fit=crop', 1),
    (7, 'MOUSSE-GOLD', 'Bánh Mousse Chocolate Bỉ Phủ Vàng', 150000.00, 'Tráng miệng', 'Chocolate Bỉ nguyên chất 70% mềm mịn, phủ bột vàng 24K sang trọng.', 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop', 1),
    (8, 'TOMAHAWK-STEAK', 'Thăn Bò Tomahawk Nướng Muối Biển', 1250000.00, 'Món chính', 'Thịt sườn bò tomahawk 800g nướng chín vừa mọng nước với muối hồng và bơ tỏi.', 'https://images.unsplash.com/photo-1558030006-450675393462?w=600&auto=format&fit=crop', 1),
    (9, 'LOBSTER-GRILLED', 'Tôm Hùm Nướng Phô Mai Mozzarella', 980000.00, 'Hải sản cao cấp', 'Tôm hùm bông nướng phô mai béo ngậy đút lò vàng ươm.', 'https://images.unsplash.com/photo-1533777857889-4be7c70b33f7?w=600&auto=format&fit=crop', 1),
    (10, 'TIRAMISU-ITALIAN', 'Bánh Tiramisu Truyền Thống Ý', 120000.00, 'Tráng miệng', 'Bánh ladyfingers ngâm cà phê Espresso Ý và kem phô mai mascarpone béo ngậy.', 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=600&auto=format&fit=crop', 1)
    ON DUPLICATE KEY UPDATE name=VALUES(name), price=VALUES(price), category=VALUES(category), description=VALUES(description), image_url=VALUES(image_url), active=VALUES(active)
  `);

  await menuConn.query(`
    INSERT INTO recipe (id, menu_id, ingredient_id, qty) VALUES
    (1, 1, 1, 0.250),
    (2, 1, 4, 0.020),
    (3, 2, 2, 1.200),
    (4, 3, 3, 0.200),
    (5, 3, 5, 0.050),
    (6, 4, 6, 0.100),
    (7, 7, 7, 0.080),
    (8, 8, 1, 0.450),
    (9, 8, 5, 0.030),
    (10, 9, 2, 0.600),
    (11, 10, 7, 0.050)
    ON DUPLICATE KEY UPDATE qty=VALUES(qty)
  `);
  await menuConn.end();
  console.log('menu_db seeded');

  // 4. INVENTORY DB
  const invConn = await mysql.createConnection({
    host: '127.0.0.1', port: 3310, user: 'root', password: 'root123', database: 'inventory_db', charset: 'utf8mb4'
  });
  await invConn.query('SET NAMES utf8mb4');
  await invConn.query(`
    INSERT INTO ingredient_category (id, name, description) VALUES
    (1, 'Thịt & Hải sản', 'Các loại thịt bò Wagyu, cua King Crab, tôm, cá hồi tươi sống'),
    (2, 'Gia vị cao cấp', 'Nấm Truffle Pháp, bơ Elle & Vire, muối hồng Himalaya'),
    (3, 'Rau củ hữu cơ', 'Măng tây xanh, xà lách Romaine Đà Lạt, thảo mộc tươi'),
    (4, 'Đồ làm bánh', 'Chocolate Bỉ nguyên chất, bột mì Pháp, kem whipping Anchor')
    ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description)
  `);

  await invConn.query(`
    INSERT INTO ingredient (id, code, name, category, unit, purchase_price, min_stock, description, main_supplier) VALUES
    (1, 'ING-WAGYU', 'Bò Wagyu A5 Ribeye', 'Thịt & Hải sản', 'kg', 2800000.00, 5, 'Thịt bò vân mỡ A5 nhập khẩu từ Miyazaki Nhật Bản', 'Horeca Food VN'),
    (2, 'ING-CRAB', 'Cua King Crab Sống', 'Thịt & Hải sản', 'kg', 1400000.00, 10, 'Cua hoàng đế đỏ sống nhập khẩu trực tiếp từ Alaska', 'Hải Sản Đại Dương'),
    (3, 'ING-SALMON', 'Cá Hồi Tươi Na Uy', 'Thịt & Hải sản', 'kg', 380000.00, 8, 'Cá hồi tươi nguyên con phi lê chuẩn ăn sống Sashimi', 'Salmar Norway Import'),
    (4, 'ING-TRUFFLE', 'Nấm Truffle Đen Pháp', 'Gia vị cao cấp', 'hộp 100g', 950000.00, 4, 'Nấm Truffle đen tự nhiên vùng Périgord nước Pháp', 'Classic Fine Foods'),
    (5, 'ING-BUTTER', 'Bơ Thảo Mộc Elle & Vire', 'Gia vị cao cấp', 'kg', 220000.00, 5, 'Bơ lạt động vật nhập khẩu vùng Normandy Pháp', 'Classic Fine Foods'),
    (6, 'ING-ABALONE', 'Bào Ngư Xanh Úc', 'Thịt & Hải sản', 'kg', 1650000.00, 3, 'Bào ngư viền xanh sống nhập khẩu từ đảo Tasmania Úc', 'Hải Sản Đại Dương'),
    (7, 'ING-CHOCO', 'Chocolate Bỉ Nguyên Chất 70%', 'Đồ làm bánh', 'kg', 320000.00, 3, 'Socola đen nguyên chất dòng Callebaut nổi tiếng Bỉ', 'Puratos Grand-Place'),
    (8, 'ING-ASPARAGUS', 'Măng Tây Xanh Đà Lạt', 'Rau củ hữu cơ', 'kg', 110000.00, 6, 'Măng tây xanh chuẩn GlobalGAP thu hoạch hàng ngày', 'Đà Lạt GAP Farm'),
    (9, 'ING-ROMAINE', 'Xà Lách Romaine Hữu Cơ', 'Rau củ hữu cơ', 'kg', 65000.00, 5, 'Xà lách Romaine giòn ngọt trồng thủy canh hồi lưu', 'Đà Lạt GAP Farm')
    ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), unit=VALUES(unit), purchase_price=VALUES(purchase_price), min_stock=VALUES(min_stock), description=VALUES(description), main_supplier=VALUES(main_supplier)
  `);

  await invConn.query(`
    INSERT INTO inventory_log (id, ingredient_id, qty_change, type, note) VALUES
    (1, 1, 8.500, 'RECEIPT', 'Tồn kho khả dụng Wagyu A5'),
    (2, 2, 6.200, 'RECEIPT', 'Tồn kho khả dụng Cua King Crab (Cảnh báo tồn dưới mức tối thiểu 10kg)'),
    (3, 3, 12.000, 'RECEIPT', 'Tồn kho khả dụng Cá Hồi'),
    (4, 4, 2.000, 'RECEIPT', 'Tồn kho khả dụng Nấm Truffle (Cảnh báo tồn dưới mức tối thiểu 4 hộp)'),
    (5, 5, 14.000, 'RECEIPT', 'Tồn kho khả dụng Bơ Thảo Mộc'),
    (6, 6, 4.500, 'RECEIPT', 'Tồn kho khả dụng Bào Ngư'),
    (7, 7, 5.000, 'RECEIPT', 'Tồn kho khả dụng Chocolate'),
    (8, 8, 15.000, 'RECEIPT', 'Tồn kho khả dụng Măng Tây Xanh'),
    (9, 9, 20.000, 'RECEIPT', 'Tồn kho khả dụng Xà Lách Romaine')
    ON DUPLICATE KEY UPDATE note=VALUES(note)
  `);

  await invConn.end();
  console.log('inventory_db seeded');

  // 5. TABLE DB
  const tblConn = await mysql.createConnection({
    host: '127.0.0.1', port: 3312, user: 'root', password: 'root123', database: 'table_db', charset: 'utf8mb4'
  });
  await tblConn.query('SET NAMES utf8mb4');
  await tblConn.query(`
    INSERT INTO restaurant_table (id, number, capacity, status, order_token) VALUES
    (1, 'B-01', 2, 'FREE', 'token-table-01-free'),
    (2, 'B-02', 4, 'OCCUPIED', 'token-table-02-occupied'),
    (3, 'B-03', 4, 'OCCUPIED', 'token-table-03-occupied'),
    (4, 'B-04', 6, 'RESERVED', 'token-table-04-reserved'),
    (5, 'B-05', 4, 'FREE', 'token-table-05-free'),
    (6, 'B-06', 2, 'FREE', 'token-table-06-free'),
    (7, 'VIP-01', 8, 'FREE', 'token-table-vip-01'),
    (8, 'VIP-02', 12, 'OCCUPIED', 'token-table-vip-02')
    ON DUPLICATE KEY UPDATE number=VALUES(number), capacity=VALUES(capacity), status=VALUES(status)
  `);

  await tblConn.query(`
    INSERT INTO reservation (id, table_id, customer_name, customer_phone, party_size, start_time, end_time, status, note) VALUES
    (1, 4, 'Nguyễn Văn Hùng', '0901234567', 4, NOW() + INTERVAL 1 HOUR, NOW() + INTERVAL 3 HOUR, 'CONFIRMED', 'Kỷ niệm ngày cưới, chuẩn bị thêm nến và hoa tươi'),
    (2, 7, 'Trần Thị Thuỷ', '0987654321', 8, NOW() + INTERVAL 1 DAY, NOW() + INTERVAL 27 HOUR, 'PENDING', 'Tiệc sinh nhật gia đình, mang theo bánh kem riêng')
    ON DUPLICATE KEY UPDATE customer_name=VALUES(customer_name), customer_phone=VALUES(customer_phone), status=VALUES(status), note=VALUES(note)
  `);
  await tblConn.end();
  console.log('table_db seeded');

  // 6. ORDER DB
  const orderConn = await mysql.createConnection({
    host: '127.0.0.1', port: 3311, user: 'root', password: 'root123', database: 'order_db', charset: 'utf8mb4'
  });
  await orderConn.query('SET NAMES utf8mb4');

  await orderConn.query(`
    CREATE TABLE IF NOT EXISTS work_shift (
      id BIGINT AUTO_INCREMENT PRIMARY KEY,
      shift_code VARCHAR(50) NOT NULL UNIQUE,
      cashier_id BIGINT,
      cashier_name VARCHAR(100) NOT NULL,
      start_time DATETIME NOT NULL,
      end_time DATETIME,
      status VARCHAR(20) NOT NULL DEFAULT 'OPEN',
      opening_cash DECIMAL(14,2) DEFAULT 0,
      cash_sales DECIMAL(14,2) DEFAULT 0,
      card_sales DECIMAL(14,2) DEFAULT 0,
      total_sales DECIMAL(14,2) DEFAULT 0,
      order_count INT DEFAULT 0,
      expected_cash DECIMAL(14,2) DEFAULT 0,
      actual_cash DECIMAL(14,2),
      difference DECIMAL(14,2) DEFAULT 0,
      handover_note TEXT,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  `);

  await orderConn.query(`
    INSERT INTO work_shift (id, shift_code, cashier_name, start_time, status, opening_cash, cash_sales, card_sales, total_sales, order_count, expected_cash, created_at) VALUES
    (1, 'CA-SANG-20260923', 'Lê Quản Lý (Giám Sát Vận Hành)', NOW() - INTERVAL 4 HOUR, 'OPEN', 2000000.00, 4850000.00, 7920000.00, 12770000.00, 14, 6850000.00, NOW() - INTERVAL 4 HOUR)
    ON DUPLICATE KEY UPDATE shift_code=VALUES(shift_code), cashier_name=VALUES(cashier_name), status=VALUES(status)
  `);

  await orderConn.query(`
    INSERT INTO sale_order (id, table_id, waiter_id, order_time, status, subtotal, discount, vat_rate, total_amount, source, customer_name, customer_phone) VALUES
    (1, 2, 3, NOW() - INTERVAL 30 MINUTE, 'OPEN', 1210000.00, 0.00, 8.00, 1306800.00, 'INTERNAL', 'Anh Nam', '0912345678'),
    (2, 3, 3, NOW() - INTERVAL 1 HOUR, 'OPEN', 2340000.00, 100000.00, 8.00, 2427200.00, 'INTERNAL', 'Chị Mai', '0934567890'),
    (3, 8, 3, NOW() - INTERVAL 3 HOUR, 'PAID', 5400000.00, 200000.00, 8.00, 5632000.00, 'INTERNAL', 'Bác Thành', '0978901234')
    ON DUPLICATE KEY UPDATE status=VALUES(status), total_amount=VALUES(total_amount), customer_name=VALUES(customer_name)
  `);

  await orderConn.query(`
    INSERT INTO sale_order_detail (id, sale_order_id, menu_id, menu_name, qty, price, status) VALUES
    (1, 1, 1, 'Bò Wagyu A5 Nướng Sốt Nấm Truffle', 1, 850000.00, 'ORDERED'),
    (2, 1, 3, 'Cá Hồi Na Uy Áp Chảo Sốt Bơ Chanh', 1, 360000.00, 'COOKING'),
    (3, 2, 2, 'Cua Hoàng Đế Hấp Rượu Vang Trắng', 1, 1850000.00, 'COOKED'),
    (4, 2, 4, 'Súp Bào Ngư Vi Cá Hoàng Gia', 1, 490000.00, 'SERVED'),
    (5, 3, 1, 'Bò Wagyu A5 Nướng Sốt Nấm Truffle', 2, 850000.00, 'SERVED'),
    (6, 3, 6, 'Rượu Vang Chateau Margaux 2018', 1, 3200000.00, 'SERVED'),
    (7, 3, 4, 'Súp Bào Ngư Vi Cá Hoàng Gia', 1, 490000.00, 'SERVED')
    ON DUPLICATE KEY UPDATE status=VALUES(status), menu_name=VALUES(menu_name)
  `);

  await orderConn.query(`
    INSERT INTO expense (id, expense_type, amount, description, expense_date) VALUES
    (1, 'Tiền điện kinh doanh tháng 9', 8200000.00, 'Hóa đơn điện lực EVN Quận 1', CURDATE() - INTERVAL 3 DAY),
    (2, 'Thuê mặt bằng nhà hàng', 35000000.00, 'Tiền thuê mặt bằng 2 tầng tháng 9', CURDATE() - INTERVAL 20 DAY),
    (3, 'Bảo trì hệ thống hút mùi', 2500000.00, 'Bảo dưỡng định kỳ lưới lọc và quạt hút bếp', CURDATE() - INTERVAL 5 DAY)
    ON DUPLICATE KEY UPDATE amount=VALUES(amount), description=VALUES(description)
  `);
  await orderConn.end();
  console.log('order_db seeded');

  // 7. REPORT DB
  const repConn = await mysql.createConnection({
    host: '127.0.0.1', port: 3313, user: 'root', password: 'root123', database: 'report_db', charset: 'utf8mb4'
  });
  await repConn.query('SET NAMES utf8mb4');
  await repConn.query(`
    INSERT INTO report_order_summary (id, order_date, total_amount, status, table_number, source) VALUES
    (101, CURDATE() - INTERVAL 6 DAY, 22000000.00, 'PAID', 'B-01', 'INTERNAL'),
    (102, CURDATE() - INTERVAL 5 DAY, 25500000.00, 'PAID', 'B-02', 'INTERNAL'),
    (103, CURDATE() - INTERVAL 4 DAY, 28000000.00, 'PAID', 'VIP-01', 'INTERNAL'),
    (104, CURDATE() - INTERVAL 3 DAY, 32000000.00, 'PAID', 'B-03', 'INTERNAL'),
    (105, CURDATE() - INTERVAL 2 DAY, 38500000.00, 'PAID', 'VIP-02', 'INTERNAL'),
    (106, CURDATE() - INTERVAL 1 DAY, 41000000.00, 'PAID', 'B-04', 'INTERNAL'),
    (107, CURDATE(), 28450000.00, 'PAID', 'VIP-02', 'INTERNAL')
    ON DUPLICATE KEY UPDATE total_amount=VALUES(total_amount)
  `);

  await repConn.query(`
    INSERT INTO report_expense_summary (id, expense_date, expense_type, amount) VALUES
    (201, CURDATE() - INTERVAL 6 DAY, 'Tiêu hao thực phẩm', 7000000.00),
    (202, CURDATE() - INTERVAL 5 DAY, 'Tiêu hao thực phẩm', 8500000.00),
    (203, CURDATE() - INTERVAL 4 DAY, 'Tiêu hao thực phẩm', 9000000.00),
    (204, CURDATE() - INTERVAL 3 DAY, 'Điện nước EVN', 12000000.00),
    (205, CURDATE() - INTERVAL 2 DAY, 'Tiêu hao thực phẩm', 14000000.00),
    (206, CURDATE() - INTERVAL 1 DAY, 'Tiêu hao thực phẩm', 15000000.00),
    (207, CURDATE(), 'Chi phí vận hành', 8200000.00)
    ON DUPLICATE KEY UPDATE amount=VALUES(amount)
  `);
  await repConn.end();
  console.log('report_db seeded');

  console.log('>>> ALL 7 DATABASES SUCCESSFULLY SEEDED WITH REAL DATA! <<<');
}

seedAll().catch(e => {
  console.error(e);
  process.exit(1);
});
