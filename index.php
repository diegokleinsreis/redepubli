<?php
/**
 * index.php - O Roteador Principal do Site
 *
 * Este arquivo não exibe conteúdo. Sua única função é verificar se o 
 * usuário está logado e redirecioná-lo para a página correta.
 */

// Inicia a sessão. Esta deve ser a primeira coisa em qualquer página
// que precise verificar o status de login de um usuário.
session_start();

// Verifica se a variável de sessão 'user_id' foi criada e não está vazia.
// (Nós criamos essa variável no script 'processa_login.php' quando o login é bem-sucedido).
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    
    // Se a variável existe, o usuário ESTÁ LOGADO.
    // Redirecionamos o navegador para a página do feed.
    header('Location: feed.php');
    exit; // É crucial parar a execução do script após um redirecionamento.

} else {

    // Se a variável não existe, o usuário NÃO ESTÁ LOGADO.
    // Redirecionamos o navegador para a página de login.
    header('Location: login.php');
    exit; // Paramos a execução do script.

}