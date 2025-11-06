<?php
// Inclui a "guarita de segurança" do administrador
require_once __DIR__ . '/../../admin/admin_auth.php';
// Inclui a conexão com o banco de dados
require_once __DIR__ . '/../../../config/database.php';

// Verifica se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Pega todos os dados do formulário
    $id = (int)($_POST['id'] ?? 0);
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $nome_de_usuario = trim($_POST['nome_de_usuario']);
    $email = trim($_POST['email']);
    $role = $_POST['role'] ?? 'membro'; 
    $status = $_POST['status'] ?? 'ativo';
    $nova_senha = $_POST['nova_senha'];

    // ADICIONADO: Pega o ID do bairro do formulário
    $id_bairro = isset($_POST['id_bairro']) ? (int)$_POST['id_bairro'] : 0;

    // ===== Verificação de Segurança (já existente) =====
    if ($id === $_SESSION['user_id'] && $role !== 'admin') {
        $count_sql = "SELECT COUNT(id) AS admin_count FROM Usuarios WHERE role = 'admin'";
        $count_result = $conn->query($count_sql);
        $admin_count = $count_result->fetch_assoc()['admin_count'];

        if ($admin_count <= 1) {
            die("Erro de segurança: Você não pode rebaixar o único administrador do sistema.");
        }
    }
    
    // Validações básicas
    // ADICIONADO: Validação para o campo de bairro
    if ($id <= 0 || empty($nome) || empty($sobrenome) || empty($nome_de_usuario) || empty($email) || $id_bairro <= 0) {
        die("Erro: Dados inválidos ou faltando. Todos os campos, incluindo o bairro, são obrigatórios.");
    }
    if (!in_array($role, ['membro', 'admin'])) {
        die("Erro: Função (role) inválida.");
    }
    if (!in_array($status, ['ativo', 'suspenso'])) {
        die("Erro: Status inválido.");
    }

    // --- Lógica de Atualização Dinâmica ---
    $sql_parts = [];
    $params = [];
    $types = '';

    // Adiciona os campos de texto à atualização
    $sql_parts[] = "nome = ?"; array_push($params, $nome); $types .= 's';
    $sql_parts[] = "sobrenome = ?"; array_push($params, $sobrenome); $types .= 's';
    
    $sql_check = "SELECT id FROM Usuarios WHERE (nome_de_usuario = ? OR email = ?) AND id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ssi", $nome_de_usuario, $email, $id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        die("Erro: O nome de usuário ou e-mail já está em uso por outra conta.");
    }
    $stmt_check->close();

    $sql_parts[] = "nome_de_usuario = ?"; array_push($params, $nome_de_usuario); $types .= 's';
    $sql_parts[] = "email = ?"; array_push($params, $email); $types .= 's';
    
    // ADICIONADO: Adiciona o id_bairro à atualização
    $sql_parts[] = "id_bairro = ?"; array_push($params, $id_bairro); $types .= 'i';

    $sql_parts[] = "role = ?"; array_push($params, $role); $types .= 's';
    $sql_parts[] = "status = ?"; array_push($params, $status); $types .= 's';

    // Se uma nova senha foi fornecida, gera o hash e adiciona à atualização
    if (!empty($nova_senha)) {
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $sql_parts[] = "senha_hash = ?";
        array_push($params, $senha_hash);
        $types .= 's';
    }

    // Monta a query UPDATE final
    $sql = "UPDATE Usuarios SET " . implode(", ", $sql_parts) . " WHERE id = ?";
    array_push($params, $id); 
    $types .= 'i';

    // Prepara e executa a atualização
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        // Redireciona de volta para a lista de usuários
        header("Location: ../../admin/admin_usuarios.php?success=1");
        exit();
    } else {
        die("Erro ao atualizar o usuário: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} else {
    // Se o acesso não for via POST, redireciona para a home do admin
    header("Location: ../../admin/index.php");
    exit();
}
?>