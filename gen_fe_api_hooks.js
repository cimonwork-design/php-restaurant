const fs = require('fs');
const path = require('path');

const src = 'D:/restaurant-microservices/frontend/src';

function write(rel, content) {
  const full = path.join(src, rel);
  fs.mkdirSync(path.dirname(full), { recursive: true });
  fs.writeFileSync(full, content.trim() + '\n', 'utf8');
  console.log('Created ' + rel);
}

// 1. API Services
write('api/auth.api.ts', `import api from './axios-instance';
import { ApiResponse } from '@/types/common';
import { AuthResponse, LoginPayload, RegisterPayload, User } from '@/types/auth';

export const authApi = {
  login: async (payload: LoginPayload): Promise<AuthResponse> => {
    const res = await api.post<ApiResponse<AuthResponse>>('/auth/login', payload);
    return res.data.data;
  },
  register: async (payload: RegisterPayload): Promise<User> => {
    const res = await api.post<ApiResponse<User>>('/auth/register', payload);
    return res.data.data;
  },
  verify: async (): Promise<User> => {
    const res = await api.get<ApiResponse<User>>('/auth/verify');
    return res.data.data;
  },
  refresh: async (refreshToken: string): Promise<AuthResponse> => {
    const res = await api.post<ApiResponse<AuthResponse>>('/auth/refresh', { refreshToken });
    return res.data.data;
  },
  logout: async (): Promise<void> => {
    await api.post('/auth/logout');
  },
};
`);

write('api/user.api.ts', `import api from './axios-instance';
import { ApiResponse, PageResponse, PaginationParams } from '@/types/common';
import { User, Role } from '@/types/auth';

export interface UserQueryParams extends PaginationParams {
  role?: Role;
  active?: boolean;
}

export const userApi = {
  getAll: async (params?: UserQueryParams): Promise<PageResponse<User>> => {
    const res = await api.get<ApiResponse<PageResponse<User>>>('/users', { params });
    return res.data.data;
  },
  getById: async (id: number): Promise<User> => {
    const res = await api.get<ApiResponse<User>>(\`/users/\${id}\`);
    return res.data.data;
  },
  create: async (payload: any): Promise<User> => {
    const res = await api.post<ApiResponse<User>>('/users', payload);
    return res.data.data;
  },
  update: async (id: number, payload: any): Promise<User> => {
    const res = await api.put<ApiResponse<User>>(\`/users/\${id}\`, payload);
    return res.data.data;
  },
  changePassword: async (id: number, payload: any): Promise<void> => {
    await api.put(\`/users/\${id}/change-password\`, payload);
  },
  delete: async (id: number): Promise<void> => {
    await api.delete(\`/users/\${id}\`);
  },
  countActive: async (): Promise<number> => {
    const res = await api.get<ApiResponse<number>>('/users/count');
    return res.data.data;
  },
};
`);

write('api/menu.api.ts', `import api from './axios-instance';
import { ApiResponse, PageResponse, PaginationParams } from '@/types/common';
import { MenuItem, RecipeItem, CheckInventoryResponse } from '@/types/menu';

export interface MenuQueryParams extends PaginationParams {
  category?: string;
  active?: boolean;
}

export const menuApi = {
  getAll: async (params?: MenuQueryParams): Promise<PageResponse<MenuItem>> => {
    const res = await api.get<ApiResponse<PageResponse<MenuItem>>>('/menu', { params });
    return res.data.data;
  },
  getById: async (id: number): Promise<MenuItem> => {
    const res = await api.get<ApiResponse<MenuItem>>(\`/menu/\${id}\`);
    return res.data.data;
  },
  getByCode: async (code: string): Promise<MenuItem> => {
    const res = await api.get<ApiResponse<MenuItem>>(\`/menu/code/\${code}\`);
    return res.data.data;
  },
  create: async (payload: Partial<MenuItem>): Promise<MenuItem> => {
    const res = await api.post<ApiResponse<MenuItem>>('/menu', payload);
    return res.data.data;
  },
  update: async (id: number, payload: Partial<MenuItem>): Promise<MenuItem> => {
    const res = await api.put<ApiResponse<MenuItem>>(\`/menu/\${id}\`, payload);
    return res.data.data;
  },
  delete: async (id: number): Promise<void> => {
    await api.delete(\`/menu/\${id}\`);
  },
  // Recipes
  getRecipes: async (menuId: number): Promise<RecipeItem[]> => {
    const res = await api.get<ApiResponse<RecipeItem[]>>('/recipes', { params: { menuId } });
    return res.data.data;
  },
  saveRecipe: async (menuId: number, items: RecipeItem[]): Promise<RecipeItem[]> => {
    const res = await api.post<ApiResponse<RecipeItem[]>>('/recipes', { menuId, items });
    return res.data.data;
  },
  deleteRecipe: async (id: number): Promise<void> => {
    await api.delete(\`/recipes/\${id}\`);
  },
  checkInventory: async (items: { menuId: number; qty: number }[]): Promise<CheckInventoryResponse> => {
    const res = await api.post<ApiResponse<CheckInventoryResponse>>('/recipes/check-inventory', { items });
    return res.data.data;
  },
};
`);

