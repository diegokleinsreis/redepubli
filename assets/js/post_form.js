document.addEventListener('DOMContentLoaded', function() {

    // LÓGICA PARA MOSTRAR O NOME DO FICHEIRO NO UPLOAD DE POSTS
    // --- CORREÇÃO APLICADA AQUI ---
    const postMediaInput = document.getElementById('post_media'); // Alterado de 'post_imagem' para 'post_media'
    const fileNameDisplay = document.getElementById('file-name-display');

    if (postMediaInput && fileNameDisplay) {
        postMediaInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                // Se um ficheiro for selecionado, mostra o nome dele no elemento span
                fileNameDisplay.textContent = 'Ficheiro selecionado: ' + this.files[0].name;
            } else {
                // Se o utilizador cancelar a seleção, limpa o texto
                fileNameDisplay.textContent = '';
            }
        });
    }

});