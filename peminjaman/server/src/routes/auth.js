import { Router } from 'express';
import { db } from '../lib/db.js';
import { authRequired } from '../lib/auth.js';
import { createUser, findUserByEmail, sign, verifyPassword } from '../lib/auth.js';

export const authRouter = Router();

authRouter.post('/register', (req,res)=>{
  const { name, email, password, role } = req.body;
  if(!name || !email || !password) return res.status(400).json({error:'Invalid payload'});
  const r = role || 'viewer';
  if(db.prepare('SELECT 1 FROM users WHERE email=?').get(email)) return res.status(409).json({error:'Email sudah terdaftar'});
  const u = createUser({ name, email, password, role:r });
  const token = sign(u);
  res.json({ user:u, token });
});

authRouter.post('/login', (req,res)=>{
  const { email, password } = req.body;
  const u = findUserByEmail(email);
  if(!u || !verifyPassword(password, u.password)) return res.status(401).json({error:'Email atau password salah'});
  const user = { id:u.id, name:u.name, email:u.email, role:u.role };
  const token = sign(user);
  res.json({ user, token });
});

authRouter.get('/me', authRequired, (req,res)=>{
  res.json({ user: req.user });
});
