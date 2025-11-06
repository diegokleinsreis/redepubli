<?php
// 1. GUARITA DE SEGURANÇA E CONEXÃO
require_once __DIR__ . '/../../admin/admin_auth.php'; // Garante que só o admin pode executar
require_once __DIR__ . '/../../../config/database.php'; // Puxa $conn

// 2. VERIFICA SE OS DADOS VIERAM DO FORMULÁRIO (MÉTODO POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. PREPARA A QUERY DE ATUALIZAÇÃO REUTILIZÁVEL
    $sql = "UPDATE Configuracoes SET valor = ? WHERE chave = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Erro ao preparar a query: " . $conn->error);
    }

    // --- LÓGICA ATUALIZADA PARA INTERRUPTORES (SWITCHES) ---

    // 4. Trata os campos do tipo "interruptor" primeiro.
    
    // Lista de chaves que são interruptores
    // ADICIONAMOS 'modo_dev' A ESTA LISTA
    $switch_keys = ['modo_manutencao', 'permite_cadastro', 'modo_dev'];
    
    foreach ($switch_keys as $chave) {
        // Define o valor como '1' se estiver 'ligado' (existe no POST), ou '0' se estiver 'desligado' (não existe)
        $valor = isset($_POST[$chave]) ? '1' : '0';
        
        $stmt->bind_param("ss", $valor, $chave);
        if (!$stmt->execute()) {
            die("Erro ao atualizar a chave do interruptor: " . htmlspecialchars($chave));
        }
    }
    
    // --- FIM DA LÓGICA ATUALIZADA ---

    // 5. FAZ O "LOOP" PELOS CAMPOS DE TEXTO RESTANTES
    // Itera por cada item enviado pelo formulário (ex: 'site_nome', 'versao_assets')
    foreach ($_POST as $chave => $valor) {
        
        // Pula os campos que já tratámos
        if (in_array($chave, $switch_keys)) {
            continue;
        }

        // Atualiza os campos de texto (site_nome, email_contato, versao_assets, etc.)
        $stmt->bind_param("ss", $valor, $chave);
        
        if (!$stmt->execute()) {
            die("Erro ao atualizar a chave: " . htmlspecialchars($chave));
        }
    }

    // 6. FECHA O PREPARED STATEMENT
    $stmt->close();
    $conn->close();

    // 7. REDIRECIONA DE VOLTA PARA A PÁGINA DE ADMIN
    header("Location: ../../admin/admin_configuracoes.php?success=1");
    exit();

} else {
    // Se alguém tentar aceder a este arquivo diretamente pela URL
    die("Acesso inválido.");
}
?>