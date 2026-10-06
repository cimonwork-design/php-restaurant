const fs = require('fs');

// 1. Update recipe-manage.tsx
const recipePath = 'D:/restaurant-microservices/frontend/src/pages/recipe/recipe-manage.tsx';
let recipeCode = fs.readFileSync(recipePath, 'utf8');

if (!recipeCode.includes('useSearchParams')) {
  recipeCode = recipeCode.replace(
    "import * as React from 'react';",
    "import * as React from 'react';\nimport { useSearchParams } from 'react-router-dom';"
  );
  recipeCode = recipeCode.replace(
    'export function RecipeManagePage() {',
    'export function RecipeManagePage() {\n  const [searchParams] = useSearchParams();\n  const urlMenuId = searchParams.get(\'menuId\') ? Number(searchParams.get(\'menuId\')) : null;'
  );
  recipeCode = recipeCode.replace(
    'const [selectedMenuId, setSelectedMenuId] = React.useState<number | null>(null);',
    'const [selectedMenuId, setSelectedMenuId] = React.useState<number | null>(urlMenuId);'
  );
  recipeCode = recipeCode.replace(
    `  // Select first menu item by default
  React.useEffect(() => {
    if (menuData && menuData.content.length > 0 && selectedMenuId === null) {
      setSelectedMenuId(menuData.content[0].id);
    }
  }, [menuData, selectedMenuId]);`,
    `  // Select first menu item or URL menuId
  React.useEffect(() => {
    if (urlMenuId) {
      setSelectedMenuId(urlMenuId);
    } else if (menuData && menuData.content.length > 0 && selectedMenuId === null) {
      setSelectedMenuId(menuData.content[0].id);
    }
  }, [menuData, urlMenuId]);`
  );
  fs.writeFileSync(recipePath, recipeCode, 'utf8');
  console.log('recipe-manage.tsx updated successfully');
} else {
  console.log('recipe-manage.tsx already has useSearchParams');
}

// 2. Update menu-list.tsx
const menuPath = 'D:/restaurant-microservices/frontend/src/pages/menu/menu-list.tsx';
let menuCode = fs.readFileSync(menuPath, 'utf8');

if (!menuCode.includes('useNavigate')) {
  menuCode = menuCode.replace(
    "import * as React from 'react';",
    "import * as React from 'react';\nimport { useNavigate } from 'react-router-dom';"
  );
  menuCode = menuCode.replace(
    'export function MenuListPage() {',
    'export function MenuListPage() {\n  const navigate = useNavigate();'
  );
  menuCode = menuCode.replace(
    '<Button variant="ghost" size="icon" onClick={() => handleOpenEdit(item)}>',
    `<Button variant="ghost" size="icon" title="Định lượng công thức (Recipe)" onClick={() => navigate(\`/recipes?menuId=\${item.id}\`)}>
                      <BookOpen className="h-4 w-4 text-primary" />
                    </Button>
                    <Button variant="ghost" size="icon" onClick={() => handleOpenEdit(item)}>`
  );
  fs.writeFileSync(menuPath, menuCode, 'utf8');
  console.log('menu-list.tsx updated successfully');
} else {
  console.log('menu-list.tsx already has useNavigate');
}
