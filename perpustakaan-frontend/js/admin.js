/* =========================
   CONFIG
========================= */
const API_URL = "http://127.0.0.1:8000/api";
const ADMIN_TOKEN_KEY = "adminToken";
const ADMIN_USER_KEY  = "adminUser";

/* =========================
   AUTO REDIRECT (LOGIN)
========================= */
function adminAutoRedirect() {
  const token = localStorage.getItem(ADMIN_TOKEN_KEY);
  const user  = localStorage.getItem(ADMIN_USER_KEY);
  if (token && user) {
    window.location.replace("admin-dashboard.html");
  }
}

/* =========================
   LOGIN ADMIN
========================= */
function loginAdmin() {
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();

  if (!email || !password) {
    Swal.fire({
      icon: "warning",
      title: "Form belum lengkap",
      text: "Email dan password wajib diisi"
    });
    return;
  }

  fetch(`${API_URL}/login`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json"
    },
    body: JSON.stringify({ email, password })
  })
  .then(async res => {
    const data = await res.json();
    return { status: res.status, data };
  })
  .then(({ status, data }) => {
    if (status !== 200) {
      Swal.fire({
        icon: "error",
        title: "Login gagal",
        text: data.message || "Email atau password salah"
      });
      return;
    }

    if (!data.user || data.user.role !== "admin") {
      Swal.fire({
        icon: "error",
        title: "Akses ditolak",
        text: "Akun ini bukan admin/librarian"
      });
      return;
    }

    localStorage.setItem(ADMIN_TOKEN_KEY, data.token);
    localStorage.setItem(ADMIN_USER_KEY, JSON.stringify(data.user));

    Swal.fire({
      icon: "success",
      title: "Login admin berhasil 🎉",
      confirmButtonText: "Masuk Dashboard"
    }).then(() => {
      window.location.href = "admin-dashboard.html";
    });
  })
  .catch(() => {
    Swal.fire({
      icon: "error",
      title: "Server Error",
      text: "Tidak bisa terhubung ke server"
    });
  });
}

/* =========================
   GUARD DASHBOARD ADMIN
========================= */
function requireAdminDashboard() {
  const token = localStorage.getItem(ADMIN_TOKEN_KEY);
  const userStr = localStorage.getItem(ADMIN_USER_KEY);

  if (!token || !userStr) {
    window.location.replace("admin-login.html");
    return;
  }

  fetch(`${API_URL}/me`, {
    headers: {
      "Accept": "application/json",
      "Authorization": "Bearer " + token
    }
  })
  .then(res => res.json())
  .then(data => {
    if (!data.user || data.user.role !== "admin") {
      throw new Error();
    }

    const name =
      data.user.full_name ||
      data.user.name ||
      data.user.email;

    document.getElementById("welcome").innerText =
      `Hai, ${name} 👋\nSelamat datang di Sistem Perpustakaan 📚`;
  })
  .catch(() => {
    localStorage.removeItem(ADMIN_TOKEN_KEY);
    localStorage.removeItem(ADMIN_USER_KEY);
    window.location.replace("admin-login.html");
  });
}

/* =========================
   NAVIGATION
========================= */
function goBooks(){ window.location.href = "admin-books.html"; }
function goSearch(){ window.location.href = "admin-search.html"; }
function goBorrow(){ window.location.href = "admin-borrow.html"; }
function goReturn(){ window.location.href = "admin-return.html"; }
function goFine(){ window.location.href = "admin-fines.html"; }

/* =========================
   LOGOUT
========================= */
function showAdminMenu() {
  Swal.fire({
    title: "Menu Admin",
    showDenyButton: true,
    confirmButtonText: "Logout",
    denyButtonText: "Tutup",
  }).then(result => {
    if (result.isConfirmed) logoutAdmin();
  });
}

function logoutAdmin() {
  const token = localStorage.getItem(ADMIN_TOKEN_KEY);

  fetch(`${API_URL}/logout`, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Authorization": "Bearer " + token
    }
  })
  .finally(() => {
    localStorage.removeItem(ADMIN_TOKEN_KEY);
    localStorage.removeItem(ADMIN_USER_KEY);
    window.location.replace("admin-login.html");
  });
}
