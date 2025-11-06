<?php
require_once __DIR__ . '/../../admin/admin_auth.php';
require_once __DIR__ . '/../../../config/database.php';

$comment_id_to_toggle = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$denuncia_id = isset($_GET['denuncia_id']) ? (int)$_GET['denuncia_id'] : 0;

if ($comment_id_to_toggle > 0) {
    $sql_check = "SELECT status FROM Comentarios WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $comment_id_to_toggle);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if ($comment = $result->fetch_assoc()) {
        $new_status = ($comment['status'] === 'ativo') ? 'inativo' : 'ativo';

        $sql_update = "UPDATE Comentarios SET status = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $comment_id_to_toggle);
        $stmt_update->execute();

        // NOVA LÓGICA: Se uma denúncia originou esta ação, marque-a como revisada.
        if ($denuncia_id > 0) {
            $sql_update_denuncia = "UPDATE Denuncias SET status = 'revisado' WHERE id = ?";
            $stmt_denuncia = $conn->prepare($sql_update_denuncia);
            $stmt_denuncia->bind_param("i", $denuncia_id);
            $stmt_denuncia->execute();
        }
    }
}

// Redirecionamento condicional
if ($denuncia_id > 0) {
    header("Location: ../../admin/admin_denuncias.php");
} else {
    header("Location: ../../admin/admin_comentarios.php");
}
exit();
?>