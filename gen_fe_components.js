const fs = require('fs');
const path = require('path');

const src = 'D:/restaurant-microservices/frontend/src';

function write(rel, content) {
  const full = path.join(src, rel);
  fs.mkdirSync(path.dirname(full), { recursive: true });
  fs.writeFileSync(full, content.trim() + '\n', 'utf8');
  console.log('Created ' + rel);
}

// 1. UI Primitives
write('components/ui/button.tsx', `import * as React from 'react';
import { cn } from '@/lib/utils';

export interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link' | 'success';
  size?: 'default' | 'sm' | 'lg' | 'icon';
  isLoading?: boolean;
}

export const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant = 'default', size = 'default', isLoading, children, disabled, ...props }, ref) => {
    const base = 'inline-flex items-center justify-center whitespace-nowrap rounded-lg text-sm font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 active:scale-[0.98] select-none';

    const variants = {
      default: 'bg-primary text-primary-foreground shadow hover:bg-primary/90 hover:shadow-md',
      destructive: 'bg-destructive text-destructive-foreground shadow hover:bg-destructive/90',
      outline: 'border border-input bg-background hover:bg-accent hover:text-accent-foreground shadow-sm',
      secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
      ghost: 'hover:bg-accent hover:text-accent-foreground',
      link: 'text-primary underline-offset-4 hover:underline',
      success: 'bg-emerald-600 text-white shadow hover:bg-emerald-700',
    };

    const sizes = {
      default: 'h-10 px-4 py-2',
      sm: 'h-8 rounded-md px-3 text-xs',
      lg: 'h-11 rounded-lg px-8 text-base',
      icon: 'h-9 w-9 p-0',
    };

    return (
      <button
        ref={ref}
        disabled={disabled || isLoading}
        className={cn(base, variants[variant], sizes[size], className)}
        {...props}
      >
        {isLoading && (
          <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-current" fill="none" viewBox="0 0 24 24">
            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
          </svg>
        )}
        {children}
      </button>
    );
  }
);
Button.displayName = 'Button';
`);

write('components/ui/input.tsx', `import * as React from 'react';
import { cn } from '@/lib/utils';

export interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  error?: string;
}

export const Input = React.forwardRef<HTMLInputElement, InputProps>(
  ({ className, type, error, ...props }, ref) => {
    return (
      <div className="w-full">
        <input
          type={type}
          className={cn(
            'flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-50 transition-colors shadow-sm',
            error && 'border-destructive focus-visible:ring-destructive',
            className
          )}
          ref={ref}
          {...props}
        />
        {error && <p className="mt-1 text-xs text-destructive font-medium">{error}</p>}
      </div>
    );
  }
);
Input.displayName = 'Input';
`);

write('components/ui/card.tsx', `import * as React from 'react';
import { cn } from '@/lib/utils';

export const Card = React.forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
  ({ className, ...props }, ref) => (
    <div ref={ref} className={cn('rounded-xl border bg-card text-card-foreground shadow-sm transition-all', className)} {...props} />
  )
);
Card.displayName = 'Card';

export const CardHeader = React.forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
  ({ className, ...props }, ref) => (
    <div ref={ref} className={cn('flex flex-col space-y-1.5 p-6', className)} {...props} />
  )
);
CardHeader.displayName = 'CardHeader';

export const CardTitle = React.forwardRef<HTMLParagraphElement, React.HTMLAttributes<HTMLHeadingElement>>(
  ({ className, ...props }, ref) => (
    <h3 ref={ref} className={cn('text-xl font-bold leading-none tracking-tight', className)} {...props} />
  )
);
CardTitle.displayName = 'CardTitle';

export const CardDescription = React.forwardRef<HTMLParagraphElement, React.HTMLAttributes<HTMLParagraphElement>>(
  ({ className, ...props }, ref) => (
    <p ref={ref} className={cn('text-sm text-muted-foreground', className)} {...props} />
  )
);
CardDescription.displayName = 'CardDescription';

export const CardContent = React.forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
  ({ className, ...props }, ref) => (
    <div ref={ref} className={cn('p-6 pt-0', className)} {...props} />
  )
);
CardContent.displayName = 'CardContent';

export const CardFooter = React.forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
  ({ className, ...props }, ref) => (
    <div ref={ref} className={cn('flex items-center p-6 pt-0', className)} {...props} />
  )
);
CardFooter.displayName = 'CardFooter';
`);

