const fs = require('fs');

let content = fs.readFileSync('D:/restaurant-microservices/frontend/src/pages/menu/menu-list.tsx', 'utf8');

content = content.replace(
  `<EmptyState
              icon={BookOpen}
              title="Không tìm thấy món ăn"
              description="Thử thay đổi bộ lọc tìm kiếm hoặc thêm món mới vào thực đơn."
              action={{ label: 'Thêm món ngay', onClick: handleOpenCreateDish }}
            />`,
  `<EmptyState
              icon={<BookOpen className="h-10 w-10 text-muted-foreground/60" />}
              title="Không tìm thấy món ăn"
              description="Thử thay đổi bộ lọc tìm kiếm hoặc thêm món mới vào thực đơn."
              actionText="Thêm món ngay"
              onAction={handleOpenCreateDish}
            />`
);

content = content.replace(
  `confirmText="Xóa Món"
        cancelText="Giữ Lại"
        variant="destructive"`,
  `confirmText="Xóa Món"
        cancelText="Giữ Lại"
        isDanger={true}`
);

fs.writeFileSync('D:/restaurant-microservices/frontend/src/pages/menu/menu-list.tsx', content, 'utf8');
console.log('Fixed EmptyState and ConfirmDialog props in menu-list.tsx');
