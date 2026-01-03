const API_URL = "http://127.0.0.1:8000/api";

console.log("api.js berhasil dibaca");

// =====================
// LOGIN (MEMBER)
// =====================
window.login = function () {
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;

  fetch(API_URL + "/login", {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Content-Type": "application/json"
    },
    body: JSON.stringify({ email, password })
  })
    .then(res => res.json())
    .then(data => {
      console.log("LOGIN RESPONSE:", data);

      if (data.token) {
        localStorage.setItem("token", data.token);
        alert("Login berhasil");
        window.location.href = "dashboard.html";
      } else {
        alert(data.message || "Login gagal");
      }
    })
    .catch(err => {
      console.error(err);
      alert("Tidak bisa konek ke backend");
    });
};

// =====================
// PINJAM BUKU (MEMBER)
// =====================
window.pinjam = function () {
  const token = localStorage.getItem("token");
  if (!token) {
    alert("Silakan login dulu");
    window.location.href = "login.html";
    return;
  }

  const params = new URLSearchParams(window.location.search);
  const api_book_id = params.get("id"); // contoh: OL505944W
  const author = params.get("author");
  const title = document.getElementById("title")?.innerText;

  if (!api_book_id || !title) {
    alert("Data buku tidak lengkap");
    return;
  }

  fetch(API_URL + "/transactions", {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Content-Type": "application/json",
      "Authorization": "Bearer " + token
    },
    body: JSON.stringify({
      api_book_id: api_book_id,
      title: title,
      author: author ? decodeURIComponent(author) : null,
      fine_per_day: 1000
    })
  })
    .then(res => res.json())
    .then(data => {
      console.log("PINJAM RESPONSE:", data);

      if (data.message) {
        alert("Request peminjaman berhasil\nMenunggu konfirmasi librarian");
        window.location.href = "dashboard.html";
      } else {
        alert("Gagal meminjam buku");
      }
    })
    .catch(err => {
      console.error(err);
      alert("Gagal konek ke backend");
    });
};
