<?php
/**
 * templates/head_common.php
 *
 * O <head> HTML comum para todas as páginas do site.
 * Espera que as seguintes variáveis PHP sejam definidas antes de ser incluído:
 * * @var array $config       (Vem do config/database.php)
 * @var string $page_title  (Deve ser definida pela página-mãe, ex: "Login")
 * @var string $asset_version (Vem do config/database.php)
 */

// Se $page_title não for definida (por segurança), cria um título padrão
if (!isset($page_title)) {
    $page_title = $config['site_nome'] ?? 'Bem-vindo(a)';
}

// Se $asset_version não foi definida (caso o config/database.php falhe), define um padrão
if (!isset($asset_version)) {
    $asset_version = time(); // Padrão de segurança para forçar limpeza de cache
}

?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($page_title); ?> - <?php echo htmlspecialchars($config['site_nome']); ?></title>

<script>
    (function() {
        // Verifica a preferência do usuário no localStorage
        const theme = localStorage.getItem('theme');
        if (theme === 'dark') {
            // Se for 'dark', aplica a classe 'dark-mode' diretamente na tag <html>
            // antes mesmo da página ser desenhada.
            document.documentElement.classList.add('dark-mode');
        }
        // Se for 'light' ou nulo, não faz nada (usa o padrão claro).
    })();
</script>

<?php // --- [INÍCIO DA CORREÇÃO DE CACHE] --- ?>
<?php // Carrega todos os arquivos CSS individualmente para aplicar o versionamento ?>
<link rel="stylesheet" href="assets/css/base/_base.css?v=<?php echo $asset_version; ?>">
<link rel="stylesheet" href="assets/css/layout/_layout.css?v=<?php echo $asset_version; ?>">
<link rel="stylesheet" href="assets/css/layout/_header.css?v=<?php echo $asset_version; ?>">
<link rel="stylesheet" href="assets/css/layout/_sidebar.css?v=<?php echo $asset_version; ?>">
<link rel="stylesheet" href="assets/css/components/_forms.css?v=<?php echo $asset_version; ?>">
<link rel="stylesheet" href="assets/css/components/_post.css?v=<?php echo $asset_version; ?>">
<link rel="stylesheet" href="assets/css/components/_profile.css?v=<?php echo $asset_version; ?>">
<link rel="stylesheet" href="assets/css/components/_comments.css?v=<?php echo $asset_version; ?>">
<link rel="stylesheet" href="assets/css/components/_notifications.css?v=<?php echo $asset_version; ?>">
<link rel="stylesheet" href="assets/css/components/_modal.css?v=<?php echo $asset_version; ?>">
<link rel="stylesheet" href="assets/css/components/_settings.css?v=<?php echo $asset_version; ?>">
<link rel="stylesheet" href="assets/css/components/_public.css?v=<?php echo $asset_version; ?>">
<link rel="stylesheet" href="assets/css/components/_lightbox.css?v=<?php echo $asset_version; ?>"> 
<link rel="stylesheet" href="assets/css/components/_dark_mode.css?v=<?php echo $asset_version; ?>">
<?php // --- [FIM DA CORREÇÃO DE CACHE] --- ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">