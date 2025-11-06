<?php
require_once 'admin_auth.php';
require_once __DIR__ . '/../../config/database.php';

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($post_id <= 0) {
    die("ID de postagem inválido.");
}

// Busca os dados da postagem original
$sql_post = "SELECT p.conteudo_texto, u.nome, u.sobrenome 
             FROM Postagens p 
             JOIN Usuarios u ON p.id_usuario = u.id 
             WHERE p.id = ?";
$stmt_post = $conn->prepare($sql_post);
$stmt_post->bind_param("i", $post_id);
$stmt_post->execute();
$post_result = $stmt_post->get_result()->fetch_assoc();
$stmt_post->close();

// Busca o histórico de edições
$sql_history = "SELECT conteudo_antigo, data_edicao FROM Postagens_Edicoes WHERE id_postagem = ? ORDER BY data_edicao DESC";
$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param("i", $post_id);
$stmt_history->execute();
$history_result = $stmt_history->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Edição da Postagem #<?php echo $post_id; ?></title>
    <link rel="stylesheet" href="assets/css/admin.css?v=1.9">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <?php 
    // --- ALTERAÇÃO APLICADA AQUI ---
    include 'templates/admin_header.php'; 
    include 'templates/admin_mobile_nav.php';
    ?>

    <main class="admin-main-content">
        <a href="admin_postagens.php" class="admin-back-button"><i class="fas fa-arrow-left"></i> Voltar para Postagens</a>
        
        <div class="admin-card">
            <h1><i class="fas fa-history"></i> Histórico de Edição da Postagem #<?php echo $post_id; ?></h1>
            <p><strong>Autor:</strong> <?php echo htmlspecialchars($post_result['nome'] . ' ' . $post_result['sobrenome']); ?></p>
            <p><strong>Conteúdo Atual:</strong> "<?php echo nl2br(htmlspecialchars($post_result['conteudo_texto'])); ?>"</p>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Versão Anterior do Conteúdo</th>
                        <th>Data da Edição</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($history_result && $history_result->num_rows > 0): ?>
                        <?php while($edicao = $history_result->fetch_assoc()): ?>
                            <tr>
                                <td class="comment-content-cell"><?php echo nl2br(htmlspecialchars($edicao['conteudo_antigo'])); ?></td>
                                <td><?php echo date("d/m/Y \à\s H:i:s", strtotime($edicao['data_edicao'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2">Nenhuma edição anterior encontrada para esta postagem.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php // Adiciona a chamada para o JavaScript do admin ?>
    <script src="assets/js/admin.js"></script>
</body>
</html>
<?php
$stmt_history->close();
$conn->close();
?>