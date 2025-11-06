<?php
require_once 'admin_auth.php';
require_once __DIR__ . '/../../config/database.php';

// Pega os valores dos filtros da URL (se existirem)
$busca = $_GET['busca'] ?? '';
$status_filter = $_GET['status'] ?? '';

// ATUALIZAÇÃO: Adicionamos um sub-select para contar o número de edições de cada comentário.
$sql = "SELECT
            c.id, c.conteudo_texto, c.data_comentario, c.status, c.id_postagem,
            u.nome, u.sobrenome,
            (SELECT COUNT(id) FROM Comentarios_Edicoes WHERE id_comentario = c.id) as total_edicoes
        FROM Comentarios AS c
        JOIN Usuarios AS u ON c.id_usuario = u.id";

$where_clauses = [];
$params = [];
$types = '';

if (!empty($busca)) {
    $where_clauses[] = "c.conteudo_texto LIKE ?";
    $busca_param = "%" . $busca . "%";
    array_push($params, $busca_param);
    $types .= 's';
}

if (!empty($status_filter)) {
    $where_clauses[] = "c.status = ?";
    array_push($params, $status_filter);
    $types .= 's';
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY c.data_comentario DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Comentários - Painel Admin</title>
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
        <a href="index.php" class="admin-back-button"><i class="fas fa-arrow-left"></i> Voltar ao Painel</a>

        <div class="admin-card">
            <h1>Gerenciar Comentários</h1>
            <p>Aqui você pode visualizar e moderar todos os comentários e respostas do site.</p>
        </div>

        <div class="filter-bar">
            <form action="admin_comentarios.php" method="GET">
                <input type="text" name="busca" placeholder="Buscar no conteúdo do comentário..." value="<?php echo htmlspecialchars($busca); ?>">
                <select name="status">
                    <option value="">Todos os Status</option>
                    <option value="ativo" <?php echo ($status_filter === 'ativo') ? 'selected' : ''; ?>>Ativo</option>
                    <option value="inativo" <?php echo ($status_filter === 'inativo') ? 'selected' : ''; ?>>Inativo (Oculto)</option>
                    <option value="excluido_pelo_usuario" <?php echo ($status_filter === 'excluido_pelo_usuario') ? 'selected' : ''; ?>>Excluído pelo Usuário</option>
                </select>
                <button type="submit" class="filter-btn">Filtrar</button>
            </form>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Comentário</th>
                        <th>Autor</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($comment = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $comment['id']; ?></td>
                                <td class="comment-content-cell">
                                    <?php echo htmlspecialchars(mb_strimwidth($comment['conteudo_texto'], 0, 100, "...")); ?>
                                    <a href="../postagem.php?id=<?php echo $comment['id_postagem']; ?>#comment-wrapper-<?php echo $comment['id']; ?>" target="_blank" title="Ver no site"><i class="fas fa-external-link-alt"></i></a>
                                </td>
                                <td><?php echo htmlspecialchars($comment['nome'] . ' ' . $comment['sobrenome']); ?></td>
                                <td><span class="status-tag status-<?php echo strtolower($comment['status']); ?>"><?php echo ucfirst(str_replace('_', ' ', $comment['status'])); ?></span></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($comment['data_comentario'])); ?></td>
                                <td class="actions-cell">
                                    <?php if ($comment['status'] === 'ativo'): ?>
                                        <a href="../api/admin/toggle_comment_status.php?id=<?php echo $comment['id']; ?>" title="Ocultar Comentário" onclick="return confirm('Tem certeza que deseja ocultar este comentário?');">
                                            <i class="fas fa-eye-slash"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="../api/admin/toggle_comment_status.php?id=<?php echo $comment['id']; ?>" title="Reativar Comentário" onclick="return confirm('Tem certeza que deseja reativar este comentário?');">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($comment['total_edicoes'] > 0): ?>
                                        <a href="admin_historico_comentario.php?id=<?php echo $comment['id']; ?>" title="Ver Histórico de Edições (<?php echo $comment['total_edicoes']; ?>)">
                                            <i class="fas fa-history"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nenhum comentário encontrado com os filtros aplicados.</td>
                        </tr>
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
$stmt->close();
$conn->close();
?>