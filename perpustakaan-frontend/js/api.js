const API_URL = "http://127.0.0.1:8000/api";

console.log("api.js berhasil dibaca");

// =====================
// LOGIN
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
