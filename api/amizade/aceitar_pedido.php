<?php
session_start();
header('Content-Type: application/json');

// 1. Verificação de Segurança: Garante que o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso negado. Você precisa estar logado.']);
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$utilizador_logado_id = $_SESSION['user_id'];
// O ID que vem do POST é o ID da amizade (da tabela Amizades)
$amizade_id = isset($_POST['id_amizade']) ? (int)$_POST['id_amizade'] : 0;

try {
    if ($amizade_id <= 0) {
        throw new Exception("ID do pedido de amizade inválido.");
    }

    // 2. Segurança Crucial: Atualiza o status APENAS SE o pedido estiver 'pendente'
    // E SE o utilizador logado for o DESTINATÁRIO (usuario_dois_id) do pedido.
    $sql = "UPDATE Amizades 
            SET status = 'aceite' 
            WHERE id = ? 
              AND usuario_dois_id = ? 
              AND status = 'pendente'";
              
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $amizade_id, $utilizador_logado_id);
    
    if ($stmt->execute()) {
        // Verifica se a linha foi realmente alterada
        if ($stmt->affected_rows > 0) {
            // --- CORREÇÃO GRAMATICAL APLICADA AQUI ---
            echo json_encode(['success' => true, 'message' => 'Pedido de amizade aceito!']);
        } else {
            // Se affected_rows for 0, significa que o pedido não existia,
            // não pertencia a este utilizador, ou já não estava pendente.
            throw new Exception("Não foi possível aceitar este pedido. Ele pode já ter sido aceito, recusado ou não lhe pertencer.");
        }
    } else {
        throw new Exception("Ocorreu um erro no servidor ao tentar aceitar o pedido.");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>