write('components/ui/badge.tsx', `import * as React from 'react';
import { cn } from '@/lib/utils';

export interface BadgeProps extends React.HTMLAttributes<HTMLDivElement> {
  variant?: 'default' | 'secondary' | 'destructive' | 'outline' | 'success' | 'warning' | 'info';
}

export function Badge({ className, variant = 'default', ...props }: BadgeProps) {
  const variants = {
    default: 'border-transparent bg-primary text-primary-foreground',
    secondary: 'border-transparent bg-secondary text-secondary-foreground',
    destructive: 'border-transparent bg-destructive/15 text-destructive font-semibold',
    outline: 'text-foreground border border-border',
    success: 'border-transparent bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 font-semibold',
    warning: 'border-transparent bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 font-semibold',
    info: 'border-transparent bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 font-semibold',
  };

  return (
    <div
      className={cn(
        'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
        variants[variant],
        className
      )}
      {...props}
    />
  );
}
`);

write('components/ui/dialog.tsx', `import * as React from 'react';
import { cn } from '@/lib/utils';
import { X } from 'lucide-react';

export interface DialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  title?: string;
  description?: string;
  children: React.ReactNode;
  className?: string;
}

export function Dialog({ open, onOpenChange, title, description, children, className }: DialogProps) {
  if (!open) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
      {/* Backdrop */}
      <div
        className="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
        onClick={() => onOpenChange(false)}
      />
      {/* Dialog box */}
      <div
        className={cn(
          'relative z-50 w-full max-w-lg rounded-2xl bg-card p-6 shadow-2xl border animate-in fade-in-0 zoom-in-95 max-h-[90vh] overflow-y-auto',
          className
        )}
      >
        <button
          onClick={() => onOpenChange(false)}
          className="absolute right-4 top-4 rounded-lg p-1.5 text-muted-foreground hover:bg-muted transition-colors"
        >
          <X className="h-5 w-5" />
          <span className="sr-only">Close</span>
        </button>

        {title && <h2 className="text-xl font-bold tracking-tight mb-1">{title}</h2>}
        {description && <p className="text-sm text-muted-foreground mb-4">{description}</p>}

        <div className="mt-4">{children}</div>
      </div>
    </div>
  );
}
`);

write('components/ui/table.tsx', `import * as React from 'react';
import { cn } from '@/lib/utils';

export const Table = React.forwardRef<HTMLTableElement, React.HTMLAttributes<HTMLTableElement>>(
  ({ className, ...props }, ref) => (
    <div className="relative w-full overflow-auto">
      <table ref={ref} className={cn('w-full caption-bottom text-sm', className)} {...props} />
    </div>
  )
);
Table.displayName = 'Table';

export const TableHeader = React.forwardRef<HTMLTableSectionElement, React.HTMLAttributes<HTMLTableSectionElement>>(
  ({ className, ...props }, ref) => (
    <thead ref={ref} className={cn('[&_tr]:border-b bg-muted/40 font-medium', className)} {...props} />
  )
);
TableHeader.displayName = 'TableHeader';

export const TableBody = React.forwardRef<HTMLTableSectionElement, React.HTMLAttributes<HTMLTableSectionElement>>(
  ({ className, ...props }, ref) => (
    <tbody ref={ref} className={cn('[&_tr:last-child]:border-0', className)} {...props} />
  )
);
TableBody.displayName = 'TableBody';

export const TableRow = React.forwardRef<HTMLTableRowElement, React.HTMLAttributes<HTMLTableRowElement>>(
  ({ className, ...props }, ref) => (
    <tr
      ref={ref}
      className={cn('border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted', className)}
      {...props}
    />
  )
);
TableRow.displayName = 'TableRow';

export const TableHead = React.forwardRef<HTMLTableCellElement, React.ThHTMLAttributes<HTMLTableCellElement>>(
  ({ className, ...props }, ref) => (
    <th
      ref={ref}
      className={cn('h-11 px-4 text-left align-middle font-semibold text-muted-foreground [&:has([role=checkbox])]:pr-0', className)}
      {...props}
    />
  )
);
TableHead.displayName = 'TableHead';

export const TableCell = React.forwardRef<HTMLTableCellElement, React.TdHTMLAttributes<HTMLTableCellElement>>(
  ({ className, ...props }, ref) => (
    <td ref={ref} className={cn('p-4 align-middle [&:has([role=checkbox])]:pr-0', className)} {...props} />
  )
);
TableCell.displayName = 'TableCell';
`);

