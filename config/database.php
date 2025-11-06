<?php
$servername = "localhost";
$username = "klscom_adm";
$password = "Di@56741634";
$dbname = "klscom_social";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Falha na conexÃ£o: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// --- INÃCIO DA LÃ“GICA DO TEMPLATE (NOVO CÃ“DIGO ADICIONADO) ---

// 1. CARREGA AS CONFIGURAÃ‡Ã•ES DO SITE
$config = [];
$sql_config = "SELECT chave, valor FROM Configuracoes";
$result_config = $conn->query($sql_config);

if ($result_config) {
    while ($row_config = $result_config->fetch_assoc()) {
        $config[$row_config['chave']] = $row_config['valor'];
    }
    $result_config->free();
} else {
    // Se a tabela 'Configuracoes' nÃ£o puder ser lida, o site nÃ£o pode continuar.
    die("Erro fatal: NÃ£o foi possÃvel carregar as configuraÃ§Ãµes do site.");
}

// 2. DEFINE A VERSÃƒO GLOBAL DOS ASSETS (CSS/JS)
// Esta Ã© a sua lÃ³gica do "Modo Dev"
global $asset_version;
if (isset($config['modo_dev']) && $config['modo_dev'] == '1') {
    // Modo Desenvolvedor: ForÃ§a a limpeza do cache a cada recarregamento
    $asset_version = time();
} else {
    // Modo ProduÃ§Ã£o: Usa a versÃ£o definida no painel admin
    $asset_version = $config['versao_assets'] ?? '1.0.0';
}


// 3. VERIFICA O MODO DE MANUTENÃ‡ÃƒO
// Garante que a sessÃ£o seja iniciada para verificar o 'user_role'
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o modo manutenÃ§Ã£o estÃ¡ LIGADO
if (isset($config['modo_manutencao']) && $config['modo_manutencao'] == '1') {
    
    // Verifica se o utilizador NÃƒO Ã© um admin
    $is_admin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
    
    // Pega o nome do script atual (ex: feed.php, login.php)
    $pagina_atual = basename($_SERVER['PHP_SELF']);

    // Lista de pÃ¡ginas "pÃºblicas" permitidas durante a manutenÃ§Ã£o (para o admin poder logar)
    $paginas_permitidas = [
        'login.php',                // PÃ¡gina de login
        'processa_login.php',       // API de login
        'site_em_construcao.php'    // A prÃ³pria pÃ¡gina de manutenÃ§Ã£o
    ];

    // Verifica se a pÃ¡gina atual NÃƒO estÃ¡ na lista de permitidas
    // E se o utilizador NÃƒO Ã© um admin
    if (!$is_admin && !in_array($pagina_atual, $paginas_permitidas)) {
        
        // Redireciona para a pÃ¡gina de construÃ§Ã£o
        // ATENÃ‡ÃƒO: Se o seu site estiver numa subpasta, talvez precise de ../site_em_construcao.php
        header("Location: site_em_construcao.php");
        exit();
    }
}
// --- FIM DA LÃ“GICA DO TEMPLATE ---

?>