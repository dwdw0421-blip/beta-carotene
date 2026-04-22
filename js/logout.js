const logoutBtn = document.getElementById('logout');

if (logoutBtn) {
  logoutBtn.onclick = () => {
    
    if (confirm('本当にログアウトしますか？')) {
      
      window.location.href = 'logout.php'; 
      
    }
  };
}