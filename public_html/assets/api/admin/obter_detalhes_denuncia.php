<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../admin/admin_auth.php';
require_once __DIR__ . '/../../../config/database.php';

header('Content-Type: application/json');

// Função de resposta de erro para evitar repetição
function error_response($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

if (!isset($_GET['id'])) {
    error_response('ID da denúncia não fornecido.');
}

$denuncia_id = intval($_GET['id']);

function getPostDetails($conn, $post_id) {
    $sql = "SELECT p.conteudo_texto, p.url_media, p.tipo_media, u.nome, u.sobrenome, u.foto_perfil_url 
            FROM Postagens AS p 
            JOIN Usuarios AS u ON p.id_usuario = u.id 
            WHERE p.id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        return '<div class="denuncia-item denuncia-post"><p>Erro na consulta da postagem: ' . htmlspecialchars($conn->error) . '</p></div>';
    }

    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($post = $result->fetch_assoc()) {
        $avatar = $post['foto_perfil_url'] ? '../' . htmlspecialchars($post['foto_perfil_url']) : '../assets/images/avatar_padrao.png';
        
        $media_html = '';
        if (!empty($post['url_media'])) {
            $media_url = '../' . htmlspecialchars($post['url_media']);
            
            $media_content = '';
            if ($post['tipo_media'] === 'imagem') {
                $media_content = '<div class="post-image-container"><img src="'.$media_url.'" alt="Imagem da postagem denunciada"></div>';
            } elseif ($post['tipo_media'] === 'video') {
                $media_content = '<div class="post-video-container"><video controls><source src="'.$media_url.'" type="video/mp4">Seu navegador não suporta vídeos.</video></div>';
            }

            if ($media_content) {
                $media_html = '<div class="post-media-container">' . $media_content . '</div>';
            }
        }

        return '<div class="denuncia-item denuncia-post">
                    <h4>Postagem Denunciada</h4>
                    <div class="post-header">
                        <img src="'.$avatar.'" class="avatar">
                        <strong>'.htmlspecialchars($post['nome'].' '.$post['sobrenome']).'</strong>
                    </div>
                    <p>'.nl2br(htmlspecialchars($post['conteudo_texto'])).'</p>
                    '.$media_html.'
                </div>';
    }
    return '<div class="denuncia-item denuncia-post"><p>Postagem não encontrada ou já foi excluída.</p></div>';
}

function getCommentDetails($conn, $comment_id, $is_reply = false) {
    $sql = "SELECT c.conteudo_texto, u.nome, u.sobrenome, u.foto_perfil_url 
            FROM Comentarios AS c 
            JOIN Usuarios AS u ON c.id_usuario = u.id 
            WHERE c.id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        return '<div class="denuncia-item denuncia-comment"><p>Erro na consulta do comentário: ' . htmlspecialchars($conn->error) . '</p></div>';
    }

    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($comment = $result->fetch_assoc()) {
        $avatar = $comment['foto_perfil_url'] ? '../' . htmlspecialchars($comment['foto_perfil_url']) : '../assets/images/avatar_padrao.png';
        $title = $is_reply ? 'Resposta Denunciada' : 'Comentário Denunciado';
        return '<div class="denuncia-item denuncia-comment">
                    <h4>'.$title.'</h4>
                    <div class="post-header">
                        <img src="'.$avatar.'" class="avatar">
                        <strong>'.htmlspecialchars($comment['nome'].' '.$comment['sobrenome']).'</strong>
                    </div>
                    <p>'.nl2br(htmlspecialchars($comment['conteudo_texto'])).'</p>
                </div>';
    }
    return '<div class="denuncia-item denuncia-comment"><p>Comentário não encontrado ou já foi excluído.</p></div>';
}


$sql_denuncia = "SELECT * FROM Denuncias WHERE id = ?";
$stmt_denuncia = $conn->prepare($sql_denuncia);
if ($stmt_denuncia === false) {
    error_response('Erro ao preparar a consulta da denúncia: ' . $conn->error);
}
$stmt_denuncia->bind_param("i", $denuncia_id);
$stmt_denuncia->execute();
$result_denuncia = $stmt_denuncia->get_result();

if ($denuncia = $result_denuncia->fetch_assoc()) {
    $html_content = '';
    $id_conteudo = $denuncia['id_conteudo'];
    $tipo_conteudo = $denuncia['tipo_conteudo'];
    
    // --- INÍCIO DA MODIFICAÇÃO ---
    $post_id_referencia = null; // Variável para guardar o ID do post
    // --- FIM DA MODIFICAÇÃO ---

    if ($tipo_conteudo === 'post') {
        $html_content .= getPostDetails($conn, $id_conteudo);
        $post_id_referencia = $id_conteudo; // O ID do conteúdo é o ID do post
    } 
    elseif ($tipo_conteudo === 'comentario') {
        $sql_comment_info = "SELECT id_postagem, id_comentario_pai FROM Comentarios WHERE id = ?";
        $stmt_info = $conn->prepare($sql_comment_info);
        if ($stmt_info === false) {
            $html_content = '<p>Erro ao preparar busca de informações do comentário: ' . $conn->error . '</p>';
        } else {
            $stmt_info->bind_param("i", $id_conteudo);
            $stmt_info->execute();
            $info = $stmt_info->get_result()->fetch_assoc();

            if ($info) {
                $html_content .= getPostDetails($conn, $info['id_postagem']);
                if ($info['id_comentario_pai']) {
                    $html_content .= getCommentDetails($conn, $info['id_comentario_pai']);
                }
                $html_content .= getCommentDetails($conn, $id_conteudo, (bool)$info['id_comentario_pai']);
                $post_id_referencia = $info['id_postagem']; // Pegamos o ID do post ao qual o comentário pertence
            } else {
                 $html_content = '<p>Não foram encontradas informações sobre o comentário (pode ter sido excluído).</p>';
            }
        }
    }
    elseif ($tipo_conteudo === 'usuario') {
        $html_content = '<p>Denúncia de perfil de usuário. ID do Usuário: ' . $id_conteudo . '</p>';
    }

    // --- MODIFICAÇÃO NA RESPOSTA JSON ---
    echo json_encode(['success' => true, 'html' => $html_content, 'denuncia' => $denuncia, 'post_id_referencia' => $post_id_referencia]);

} else {
    error_response('Denúncia não encontrada.');
}

$conn->close();
?>