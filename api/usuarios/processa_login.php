<?php
// Inicia a sessão.
session_start();

// Puxa a conexão com o banco de dados.
require_once __DIR__ . '/../../../config/database.php';

// Verifica se os dados foram enviados via POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email_ou_usuario = $_POST['email_ou_usuario'];
    $senha = $_POST['senha'];

    if (empty($email_ou_usuario) || empty($senha)) {
        $_SESSION['login_error'] = "Preencha todos os campos.";
        header("Location: ../../login.php");
        exit();
    }

    $sql = "SELECT id, senha_hash, role, status FROM Usuarios WHERE email = ? OR nome_de_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email_ou_usuario, $email_ou_usuario);
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        
        if (password_verify($senha, $usuario['senha_hash'])) {
            
            if ($usuario['status'] === 'ativo') {
                
                session_regenerate_id(true);

                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_role'] = $usuario['role'];
                
                // --- [INÍCIO DA NOVA LÓGICA - PASSO 2 DO LOG DE LOGIN] ---
                try {
                    // Pega o ID do usuário e o IP para o log
                    $id_usuario_logado = $usuario['id'];
                    $ip_usuario = $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido';

                    // Insere o registo na nova tabela
                    $sql_log = "INSERT INTO Logs_Login (id_usuario, ip_usuario) VALUES (?, ?)";
                    $stmt_log = $conn->prepare($sql_log);
                    $stmt_log->bind_param("is", $id_usuario_logado, $ip_usuario);
                    $stmt_log->execute();
                    $stmt_log->close();
                    
                } catch (Exception $e) {
                    // Se a inserção no log falhar, não impede o login.
                    // Apenas regista o erro silenciosamente.
                    error_log("Falha ao registar Log de Login: " . $e->getMessage());
                }
                // --- [FIM DA NOVA LÓGICA] ---

                // --- INÍCIO DA MODIFICAÇÃO (redirecionamento) ---

                // Verifica se há um URL de redirecionamento guardado na sessão
                if (isset($_SESSION['redirect_url']) && !empty($_SESSION['redirect_url'])) {
                    $redirect_url = $_SESSION['redirect_url'];
                    // Limpa a variável da sessão para não ser usada novamente
                    unset($_SESSION['redirect_url']); 
                    header("Location: " . $redirect_url);
                    exit();
                } else {
                    // Se não houver, redireciona para o feed (comportamento padrão)
                    header("Location: ../../feed.php");
                    exit();
                }

                // --- FIM DA MODIFICAÇÃO ---

            } else {
                $_SESSION['login_error'] = "Sua conta está suspensa. Entre em contato com o suporte.";
                header("Location: ../../login.php");
                exit();
            }

        } else {
            $_SESSION['login_error'] = "E-mail/usuário ou senha inválidos.";
            header("Location: ../../login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "E-mail/usuário ou senha inválidos.";
        header("Location: ../../login.php");
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    echo "Acesso inválido.";
}
?>