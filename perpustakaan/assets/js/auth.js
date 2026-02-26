/**
 * PERPUSTAKAAN DIGITAL — Auth JS
 * Untuk halaman login saja
 */

document.addEventListener("DOMContentLoaded", function () {
  // ── Toggle Visibilitas Password ──
  document.querySelectorAll(".toggle-password").forEach(function (btn) {
    btn.addEventListener("click", function () {
      const targetId = btn.getAttribute("data-target");
      const input = document.getElementById(targetId);
      const icon = btn.querySelector("i");

      if (!input) return;

      if (input.type === "password") {
        input.type = "text";
        icon.className = "bi bi-eye-slash";
      } else {
        input.type = "password";
        icon.className = "bi bi-eye";
      }
    });
  });

  // ── Password Strength Indicator ──
  const passInput = document.getElementById("passwordInput");
  const strengthBar = document.getElementById("strengthBar");
  const strengthLabel = document.getElementById("strengthLabel");

  if (passInput && strengthBar) {
    passInput.addEventListener("input", function () {
      const val = passInput.value;
      let score = 0;

      if (val.length >= 6) score++;
      if (val.length >= 10) score++;
      if (/[A-Z]/.test(val)) score++;
      if (/[0-9]/.test(val)) score++;
      if (/[^A-Za-z0-9]/.test(val)) score++;

      strengthBar.className = "password-strength";
      if (val.length === 0) {
        strengthBar.style.width = "0";
        if (strengthLabel) strengthLabel.textContent = "";
      } else if (score <= 2) {
        strengthBar.classList.add("strength-weak");
        if (strengthLabel) strengthLabel.textContent = "Lemah";
      } else if (score <= 3) {
        strengthBar.classList.add("strength-medium");
        if (strengthLabel) strengthLabel.textContent = "Sedang";
      } else {
        strengthBar.classList.add("strength-strong");
        if (strengthLabel) strengthLabel.textContent = "Kuat";
      }
    });
  }

  // ── Auto focus ──
  const emailInput = document.getElementById("emailInput");
  if (emailInput) emailInput.focus();
});
