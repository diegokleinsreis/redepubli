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

// 2. Pega os dados do formulário
$email = trim($_POST['email']);
$nome_de_usuario = trim($_POST['nome_de_usuario']);
$senha_atual = $_POST['senha_atual'];
$nova_senha = $_POST['nova_senha'];
$confirmar_nova_senha = $_POST['confirmar_nova_senha'];

try {
    // 3. Verifica se o e-mail ou nome de usuário já não estão em uso por OUTRO usuário
    $sql_check = "SELECT id FROM Usuarios WHERE (email = ? OR nome_de_usuario = ?) AND id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ssi", $email, $nome_de_usuario, $user_id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        throw new Exception("O e-mail ou nome de usuário já está em uso por outra conta.");
    }

    // 4. Atualiza o e-mail e nome de usuário
    $sql_update_info = "UPDATE Usuarios SET email = ?, nome_de_usuario = ? WHERE id = ?";
    $stmt_info = $conn->prepare($sql_update_info);
    $stmt_info->bind_param("ssi", $email, $nome_de_usuario, $user_id);
    if (!$stmt_info->execute()) {
        throw new Exception("Ocorreu um erro ao atualizar o seu e-mail e nome de usuário.");
    }

    // 5. Lógica para Alteração de Senha (só executa se o campo 'nova_senha' foi preenchido)
    if (!empty($nova_senha)) {
        // Validação dos campos de senha
        if (empty($senha_atual) || empty($confirmar_nova_senha)) {
            throw new Exception("Para alterar a senha, precisa de fornecer a senha atual e a confirmação da nova senha.");
        }
        if ($nova_senha !== $confirmar_nova_senha) {
            throw new Exception("A nova senha e a confirmação não coincidem.");
        }
        if (strlen($nova_senha) < 6) {
            throw new Exception("A nova senha deve ter no mínimo 6 caracteres.");
        }

        // Busca a senha atual no banco para verificação
        $sql_fetch_pass = "SELECT senha_hash FROM Usuarios WHERE id = ?";
        $stmt_fetch = $conn->prepare($sql_fetch_pass);
        $stmt_fetch->bind_param("i", $user_id);
        $stmt_fetch->execute();
        $user = $stmt_fetch->get_result()->fetch_assoc();

        // Verifica se a senha atual fornecida está correta
        if (!password_verify($senha_atual, $user['senha_hash'])) {
            throw new Exception("A sua senha atual está incorreta.");
        }

        // Se tudo estiver correto, cria o hash da nova senha e atualiza no banco
        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $sql_update_pass = "UPDATE Usuarios SET senha_hash = ? WHERE id = ?";
        $stmt_pass = $conn->prepare($sql_update_pass);
        $stmt_pass->bind_param("si", $nova_senha_hash, $user_id);
        if (!$stmt_pass->execute()) {
            throw new Exception("Ocorreu um erro ao atualizar a sua senha.");
        }
    }

    // Se todas as operações foram bem-sucedidas
    echo json_encode(['success' => true, 'message' => 'As informações da sua conta foram atualizadas com sucesso!']);

} catch (Exception $e) {
    // Se qualquer uma das validações falhar, envia a mensagem de erro
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>