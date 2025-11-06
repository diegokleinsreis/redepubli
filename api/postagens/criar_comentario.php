<?php
// Inicia a sessão para pegar o ID do usuário logado
session_start();

// Verifica se o usuário está logado, senão, encerra o script.
if (!isset($_SESSION['user_id'])) {
    die("Acesso negado. Por favor, faça o login para comentar.");
}

// Inclui a conexão com o banco de dados
require_once __DIR__ . '/../../../config/database.php';

// Verifica se a requisição foi feita usando o método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Pega os dados enviados pelo formulário e pela sessão
    $conteudo_texto = trim($_POST['conteudo_texto']);
    $id_postagem = isset($_POST['id_postagem']) ? (int)$_POST['id_postagem'] : 0;
    $id_usuario = $_SESSION['user_id'];
    
    // MUDANÇA: Pega o ID do comentário pai, se ele existir.
    // Se não for enviado, ele será NULL, indicando um comentário principal.
    $id_comentario_pai = isset($_POST['id_comentario_pai']) && !empty($_POST['id_comentario_pai']) ? (int)$_POST['id_comentario_pai'] : null;

    // Validação para não permitir comentários vazios ou em posts inválidos
    if (empty($conteudo_texto) || $id_postagem <= 0) {
        die("Erro: O comentário não pode estar vazio e precisa estar associado a uma postagem válida.");
    }

    // MUDANÇA: A query SQL foi atualizada para incluir a coluna 'id_comentario_pai'
    $sql = "INSERT INTO Comentarios (id_postagem, id_usuario, id_comentario_pai, conteudo_texto) VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    // MUDANÇA: O bind_param agora tem 4 parâmetros: "iiis"
    // (Integer, Integer, Integer, String). O PHP trata o 'null' corretamente aqui.
    $stmt->bind_param("iiis", $id_postagem, $id_usuario, $id_comentario_pai, $conteudo_texto);

    // Executa a query
    if ($stmt->execute()) {
        
        // --- NOVO: LÓGICA PARA CRIAR A NOTIFICAÇÃO DE COMENTÁRIO ---

        // Primeiro, pegamos o ID do autor do post
        $sql_post_autor = "SELECT id_usuario FROM Postagens WHERE id = ?";
        $stmt_post_autor = $conn->prepare($sql_post_autor);
        $stmt_post_autor->bind_param("i", $id_postagem);
        $stmt_post_autor->execute();
        $result_post_autor = $stmt_post_autor->get_result();
        
        if ($row = $result_post_autor->fetch_assoc()) {
            $post_autor_id = $row['id_usuario'];

            // Apenas cria a notificação se o usuário não estiver comentando no próprio post
            if ($post_autor_id != $id_usuario) {
                $tipo_notificacao = 'comentario_post';
                $sql_notificacao = "INSERT INTO notificacoes (usuario_id, remetente_id, tipo, id_referencia) VALUES (?, ?, ?, ?)";
                $stmt_notificacao = $conn->prepare($sql_notificacao);
                // Parâmetros: [quem recebe], [quem envia], [tipo], [id do post]
                $stmt_notificacao->bind_param("iisi", $post_autor_id, $id_usuario, $tipo_notificacao, $id_postagem);
                $stmt_notificacao->execute();
                $stmt_notificacao->close();
            }
        }
        $stmt_post_autor->close();
        // --- FIM DA LÓGICA DE NOTIFICAÇÃO ---

        // Redireciona de volta para a página da postagem, rolando para a âncora do post.
        header("Location: ../../postagem.php?id=" . $id_postagem . "#post-" . $id_postagem);
        exit();
    } else {
        // Se deu algum erro no banco de dados
        die("Erro ao salvar o comentário: " . $stmt->error);
    }

    // Fecha a conexão
    $stmt->close();
    $conn->close();
} else {
    // Se alguém tentar acessar este arquivo diretamente pelo navegador
    header("Location: ../../feed.php");
    exit();
}
?>