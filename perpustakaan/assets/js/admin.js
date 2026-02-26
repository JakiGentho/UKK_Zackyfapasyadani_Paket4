// ── Sidebar Toggle ─────────────────────────────────────────
const sidebar = document.getElementById("sidebar");
const sidebarToggle = document.getElementById("sidebarToggle");
const sidebarOverlay = document.getElementById("sidebarOverlay");

function openSidebar() {
  sidebar?.classList.add("open");
  sidebarOverlay?.classList.add("show");
  document.body.style.overflow = "hidden";
}
function closeSidebar() {
  sidebar?.classList.remove("open");
  sidebarOverlay?.classList.remove("show");
  document.body.style.overflow = "";
}

sidebarToggle?.addEventListener("click", function () {
  sidebar?.classList.contains("open") ? closeSidebar() : openSidebar();
});
sidebarOverlay?.addEventListener("click", closeSidebar);

// Tutup sidebar saat resize ke desktop
window.addEventListener("resize", function () {
  if (window.innerWidth > 1024) closeSidebar();
});

// ── Confirm Dialog ─────────────────────────────────────────
document.querySelectorAll("[data-confirm]").forEach(function (el) {
  el.addEventListener("click", function (e) {
    if (!confirm(this.dataset.confirm)) e.preventDefault();
  });
});

// ── Tooltip Bootstrap ──────────────────────────────────────
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
  new bootstrap.Tooltip(el, { trigger: "hover" });
});

// ── Cover Preview ──────────────────────────────────────────
const coverInput = document.getElementById("coverInput");
const coverPreview = document.getElementById("coverPreview");
if (coverInput && coverPreview) {
  coverInput.addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        coverPreview.src = e.target.result;
        coverPreview.style.display = "block";
      };
      reader.readAsDataURL(file);
    }
  });
}

// ── Auto Dismiss Alert ─────────────────────────────────────
document.querySelectorAll(".auto-dismiss").forEach(function (el) {
  setTimeout(function () {
    el.classList.remove("show");
    el.classList.add("fade");
    setTimeout(() => el.remove(), 400);
  }, 4000);
});

// ── Approve Button Confirm ─────────────────────────────────
document.querySelectorAll(".btn-approve").forEach(function (el) {
  el.addEventListener("click", function (e) {
    const title = this.dataset.name || "buku ini";
    if (!confirm(`Setujui peminjaman "${title}"?`)) e.preventDefault();
  });
});

// ── Return Button Confirm ──────────────────────────────────
document.querySelectorAll(".btn-return").forEach(function (el) {
  el.addEventListener("click", function (e) {
    const title = this.dataset.title || "buku ini";
    if (!confirm(`Konfirmasi pengembalian "${title}"?`)) e.preventDefault();
  });
});

// ── Toggle Password Visibility ─────────────────────────────
document.querySelectorAll(".toggle-password").forEach(function (btn) {
  btn.addEventListener("click", function () {
    const targetId = this.dataset.target;
    const input = document.getElementById(targetId);
    const icon = this.querySelector("i");
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

// ── Due Date Highlight ─────────────────────────────────────
document.querySelectorAll("[data-due-date]").forEach(function (el) {
  const due = new Date(el.dataset.dueDate);
  const now = new Date();
  const diff = Math.floor((due - now) / (1000 * 60 * 60 * 24));

  if (diff < 0) {
    el.classList.add("text-danger", "fw-bold");
    el.setAttribute("title", `Terlambat ${Math.abs(diff)} hari`);
    new bootstrap.Tooltip(el);
  } else if (diff <= 2) {
    el.classList.add("text-warning", "fw-semibold");
    el.setAttribute(
      "title",
      `Jatuh tempo ${diff === 0 ? "hari ini" : diff + " hari lagi"}`,
    );
    new bootstrap.Tooltip(el);
  }
});
