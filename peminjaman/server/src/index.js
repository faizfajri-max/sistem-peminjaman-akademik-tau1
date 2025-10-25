import 'dotenv/config';
import express from 'express';
import cors from 'cors';

import { db, initDb } from './lib/db.js';
import { authRouter } from './routes/auth.js';
import { loansRouter } from './routes/loans.js';
import { facilitiesRouter } from './routes/facilities.js';
import { reportsRouter } from './routes/reports.js';
import { commentsRouter } from './routes/comments.js';

const app = express();
app.use(cors({ origin: process.env.CORS_ORIGIN?.split(',') || '*', credentials: false }));
app.use(express.json({ limit: '10mb' }));

await initDb();

app.get('/api/health', (req,res)=> res.json({ ok: true }));
app.use('/api/auth', authRouter);
app.use('/api/loans', loansRouter);
app.use('/api/facilities', facilitiesRouter);
app.use('/api/reports', reportsRouter);
app.use('/api/comments', commentsRouter);

const port = process.env.PORT || 4000;
app.listen(port, ()=> console.log(`[SPFK] API listening on http://localhost:${port}`));
