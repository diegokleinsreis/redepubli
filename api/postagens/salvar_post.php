<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$post_id = $_POST['post_id'] ?? 0;
$user_id = $_SESSION['user_id'];

if ($post_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID da postagem inválido.']);
    exit();
}

// 1. Verifica se o usuário já salvou este post
$sql_check = "SELECT id FROM Postagens_Salvas WHERE id_usuario = ? AND id_postagem = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $post_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // JÁ SALVOU -> REMOVER DOS SALVOS
    $sql_delete = "DELETE FROM Postagens_Salvas WHERE id_usuario = ? AND id_postagem = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $user_id, $post_id);
    $stmt_delete->execute();
    $salvo = false;
} else {
    // AINDA NÃO SALVOU -> ADICIONAR AOS SALVOS
    $sql_insert = "INSERT INTO Postagens_Salvas (id_usuario, id_postagem) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ii", $user_id, $post_id);
    $stmt_insert->execute();
    $salvo = true;
}

// 3. Envia uma resposta de sucesso para o JavaScript
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'salvo' => $salvo
]);

$conn->close();
?>