const fs = require('fs');

// 1. Patch server.js
let serverContent = fs.readFileSync('c:/xampp/htdocs/php-restaurant-main-main/server.js', 'utf8');

const targetInServer = "app.post(['/api/orders/:id/pay', '/orders/:id/pay'], async (req, res) => {";
const kdsEndpoints = `
// KDS (Kitchen Display System) - Update single order item status
app.put(['/api/orders/:orderId/items/:itemId/status', '/orders/:orderId/items/:itemId/status'], async (req, res) => {
  try {
    const { status } = req.body;
    const { orderId, itemId } = req.params;
    await pools.order.query(
      'UPDATE sale_order_detail SET status = ? WHERE id = ? AND sale_order_id = ?',
      [status, itemId, orderId]
    );

    // If all items are served, also check order status
    const [details] = await pools.order.query(
      'SELECT status FROM sale_order_detail WHERE sale_order_id = ?',
      [orderId]
    );
    const allServed = details.length > 0 && details.every(d => d.status === 'SERVED');
    if (allServed) {
      await pools.order.query('UPDATE sale_order SET status = "SERVED" WHERE id = ?', [orderId]);
    }

    return success(res, { orderId: Number(orderId), itemId: Number(itemId), status, allServed }, 'Cập nhật trạng thái món thành công');
  } catch (err) {
    return res.status(500).json({ success: false, message: err.message });
  }
});

// KDS - Update all items in order ticket (e.g. Nấu tất cả, Nấu xong hết, Ra món hết)
app.put(['/api/orders/:orderId/items/status', '/orders/:orderId/items/status'], async (req, res) => {
  try {
    const { status } = req.body;
    const { orderId } = req.params;
    await pools.order.query(
      'UPDATE sale_order_detail SET status = ? WHERE sale_order_id = ?',
      [status, orderId]
    );
    if (status === 'SERVED') {
      await pools.order.query('UPDATE sale_order SET status = "SERVED" WHERE id = ?', [orderId]);
    }
    return success(res, { orderId: Number(orderId), status }, 'Cập nhật toàn bộ vé thành công');
  } catch (err) {
    return res.status(500).json({ success: false, message: err.message });
  }
});

`;

if (!serverContent.includes('/api/orders/:orderId/items/:itemId/status')) {
  serverContent = serverContent.replace(targetInServer, kdsEndpoints + targetInServer);
  fs.writeFileSync('c:/xampp/htdocs/php-restaurant-main-main/server.js', serverContent, 'utf8');
  console.log('1. Added KDS status endpoints to server.js');
} else {
  console.log('1. KDS endpoints already in server.js');
}

// 2. Patch frontend order.api.ts
let orderApiContent = fs.readFileSync('D:/restaurant-microservices/frontend/src/api/order.api.ts', 'utf8');
const targetInOrderApi = `getInvoice: async (id: number): Promise<Invoice> => {
    const res = await api.get<ApiResponse<Invoice>>(\`/orders/\${id}/invoice\`);
    return res.data.data;
  },`;

const kdsMethods = `getInvoice: async (id: number): Promise<Invoice> => {
    const res = await api.get<ApiResponse<Invoice>>(\`/orders/\${id}/invoice\`);
    return res.data.data;
  },
  updateItemStatus: async (orderId: number, itemId: number, status: string): Promise<any> => {
    const res = await api.put<ApiResponse<any>>(\`/orders/\${orderId}/items/\${itemId}/status\`, { status });
    return res.data.data;
  },
  updateAllTicketStatus: async (orderId: number, status: string): Promise<any> => {
    const res = await api.put<ApiResponse<any>>(\`/orders/\${orderId}/items/status\`, { status });
    return res.data.data;
  },`;

if (!orderApiContent.includes('updateItemStatus:')) {
  orderApiContent = orderApiContent.replace(targetInOrderApi, kdsMethods);
  fs.writeFileSync('D:/restaurant-microservices/frontend/src/api/order.api.ts', orderApiContent, 'utf8');
  console.log('2. Added updateItemStatus & updateAllTicketStatus to order.api.ts');
} else {
  console.log('2. order.api.ts already has KDS methods');
}

