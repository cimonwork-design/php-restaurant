const fs = require('fs');

const path = 'D:/restaurant-microservices/frontend/src/pages/menu/menu-list.tsx';
let code = fs.readFileSync(path, 'utf8');

const target = `          <div>
            <label className="text-xs font-semibold text-muted-foreground mb-1 block">Danh mục</label>
            <Input value={itemCategory} onChange={(e) => setItemCategory(e.target.value)} />
          </div>`;

const replacement = `          <div>
            <label className="text-xs font-semibold text-muted-foreground mb-1 block">
              Danh mục món ăn <span className="text-destructive">*</span>
            </label>
            <Select value={itemCategory} onChange={(e) => setItemCategory(e.target.value)} required>
              <option value="Món chính">Món chính</option>
              <option value="Khai vị">Khai vị</option>
              <option value="Hải sản cao cấp">Hải sản cao cấp</option>
              <option value="Đồ uống & Rượu">Đồ uống & Rượu</option>
              <option value="Tráng miệng">Tráng miệng</option>
              <option value="Món ăn kèm">Món ăn kèm</option>
            </Select>
          </div>`;

code = code.replace(target, replacement);
fs.writeFileSync(path, code, 'utf8');
console.log('Updated menu-list.tsx category to Select dropdown');
