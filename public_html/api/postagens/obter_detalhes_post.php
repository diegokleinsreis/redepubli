<?php
session_start();
header('Content-Type: application/json');

// Resposta de erro padronizada
function error_response($message) {
    echo json_encode(['success' => false, 'error' => $message]);
    exit();
}

// Verifica se o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    error_response('Acesso negado. Você precisa estar logado para ver os detalhes.');
}

require_once __DIR__ . '/../../../config/database.php';

$user_id = (int)$_SESSION['user_id'];
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id <= 0) {
    error_response('ID de publicação inválido.');
}

try {
    // 1. BUSCAR OS DETALHES COMPLETOS DA PUBLICAÇÃO (sem alterações aqui)
    $sql_post = "SELECT 
                    p.id, p.conteudo_texto, p.data_postagem, p.url_media, p.tipo_media,
                    u.id AS autor_id, u.nome AS autor_nome, u.sobrenome AS autor_sobrenome, u.foto_perfil_url AS autor_foto_perfil,
                    (SELECT COUNT(*) FROM Curtidas WHERE id_postagem = p.id) AS total_curtidas,
                    (SELECT COUNT(*) FROM Comentarios WHERE id_postagem = p.id AND status = 'ativo') AS total_comentarios,
                    (SELECT COUNT(*) FROM Curtidas WHERE id_postagem = p.id AND id_usuario = ?) AS usuario_curtiu,
                    (SELECT COUNT(*) FROM Postagens_Salvas WHERE id_postagem = p.id AND id_usuario = ?) AS usuario_salvou
                 FROM Postagens AS p
                 JOIN Usuarios AS u ON p.id_usuario = u.id
                 WHERE p.id = ? AND p.status = 'ativo'";
    
    $stmt_post = $conn->prepare($sql_post);
    $stmt_post->bind_param("iii", $user_id, $user_id, $post_id);
    $stmt_post->execute();
    $result_post = $stmt_post->get_result();
    $post_data = $result_post->fetch_assoc();

    if (!$post_data) {
        error_response('Publicação não encontrada ou indisponível.');
    }

    $post_data['conteudo_texto_formatado'] = nl2br(htmlspecialchars($post_data['conteudo_texto']));

    // --- INÍCIO DA MODIFICAÇÃO ---
    // 2. BUSCAR E ESTRUTURAR OS COMENTÁRIOS E RESPOSTAS
    $sql_comments = "SELECT
                        c.id, c.conteudo_texto, c.data_comentario, c.id_postagem, c.id_comentario_pai,
                        u.id AS autor_id, u.nome AS autor_nome, u.sobrenome AS autor_sobrenome, u.foto_perfil_url AS autor_foto_perfil,
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
    while ($row = $result_comments->fetch_assoc()) {
        $row['conteudo_texto_formatado'] = nl2br(htmlspecialchars($row['conteudo_texto']));
        if ($row['id_comentario_pai'] === null) {
            // Se é um comentário principal, adiciona um array 'respostas' vazio
            $row['respostas'] = [];
            $comentarios[$row['id']] = $row;
        } else {
            // Se é uma resposta, agrupa pelo ID do comentário pai
            $respostas[$row['id_comentario_pai']][] = $row;
        }
    }

    // Adiciona as respostas aos seus respectivos comentários pais
    foreach ($respostas as $id_pai => $lista_respostas) {
        if (isset($comentarios[$id_pai])) {
            $comentarios[$id_pai]['respostas'] = $lista_respostas;
        }
    }
    // --- FIM DA MODIFICAÇÃO ---


    // 3. MONTAR E ENVIAR A RESPOSTA JSON COMPLETA
    echo json_encode([
        'success' => true,
        'post' => $post_data,
        'comentarios' => array_values($comentarios) // Retorna um array simples, não associativo
    ]);

} catch (Exception $e) {
    error_response('Ocorreu um erro no servidor: ' . $e->getMessage());
}

$conn->close();
?>