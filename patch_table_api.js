const fs = require('fs');

let content = fs.readFileSync('D:/restaurant-microservices/frontend/src/api/table.api.ts', 'utf8');

const oldCode = `  delete: async (id: number): Promise<void> => {
    await api.delete(\`/tables/\${id}\`);
  },`;

const newCode = `  delete: async (id: number): Promise<void> => {
    await api.delete(\`/tables/\${id}\`);
  },
  transferTable: async (sourceId: number, targetTableId: number, reason?: string): Promise<any> => {
    const res = await api.post<ApiResponse<any>>(\`/tables/\${sourceId}/transfer\`, { targetTableId, reason });
    return res.data.data;
  },
  mergeTables: async (primaryId: number, mergedTableIds: number[]): Promise<any> => {
    const res = await api.post<ApiResponse<any>>(\`/tables/\${primaryId}/merge\`, { mergedTableIds });
    return res.data.data;
  },`;

if (content.includes(oldCode)) {
  content = content.replace(oldCode, newCode);
  fs.writeFileSync('D:/restaurant-microservices/frontend/src/api/table.api.ts', content, 'utf8');
  console.log('Successfully updated table.api.ts with transferTable and mergeTables!');
} else {
  console.error('Could not locate oldCode in table.api.ts');
}
