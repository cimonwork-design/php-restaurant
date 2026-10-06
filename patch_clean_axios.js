const fs = require('fs');

// 1. mock-data.ts
const mockPath = 'D:/restaurant-microservices/frontend/src/api/mock-data.ts';
let mockCode = fs.readFileSync(mockPath, 'utf8');
mockCode = mockCode.replace(
  "import { Ingredient } from '@/types/ingredient';",
  "import { Ingredient, IngredientCategory } from '@/types/ingredient';"
);
fs.writeFileSync(mockPath, mockCode, 'utf8');

// 2. axios-instance.ts
const axiosPath = 'D:/restaurant-microservices/frontend/src/api/axios-instance.ts';
let axiosCode = fs.readFileSync(axiosPath, 'utf8');

axiosCode = axiosCode.replace(/typeof error\.config\.data === 'string' \? JSON\.parse\(error\.config\.data\) : error\.config\.data/g, "typeof reqData === 'string' ? JSON.parse(reqData) : reqData");
axiosCode = axiosCode.replace("const mockResult = getMockFallback(url, method, reqData);", "const mockResult = getMockFallback(url, method, error.config?.data);");

fs.writeFileSync(axiosPath, axiosCode, 'utf8');
console.log('Patched mock-data and axios-instance cleanly');
