/**
 * Auth UI Handler - Update topbar dengan status login
 * Include script ini di semua halaman setelah app.js
 */

(function() {
  'use strict';
  
  // Update auth buttons pada topbar
  function updateAuthUI() {
    const authActions = document.getElementById('authActions');
    if (!authActions) return;
    
    // Use unified auth getter to support multiple storages
    const authUser = (function(){
      try { return SPFK.getAuth(); } catch { return JSON.parse(localStorage.getItem('spfk_auth_user') || 'null'); }
    })();
    
    if (authUser && authUser.email) {
      // User sudah login
      const userName = authUser.name || authUser.email.split('@')[0];
  const isElevated = authUser.role === 'admin' || authUser.role === 'staff';
      
      authActions.innerHTML = `
        <span class="badge" style="margin-right: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 6px 12px; border-radius: 20px; font-size: 13px;">
          👤 <strong>${userName}</strong>
        </span>
  ${isElevated ? '<a class="btn btn-primary" href="admin.html" style="margin-right: 8px;">🛠️ Admin Panel</a>' : ''}
        <button class="btn btn-ghost" onclick="window.handleLogout()" style="border: 1px solid #ddd;">
          🚪 Logout
        </button>
      `;
    } else {
      // User belum login
      authActions.innerHTML = `
        <a class="btn btn-primary" href="login.html" style="margin-right: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
          🔑 Login
        </a>
        <a class="btn btn-ghost" href="register.html" style="border: 1px solid #667eea; color: #667eea;">
          📝 Register
        </a>
      `;
    }
  }
  
  // Logout handler
  window.handleLogout = function() {
    if (confirm('Yakin ingin logout?')) {
      localStorage.removeItem('spfk_auth_user');
      localStorage.removeItem('spfk_auth_token');
      
      // Redirect ke homepage atau reload
      if (window.location.pathname.includes('admin.html')) {
        window.location.href = 'index.html';
      } else {
        window.location.reload();
      }
    }
  };

  // --- Admin Sidebar Menu Logic ---
  function updateAdminSidebarMenu() {
    const authUser = SPFK.getAuth();
    const adminLinks = document.querySelectorAll('a[href="admin.html"]');
    
    adminLinks.forEach(link => {
      // Check if the link is in the sidebar, not the topbar
      if (link.closest('.sidebar')) {
        if (authUser && (authUser.role === 'admin' || authUser.role === 'staff')) {
          link.style.display = 'flex';
        } else {
          link.style.display = 'none';
        }
      }
    });
  }

  // Panggil saat DOM siap dan saat status auth berubah
  function updateAllUI() {
    updateAuthUI();
    updateAdminSidebarMenu();
  }
  
  // Auto-update on page load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', updateAllUI);
  } else {
    updateAllUI();
  }
  
  // Listen for storage changes (cross-tab sync)
  window.addEventListener('storage', function(e) {
    if (e.key === 'spfk_auth_user') {
      updateAllUI();
    }
  });
  
})();
