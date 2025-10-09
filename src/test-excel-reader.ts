import * as XLSX from 'xlsx';

// This is a temporary test file to debug Excel content
export async function testExcelRead() {
  try {
    const response = await fetch('/src/assets/temp_test.xlsx');
    const arrayBuffer = await response.arrayBuffer();
    const workbook = XLSX.read(arrayBuffer);
    const worksheet = workbook.Sheets[workbook.SheetNames[0]];
    const jsonData = XLSX.utils.sheet_to_json(worksheet);
    
    console.log('=== EXCEL TARTALOM ===');
    console.log('Összes sor:', jsonData.length);
    console.log('Adatok:', jsonData);
    
    return jsonData;
  } catch (error) {
    console.error('Hiba az Excel olvasása során:', error);
    return null;
  }
}

// Automatically run on import
if (typeof window !== 'undefined') {
  testExcelRead();
}
