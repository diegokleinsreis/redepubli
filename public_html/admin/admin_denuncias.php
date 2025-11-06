<?php
require_once 'admin_auth.php';
require_once __DIR__ . '/../../config/database.php';

// --- NOVA LÓGICA DE ABAS ---
// Define qual aba está ativa. O padrão é 'conteudo'.
$active_tab = $_GET['tab'] ?? 'conteudo';
// --- FIM DA NOVA LÓGICA ---
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Denúncias - Painel Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css?v=2.5">
    <link rel="stylesheet" href="assets/css/components/_admin_modal.css?v=1.0">
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
            <h1>Gerenciar Denúncias</h1>
            <p>Revise e tome ações sobre o conteúdo e perfis denunciados pelos usuários.</p>
        </div>

        <nav class="admin-tabs">
            <a href="admin_denuncias.php?tab=conteudo" class="<?php echo ($active_tab === 'conteudo') ? 'active' : ''; ?>">
                Denúncias de Conteúdo
            </a>
            <a href="admin_denuncias.php?tab=usuarios" class="<?php echo ($active_tab === 'usuarios') ? 'active' : ''; ?>">
                Denúncias de Usuários
            </a>
        </nav>
        <?php // --- INÍCIO DO CONTEÚDO DINÂMICO --- ?>
        <?php if ($active_tab === 'usuarios'): ?>
            
            <?php // --- LÓGICA E HTML DO ANTIGO 'admin_denuncias_usuarios.php' ---
            
            // Query para buscar denúncias de USUÁRIOS
            $sql = "SELECT 
                        d.id,
                        d.id_conteudo AS id_usuario_denunciado,
                        d.motivo,
                        d.data_denuncia,
                        d.status,
                        u_denunciante.id AS denunciante_id,
                        u_denunciante.nome AS denunciante_nome,
                        u_denunciante.sobrenome AS denunciante_sobrenome,
                        u_denunciado.nome AS denunciado_nome,
                        u_denunciado.sobrenome AS denunciado_sobrenome,
                        u_denunciado.status AS denunciado_status
                    FROM Denuncias AS d
                    JOIN Usuarios AS u_denunciante ON d.id_usuario_denunciou = u_denunciante.id
                    JOIN Usuarios AS u_denunciado ON d.id_conteudo = u_denunciado.id
                    WHERE d.tipo_conteudo = 'usuario'
                    ORDER BY d.data_denuncia DESC";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Perfil Denunciado</th>
                            <th>Denunciado por</th>
                            <th>Motivo</th>
                            <th>Data</th>
                            <th>Status Denúncia</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($denuncia = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $denuncia['id']; ?></td>
                                    <td><a href="admin_editar_usuario.php?id=<?php echo $denuncia['id_usuario_denunciado']; ?>" target="_blank"><?php echo htmlspecialchars($denuncia['denunciado_nome'] . ' ' . $denuncia['denunciado_sobrenome']); ?></a></td>
                                    <td><a href="admin_editar_usuario.php?id=<?php echo $denuncia['denunciante_id']; ?>" target="_blank"><?php echo htmlspecialchars($denuncia['denunciante_nome'] . ' ' . $denuncia['denunciante_sobrenome']); ?></a></td>
                                    <td><?php echo htmlspecialchars($denuncia['motivo']); ?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($denuncia['data_denuncia'])); ?></td>
                                    <td><span class="status-tag status-<?php echo strtolower($denuncia['status']); ?>"><?php echo ucfirst($denuncia['status']); ?></span></td>
                                    <td class="actions-cell">
                                        <?php if($denuncia['status'] === 'pendente'): ?>
                                            <a href="../api/admin/atualizar_status_denuncia.php?id=<?php echo $denuncia['id']; ?>&status=revisado" title="Marcar Denúncia como Revisada" onclick="return confirm('Marcar esta denúncia como REVISADA?');"><i class="fas fa-check"></i></a>
                                            <a href="../api/admin/atualizar_status_denuncia.php?id=<?php echo $denuncia['id']; ?>&status=ignorado" title="Ignorar Denúncia" onclick="return confirm('IGNORAR esta denúncia?');"><i class="fas fa-times"></i></a>
                                            <?php $link_suspender = "../api/admin/toggle_user_status.php?id=" . $denuncia['id_usuario_denunciado'] . "&denuncia_id=" . $denuncia['id']; ?>
                                            <?php if($denuncia['denunciado_status'] === 'ativo'): ?>
                                                <a href="<?php echo $link_suspender; ?>" title="Suspender Usuário" onclick="return confirm('SUSPENDER o usuário denunciado? A denúncia será marcada como revisada.');"><i class="fas fa-user-slash"></i></a>
                                            <?php else: ?>
                                                <a href="<?php echo $link_suspender; ?>" title="Reativar Usuário"><i class="fas fa-user-check"></i></a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span>-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">Nenhuma denúncia de usuário encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php $stmt->close(); ?>

        <?php else: ?>

            <?php // --- LÓGICA E HTML DO ANTIGO 'admin_denuncias.php' (CONTEÚDO) ---
            
            // Query para buscar denúncias de CONTEÚDO
            $sql = "SELECT 
                        d.id,
                        d.tipo_conteudo,
                        d.id_conteudo,
                        d.motivo,
                        d.data_denuncia,
                        d.status,
                        u_denunciante.id as denunciante_id,
                        u_denunciante.nome AS denunciante_nome,
                        u_denunciante.sobrenome AS denunciante_sobrenome,
                        u_denunciado.id AS denunciado_id,
                        u_denunciado.nome AS denunciado_nome,
                        u_denunciado.sobrenome AS denunciado_sobrenome
                    FROM Denuncias AS d
                    JOIN Usuarios AS u_denunciante ON d.id_usuario_denunciou = u_denunciante.id
                    LEFT JOIN Postagens p ON d.id_conteudo = p.id AND d.tipo_conteudo = 'post'
                    LEFT JOIN Comentarios c ON d.id_conteudo = c.id AND d.tipo_conteudo = 'comentario'
                    LEFT JOIN Usuarios u_denunciado ON u_denunciado.id = COALESCE(p.id_usuario, c.id_usuario, IF(d.tipo_conteudo = 'usuario', d.id_conteudo, NULL))
                    WHERE d.status = 'pendente' AND d.tipo_conteudo IN ('post', 'comentario')
                    GROUP BY d.id
                    ORDER BY d.data_denuncia DESC";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Denunciante</th>
                            <th>Denunciado</th>
                            <th>Motivo</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($denuncia = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $denuncia['id']; ?></td>
                                    <td><a href="admin_editar_usuario.php?id=<?php echo $denuncia['denunciante_id']; ?>" target="_blank"><?php echo htmlspecialchars($denuncia['denunciante_nome'] . ' ' . $denuncia['denunciante_sobrenome']); ?></a></td>
                                    <td>
                                        <?php if ($denuncia['denunciado_id']): ?>
                                            <a href="admin_editar_usuario.php?id=<?php echo $denuncia['denunciado_id']; ?>" target="_blank"><?php echo htmlspecialchars($denuncia['denunciado_nome'] . ' ' . $denuncia['denunciado_sobrenome']); ?></a>
                                        <?php else: ?>
                                            <span>Usuário não encontrado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($denuncia['motivo']); ?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($denuncia['data_denuncia'])); ?></td>
                                    <td><span class="status-tag status-<?php echo strtolower($denuncia['status']); ?>"><?php echo ucfirst($denuncia['status']); ?></span></td>
                                    <td class="actions-cell">
                                        <button class="action-btn view-btn" data-denuncia-id="<?php echo $denuncia['id']; ?>">
                                            <i class="fas fa-eye"></i> Ver Conteúdo
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">Nenhuma denúncia de conteúdo pendente encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php $stmt->close(); ?>

        <?php endif; // --- FIM DO CONTEÚDO DINÂMICO --- ?>

    </main>

    <div id="denunciaModal" class="admin-modal">
        <div class="admin-modal-content">
            <span class="admin-modal-close">&times;</span>
            
            <div class="admin-modal-header">
                <h2>Detalhes da Denúncia</h2>
                <div id="admin-modal-header-actions"></div>
            </div>

            <div class="admin-modal-body">
                <div id="denunciaConteudo">
                    <p>Carregando...</p>
                </div>
            </div>
            
            <div id="denunciaAcoes" class="admin-modal-actions">
                </div>
            </div>
    </div>

    <script src="assets/js/admin.js?v=2.4"></script>

</body>
</html>
<?php 
$conn->close();
?>