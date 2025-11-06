<?php
session_start();

header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não logado.']);
    exit();
}

// Inclui a conexão com o banco
require_once __DIR__ . '/../../../config/database.php';

$user_id = $_SESSION['user_id'];

// --- INÍCIO DA MODIFICAÇÃO ---

// Query para buscar as notificações do usuário logado
// Juntamos com a tabela de usuários para pegar o nome e a foto do remetente
$sql = "SELECT 
            n.id, 
            n.tipo, 
            n.id_referencia, 
            n.lida, 
            n.data_criacao,
            u.nome AS remetente_nome,
            u.sobrenome AS remetente_sobrenome,
            u.foto_perfil_url AS remetente_foto
        FROM 
            notificacoes AS n
        JOIN 
            Usuarios AS u ON n.remetente_id = u.id
        WHERE 
            n.usuario_id = ?
        ORDER BY 
            n.lida ASC, n.data_criacao DESC
        LIMIT 7"; // <-- LIMITE ALTERADO PARA 10

// --- FIM DA MODIFICAÇÃO ---

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notificacoes = [];
while ($row = $result->fetch_assoc()) {
    $notificacoes[] = $row;
}

// Conta quantas notificações não lidas o usuário tem
$sql_count = "SELECT COUNT(*) as count FROM notificacoes WHERE usuario_id = ? AND lida = 0";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $user_id);
$stmt_count->execute();
$count_result = $stmt_count->get_result()->fetch_assoc();
$nao_lidas = $count_result['count'];


echo json_encode([
    'success' => true,
    'notificacoes' => $notificacoes,
    'nao_lidas' => $nao_lidas
]);

$stmt->close();
$conn->close();
?>