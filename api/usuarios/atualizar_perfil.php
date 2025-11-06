<?php
session_start();

// Resposta padrão em JSON para o JavaScript
header('Content-Type: application/json');

// 1. Verificações de Segurança e Sessão
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso negado. Você precisa estar logado.']);
    exit();
}

require_once __DIR__ . '/../../../config/database.php';
$user_id = $_SESSION['user_id'];

/**
 * Função para redimensionar e cortar a imagem de perfil para um quadrado perfeito.
 * @param string $source_path Caminho do ficheiro original.
 * @param string $destination_path Caminho para guardar o novo ficheiro.
 * @param int $target_size O tamanho final desejado (ex: 200 para 200x200).
 * @return bool True em sucesso, false em falha.
 */
function resize_avatar($source_path, $destination_path, $target_size = 200) {
    list($width, $height, $type) = getimagesize($source_path);

    $src_image = null;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $src_image = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $src_image = imagecreatefrompng($source_path);
            break;
        default:
            return false;
    }

    if (!$src_image) return false;

    $source_x = 0;
    $source_y = 0;
    $source_size = min($width, $height);

    if ($width > $height) {
        $source_x = ($width - $height) / 2;
    } elseif ($height > $width) {
        $source_y = ($height - $width) / 2;
    }

    $new_image = imagecreatetruecolor($target_size, $target_size);
    
    if ($type == IMAGETYPE_PNG) {
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
        imagefilledrectangle($new_image, 0, 0, $target_size, $target_size, $transparent);
    }

    imagecopyresampled($new_image, $src_image, 0, 0, $source_x, $source_y, $target_size, $target_size, $source_size, $source_size);

    $success = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($new_image, $destination_path, 85);
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($new_image, $destination_path, 9);
            break;
    }

    imagedestroy($src_image);
    imagedestroy($new_image);

    return $success;
}


$response = ['success' => true];

// 2. Lógica para Upload da Foto de Perfil (se um novo ficheiro for enviado)
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
    $arquivo_enviado = $_FILES['foto_perfil'];
    $arquivo_tmp = $arquivo_enviado['tmp_name'];
    $extensao = strtolower(pathinfo($arquivo_enviado['name'], PATHINFO_EXTENSION));

    if ($arquivo_enviado['size'] > 5000000) {
        echo json_encode(['success' => false, 'error' => 'O ficheiro de imagem é muito grande (máx 5MB).']);
        exit();
    }
    
    // --- CORREÇÃO APLICADA AQUI ---
    if (!in_array($extensao, ['jpg', 'jpeg', 'png'])) {
        echo json_encode(['success' => false, 'error' => 'Apenas ficheiros JPG e PNG são permitidos.']);
        exit();
    }

    $novo_nome_arquivo = "user_" . $user_id . "_" . time() . "." . $extensao;
    $caminho_destino = __DIR__ . "/../../uploads/avatars/" . $novo_nome_arquivo;

    if (resize_avatar($arquivo_tmp, $caminho_destino, 200)) {
        $url_para_db = "uploads/avatars/" . $novo_nome_arquivo;
        $sql_update_avatar = "UPDATE Usuarios SET foto_perfil_url = ? WHERE id = ?";
        $stmt_avatar = $conn->prepare($sql_update_avatar);
        $stmt_avatar->bind_param("si", $url_para_db, $user_id);
        $stmt_avatar->execute();
        
        $response['new_avatar_url'] = $url_para_db;
    } else {
        echo json_encode(['success' => false, 'error' => 'Ocorreu um erro ao processar a sua nova foto de perfil.']);
        exit();
    }
}

// 3. Lógica para atualizar os outros campos do formulário
try {
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $biografia = trim($_POST['biografia']);
    $data_nascimento = $_POST['data_nascimento'];
    $relacionamento = $_POST['relacionamento'];
    $id_bairro = (int)$_POST['id_bairro'];

    if (empty($nome) || empty($sobrenome) || empty($data_nascimento) || $id_bairro <= 0) {
        throw new Exception("Nome, sobrenome, data de nascimento e bairro são campos obrigatórios.");
    }
    
    $sql = "UPDATE Usuarios SET 
                nome = ?, 
                sobrenome = ?, 
                biografia = ?, 
                data_nascimento = ?, 
                relacionamento = ?, 
                id_bairro = ? 
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssii", $nome, $sobrenome, $biografia, $data_nascimento, $relacionamento, $id_bairro, $user_id);

    if (!$stmt->execute()) {
        throw new Exception("Erro ao atualizar as informações no banco de dados.");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit();
}

// 4. Envia a resposta final de sucesso
echo json_encode($response);
$conn->close();
?>