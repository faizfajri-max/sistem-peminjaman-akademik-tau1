<!-- Quick Update Instructions -->
Untuk update semua halaman dengan tombol Login/Register yang baru:

1. Ganti bagian topbar actions menjadi:
```html
<div class="actions" id="authActions">
  <a class="btn btn-primary" href="auth.html" style="margin-right: 8px;">🔑 Login</a>
  <a class="btn btn-ghost" href="auth.html?tab=register">📝 Register</a>
</div>
```

2. Tambahkan script sebelum </body>:
```html
<script src="assets/js/auth-ui.js"></script>
```

File yang perlu diupdate:
- ✅ index.html (DONE)
- ✅ borrow.html (DONE)
- ⏳ facilities.html
- ⏳ schedule.html
- ⏳ confirmation.html
- ⏳ admin.html
