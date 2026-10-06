const fs = require('fs');

let content = fs.readFileSync('D:/restaurant-microservices/frontend/src/pages/menu/menu-list.tsx', 'utf8');

// 1. Add update helper function for matrix items
const oldMatrixTable = `{matrixItems.map((item, idx) => (
                            <TableRow key={idx}>
                              <TableCell className="font-semibold text-xs">{item.ingredientName}</TableCell>
                              <TableCell className="text-center font-mono text-xs">{item.qty}</TableCell>
                              <TableCell className="text-center text-xs text-muted-foreground">{item.ingredientUnit}</TableCell>
                              <TableCell className="text-right text-xs">{formatVND(item.unitPrice || 0)}</TableCell>
                              <TableCell className="text-right text-xs font-bold text-rose-600 dark:text-rose-400">
                                {formatVND((item.unitPrice || 0) * item.qty)}
                              </TableCell>
                              <TableCell className="text-right">
                                <Button
                                  size="sm"
                                  variant="ghost"
                                  className="h-7 w-7 p-0 text-destructive hover:bg-destructive/10"
                                  onClick={() => setMatrixItems((prev) => prev.filter((_, i) => i !== idx))}
                                >
                                  <Trash2 className="h-3 w-3" />
                                </Button>
                              </TableCell>
                            </TableRow>
                          ))}`;

const newMatrixTable = `{matrixItems.map((item, idx) => (
                            <TableRow key={idx} className="hover:bg-muted/30">
                              <TableCell className="font-semibold text-xs py-2">{item.ingredientName}</TableCell>
                              <TableCell className="text-center py-2">
                                <div className="inline-flex items-center gap-1.5 justify-center">
                                  <button
                                    type="button"
                                    onClick={() => {
                                      const nextQty = Math.max(0.01, Number((item.qty - 0.05).toFixed(3)));
                                      setMatrixItems((prev) => prev.map((p, i) => i === idx ? { ...p, qty: nextQty } : p));
                                    }}
                                    className="h-6 w-6 rounded bg-muted hover:bg-muted/80 text-foreground font-black text-xs flex items-center justify-center transition-colors"
                                    title="Giảm 0.05"
                                  >
                                    -
                                  </button>
                                  <Input
                                    type="number"
                                    step="0.01"
                                    min="0.001"
                                    value={item.qty}
                                    onChange={(e) => {
                                      const val = Math.max(0, Number(e.target.value));
                                      setMatrixItems((prev) => prev.map((p, i) => i === idx ? { ...p, qty: val } : p));
                                    }}
                                    className="h-7 w-20 text-center font-mono font-bold text-xs px-1 border-primary/40 focus:border-primary"
                                    title="Nhập trực tiếp số lượng định mức"
                                  />
                                  <button
                                    type="button"
                                    onClick={() => {
                                      const nextQty = Number((item.qty + 0.05).toFixed(3));
                                      setMatrixItems((prev) => prev.map((p, i) => i === idx ? { ...p, qty: nextQty } : p));
                                    }}
                                    className="h-6 w-6 rounded bg-muted hover:bg-muted/80 text-foreground font-black text-xs flex items-center justify-center transition-colors"
                                    title="Tăng 0.05"
                                  >
                                    +
                                  </button>
                                </div>
                              </TableCell>
                              <TableCell className="text-center text-xs font-semibold text-muted-foreground py-2">{item.ingredientUnit}</TableCell>
                              <TableCell className="text-right text-xs py-2 text-muted-foreground">{formatVND(item.unitPrice || 0)}</TableCell>
                              <TableCell className="text-right text-xs font-black text-rose-600 dark:text-rose-400 py-2">
                                {formatVND((item.unitPrice || 0) * item.qty)}
                              </TableCell>
                              <TableCell className="text-right py-2">
                                <div className="flex items-center justify-end gap-1">
                                  <Button
                                    size="sm"
                                    variant="ghost"
                                    className="h-7 w-7 p-0 text-muted-foreground hover:text-primary"
                                    title="Chỉnh sửa định mức"
                                    onClick={() => {
                                      const currentVal = item.qty;
                                      const promptVal = window.prompt('Nhập số lượng định mức mới cho ' + item.ingredientName + ' (' + item.ingredientUnit + '):', String(currentVal));
                                      if (promptVal !== null) {
                                        const num = Number(promptVal);
                                        if (!isNaN(num) && num > 0) {
                                          setMatrixItems((prev) => prev.map((p, i) => i === idx ? { ...p, qty: num } : p));
                                          toast.info('Đã cập nhật định mức: ' + num + ' ' + item.ingredientUnit);
                                        }
                                      }
                                    }}
                                  >
                                    <Edit2 className="h-3.5 w-3.5" />
                                  </Button>
                                  <Button
                                    size="sm"
                                    variant="ghost"
                                    className="h-7 w-7 p-0 text-destructive hover:bg-destructive/10"
                                    title="Xóa nguyên liệu"
                                    onClick={() => setMatrixItems((prev) => prev.filter((_, i) => i !== idx))}
                                  >
                                    <Trash2 className="h-3.5 w-3.5" />
                                  </Button>
                                </div>
                              </TableCell>
                            </TableRow>
                          ))}`;

