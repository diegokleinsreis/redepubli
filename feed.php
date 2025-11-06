<?php
// 1. CARREGA A CONFIGURAÇÃO, SESSÃO E VERIFICA O MODO MANUTENÇÃO
// Esta é a correção de bug: esta linha TEM de vir antes da verificação de login.
require_once __DIR__ . '/../config/database.php';

// 2. AGORA VERIFICA SE O UTILIZADOR ESTÁ LOGADO
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
// O restante do script continua como estava...

// --- ATUALIZAÇÃO DA QUERY SQL ---
// Adicionamos as novas colunas 'tipo_media' e renomeamos 'url_imagem' para 'url_media'
$sql_posts = "SELECT
                p.id, p.conteudo_texto, p.data_postagem, p.url_media, p.tipo_media,
                u.id AS autor_id, u.nome, u.sobrenome, u.foto_perfil_url,
                (SELECT COUNT(*) FROM Curtidas WHERE id_postagem = p.id) AS total_curtidas,
                (SELECT COUNT(*) FROM Curtidas WHERE id_postagem = p.id AND id_usuario = ?) AS usuario_curtiu,
                (SELECT COUNT(*) FROM Comentarios WHERE id_postagem = p.id AND status = 'ativo') AS total_comentarios,
                (SELECT COUNT(*) FROM Postagens_Salvas WHERE id_postagem = p.id AND id_usuario = ?) AS usuario_salvou
              FROM Postagens AS p
              JOIN Usuarios AS u ON p.id_usuario = u.id
              WHERE p.status = 'ativo' AND u.status = 'ativo'
              ORDER BY p.data_postagem DESC";

$stmt_posts = $conn->prepare($sql_posts);
$stmt_posts->bind_param("ii", $user_id, $user_id);
$stmt_posts->execute();
$result_posts = $stmt_posts->get_result();

$posts_para_exibir = [];
if ($result_posts && $result_posts->num_rows > 0) {
    
    $sql_comments_preview = "SELECT c.id, c.conteudo_texto, u.nome AS autor_nome, u.foto_perfil_url,
                                (SELECT COUNT(*) FROM Curtidas_Comentarios WHERE id_comentario = c.id) as total_curtidas_comentario,
                                (SELECT COUNT(*) FROM Curtidas_Comentarios WHERE id_comentario = c.id AND id_usuario = ?) as usuario_curtiu_comentario
                             FROM Comentarios c
                             JOIN Usuarios u ON c.id_usuario = u.id
                             WHERE c.id_postagem = ? AND c.status = 'ativo'
                             ORDER BY total_curtidas_comentario DESC, c.data_comentario DESC
                             LIMIT 2";
    $stmt_comments = $conn->prepare($sql_comments_preview);

    while ($post = $result_posts->fetch_assoc()) {
        
        $stmt_comments->bind_param("ii", $user_id, $post['id']);
        $stmt_comments->execute();
        $result_comments = $stmt_comments->get_result();
        
        $preview_comments = [];
        while ($comment = $result_comments->fetch_assoc()) {
            $preview_comments[] = $comment;
        }
        
        $post['ultimos_comentarios'] = $preview_comments;
        
        $posts_para_exibir[] = $post;
    }
    $stmt_comments->close();
}

// 3. DEFINE O TÍTULO DA PÁGINA PARA O TEMPLATE
$page_title = 'Feed';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php 
    // 4. INCLUI O NOSSO NOVO <HEAD> CENTRALIZADO
    // (Ele contém o <title>, <meta>, style.css com $asset_version, e Font Awesome)
    include 'templates/head_common.php'; 
    ?>
</head>
<body>

    <?php include 'templates/header.php'; ?>
    <?php include 'templates/mobile_nav.php'; ?>

    <div class="main-content-area">
        <?php include 'templates/sidebar.php'; ?>

        <main class="feed-container">
            <div class="create-post-card">
                <?php // --- INÍCIO DA MODIFICAÇÃO DO FORMULÁRIO --- ?>
                <form action="api/postagens/criar_post.php" method="POST" enctype="multipart/form-data">
                    <textarea name="conteudo_texto" placeholder="No que você está pensando?" required></textarea>
                    
                    <div class="create-post-actions">
                        <input type="file" name="post_media" id="post_media" class="input-file" accept="image/*,video/mp4,video/webm,video/mov">
                        <label for="post_media" class="input-file-label"><i class="fas fa-camera"></i> Adicionar Foto/Vídeo</label>
                        <button type="submit" class="primary-btn-small">Publicar</button>
                    </div>
                    <span id="file-name-display" class="file-name-display"></span>
                </form>
                <?php // --- FIM DA MODIFICAÇÃO DO FORMULÁRIO --- ?>
            </div>

            <?php if (!empty($posts_para_exibir)): ?>
                <?php foreach ($posts_para_exibir as $post): ?>
                    <div class="post-card" id="post-<?php echo $post['id']; ?>">
                        <?php include 'templates/post_template.php'; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="post-card"><p>Ainda não há nenhuma postagem no feed.</p></div>
            <?php endif; ?>
            
            <?php $conn->close(); ?>
        </main>
    </div>

    <?php 
    // 5. INCLUI O FOOTER
    // (Que agora também terá o $asset_version nos scripts JS)
    include 'templates/footer.php'; 
    ?>
</body>
</html>