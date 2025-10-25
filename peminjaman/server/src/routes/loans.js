import { Router } from 'express';
import { db, uid } from '../lib/db.js';
import { authRequired, roleRequired } from '../lib/auth.js';
import { sendStatusEmail } from '../lib/mail.js';

export const loansRouter = Router();

// List with filters: status, unit, date range, roomType
loansRouter.get('/', authRequired, (req,res)=>{
  const { status, unit, from, to, roomType } = req.query;
  let sql = 'SELECT * FROM loans WHERE 1=1';
  const params = [];
  if(status){ sql += ' AND status=?'; params.push(status); }
  if(unit){ sql += ' AND unit LIKE ?'; params.push('%'+unit+'%'); }
  if(roomType){ sql += ' AND roomType=?'; params.push(roomType); }
  if(from){ sql += ' AND startDate>=?'; params.push(from); }
  if(to){ sql += ' AND endDate<=?'; params.push(to); }
  sql += ' ORDER BY createdAt DESC';
  const rows = db.prepare(sql).all(...params);
  res.json(rows);
});

// Create
loansRouter.post('/', authRequired, (req,res)=>{
  const { borrowerName, identity, unit, roomType, facilityId, startDate, endDate, notes, items } = req.body;
  if(!borrowerName || !identity || !unit || !roomType || !facilityId || !startDate || !endDate){
    return res.status(400).json({error:'Invalid payload'});
  }
  
  // H-7 validation: minimal 7 hari sebelum acara
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const minDate = new Date(today);
  minDate.setDate(minDate.getDate() + 7);
  const requestedDate = new Date(startDate);
  if(requestedDate < minDate){
    return res.status(400).json({error:'Peminjaman minimal H-7 (7 hari) dari hari ini.'});
  }
  
  // Check overlap (same facility, date range intersect)
  const overlap = db.prepare('SELECT 1 FROM loans WHERE facilityId=? AND status!=\'rejected\' AND NOT( ? > endDate OR ? < startDate )')
    .get(facilityId, startDate, endDate);
  if(overlap) return res.status(409).json({error:'Ruangan sudah dipakai pada tanggal ini.'});

  const id = uid('ln'); const now = new Date().toISOString();
  db.prepare('INSERT INTO loans (id,borrowerName,identity,unit,roomType,facilityId,startDate,endDate,notes,status,createdAt) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
    .run(id, borrowerName, identity, unit, roomType, facilityId, startDate, endDate, notes||'', 'pending', now);
  if(Array.isArray(items)){
    const ins = db.prepare('INSERT INTO loan_facilities (id,loanId,item,quantity) VALUES (?,?,?,?)');
    items.forEach(it=> ins.run(uid('lnf'), id, it.item, it.quantity||0));
  }
  const row = db.prepare('SELECT * FROM loans WHERE id=?').get(id);
  res.status(201).json(row);
});

// Update status: approve/reject/done (admin, staff can approve/reject/done; viewer cannot)
loansRouter.patch('/:id/status', authRequired, roleRequired('admin','staff'), async (req,res)=>{
  const { id } = req.params; const { status, email } = req.body;
  if(!['approved','rejected','done'].includes(status)) return res.status(400).json({error:'Invalid status'});
  const now = new Date().toISOString();
  const info = db.prepare('UPDATE loans SET status=?, updatedAt=? WHERE id=?').run(status, now, id);
  if(info.changes===0) return res.status(404).json({error:'Not found'});
  const row = db.prepare('SELECT * FROM loans WHERE id=?').get(id);
  // notify via email if provided
  try{ if(email){ await sendStatusEmail({ to: email, name: row.borrowerName, facility: row.facilityId, start: row.startDate, end: row.endDate, status }); } }catch(e){ console.warn('Email error:', e.message); }
  res.json(row);
});

// Get loan by id
loansRouter.get('/:id', authRequired, (req,res)=>{
  const { id } = req.params;
  const loan = db.prepare('SELECT * FROM loans WHERE id=?').get(id);
  if(!loan) return res.status(404).json({error:'Not found'});
  const items = db.prepare('SELECT item,quantity FROM loan_facilities WHERE loanId=?').all(id);
  res.json({ ...loan, items });
});
