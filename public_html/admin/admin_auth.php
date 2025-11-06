<?php
// Inicia a sessão para verificar as credenciais do usuário.
session_start();

// A Regra de Segurança:
// 1. O usuário precisa estar logado (ter um 'user_id' na sessão).
// 2. E o 'role' do usuário na sessão precisa ser exatamente 'admin'.
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    
    // Se qualquer uma das condições falhar, o acesso é negado.
    // Redireciona o usuário para a página de login principal.
    header("Location: ../login.php");
    exit(); // Para a execução do script imediatamente.
}

// Se o script passar por este 'if', significa que o usuário é um admin verificado.
// O restante da página pode ser carregado.
?>