write('api/ingredient.api.ts', `import api from './axios-instance';
import { ApiResponse, PageResponse, PaginationParams } from '@/types/common';
import { Ingredient, IngredientCategory, IngredientStock } from '@/types/ingredient';

export interface IngredientQueryParams extends PaginationParams {
  category?: string;
}

export const ingredientApi = {
  getAll: async (params?: IngredientQueryParams): Promise<PageResponse<Ingredient>> => {
    const res = await api.get<ApiResponse<PageResponse<Ingredient>>>('/ingredients', { params });
    return res.data.data;
  },
  getById: async (id: number): Promise<Ingredient> => {
    const res = await api.get<ApiResponse<Ingredient>>(\`/ingredients/\${id}\`);
    return res.data.data;
  },
  getStock: async (id: number): Promise<IngredientStock> => {
    const res = await api.get<ApiResponse<IngredientStock>>(\`/ingredients/\${id}/stock\`);
    return res.data.data;
  },
  getLowStock: async (): Promise<Ingredient[]> => {
    const res = await api.get<ApiResponse<Ingredient[]>>('/ingredients/low-stock');
    return res.data.data;
  },
  create: async (payload: Partial<Ingredient>): Promise<Ingredient> => {
    const res = await api.post<ApiResponse<Ingredient>>('/ingredients', payload);
    return res.data.data;
  },
  update: async (id: number, payload: Partial<Ingredient>): Promise<Ingredient> => {
    const res = await api.put<ApiResponse<Ingredient>>(\`/ingredients/\${id}\`, payload);
    return res.data.data;
  },
  delete: async (id: number): Promise<void> => {
    await api.delete(\`/ingredients/\${id}\`);
  },
  // Categories
  getCategories: async (): Promise<IngredientCategory[]> => {
    const res = await api.get<ApiResponse<IngredientCategory[]>>('/ingredient-categories');
    return res.data.data;
  },
  createCategory: async (payload: Partial<IngredientCategory>): Promise<IngredientCategory> => {
    const res = await api.post<ApiResponse<IngredientCategory>>('/ingredient-categories', payload);
    return res.data.data;
  },
  updateCategory: async (id: number, payload: Partial<IngredientCategory>): Promise<IngredientCategory> => {
    const res = await api.put<ApiResponse<IngredientCategory>>(\`/ingredient-categories/\${id}\`, payload);
    return res.data.data;
  },
  deleteCategory: async (id: number): Promise<void> => {
    await api.delete(\`/ingredient-categories/\${id}\`);
  },
};
`);

