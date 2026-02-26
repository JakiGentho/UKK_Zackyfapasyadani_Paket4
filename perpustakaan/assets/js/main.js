/**
 * PERPUSTAKAAN DIGITAL — Global JS
 * Digunakan di semua halaman
 */

document.addEventListener("DOMContentLoaded", function () {
  // ── Auto-dismiss Alert setelah 4 detik ──
  const alerts = document.querySelectorAll(".alert.auto-dismiss");
  alerts.forEach(function (alert) {
    setTimeout(function () {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
      bsAlert.close();
    }, 4000);
  });

  // ── Konfirmasi Hapus (data-confirm) ──
  document.querySelectorAll("[data-confirm]").forEach(function (el) {
    el.addEventListener("click", function (e) {
      const msg = el.getAttribute("data-confirm") || "Apakah Anda yakin?";
      if (!confirm(msg)) e.preventDefault();
    });
  });

  // ── Tooltip Bootstrap ──
  const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltipEls.forEach(function (el) {
    new bootstrap.Tooltip(el, { trigger: "hover" });
  });

  // ── Active Nav Highlight (fallback) ──
  const currentPath = window.location.pathname.split("/").pop();
  document.querySelectorAll(".nav-link").forEach(function (link) {
    const href = link.getAttribute("href");
    if (href && href.includes(currentPath) && currentPath !== "") {
      link.classList.add("active");
    }
  });
});

/**
 * Format angka ke format Rupiah
 * @param {number} amount
 * @returns {string}
 */
function formatRupiah(amount) {
  return "Rp " + new Intl.NumberFormat("id-ID").format(amount);
}

/**
 * Debounce function
 * @param {Function} fn
 * @param {number} delay
 */
function debounce(fn, delay = 300) {
  let timer;
  return function (...args) {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(this, args), delay);
  };
}