write('components/ui/select.tsx', `import * as React from 'react';
import { cn } from '@/lib/utils';

export interface SelectProps extends React.SelectHTMLAttributes<HTMLSelectElement> {
  error?: string;
  label?: string;
}

export const Select = React.forwardRef<HTMLSelectElement, SelectProps>(
  ({ className, children, error, label, ...props }, ref) => {
    return (
      <div className="w-full">
        {label && <label className="block text-xs font-semibold text-muted-foreground mb-1.5">{label}</label>}
        <select
          ref={ref}
          className={cn(
            'flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-50 transition-colors shadow-sm',
            error && 'border-destructive focus-visible:ring-destructive',
            className
          )}
          {...props}
        >
          {children}
        </select>
        {error && <p className="mt-1 text-xs text-destructive font-medium">{error}</p>}
      </div>
    );
  }
);
Select.displayName = 'Select';
`);

// 2. Shared Components
write('components/shared/status-badge.tsx', `import { Badge } from '@/components/ui/badge';

interface StatusBadgeProps {
  status?: string | null;
}

export function StatusBadge({ status }: StatusBadgeProps) {
  if (!status) return null;

  switch (status.toUpperCase()) {
    case 'OPEN':
    case 'PENDING':
      return <Badge variant="warning">Đang chờ (PENDING)</Badge>;
    case 'SERVED':
    case 'CONFIRMED':
      return <Badge variant="info">Đã phục vụ</Badge>;
    case 'PAID':
    case 'COMPLETED':
      return <Badge variant="success">Hoàn thành</Badge>;
    case 'CANCEL':
    case 'CANCELLED':
      return <Badge variant="destructive">Đã hủy</Badge>;
    case 'FREE':
      return <Badge variant="success">Bàn trống</Badge>;
    case 'OCCUPIED':
      return <Badge variant="destructive">Có khách</Badge>;
    case 'RESERVED':
      return <Badge variant="warning">Đã đặt trước</Badge>;
    case 'CRITICAL':
      return <Badge variant="destructive">Hết hàng (Khẩn cấp)</Badge>;
    case 'WARNING':
      return <Badge variant="warning">Sắp hết</Badge>;
    case 'NORMAL':
      return <Badge variant="success">Ổn định</Badge>;
    case 'ADMIN':
      return <Badge variant="destructive">Admin</Badge>;
    case 'MANAGER':
      return <Badge variant="info">Quản lý</Badge>;
    case 'USER':
      return <Badge variant="secondary">Nhân viên</Badge>;
    default:
      return <Badge variant="outline">{status}</Badge>;
  }
}
`);

write('components/shared/page-header.tsx', `import * as React from 'react';

interface PageHeaderProps {
  title: string;
  description?: string;
  children?: React.ReactNode;
}

export function PageHeader({ title, description, children }: PageHeaderProps) {
  return (
    <div className="flex flex-col md:flex-row md:items-center justify-between pb-6 gap-4 border-b">
      <div>
        <h1 className="text-2xl md:text-3xl font-extrabold tracking-tight text-foreground">{title}</h1>
        {description && <p className="text-sm text-muted-foreground mt-1">{description}</p>}
      </div>
      {children && <div className="flex items-center gap-3">{children}</div>}
    </div>
  );
}
`);

write('components/shared/stats-card.tsx', `import * as React from 'react';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';

interface StatsCardProps {
  title: string;
  value: string | number;
  subtitle?: string;
  icon: React.ReactNode;
  iconBg?: string;
  trend?: string;
  trendPositive?: boolean;
}

export function StatsCard({ title, value, subtitle, icon, iconBg = 'bg-primary/10 text-primary', trend, trendPositive }: StatsCardProps) {
  return (
    <Card className="hover:shadow-md transition-all border">
      <CardContent className="p-6">
        <div className="flex items-center justify-between">
          <p className="text-sm font-medium text-muted-foreground">{title}</p>
          <div className={cn('p-2.5 rounded-xl', iconBg)}>{icon}</div>
        </div>
        <div className="mt-3">
          <h3 className="text-2xl font-bold tracking-tight">{value}</h3>
          {(subtitle || trend) && (
            <div className="flex items-center gap-2 mt-1">
              {trend && (
                <span className={cn('text-xs font-semibold', trendPositive ? 'text-emerald-600' : 'text-rose-600')}>
                  {trend}
                </span>
              )}
              {subtitle && <span className="text-xs text-muted-foreground">{subtitle}</span>}
            </div>
          )}
        </div>
      </CardContent>
    </Card>
  );
}
`);

