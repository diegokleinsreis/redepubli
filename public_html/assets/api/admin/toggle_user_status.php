<?php
// Inclui a "guarita de segurança" para garantir que apenas um admin possa executar este script.
require_once __DIR__ . '/../../admin/admin_auth.php';
// Inclui a conexão com o banco de dados.
require_once __DIR__ . '/../../../config/database.php';

// Pega os IDs da URL
$user_id_to_toggle = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$denuncia_id = isset($_GET['denuncia_id']) ? (int)$_GET['denuncia_id'] : 0;

// Regra de segurança: impede que um admin suspenda a si mesmo.
if ($user_id_to_toggle === $_SESSION['user_id']) {
    die("Você não pode alterar o status da sua própria conta.");
}

if ($user_id_to_toggle > 0) {
    // 1. Busca o status atual do usuário.
    $sql_check = "SELECT status FROM Usuarios WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $user_id_to_toggle);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if ($user = $result->fetch_assoc()) {
        // 2. Decide qual será o novo status.
        $new_status = ($user['status'] === 'ativo') ? 'suspenso' : 'ativo';

        // 3. Atualiza o status do usuário no banco de dados.
        $sql_update = "UPDATE Usuarios SET status = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $user_id_to_toggle);
        $stmt_update->execute();

        // NOVA LÓGICA: Se esta ação veio de uma denúncia, atualiza o status da denúncia
        if ($denuncia_id > 0) {
            $sql_update_denuncia = "UPDATE Denuncias SET status = 'revisado' WHERE id = ?";
            $stmt_denuncia = $conn->prepare($sql_update_denuncia);
            $stmt_denuncia->bind_param("i", $denuncia_id);
            $stmt_denuncia->execute();
        }
    }
}

// 4. Redireciona para a página correta
if ($denuncia_id > 0) {
    header("Location: ../../admin/admin_denuncias_usuarios.php");
} else {
    header("Location: ../../admin/admin_usuarios.php");
}
exit();
?>