write('api/inventory.api.ts', `import api from './axios-instance';
import { ApiResponse, PageResponse, PaginationParams } from '@/types/common';
import { InventoryReceipt, InventoryIssue, StockAdjustment, ReceiptStatus } from '@/types/inventory';

export interface ReceiptQueryParams extends PaginationParams {
  status?: ReceiptStatus;
}

export const inventoryApi = {
  // Receipts
  getReceipts: async (params?: ReceiptQueryParams): Promise<PageResponse<InventoryReceipt>> => {
    const res = await api.get<ApiResponse<PageResponse<InventoryReceipt>>>('/inventory/receipts', { params });
    return res.data.data;
  },
  getReceiptById: async (id: number): Promise<InventoryReceipt> => {
    const res = await api.get<ApiResponse<InventoryReceipt>>(\`/inventory/receipts/\${id}\`);
    return res.data.data;
  },
  createReceipt: async (payload: any): Promise<InventoryReceipt> => {
    const res = await api.post<ApiResponse<InventoryReceipt>>('/inventory/receipts', payload);
    return res.data.data;
  },
  updateReceipt: async (id: number, payload: any): Promise<InventoryReceipt> => {
    const res = await api.put<ApiResponse<InventoryReceipt>>(\`/inventory/receipts/\${id}\`, payload);
    return res.data.data;
  },
  deleteReceipt: async (id: number): Promise<void> => {
    await api.delete(\`/inventory/receipts/\${id}\`);
  },
  completeReceipt: async (id: number): Promise<InventoryReceipt> => {
    const res = await api.post<ApiResponse<InventoryReceipt>>(\`/inventory/receipts/\${id}/complete\`);
    return res.data.data;
  },
  // Issues
  getIssues: async (params?: PaginationParams): Promise<PageResponse<InventoryIssue>> => {
    const res = await api.get<ApiResponse<PageResponse<InventoryIssue>>>('/inventory/issues', { params });
    return res.data.data;
  },
  getIssueById: async (id: number): Promise<InventoryIssue> => {
    const res = await api.get<ApiResponse<InventoryIssue>>(\`/inventory/issues/\${id}\`);
    return res.data.data;
  },
  createManualIssue: async (payload: any): Promise<InventoryIssue> => {
    const res = await api.post<ApiResponse<InventoryIssue>>('/inventory/issues', payload);
    return res.data.data;
  },
  // Stock Adjustments
  adjustStock: async (payload: any): Promise<StockAdjustment> => {
    const res = await api.post<ApiResponse<StockAdjustment>>('/inventory/adjustments', payload);
    return res.data.data;
  },
  getAdjustments: async (ingredientId: number): Promise<StockAdjustment[]> => {
    const res = await api.get<ApiResponse<StockAdjustment[]>>(\`/inventory/adjustments/ingredient/\${ingredientId}\`);
    return res.data.data;
  },
};
`);

write('api/table.api.ts', `import api from './axios-instance';
import { ApiResponse } from '@/types/common';
import { RestaurantTable, TableStatus, Reservation, QrTokenDetails } from '@/types/table';

export const tableApi = {
  getAll: async (): Promise<RestaurantTable[]> => {
    const res = await api.get<ApiResponse<RestaurantTable[]>>('/tables');
    return res.data.data;
  },
  getById: async (id: number): Promise<RestaurantTable> => {
    const res = await api.get<ApiResponse<RestaurantTable>>(\`/tables/\${id}\`);
    return res.data.data;
  },
  getByToken: async (token: string): Promise<RestaurantTable> => {
    const res = await api.get<ApiResponse<RestaurantTable>>(\`/tables/by-token/\${token}\`);
    return res.data.data;
  },
  create: async (payload: Partial<RestaurantTable>): Promise<RestaurantTable> => {
    const res = await api.post<ApiResponse<RestaurantTable>>('/tables', payload);
    return res.data.data;
  },
  update: async (id: number, payload: Partial<RestaurantTable>): Promise<RestaurantTable> => {
    const res = await api.put<ApiResponse<RestaurantTable>>(\`/tables/\${id}\`, payload);
    return res.data.data;
  },
  updateStatus: async (id: number, status: TableStatus): Promise<RestaurantTable> => {
    const res = await api.put<ApiResponse<RestaurantTable>>(\`/tables/\${id}/status\`, { status });
    return res.data.data;
  },
  delete: async (id: number): Promise<void> => {
    await api.delete(\`/tables/\${id}\`);
  },
  // Reservations
  getReservations: async (tableId?: number): Promise<Reservation[]> => {
    const res = await api.get<ApiResponse<Reservation[]>>('/reservations', { params: { tableId } });
    return res.data.data;
  },
  getReservationById: async (id: number): Promise<Reservation> => {
    const res = await api.get<ApiResponse<Reservation>>(\`/reservations/\${id}\`);
    return res.data.data;
  },
  createReservation: async (payload: any): Promise<Reservation> => {
    const res = await api.post<ApiResponse<Reservation>>('/reservations', payload);
    return res.data.data;
  },
  updateReservation: async (id: number, payload: any): Promise<Reservation> => {
    const res = await api.put<ApiResponse<Reservation>>(\`/reservations/\${id}\`, payload);
    return res.data.data;
  },
  deleteReservation: async (id: number): Promise<void> => {
    await api.delete(\`/reservations/\${id}\`);
  },
  // QR
  generateQr: async (tableId: number): Promise<QrTokenDetails> => {
    const res = await api.post<ApiResponse<QrTokenDetails>>(\`/qr/\${tableId}/generate\`);
    return res.data.data;
  },
  clearQr: async (tableId: number): Promise<void> => {
    await api.delete(\`/qr/\${tableId}/clear\`);
  },
  getQrDetails: async (tableId: number): Promise<QrTokenDetails> => {
    const res = await api.get<ApiResponse<QrTokenDetails>>(\`/qr/\${tableId}\`);
    return res.data.data;
  },
};
`);

