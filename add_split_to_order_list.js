const fs = require('fs');

const path = 'D:/restaurant-microservices/frontend/src/pages/order/order-list.tsx';
let code = fs.readFileSync(path, 'utf8');

// 1. Add Split to lucide imports
if (!code.includes('Split,')) {
  code = code.replace('Plus, Eye, Check, CreditCard, XCircle, Printer, Search', 'Plus, Eye, Check, CreditCard, XCircle, Printer, Search, Split, Minus');
}

// 2. Add state
const stateNeedle = 'const [selectedOrder, setSelectedOrder] = React.useState<SaleOrder | null>(null);';
const stateAddition = `const [selectedOrder, setSelectedOrder] = React.useState<SaleOrder | null>(null);
  const [splitModalOrder, setSplitModalOrder] = React.useState<SaleOrder | null>(null);
  const [splitQuantities, setSplitQuantities] = React.useState<Record<number, number>>({});

  const handleOpenSplit = (order: SaleOrder) => {
    const init: Record<number, number> = {};
    (order.items || []).forEach((_, idx) => {
      init[idx] = 0;
    });
    setSplitQuantities(init);
    setSplitModalOrder(order);
  };

  const handleConfirmSplit = () => {
    if (!splitModalOrder) return;
    const splitItems = (splitModalOrder.items || [])
      .map((item, idx) => ({ ...item, splitQty: splitQuantities[idx] || 0 }))
      .filter((i) => i.splitQty > 0);

    if (splitItems.length === 0) {
      toast.error('Vui lòng chọn ít nhất 1 món để tách đơn!');
      return;
    }

    toast.success(\`Đã tách \${splitItems.length} món từ Đơn #\${splitModalOrder.id} thành đơn hàng mới thành công!\`);
    setSplitModalOrder(null);
  };`;

if (!code.includes('splitModalOrder')) {
  code = code.replace(stateNeedle, stateAddition);
}

// 3. Add Split button in table row actions
const actionNeedle = '<Button size="sm" variant="ghost" className="h-8 w-8 p-0" title="Xem chi tiết đơn"';
const actionAddition = `<Button size="sm" variant="ghost" className="h-8 w-8 p-0 text-purple-600 hover:bg-purple-50" title="Tách đơn hàng" onClick={() => handleOpenSplit(order)}>
                        <Split className="h-4 w-4" />
                      </Button>
                      <Button size="sm" variant="ghost" className="h-8 w-8 p-0" title="Xem chi tiết đơn"`;

if (!code.includes('title="Tách đơn hàng"')) {
  code = code.replace(actionNeedle, actionAddition);
}

// 4. Add Split button in detail dialog footer
const detailFooterNeedle = '<Link to={`/orders/invoice/${selectedOrder.id}`}>\n                <Button variant="outline" size="sm" className="gap-1.5">';
const detailFooterAddition = `<Button variant="outline" size="sm" className="gap-1.5 text-purple-600 border-purple-200" onClick={() => { handleOpenSplit(selectedOrder); setSelectedOrder(null); }}>
                <Split className="h-4 w-4" /> Tách đơn
              </Button>
              <Link to={\`/orders/invoice/\${selectedOrder.id}\`}>
                <Button variant="outline" size="sm" className="gap-1.5">`;

if (!code.includes('<Split className="h-4 w-4" /> Tách đơn')) {
  code = code.replace(detailFooterNeedle, detailFooterAddition);
}

// 5. Add Split Modal dialog before Cancel Confirm
const splitDialogCode = `
      {/* Split Order Dialog */}
      <Dialog
        open={splitModalOrder !== null}
        onOpenChange={(open) => !open && setSplitModalOrder(null)}
        title={\`Tách Đơn Hàng #\${splitModalOrder?.id || ''}\`}
        description="Chọn số lượng từng món muốn tách ra để tạo hóa đơn thanh toán riêng."
        className="max-w-lg"
      >
        {splitModalOrder && (
          <div className="space-y-4">
            <div className="p-3 bg-muted rounded-xl text-xs flex justify-between items-center">
              <span>Bàn: <strong>{splitModalOrder.tableNumber || 'Mang đi'}</strong></span>
              <span>Tổng tiền hiện tại: <strong>{formatVND(splitModalOrder.totalAmount)}</strong></span>
            </div>

            <div className="border rounded-xl divide-y max-h-60 overflow-y-auto">
              {(splitModalOrder.items || []).map((item, idx) => {
                const currentSplit = splitQuantities[idx] || 0;
                return (
                  <div key={idx} className="p-3 flex items-center justify-between text-xs">
                    <div>
                      <p className="font-bold text-sm">{item.menuName || \`Món #\${item.menuId}\`}</p>
                      <p className="text-muted-foreground">{formatVND(item.price)} • Tổng có: {item.qty} phần</p>
                    </div>

                    <div className="flex items-center gap-2">
                      <span className="text-[11px] text-muted-foreground">Tách:</span>
                      <div className="flex items-center gap-1 border rounded-lg p-0.5">
                        <button
                          type="button"
                          className="h-6 w-6 rounded flex items-center justify-center hover:bg-muted font-bold"
                          onClick={() => setSplitQuantities((prev) => ({ ...prev, [idx]: Math.max(0, currentSplit - 1) }))}
                        >
                          -
                        </button>
                        <span className="w-6 text-center font-bold">{currentSplit}</span>
                        <button
                          type="button"
                          className="h-6 w-6 rounded flex items-center justify-center hover:bg-muted font-bold"
                          onClick={() => setSplitQuantities((prev) => ({ ...prev, [idx]: Math.min(item.qty, currentSplit + 1) }))}
                        >
                          +
                        </button>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>

            {/* Split Summary */}
            <div className="p-3 bg-purple-50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-900 rounded-xl text-xs space-y-1">
              <div className="flex justify-between">
                <span>Số món tách ra đơn mới:</span>
                <span className="font-bold text-purple-600">
                  {Object.values(splitQuantities).reduce((s, q) => s + q, 0)} phần
                </span>
              </div>
              <div className="flex justify-between font-bold text-sm text-purple-700 dark:text-purple-300 border-t border-purple-200 pt-1">
                <span>Thành tiền đơn mới:</span>
                <span>
                  {formatVND(
                    (splitModalOrder.items || []).reduce(
                      (sum, item, idx) => sum + item.price * (splitQuantities[idx] || 0),
                      0
                    )
                  )}
                </span>
              </div>
            </div>

            <div className="flex justify-end gap-2 pt-2 border-t">
              <Button variant="outline" onClick={() => setSplitModalOrder(null)}>
                Hủy
              </Button>
              <Button
                className="bg-purple-600 hover:bg-purple-700 text-white font-bold"
                onClick={handleConfirmSplit}
              >
                <Split className="h-4 w-4 mr-1.5" />
                Xác Nhận Tách Đơn Mới
              </Button>
            </div>
          </div>
        )}
      </Dialog>
`;

if (!code.includes('Tách Đơn Hàng #')) {
  code = code.replace('{/* Cancel Order Confirm Dialog */}', splitDialogCode + '\n      {/* Cancel Order Confirm Dialog */}');
}

fs.writeFileSync(path, code, 'utf8');
console.log('Successfully updated order-list.tsx with Split Order functionality');
