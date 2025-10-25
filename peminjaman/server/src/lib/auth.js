import jwt from 'jsonwebtoken';
import bcrypt from 'bcryptjs';
import { db } from './db.js';

export function sign(user){
  return jwt.sign({ id:user.id, name:user.name, email:user.email, role:user.role }, process.env.JWT_SECRET || 'dev', { expiresIn:'7d' });
}

export function authRequired(req,res,next){
  const h = req.headers.authorization||'';
  const token = h.startsWith('Bearer ')?h.slice(7):null;
  if(!token) return res.status(401).json({error:'Unauthorized'});
  try{
    const payload = jwt.verify(token, process.env.JWT_SECRET || 'dev');
    req.user = payload; next();
  }catch{
    return res.status(401).json({error:'Unauthorized'});
  }
}

export function roleRequired(...roles){
  return function(req,res,next){
    if(!req.user) return res.status(401).json({error:'Unauthorized'});
    if(!roles.includes(req.user.role)) return res.status(403).json({error:'Forbidden'});
    next();
  }
}

export function findUserByEmail(email){
  return db.prepare('SELECT * FROM users WHERE email=?').get(email);
}

export function createUser({name,email,password,role}){
  const now = new Date().toISOString();
  const id = 'u_'+Math.random().toString(36).slice(2,10);
  const hash = bcrypt.hashSync(password,10);
  db.prepare('INSERT INTO users (id,name,email,password,role,createdAt) VALUES (?,?,?,?,?,?)').run(id,name,email,hash,role,now);
  return db.prepare('SELECT id,name,email,role FROM users WHERE id=?').get(id);
}

export function verifyPassword(raw, hash){
  return bcrypt.compareSync(raw, hash);
}
