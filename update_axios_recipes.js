const fs = require('fs');

const path = 'D:/restaurant-microservices/frontend/src/api/axios-instance.ts';
let code = fs.readFileSync(path, 'utf8');

const target = `  if (cleanUrl.includes('/recipes')) {
    return [
      { id: 1, menuId: 1, ingredientId: 1, ingredientName: 'Bò Wagyu A5 Ribeye', qty: 0.25, unit: 'kg' },
      { id: 2, menuId: 1, ingredientId: 4, ingredientName: 'Nấm Truffle Đen Pháp', qty: 0.02, unit: 'hộp 100g' },
    ];
  }`;

const replacement = `  if (cleanUrl.includes('/recipes')) {
    const menuIdMatch = url.match(/[?&]menuId=(\\d+)/);
    const menuId = menuIdMatch ? Number(menuIdMatch[1]) : 1;
    return mock.mockRecipes[menuId] || [];
  }`;

code = code.replace(target, replacement);

// Also handle POST /recipes saving
const postHandlerNeedle = `    if (error.code === 'ERR_NETWORK' || error.message?.includes('Network Error') || !error.response) {`;
const postHandlerCode = `    if (error.code === 'ERR_NETWORK' || error.message?.includes('Network Error') || !error.response) {
      // If saving recipe in mock mode, persist to mockRecipes
      if (error.config?.url?.includes('/recipes') && error.config?.method === 'post' && error.config?.data) {
        try {
          const body = typeof error.config.data === 'string' ? JSON.parse(error.config.data) : error.config.data;
          if (body.menuId && Array.isArray(body.items)) {
            mock.mockRecipes[body.menuId] = body.items;
            return Promise.resolve({
              data: { success: true, message: 'Đã lưu công thức thành công (Mock)', data: body.items },
              status: 200,
              statusText: 'OK',
              headers: {},
              config: error.config,
            });
          }
        } catch {}
      }`;

if (!code.includes('mockRecipes[body.menuId]')) {
  code = code.replace(postHandlerNeedle, postHandlerCode);
}

fs.writeFileSync(path, code, 'utf8');
console.log('Updated axios-instance.ts to support dynamic recipes and saving');
