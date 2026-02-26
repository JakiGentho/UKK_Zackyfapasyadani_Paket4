<header>
    <div class="container">
        <h1><?php echo SITE_NAME; ?></h1>
        <nav>
            <ul>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="?page=admin-dashboard">Dashboard</a></li>
                        <li><a href="?page=books">Buku</a></li>
                        <li><a href="?page=users">User</a></li>
                        <li><a href="?page=loans">Peminjaman</a></li>
                    <?php else: ?>
                        <li><a href="?page=user-dashboard">Dashboard</a></li>
                        <li><a href="?page=catalog">Katalog Buku</a></li>
                        <li><a href="?page=my-loans">Peminjaman Saya</a></li>
                    <?php endif; ?>
                    <li><span class="nav-user">Hi, <?php echo escape($_SESSION['nama']); ?></span></li>
                    <li><a href="?page=logout">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
