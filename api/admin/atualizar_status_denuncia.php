<?php
require_once __DIR__ . '/../../admin/admin_auth.php';
require_once __DIR__ . '/../../../config/database.php';

$denuncia_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$novo_status = $_GET['status'] ?? '';

// Validação para garantir que o status é um dos valores permitidos
if ($denuncia_id > 0 && in_array($novo_status, ['revisado', 'ignorado'])) {
    
    // Atualiza o status da denúncia no banco de dados
    $sql_update = "UPDATE Denuncias SET status = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $novo_status, $denuncia_id);
    $stmt_update->execute();
}

// Redireciona de volta para a página de gerenciamento de denúncias
header("Location: ../../admin/admin_denuncias.php");
exit();
?>