<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Acesso negado.");
}

require_once __DIR__ . '/../../../config/database.php';

// --- FUNÇÃO DE OTIMIZAÇÃO ATUALIZADA ---

/**
 * Função para redimensionar e cortar a imagem de perfil para um quadrado perfeito.
 * @param string $source_path Caminho do arquivo original.
 * @param string $destination_path Caminho para salvar o novo arquivo.
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

    // --- NOVA LÓGICA DE CORTE (CROP) ---
    $source_x = 0;
    $source_y = 0;
    $source_size = min($width, $height); // Usa a menor dimensão como base para o quadrado

    if ($width > $height) { // Se a imagem for paisagem (mais larga)
        $source_x = ($width - $height) / 2; // Centraliza o corte horizontalmente
    } elseif ($height > $width) { // Se a imagem for retrato (mais alta)
        $source_y = ($height - $width) / 2; // Centraliza o corte verticalmente
    }
    // --- FIM DA NOVA LÓGICA DE CORTE ---

    $new_image = imagecreatetruecolor($target_size, $target_size);
    
    if ($type == IMAGETYPE_PNG) {
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
        imagefilledrectangle($new_image, 0, 0, $target_size, $target_size, $transparent);
    }

    // Copia e redimensiona a porção quadrada da imagem original para a nova imagem
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

// --- RESTANTE DO SCRIPT (SEM ALTERAÇÕES) ---

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];

    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
        $arquivo_enviado = $_FILES['foto_perfil'];
        
        $arquivo_tmp = $arquivo_enviado['tmp_name'];
        $extensao = strtolower(pathinfo($arquivo_enviado['name'], PATHINFO_EXTENSION));
        
        if ($arquivo_enviado['size'] > 5000000) { die("Erro: O arquivo é muito grande (máx 5MB)."); }
        if (!in_array($extensao, ['jpg', 'jpeg', 'png'])) { die("Erro: Apenas arquivos JPG e PNG são permitidos."); }

        $novo_nome_arquivo = "user_" . $user_id . "_" . time() . "." . $extensao;
        $caminho_destino = __DIR__ . "/../../uploads/avatars/" . $novo_nome_arquivo;
        
        if (resize_avatar($arquivo_tmp, $caminho_destino, 200)) {
            
            $url_para_db = "uploads/avatars/" . $novo_nome_arquivo;
            
            $sql = "UPDATE Usuarios SET foto_perfil_url = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $url_para_db, $user_id);
            
            if ($stmt->execute()) {
                header("Location: ../../perfil.php");
                exit();
            } else {
                die("Erro ao atualizar o banco de dados.");
            }

        } else {
            die("Erro ao processar e salvar a imagem de perfil.");
        }
    } else {
        die("Erro no envio do arquivo: " . ($_FILES['foto_perfil']['error'] ?? 'Nenhum arquivo enviado'));
    }
} else {
    header("Location: ../../perfil.php");
    exit();
}
?>