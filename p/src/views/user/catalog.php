<?php
$title = "Katalog Buku";
include '../src/views/layouts/header.php';
include '../src/views/layouts/navbar.php';
?>

<div class="container content-wrapper">
    <h2>Katalog Buku</h2>
    
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <?php echo $flash['message']; ?>
        </div>
    <?php endif; ?>

    <div class="search-box">
        <form method="GET" class="search-form">
            <input type="hidden" name="page" value="catalog">
            <input type="text" name="search" class="search-input" 
                   placeholder="Cari judul buku, pengarang, atau kode buku..." 
                   value="<?php echo escape($_GET['search'] ?? ''); ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
            <?php if (isset($_GET['search'])): ?>
                <a href="?page=catalog" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($books)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">📚</div>
            <p>Tidak ada buku yang tersedia</p>
        </div>
    <?php else: ?>
        <div class="book-grid">
            <?php foreach ($books as $book): ?>
                <div class="book-card">
                    <div class="book-cover">
                        📖
                    </div>
                    <div class="book-card-body">
                        <h3 class="book-title"><?php echo escape($book['judul']); ?></h3>
                        <p class="book-author"><?php echo escape($book['pengarang']); ?></p>
                        <p class="book-info"><?php echo escape($book['penerbit']); ?> (<?php echo $book['tahun_terbit']; ?>)</p>
                        <span class="book-stock <?php echo $book['stok'] <= 0 ? 'out-of-stock' : ''; ?>">
                            Stok: <?php echo $book['stok']; ?>
                        </span>
                        
                        <?php if ($book['stok'] > 0): ?>
                            <form method="POST" action="?page=borrow">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">Pinjam</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm btn-block" disabled>Tidak Tersedia</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../src/views/layouts/footer.php'; ?>
