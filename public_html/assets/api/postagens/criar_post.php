<?php
// Inicia a sessão para pegar o ID do usuário logado
session_start();

// Verifica se o usuário está logado, senão, encerra
if (!isset($_SESSION['user_id'])) {
    die("Acesso negado. Por favor, faça o login.");
}

// Inclui a conexão com o banco
require_once __DIR__ . '/../../../config/database.php';
// Inclui a nossa ferramenta de processamento de imagem
require_once __DIR__ . '/../../utils/image_handler.php';


// Verifica se o formulário foi enviado (método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $conteudo_texto = trim($_POST['conteudo_texto']);
    $id_usuario = $_SESSION['user_id'];
    $url_media_para_db = null;
    $tipo_media_para_db = null;

    // --- INÍCIO DA LÓGICA ATUALIZADA PARA UPLOAD ---

    // Verifica se um ficheiro (imagem ou vídeo) foi enviado
    if (isset($_FILES['post_media']) && $_FILES['post_media']['error'] == 0) {
        $media_enviada = $_FILES['post_media'];
        
        // Define as extensões permitidas para cada tipo de média
        $extensoes_imagem = ['jpg', 'jpeg', 'png', 'gif'];
        $extensoes_video = ['mp4', 'webm', 'mov']; // Adicione outras se desejar

        $tamanho_maximo_imagem = 5 * 1024 * 1024; // 5 MB
        $tamanho_maximo_video = 50 * 1024 * 1024; // 50 MB (para vídeos curtos)

        $extensao = strtolower(pathinfo($media_enviada['name'], PATHINFO_EXTENSION));
        
        $diretorio_destino = __DIR__ . "/../../uploads/posts/";
        if (!file_exists($diretorio_destino)) {
            mkdir($diretorio_destino, 0777, true);
        }

        $novo_nome_arquivo = "post_" . $id_usuario . "_" . time() . "." . $extensao;
        $caminho_final = $diretorio_destino . $novo_nome_arquivo;

        // Verifica se é uma IMAGEM
        if (in_array($extensao, $extensoes_imagem)) {
            if ($media_enviada['size'] > $tamanho_maximo_imagem) {
                die("Erro: A imagem é muito grande (máximo 5MB).");
            }
            // Usa a função de otimização para imagens
            if (process_and_save_image($media_enviada['tmp_name'], $caminho_final, 'resize_to_width', 1080)) {
                $url_media_para_db = "uploads/posts/" . $novo_nome_arquivo;
                $tipo_media_para_db = 'imagem';
            } else {
                die("Erro: Ocorreu uma falha ao processar a sua imagem.");
            }
        }
        // Verifica se é um VÍDEO
        elseif (in_array($extensao, $extensoes_video)) {
            if ($media_enviada['size'] > $tamanho_maximo_video) {
                die("Erro: O vídeo é muito grande (máximo 50MB).");
            }
            // Para vídeos, por enquanto, apenas movemos o ficheiro
            if (move_uploaded_file($media_enviada['tmp_name'], $caminho_final)) {
                $url_media_para_db = "uploads/posts/" . $novo_nome_arquivo;
                $tipo_media_para_db = 'video';
            } else {
                die("Erro: Ocorreu uma falha ao enviar o seu vídeo.");
            }
        }
        // Se não for nenhum dos tipos permitidos
        else {
            die("Erro: Tipo de ficheiro inválido. Apenas imagens (JPG, PNG, GIF) e vídeos (MP4, WEBM, MOV) são permitidos.");
        }
    }

    // --- FIM DA LÓGICA ATUALIZADA ---


    if (empty($conteudo_texto) && $url_media_para_db === null) {
        die("Erro: A postagem não pode estar vazia.");
    }

    // A query SQL agora usa as novas colunas
    $sql = "INSERT INTO Postagens (id_usuario, conteudo_texto, tipo_media, url_media) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $id_usuario, $conteudo_texto, $tipo_media_para_db, $url_media_para_db);

    if ($stmt->execute()) {
        header("Location: ../../feed.php");
        exit();
    } else {
        die("Erro ao criar a postagem: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>