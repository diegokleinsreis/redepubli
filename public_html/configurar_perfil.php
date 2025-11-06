<?php
// 1. CARREGA A CONFIGURAÇÃO, SESSÃO E VERIFICA O MODO MANUTENÇÃO
// Esta é a correção de bug: esta linha TEM de vir antes de tudo.
require_once __DIR__ . '/../config/database.php';

// 2. AGORA VERIFICA SE O UTILIZADOR ESTÁ LOGADO
// (O session_start() original foi removido pois já está no database.php)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// A linha 'require_once' original foi movida para o topo.

// Busca todos os dados do usuário logado, incluindo as novas colunas que criamos
$sql_user = "SELECT u.*, b.nome AS nome_bairro 
             FROM Usuarios u 
             LEFT JOIN Bairros b ON u.id_bairro = b.id
             WHERE u.id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_data = $stmt_user->get_result()->fetch_assoc();

// Busca a lista de bairros para o campo 'select'
$sql_bairros = "SELECT id, nome FROM Bairros WHERE id_cidade = 129 ORDER BY nome ASC";
$result_bairros = $conn->query($sql_bairros);

// 3. DEFINE O TÍTULO DA PÁGINA (para o head_common.php)
$page_title = 'Configurações';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php 
    // 4. INCLUI O NOSSO NOVO <HEAD> CENTRALIZADO
    // (Substitui o <head> antigo)
    include 'templates/head_common.php'; 
    ?>
