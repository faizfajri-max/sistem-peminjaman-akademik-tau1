import Database from 'better-sqlite3';

const db = new Database('spfk.db');

console.log('🔄 Running migration: Add borrowType and quantity to loans table...');

try {
  // Check if columns already exist
  const tableInfo = db.pragma('table_info(loans)');
  const columns = tableInfo.map(col => col.name);
  
  if (!columns.includes('borrowType')) {
    console.log('  ➕ Adding column: borrowType');
    db.exec(`ALTER TABLE loans ADD COLUMN borrowType TEXT DEFAULT 'room'`);
    // Update existing records to 'room'
    db.exec(`UPDATE loans SET borrowType = 'room' WHERE borrowType IS NULL`);
  } else {
    console.log('  ✓ Column borrowType already exists');
  }
  
  if (!columns.includes('quantity')) {
    console.log('  ➕ Adding column: quantity');
    db.exec(`ALTER TABLE loans ADD COLUMN quantity INTEGER DEFAULT 1`);
    // Update existing records to 1
    db.exec(`UPDATE loans SET quantity = 1 WHERE quantity IS NULL`);
  } else {
    console.log('  ✓ Column quantity already exists');
  }
  
  // Make roomType nullable (if it's not already)
  console.log('  ℹ️ Note: roomType is now nullable (optional for equipment loans)');
  
  console.log('✅ Migration completed successfully!');
  console.log('\nTable structure:');
  db.pragma('table_info(loans)').forEach(col => {
    console.log(`  - ${col.name}: ${col.type}${col.notnull ? ' NOT NULL' : ''}${col.dflt_value ? ` DEFAULT ${col.dflt_value}` : ''}`);
  });
  
} catch (error) {
  console.error('❌ Migration failed:', error.message);
  process.exit(1);
}

db.close();
console.log('\n🎉 Database migration completed. You can now use borrowType and quantity!');
