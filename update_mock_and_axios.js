const fs = require('fs');

// 1. Update mock-data.ts
const mockPath = 'D:/restaurant-microservices/frontend/src/api/mock-data.ts';
let mockCode = fs.readFileSync(mockPath, 'utf8');

if (!mockCode.includes('mockIngredientCategories')) {
  const catCode = `
export const mockIngredientCategories: IngredientCategory[] = [
  { id: 1, name: 'Thịt & Gia cầm', description: 'Thịt bò Wagyu, gà thả vườn, heo Iberico' },
  { id: 2, name: 'Hải sản tươi sống', description: 'Cua King Crab, tôm hùm Alaska, cá hồi Na Uy, bào ngư Úc' },
  { id: 3, name: 'Rau củ & Nấm tươi', description: 'Măng tây, nấm Truffle đen, xà lách Romaine, cà chua bi' },
  { id: 4, name: 'Gia vị & Sữa bơ', description: 'Bơ thảo mộc Pháp Elle & Vire, phô mai Parmesan, dầu ô liu' },
  { id: 5, name: 'Đồ uống & Pha chế Bar', description: 'Rượu vang đỏ Bordeaux, siro, hoa quả tươi' },
  { id: 6, name: 'Đồ khô & Đóng hộp', description: 'Chocolate Bỉ 70%, bột mì số 8, gia vị nhập khẩu' },
];
`;
  mockCode += catCode;
  fs.writeFileSync(mockPath, mockCode, 'utf8');
  console.log('mock-data.ts updated with mockIngredientCategories');
}

// 2. Update axios-instance.ts
const axiosPath = 'D:/restaurant-microservices/frontend/src/api/axios-instance.ts';
let axiosCode = fs.readFileSync(axiosPath, 'utf8');

// Ensure /ingredient-categories route
if (!axiosCode.includes("cleanUrl.includes('/ingredient-categories')")) {
  const catRouteCode = `  // Ingredient Categories
  if (cleanUrl.includes('/ingredient-categories')) {
    if (method === 'post' && error.config?.data) {
      try {
        const body = typeof error.config.data === 'string' ? JSON.parse(error.config.data) : error.config.data;
        const newCat = { id: Date.now(), name: body.name, description: body.description };
        mock.mockIngredientCategories.push(newCat);
        return newCat;
      } catch {}
    }
    return mock.mockIngredientCategories;
  }\n`;

  axiosCode = axiosCode.replace("  // Ingredients & Inventory", catRouteCode + "\n  // Ingredients & Inventory");
}

// Ensure /recipes GET and POST dynamic support
if (!axiosCode.includes('menuIdMatch')) {
  axiosCode = axiosCode.replace(
    `  if (cleanUrl.includes('/recipes')) {
    return [
      { id: 1, menuId: 1, ingredientId: 1, ingredientName: 'Bò Wagyu A5 Ribeye', qty: 0.25, unit: 'kg' },
      { id: 2, menuId: 1, ingredientId: 4, ingredientName: 'Nấm Truffle Đen Pháp', qty: 0.02, unit: 'hộp 100g' },
    ];
  }`,
    `  if (cleanUrl.includes('/recipes')) {
    if (method === 'post' && error.config?.data) {
      try {
        const body = typeof error.config.data === 'string' ? JSON.parse(error.config.data) : error.config.data;
        if (body.menuId && Array.isArray(body.items)) {
          mock.mockRecipes[body.menuId] = body.items;
          return body.items;
        }
      } catch {}
    }
    const menuIdMatch = url.match(/[?&]menuId=(\\d+)/);
    const menuId = menuIdMatch ? Number(menuIdMatch[1]) : 1;
    return mock.mockRecipes[menuId] || [];
  }`
  );
}

fs.writeFileSync(axiosPath, axiosCode, 'utf8');
console.log('axios-instance.ts updated with full category and recipe handling');
