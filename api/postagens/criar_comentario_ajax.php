<?php
session_start();

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');

// Função para padronizar as respostas de erro
function error_response($message) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => $message]);
    exit();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

// Verifica se a requisição foi feita usando o método POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    error_response("Método de requisição inválido.");
}

$conteudo_texto = trim($_POST['conteudo_texto'] ?? '');
$id_postagem = isset($_POST['id_postagem']) ? (int)$_POST['id_postagem'] : 0;
$id_usuario = (int)$_SESSION['user_id'];
$id_comentario_pai = isset($_POST['id_comentario_pai']) && !empty($_POST['id_comentario_pai']) ? (int)$_POST['id_comentario_pai'] : null;

// Validações
if (empty($conteudo_texto) || $id_postagem <= 0) {
    error_response("O comentário não pode estar vazio e precisa estar associado a uma postagem válida.");
}

// Inicia uma transação para garantir que todas as operações sejam bem-sucedidas
$conn->begin_transaction();

try {
    // 1. INSERE O NOVO COMENTÁRIO NO BANCO DE DADOS
    $sql_insert = "INSERT INTO Comentarios (id_postagem, id_usuario, id_comentario_pai, conteudo_texto) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iiis", $id_postagem, $id_usuario, $id_comentario_pai, $conteudo_texto);

    if (!$stmt_insert->execute()) {
        throw new Exception("Erro ao salvar o comentário no banco de dados.");
    }

    // Pega o ID do comentário que acabamos de inserir
    $new_comment_id = $conn->insert_id;
    $stmt_insert->close();

    // 2. BUSCA OS DADOS COMPLETOS DO COMENTÁRIO RECÉM-CRIADO PARA ENVIAR DE VOLTA
    $sql_select = "SELECT
                        c.id, c.conteudo_texto, c.data_comentario, c.id_comentario_pai,
                        u.id AS autor_id, u.nome, u.sobrenome, u.foto_perfil_url
                   FROM Comentarios AS c
                   JOIN Usuarios AS u ON c.id_usuario = u.id
                   WHERE c.id = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $new_comment_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $new_comment_data = $result->fetch_assoc();
    $stmt_select->close();

    if (!$new_comment_data) {
        throw new Exception("Não foi possível recuperar o comentário após a criação.");
    }
    
    // 3. LÓGICA PARA CRIAR A NOTIFICAÇÃO (opcional, mas bom manter)
    $sql_post_autor = "SELECT id_usuario FROM Postagens WHERE id = ?";
    $stmt_post_autor = $conn->prepare($sql_post_autor);
    $stmt_post_autor->bind_param("i", $id_postagem);
    $stmt_post_autor->execute();
    $post_autor_id = $stmt_post_autor->get_result()->fetch_assoc()['id_usuario'];
    $stmt_post_autor->close();

    if ($post_autor_id != $id_usuario) {
        $tipo_notificacao = 'comentario_post';
        $sql_notificacao = "INSERT INTO notificacoes (usuario_id, remetente_id, tipo, id_referencia) VALUES (?, ?, ?, ?)";
        $stmt_notificacao = $conn->prepare($sql_notificacao);
        $stmt_notificacao->bind_param("iisi", $post_autor_id, $id_usuario, $tipo_notificacao, $id_postagem);
        $stmt_notificacao->execute();
        $stmt_notificacao->close();
    }
    
    // Se tudo correu bem, confirma as operações
    $conn->commit();

    // 4. ENVIA A RESPOSTA DE SUCESSO COM OS DADOS DO NOVO COMENTÁRIO EM JSON
    echo json_encode(['success' => true, 'comment' => $new_comment_data]);

} catch (Exception $e) {
    // Se algo deu errado, desfaz todas as operações
    $conn->rollback();
    error_response($e->getMessage());
}

$conn->close();
?>