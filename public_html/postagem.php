<?php
// 1. CARREGA A CONFIGURAÇÃO, SESSÃO E VERIFICA O MODO MANUTENÇÃO
// Esta é a correção de bug: esta linha TEM de vir antes da verificação de login.
require_once __DIR__ . '/../config/database.php';

// 2. AGORA VERIFICA SE O UTILIZADOR ESTÁ LOGADO
// (O session_start() original foi removido pois já está no database.php)
if (!isset($_SESSION['user_id'])) {
    // Guarda a URL que o utilizador estava a tentar aceder
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}
// --- FIM DA MODIFICAÇÃO ---

$user_id = $_SESSION['user_id'];
// A linha 'require_once' original estava aqui e foi movida para o topo.

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($post_id <= 0) {
    die("Postagem inválida.");
}

// --- [INÍCIO DA NOVA LÓGICA - PASSO 2 DO LOG DE VISUALIZAÇÃO] ---
// Registra a visualização desta postagem na tabela de logs
// Fazemos isto num bloco try/catch para que, se falhar, não impeça a página de carregar.
try {
    $sql_log_view = "INSERT INTO Logs_Visualizacao_Post (id_postagem, id_usuario_visualizou) VALUES (?, ?)";
    $stmt_log_view = $conn->prepare($sql_log_view);
    // $post_id vem da URL, $user_id vem da sessão
    $stmt_log_view->bind_param("ii", $post_id, $user_id);
    $stmt_log_view->execute();
    $stmt_log_view->close();
} catch (Exception $e) {
    // Se falhar (ex: post duplicado, etc.), apenas regista no log de erros do servidor
    // e continua a carregar a página normalmente.
    error_log("Falha ao registar visualização de post: " . $e->getMessage());
}
// --- [FIM DA NOVA LÓGICA] ---


// --- BUSCA OS DADOS DA POSTAGEM PRINCIPAL (QUERY ATUALIZADA) ---
$sql_post = "SELECT
                p.id, p.conteudo_texto, p.data_postagem, p.url_media, p.tipo_media,
                u.id AS autor_id, u.nome, u.sobrenome, u.foto_perfil_url,
                (SELECT COUNT(*) FROM Curtidas WHERE id_postagem = p.id) AS total_curtidas,
                (SELECT COUNT(*) FROM Curtidas WHERE id_postagem = p.id AND id_usuario = ?) AS usuario_curtiu,
                (SELECT COUNT(*) FROM Comentarios WHERE id_postagem = p.id AND status = 'ativo') AS total_comentarios,
                (SELECT COUNT(*) FROM Postagens_Salvas WHERE id_postagem = p.id AND id_usuario = ?) AS usuario_salvou
             FROM Postagens AS p
             JOIN Usuarios AS u ON p.id_usuario = u.id
             WHERE p.status = 'ativo' AND p.id = ?";

$stmt_post = $conn->prepare($sql_post);
$stmt_post->bind_param("iii", $user_id, $user_id, $post_id);
$stmt_post->execute();
$result_post = $stmt_post->get_result();
$post = $result_post->fetch_assoc();


if (!$post) {
    die("Postagem não encontrada ou indisponível.");
}

// --- BUSCA E ORGANIZA COMENTÁRIOS E RESPOSTAS ---
$sql_comments = "SELECT
                    c.id, c.conteudo_texto, c.data_comentario, c.id_comentario_pai,
                    u.id AS autor_id, u.nome, u.sobrenome, u.foto_perfil_url,
                    (SELECT COUNT(*) FROM Curtidas_Comentarios WHERE id_comentario = c.id) AS total_curtidas_comentario,
                    (SELECT COUNT(*) FROM Curtidas_Comentarios WHERE id_comentario = c.id AND id_usuario = ?) AS usuario_curtiu_comentario
                 FROM Comentarios AS c
                 JOIN Usuarios AS u ON c.id_usuario = u.id
                 WHERE c.id_postagem = ? AND c.status = 'ativo'
                 ORDER BY c.data_comentario ASC";

$stmt_comments = $conn->prepare($sql_comments);
$stmt_comments->bind_param("ii", $user_id, $post_id);
$stmt_comments->execute();
$result_comments = $stmt_comments->get_result();

