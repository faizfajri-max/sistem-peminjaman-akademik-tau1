import nodemailer from 'nodemailer';

let transporter = null;

export function getTransport(){
  if(transporter) return transporter;
  const { SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS } = process.env;
  if(SMTP_HOST && SMTP_USER && SMTP_PASS){
    transporter = nodemailer.createTransport({ host: SMTP_HOST, port: Number(SMTP_PORT||587), secure:false, auth:{ user:SMTP_USER, pass:SMTP_PASS } });
  }else{
    // Fallback: console logger transport
    transporter = { sendMail: async (opts)=>{ console.log('[MAIL] to:', opts.to, 'subject:', opts.subject); return { messageId: 'console' }; } };
  }
  return transporter;
}

export async function sendStatusEmail({ to, name, facility, start, end, status }){
  const trans = getTransport();
  const from = process.env.SMTP_FROM || 'no-reply@kampus.ac.id';
  const subject = `Status Peminjaman: ${status.toUpperCase()} — ${facility}`;
  const text = `Halo ${name},\n\nPengajuan peminjaman Anda untuk ${facility} pada ${new Date(start).toLocaleString('id-ID')} - ${new Date(end).toLocaleString('id-ID')} kini berstatus: ${status}.\n\nTerima kasih.`;
  return trans.sendMail({ from, to, subject, text });
}
