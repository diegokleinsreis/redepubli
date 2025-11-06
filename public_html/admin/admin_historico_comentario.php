<?php
require_once 'admin_auth.php';
require_once __DIR__ . '/../../config/database.php';

$comment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($comment_id <= 0) {
    die("ID de comentário inválido.");
}

// Busca os dados do comentário original para exibir o texto atual
$sql_comment = "SELECT c.conteudo_texto, u.nome, u.sobrenome 
                FROM Comentarios c 
                JOIN Usuarios u ON c.id_usuario = u.id 
                WHERE c.id = ?";
$stmt_comment = $conn->prepare($sql_comment);
$stmt_comment->bind_param("i", $comment_id);
$stmt_comment->execute();
$comment_result = $stmt_comment->get_result()->fetch_assoc();
$stmt_comment->close();

// Busca o histórico de edições do comentário
$sql_history = "SELECT conteudo_antigo, data_edicao FROM Comentarios_Edicoes WHERE id_comentario = ? ORDER BY data_edicao DESC";
$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param("i", $comment_id);
$stmt_history->execute();
$history_result = $stmt_history->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Edição do Comentário #<?php echo $comment_id; ?></title>
    <link rel="stylesheet" href="assets/css/admin.css?v=1.9">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="admin-header">
        <div class="header-content">
            <a href="index.php" class="logo"><i class="fas fa-shield-alt"></i><span>Painel Administrativo</span></a>
            <nav>
                <a href="../feed.php" class="nav-link" target="_blank">Ver o Site</a>
                <a href="../api/usuarios/logout.php" class="logout-btn">Sair</a>
            </nav>
        </div>
    </header>

    <main class="admin-main-content">
        <a href="admin_comentarios.php" class="admin-back-button"><i class="fas fa-arrow-left"></i> Voltar para Comentários</a>
        
        <div class="admin-card">
            <h1><i class="fas fa-history"></i> Histórico de Edição do Comentário #<?php echo $comment_id; ?></h1>
            <p><strong>Autor:</strong> <?php echo htmlspecialchars($comment_result['nome'] . ' ' . $comment_result['sobrenome']); ?></p>
            <p><strong>Conteúdo Atual:</strong> "<?php echo nl2br(htmlspecialchars($comment_result['conteudo_texto'])); ?>"</p>
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
                        <tr><td colspan="2">Nenhuma edição anterior encontrada para este comentário.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
<?php
$stmt_history->close();
$conn->close();
?>