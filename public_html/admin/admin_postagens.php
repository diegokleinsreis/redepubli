<?php
require_once 'admin_auth.php';
require_once __DIR__ . '/../../config/database.php';

// Pega os valores dos filtros da URL (se existirem)
$busca = $_GET['busca'] ?? '';
$status_filter = $_GET['status'] ?? '';

// --- [NOVA LÓGICA DE FILTRO DE DATA] ---
$data_filtro = $_GET['data'] ?? ''; // Pega a data do filtro
// --- [FIM DA NOVA LÓGICA] ---


// --- ATUALIZAÇÃO DA QUERY SQL ---
// 1. Adicionamos o LEFT JOIN à nova tabela Logs_Visualizacao_Post
// 2. Adicionamos o filtro de data (se existir)
// 3. Modificamos o COUNT para total_visualizacoes
$sql = "SELECT 
            p.id, p.conteudo_texto, p.data_postagem, p.status,
            u.nome, u.sobrenome,
            (SELECT COUNT(id) FROM Postagens_Edicoes WHERE id_postagem = p.id) as total_edicoes,
            (SELECT COUNT(id) FROM Curtidas WHERE id_postagem = p.id) as total_curtidas,
            (SELECT COUNT(id) FROM Comentarios WHERE id_postagem = p.id AND status = 'ativo') as total_comentarios,
            COUNT(DISTINCT lvp.id) as total_visualizacoes
        FROM Postagens AS p
        JOIN Usuarios AS u ON p.id_usuario = u.id
        LEFT JOIN Logs_Visualizacao_Post AS lvp ON p.id = lvp.id_postagem";

$where_clauses = [];
$params = [];
$types = '';

if (!empty($busca)) {
    $where_clauses[] = "p.conteudo_texto LIKE ?";
    $busca_param = "%" . $busca . "%";
    array_push($params, $busca_param);
    $types .= 's';
}

if (!empty($status_filter)) {
    $where_clauses[] = "p.status = ?";
    array_push($params, $status_filter);
    $types .= 's';
}

// --- [NOVA LÓGICA DE FILTRO DE DATA] ---
if (!empty($data_filtro)) {
    // Filtra os logs de visualização para aquele dia específico
    $where_clauses[] = "DATE(lvp.data_visualizacao) = ?";
    array_push($params, $data_filtro);
    $types .= 's';
}
// --- [FIM DA NOVA LÓGICA] ---


if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

// Agrupamos por post para que o COUNT(lvp.id) funcione corretamente
$sql .= " GROUP BY p.id";

// Ordena por visualizações (ou data, se não houver filtro de data)
$order_by = !empty($data_filtro) ? "total_visualizacoes DESC" : "p.data_postagem DESC";
$sql .= " ORDER BY $order_by";


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
    <title>Gerenciar Postagens - Painel Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css?v=2.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <?php 
    include 'templates/admin_header.php'; 
    include 'templates/admin_mobile_nav.php';
    ?>

    <main class="admin-main-content">

        <a href="index.php" class="admin-back-button"><i class="fas fa-arrow-left"></i> Voltar ao Painel</a>

        <div class="admin-card">
            <h1>Gerenciar Postagens</h1>
            <p>Aqui você pode visualizar e moderar todas as postagens do site.</p>
        </div>

        <div class="filter-bar">
            <form action="admin_postagens.php" method="GET">
                <input type="text" name="busca" placeholder="Buscar no conteúdo da postagem..." value="<?php echo htmlspecialchars($busca); ?>">
                
                <?php // --- [CORREÇÃO DO TYPO APLICADA AQUI] --- ?>
                <input type="date" name="data" value="<?php echo htmlspecialchars($data_filtro); ?>" title="Filtrar por data de acesso" style="padding: 9px;">
                
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
                        <th>Postagem</th>
                        <th>Autor</th>
                        <th><i class="fas fa-eye"></i> Acessos</th> <?php // <-- NOVA COLUNA ?>
                        <th><i class="fas fa-thumbs-up"></i></th>
                        <th><i class="fas fa-comments"></i></th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($post = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $post['id']; ?></td>
                                <td class="comment-content-cell">
                                    <?php echo htmlspecialchars(mb_strimwidth($post['conteudo_texto'], 0, 100, "...")); ?>
                                    <a href="../postagem.php?id=<?php echo $post['id']; ?>" target="_blank" title="Ver postagem no site"><i class="fas fa-external-link-alt"></i></a>
                                </td>
                                <td><?php echo htmlspecialchars($post['nome'] . ' ' . $post['sobrenome']); ?></td>
                                
                                <?php // --- [NOVA CÉLULA DE DADOS] --- ?>
                                <td style="font-weight: bold; color: #0c2d54;"><?php echo $post['total_visualizacoes']; ?></td>

                                <td><?php echo $post['total_curtidas']; ?></td>
                                <td><?php echo $post['total_comentarios']; ?></td>
                                <td><span class="status-tag status-<?php echo strtolower($post['status']); ?>"><?php echo ucfirst(str_replace('_', ' ', $post['status'])); ?></span></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($post['data_postagem'])); ?></td>
                                <td class="actions-cell">
                                    <?php if ($post['status'] === 'ativo'): ?>
                                        <a href="../api/admin/toggle_post_status.php?id=<?php echo $post['id']; ?>" title="Ocultar Postagem (Tornar Inativo)" onclick="return confirm('Tem certeza que deseja ocultar esta postagem?');">
                                            <i class="fas fa-eye-slash"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="../api/admin/toggle_post_status.php?id=<?php echo $post['id']; ?>" title="Reativar Postagem" onclick="return confirm('Tem certeza que deseja reativar esta postagem?');">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($post['total_edicoes'] > 0): ?>
                                        <a href="admin_historico_postagem.php?id=<?php echo $post['id']; ?>" title="Ver Histórico de Edições (<?php echo $post['total_edicoes']; ?>)">
                                            <i class="fas fa-history"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">Nenhuma postagem encontrada com os filtros aplicados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <script src="assets/js/admin.js"></script>
</body>
</html>
<?php 
$stmt->close();
$conn->close();
?>