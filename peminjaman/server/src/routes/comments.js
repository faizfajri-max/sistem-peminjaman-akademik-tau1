import { Router } from 'express';
import { db, uid } from '../lib/db.js';
import { authRequired } from '../lib/auth.js';

export const commentsRouter = Router();

// Get comments for a loan
commentsRouter.get('/:loanId', (req, res) => {
  const { loanId } = req.params;
  const comments = db.prepare('SELECT * FROM comments WHERE loanId=? ORDER BY createdAt ASC').all(loanId);
  res.json(comments);
});

// Post a comment (with optional photo)
commentsRouter.post('/:loanId', authRequired, (req, res) => {
  const { loanId } = req.params;
  const { message, photoBase64 } = req.body;
  if (!message) return res.status(400).json({ error: 'Message required' });
  
  const id = uid('cmt');
  const now = new Date().toISOString();
  const { name, role } = req.user;
  
  db.prepare('INSERT INTO comments (id,loanId,userId,userName,userRole,message,photoBase64,createdAt) VALUES (?,?,?,?,?,?,?,?)')
    .run(id, loanId, req.user.id, name, role, message, photoBase64 || null, now);
  
  const comment = db.prepare('SELECT * FROM comments WHERE id=?').get(id);
  res.status(201).json(comment);
});

// Mark loan items as returned complete (admin/staff)
commentsRouter.patch('/:loanId/mark-returned', authRequired, (req, res) => {
  const { loanId } = req.params;
  const { role } = req.user;
  if (role !== 'admin' && role !== 'staff') {
    return res.status(403).json({ error: 'Forbidden' });
  }
  
  const now = new Date().toISOString();
  const info = db.prepare('UPDATE loans SET status=?, updatedAt=? WHERE id=?').run('done', now, loanId);
  if (info.changes === 0) return res.status(404).json({ error: 'Loan not found' });
  
  const loan = db.prepare('SELECT * FROM loans WHERE id=?').get(loanId);
  res.json(loan);
});
