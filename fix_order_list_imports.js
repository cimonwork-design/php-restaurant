const fs = require('fs');

const path = 'D:/restaurant-microservices/frontend/src/pages/order/order-list.tsx';
let code = fs.readFileSync(path, 'utf8');

code = code.replace(
  "import { Plus, Check, CreditCard, XCircle, Printer, Search, Eye } from 'lucide-react';",
  "import { Plus, Check, CreditCard, XCircle, Printer, Search, Eye, Split } from 'lucide-react';\nimport { toast } from 'sonner';"
);

fs.writeFileSync(path, code, 'utf8');
console.log('Fixed imports in order-list.tsx');
