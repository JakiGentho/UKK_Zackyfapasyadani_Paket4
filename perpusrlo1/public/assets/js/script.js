// Custom JavaScript for Perpustakaan RLO

document.addEventListener("DOMContentLoaded", function () {
  // Auto dismiss alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert:not(.alert-permanent)");
  alerts.forEach(function (alert) {
    setTimeout(function () {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 5000);
  });

  // Confirm delete actions
  const deleteLinks = document.querySelectorAll('a[onclick*="confirm"]');
  deleteLinks.forEach(function (link) {
    link.addEventListener("click", function (e) {
      if (!confirm("Apakah Anda yakin?")) {
        e.preventDefault();
      }
    });
  });

  // Form validation
  const forms = document.querySelectorAll(".needs-validation");
  forms.forEach(function (form) {
    form.addEventListener(
      "submit",
      function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add("was-validated");
      },
      false,
    );
  });

  // Password visibility toggle
  const passwordToggles = document.querySelectorAll(".password-toggle");
  passwordToggles.forEach(function (toggle) {
    toggle.addEventListener("click", function () {
      const input = this.previousElementSibling;
      const icon = this.querySelector("i");

      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
      }
    });
  });
});
