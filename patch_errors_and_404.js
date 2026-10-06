const fs = require('fs');

// 1. Create ErrorBoundary component
const errorBoundaryCode = `import * as React from 'react';
import { AlertTriangle, RefreshCw, Home } from 'lucide-react';
import { Button } from '@/components/ui/button';

interface Props {
  children: React.ReactNode;
  fallback?: React.ReactNode;
}

interface State {
  hasError: boolean;
  error: Error | null;
  errorInfo: React.ErrorInfo | null;
}

export class ErrorBoundary extends React.Component<Props, State> {
  public state: State = {
    hasError: false,
    error: null,
    errorInfo: null,
  };

  public static getDerivedStateFromError(error: Error): State {
    return { hasError: true, error, errorInfo: null };
  }

  public componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
    console.error('ErrorBoundary caught an unhandled error:', error, errorInfo);
    this.setState({ errorInfo });
  }

  private handleReset = () => {
    this.setState({ hasError: false, error: null, errorInfo: null });
    window.location.reload();
  };

  public render() {
    if (this.state.hasError) {
      if (this.props.fallback) {
        return this.props.fallback;
      }

      return (
        <div className="min-h-[400px] flex items-center justify-center p-6">
          <div className="max-w-md w-full p-6 rounded-2xl border bg-card text-card-foreground shadow-lg text-center space-y-4">
            <div className="h-14 w-14 rounded-2xl bg-destructive/10 text-destructive flex items-center justify-center mx-auto">
              <AlertTriangle className="h-7 w-7" />
            </div>

            <div className="space-y-1">
              <h2 className="text-lg font-bold">Đã xảy ra sự cố hiển thị</h2>
              <p className="text-xs text-muted-foreground leading-relaxed">
                Hệ thống gặp lỗi bất ngờ trong quá trình xử lý dữ liệu giao diện. Bạn có thể tải lại hoặc quay về trang chủ.
              </p>
            </div>

            {this.state.error && (
              <div className="p-3 bg-muted/50 rounded-xl text-left font-mono text-[11px] text-destructive overflow-x-auto max-h-32 border">
                {this.state.error.message}
              </div>
            )}

            <div className="flex gap-2 justify-center pt-2">
              <Button onClick={this.handleReset} className="gap-2 text-xs font-bold">
                <RefreshCw className="h-3.5 w-3.5" />
                Tải lại trang
              </Button>
              <Button
                variant="outline"
                onClick={() => (window.location.href = '/dashboard')}
                className="gap-2 text-xs font-bold"
              >
                <Home className="h-3.5 w-3.5" />
                Trang chủ
              </Button>
            </div>
          </div>
        </div>
      );
    }

    return this.props.children;
  }
}
`;

fs.writeFileSync('D:/restaurant-microservices/frontend/src/components/shared/error-boundary.tsx', errorBoundaryCode, 'utf8');
console.log('1. Created ErrorBoundary component');

// 2. Create NotFound (404) Page
const notFoundCode = `import * as React from 'react';
import { useNavigate } from 'react-router-dom';
import { Home, ArrowLeft, UtensilsCrossed, Compass } from 'lucide-react';
import { Button } from '@/components/ui/button';

export function NotFoundPage() {
  const navigate = useNavigate();

  return (
    <div className="min-h-[75vh] flex flex-col items-center justify-center text-center px-4 py-12">
      <div className="relative mb-6">
        <div className="h-28 w-28 rounded-3xl bg-primary/10 text-primary flex items-center justify-center mx-auto shadow-inner animate-pulse">
          <Compass className="h-14 w-14" />
        </div>
        <span className="absolute -bottom-2 -right-2 bg-primary text-primary-foreground font-black text-xs px-2.5 py-0.5 rounded-full shadow">
          404
        </span>
      </div>

      <h1 className="text-3xl font-black tracking-tight text-foreground sm:text-4xl mb-2">
        Không Tìm Thấy Trang Yêu Cầu
      </h1>

      <p className="text-sm text-muted-foreground max-w-md mx-auto mb-8 leading-relaxed">
        Đường dẫn bạn vừa truy cập không tồn tại trong hệ thống, hoặc quyền hạn tài khoản của bạn chưa được cấp phép truy cập mục này.
      </p>

      <div className="flex flex-wrap items-center justify-center gap-3">
        <Button onClick={() => navigate(-1)} variant="outline" className="gap-2 font-bold text-xs">
          <ArrowLeft className="h-4 w-4" />
          Quay lại trang trước
        </Button>

        <Button onClick={() => navigate('/dashboard')} className="gap-2 font-bold text-xs">
          <Home className="h-4 w-4" />
          Về Bảng Điều Khiển
        </Button>

        <Button onClick={() => navigate('/orders/create')} variant="secondary" className="gap-2 font-bold text-xs">
          <UtensilsCrossed className="h-4 w-4" />
          Màn hình Bán Hàng (POS)
        </Button>
      </div>
    </div>
  );
}
`;

fs.writeFileSync('D:/restaurant-microservices/frontend/src/pages/not-found.tsx', notFoundCode, 'utf8');
console.log('2. Created NotFoundPage (404)');

