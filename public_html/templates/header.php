<header class="site-header">
    <div class="header-content">
        <a href="feed.php" class="logo">
            <i class="fas fa-home"></i> <?php // ?>
        </a>

        <div class="header-title"><?php echo htmlspecialchars($config['site_nome']); ?></div> <?php // ?>

        <nav class="desktop-nav">
            <a href="api/usuarios/logout.php" class="logout-btn">Sair</a>
        </nav>
        
        <div class="mobile-menu-container">
            <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <span class="notification-count" style="display: none;">0</span>
        </div>
    </div>
</header>