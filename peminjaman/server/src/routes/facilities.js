import { Router } from 'express';
import { db, uid } from '../lib/db.js';
import { authRequired, roleRequired } from '../lib/auth.js';

export const facilitiesRouter = Router();

facilitiesRouter.get('/', (req,res)=>{
  const rows = db.prepare('SELECT * FROM facilities ORDER BY name').all();
  rows.forEach(r=> r.features = r.features ? JSON.parse(r.features) : []);
  res.json(rows);
});

facilitiesRouter.post('/', authRequired, roleRequired('admin'), (req,res)=>{
  const { name, type, capacity, location, features } = req.body;
  if(!name || !type) return res.status(400).json({error:'Invalid payload'});
  const id = uid('fac'); const now = new Date().toISOString();
  db.prepare('INSERT INTO facilities (id,name,type,capacity,location,features,createdAt) VALUES (?,?,?,?,?,?,?)')
    .run(id,name,type,capacity||0,location||'', JSON.stringify(features||[]), now);
  const row = db.prepare('SELECT * FROM facilities WHERE id=?').get(id);
  row.features = row.features ? JSON.parse(row.features) : [];
  res.status(201).json(row);
});

facilitiesRouter.put('/:id', authRequired, roleRequired('admin'), (req,res)=>{
  const { id } = req.params;
  const { name, type, capacity, location, features } = req.body;
  const now = new Date().toISOString();
  const info = db.prepare('UPDATE facilities SET name=?, type=?, capacity=?, location=?, features=?, updatedAt=? WHERE id=?')
    .run(name,type,capacity||0,location||'', JSON.stringify(features||[]), now, id);
  if(info.changes===0) return res.status(404).json({error:'Not found'});
  const row = db.prepare('SELECT * FROM facilities WHERE id=?').get(id);
  row.features = row.features ? JSON.parse(row.features) : [];
  res.json(row);
});

facilitiesRouter.delete('/:id', authRequired, roleRequired('admin'), (req,res)=>{
  const { id } = req.params;
  const info = db.prepare('DELETE FROM facilities WHERE id=?').run(id);
  if(info.changes===0) return res.status(404).json({error:'Not found'});
  res.json({ ok:true });
});