$comentarios = [];
$respostas = [];
if($result_comments) {
    while ($row = $result_comments->fetch_assoc()) {
        if ($row['id_comentario_pai'] === null) {
            $comentarios[$row['id']] = $row;
        } else {
            // Guardamos o ID do comentário principal junto com a resposta
            $row['id_comentario_principal'] = $row['id_comentario_pai'];
            $respostas[$row['id_comentario_pai']][] = $row;
        }
    }
}

// 3. DEFINE O TÍTULO DA PÁGINA (para o head_common.php)
$page_title = "Postagem de " . htmlspecialchars($post['nome']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php 
    // 4. INCLUI O NOSSO NOVO <HEAD> CENTRALIZADO
    include 'templates/head_common.php'; 
    ?>
</head>
<body>

    <?php include 'templates/header.php'; ?>
    <?php include 'templates/mobile_nav.php'; ?>

    <div class="main-content-area">
        <?php include 'templates/sidebar.php'; ?>

        <main class="feed-container">

            <div class="post-card" id="post-<?php echo $post['id']; ?>">
                <?php include 'templates/post_template.php'; ?>
            </div>

            <div class="post-card comment-list-card">
                <div class="full-comment-list">
                    <?php if (!empty($comentarios)): ?>
                        <?php foreach ($comentarios as $comment_id => $comment): ?>
                            <div class="comment-item-wrapper" id="comment-wrapper-<?php echo $comment_id; ?>">
                                <div class="comment-view-mode">
                                    <div class="comment-item">
                                        <div class="comment-author-avatar"><a href="perfil.php?id=<?php echo $comment['autor_id']; ?>"><?php if (!empty($comment['foto_perfil_url'])): ?><img src="<?php echo htmlspecialchars($comment['foto_perfil_url']); ?>" alt="Foto de <?php echo htmlspecialchars($comment['nome']); ?>"><?php else: ?><i class="fas fa-user"></i><?php endif; ?></a></div>
                                        <div class="comment-content">
                                            <div class="comment-bubble"><a href="perfil.php?id=<?php echo $comment['autor_id']; ?>" class="comment-author-name"><?php echo htmlspecialchars($comment['nome'] . ' ' . $comment['sobrenome']); ?></a><p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['conteudo_texto'])); ?></p></div>
                                            <div class="comment-actions">
                                                <span class="comment-timestamp"><?php echo date("d/m H:i", strtotime($comment['data_comentario'])); ?></span>
                                                <a href="#" class="comment-like-btn <?php echo ($comment['usuario_curtiu_comentario'] > 0) ? 'active' : ''; ?>" data-comment-id="<?php echo $comment_id; ?>">Curtir</a>
                                                <a href="#" class="reply-link" data-comment-id="<?php echo $comment_id; ?>">Responder</a>
                                                <span class="comment-like-count" <?php if($comment['total_curtidas_comentario'] == 0) echo 'style="display:none;"'; ?> data-comment-id="<?php echo $comment_id; ?>"><i class="fas fa-thumbs-up"></i> <?php echo $comment['total_curtidas_comentario']; ?></span>
                                            </div>
                                        </div>
                                        <div class="comment-options"><button class="comment-options-btn"><i class="fas fa-ellipsis-h"></i></button><div class="comment-options-menu is-hidden"><?php if ($comment['autor_id'] == $user_id): ?><a href="#" class="comment-edit-btn" data-comment-id="<?php echo $comment_id; ?>">Editar</a><a href="#" class="comment-delete-btn" data-comment-id="<?php echo $comment_id; ?>">Excluir</a><?php else: ?><a href="#" class="report-btn" data-content-type="comentario" data-content-id="<?php echo $comment_id; ?>"><i class="fas fa-flag"></i> Denunciar</a><?php endif; ?></div></div>
                                    </div>
                                    <?php if (isset($respostas[$comment_id])): ?>
                                        <div class="comment-replies">
                                            <?php foreach ($respostas[$comment_id] as $reply): ?>
                                                <div id="comment-wrapper-<?php echo $reply['id']; ?>">
                                                    <div class="comment-view-mode">
                                                        <div class="comment-item is-reply">
                                                            <div class="comment-author-avatar"><a href="perfil.php?id=<?php echo $reply['autor_id']; ?>"><?php if (!empty($reply['foto_perfil_url'])): ?><img src="<?php echo htmlspecialchars($reply['foto_perfil_url']); ?>" alt="Foto de <?php echo htmlspecialchars($reply['nome']); ?>"><?php else: ?><i class="fas fa-user"></i><?php endif; ?></a></div>
                                                            <div class="comment-content">
                                                                <div class="comment-bubble"><a href="perfil.php?id=<?php echo $reply['autor_id']; ?>" class="comment-author-name"><?php echo htmlspecialchars($reply['nome'] . ' ' . $reply['sobrenome']); ?></a><p class="comment-text"><?php echo nl2br(htmlspecialchars($reply['conteudo_texto'])); ?></p></div>
                                                                <div class="comment-actions">
                                                                    <span class="comment-timestamp"><?php echo date("d/m H:i", strtotime($reply['data_comentario'])); ?></span>
                                                                    <a href="#" class="comment-like-btn <?php echo ($reply['usuario_curtiu_comentario'] > 0) ? 'active' : ''; ?>" data-comment-id="<?php echo $reply['id']; ?>">Curtir</a>
                                                                    <a href="#" class="reply-link" data-comment-id="<?php echo $comment_id; ?>">Responder</a>
                                                                    <span class="comment-like-count" <?php if($reply['total_curtidas_comentario'] == 0) echo 'style="display:none;"'; ?> data-comment-id="<?php echo $reply['id']; ?>"><i class="fas fa-thumbs-up"></i> <?php echo $reply['total_curtidas_comentario']; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="comment-options"><button class="comment-options-btn"><i class="fas fa-ellipsis-h"></i></button><div class="comment-options-menu is-hidden"><?php if ($reply['autor_id'] == $user_id): ?><a href="#" class="comment-edit-btn" data-comment-id="<?php echo $reply['id']; ?>">Editar</a><a href="#" class="comment-delete-btn" data-comment-id="<?php echo $reply['id']; ?>">Excluir</a><?php else: ?><a href="#" class="report-btn" data-content-type="comentario" data-content-id="<?php echo $reply['id']; ?>"><i class="fas fa-flag"></i> Denunciar</a><?php endif; ?></div></div>
                                                        </div>
                                                    </div>
                                                    <div class="comment-edit-form is-hidden" id="edit-form-<?php echo $reply['id']; ?>"><textarea class="comment-edit-textarea"><?php echo htmlspecialchars($reply['conteudo_texto']); ?></textarea><div class="comment-edit-actions"><button class="comment-edit-cancel" data-comment-id="<?php echo $reply['id']; ?>">Cancelar</button><button class="comment-edit-save" data-comment-id="<?php echo $reply['id']; ?>">Salvar</button></div></div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="comment-edit-form is-hidden" id="edit-form-<?php echo $comment_id; ?>"><textarea class="comment-edit-textarea"><?php echo htmlspecialchars($comment['conteudo_texto']); ?></textarea><div class="comment-edit-actions"><button class="comment-edit-cancel" data-comment-id="<?php echo $comment_id; ?>">Cancelar</button><button class="comment-edit-save" data-comment-id="<?php echo $comment_id; ?>">Salvar</button></div></div>
                                <div class="reply-form-container is-hidden" id="reply-form-<?php echo $comment_id; ?>"><form action="api/postagens/criar_comentario.php" method="POST"><input type="hidden" name="id_postagem" value="<?php echo $post['id']; ?>"><input type="hidden" name="id_comentario_pai" value="<?php echo $comment_id; ?>"><input type="text" name="conteudo_texto" class="comment-input" placeholder="Escreva sua resposta..." required><button type="submit" class="comment-submit-btn"><i class="fas fa-paper-plane"></i></button></form></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Nenhum comentário ainda. Seja o primeiro a comentar!</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <?php
    $stmt_post->close();
    if (isset($stmt_comments)) $stmt_comments->close();
    $conn->close();
    ?>

    <?php
    // 5. INCLUI O FOOTER (que agora terá $asset_version)
    include 'templates/footer.php';
    ?>
</body>
</html>