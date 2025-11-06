<?php
// 1. CARREGA A CONFIGURAÇÃO, SESSÃO E VERIFICA O MODO MANUTENÇÃO
// Esta é a correção de bug: esta linha TEM de vir antes de tudo.
require_once __DIR__ . '/../config/database.php';

// 2. AGORA VERIFICA SE O UTILIZADOR ESTÁ LOGADO
// (O session_start() original foi removido pois já está no database.php)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}
// --- FIM DA MODIFICAÇÃO ---

$user_id = $_SESSION['user_id'];
// A linha 'require_once' original foi movida para o topo.

// --- BUSCA TODAS AS NOTIFICAÇÕES DO UTILIZADOR ---
$sql_notificacoes = "SELECT 
                        n.id, 
                        n.tipo, 
                        n.id_referencia, 
                        n.lida, 
                        n.data_criacao,
                        u.nome AS remetente_nome,
                        u.sobrenome AS remetente_sobrenome,
                        u.foto_perfil_url AS remetente_foto
                    FROM 
                        notificacoes AS n
                    JOIN 
                        Usuarios AS u ON n.remetente_id = u.id
                    WHERE 
                        n.usuario_id = ?
                    ORDER BY 
                        n.data_criacao DESC";

$stmt = $conn->prepare($sql_notificacoes);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notificacoes = [];
while ($row = $result->fetch_assoc()) {
    $notificacoes[] = $row;
}
$stmt->close();

// 3. DEFINE O TÍTULO DA PÁGINA (para o head_common.php)
$page_title = 'Histórico de Notificações';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php 
    // 4. INCLUI O NOSSO NOVO <HEAD> CENTRALIZADO
    // (Substitui o <head> antigo)
    include 'templates/head_common.php'; 
    ?>
</head>
<body>

    <?php include 'templates/header.php'; ?>
    <?php include 'templates/mobile_nav.php'; ?>

    <div class="main-content-area">
        <?php include 'templates/sidebar.php'; ?>

        <main class="feed-container">
            <div class="post-card">
                <div class="notifications-header">
                    <h3>Todas as Notificações</h3>
                </div>
                <div class="full-notifications-page-list">
                    <?php if (empty($notificacoes)): ?>
                        <p class="no-notifications-message">Você não tem nenhuma notificação.</p>
                    <?php else: ?>
                        <?php foreach ($notificacoes as $notif): ?>
                            <?php
                                // --- LÓGICA ATUALIZADA AQUI ---
                                $isUnreadClass = $notif['lida'] == 0 ? 'unread' : '';
                                $text = '';
                                $link = '#'; // Link padrão

                                switch ($notif['tipo']) {
                                    case 'curtida_post':
                                        $text = 'curtiu a sua publicação.';
                                        $link = 'postagem.php?id=' . $notif['id_referencia'];
                                        break;
                                    case 'comentario_post':
                                        $text = 'comentou na sua publicação.';
                                        $link = 'postagem.php?id=' . $notif['id_referencia'];
                                        break;
                                    case 'curtida_comentario':
                                        $text = 'curtiu o seu comentário.';
                                        $link = 'postagem.php?id=' . $notif['id_referencia'];
                                        break;
                                    case 'pedido_amizade': // <-- NOVO CASO ADICIONADO
                                        $text = 'enviou-lhe um pedido de amizade.';
                                        $link = 'perfil.php?id=' . $notif['id_referencia'];
                                        break;
                                    default:
                                        $text = 'interagiu consigo.';
                                }
                                
                                $avatarHTML = $notif['remetente_foto']
                                    ? '<img src="' . htmlspecialchars($notif['remetente_foto']) . '" alt="Avatar">'
                                    : '<div class="default-avatar-icon"><i class="fas fa-user"></i></div>';
                            ?>
                            <a href="<?php echo $link; ?>" class="notification-item <?php echo $isUnreadClass; ?>">
                                <div class="notification-avatar">
                                    <?php echo $avatarHTML; ?>
                                </div>
                                <div class="notification-text">
                                    <p>
                                        <span class="user-name"><?php echo htmlspecialchars($notif['remetente_nome'] . ' ' . $notif['remetente_sobrenome']); ?></span>
                                        <?php echo $text; ?>
                                    </p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

<?php 
// 5. INCLUI O FOOTER (que agora terá $asset_version)
include 'templates/footer.php'; 
?>
</body>
</html>