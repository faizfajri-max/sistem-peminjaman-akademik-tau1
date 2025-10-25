/**
 * Auth Guard - Proteksi halaman yang butuh login
 * Include script ini di halaman yang perlu proteksi
 */

(function() {
  'use strict';
  
  // Check if user is logged in
  function checkAuth() {
    const authUser = JSON.parse(localStorage.getItem('spfk_auth_user') || 'null');
    
    if (!authUser || !authUser.email) {
      // User belum login
      const currentPage = window.location.pathname;
      const currentPath = currentPage.split('/').pop() || 'index.html';
      
      // Jika di index.html, redirect ke welcome page
      if (currentPath === 'index.html' || currentPath === '') {
        window.location.href = 'welcome.html';
        return false;
      }
      
      // Untuk halaman lain, redirect ke login dengan return URL
      const returnUrl = encodeURIComponent(currentPage + window.location.search);
      alert('Anda harus login terlebih dahulu untuk mengakses halaman ini.');
      window.location.href = `login.html?return=${returnUrl}`;
      
      return false;
    }
    
    return true;
  }
  
  // Check admin role
  function checkAdmin() {
    const authUser = JSON.parse(localStorage.getItem('spfk_auth_user') || 'null');
    
    if (!authUser || authUser.role !== 'admin') {
      alert('Halaman ini hanya untuk Admin.');
      window.location.href = 'index.html';
      return false;
    }
    
    return true;
  }
  
  // Export functions
  window.AuthGuard = {
    requireAuth: checkAuth,
    requireAdmin: checkAdmin
  };
  
  // Auto-check jika halaman memiliki attribute data-require-auth
  if (document.body.getAttribute('data-require-auth') === 'true') {
    checkAuth();
  }
  
  // Auto-check jika halaman memiliki attribute data-require-admin
  if (document.body.getAttribute('data-require-admin') === 'true') {
    checkAdmin();
  }
  
})();
