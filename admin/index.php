<?php
// CHAMA A GUARITA DE SEGURANÇA!
require_once 'admin_auth.php';
// Conecta ao banco para buscar as denúncias pendentes
require_once __DIR__ . '/../../config/database.php';

// Query para contar denúncias de CONTEÚDO (posts e comentários) pendentes
$sql_content_count = "SELECT COUNT(id) AS pending_count FROM Denuncias WHERE status = 'pendente' AND tipo_conteudo IN ('post', 'comentario')";
$result_content_count = $conn->query($sql_content_count);
$pending_content_count = $result_content_count->fetch_assoc()['pending_count'];

// Query para contar denúncias de USUÁRIOS pendentes
$sql_user_count = "SELECT COUNT(id) AS pending_count FROM Denuncias WHERE status = 'pendente' AND tipo_conteudo = 'usuario'";
$result_user_count = $conn->query($sql_user_count);
$pending_user_count = $result_user_count->fetch_assoc()['pending_count'];

// Query para contar o total de usuários cadastrados
$sql_total_users = "SELECT COUNT(id) AS total_users FROM Usuarios";
$result_total_users = $conn->query($sql_total_users);
$total_users = $result_total_users->fetch_assoc()['total_users'];

// Query para contar usuários online (últimos 5 min)
$sql_online_users = "SELECT COUNT(id) AS online_count FROM Usuarios WHERE ultimo_acesso >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
$result_online_users = $conn->query($sql_online_users);
$online_users_count = $result_online_users->fetch_assoc()['online_count'];

// Query para contar o total de logins (visitas)
$sql_total_logins = "SELECT COUNT(id) AS total_logins FROM Logs_Login";
$result_total_logins = $conn->query($sql_total_logins);
$total_logins = $result_total_logins->fetch_assoc()['total_logins'];

// Query para contar os logins de hoje
$sql_logins_hoje = "SELECT COUNT(id) AS logins_hoje FROM Logs_Login WHERE data_login >= CURDATE()";
$result_logins_hoje = $conn->query($sql_logins_hoje);
$logins_hoje = $result_logins_hoje->fetch_assoc()['logins_hoje'];

// --- [LÓGICA DO POST MAIS VISTO] ---
// Busca o post mais acessado
$sql_top_post = "SELECT 
                    p.id, 
                    p.conteudo_texto, 
                    COUNT(l.id) as total_visualizacoes
                 FROM Logs_Visualizacao_Post AS l
                 JOIN Postagens AS p ON l.id_postagem = p.id
                 WHERE p.status = 'ativo'
                 GROUP BY l.id_postagem
                 ORDER BY total_visualizacoes DESC
                 LIMIT 1";
$result_top_post = $conn->query($sql_top_post);
$top_post = $result_top_post->fetch_assoc(); // Será null se nenhum post foi visto ainda
// --- [FIM DA LÓGICA] ---

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Painel Administrativo</title>
    
    <link rel="stylesheet" href="assets/css/admin.css?v=2.3">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <?php include 'templates/admin_header.php'; ?>
    <?php include 'templates/admin_mobile_nav.php'; ?>

    <div class="main-layout">
        
        <?php include 'templates/admin_sidebar.php'; ?>

        <main class="main-content">
            <div class="admin-card">
                <h1>Bem-vindo, Administrador!</h1>
                <p>Este é o seu painel de controle. Use a navegação ao lado para gerenciar o site.</p>
            </div>

            <div class="admin-card">
                <h2>Ações Rápidas</h2>
                <div class="actions-grid" style="margin-top: 20px;">
                    
                    <a href="admin_denuncias.php?tab=conteudo" class="action-card">
                        <i class="fas fa-flag"></i>
                        <span><strong>Denúncias de Conteúdo</strong><br>(<?php echo $pending_content_count; ?> pendentes)</span>
                    </a>
                    
                    <a href="admin_denuncias.php?tab=usuarios" class="action-card">
                        <i class="fas fa-user-shield"></i>
                        <span><strong>Denúncias de Usuários</strong><br>(<?php echo $pending_user_count; ?> pendentes)</span>
                    </a>
                    
                    <a href="admin_postagens.php" class="action-card">
                        <i class="fas fa-file-alt"></i>
                        <span><strong>Gerenciar Postagens</strong><br>Ver todas as postagens</span>
                    </a>

                    <a href="admin_comentarios.php" class="action-card">
                        <i class="fas fa-comments"></i>
                        <span><strong>Gerenciar Comentários</strong><br>Ver todos os comentários</span>
                    </a>

                </div>
            </div>

             <div class="admin-card">
                <h2><i class="fas fa-chart-bar"></i> Relatórios e Estatísticas</h2>
                <div class="stats-list">
                    <div class="stats-list-item">
                        <i class="fas fa-users stat-icon"></i>
                        <span class="stat-label">Total de Usuários Cadastrados</span>
                        <span class="stat-value"><?php echo $total_users; ?></span>
                    </div>
                    
                    <div class="stats-list-item">
                        <i class="fas fa-signal stat-icon" style="color: #28a745;"></i>
                        <span class="stat-label">Usuários Online Agora</span>
                        <span class="stat-value" style="color: #28a745; font-weight: bold;"><?php echo $online_users_count; ?></span>
                    </div>
                    
                    <div class="stats-list-item">
                        <i class="fas fa-eye stat-icon"></i>
                        <span class="stat-label">Visitas Totais (Logins)</span>
                        <span class="stat-value"><?php echo $total_logins; ?></span>
                    </div>

                    <div class="stats-list-item">
                        <i class="fas fa-fire stat-icon" style="color: #dc3545;"></i>
                        <span class="stat-label">Post Mais Acessado</span>
                        <span class="stat-value" style="font-size: 0.9em; text-align: right; line-height: 1.2;">
                            <?php if ($top_post): ?>
                                <a href="../postagem.php?id=<?php echo $top_post['id']; ?>" target="_blank" title="Ver postagem">
                                    <strong><?php echo htmlspecialchars(mb_strimwidth($top_post['conteudo_texto'], 0, 30, "...")); ?></strong>
                                </a>
                                <small style="display: block; color: #6c757d;"><?php echo $top_post['total_visualizacoes']; ?> acessos</small>
                            <?php else: ?>
                                <span class="status-tag status-pendente">Nenhum</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="stats-list-item">
                        <i class="fas fa-clipboard-list stat-icon"></i>
                        <span class="stat-label">Logins Efetuados Hoje</span>
                        <span class="stat-value"><?php echo $logins_hoje; ?></span>
                    </div>
                    
                    <?php // --- [BLOCO REMOVIDO DAQUI] --- ?>
                    
                </div>
            </div>
        </main>
        
    </div>
    
    <script src="assets/js/admin.js"></script>

</body>
</html>