write('api/order.api.ts', `import api from './axios-instance';
import { ApiResponse, PageResponse, PaginationParams } from '@/types/common';
import { SaleOrder, OrderStatus, Invoice } from '@/types/order';

export interface OrderQueryParams extends PaginationParams {
  status?: OrderStatus;
  tableId?: number;
}

export const orderApi = {
  getAll: async (params?: OrderQueryParams): Promise<PageResponse<SaleOrder>> => {
    const res = await api.get<ApiResponse<PageResponse<SaleOrder>>>('/orders', { params });
    return res.data.data;
  },
  getById: async (id: number): Promise<SaleOrder> => {
    const res = await api.get<ApiResponse<SaleOrder>>(\`/orders/\${id}\`);
    return res.data.data;
  },
  create: async (payload: any): Promise<SaleOrder> => {
    const res = await api.post<ApiResponse<SaleOrder>>('/orders', payload);
    return res.data.data;
  },
  update: async (id: number, payload: any): Promise<SaleOrder> => {
    const res = await api.put<ApiResponse<SaleOrder>>(\`/orders/\${id}\`, payload);
    return res.data.data;
  },
  addItems: async (id: number, items: { menuId: number; qty: number; note?: string }[]): Promise<SaleOrder> => {
    const res = await api.post<ApiResponse<SaleOrder>>(\`/orders/\${id}/add-items\`, { items });
    return res.data.data;
  },
  complete: async (id: number): Promise<SaleOrder> => {
    const res = await api.post<ApiResponse<SaleOrder>>(\`/orders/\${id}/complete\`);
    return res.data.data;
  },
  pay: async (id: number): Promise<SaleOrder> => {
    const res = await api.post<ApiResponse<SaleOrder>>(\`/orders/\${id}/pay\`);
    return res.data.data;
  },
  cancel: async (id: number): Promise<SaleOrder> => {
    const res = await api.post<ApiResponse<SaleOrder>>(\`/orders/\${id}/cancel\`);
    return res.data.data;
  },
  delete: async (id: number): Promise<void> => {
    await api.delete(\`/orders/\${id}\`);
  },
  getInvoice: async (id: number): Promise<Invoice> => {
    const res = await api.get<ApiResponse<Invoice>>(\`/orders/\${id}/invoice\`);
    return res.data.data;
  },
};
`);

