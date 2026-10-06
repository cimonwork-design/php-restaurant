const fs = require('fs');

// 1. Update table.api.ts to ensure safe non-undefined return
const tableApiPath = 'D:/restaurant-microservices/frontend/src/api/table.api.ts';
let tableApiContent = fs.readFileSync(tableApiPath, 'utf8');
tableApiContent = tableApiContent.replace(
  'getAll: async (): Promise<RestaurantTable[]> => {\n    const res = await api.get<ApiResponse<RestaurantTable[]>>(\'/tables\');\n    return res.data.data;\n  },',
  'getAll: async (): Promise<RestaurantTable[]> => {\n    const res = await api.get<ApiResponse<RestaurantTable[]>>(\'/tables\');\n    return res.data?.data ?? [];\n  },'
);
tableApiContent = tableApiContent.replace(
  'getReservations: async (tableId?: number): Promise<Reservation[]> => {\n    const res = await api.get<ApiResponse<Reservation[]>>(\'/reservations\', { params: { tableId } });\n    return res.data.data;\n  },',
  'getReservations: async (tableId?: number): Promise<Reservation[]> => {\n    const res = await api.get<ApiResponse<Reservation[]>>(\'/reservations\', { params: { tableId } });\n    return res.data?.data ?? [];\n  },'
);
fs.writeFileSync(tableApiPath, tableApiContent, 'utf8');
console.log('table.api.ts updated');

// 2. Update use-tables.ts
const useTablesPath = 'D:/restaurant-microservices/frontend/src/hooks/use-tables.ts';
let useTablesContent = fs.readFileSync(useTablesPath, 'utf8');
useTablesContent = useTablesContent.replace(
  'export const useTables = () => {\n  return useQuery({\n    queryKey: [\'tables\'],\n    queryFn: tableApi.getAll,\n  });\n};',
  'export const useTables = () => {\n  return useQuery({\n    queryKey: [\'tables\'],\n    queryFn: async () => {\n      const data = await tableApi.getAll();\n      return data ?? [];\n    },\n  });\n};'
);
fs.writeFileSync(useTablesPath, useTablesContent, 'utf8');
console.log('use-tables.ts updated');
