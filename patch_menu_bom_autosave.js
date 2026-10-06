const fs = require('fs');

// 1. Update use-menu.ts
let hookContent = fs.readFileSync('D:/restaurant-microservices/frontend/src/hooks/use-menu.ts', 'utf8');
hookContent = hookContent.replace(
  "qc.invalidateQueries({ queryKey: ['recipes', variables.menuId] });",
  "qc.invalidateQueries({ queryKey: ['recipes'] });"
);
fs.writeFileSync('D:/restaurant-microservices/frontend/src/hooks/use-menu.ts', hookContent, 'utf8');
console.log('1. Updated use-menu.ts!');

// 2. Read menu-list.tsx
let content = fs.readFileSync('D:/restaurant-microservices/frontend/src/pages/menu/menu-list.tsx', 'utf8');

// Remember last active tab
content = content.replace(
  "const currentTab = searchParams.get('tab') || 'items';",
  "const currentTab = searchParams.get('tab') || localStorage.getItem('last_menu_tab') || 'items';"
);

// When switching tabs, save to localStorage
content = content.replace(
  "onClick={() => setSearchParams({ tab: 'items' })}",
  "onClick={() => { setSearchParams({ tab: 'items' }); localStorage.setItem('last_menu_tab', 'items'); }}"
);
content = content.replace(
  "onClick={() => setSearchParams({ tab: 'recipes' })}",
  "onClick={() => { setSearchParams({ tab: 'recipes' }); localStorage.setItem('last_menu_tab', 'recipes'); }}"
);

// Remember last selected BOM dish in Tab 2
content = content.replace(
  "const [matrixMenuId, setMatrixMenuId] = React.useState<number | null>(null);",
  `const [matrixMenuId, setMatrixMenuId] = React.useState<number | null>(() => {
    const saved = localStorage.getItem('last_selected_bom_menu_id');
    return saved ? Number(saved) : null;
  });`
);

content = content.replace(
  "onClick={() => setMatrixMenuId(m.id)}",
  `onClick={() => {
    setMatrixMenuId(m.id);
    localStorage.setItem('last_selected_bom_menu_id', String(m.id));
  }}`
);

// When menuItems load, restore remembered dish or fallback to first
content = content.replace(
  `React.useEffect(() => {
    if (menuItems.length > 0 && matrixMenuId === null) {
      setMatrixMenuId(menuItems[0].id);
    }
  }, [menuItems, matrixMenuId]);`,
  `React.useEffect(() => {
    if (menuItems.length > 0) {
      const saved = localStorage.getItem('last_selected_bom_menu_id');
      const found = saved ? menuItems.find((m) => m.id === Number(saved)) : null;
      if (found) {
        setMatrixMenuId(found.id);
      } else if (matrixMenuId === null) {
        setMatrixMenuId(menuItems[0].id);
      }
    }
  }, [menuItems]);`
);

// Add auto-save helper function
const autoSaveHelper = `
  // Auto-save helper for immediate persistence to MySQL
  const autoSaveMatrixRecipe = (updatedItems: RecipeItem[]) => {
    if (!matrixMenuId) return;
    saveRecipeMutation.mutate(
      { menuId: matrixMenuId, items: updatedItems },
      {
        onSuccess: () => {
          toast.success('Đã lưu định mức mới vào CSDL!', { duration: 1500 });
        },
      }
    );
  };
`;

if (!content.includes('autoSaveMatrixRecipe')) {
  content = content.replace(
    'const handleSaveMatrixRecipe = () => {',
    autoSaveHelper + '\n  const handleSaveMatrixRecipe = () => {'
  );
}

// Now replace matrix table actions with auto-save
// 1. - button:
content = content.replace(
  `onClick={() => {
                                      const nextQty = Math.max(0.01, Number((item.qty - 0.05).toFixed(3)));
                                      setMatrixItems((prev) => prev.map((p, i) => i === idx ? { ...p, qty: nextQty } : p));
                                    }}`,
  `onClick={() => {
                                      const nextQty = Math.max(0.01, Number((item.qty - 0.05).toFixed(3)));
                                      const updated = matrixItems.map((p, i) => i === idx ? { ...p, qty: nextQty } : p);
                                      setMatrixItems(updated);
                                      autoSaveMatrixRecipe(updated);
                                    }}`
);

// 2. + button:
content = content.replace(
  `onClick={() => {
                                      const nextQty = Number((item.qty + 0.05).toFixed(3));
                                      setMatrixItems((prev) => prev.map((p, i) => i === idx ? { ...p, qty: nextQty } : p));
                                    }}`,
  `onClick={() => {
                                      const nextQty = Number((item.qty + 0.05).toFixed(3));
                                      const updated = matrixItems.map((p, i) => i === idx ? { ...p, qty: nextQty } : p);
                                      setMatrixItems(updated);
                                      autoSaveMatrixRecipe(updated);
                                    }}`
);

// 3. Input onBlur:
content = content.replace(
  `title="Nhập trực tiếp số lượng định mức"\n                                  />`,
  `title="Nhập trực tiếp số lượng định mức"
                                    onBlur={() => autoSaveMatrixRecipe(matrixItems)}
                                  />`
);

// 4. Prompt Edit:
content = content.replace(
  `if (!isNaN(num) && num > 0) {
                                          setMatrixItems((prev) => prev.map((p, i) => i === idx ? { ...p, qty: num } : p));
                                          toast.info('Đã cập nhật định mức: ' + num + ' ' + item.ingredientUnit);
                                        }`,
  `if (!isNaN(num) && num > 0) {
                                          const updated = matrixItems.map((p, i) => i === idx ? { ...p, qty: num } : p);
                                          setMatrixItems(updated);
                                          autoSaveMatrixRecipe(updated);
                                        }`
);

// 5. Trash button:
content = content.replace(
  `onClick={() => setMatrixItems((prev) => prev.filter((_, i) => i !== idx))}`,
  `onClick={() => {
                                      const updated = matrixItems.filter((_, i) => i !== idx);
                                      setMatrixItems(updated);
                                      autoSaveMatrixRecipe(updated);
                                    }}`
);

// 6. Add ingredient button:
content = content.replace(
  `setMatrixItems((prev) => [
                          ...prev,
                          {
                            menuId: matrixMenuId!,
                            ingredientId: ing.id,
                            qty: newIngQty,
                            ingredientName: ing.name,
                            ingredientUnit: ing.unit,
                            unitPrice: ing.purchasePrice,
                          },
                        ]);
                        setNewIngId(0);
                        setNewIngQty(0.1);`,
  `const updated = [
                          ...matrixItems,
                          {
                            menuId: matrixMenuId!,
                            ingredientId: ing.id,
                            qty: newIngQty,
                            ingredientName: ing.name,
                            ingredientUnit: ing.unit,
                            unitPrice: ing.purchasePrice,
                          },
                        ];
                        setMatrixItems(updated);
                        autoSaveMatrixRecipe(updated);
                        setNewIngId(0);
                        setNewIngQty(0.1);`
);

fs.writeFileSync('D:/restaurant-microservices/frontend/src/pages/menu/menu-list.tsx', content, 'utf8');
console.log('2. Successfully patched menu-list.tsx with auto-save & state retention!');
