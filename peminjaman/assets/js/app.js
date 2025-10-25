// Sistem Peminjaman Fasilitas Kampus - Core JS
// Utilities, state, auth, nav, storage helpers

(function(){
  const STORAGE_KEYS = {
    BOOKINGS: 'spfk_bookings',
    USERS: 'spfk_users',
    AUTH: 'spfk_auth',
    FACILITIES: 'spfk_facilities',
    LOANS: 'spfk_loans',
    LOAN_FAC: 'spfk_loan_facilities'
  };

  // Seed facilities
  const defaultFacilities = [
    // Ruangan Kelas (14 ruangan)
    { id:'kelas-203', type:'Kelas', name:'Kelas 203', capacity:40, location:'Gedung Utama Lantai 2', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-204', type:'Kelas', name:'Kelas 204', capacity:40, location:'Gedung Utama Lantai 2', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-205', type:'Kelas', name:'Kelas 205', capacity:40, location:'Gedung Utama Lantai 2', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-206', type:'Kelas', name:'Kelas 206', capacity:40, location:'Gedung Utama Lantai 2', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-304', type:'Kelas', name:'Kelas 304', capacity:40, location:'Gedung Utama Lantai 3', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-306', type:'Kelas', name:'Kelas 306', capacity:40, location:'Gedung Utama Lantai 3', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-308', type:'Kelas', name:'Kelas 308', capacity:40, location:'Gedung Utama Lantai 3', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-401', type:'Kelas', name:'Kelas 401', capacity:40, location:'Gedung Utama Lantai 4', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-402', type:'Kelas', name:'Kelas 402', capacity:40, location:'Gedung Utama Lantai 4', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-403', type:'Kelas', name:'Kelas 403', capacity:40, location:'Gedung Utama Lantai 4', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-404', type:'Kelas', name:'Kelas 404', capacity:40, location:'Gedung Utama Lantai 4', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-405', type:'Kelas', name:'Kelas 405', capacity:40, location:'Gedung Utama Lantai 4', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-406', type:'Kelas', name:'Kelas 406', capacity:40, location:'Gedung Utama Lantai 4', features:['Proyektor','AC','Whiteboard'] },
    { id:'kelas-408', type:'Kelas', name:'Kelas 408', capacity:40, location:'Gedung Utama Lantai 4', features:['Proyektor','AC','Whiteboard'] },
    // Fasilitas Lainnya (tetap ada)
    { id:'ballroom', type:'Ballroom', name:'Ballroom Kampus', capacity:400, location:'Gedung Serbaguna', features:['Panggung','Sound System','LED'] },
    { id:'rans', type:'Rans Room', name:'Rans Room Studio', capacity:20, location:'Gedung Media', features:['Podcast Mic','Green Screen'] },
    { id:'bumr', type:'BUMR', name:'Ruang BUMR', capacity:25, location:'Gedung Administrasi', features:['AC','WiFi'] },
    { id:'lppm', type:'LPPM', name:'Ruang LPPM Rapat', capacity:18, location:'Gedung Riset', features:['TV 55"','VC Camera'] },
    { id:'perpus', type:'Perpustakaan', name:'Ruang Diskusi Perpustakaan', capacity:12, location:'Perpustakaan', features:['AC','Whiteboard'] },
    { id:'podcast', type:'Ruang Podcast', name:'Studio Podcast', capacity:6, location:'Gedung Media', features:['Mic','Mixer','Akustik'] },
    { id:'kamera-1', type:'Peralatan', name:'Kamera DSLR A', capacity:1, location:'Unit Multimedia', features:['Body + Lensa 24-70'] },
    { id:'proyektor-1', type:'Peralatan', name:'Proyektor Portable', capacity:1, location:'Gudang Peralatan', features:['HDMI','Remote'] },
  ];

  function getJSON(key, fallback){
    try{ return JSON.parse(localStorage.getItem(key)) ?? fallback }catch{ return fallback }
  }
  function setJSON(key, value){ localStorage.setItem(key, JSON.stringify(value)) }

  // Seed default data if missing
  if(!localStorage.getItem(STORAGE_KEYS.FACILITIES)) setJSON(STORAGE_KEYS.FACILITIES, defaultFacilities);
  if(!localStorage.getItem(STORAGE_KEYS.BOOKINGS)) setJSON(STORAGE_KEYS.BOOKINGS, []);
  if(!localStorage.getItem(STORAGE_KEYS.USERS)) {
    // Seed default users with different roles
    setJSON(STORAGE_KEYS.USERS, [
      {id:'admin1', name:'Administrator', email:'admin@admin.tau.ac.id', role:'admin', password:'admin123'},
      {id:'staff1', name:'Staff Fasilitas', email:'staff@staff.tau.ac.id', role:'staff', password:'staff123'}
    ]);
  }
  if(!localStorage.getItem(STORAGE_KEYS.LOANS)) setJSON(STORAGE_KEYS.LOANS, []);
  if(!localStorage.getItem(STORAGE_KEYS.LOAN_FAC)) setJSON(STORAGE_KEYS.LOAN_FAC, []);

  // Auth helpers
  window.SPFK = {
    getFacilities: () => getJSON(STORAGE_KEYS.FACILITIES, []),
    setFacilities: (list) => setJSON(STORAGE_KEYS.FACILITIES, list),
    getBookings: () => getJSON(STORAGE_KEYS.BOOKINGS, []),
    setBookings: (list) => setJSON(STORAGE_KEYS.BOOKINGS, list),
    addBooking: (b) => { const list = getJSON(STORAGE_KEYS.BOOKINGS, []); list.push(b); setJSON(STORAGE_KEYS.BOOKINGS, list); return b; },
    // Loans (main table)
    getLoans: () => getJSON(STORAGE_KEYS.LOANS, []),
    setLoans: (list) => setJSON(STORAGE_KEYS.LOANS, list),
    addLoan: (loan) => { const list = getJSON(STORAGE_KEYS.LOANS, []); list.push(loan); setJSON(STORAGE_KEYS.LOANS, list); return loan; },
    // Loan Facilities (child table)
    getLoanFacilities: () => getJSON(STORAGE_KEYS.LOAN_FAC, []),
    addLoanFacilities: (rows) => { const list = getJSON(STORAGE_KEYS.LOAN_FAC, []); rows.forEach(r=>list.push(r)); setJSON(STORAGE_KEYS.LOAN_FAC, list); return rows; },
    // Comments (for return confirmation)
    getComments: (loanId) => {
      const key = 'spfk_comments_' + loanId;
      return getJSON(key, []);
    },
    addComment: (loanId, comment) => {
      const key = 'spfk_comments_' + loanId;
      const list = getJSON(key, []);
      list.push(comment);
      setJSON(key, list);
      return comment;
    },
    // Auth with backward compatibility across keys
    getAuth: () => {
      // Prefer unified key if present
      const unified = getJSON(STORAGE_KEYS.AUTH, null);
      if (unified && unified.email) return unified;
      // Fallback to keys used by login/auth pages
      try {
        const userStr = localStorage.getItem('spfk_auth_user');
        if (userStr) {
          const u = JSON.parse(userStr);
          if (u && u.email) return u;
        }
      } catch {}
      try {
        const apiUserStr = localStorage.getItem('spfk_api_user');
        if (apiUserStr) {
          const u = JSON.parse(apiUserStr);
          if (u && u.email) return u;
        }
      } catch {}
      return null;
    },
    setAuth: (auth) => {
      setJSON(STORAGE_KEYS.AUTH, auth);
      try { localStorage.setItem('spfk_auth_user', JSON.stringify(auth)); } catch {}
      try { localStorage.setItem('spfk_api_user', JSON.stringify(auth)); } catch {}
    },
    logout: () => {
      localStorage.removeItem(STORAGE_KEYS.AUTH);
      localStorage.removeItem('spfk_auth_user');
      localStorage.removeItem('spfk_api_user');
      localStorage.removeItem('spfk_auth_token');
      localStorage.removeItem('spfk_api_token');
    },
    users: {
      register: (name, email, password) => {
        const users = getJSON(STORAGE_KEYS.USERS, []);
        if(users.find(u=>u.email===email)) throw new Error('Email sudah terdaftar');
        const user = { id: 'u_'+Date.now(), name, email, role:'user', password };
        users.push(user); setJSON(STORAGE_KEYS.USERS, users); return user;
      },
      login: (email, password) => {
        const users = getJSON(STORAGE_KEYS.USERS, []);
        const user = users.find(u=>u.email===email && u.password===password);
        if(!user) throw new Error('Email atau password salah');
        const auth = { id:user.id, name:user.name, email:user.email, role:user.role };
        setJSON(STORAGE_KEYS.AUTH, auth); return auth;
      }
    },
    utils: {
      fmtDate: (d)=> new Date(d).toLocaleDateString('id-ID',{weekday:'short', day:'2-digit', month:'short', year:'numeric'}),
      fmtTime: (d)=> new Date(d).toLocaleTimeString('id-ID',{hour:'2-digit', minute:'2-digit'}),
      uid: (p='id')=> `${p}_${Math.random().toString(36).slice(2,8)}${Date.now().toString(36).slice(-3)}`
    }
  };

  // Sidebar toggle and active link
  function initNav(){
    const sidebar = document.querySelector('.sidebar');
    const burger = document.querySelector('.hamburger');
    burger?.addEventListener('click', ()=> sidebar?.classList.toggle('open'));

    const links = document.querySelectorAll('.nav a');
    links.forEach(a=>{
      const isActive = a.getAttribute('href') && location.pathname.toLowerCase().endsWith(a.getAttribute('href').toLowerCase());
      if(isActive) a.classList.add('active');
    });

    // Get current user (prefer API user)
    const currentUser = SPFK.getAuth();
    
    // Hide/Show Admin Panel link based on role
    const adminLink = document.querySelector('.nav a[href="admin.html"]');
    if(adminLink) {
      if(currentUser && (currentUser.role === 'admin' || currentUser.role === 'staff')) {
        adminLink.style.display = ''; // Show for admin/staff
      } else {
        adminLink.style.display = 'none'; // Hide for others
      }
    }
    
    // Render user in topbar if auth exists
    const userEl = document.querySelector('#topbar-user');
    if(userEl){
      if(currentUser){ 
        let roleText = '';
        if(currentUser.role === 'admin') roleText = ' (Admin)';
        else if(currentUser.role === 'staff') roleText = ' (Staff)';
        userEl.textContent = currentUser.name + roleText; 
      } else { 
        userEl.textContent = 'Tamu'; 
      }
    }
  }

  // Protect admin page - Allow admin and staff
  function protectAdmin(){
    if(document.body.dataset.page !== 'admin') return;
    
    // Check localStorage auth first
    const currentUser = SPFK.getAuth();
    
    if(!currentUser || (currentUser.role !== 'admin' && currentUser.role !== 'staff')){
      alert('Akses ditolak. Anda harus login sebagai Admin atau Staff untuk mengakses halaman ini.');
      location.href = 'auth.html';
    }
  }

  document.addEventListener('DOMContentLoaded', ()=>{
    initNav();
    protectAdmin();
  });
})();

// Buka Browser Console (F12) dan jalankan:
// localStorage.removeItem('spfk_facilities');
// location.reload();
