<?php
// Se a sessão não foi iniciada na página principal, inicia aqui
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Se a conexão com o banco ($conn) não existir ainda, cria ela.
if (!isset($conn)) {
    // CORREÇÃO: O caminho para o banco de dados estava errado para este template
    require_once __DIR__ . '/../../config/database.php';
}

// Busca os dados do usuário logado para o link do perfil
$user_id_sidebar = $_SESSION['user_id'] ?? 0;
$usuario_sidebar = null;
$user_nome_sidebar = 'Visitante';

if ($user_id_sidebar > 0 && isset($conn)) {
    $sql_sidebar = "SELECT nome, sobrenome, foto_perfil_url FROM Usuarios WHERE id = ?";
    $stmt_sidebar = $conn->prepare($sql_sidebar);
    $stmt_sidebar->bind_param("i", $user_id_sidebar);
    $stmt_sidebar->execute();
    $result_sidebar = $stmt_sidebar->get_result();
    if ($data = $result_sidebar->fetch_assoc()) {
        $usuario_sidebar = $data;
        $user_nome_sidebar = $usuario_sidebar['nome'] . ' ' . $usuario_sidebar['sobrenome'];
    }
    $stmt_sidebar->close();
}
?>
<nav class="sidebar-nav">
    <ul>
        <li>
            <a href="feed.php">
                <span class="nav-icon"><i class="fas fa-home"></i></span>
                <span class="nav-text">Início</span>
            </a>
        </li>

        <li class="nav-dropdown-toggle">
            <a href="#" class="nav-link-dropdown notification-dropdown-toggle">
                <span class="nav-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-count" style="display: none;">0</span>
                </span>
                <span class="nav-text">Notificações</span>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
            </a>
        </li>
        
        <li class="nav-submenu-wrapper is-hidden notification-panel-wrapper">
            <div id="notifications-panel-unificado" class="notifications-panel">
                
                <div class="notifications-list">
                    </div>
                <div class="notifications-footer">
                    <a href="historico_notificacoes.php" class="primary-btn">Ver todas as notificações</a>
                </div>
            </div>
        </li>
        <li>
            <a href="perfil.php">
                <span class="nav-icon nav-avatar">
                    <?php if (!empty($usuario_sidebar['foto_perfil_url'])): ?>
                        <img src="<?php echo htmlspecialchars($usuario_sidebar['foto_perfil_url']); ?>" alt="Sua foto de perfil">
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
                </span>
                <span class="nav-text"><?php echo htmlspecialchars($user_nome_sidebar); ?></span>
            </a>
        </li>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <li>
            <a href="admin/index.php" target="_blank">
                <span class="nav-icon"><i class="fas fa-shield-alt"></i></span>
                <span class="nav-text">Painel Admin</span>
            </a>
        </li>
        <?php endif; ?>
        <hr>
        <li>
            <a href="#">
                <span class="nav-icon"><i class="fas fa-users"></i></span>
                <span class="nav-text">Grupos</span>
            </a>
        </li>
        <li>
            <a href="#">
                <span class="nav-icon"><i class="fas fa-flag"></i></span>
                <span class="nav-text">Páginas</span>
            </a>
        </li>
        <li>
            <a href="#">
                <span class="nav-icon"><i class="fas fa-store"></i></span>
                <span class="nav-text">Marketplace</span>
            </a>
        </li>
        
        <li class="nav-dropdown-toggle">
            <a href="#" class="nav-link-dropdown config-dropdown-toggle">
                <span class="nav-icon"><i class="fas fa-cog"></i></span>
                <span class="nav-text">Configurações</span>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
            </a>
        </li>
        
        <ul class="nav-submenu config-submenu is-hidden">
            <li>
                <a href="configurar_perfil.php">
                    <span class="nav-icon"><i class="fas fa-user-cog"></i></span>
                    <span class="nav-text">Configurações Gerais</span>
                </a>
            </li>
            <li>
                <a href="#" class="theme-toggle-link theme-toggle-btn-menu">
                    <span class="nav-icon">
                        <i class="fas fa-moon theme-icon-moon"></i>
                        <i class="fas fa-sun theme-icon-sun is-hidden"></i>
                    </span>
                    <span class="nav-text">
                        <span class="theme-text-light">Modo Escuro</span>
                        <span class="theme-text-dark">Modo Claro</span>
                    </span>
                </a>
            </li>
        </ul>

        <hr>
        <li>
            <a href="api/usuarios/logout.php">
                <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="nav-text">Sair</span>
            </a>
        </li>
    </ul>
</nav>