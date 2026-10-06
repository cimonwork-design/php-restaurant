const fs = require('fs');
const p = 'D:/restaurant-microservices/frontend/src/api/mock-data.ts';
let code = fs.readFileSync(p, 'utf8');

code = code.replace(
  "currentStock: 4.5, purchasePrice: 1650000, category: 'Thịt & Hải sản' }, // Bào ngư",
  "currentStock: 0, purchasePrice: 1650000, category: 'Thịt & Hải sản' }, // Bào ngư"
);
// or if without comment:
code = code.replace(
  "code: 'ING-ABALONE', unit: 'kg', minStock: 3, currentStock: 4.5",
  "code: 'ING-ABALONE', unit: 'kg', minStock: 3, currentStock: 0"
);

fs.writeFileSync(p, code, 'utf8');
console.log('Successfully updated Bào Ngư to stock 0');