write('api/expense.api.ts', `import api from './axios-instance';
import { ApiResponse, PageResponse, PaginationParams } from '@/types/common';
import { Expense } from '@/types/expense';

export interface ExpenseQueryParams extends PaginationParams {
  start?: string;
  end?: string;
}

export const expenseApi = {
  getAll: async (params?: ExpenseQueryParams): Promise<PageResponse<Expense>> => {
    const res = await api.get<ApiResponse<PageResponse<Expense>>>('/expenses', { params });
    return res.data.data;
  },
  getById: async (id: number): Promise<Expense> => {
    const res = await api.get<ApiResponse<Expense>>(\`/expenses/\${id}\`);
    return res.data.data;
  },
  create: async (payload: Partial<Expense>): Promise<Expense> => {
    const res = await api.post<ApiResponse<Expense>>('/expenses', payload);
    return res.data.data;
  },
  update: async (id: number, payload: Partial<Expense>): Promise<Expense> => {
    const res = await api.put<ApiResponse<Expense>>(\`/expenses/\${id}\`, payload);
    return res.data.data;
  },
  delete: async (id: number): Promise<void> => {
    await api.delete(\`/expenses/\${id}\`);
  },
};
`);

write('api/report.api.ts', `import api from './axios-instance';
import { ApiResponse } from '@/types/common';
import { DashboardMetrics, RevenueReport, StockAlert, OrderSummary } from '@/types/report';

export const reportApi = {
  getDashboard: async (): Promise<DashboardMetrics> => {
    const res = await api.get<ApiResponse<DashboardMetrics>>('/dashboard');
    return res.data.data;
  },
  getRevenue: async (start?: string, end?: string): Promise<RevenueReport> => {
    const res = await api.get<ApiResponse<RevenueReport>>('/reports/revenue', { params: { start, end } });
    return res.data.data;
  },
  getRevenueByDay: async (day: string): Promise<OrderSummary[]> => {
    const res = await api.get<ApiResponse<OrderSummary[]>>(\`/reports/revenue/\${day}\`);
    return res.data.data;
  },
  getStockReport: async (): Promise<StockAlert[]> => {
    const res = await api.get<ApiResponse<StockAlert[]>>('/reports/stock');
    return res.data.data;
  },
};
`);

write('api/public-order.api.ts', `import api from './axios-instance';
import { ApiResponse } from '@/types/common';
import { SaleOrder } from '@/types/order';

export interface PublicTableSession {
  tableId: number;
  tableNumber: string;
  capacity: number;
  tableStatus: string;
  activeOrder?: SaleOrder;
}

export const publicOrderApi = {
  startSession: async (token: string): Promise<PublicTableSession> => {
    const res = await api.get<ApiResponse<PublicTableSession>>('/public-order/start', { params: { token } });
    return res.data.data;
  },
  submitOrder: async (payload: any): Promise<SaleOrder> => {
    const res = await api.post<ApiResponse<SaleOrder>>('/public-order/submit', payload);
    return res.data.data;
  },
};
`);

// 2. Custom Hooks (TanStack Query)
write('hooks/use-auth.ts', `import { useMutation } from '@tanstack/react-query';
import { authApi } from '@/api/auth.api';
import { useAuthStore } from '@/stores/auth-store';
import { LoginPayload, RegisterPayload } from '@/types/auth';
import { toast } from 'sonner';

export const useLogin = () => {
  const login = useAuthStore((state) => state.login);

  return useMutation({
    mutationFn: (payload: LoginPayload) => authApi.login(payload),
    onSuccess: (data) => {
      login(data.token, data.user);
      toast.success('Đăng nhập thành công! Chào mừng ' + data.user.fullname);
    },
    onError: (err: any) => {
      toast.error(err.response?.data?.message || 'Đăng nhập thất bại. Vui lòng kiểm tra lại tài khoản');
    },
  });
};

export const useRegister = () => {
  return useMutation({
    mutationFn: (payload: RegisterPayload) => authApi.register(payload),
    onSuccess: () => {
      toast.success('Đăng ký tài khoản thành công! Bạn có thể đăng nhập ngay');
    },
    onError: (err: any) => {
      toast.error(err.response?.data?.message || 'Đăng ký thất bại');
    },
  });
};
`);

