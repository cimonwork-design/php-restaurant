const fs = require('fs');

let content = fs.readFileSync('D:/restaurant-microservices/frontend/src/hooks/use-tables.ts', 'utf8');

const hookAddition = `export const useTransferTable = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: ({ sourceId, targetTableId, reason }: { sourceId: number; targetTableId: number; reason?: string }) =>
      tableApi.transferTable(sourceId, targetTableId, reason),
    onSuccess: (data) => {
      qc.invalidateQueries({ queryKey: ['tables'] });
      qc.invalidateQueries({ queryKey: ['orders'] });
      toast.success(data?.message || 'Chuyển bàn và chuyển toàn bộ đơn hàng thành công!');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi khi chuyển bàn'),
  });
};

export const useMergeTables = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: ({ primaryId, mergedTableIds }: { primaryId: number; mergedTableIds: number[] }) =>
      tableApi.mergeTables(primaryId, mergedTableIds),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tables'] });
      qc.invalidateQueries({ queryKey: ['orders'] });
      toast.success('Ghép bàn và gộp toàn bộ đơn hàng thành công!');
    },
    onError: (err: any) => toast.error(err.response?.data?.message || 'Lỗi khi ghép bàn'),
  });
};

`;

content = hookAddition + content;
fs.writeFileSync('D:/restaurant-microservices/frontend/src/hooks/use-tables.ts', content, 'utf8');
console.log('Successfully updated use-tables.ts with useTransferTable and useMergeTables!');
