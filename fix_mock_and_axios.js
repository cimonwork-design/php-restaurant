const fs = require('fs');

// 1. Fix mock-data.ts imports
const mockPath = 'D:/restaurant-microservices/frontend/src/api/mock-data.ts';
let mockCode = fs.readFileSync(mockPath, 'utf8');
if (!mockCode.includes('IngredientCategory')) {
  mockCode = mockCode.replace("import { Ingredient } from '@/types/ingredient';", "import { Ingredient, IngredientCategory } from '@/types/ingredient';");
}
fs.writeFileSync(mockPath, mockCode, 'utf8');

// 2. Fix axios-instance.ts
const axiosPath = 'D:/restaurant-microservices/frontend/src/api/axios-instance.ts';
let axiosCode = fs.readFileSync(axiosPath, 'utf8');

// Update getMockFallback signature
axiosCode = axiosCode.replace(
  "function getMockFallback(url: string, method: string = 'get'): any {",
  "function getMockFallback(url: string, method: string = 'get', reqData?: any): any {"
);

// In response interceptor call
axiosCode = axiosCode.replace(
  "const mockResult = getMockFallback(url, method);",
  "const mockResult = getMockFallback(url, method, error.config?.data);"
);

// Replace error.config?.data with reqData inside getMockFallback
axiosCode = axiosCode.replace(/error\.config\?\.data/g, 'reqData');

fs.writeFileSync(axiosPath, axiosCode, 'utf8');
console.log('Fixed types and variables in mock-data and axios-instance');
