const fs = require('fs');

// 1. Update App.tsx
const appPath = 'D:/restaurant-microservices/frontend/src/App.tsx';
let appCode = fs.readFileSync(appPath, 'utf8');

if (!appCode.includes('KitchenDisplayPage')) {
  appCode = appCode.replace(
    "import { PublicOrderPage } from '@/pages/public/public-order';",
    "import { PublicOrderPage } from '@/pages/public/public-order';\nimport { KitchenDisplayPage } from '@/pages/kitchen/kitchen-display';\nimport { ShiftManagePage } from '@/pages/shift/shift-manage';"
  );
  appCode = appCode.replace(
    '<Route path="/orders/new" element={<OrderCreatePage />} />',
    '<Route path="/orders/new" element={<OrderCreatePage />} />\n          <Route path="/orders/create" element={<OrderCreatePage />} />\n          <Route path="/kitchen" element={<KitchenDisplayPage />} />\n          <Route path="/shifts" element={<ShiftManagePage />} />'
  );
  fs.writeFileSync(appPath, appCode, 'utf8');
  console.log('App.tsx updated with /kitchen and /shifts routes');
}

// 2. Update app-sidebar.tsx
const sidebarPath = 'D:/restaurant-microservices/frontend/src/components/layout/app-sidebar.tsx';
let sidebarCode = fs.readFileSync(sidebarPath, 'utf8');

if (!sidebarCode.includes('ChefHat')) {
  sidebarCode = sidebarCode.replace(
    'LayoutDashboard,',
    'LayoutDashboard,\n  ChefHat,\n  Clock,'
  );
  sidebarCode = sidebarCode.replace(
    "{ name: 'Đơn hàng & POS', href: '/orders', icon: UtensilsCrossed },",
    "{ name: 'Đơn hàng & POS', href: '/orders', icon: UtensilsCrossed },\n  { name: 'Màn hình bếp (KDS)', href: '/kitchen', icon: ChefHat },"
  );
  sidebarCode = sidebarCode.replace(
    "{ name: 'Sổ chi phí', href: '/expenses', icon: DollarSign },",
    "{ name: 'Sổ chi phí', href: '/expenses', icon: DollarSign },\n  { name: 'Quản lý ca & Chốt két', href: '/shifts', icon: Clock },"
  );
  fs.writeFileSync(sidebarPath, sidebarCode, 'utf8');
  console.log('app-sidebar.tsx updated with Kitchen and Shifts navigation');
}