write('components/shared/confirm-dialog.tsx', `import { Dialog } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

interface ConfirmDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  title: string;
  description: string;
  onConfirm: () => void;
  confirmText?: string;
  cancelText?: string;
  isDanger?: boolean;
  isLoading?: boolean;
}

export function ConfirmDialog({
  open,
  onOpenChange,
  title,
  description,
  onConfirm,
  confirmText = 'Xác nhận',
  cancelText = 'Hủy bỏ',
  isDanger = false,
  isLoading = false,
}: ConfirmDialogProps) {
  return (
    <Dialog open={open} onOpenChange={onOpenChange} title={title} description={description}>
      <div className="flex justify-end gap-3 mt-6">
        <Button variant="outline" onClick={() => onOpenChange(false)} disabled={isLoading}>
          {cancelText}
        </Button>
        <Button
          variant={isDanger ? 'destructive' : 'default'}
          onClick={() => {
            onConfirm();
            onOpenChange(false);
          }}
          isLoading={isLoading}
        >
          {confirmText}
        </Button>
      </div>
    </Dialog>
  );
}
`);

write('components/shared/loading-spinner.tsx', `export function LoadingSpinner({ text = 'Đang tải dữ liệu...' }: { text?: string }) {
  return (
    <div className="flex flex-col items-center justify-center min-h-[300px] w-full p-8">
      <div className="h-10 w-10 animate-spin rounded-full border-4 border-primary border-t-transparent" />
      <p className="mt-4 text-sm font-medium text-muted-foreground animate-pulse">{text}</p>
    </div>
  );
}
`);

write('components/shared/empty-state.tsx', `import * as React from 'react';
import { Inbox } from 'lucide-react';
import { Button } from '@/components/ui/button';

interface EmptyStateProps {
  title?: string;
  description?: string;
  actionText?: string;
  onAction?: () => void;
  icon?: React.ReactNode;
}

export function EmptyState({
  title = 'Chưa có dữ liệu',
  description = 'Hiện tại chưa có bản ghi nào để hiển thị.',
  actionText,
  onAction,
  icon = <Inbox className="h-12 w-12 text-muted-foreground/60" />,
}: EmptyStateProps) {
  return (
    <div className="flex flex-col items-center justify-center min-h-[260px] p-8 text-center rounded-xl border border-dashed bg-muted/20">
      <div className="mb-4">{icon}</div>
      <h3 className="text-base font-semibold text-foreground">{title}</h3>
      <p className="text-sm text-muted-foreground mt-1 max-w-sm">{description}</p>
      {actionText && onAction && (
        <Button onClick={onAction} size="sm" className="mt-4">
          {actionText}
        </Button>
      )}
    </div>
  );
}
`);

// 3. Layout Components
write('components/layout/protected-route.tsx', `import * as React from 'react';
import { Navigate, Outlet } from 'react-router-dom';
import { useAuthStore } from '@/stores/auth-store';
import { Role } from '@/types/auth';

interface ProtectedRouteProps {
  allowedRoles?: Role[];
}

export function ProtectedRoute({ allowedRoles }: ProtectedRouteProps) {
  const { isAuthenticated, user } = useAuthStore();

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  if (allowedRoles && user && !allowedRoles.includes(user.role)) {
    return <Navigate to="/dashboard" replace />;
  }

  return <Outlet />;
}
`);

write('components/layout/app-header.tsx', `import { useAuthStore } from '@/stores/auth-store';
import { Button } from '@/components/ui/button';
import { StatusBadge } from '@/components/shared/status-badge';
import { LogOut, User as UserIcon, Bell } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

export function AppHeader() {
  const { user, logout } = useAuthStore();
  const navigate = useNavigate();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <header className="sticky top-0 z-40 flex h-16 w-full items-center justify-between border-b bg-card/80 px-6 backdrop-blur-md">
      <div className="flex items-center gap-3">
        <span className="text-xs font-semibold px-2 py-1 rounded bg-primary/10 text-primary">
          SOA Microservices
        </span>
      </div>

      <div className="flex items-center gap-4">
        {/* User Info */}
        <div className="flex items-center gap-3 border-r pr-4">
          <div className="h-9 w-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
            {user?.fullname ? user.fullname.charAt(0).toUpperCase() : <UserIcon className="h-4 w-4" />}
          </div>
          <div className="hidden sm:block text-left">
            <p className="text-sm font-bold leading-none">{user?.fullname || user?.username}</p>
            <div className="mt-1">
              <StatusBadge status={user?.role} />
            </div>
          </div>
        </div>

        {/* Logout */}
        <Button variant="ghost" size="sm" onClick={handleLogout} className="text-muted-foreground hover:text-destructive">
          <LogOut className="h-4 w-4 mr-1.5" />
          <span className="hidden sm:inline">Đăng xuất</span>
        </Button>
      </div>
    </header>
  );
}
`);

