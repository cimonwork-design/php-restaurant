const fs = require('fs');

let serverContent = fs.readFileSync('c:/xampp/htdocs/php-restaurant-main-main/server.js', 'utf8');

// 1. Add /api/reports/revenue/:day
const dayReportEndpoint = `
app.get(['/api/reports/revenue/:day', '/reports/revenue/:day'], async (req, res) => {
  try {
    const day = req.params.day;
    const [orders] = await pools.order.query(
      'SELECT id, order_time, total_amount, status, table_id, source FROM sale_order WHERE DATE(order_time) = DATE(?) ORDER BY id DESC',
      [day]
    );

    const [tables] = await pools.table.query('SELECT id, number FROM restaurant_table');
    const tableMap = {};
    tables.forEach(t => { tableMap[t.id] = t.number; });

    const result = orders.map(o => ({
      id: o.id,
      orderDate: o.order_time,
      totalAmount: Number(o.total_amount),
      status: o.status,
      tableNumber: tableMap[o.table_id] || (o.table_id ? \`Bàn \${o.table_id}\` : 'Mang về'),
      cashierName: 'Thu ngân ca',
      source: o.source || 'POS'
    }));

    return success(res, result);
  } catch (err) {
    return res.status(500).json({ success: false, message: err.message });
  }
});
`;

if (!serverContent.includes('/api/reports/revenue/:day')) {
  serverContent = serverContent.replace(
    "app.get(['/api/reports/stock', '/reports/stock'],",
    dayReportEndpoint + "\napp.get(['/api/reports/stock', '/reports/stock'],"
  );
}

// 2. Enhance /api/reports/stock with statusLevel, currentQty, ingredientName
const oldStockMap = `    const stockReports = ingredients.map(ing => {
      const current = stockMap[ing.id] !== undefined ? stockMap[ing.id] : 10;
      return {
        ingredientId: ing.id,
        name: ing.name,
        category: ing.category,
        currentStock: current,
        minStock: Number(ing.min_stock),
        unit: ing.unit,
        status: current <= Number(ing.min_stock) ? 'LOW' : 'NORMAL'
      };
    });`;

const newStockMap = `    const stockReports = ingredients.map(ing => {
      const current = stockMap[ing.id] !== undefined ? stockMap[ing.id] : 10;
      const min = Number(ing.min_stock);
      let statusLevel = 'NORMAL';
      if (current <= 0) statusLevel = 'CRITICAL';
      else if (current <= min) statusLevel = 'WARNING';

      return {
        ingredientId: ing.id,
        ingredientName: ing.name,
        name: ing.name,
        category: ing.category,
        currentQty: current,
        currentStock: current,
        minStock: min,
        unit: ing.unit,
        statusLevel,
        status: current <= min ? 'LOW' : 'NORMAL'
      };
    });`;

if (serverContent.includes('const stockReports = ingredients.map')) {
  serverContent = serverContent.replace(oldStockMap, newStockMap);
}

fs.writeFileSync('c:/xampp/htdocs/php-restaurant-main-main/server.js', serverContent, 'utf8');
console.log('Successfully added /api/reports/revenue/:day and upgraded /api/reports/stock!');
