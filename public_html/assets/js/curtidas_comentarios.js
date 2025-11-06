document.addEventListener('DOMContentLoaded', function() {
    
    // Adiciona um "ouvinte" de cliques ao corpo do documento
    document.body.addEventListener('click', function(event) {
        
        // Verifica se o elemento clicado é um botão de curtir comentário
        const likeButton = event.target.closest('.comment-like-btn');

        // Se não for o botão certo, não faz nada
        if (!likeButton) {
            return;
        }

        event.preventDefault(); // Impede que o link '#' recarregue a página

        const commentId = likeButton.dataset.commentId;
        const formData = new FormData();
        formData.append('comment_id', commentId);

        // Envia o pedido para o nosso script PHP
        fetch('api/comentarios/curtir_comentario.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Encontra TODOS os botões e contadores para este comentário específico
                const allLikeButtons = document.querySelectorAll(`.comment-like-btn[data-comment-id="${commentId}"]`);
                const allLikeCountSpans = document.querySelectorAll(`.comment-like-count[data-comment-id="${commentId}"]`);

                // Atualiza a aparência de todos os botões
                allLikeButtons.forEach(button => {
                    button.classList.toggle('active', data.curtido);
                });

                // Atualiza todos os contadores
                allLikeCountSpans.forEach(span => {
                    span.querySelector('i').nextSibling.textContent = ' ' + data.total_curtidas;
                    
                    if (data.total_curtidas > 0) {
                        span.style.display = 'inline-flex'; // Usar inline-flex para alinhar o ícone e o texto
                    } else {
                        span.style.display = 'none';
                    }
                });

            } else {
                // Mostra um alerta se algo der errado
                alert(data.error || 'Ocorreu um erro ao processar sua curtida.');
            }
        })
        .catch(error => console.error('Erro na requisição:', error));
    });
});