write('hooks/use-users.ts', `import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { userApi, UserQueryParams } from '@/api/user.api';
import { toast } from 'sonner';

export const useUsers = (params?: UserQueryParams) => {
  return useQuery({
    queryKey: ['users', params],
    queryFn: () => userApi.getAll(params),
  });
};

export const useCreateUser = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: userApi.create,
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['users'] });
      toast.success('Tạo người dùng thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi khi tạo người dùng'),
  });
};

export const useUpdateUser = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: ({ id, payload }: { id: number; payload: any }) => userApi.update(id, payload),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['users'] });
      toast.success('Cập nhật người dùng thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi cập nhật'),
  });
};

export const useChangePassword = () => {
  return useMutation({
    mutationFn: ({ id, payload }: { id: number; payload: any }) => userApi.changePassword(id, payload),
    onSuccess: () => {
      toast.success('Đổi mật khẩu thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi đổi mật khẩu'),
  });
};

export const useDeleteUser = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: userApi.delete,
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['users'] });
      toast.success('Xóa người dùng thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi xóa người dùng'),
  });
};
`);

write('hooks/use-menu.ts', `import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { menuApi, MenuQueryParams } from '@/api/menu.api';
import { MenuItem, RecipeItem } from '@/types/menu';
import { toast } from 'sonner';

export const useMenuItems = (params?: MenuQueryParams) => {
  return useQuery({
    queryKey: ['menu-items', params],
    queryFn: () => menuApi.getAll(params),
  });
};

export const useMenuItem = (id: number) => {
  return useQuery({
    queryKey: ['menu-item', id],
    queryFn: () => menuApi.getById(id),
    enabled: !!id,
  });
};

export const useCreateMenuItem = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (payload: Partial<MenuItem>) => menuApi.create(payload),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['menu-items'] });
      toast.success('Tạo món ăn thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi tạo món'),
  });
};

export const useUpdateMenuItem = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: ({ id, payload }: { id: number; payload: Partial<MenuItem> }) => menuApi.update(id, payload),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['menu-items'] });
      toast.success('Cập nhật món thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi cập nhật'),
  });
};

export const useDeleteMenuItem = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (id: number) => menuApi.delete(id),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['menu-items'] });
      toast.success('Xóa món ăn thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi xóa món'),
  });
};

export const useRecipes = (menuId: number) => {
  return useQuery({
    queryKey: ['recipes', menuId],
    queryFn: () => menuApi.getRecipes(menuId),
    enabled: !!menuId,
  });
};

export const useSaveRecipe = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: ({ menuId, items }: { menuId: number; items: RecipeItem[] }) => menuApi.saveRecipe(menuId, items),
    onSuccess: (_, variables) => {
      qc.invalidateQueries({ queryKey: ['recipes', variables.menuId] });
      toast.success('Lưu công thức món thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi lưu công thức'),
  });
};
`);

write('hooks/use-ingredients.ts', `import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ingredientApi, IngredientQueryParams } from '@/api/ingredient.api';
import { Ingredient, IngredientCategory } from '@/types/ingredient';
import { toast } from 'sonner';

export const useIngredients = (params?: IngredientQueryParams) => {
  return useQuery({
    queryKey: ['ingredients', params],
    queryFn: () => ingredientApi.getAll(params),
  });
};

export const useIngredientCategories = () => {
  return useQuery({
    queryKey: ['ingredient-categories'],
    queryFn: () => ingredientApi.getCategories(),
  });
};

export const useCreateIngredient = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (payload: Partial<Ingredient>) => ingredientApi.create(payload),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['ingredients'] });
      toast.success('Tạo nguyên liệu thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi tạo nguyên liệu'),
  });
};

export const useUpdateIngredient = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: ({ id, payload }: { id: number; payload: Partial<Ingredient> }) => ingredientApi.update(id, payload),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['ingredients'] });
      toast.success('Cập nhật nguyên liệu thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi cập nhật'),
  });
};

export const useDeleteIngredient = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (id: number) => ingredientApi.delete(id),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['ingredients'] });
      toast.success('Xóa nguyên liệu thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi xóa nguyên liệu'),
  });
};

export const useCreateCategory = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (payload: Partial<IngredientCategory>) => ingredientApi.createCategory(payload),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['ingredient-categories'] });
      toast.success('Tạo danh mục thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi tạo danh mục'),
  });
};

export const useDeleteCategory = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (id: number) => ingredientApi.deleteCategory(id),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['ingredient-categories'] });
      toast.success('Xóa danh mục thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi xóa danh mục'),
  });
};
`);

