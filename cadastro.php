<?php
// 1. CARREGA A CONFIGURAÇÃO, SESSÃO E VERIFICA O MODO MANUTENÇÃO
require_once __DIR__ . '/../config/database.php';

// 2. VERIFICA SE OS CADASTROS ESTÃO PERMITIDOS
// Se o admin desligou os cadastros, redireciona para o login.
if (!isset($config['permite_cadastro']) || $config['permite_cadastro'] == '0') {
    header("Location: login.php");
    exit();
}

// Busca bairros (lógica existente)
$sql_bairros = "SELECT id, nome FROM Bairros WHERE id_cidade = 129 ORDER BY nome ASC";
$result_bairros = $conn->query($sql_bairros);

// 3. DEFINE O TÍTULO DA PÁGINA PARA O TEMPLATE
$page_title = 'Crie sua Conta';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php 
    // 4. INCLUI O NOSSO NOVO <HEAD> CENTRALIZADO
    include 'templates/head_common.php'; 
    ?>
</head>
<body class="register-page-body">

    <a href="login.php" class="back-button"><i class="fas fa-arrow-left"></i> Voltar</a>

    <div class="container">

        <div class="form-header">
            <?php // --- ÍCONE REMOVIDO DESTA LINHA --- ?>
            <div class="header-text">
                <h1>Crie sua conta em<br><?php echo htmlspecialchars($config['site_nome']); ?></h1>
                <h2>É rápido e fácil.</h2>
            </div>
        </div>

        <form action="api/usuarios/criar_usuario.php" method="POST">
            
            <div class="input-container">
                <input type="text" name="nome" placeholder="Nome" required>
            </div>
            <div class="input-container">
                <input type="text" name="sobrenome" placeholder="Sobrenome" required>
            </div>
            <div class="input-container">
                <input type="text" name="nome_de_usuario" placeholder="Nome de Usuário (@exemplo)" required>
            </div>
            <div class="input-container">
                <label for="data_nasc">Data de Nascimento</label>
                <input type="date" id="data_nasc" name="data_nascimento" required>
            </div>
            <div class="input-container">
                <label for="bairro">Seu Bairro</label>
                <select name="id_bairro" id="bairro" required>
                    <option value="" disabled selected>Selecione seu bairro</option>
                    <?php
                    if ($result_bairros && $result_bairros->num_rows > 0) {
                        while($bairro = $result_bairros->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($bairro['id']) . '">' . htmlspecialchars($bairro['nome']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="input-container">
                <input type="email" name="email" placeholder="Seu melhor e-mail" required>
            </div>
            <div class="input-container">
                <input type="password" name="senha" placeholder="Crie uma senha" required>
            </div>
            <div class="input-container">
                <input type="password" name="confirmar_senha" placeholder="Confirme sua senha" required>
            </div>
            
            <button type="submit" class="primary-btn">Cadastrar</button>
        </form>
    </div>

</body>
</html>
<?php
$conn->close();
?>