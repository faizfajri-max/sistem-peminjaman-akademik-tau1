import Database from 'better-sqlite3';
import bcrypt from 'bcryptjs';

export const db = new Database('spfk.db');

export async function initDb(){
  db.pragma('journal_mode = WAL');
  db.exec(`
  CREATE TABLE IF NOT EXISTS users (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('admin','staff','viewer')),
    createdAt TEXT NOT NULL
  );
  CREATE TABLE IF NOT EXISTS facilities (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    type TEXT NOT NULL,
    capacity INTEGER,
    location TEXT,
    features TEXT,
    createdAt TEXT NOT NULL,
    updatedAt TEXT
  );
  CREATE TABLE IF NOT EXISTS loans (
    id TEXT PRIMARY KEY,
    borrowerName TEXT NOT NULL,
    identity TEXT NOT NULL,
    unit TEXT NOT NULL,
    borrowType TEXT DEFAULT 'room',
    roomType TEXT,
    facilityId TEXT NOT NULL,
    quantity INTEGER DEFAULT 1,
    startDate TEXT NOT NULL,
    endDate TEXT NOT NULL,
    notes TEXT,
    status TEXT NOT NULL CHECK(status IN ('pending','approved','rejected','done')),
    createdAt TEXT NOT NULL,
    updatedAt TEXT,
    FOREIGN KEY (facilityId) REFERENCES facilities(id)
  );
  CREATE TABLE IF NOT EXISTS loan_facilities (
    id TEXT PRIMARY KEY,
    loanId TEXT NOT NULL,
    item TEXT NOT NULL,
    quantity INTEGER NOT NULL,
    FOREIGN KEY (loanId) REFERENCES loans(id)
  );
  CREATE TABLE IF NOT EXISTS comments (
    id TEXT PRIMARY KEY,
    loanId TEXT NOT NULL,
    userId TEXT,
    userName TEXT NOT NULL,
    userRole TEXT,
    message TEXT NOT NULL,
    photoBase64 TEXT,
    createdAt TEXT NOT NULL,
    FOREIGN KEY (loanId) REFERENCES loans(id)
  );
  `);

  // seed admin/staff/viewer if not exists
  const userCount = db.prepare('SELECT COUNT(*) as c FROM users').get().c;
  if(userCount===0){
    const now = new Date().toISOString();
    const insert = db.prepare('INSERT INTO users (id,name,email,password,role,createdAt) VALUES (?,?,?,?,?,?)');
    insert.run('u_admin','Administrator','admin@kampus.ac.id', bcrypt.hashSync('admin123',10),'admin',now);
    insert.run('u_staff','Staf Teknis','staff@kampus.ac.id', bcrypt.hashSync('staff123',10),'staff',now);
    insert.run('u_viewer','Pak Farid','viewer@kampus.ac.id', bcrypt.hashSync('viewer123',10),'viewer',now);
  }

  // seed some facilities if not exists
  const facCount = db.prepare('SELECT COUNT(*) as c FROM facilities').get().c;
  if(facCount===0){
    const now = new Date().toISOString();
    const insert = db.prepare('INSERT INTO facilities (id,name,type,capacity,location,features,createdAt) VALUES (?,?,?,?,?,?,?)');
    // Ruangan Kelas (14 ruangan)
    insert.run('kelas-203','Kelas 203','Kelas',40,'Gedung Utama Lantai 2', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-204','Kelas 204','Kelas',40,'Gedung Utama Lantai 2', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-205','Kelas 205','Kelas',40,'Gedung Utama Lantai 2', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-206','Kelas 206','Kelas',40,'Gedung Utama Lantai 2', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-304','Kelas 304','Kelas',40,'Gedung Utama Lantai 3', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-306','Kelas 306','Kelas',40,'Gedung Utama Lantai 3', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-308','Kelas 308','Kelas',40,'Gedung Utama Lantai 3', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-401','Kelas 401','Kelas',40,'Gedung Utama Lantai 4', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-402','Kelas 402','Kelas',40,'Gedung Utama Lantai 4', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-403','Kelas 403','Kelas',40,'Gedung Utama Lantai 4', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-404','Kelas 404','Kelas',40,'Gedung Utama Lantai 4', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-405','Kelas 405','Kelas',40,'Gedung Utama Lantai 4', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-406','Kelas 406','Kelas',40,'Gedung Utama Lantai 4', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    insert.run('kelas-408','Kelas 408','Kelas',40,'Gedung Utama Lantai 4', JSON.stringify(['Proyektor','AC','Whiteboard']), now);
    // Fasilitas Lainnya (tetap ada)
    insert.run('ballroom','Ballroom Kampus','Ballroom',400,'Gedung Serbaguna', JSON.stringify(['Panggung','Sound System','LED']), now);
    insert.run('rans','Rans Room Studio','Rans Room',20,'Gedung Media', JSON.stringify(['Podcast Mic','Green Screen']), now);
    insert.run('bumr','Ruang BUMR','BUMR',25,'Gedung Administrasi', JSON.stringify(['AC','WiFi']), now);
    insert.run('lppm','Ruang LPPM Rapat','LPPM',18,'Gedung Riset', JSON.stringify(['TV 55"','VC Camera']), now);
    insert.run('perpus','Ruang Diskusi Perpustakaan','Perpustakaan',12,'Perpustakaan', JSON.stringify(['AC','Whiteboard']), now);
    insert.run('podcast','Studio Podcast','Ruang Podcast',6,'Gedung Media', JSON.stringify(['Mic','Mixer','Akustik']), now);
    insert.run('kamera-1','Kamera DSLR A','Peralatan',1,'Unit Multimedia', JSON.stringify(['Body + Lensa 24-70']), now);
    insert.run('proyektor-1','Proyektor Portable','Peralatan',1,'Gudang Peralatan', JSON.stringify(['HDMI','Remote']), now);
  }
}

export function uid(p='id'){
  return `${p}_${Math.random().toString(36).slice(2,8)}${Date.now().toString(36).slice(-3)}`;
}
