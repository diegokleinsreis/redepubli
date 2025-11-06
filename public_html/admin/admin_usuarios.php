<?php
require_once 'admin_auth.php';
require_once __DIR__ . '/../../config/database.php';

$busca = $_GET['busca'] ?? '';
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT id, nome, sobrenome, email, role, data_cadastro, status FROM Usuarios";
$where_clauses = [];
$params = [];
$types = '';

if (!empty($busca)) {
    $where_clauses[] = "(nome LIKE ? OR sobrenome LIKE ? OR email LIKE ?)";
    $busca_param = "%" . $busca . "%";
    array_push($params, $busca_param, $busca_param, $busca_param);
    $types .= 'sss';
}
if (!empty($role_filter)) {
    $where_clauses[] = "role = ?";
    array_push($params, $role_filter);
    $types .= 's';
}
if (!empty($status_filter)) {
    $where_clauses[] = "status = ?";
    array_push($params, $status_filter);
    $types .= 's';
}
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY id DESC";

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
    <title>Gerenciar Usuários - Painel Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css?v=2.3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php 
    // Usa o cabeçalho padronizado
    include 'templates/admin_header.php'; 
    include 'templates/admin_mobile_nav.php';
    ?>
    <main class="admin-main-content">
        <a href="index.php" class="admin-back-button"><i class="fas fa-arrow-left"></i> Voltar ao Painel</a>
        <div class="admin-card">
            <h1>Gerenciar Usuários</h1>
            <p>Aqui você pode visualizar e gerenciar todos os usuários cadastrados no site.</p>
        </div>
        <div class="filter-bar">
            <form action="admin_usuarios.php" method="GET">
                <input type="text" name="busca" placeholder="Buscar por nome, e-mail..." value="<?php echo htmlspecialchars($busca); ?>">
                <select name="role">
                    <option value="">Todas as Funções</option>
                    <option value="admin" <?php echo ($role_filter === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="membro" <?php echo ($role_filter === 'membro') ? 'selected' : ''; ?>>Membro</option>
                </select>
                <select name="status">
                    <option value="">Todos os Status</option>
                    <option value="ativo" <?php echo ($status_filter === 'ativo') ? 'selected' : ''; ?>>Ativo</option>
                    <option value="suspenso" <?php echo ($status_filter === 'suspenso') ? 'selected' : ''; ?>>Suspenso</option>
                </select>
                <button type="submit" class="filter-btn">Filtrar</button>
            </form>
        </div>
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome Completo</th>
                        <th>E-mail</th>
                        <th>Função</th>
                        <th>Status</th>
                        <th>Data de Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><a href="admin_editar_usuario.php?id=<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['nome'] . ' ' . $user['sobrenome']); ?></a></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><span class="role-tag role-<?php echo strtolower($user['role']); ?>"><?php echo ucfirst($user['role']); ?></span></td>
                                <td><span class="status-tag status-<?php echo strtolower($user['status']); ?>"><?php echo ucfirst($user['status']); ?></span></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($user['data_cadastro'])); ?></td>
                                <td class="actions-cell">
                                    <a href="admin_editar_usuario.php?id=<?php echo $user['id']; ?>" title="Editar Usuário"><i class="fas fa-edit"></i></a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <?php if ($user['status'] === 'ativo'): ?>
                                            <a href="../api/admin/toggle_user_status.php?id=<?php echo $user['id']; ?>" title="Suspender Usuário" onclick="return confirm('Tem certeza que deseja suspender este usuário?');"><i class="fas fa-ban"></i></a>
                                        <?php else: ?>
                                            <a href="../api/admin/toggle_user_status.php?id=<?php echo $user['id']; ?>" title="Reativar Usuário" onclick="return confirm('Tem certeza que deseja reativar este usuário?');"><i class="fas fa-user-check"></i></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Nenhum usuário encontrado com os filtros aplicados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <?php 
    // --- CORREÇÃO REAL APLICADA AQUI ---
    // Adiciona a chamada para o ficheiro JavaScript que faz o menu funcionar
    ?>
    <script src="assets/js/admin.js"></script>
</body>
</html>
<?php $stmt->close(); $conn->close(); ?>