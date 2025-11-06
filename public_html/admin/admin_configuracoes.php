<?php
// 1. GUARITA DE SEGURANÇA E CONEXÃO
require_once 'admin_auth.php'; // Garante que só o admin veja
require_once __DIR__ . '/../../config/database.php'; // Puxa $conn e $config

// 2. BUSCAR DADOS ATUALIZADOS
// Embora $config já exista, é uma boa prática buscar os dados frescos
// na página de edição para garantir que estamos vendo a última versão.
$sql = "SELECT chave, valor FROM Configuracoes";
$result = $conn->query($sql);
$configs_db = [];
while ($row = $result->fetch_assoc()) {
    $configs_db[$row['chave']] = $row['valor'];
}

// Helper para buscar o valor de forma segura
function getConfigValue($key, $configs_array) {
    return isset($configs_array[$key]) ? htmlspecialchars($configs_array[$key]) : '';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações Gerais - Painel Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css?v=2.91"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <?php include 'templates/admin_header.php'; ?>
    <?php include 'templates/admin_mobile_nav.php'; ?>

    <div class="main-layout">
        <?php include 'templates/admin_sidebar.php'; ?>

        <main class="main-content">
            <div class="admin-card">
                <h1><i class="fas fa-cogs"></i> Configurações Gerais do Site</h1>
                <p>Aqui você define as variáveis globais do seu template "White-Label".</p>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" style="margin: 20px 0; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px; border-radius: 6px; font-size: 0.9em;">
                    Configurações atualizadas com sucesso!
                </div>
            <?php endif; ?>

            <div class="admin-card">
                
                <form class="admin-form" action="../api/admin/atualizar_configuracoes.php" method="POST">
                    
                    <h2><i class="fas fa-info-circle"></i> Informações Principais</h2>
                    <div class="form-group">
                        <label for="site_nome">Nome do Site</label>
                        <input type="text" id="site_nome" name="site_nome" value="<?php echo getConfigValue('site_nome', $configs_db); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="site_descricao">Descrição Curta do Site (Slogan)</label>
                        <input type="text" id="site_descricao" name="site_descricao" value="<?php echo getConfigValue('site_descricao', $configs_db); ?>">
                    </div>
                    <div class="form-group">
                        <label for="site_url">URL Completa do Site (com http://)</label>
                        <input type="url" id="site_url" name="site_url" value="<?php echo getConfigValue('site_url', $configs_db); ?>" placeholder="ex: https://www.seusite.com.br">
                    </div>
                    <div class="form-group">
                        <label for="email_contato">E-mail de Contato/Suporte</label>
                        <input type="email" id="email_contato" name="email_contato" value="<?php echo getConfigValue('email_contato', $configs_db); ?>">
                    </div>

                    <hr>

                    <h2><i class="fas fa-toggle-on"></i> Módulos e Funções</h2>
                    
                    <div class="form-group switch-group">
                        <label for="modo_manutencao">Modo de Manutenção</label>
                        <p class="form-group-description">Se ativado, apenas Admins poderão ver o site. Visitantes verão a página "<?php echo htmlspecialchars($config['site_nome']); ?> em construção".</p>
                        <label class="switch">
                            <input type="checkbox" id="modo_manutencao" name="modo_manutencao" value="1" <?php echo (getConfigValue('modo_manutencao', $configs_db) == '1') ? 'checked' : ''; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    
                    <div class="form-group switch-group" style="padding-bottom: 20px; border-bottom: none;">
                        <label for="permite_cadastro">Permitir Novos Cadastros</label>
                        <p class="form-group-description">Se desativado, o botão "Criar nova conta" na página de login será escondido.</p>
                        <label class="switch">
                            <input type="checkbox" id="permite_cadastro" name="permite_cadastro" value="1" <?php echo (getConfigValue('permite_cadastro', $configs_db) == '1') ? 'checked' : ''; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <hr>
                    
                    <h2><i class="fas fa-code"></i> Configurações de Desenvolvimento</h2>

                    <div class="form-group switch-group">
                        <label for="modo_dev">Modo Desenvolvedor (Forçar Limpeza de Cache)</label>
                        <p class="form-group-description">Se ativado, o site irá forçar o recarregamento dos arquivos CSS e JS a cada página (útil para testes de design). Desative para melhor performance no site "ao vivo".</p>
                        <label class="switch">
                            <input type="checkbox" id="modo_dev" name="modo_dev" value="1" <?php echo (getConfigValue('modo_dev', $configs_db) == '1') ? 'checked' : ''; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="versao_assets">Versão dos Assets (Cache)</label>
                        <input type="text" id="versao_assets" name="versao_assets" value="<?php echo getConfigValue('versao_assets', $configs_db); ?>" required>
                        <small>Quando o "Modo Desenvolvedor" está desligado, o site usará este número (ex: 1.0.0, 1.0.1) para controlar o cache. Mude este número para forçar todos os utilizadores a baixarem os novos arquivos CSS/JS após uma atualização.</small>
                    </div>
                    <hr>
                    
                    <button type="submit" class="filter-btn">Salvar Configurações</button>
                </form>
            </div>
        </main>
    </div>
    
    <script src="assets/js/admin.js"></script>
</body>
</html>