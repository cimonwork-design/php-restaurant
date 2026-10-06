const fs = require('fs');

let content = fs.readFileSync('c:/xampp/htdocs/php-restaurant-main-main/server.js', 'utf8');

const newQrBlock = `// QR CODES & PUBLIC ONLINE ORDERING
app.post(['/api/qr/:tableId/generate', '/qr/:tableId/generate'], async (req, res) => {
  try {
    const tableId = Number(req.params.tableId);
    const [rows] = await pools.table.query('SELECT * FROM restaurant_table WHERE id = ?', [tableId]);
    if (!rows.length) return res.status(404).json({ success: false, message: 'Bàn không tồn tại' });
    const table = rows[0];
    const token = 'token-' + table.number.toLowerCase().replace(/[^a-z0-9]/g, '-') + '-' + Date.now();
    await pools.table.query('UPDATE restaurant_table SET order_token = ? WHERE id = ?', [token, tableId]);
    return success(res, {
      tableId,
      tableNumber: table.number,
      orderToken: token,
      qrUrl: 'http://localhost:5174/public/order?token=' + token
    }, 'Kích hoạt mã QR bàn thành công');
  } catch (err) {
    return res.status(500).json({ success: false, message: err.message });
  }
});

app.delete(['/api/qr/:tableId/clear', '/qr/:tableId/clear'], async (req, res) => {
  try {
    const tableId = Number(req.params.tableId);
    await pools.table.query('UPDATE restaurant_table SET order_token = NULL WHERE id = ?', [tableId]);
    return success(res, null, 'Đã xóa mã QR bàn thành công');
  } catch (err) {
    return res.status(500).json({ success: false, message: err.message });
  }
});

app.get(['/api/qr/:tableId', '/qr/:tableId'], async (req, res) => {
  try {
    const tableId = Number(req.params.tableId);
    const [rows] = await pools.table.query('SELECT * FROM restaurant_table WHERE id = ?', [tableId]);
    if (!rows.length) return res.status(404).json({ success: false, message: 'Bàn không tồn tại' });
    const table = rows[0];
    return success(res, {
      tableId,
      tableNumber: table.number,
      orderToken: table.order_token,
      qrUrl: table.order_token ? ('http://localhost:5174/public/order?token=' + table.order_token) : null
    });
  } catch (err) {
    return res.status(500).json({ success: false, message: err.message });
  }
});

// PUBLIC ONLINE ORDERING FOR GUESTS SCANNING QR
app.get(['/api/public-order/start', '/public-order/start'], async (req, res) => {
  try {
    const token = req.query.token;
    if (!token) return res.status(400).json({ success: false, message: 'Thiếu mã token QR' });
    const [rows] = await pools.table.query('SELECT * FROM restaurant_table WHERE order_token = ?', [token]);
    if (!rows.length) return res.status(404).json({ success: false, message: 'Mã QR không hợp lệ hoặc đã hết hạn' });
    const table = rows[0];

    const [orders] = await pools.order.query('SELECT * FROM sale_order WHERE table_id = ? AND status = "OPEN" ORDER BY id DESC LIMIT 1', [table.id]);
    let activeOrder = undefined;
    if (orders.length) {
      const o = orders[0];
      const [details] = await pools.order.query('SELECT * FROM sale_order_detail WHERE sale_order_id = ?', [o.id]);
      activeOrder = {
        id: o.id,
        tableId: o.table_id,
        totalAmount: Number(o.total_amount),
        status: o.status,
        items: details.map(d => ({ menuId: d.menu_id, menuName: d.menu_name, qty: d.qty, price: Number(d.price) }))
      };
    }

    return success(res, {
      tableId: table.id,
      tableNumber: table.number,
      capacity: table.capacity,
      tableStatus: table.status,
      activeOrder
    });
  } catch (err) {
    return res.status(500).json({ success: false, message: err.message });
  }
});

app.post(['/api/public-order/submit', '/public-order/submit'], async (req, res) => {
  try {
    const { token, items, customerName, note } = req.body;
    if (!token) return res.status(400).json({ success: false, message: 'Thiếu mã token QR' });
    const [tables] = await pools.table.query('SELECT * FROM restaurant_table WHERE order_token = ?', [token]);
    if (!tables.length) return res.status(404).json({ success: false, message: 'Bàn không hợp lệ' });
    const table = tables[0];

    let subtotal = 0;
    if (Array.isArray(items)) {
      items.forEach(it => { subtotal += (Number(it.price) || 0) * (Number(it.qty) || 1); });
    }
    const vatRate = 8;
    const vatAmount = subtotal * (vatRate / 100);
    const totalAmount = subtotal + vatAmount;

    const [r] = await pools.order.query(
      'INSERT INTO sale_order (table_id, waiter_id, order_time, status, subtotal, discount, vat_rate, total_amount, source, customer_name, customer_phone, note) VALUES (?, 3, NOW(), "OPEN", ?, 0, ?, ?, "QR_CODE", ?, null, ?)',
      [table.id, subtotal, vatRate, totalAmount, customerName || 'Khách quét QR', note || ('Khách tự gọi món qua QR Bàn ' + table.number)]
    );
    const orderId = r.insertId;

    if (Array.isArray(items) && items.length) {
      for (const it of items) {
        await pools.order.query(
          'INSERT INTO sale_order_detail (sale_order_id, menu_id, menu_name, qty, price, status, note) VALUES (?, ?, ?, ?, ?, "ORDERED", ?)',
          [orderId, it.menuId, it.menuName || ('Món #' + it.menuId), Number(it.qty) || 1, Number(it.price) || 0, it.note || null]
        );
      }
    }

    await pools.table.query('UPDATE restaurant_table SET status = "OCCUPIED" WHERE id = ?', [table.id]);

    return success(res, { id: orderId, tableNumber: table.number, totalAmount, status: 'OPEN' }, 'Khách đã gửi gọi món thành công!');
  } catch (err) {
    return res.status(500).json({ success: false, message: err.message });
  }
});`;

const start = content.indexOf('// QR CODES');
const end = content.indexOf('// ==========================================\n// 5. ORDER & INVOICE ENDPOINTS');
if (start !== -1 && end !== -1) {
  content = content.slice(0, start) + newQrBlock + '\n\n' + content.slice(end);
  fs.writeFileSync('c:/xampp/htdocs/php-restaurant-main-main/server.js', content, 'utf8');
  console.log('Successfully patched server.js with complete QR & Public Order handling!');
} else {
  console.error('Could not find QR section markers in server.js');
}
