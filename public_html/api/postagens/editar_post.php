<?php
session_start();

// 1. Verificações de Segurança
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$post_id_to_edit = $_POST['post_id'] ?? 0;
$new_text = trim($_POST['new_text'] ?? '');
$user_id = $_SESSION['user_id'];

if ($post_id_to_edit <= 0 || empty($new_text)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Dados inválidos.']);
    exit();
}

// 2. Confirma se o post pertence ao usuário e pega o conteúdo antigo
$sql_check_owner = "SELECT conteudo_texto FROM Postagens WHERE id = ? AND id_usuario = ?";
$stmt_check = $conn->prepare($sql_check_owner);
$stmt_check->bind_param("ii", $post_id_to_edit, $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 1) {
    $post_data = $result_check->fetch_assoc();
    $conteudo_antigo = $post_data['conteudo_texto'];

    // 3. Salva o histórico se o conteúdo mudou
    if ($new_text !== $conteudo_antigo) {
        $sql_history = "INSERT INTO Postagens_Edicoes (id_postagem, conteudo_antigo) VALUES (?, ?)";
        $stmt_history = $conn->prepare($sql_history);
        $stmt_history->bind_param("is", $post_id_to_edit, $conteudo_antigo);
        $stmt_history->execute();
        $stmt_history->close();
    }

    // 4. Atualiza o post na tabela principal
    $sql_update = "UPDATE Postagens SET conteudo_texto = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $new_text, $post_id_to_edit);

    if ($stmt_update->execute()) {
        // Sucesso! Envia o novo texto formatado de volta para o JavaScript
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'new_text_html' => nl2br(htmlspecialchars($new_text))]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar o banco de dados.']);
    }
    $stmt_update->close();
} else {
    // 5. Falha de segurança
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Você não tem permissão para editar esta postagem.']);
}

$stmt_check->close();
$conn->close();
?>