write('hooks/use-inventory.ts', `import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { inventoryApi, ReceiptQueryParams } from '@/api/inventory.api';
import { PaginationParams } from '@/types/common';
import { toast } from 'sonner';

export const useReceipts = (params?: ReceiptQueryParams) => {
  return useQuery({
    queryKey: ['inventory-receipts', params],
    queryFn: () => inventoryApi.getReceipts(params),
  });
};

export const useCreateReceipt = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: inventoryApi.createReceipt,
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['inventory-receipts'] });
      toast.success('Tạo phiếu nhập kho thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi tạo phiếu nhập'),
  });
};

export const useCompleteReceipt = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (id: number) => inventoryApi.completeReceipt(id),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['inventory-receipts'] });
      qc.invalidateQueries({ queryKey: ['ingredients'] });
      toast.success('Đã hoàn thành phiếu nhập và cộng tồn kho');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi hoàn thành phiếu'),
  });
};

export const useIssues = (params?: PaginationParams) => {
  return useQuery({
    queryKey: ['inventory-issues', params],
    queryFn: () => inventoryApi.getIssues(params),
  });
};

export const useCreateManualIssue = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: inventoryApi.createManualIssue,
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['inventory-issues'] });
      qc.invalidateQueries({ queryKey: ['ingredients'] });
      toast.success('Xuất kho thành công và đã trừ tồn kho');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi xuất kho'),
  });
};

export const useAdjustStock = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: inventoryApi.adjustStock,
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['ingredients'] });
      toast.success('Điều chỉnh số lượng kho thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi điều chỉnh kho'),
  });
};
`);

write('hooks/use-tables.ts', `import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { tableApi } from '@/api/table.api';
import { RestaurantTable, TableStatus } from '@/types/table';
import { toast } from 'sonner';

export const useTables = () => {
  return useQuery({
    queryKey: ['tables'],
    queryFn: tableApi.getAll,
  });
};

export const useCreateTable = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (payload: Partial<RestaurantTable>) => tableApi.create(payload),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tables'] });
      toast.success('Tạo bàn mới thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi tạo bàn'),
  });
};

export const useUpdateTableStatus = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: ({ id, status }: { id: number; status: TableStatus }) => tableApi.updateStatus(id, status),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tables'] });
      toast.success('Cập nhật trạng thái bàn thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi cập nhật'),
  });
};

export const useDeleteTable = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (id: number) => tableApi.delete(id),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tables'] });
      toast.success('Xóa bàn thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi xóa bàn'),
  });
};

export const useGenerateQr = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (tableId: number) => tableApi.generateQr(tableId),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tables'] });
      toast.success('Sinh mã QR bàn thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi sinh mã QR'),
  });
};

export const useClearQr = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (tableId: number) => tableApi.clearQr(tableId),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tables'] });
      toast.success('Xóa mã QR bàn thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi xóa mã QR'),
  });
};
`);

write('hooks/use-reservations.ts', `import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { tableApi } from '@/api/table.api';
import { toast } from 'sonner';

export const useReservations = (tableId?: number) => {
  return useQuery({
    queryKey: ['reservations', tableId],
    queryFn: () => tableApi.getReservations(tableId),
  });
};

export const useCreateReservation = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: tableApi.createReservation,
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['reservations'] });
      qc.invalidateQueries({ queryKey: ['tables'] });
      toast.success('Đặt bàn thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi đặt bàn hoặc trùng lịch'),
  });
};

export const useDeleteReservation = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: tableApi.deleteReservation,
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['reservations'] });
      toast.success('Hủy lịch đặt bàn thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi hủy lịch đặt bàn'),
  });
};
`);

