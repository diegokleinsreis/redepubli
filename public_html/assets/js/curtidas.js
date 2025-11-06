document.addEventListener('DOMContentLoaded', function() {
    
    // Adiciona um único "ouvinte" de cliques ao corpo do documento
    document.body.addEventListener('click', function(event) {
        
        // Verifica se o elemento que foi clicado (ou um dos seus pais) é um botão de curtir de POST
        const likeBtn = event.target.closest('.like-btn');

        // Se o clique não foi num botão de curtir de post, não faz nada
        if (!likeBtn) {
            return;
        }

        const postId = likeBtn.dataset.postid;
        const formData = new FormData();
        formData.append('post_id', postId);

        fetch('api/postagens/curtir_post.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Seleciona TODOS os botões de curtir para este post (no feed E no modal, se estiver aberto)
                const allLikeButtons = document.querySelectorAll(`.like-btn[data-postid="${postId}"]`);
                
                // Encontra os contadores de curtida no post do feed e DENTRO do modal
                const allLikeCountSpans = document.querySelectorAll(
                    `#post-${postId} .post-stats .like-count, #lightbox-modal .post-stats .like-count`
                );

                // Atualiza a aparência de todos os botões correspondentes
                allLikeButtons.forEach(button => {
                    button.classList.toggle('active', data.curtido);
                });

                // Atualiza o texto de todos os contadores correspondentes
                allLikeCountSpans.forEach(span => {
                    span.textContent = data.total_curtidas + (data.total_curtidas == 1 ? ' curtida' : ' curtidas');
                });

            } else {
                alert(data.error || 'Ocorreu um erro.');
            }
        })
        .catch(error => console.error('Error:', error));
    });
});