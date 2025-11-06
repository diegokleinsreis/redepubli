<?php
require_once __DIR__ . '/../../admin/admin_auth.php';
require_once __DIR__ . '/../../../config/database.php';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id > 0) {
    // Altera o status de denúncias resolvidas feitas diretamente ao perfil do usuário
    $sql_user = "UPDATE Denuncias SET status = 'excluida_pelo_adm' WHERE tipo_conteudo = 'usuario' AND id_conteudo = ? AND status != 'pendente'";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();

    // Altera o status de denúncias resolvidas feitas em posts do usuário
    $sql_posts = "UPDATE Denuncias d JOIN Postagens p ON d.id_conteudo = p.id SET d.status = 'excluida_pelo_adm' WHERE d.tipo_conteudo = 'post' AND p.id_usuario = ? AND d.status != 'pendente'";
    $stmt_posts = $conn->prepare($sql_posts);
    $stmt_posts->bind_param("i", $user_id);
    $stmt_posts->execute();

    // Altera o status de denúncias resolvidas feitas em comentários do usuário
    $sql_comments = "UPDATE Denuncias d JOIN Comentarios c ON d.id_conteudo = c.id SET d.status = 'excluida_pelo_adm' WHERE d.tipo_conteudo = 'comentario' AND c.id_usuario = ? AND d.status != 'pendente'";
    $stmt_comments = $conn->prepare($sql_comments);
    $stmt_comments->bind_param("i", $user_id);
    $stmt_comments->execute();
}

// Redireciona de volta para a página de detalhes do usuário
header("Location: ../../admin/admin_editar_usuario.php?id=" . $user_id);
exit();
?>