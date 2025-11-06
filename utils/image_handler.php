<?php

/**
 * Função centralizada para processar e guardar imagens enviadas.
 * Pode redimensionar por largura máxima ou cortar para um quadrado.
 *
 * @param string $source_path Caminho do ficheiro temporário enviado (ex: $_FILES['my_image']['tmp_name']).
 * @param string $destination_path Caminho completo onde a nova imagem será guardada.
 * @param string $mode O modo de processamento: 'resize_to_width' ou 'crop_to_square'.
 * @param int $max_size A dimensão alvo (largura máxima para 'resize_to_width', ou o lado do quadrado para 'crop_to_square').
 * @return bool Retorna true em sucesso, false em falha.
 */
function process_and_save_image($source_path, $destination_path, $mode = 'resize_to_width', $max_size = 1080) {
    
    // Pega as informações da imagem original
    list($width, $height, $type) = getimagesize($source_path);
    if (!$type) return false;

    // Carrega a imagem original para a memória, dependendo do seu tipo
    $src_image = null;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $src_image = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $src_image = imagecreatefrompng($source_path);
            break;
        case IMAGETYPE_GIF:
            $src_image = imagecreatefromgif($source_path);
            break;
        default:
            return false; // Tipo de imagem não suportado
    }

    if (!$src_image) return false;

    // Calcula as novas dimensões e posições de corte com base no modo
    $new_width = $max_size;
    $new_height = $max_size;
    $source_x = 0;
    $source_y = 0;
    $source_w = $width;
    $source_h = $height;

    if ($mode === 'resize_to_width') {
        // Redimensiona mantendo a proporção, baseado na largura máxima
        if ($width > $max_size) {
            $new_width = $max_size;
            $new_height = ($height / $width) * $max_size;
        } else {
            // Se a imagem já for menor, mantém o tamanho original
            $new_width = $width;
            $new_height = $height;
        }
    } elseif ($mode === 'crop_to_square') {
        // Corta a maior porção quadrada possível do centro da imagem
        $source_w = min($width, $height);
        $source_h = min($width, $height);
        if ($width > $height) {
            $source_x = ($width - $height) / 2;
        } elseif ($height > $width) {
            $source_y = ($height - $width) / 2;
        }
    }

    // Cria uma nova imagem em branco com as dimensões calculadas
    $new_image = imagecreatetruecolor($new_width, $new_height);

    // Se for PNG, preserva a transparência
    if ($type == IMAGETYPE_PNG) {
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
        imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $transparent);
    }

    // Copia a imagem original para a nova imagem, redimensionando/cortando no processo
    imagecopyresampled($new_image, $src_image, 0, 0, $source_x, $source_y, $new_width, $new_height, $source_w, $source_h);

    // Guarda a nova imagem no destino final com compressão
    $success = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($new_image, $destination_path, 80); // 80% de qualidade
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($new_image, $destination_path, 6); // Nível de compressão 6 (de 0 a 9)
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($new_image, $destination_path);
            break;
    }

    // Liberta a memória
    imagedestroy($src_image);
    imagedestroy($new_image);

    return $success;
}

?>