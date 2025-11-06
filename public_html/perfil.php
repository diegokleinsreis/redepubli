<?php
// 1. CARREGA A CONFIGURAÇÃO, SESSÃO E VERIFICA O MODO MANUTENÇÃO
require_once __DIR__ . '/../config/database.php';

// 2. LÓGICA DE LOGIN E DETERMINAÇÃO DE PERFIL
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$id_usuario_logado = $is_logged_in ? (int)$_SESSION['user_id'] : 0;

$id_do_perfil_a_exibir = $_GET['id'] ?? 0;
if (empty($id_do_perfil_a_exibir) && $is_logged_in) {
    $id_do_perfil_a_exibir = $id_usuario_logado;
}
$id_do_perfil_a_exibir = (int)$id_do_perfil_a_exibir;

if ($id_do_perfil_a_exibir <= 0) {
    die("Perfil não especificado.");
}

// --- NOVA LÓGICA DE ROTEAMENTO DE ABAS ---
// Define a aba ativa. O padrão é 'posts'.
$active_page = $_GET['tab'] ?? 'posts';
// --- FIM DA NOVA LÓGICA ---

if (!$is_logged_in) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    // A lógica de modo manutenção ou de visitante será tratada mais abaixo.
}

// 3. BUSCA OS DADOS DO PERFIL
// Usamos a query mais completa (do antigo sobre_perfil.php) para já ter todos os dados.

// ===== ALTERAÇÃO FEITA AQUI =====
// Adicionamos "u.ultimo_acesso" à consulta SELECT
$sql_perfil = "SELECT u.*, u.ultimo_acesso, b.nome AS nome_bairro, c.nome AS nome_cidade, e.sigla AS sigla_estado 
               FROM Usuarios AS u 
               LEFT JOIN Bairros AS b ON u.id_bairro = b.id 
               LEFT JOIN Cidades AS c ON b.id_cidade = c.id 
               LEFT JOIN Estados AS e ON c.id_estado = e.id 
               WHERE u.id = ?";
// ===== FIM DA ALTERAÇÃO =====

$stmt_perfil = $conn->prepare($sql_perfil);
$stmt_perfil->bind_param("i", $id_do_perfil_a_exibir);
$stmt_perfil->execute();
$perfil_data = $stmt_perfil->get_result()->fetch_assoc();

if (!$perfil_data) { die("Usuário não encontrado."); }

// 4. DEFINE O TÍTULO DA PÁGINA
$page_title = htmlspecialchars($perfil_data['nome'] . ' ' . $perfil_data['sobrenome']);

// 5. INICIALIZA VARIÁVEIS DE CONTEÚDO
$result_posts = null;
$lista_amigos = [];
$pode_ver_lista_amigos = false;
$status_amizade = null;
$amizade_id = null;
$id_remetente_pedido = null;
$sao_amigos = false;
$pode_ver_conteudo = false;

