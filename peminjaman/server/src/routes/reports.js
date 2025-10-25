import { Router } from 'express';
import { db } from '../lib/db.js';
import { authRequired, roleRequired } from '../lib/auth.js';

export const reportsRouter = Router();

reportsRouter.get('/summary', authRequired, roleRequired('admin','staff'), (req,res)=>{
  const { unit, roomType, from, to } = req.query;
  let sql = 'SELECT status, COUNT(*) as count FROM loans WHERE 1=1';
  const params = [];
  if(unit){ sql += ' AND unit LIKE ?'; params.push('%'+unit+'%'); }
  if(roomType){ sql += ' AND roomType=?'; params.push(roomType); }
  if(from){ sql += ' AND startDate>=?'; params.push(from); }
  if(to){ sql += ' AND endDate<=?'; params.push(to); }
  sql += ' GROUP BY status';
  const rows = db.prepare(sql).all(...params);
  const out = { pending:0, approved:0, rejected:0, done:0 };
  rows.forEach(r=> out[r.status] = r.count);
  res.json(out);
});
