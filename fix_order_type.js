const fs = require('fs');
const p = 'D:/restaurant-microservices/frontend/src/types/order.ts';
let code = fs.readFileSync(p, 'utf8');
code = code.replace(
  "export type OrderDetailStatus = 'ORDERED' | 'COOKED' | 'SERVED' | 'CANCELED';",
  "export type OrderDetailStatus = 'ORDERED' | 'COOKING' | 'COOKED' | 'SERVED' | 'CANCELED';"
);
fs.writeFileSync(p, code, 'utf8');
console.log('Updated types/order.ts with COOKING');
