/**
 * PERPUSTAKAAN DIGITAL — User JS
 */

document.addEventListener("DOMContentLoaded", function () {
  // ── Live Filter Katalog Buku ──
  const searchBox = document.getElementById("bookSearch");
  const bookGrid = document.getElementById("bookGrid");

  if (searchBox && bookGrid) {
    searchBox.addEventListener(
      "input",
      debounce(function () {
        const query = this.value.toLowerCase().trim();
        const cards = bookGrid.querySelectorAll(".book-item");
        let visible = 0;

        cards.forEach(function (card) {
          const text = card.textContent.toLowerCase();
          const show = text.includes(query);
          card.style.display = show ? "" : "none";
          if (show) visible++;
        });

        const emptyMsg = document.getElementById("emptySearch");
        if (emptyMsg) {
          emptyMsg.style.display = visible === 0 ? "block" : "none";
        }

        const countLabel = document.getElementById("bookCount");
        if (countLabel) countLabel.textContent = visible;
      }, 300),
    );
  }

  // ── Konfirmasi Pinjam Buku ──
  document.querySelectorAll(".btn-borrow").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      const title = btn.getAttribute("data-title") || "buku ini";
      if (
        !confirm(
          `Ajukan peminjaman untuk:\n📚 "${title}"?\n\nPermintaan akan dikonfirmasi oleh admin.`,
        )
      ) {
        e.preventDefault();
      }
    });
  });

  // ── Konfirmasi Pengembalian Buku ──
  document.querySelectorAll(".btn-return").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      const title = btn.getAttribute("data-title") || "buku ini";
      if (!confirm(`Konfirmasi pengembalian:\n📚 "${title}"?`)) {
        e.preventDefault();
      }
    });
  });

  // ── Countdown Jatuh Tempo ──
  document.querySelectorAll("[data-due-date]").forEach(function (el) {
    const dueDate = new Date(el.getAttribute("data-due-date"));
    const now = new Date();
    const diff = Math.ceil((dueDate - now) / (1000 * 60 * 60 * 24));

    if (diff < 0) {
      el.innerHTML = `<span class="text-danger fw-bold">Terlambat ${Math.abs(diff)} hari</span>`;
    } else if (diff === 0) {
      el.innerHTML = `<span class="text-warning fw-bold">Jatuh tempo hari ini!</span>`;
    } else if (diff <= 3) {
      el.innerHTML = `<span class="text-warning">${diff} hari lagi</span>`;
    } else {
      el.innerHTML = `<span class="text-success">${diff} hari lagi</span>`;
    }
  });

  // ── Toggle Filter Kategori ──
  document.querySelectorAll(".category-filter-btn").forEach(function (btn) {
    btn.addEventListener("click", function () {
      document
        .querySelectorAll(".category-filter-btn")
        .forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      const catId = btn.getAttribute("data-cat");
      const cards = document.querySelectorAll(".book-item");

      cards.forEach(function (card) {
        if (catId === "all" || card.getAttribute("data-cat") === catId) {
          card.style.display = "";
        } else {
          card.style.display = "none";
        }
      });
    });
  });
});