if (content.includes(oldMatrixTable)) {
  content = content.replace(oldMatrixTable, newMatrixTable);
  console.log('Replaced matrix table with editable inputs and Edit2 button!');
} else {
  console.warn('oldMatrixTable match not found directly, checking partial');
}

// 2. Also enhance Dialog 2 (dishRecipeItems) with the same editable input
const oldDialogTable = `{dishRecipeItems.map((item, idx) => (
                    <TableRow key={idx}>
                      <TableCell className="text-xs font-semibold py-2">
                        {item.ingredientName}
                      </TableCell>
                      <TableCell className="text-xs text-center font-mono py-2">
                        {item.qty} {item.ingredientUnit}
                      </TableCell>
                      <TableCell className="text-xs text-right font-bold text-rose-600 dark:text-rose-400 py-2">
                        {formatVND((item.unitPrice || 0) * item.qty)}
                      </TableCell>
                      <TableCell className="text-right py-2">
                        <button
                          type="button"
                          onClick={() => handleRemoveIngredientFromDish(idx)}
                          className="text-muted-foreground hover:text-destructive p-1"
                        >
                          <Trash2 className="h-3 w-3" />
                        </button>
                      </TableCell>
                    </TableRow>
                  ))}`;

const newDialogTable = `{dishRecipeItems.map((item, idx) => (
                    <TableRow key={idx} className="hover:bg-muted/30">
                      <TableCell className="text-xs font-semibold py-2">
                        {item.ingredientName}
                      </TableCell>
                      <TableCell className="text-xs text-center py-2">
                        <div className="inline-flex items-center gap-1 justify-center">
                          <button
                            type="button"
                            onClick={() => {
                              const nextQty = Math.max(0.01, Number((item.qty - 0.05).toFixed(3)));
                              setDishRecipeItems((prev) => prev.map((p, i) => i === idx ? { ...p, qty: nextQty } : p));
                            }}
                            className="h-5 w-5 rounded bg-muted hover:bg-muted/80 text-foreground font-black text-xs flex items-center justify-center"
                          >
                            -
                          </button>
                          <Input
                            type="number"
                            step="0.01"
                            min="0.001"
                            value={item.qty}
                            onChange={(e) => {
                              const val = Math.max(0, Number(e.target.value));
                              setDishRecipeItems((prev) => prev.map((p, i) => i === idx ? { ...p, qty: val } : p));
                            }}
                            className="h-6 w-16 text-center font-mono font-bold text-xs px-1"
                          />
                          <button
                            type="button"
                            onClick={() => {
                              const nextQty = Number((item.qty + 0.05).toFixed(3));
                              setDishRecipeItems((prev) => prev.map((p, i) => i === idx ? { ...p, qty: nextQty } : p));
                            }}
                            className="h-5 w-5 rounded bg-muted hover:bg-muted/80 text-foreground font-black text-xs flex items-center justify-center"
                          >
                            +
                          </button>
                          <span className="text-[11px] text-muted-foreground font-medium ml-1">{item.ingredientUnit}</span>
                        </div>
                      </TableCell>
                      <TableCell className="text-xs text-right font-bold text-rose-600 dark:text-rose-400 py-2">
                        {formatVND((item.unitPrice || 0) * item.qty)}
                      </TableCell>
                      <TableCell className="text-right py-2">
                        <div className="flex items-center justify-end gap-1">
                          <button
                            type="button"
                            onClick={() => {
                              const promptVal = window.prompt('Nhập định mức mới cho ' + item.ingredientName + ' (' + item.ingredientUnit + '):', String(item.qty));
                              if (promptVal !== null) {
                                const num = Number(promptVal);
                                if (!isNaN(num) && num > 0) {
                                  setDishRecipeItems((prev) => prev.map((p, i) => i === idx ? { ...p, qty: num } : p));
                                }
                              }
                            }}
                            className="text-muted-foreground hover:text-primary p-1"
                            title="Sửa định mức"
                          >
                            <Edit2 className="h-3 w-3" />
                          </button>
                          <button
                            type="button"
                            onClick={() => handleRemoveIngredientFromDish(idx)}
                            className="text-muted-foreground hover:text-destructive p-1"
                            title="Xóa nguyên liệu"
                          >
                            <Trash2 className="h-3 w-3" />
                          </button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}`;

if (content.includes(oldDialogTable)) {
  content = content.replace(oldDialogTable, newDialogTable);
  console.log('Replaced dialog table with editable inputs and Edit2 button!');
}

fs.writeFileSync('D:/restaurant-microservices/frontend/src/pages/menu/menu-list.tsx', content, 'utf8');
console.log('Successfully written updated menu-list.tsx with full inline recipe editing!');
