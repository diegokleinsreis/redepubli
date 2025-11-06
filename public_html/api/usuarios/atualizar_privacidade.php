<?php
session_start();

// Resposta padrão em JSON para o JavaScript
header('Content-Type: application/json');

// 1. Verificações de Segurança e Sessão
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso negado. Você precisa estar logado.']);
    exit();
}

require_once __DIR__ . '/../../../config/database.php';
$user_id = $_SESSION['user_id'];

// 2. Processa os valores do formulário
$perfil_privado = isset($_POST['perfil_privado']) ? 1 : 0;

// --- INÍCIO DA MODIFICAÇÃO ---
$privacidade_amigos = $_POST['privacidade_amigos'] ?? 'amigos'; // Pega o valor do novo campo

// Validação de segurança para garantir que o valor é um dos permitidos
if (!in_array($privacidade_amigos, ['todos', 'amigos', 'ninguem'])) {
    $privacidade_amigos = 'amigos'; // Se o valor for inválido, define um padrão seguro
}
// --- FIM DA MODIFICAÇÃO ---


try {
    // 3. Atualiza as colunas no banco de dados
    // --- ALTERAÇÃO NA QUERY SQL ---
    $sql = "UPDATE Usuarios SET perfil_privado = ?, privacidade_amigos = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    // --- ALTERAÇÃO NO BIND_PARAM ---
    $stmt->bind_param("isi", $perfil_privado, $privacidade_amigos, $user_id);

    if (!$stmt->execute()) {
        throw new Exception("Ocorreu um erro ao atualizar a sua configuração de privacidade.");
    }

    // 4. Envia a resposta de sucesso
    echo json_encode(['success' => true, 'message' => 'Configuração de privacidade atualizada com sucesso!']);

} catch (Exception $e) {
    // Se ocorrer um erro, envia a mensagem
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>