// 3. Patch use-kitchen.ts to persist directly to MySQL and invalidate orders query
const newUseKitchen = `import * as React from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { useOrders } from './use-orders';
import { orderApi } from '@/api/order.api';
import { SaleOrder, OrderDetailStatus, OrderDetail } from '@/types/order';
import { toast } from 'sonner';

export interface KitchenTicket {
  orderId: number;
  tableNumber: string;
  orderTime: string;
  customerName?: string;
  note?: string;
  items: Array<OrderDetail & { currentStatus: OrderDetailStatus }>;
}

export function useKitchen() {
  const qc = useQueryClient();
  const { data: ordersData, isLoading, refetch } = useOrders({ status: 'OPEN', size: 50 });

  const orders: SaleOrder[] = ordersData?.content || (Array.isArray(ordersData) ? ordersData : []);

  // Build Kitchen tickets directly from MySQL persisted item status
  const tickets: KitchenTicket[] = React.useMemo(() => {
    return orders.map((order) => {
      const items = (order.items || []).map((item) => {
        const currentStatus = (item.status as OrderDetailStatus) || 'ORDERED';
        return {
          ...item,
          currentStatus,
        };
      });

      return {
        orderId: order.id,
        tableNumber: order.tableNumber || (order.tableId ? \`Bàn \${order.tableId}\` : 'Mang về'),
        orderTime: order.orderTime || order.createdAt || new Date().toISOString(),
        customerName: order.customerName,
        note: order.note,
        items,
      };
    });
  }, [orders]);

  const updateItemStatus = async (orderId: number, itemIndex: number, newStatus: OrderDetailStatus) => {
    const targetTicket = tickets.find((t) => t.orderId === orderId);
    if (!targetTicket) return;
    const targetItem = targetTicket.items[itemIndex];
    if (!targetItem) return;

    const statusNames: Record<OrderDetailStatus, string> = {
      ORDERED: 'Chờ nấu',
      COOKING: 'Đang nấu',
      COOKED: 'Đã nấu xong',
      SERVED: 'Đã ra món',
      CANCELED: 'Đã hủy',
    };

    // Optimistic cache update in React Query
    qc.setQueryData(['orders', { status: 'OPEN', size: 50 }], (old: any) => {
      if (!old) return old;
      const content = (old.content || []).map((ord: SaleOrder) => {
        if (ord.id !== orderId) return ord;
        const updatedItems = (ord.items || []).map((it, idx) => {
          if (idx === itemIndex || it.id === targetItem.id) {
            return { ...it, status: newStatus };
          }
          return it;
        });
        return { ...ord, items: updatedItems };
      });
      return { ...old, content };
    });

    try {
      await orderApi.updateItemStatus(orderId, targetItem.id, newStatus);
      qc.invalidateQueries({ queryKey: ['orders'] });
      qc.invalidateQueries({ queryKey: ['tables'] });
      toast.success(\`Món "\${targetItem.menuName || 'Món ăn'}" -> \${statusNames[newStatus]}\`);
    } catch (err: any) {
      qc.invalidateQueries({ queryKey: ['orders'] });
      toast.error('Lỗi lưu trạng thái món vào CSDL: ' + (err.message || ''));
    }
  };

  const markAllTicket = async (orderId: number, newStatus: OrderDetailStatus) => {
    const targetTicket = tickets.find((t) => t.orderId === orderId);
    if (!targetTicket) return;

    // Optimistic cache update
    qc.setQueryData(['orders', { status: 'OPEN', size: 50 }], (old: any) => {
      if (!old) return old;
      const content = (old.content || []).map((ord: SaleOrder) => {
        if (ord.id !== orderId) return ord;
        const updatedItems = (ord.items || []).map((it) => ({ ...it, status: newStatus }));
        return { ...ord, items: updatedItems };
      });
      return { ...old, content };
    });

    try {
      await orderApi.updateAllTicketStatus(orderId, newStatus);
      qc.invalidateQueries({ queryKey: ['orders'] });
      qc.invalidateQueries({ queryKey: ['tables'] });
      toast.success(\`Toàn bộ vé #\${orderId} đã lưu trạng thái: \${newStatus}\`);
    } catch (err: any) {
      qc.invalidateQueries({ queryKey: ['orders'] });
      toast.error('Lỗi cập nhật vé vào CSDL: ' + (err.message || ''));
    }
  };

  return {
    tickets,
    isLoading,
    refetch,
    updateItemStatus,
    markAllTicket,
  };
}
`;

fs.writeFileSync('D:/restaurant-microservices/frontend/src/hooks/use-kitchen.ts', newUseKitchen, 'utf8');
console.log('3. Replaced use-kitchen.ts with full MySQL database persistence!');
