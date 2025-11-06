<?php
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $data_nascimento = $_POST['data_nascimento'];
    $nome_de_usuario = trim($_POST['nome_de_usuario']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Pega o ID do bairro do formulário e converte para um número inteiro
    $id_bairro = isset($_POST['id_bairro']) ? (int)$_POST['id_bairro'] : 0;

    // Validação de campos vazios, incluindo o campo de bairro
    if (empty($nome) || empty($sobrenome) || empty($data_nascimento) || empty($nome_de_usuario) || empty($email) || empty($senha) || empty($confirmar_senha) || $id_bairro <= 0) {
        die("Erro: Todos os campos são obrigatórios, incluindo o bairro.");
    }

    // Validação para confirmar se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        die("Erro: As senhas não coincidem. Por favor, tente novamente.");
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // A query SQL agora inclui a coluna 'id_bairro'
    $sql = "INSERT INTO Usuarios (nome, sobrenome, data_nascimento, nome_de_usuario, email, senha_hash, id_bairro) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);

    // O bind_param agora tem 7 parâmetros. 'i' no final para o integer 'id_bairro'
    $stmt->bind_param("ssssssi", $nome, $sobrenome, $data_nascimento, $nome_de_usuario, $email, $senha_hash, $id_bairro);

    if ($stmt->execute()) {
        // Redireciona para a página de login com um parâmetro de sucesso na URL
        header("Location: ../../login.php?cadastro=sucesso");
        exit(); // Encerra o script após o redirecionamento
    } else {
        if ($conn->errno == 1062) {
            echo "Erro: O nome de usuário ou email já está em uso. Por favor, escolha outro.";
        } else {
            echo "Erro ao cadastrar o usuário: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Acesso inválido.";
}
?>