write('components/layout/app-sidebar.tsx', `import * as React from 'react';
import { NavLink } from 'react-router-dom';
import { cn } from '@/lib/utils';
import { useAuthStore } from '@/stores/auth-store';
import {
  LayoutDashboard,
  UtensilsCrossed,
  BookOpen,
  Apple,
  PackagePlus,
  PackageMinus,
  Table,
  CalendarDays,
  QrCode,
  DollarSign,
  BarChart3,
  Users,
  Layers,
} from 'lucide-react';

interface NavItem {
  name: string;
  href: string;
  icon: React.ElementType;
  roles?: string[];
}

const navItems: NavItem[] = [
  { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
  { name: 'Đơn hàng & POS', href: '/orders', icon: UtensilsCrossed },
  { name: 'Sơ đồ bàn ăn', href: '/tables', icon: Table },
  { name: 'Đặt chỗ trước', href: '/reservations', icon: CalendarDays },
  { name: 'Thực đơn món', href: '/menu', icon: BookOpen },
  { name: 'Quản lý công thức', href: '/recipes', icon: Layers },
  { name: 'Nguyên liệu', href: '/ingredients', icon: Apple },
  { name: 'Nhập kho', href: '/inventory/receipts', icon: PackagePlus },
  { name: 'Xuất kho', href: '/inventory/issues', icon: PackageMinus },
  { name: 'Mã QR bàn ăn', href: '/qr', icon: QrCode },
  { name: 'Sổ chi phí', href: '/expenses', icon: DollarSign },
  { name: 'Báo cáo doanh thu', href: '/reports/revenue', icon: BarChart3 },
  { name: 'Báo cáo tồn kho', href: '/reports/stock', icon: BarChart3 },
  { name: 'Quản trị nhân viên', href: '/users', icon: Users, roles: ['ADMIN'] },
];

export function AppSidebar() {
  const { user } = useAuthStore();

  return (
    <aside className="w-64 border-r bg-card h-screen sticky top-0 flex flex-col justify-between hidden md:flex select-none">
      <div>
        {/* Brand */}
        <div className="h-16 flex items-center px-6 border-b gap-2.5">
          <div className="h-9 w-9 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center text-white text-lg shadow-md shadow-orange-500/20 font-black">
            🍽️
          </div>
          <div>
            <h2 className="font-extrabold text-base tracking-tight leading-none">Gourmet Haven</h2>
            <p className="text-[11px] text-muted-foreground font-medium mt-0.5">Hệ Thống Nhà Hàng SOA</p>
          </div>
        </div>

        {/* Navigation */}
        <nav className="p-3 space-y-1 overflow-y-auto max-h-[calc(100vh-8rem)]">
          {navItems
            .filter((item) => !item.roles || (user && item.roles.includes(user.role)))
            .map((item) => {
              const Icon = item.icon;
              return (
                <NavLink
                  key={item.href}
                  to={item.href}
                  className={({ isActive }) =>
                    cn(
                      'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all',
                      isActive
                        ? 'bg-primary text-primary-foreground shadow-sm shadow-primary/25'
                        : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                    )
                  }
                >
                  <Icon className="h-4 w-4 shrink-0" />
                  <span>{item.name}</span>
                </NavLink>
              );
            })}
        </nav>
      </div>

      {/* Footer Info */}
      <div className="p-4 border-t bg-muted/20 text-xs text-muted-foreground text-center">
        <p className="font-semibold text-foreground">Phần Mềm Hướng Dịch Vụ</p>
        <p className="text-[11px]">8 Services • React 18 • Spring Boot 3</p>
      </div>
    </aside>
  );
}
`);

write('components/layout/app-layout.tsx', `import { Outlet } from 'react-router-dom';
import { AppSidebar } from './app-sidebar';
import { AppHeader } from './app-header';

export function AppLayout() {
  return (
    <div className="flex min-h-screen bg-background text-foreground">
      <AppSidebar />
      <div className="flex-1 flex flex-col min-w-0">
        <AppHeader />
        <main className="flex-1 p-6 md:p-8 overflow-y-auto">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
`);

console.log('Finished writing Frontend UI Primitives, Shared and Layout Components!');
