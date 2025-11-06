<?php
session_start();

// 1. Verificações de Segurança
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$post_id_to_delete = $_POST['post_id'] ?? 0;
$user_id = $_SESSION['user_id'];

if ($post_id_to_delete <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID da postagem inválido.']);
    exit();
}

// 2. Confirma se o post pertence ao usuário logado
$sql_check_owner = "SELECT id FROM Postagens WHERE id = ? AND id_usuario = ?";
$stmt_check = $conn->prepare($sql_check_owner);
$stmt_check->bind_param("ii", $post_id_to_delete, $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 1) {
    // 3. O usuário é o dono, então faz o "soft delete"
    // Apenas mudamos o status, mantendo o registro no banco.
    $sql_delete = "UPDATE Postagens SET status = 'excluido_pelo_usuario' WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $post_id_to_delete);

    if ($stmt_delete->execute()) {
        // Sucesso!
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } else {
        // Erro no banco de dados
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar o banco de dados.']);
    }
    $stmt_delete->close();
} else {
    // 4. Falha de segurança: Tentativa de excluir post de outra pessoa
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Você não tem permissão para excluir esta postagem.']);
}

$stmt_check->close();
$conn->close();
?>