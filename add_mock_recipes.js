const fs = require('fs');

const mockPath = 'D:/restaurant-microservices/frontend/src/api/mock-data.ts';
let code = fs.readFileSync(mockPath, 'utf8');

// Import RecipeItem if needed
if (!code.includes('RecipeItem')) {
  code = code.replace("import { MenuItem } from '@/types/menu';", "import { MenuItem, RecipeItem } from '@/types/menu';");
}

const recipesCode = `
export const mockRecipes: Record<number, RecipeItem[]> = {
  1: [
    { id: 1, menuId: 1, ingredientId: 1, ingredientName: 'Bò Wagyu A5 Ribeye', unit: 'kg', qty: 0.25 },
    { id: 2, menuId: 1, ingredientId: 4, ingredientName: 'Nấm Truffle Đen Pháp', unit: 'hộp 100g', qty: 0.02 },
    { id: 3, menuId: 1, ingredientId: 5, ingredientName: 'Bơ Thảo Mộc Pháp Elle & Vire', unit: 'kg', qty: 0.03 },
  ],
  2: [
    { id: 4, menuId: 2, ingredientId: 2, ingredientName: 'Cua King Crab Sống', unit: 'kg', qty: 1.2 },
    { id: 5, menuId: 2, ingredientId: 5, ingredientName: 'Bơ Thảo Mộc Pháp Elle & Vire', unit: 'kg', qty: 0.05 },
  ],
  3: [
    { id: 6, menuId: 3, ingredientId: 3, ingredientName: 'Cá Hồi Tươi Na Uy', unit: 'kg', qty: 0.28 },
    { id: 7, menuId: 3, ingredientId: 5, ingredientName: 'Bơ Thảo Mộc Pháp Elle & Vire', unit: 'kg', qty: 0.04 },
  ],
  4: [
    { id: 8, menuId: 4, ingredientId: 6, ingredientName: 'Bào Ngư Xanh Úc', unit: 'kg', qty: 0.15 },
    { id: 9, menuId: 4, ingredientId: 4, ingredientName: 'Nấm Truffle Đen Pháp', unit: 'hộp 100g', qty: 0.01 },
  ],
  7: [
    { id: 10, menuId: 7, ingredientId: 7, ingredientName: 'Chocolate Bỉ Nguyên Chất 70%', unit: 'kg', qty: 0.12 },
    { id: 11, menuId: 7, ingredientId: 5, ingredientName: 'Bơ Thảo Mộc Pháp Elle & Vire', unit: 'kg', qty: 0.02 },
  ],
};
`;

if (!code.includes('mockRecipes')) {
  code += recipesCode;
  fs.writeFileSync(mockPath, code, 'utf8');
  console.log('Added mockRecipes to mock-data.ts');
} else {
  console.log('mockRecipes already exists');
}