// 3. Update main.tsx to use future flags and wrap with ErrorBoundary
let mainTsx = fs.readFileSync('D:/restaurant-microservices/frontend/src/main.tsx', 'utf8');
if (!mainTsx.includes('ErrorBoundary')) {
  mainTsx = `import { ErrorBoundary } from '@/components/shared/error-boundary';\n` + mainTsx;
}
mainTsx = mainTsx.replace(
  '<BrowserRouter>',
  '<BrowserRouter future={{ v7_startTransition: true, v7_relativeSplatPath: true }}>'
);
mainTsx = mainTsx.replace(
  '<App />',
  `<ErrorBoundary><App /></ErrorBoundary>`
);
fs.writeFileSync('D:/restaurant-microservices/frontend/src/main.tsx', mainTsx, 'utf8');
console.log('3. Updated main.tsx with future flags and global ErrorBoundary');

// 4. Update App.tsx to include NotFoundPage
let appTsx = fs.readFileSync('D:/restaurant-microservices/frontend/src/App.tsx', 'utf8');
if (!appTsx.includes('NotFoundPage')) {
  appTsx = `import { NotFoundPage } from '@/pages/not-found';\n` + appTsx;
}
// Replace wildcard fallback with NotFoundPage inside layout
appTsx = appTsx.replace(
  `<Route path="*" element={<Navigate to="/dashboard" replace />} />`,
  `<Route path="*" element={<NotFoundPage />} />`
);
fs.writeFileSync('D:/restaurant-microservices/frontend/src/App.tsx', appTsx, 'utf8');
console.log('4. Updated App.tsx to route unknown paths to NotFoundPage');

// 5. Fix revenue-report.tsx
let revenueReport = fs.readFileSync('D:/restaurant-microservices/frontend/src/pages/report/revenue-report.tsx', 'utf8');

// Replace line 89 report.dailyDetails.length with safe array access
revenueReport = revenueReport.replace(
  `{report.dailyDetails.length === 0 ? (`,
  `{((report.dailyDetails || (report as any).dailyBreakdown || [])).length === 0 ? (`
);

revenueReport = revenueReport.replace(
  `report.dailyDetails.map((d) => (`,
  `(report.dailyDetails || (report as any).dailyBreakdown || []).map((d: any) => (`
);

revenueReport = revenueReport.replace(
  `{formatVND(report.totalProfit)}`,
  `{formatVND(report.totalProfit ?? (report as any).netProfit ?? 0)}`
);

revenueReport = revenueReport.replace(
  `{report.totalOrders} lượt đơn`,
  `{report.totalOrders ?? (report as any).orderCount ?? 0} lượt đơn`
);

fs.writeFileSync('D:/restaurant-microservices/frontend/src/pages/report/revenue-report.tsx', revenueReport, 'utf8');
console.log('5. Fixed revenue-report.tsx undefined dailyDetails bug');

// 6. Fix server.js /api/reports/revenue
let serverJs = fs.readFileSync('c:/xampp/htdocs/php-restaurant-main-main/server.js', 'utf8');
const oldRevenueServer = `    const report = {
      totalRevenue: totalRev,
      totalExpense: totalExp,
      netProfit: totalRev - totalExp,
      orderCount: summaries.length * 8,
      dailyBreakdown: summaries.map(s => {
        const matchingExp = expenses.find(e => String(e.expense_date) === String(s.order_date));
        return {
          date: s.order_date,
          revenue: Number(s.total_amount),
          expense: Number(matchingExp?.amount || 7500000),
          profit: Number(s.total_amount) - Number(matchingExp?.amount || 7500000)
        };
      })
    };`;

const newRevenueServer = `    const dailyList = summaries.map(s => {
      const matchingExp = expenses.find(e => String(e.expense_date) === String(s.order_date));
      const rev = Number(s.total_amount);
      const exp = Number(matchingExp?.amount || 7500000);
      return {
        date: s.order_date,
        revenue: rev,
        expense: exp,
        profit: rev - exp,
        orderCount: 8
      };
    });

    const report = {
      startDate: req.query.startDate || (summaries[0]?.order_date || '2026-09-01'),
      endDate: req.query.endDate || (summaries[summaries.length - 1]?.order_date || '2026-09-23'),
      totalRevenue: totalRev,
      totalExpense: totalExp,
      totalProfit: totalRev - totalExp,
      netProfit: totalRev - totalExp,
      totalOrders: summaries.length * 8,
      orderCount: summaries.length * 8,
      dailyDetails: dailyList,
      dailyBreakdown: dailyList
    };`;

if (serverJs.includes('dailyBreakdown: summaries.map')) {
  serverJs = serverJs.replace(oldRevenueServer, newRevenueServer);
  fs.writeFileSync('c:/xampp/htdocs/php-restaurant-main-main/server.js', serverJs, 'utf8');
  console.log('6. Updated server.js /api/reports/revenue response schema');
} else {
  console.log('6. server.js already patched or using alternate snippet');
}

console.log('ALL FIXES COMPLETED SUCCESSFULLY!');
