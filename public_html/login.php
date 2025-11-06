<?php
// 1. CARREGA A CONFIGURAÇÃO, SESSÃO E VERIFICA O MODO MANUTENÇÃO
// Esta linha substitui o antigo session_start()
require_once __DIR__ . '/../config/database.php';

// 2. LÓGICA DE REDIRECIONAMENTO (existente)
if (isset($_SESSION['user_id'])) {
    header("Location: feed.php");
    exit();
}

// 3. DEFINE O TÍTULO DA PÁGINA PARA O TEMPLATE
$page_title = 'Login';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php 
    // 4. INCLUI O NOSSO NOVO <HEAD> CENTRALIZADO
    // Ele contém o <title>, <meta>, style.css com $asset_version, e Font Awesome
    include 'templates/head_common.php'; 
    ?>
</head>
<body class="login-page-body">

    <div class="container">
        
        <div class="form-header">
            <?php // --- ÍCONE REMOVIDO DESTA LINHA --- ?>
            <div class="header-text">
                <h1><?php echo htmlspecialchars($config['site_nome']); ?></h1>
                <h2>Faça login para continuar</h2>
            </div>
        </div>

        <?php
        if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso') {
            echo '<div class="success-message">Cadastro realizado com sucesso! Faça o login para continuar.</div>';
        }

        if (isset($_SESSION['login_error'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
            unset($_SESSION['login_error']);
        }
        ?>

        <form action="api/usuarios/processa_login.php" method="POST">
            <input type="text" name="email_ou_usuario" placeholder="E-mail ou nome de usuário" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit" class="primary-btn">Entrar</button>
        </form>

        <div class="form-actions">
            <a href="#" class="link-secondary">Esqueceu sua senha?</a>
            
            <?php if (isset($config['permite_cadastro']) && $config['permite_cadastro'] == '1'): ?>
                <button type="button" class="secondary-btn" onclick="location.href='cadastro.php'">Criar nova conta</button>
            <?php endif; ?>
            
        </div>

    </div>

    <footer class="site-footer">
        <div class="footer-links">
            <a href="#">Sobre</a>
            <a href="#">Termos</a>
            <a href="#">Políticas de Privacidade</a>
            <a href="#">Desenvolvedores</a>
            <a href="#">Ajuda</a>
        </div>
        <div class="footer-copyright">
            &copy; 2025 <?php echo htmlspecialchars($config['site_nome']); ?>
        </div>
    </footer>
</body>
</html>