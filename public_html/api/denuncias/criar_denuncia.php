<?php
session_start();

// 1. Verificação de Segurança: Garante que o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Acesso negado. Você precisa estar logado para denunciar.']);
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$user_id = $_SESSION['user_id'];
$content_type = $_POST['content_type'] ?? '';
$content_id = isset($_POST['content_id']) ? (int)$_POST['content_id'] : 0;
$motivo = trim($_POST['motivo'] ?? '');

// 2. Validação dos Dados Recebidos (LINHA CORRIGIDA)
if (!in_array($content_type, ['post', 'comentario', 'usuario'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Tipo de conteúdo inválido.']);
    exit();
}
if ($content_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID de conteúdo inválido.']);
    exit();
}
if (empty($motivo)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'O motivo da denúncia não pode estar vazio.']);
    exit();
}

// 3. Prepara e Executa a Inserção no Banco de Dados
try {
    $sql = "INSERT INTO Denuncias (id_usuario_denunciou, tipo_conteudo, id_conteudo, motivo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isis", $user_id, $content_type, $content_id, $motivo);

    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Denúncia enviada com sucesso. Agradecemos sua colaboração!']);
    } else {
        throw new Exception('Erro ao registrar a denúncia no banco de dados.');
    }
    $stmt->close();
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    // Em um ambiente de produção, você poderia registrar $e->getMessage() em um log de erros.
    echo json_encode(['success' => false, 'error' => 'Ocorreu um erro interno. Por favor, tente novamente mais tarde.']);
}

$conn->close();
?>