write('hooks/use-orders.ts', `import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { orderApi, OrderQueryParams } from '@/api/order.api';
import { toast } from 'sonner';

export const useOrders = (params?: OrderQueryParams) => {
  return useQuery({
    queryKey: ['orders', params],
    queryFn: () => orderApi.getAll(params),
  });
};

export const useOrder = (id: number) => {
  return useQuery({
    queryKey: ['order', id],
    queryFn: () => orderApi.getById(id),
    enabled: !!id,
  });
};

export const useInvoice = (id: number) => {
  return useQuery({
    queryKey: ['invoice', id],
    queryFn: () => orderApi.getInvoice(id),
    enabled: !!id,
  });
};

export const useCreateOrder = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: orderApi.create,
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['orders'] });
      qc.invalidateQueries({ queryKey: ['tables'] });
      toast.success('Tạo đơn hàng thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi tạo đơn hàng'),
  });
};

export const useAddOrderItems = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: ({ id, items }: { id: number; items: any[] }) => orderApi.addItems(id, items),
    onSuccess: (_, variables) => {
      qc.invalidateQueries({ queryKey: ['orders'] });
      qc.invalidateQueries({ queryKey: ['order', variables.id] });
      toast.success('Đã thêm món vào đơn');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi thêm món'),
  });
};

export const useCompleteOrder = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (id: number) => orderApi.complete(id),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['orders'] });
      toast.success('Đơn hàng đã phục vụ (SERVED) và đã trừ kho');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi hoàn thành đơn'),
  });
};

export const usePayOrder = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (id: number) => orderApi.pay(id),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['orders'] });
      qc.invalidateQueries({ queryKey: ['tables'] });
      qc.invalidateQueries({ queryKey: ['dashboard'] });
      toast.success('Thanh toán thành công và đã giải phóng bàn');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi thanh toán'),
  });
};

export const useCancelOrder = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (id: number) => orderApi.cancel(id),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['orders'] });
      qc.invalidateQueries({ queryKey: ['tables'] });
      toast.success('Đã hủy đơn hàng');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi hủy đơn'),
  });
};
`);

write('hooks/use-expenses.ts', `import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { expenseApi, ExpenseQueryParams } from '@/api/expense.api';
import { Expense } from '@/types/expense';
import { toast } from 'sonner';

export const useExpenses = (params?: ExpenseQueryParams) => {
  return useQuery({
    queryKey: ['expenses', params],
    queryFn: () => expenseApi.getAll(params),
  });
};

export const useCreateExpense = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (payload: Partial<Expense>) => expenseApi.create(payload),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['expenses'] });
      qc.invalidateQueries({ queryKey: ['dashboard'] });
      toast.success('Ghi nhận chi phí thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi tạo chi phí'),
  });
};

export const useDeleteExpense = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (id: number) => expenseApi.delete(id),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['expenses'] });
      toast.success('Xóa chi phí thành công');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi xóa chi phí'),
  });
};
`);

write('hooks/use-reports.ts', `import { useQuery } from '@tanstack/react-query';
import { reportApi } from '@/api/report.api';

export const useDashboard = () => {
  return useQuery({
    queryKey: ['dashboard'],
    queryFn: reportApi.getDashboard,
    refetchInterval: 30000, // auto refresh every 30s
  });
};

export const useRevenueReport = (start?: string, end?: string) => {
  return useQuery({
    queryKey: ['revenue-report', start, end],
    queryFn: () => reportApi.getRevenue(start, end),
  });
};

export const useDailyOrderDetails = (day: string) => {
  return useQuery({
    queryKey: ['daily-orders', day],
    queryFn: () => reportApi.getRevenueByDay(day),
    enabled: !!day,
  });
};

export const useStockReport = () => {
  return useQuery({
    queryKey: ['stock-report'],
    queryFn: reportApi.getStockReport,
  });
};
`);

console.log('Finished writing Frontend APIs and TanStack Query Hooks!');