// 6. LÓGICA DE AMIZADE E PRIVACIDADE (APENAS SE LOGADO)
if ($is_logged_in) {
    // --- VERIFICAR STATUS DA AMIZADE ---
    if ($id_usuario_logado != $id_do_perfil_a_exibir) {
        $sql_amizade = "SELECT id, status, usuario_um_id FROM Amizades 
                        WHERE (usuario_um_id = ? AND usuario_dois_id = ?) 
                           OR (usuario_um_id = ? AND usuario_dois_id = ?)";
        $stmt_amizade = $conn->prepare($sql_amizade);
        $stmt_amizade->bind_param("iiii", $id_usuario_logado, $id_do_perfil_a_exibir, $id_do_perfil_a_exibir, $id_usuario_logado);
        $stmt_amizade->execute();
        $resultado_amizade = $stmt_amizade->get_result()->fetch_assoc();
        
        if ($resultado_amizade) {
            $status_amizade = $resultado_amizade['status'];
            $amizade_id = $resultado_amizade['id'];
            $id_remetente_pedido = $resultado_amizade['usuario_um_id'];
            if ($status_amizade === 'aceite') {
                $sao_amigos = true;
            }
        }
        $stmt_amizade->close();
    }

    // --- LÓGICA DE VISUALIZAÇÃO ---
    if ($id_usuario_logado == $id_do_perfil_a_exibir || $perfil_data['perfil_privado'] == 0 || $sao_amigos) {
        $pode_ver_conteudo = true;
    }

    // --- 7. BUSCA DADOS DA ABA ATIVA ---
    if ($pode_ver_conteudo) {
        switch ($active_page) {
            case 'sobre':
                // Os dados já foram buscados na query $sql_perfil
                break;

            case 'amigos':
                // Lógica de permissão para ver a lista de amigos (do antigo amigos.php)
                if ($id_usuario_logado === $id_do_perfil_a_exibir) {
                    $pode_ver_lista_amigos = true;
                } else {
                    $privacidade = $perfil_data['privacidade_amigos'];
                    if ($privacidade === 'todos') {
                        $pode_ver_lista_amigos = true;
                    } elseif ($privacidade === 'amigos' && $sao_amigos) {
                        $pode_ver_lista_amigos = true;
                    }
                }
                
                // Busca a lista de amigos (do antigo amigos.php)
                if ($pode_ver_lista_amigos) {
                    $sql_amigos = "SELECT u.id, u.nome, u.sobrenome, u.nome_de_usuario, u.foto_perfil_url
                                   FROM Amizades a
                                   JOIN Usuarios u ON u.id = IF(a.usuario_um_id = ?, a.usuario_dois_id, a.usuario_um_id)
                                   WHERE (a.usuario_um_id = ? OR a.usuario_dois_id = ?) AND a.status = 'aceite'";

                    $stmt_amigos = $conn->prepare($sql_amigos);
                    $stmt_amigos->bind_param("iii", $id_do_perfil_a_exibir, $id_do_perfil_a_exibir, $id_do_perfil_a_exibir);
                    $stmt_amigos->execute();
                    $result_amigos = $stmt_amigos->get_result();
                    while ($amigo = $result_amigos->fetch_assoc()) {
                        $lista_amigos[] = $amigo;
                    }
                    $stmt_amigos->close();
                }
                break;

            case 'salvos':
                // Lógica do antigo salvos.php
                // Só mostra se o usuário logado estiver vendo o próprio perfil
                if ($id_do_perfil_a_exibir == $id_usuario_logado) {
                    $sql_posts = "SELECT p.id, p.conteudo_texto, p.data_postagem, p.url_media, p.tipo_media, u.id AS autor_id, u.nome, u.sobrenome, u.foto_perfil_url, 
                                  (SELECT COUNT(*) FROM Curtidas WHERE id_postagem = p.id) AS total_curtidas, 
                                  (SELECT COUNT(*) FROM Curtidas WHERE id_postagem = p.id AND id_usuario = ?) AS usuario_curtiu, 
                                  (SELECT COUNT(*) FROM Comentarios WHERE id_postagem = p.id AND status = 'ativo') AS total_comentarios, 
                                  1 AS usuario_salvou 
                                  FROM Postagens_Salvas AS ps 
                                  JOIN Postagens AS p ON ps.id_postagem = p.id 
                                  JOIN Usuarios AS u ON p.id_usuario = u.id 
                                  WHERE ps.id_usuario = ? AND p.status = 'ativo' 
                                  ORDER BY ps.data_salvo DESC";
                    $stmt_posts = $conn->prepare($sql_posts);
                    $stmt_posts->bind_param("ii", $id_usuario_logado, $id_usuario_logado);
                    $stmt_posts->execute();
                    $result_posts = $stmt_posts->get_result();
                }
                break;

            case 'posts':
            default:
                // Lógica padrão do antigo perfil.php
                $sql_posts = "SELECT p.id, p.conteudo_texto, p.data_postagem, p.url_media, p.tipo_media, u.id AS autor_id, u.nome, u.sobrenome, u.foto_perfil_url, 
                              (SELECT COUNT(*) FROM Curtidas WHERE id_postagem = p.id) AS total_curtidas, 
                              (SELECT COUNT(*) FROM Curtidas WHERE id_postagem = p.id AND id_usuario = ?) AS usuario_curtiu, 
                              (SELECT COUNT(*) FROM Comentarios WHERE id_postagem = p.id AND status = 'ativo') AS total_comentarios, 
                              (SELECT COUNT(*) FROM Postagens_Salvas WHERE id_postagem = p.id AND id_usuario = ?) AS usuario_salvou 
                              FROM Postagens AS p JOIN Usuarios AS u ON p.id_usuario = u.id 
                              WHERE p.id_usuario = ? AND p.status = 'ativo' AND u.status = 'ativo' 
                              ORDER BY p.data_postagem DESC";
                $stmt_posts = $conn->prepare($sql_posts);
                $stmt_posts->bind_param("iii", $id_usuario_logado, $id_usuario_logado, $id_do_perfil_a_exibir);
                $stmt_posts->execute();
                $result_posts = $stmt_posts->get_result();
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include 'templates/head_common.php'; ?>
    
    <?php // Estilos que eram exclusivos da página de amigos ?>
    <style>
        .friends-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; }
        .friend-card { background: #fff; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); text-align: center; padding: 15px; }
        .friend-card a { text-decoration: none; color: #050505; }
        .friend-avatar { width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 10px; object-fit: cover; }
        .friend-name { font-weight: 600; font-size: 0.9em; margin: 0; }
        .friend-username { font-size: 0.8em; color: #606770; margin: 2px 0 0 0; }
    </style>
</head>
<body>
    
    <?php if ($is_logged_in): ?>
        
        <?php // --- VISUALIZAÇÃO COMPLETA PARA UTILIZADORES LOGADOS --- ?>
        <?php include 'templates/header.php'; ?>
        <?php include 'templates/mobile_nav.php'; ?>
        <div class="main-content-area">
            <?php include 'templates/sidebar.php'; ?>
            <main class="profile-main-content">
                
                <?php // O cabeçalho do perfil é sempre exibido ?>
                <?php include 'templates/profile_header_template.php'; ?>

                <?php // --- MENU DE NAVEGAÇÃO (antigo profile_nav_template.php) --- ?>
                <nav class="profile-nav">
                    <a href="perfil.php?id=<?php echo $id_do_perfil_a_exibir; ?>" 
                       class="<?php echo ($active_page === 'posts') ? 'active' : ''; ?>">Posts</a>
                       
                    <a href="perfil.php?id=<?php echo $id_do_perfil_a_exibir; ?>&tab=sobre" 
                       class="<?php echo ($active_page === 'sobre') ? 'active' : ''; ?>">Sobre</a>
                       
                    <a href="perfil.php?id=<?php echo $id_do_perfil_a_exibir; ?>&tab=amigos" 
                       class="<?php echo ($active_page === 'amigos') ? 'active' : ''; ?>">Amigos</a>
                    
                    <?php if ($id_do_perfil_a_exibir == $id_usuario_logado): ?>
                        <a href="perfil.php?id=<?php echo $id_do_perfil_a_exibir; ?>&tab=salvos"
                           class="<?php echo ($active_page === 'salvos') ? 'active' : ''; ?>">Salvos</a>
                    <?php endif; ?>
                </nav>
                <?php // --- FIM DO MENU DE NAVEGAÇÃO --- ?>


                <?php // --- ROTEADOR DE CONTEÚDO DA ABA --- ?>
                <?php if ($perfil_data['status'] === 'suspenso'): ?>
                    <div class="post-card">
                        <h2 style="text-align: center; color: #8a1717;">Esta conta está suspensa.</h2>
                    </div>
                <?php elseif (!$pode_ver_conteudo): ?>
                    <div class="post-card private-profile-card">
                        <i class="fas fa-lock"></i>
                        <h3>Este perfil é privado</h3>
                        <p>Adicione <?php echo htmlspecialchars($perfil_data['nome']); ?> como amigo para ver as suas publicações e informações.</p>
                    </div>
                <?php else: ?>
                    
                    <?php // --- Lógica para exibir o conteúdo da aba correta --- ?>
                    <?php switch ($active_page):
                        
                        // --- CASO: SOBRE ---
                        case 'sobre': ?>
                            <?php if (!empty($perfil_data['biografia'])): ?>
                            <div class="profile-details-card">
                                <h3><i class="fas fa-info-circle"></i> Biografia</h3>
                                <p class="profile-bio" style="font-size: 1em; padding: 5px;"><?php echo nl2br(htmlspecialchars($perfil_data['biografia'])); ?></p>
                            </div>
                            <?php endif; ?>

                            <div class="profile-details-card">
                                <h3>Informações de <?php echo htmlspecialchars($perfil_data['nome']); ?></h3>
                                <div class="info-item"><i class="fas fa-user"></i><label>Nome Completo</label><span><?php echo htmlspecialchars($perfil_data['nome'] . ' ' . $perfil_data['sobrenome']); ?></span></div>
                                <div class="info-item"><i class="fas fa-at"></i><label>Nome de Usuário</label><span>@<?php echo htmlspecialchars($perfil_data['nome_de_usuario']); ?></span></div>
                                <div class="info-item"><i class="fas fa-envelope"></i><label>E-mail</label><span><?php echo htmlspecialchars($perfil_data['email']); ?></span></div>
                                <?php if (!empty($perfil_data['nome_bairro'])): ?>
                                <div class="info-item"><i class="fas fa-map-marker-alt"></i><label>Localização</label><span><?php echo htmlspecialchars($perfil_data['nome_bairro'] . ', ' . $perfil_data['nome_cidade'] . ' - ' . $perfil_data['sigla_estado']); ?></span></div>
                                <?php endif; ?>
                                <div class="info-item"><i class="fas fa-birthday-cake"></i><label>Data de Nascimento</label><span><?php echo date("d/m/Y", strtotime($perfil_data['data_nascimento'])); ?></span></div>
                                <div class="info-item"><i class="fas fa-calendar-alt"></i><label>Membro desde</label><span><?php echo date("d/m/Y", strtotime($perfil_data['data_cadastro'])); ?></span></div>
                            </div>
                            <?php break; ?>

                        <?php // --- CASO: AMIGOS ---
                        case 'amigos': ?>
                            <div class="page-section-header">
                                <h1>Amigos de <?php echo htmlspecialchars($perfil_data['nome']); ?></h1>
                            </div>
                            <?php if ($pode_ver_lista_amigos): ?>
                                <?php if (!empty($lista_amigos)): ?>
                                    <div class="friends-grid">
                                        <?php foreach ($lista_amigos as $amigo): ?>
                                            <div class="friend-card">
                                                <a href="perfil.php?id=<?php echo $amigo['id']; ?>">
                                                    <img src="<?php echo htmlspecialchars($amigo['foto_perfil_url'] ?? 'assets/images/default-avatar.png.png'); ?>" alt="Foto de <?php echo htmlspecialchars($amigo['nome']); ?>" class="friend-avatar">
                                                    <p class="friend-name"><?php echo htmlspecialchars($amigo['nome'] . ' ' . $amigo['sobrenome']); ?></p>
                                                    <p class="friend-username">@<?php echo htmlspecialchars($amigo['nome_de_usuario']); ?></p>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="post-card">
                                        <p><?php echo htmlspecialchars($perfil_data['nome']); ?> ainda não tem amigos.</p>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="post-card private-profile-card">
                                    <i class="fas fa-user-friends"></i>
                                    <h3>Lista de amigos privada</h3>
                                    <p>Apenas amigos de <?php echo htmlspecialchars($perfil_data['nome']); ?> podem ver a sua lista de amigos.</p>
                                </div>
                            <?php endif; ?>
                            <?php break; ?>

                        <?php // --- CASO: SALVOS ---
                        case 'salvos': ?>
                            <div class="page-section-header">
                                <h1>Meus Itens Salvos</h1>
                                <p>Aqui estão todas as publicações que você salvou para ver mais tarde.</p>
                            </div>
                            <?php if ($result_posts && $result_posts->num_rows > 0): ?>
                                <?php while($post = $result_posts->fetch_assoc()): ?>
                                    <div class="post-card" id="post-<?php echo $post['id']; ?>">
                                        <?php $user_id = $id_usuario_logado; include 'templates/post_template.php'; ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="post-card"><p>Você ainda não salvou nenhuma postagem...</p></div>
                            <?php endif; ?>
                            <?php break; ?>

                        <?php // --- CASO: POSTS (Padrão) ---
                        case 'posts':
                        default: ?>
                            <?php if ($id_do_perfil_a_exibir == $id_usuario_logado): ?>
                            <div class="create-post-card">
                                <form action="api/postagens/criar_post.php" method="POST" enctype="multipart/form-data">
                                    <textarea name="conteudo_texto" placeholder="No que você está pensando?" required></textarea>
                                    <div class="create-post-actions">
                                        <input type="file" name="post_media" id="post_media" class="input-file" accept="image/*,video/mp4,video/webm,video/mov">
                                        <label for="post_media" class="input-file-label"><i class="fas fa-camera"></i> Adicionar Foto/Vídeo</label>
                                        <button type="submit" class="primary-btn-small">Publicar</button>
                                    </div>
                                    <span id="file-name-display" class="file-name-display"></span>
                                </form>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($result_posts && $result_posts->num_rows > 0): ?>
                                <?php while($post = $result_posts->fetch_assoc()): ?>
                                    <div class="post-card" id="post-<?php echo $post['id']; ?>">
                                        <?php $user_id = $id_usuario_logado; include 'templates/post_template.php'; ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="post-card"><p>Este utilizador ainda não publicou nada.</p></div>
                            <?php endif; ?>
                            <?php break; ?>
                            
                    <?php endswitch; // Fim do switch $active_page ?>
                <?php endif; // Fim do else $pode_ver_conteudo ?>

            </main>
        </div>
        <div class="report-modal-overlay is-hidden" id="report-modal-overlay"></div>
        <?php include 'templates/footer.php'; ?>

    <?php else: ?>

        <?php // --- VISUALIZAÇÃO PÚBLICA (NÃO LOGADO) --- ?>
        <div class="public-view-header">
            <a href="index.php" class="logo">
                <i class="fas fa-home"></i> <?php // ?>
                <span class="logo-text"><?php echo htmlspecialchars($config['site_nome']); ?></span>
            </a>
            <div class="public-header-actions">
                <a href="login.php" class="login-link-public">Entrar</a>
                <?php if (isset($config['permite_cadastro']) && $config['permite_cadastro'] == '1'): ?>
                    <a href="cadastro.php" class="register-btn-public">Criar nova conta</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="main-content-public">
            <div class="profile-preview-card">
                <div class="profile-avatar-large">
                    <?php if (!empty($perfil_data['foto_perfil_url'])): ?>
                        <img src="<?php echo htmlspecialchars($perfil_data['foto_perfil_url']); ?>" alt="Foto de Perfil">
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
                </div>
                <div class="profile-header-info">
                    <h1><?php echo htmlspecialchars($perfil_data['nome'] . ' ' . $perfil_data['sobrenome']); ?></h1>
                    <p>@<?php echo htmlspecialchars($perfil_data['nome_de_usuario']); ?></p>
                </div>
            </div>

            <div class="public-prompt-card">
                <h2>Entre ou cadastre-se para ver o perfil completo</h2>
                <p>Conecte-se com os seus amigos, familiares e outras pessoas que você talvez conheça.</p>
                <div class="public-prompt-actions">
                    <a href="login.php" class="primary-btn">Entrar</a>
                    <?php if (isset($config['permite_cadastro']) && $config['permite_cadastro'] == '1'): ?>
                        <span>ou</span>
                        <a href="cadastro.php" class="secondary-btn">Criar nova conta</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
    <?php endif; // Fim do if $is_logged_in ?>

    <?php 
    // Fechando conexões
    $stmt_perfil->close();
    if (isset($stmt_posts) && $stmt_posts) $stmt_posts->close();
    $conn->close();
    ?>
</body>
</html>