<?php
/**
 * logout.php - Encerra a sessão do usuário.
 */

// PASSO 1: Inicia a sessão para poder acessá-la.
session_start();

// PASSO 2: Limpa todas as variáveis da sessão.
// Isso remove todos os dados guardados, como o 'user_id'.
$_SESSION = array();

// PASSO 3: Destrói o cookie da sessão no navegador do usuário.
// Isso garante que o "tíquete" seja invalidado.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// PASSO 4: Finalmente, destrói a sessão no servidor.
session_destroy();

// PASSO 5: Redireciona o usuário de volta para a página de login.
header("Location: ../../login.php");
exit();
?>