</head>
<body>
    <?php include 'templates/header.php'; ?>
    <?php include 'templates/mobile_nav.php'; ?>

    <div class="main-content-area">
        <?php include 'templates/sidebar.php'; ?>

        <main class="profile-main-content">
            <div class="page-section-header">
                <h1>Configurações</h1>
                <p>Gerencie suas informações de perfil, conta e privacidade.</p>
            </div>

            <div class="settings-card">
                <h2><i class="fas fa-user-edit"></i> Configuração do Perfil</h2>
                
                <form id="form-perfil" action="api/usuarios/atualizar_perfil.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group avatar-upload-group">
                        <label>Foto de Perfil</label>
                        <div class="avatar-preview">
                            <img src="<?php echo htmlspecialchars($user_data['foto_perfil_url'] ?? 'assets/images/default-avatar.png'); ?>" alt="Sua foto de perfil" id="avatar-preview-img">
                        </div>
                        <input type="file" name="foto_perfil" id="foto_perfil" class="input-file">
                        <label for="foto_perfil" class="input-file-label"><i class="fas fa-camera"></i> Alterar Foto</label>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($user_data['nome']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="sobrenome">Sobrenome</label>
                            <input type="text" id="sobrenome" name="sobrenome" value="<?php echo htmlspecialchars($user_data['sobrenome']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="biografia">Biografia</label>
                        <textarea id="biografia" name="biografia" rows="3" placeholder="Escreva um pouco sobre você..."><?php echo htmlspecialchars($user_data['biografia']); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_nascimento">Data de Nascimento</label>
                            <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($user_data['data_nascimento']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="relacionamento">Relacionamento</label>
                            <select id="relacionamento" name="relacionamento">
                                <?php 
                                $relacionamentos = ['Não especificado', 'Solteiro(a)', 'Em um relacionamento sério', 'Casado(a)', 'Divorciado(a)'];
                                foreach ($relacionamentos as $r) {
                                    $selected = ($user_data['relacionamento'] == $r) ? 'selected' : '';
                                    echo "<option value=\"$r\" $selected>" . htmlspecialchars($r) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="id_bairro">Bairro</label>
                        <select id="id_bairro" name="id_bairro" required>
                            <?php if ($result_bairros && $result_bairros->num_rows > 0): mysqli_data_seek($result_bairros, 0); while($bairro = $result_bairros->fetch_assoc()): $selected = ($bairro['id'] == $user_data['id_bairro']) ? 'selected' : ''; echo '<option value="' . htmlspecialchars($bairro['id']) . '" ' . $selected . '>' . htmlspecialchars($bairro['nome']) . '</option>'; endwhile; endif; ?>
                        </select>
                    </div>

                    <div class="form-actions-right">
                        <button type="submit" class="primary-btn-small">Salvar Alterações do Perfil</button>
                    </div>
                </form>
            </div>

            <div class="settings-card">
                <h2><i class="fas fa-cog"></i> Configuração da Conta</h2>
                <form id="form-conta" action="api/usuarios/atualizar_conta.php" method="POST">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nome_de_usuario">Nome de Usuário</label>
                        <input type="text" id="nome_de_usuario" name="nome_de_usuario" value="<?php echo htmlspecialchars($user_data['nome_de_usuario']); ?>" required>
                    </div>

                    <hr>
                    
                    <p class="form-section-title">Alterar Senha</p>
                    <div class="form-group">
                        <label for="senha_atual">Senha Atual</label>
                        <input type="password" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nova_senha">Nova Senha</label>
                            <input type="password" id="nova_senha" name="nova_senha" placeholder="Mínimo de 6 caracteres">
                        </div>
                        <div class="form-group">
                            <label for="confirmar_nova_senha">Confirmar Nova Senha</label>
                            <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" placeholder="Repita a nova senha">
                        </div>
                    </div>

                    <div class="form-actions-right">
                        <button type="submit" class="primary-btn-small">Salvar Alterações da Conta</button>
                    </div>
                </form>
            </div>

            <div class="settings-card">
                <h2><i class="fas fa-user-secret"></i> Privacidade e Segurança</h2>
                <form id="form-privacidade" action="api/usuarios/atualizar_privacidade.php" method="POST">
                    <div class="form-group switch-group">
                        <label for="perfil_privado">Perfil Privado</label>
                        <p class="form-group-description">Se ativado, apenas seus amigos poderão ver suas postagens e informações detalhadas.</p>
                        <label class="switch">
                            <input type="checkbox" id="perfil_privado" name="perfil_privado" value="1" <?php echo ($user_data['perfil_privado'] == 1) ? 'checked' : ''; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <?php // --- INÍCIO DA MODIFICAÇÃO --- ?>
                    <div class="form-group">
                        <label for="privacidade_amigos">Quem pode ver a sua lista de amigos?</label>
                        <p class="form-group-description">Escolha quem terá permissão para ver a lista completa dos seus amigos no seu perfil.</p>
                        <select id="privacidade_amigos" name="privacidade_amigos">
                            <option value="todos" <?php echo ($user_data['privacidade_amigos'] == 'todos') ? 'selected' : ''; ?>>Todos</option>
                            <option value="amigos" <?php echo ($user_data['privacidade_amigos'] == 'amigos') ? 'selected' : ''; ?>>Apenas amigos</option>
                            <option value="ninguem" <?php echo ($user_data['privacidade_amigos'] == 'ninguem') ? 'selected' : ''; ?>>Ninguém</option>
                        </select>
                    </div>
                    <?php // --- FIM DA MODIFICAÇÃO --- ?>

                    <div class="form-actions-right">
                        <button type="submit" class="primary-btn-small">Salvar Configuração de Privacidade</button>
                    </div>
                </form>
            </div>
             <div class="settings-card danger-zone">
                <h2><i class="fas fa-exclamation-triangle"></i> Zona de Perigo</h2>
                <div class="danger-actions">
                    <div>
                        <strong>Desativar sua conta</strong>
                        <p>Sua conta será desativada, mas você poderá reativá-la fazendo login novamente.</p>
                    </div>
                    <button class="danger-btn" id="btn-desativar-conta">Desativar Conta</button>
                </div>
                 <div class="danger-actions">
                    <div>
                        <strong>Excluir sua conta</strong>
                        <p>Esta ação é permanente e não pode ser desfeita. Todos os seus dados serão apagados.</p>
                    </div>
                    <button class="danger-btn" id="btn-excluir-conta">Excluir Conta Permanentemente</button>
                </div>
            </div>

        </main>
    </div>

    <?php 
    $stmt_user->close();
    $conn->close();
    // 5. INCLUI O FOOTER (que agora terá $asset_version)
    include 'templates/footer.php'; 
    ?>
</body>
</html>