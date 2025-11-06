document.addEventListener('DOMContentLoaded', function() {

    // --- LÓGICA PARA O BOTÃO 'RESPONDER' ---

    // Seleciona todas as áreas principais onde listas de comentários podem aparecer
    // (Ajuste os seletores se tiver outras áreas além destas)
    const commentAreas = document.querySelectorAll('.post-card .post-comments-section, .post-card .full-comment-list');

    // Adiciona um "ouvinte" de cliques a CADA UMA dessas áreas
    commentAreas.forEach(area => {
        area.addEventListener('click', function(event) {
            
            // Verifica se o elemento clicado DENTRO DESTA ÁREA é um link de resposta
            const replyBtn = event.target.closest('.reply-link');
            
            if (replyBtn) {
                // Como o listener está na área de comentário principal,
                // não precisamos mais verificar se está no lightbox.
                // Qualquer clique aqui é garantidamente fora do lightbox.

                event.preventDefault(); // Impede que o link '#' recarregue a página

                // Pega o ID do comentário pai a partir do atributo 'data-comment-id' do botão
                const commentId = replyBtn.dataset.commentId;
                
                // Encontra o formulário de resposta correspondente USANDO O BOTÃO CLICADO COMO REFERÊNCIA
                // Procura o wrapper do comentário pai e depois o formulário dentro dele
                const commentWrapper = replyBtn.closest('.comment-item-wrapper');
                const replyForm = commentWrapper ? commentWrapper.querySelector(`.reply-form-container[id="reply-form-${commentId}"]`) : null;

                // Se o formulário existir, alterna a sua visibilidade
                if (replyForm) {
                    const isHidden = replyForm.classList.contains('is-hidden');
                    
                    // A classe 'is-hidden' (do seu CSS) controla se o elemento está visível ou não
                    replyForm.classList.toggle('is-hidden');

                    // Se o formulário estava escondido e agora está visível, foca no campo de texto.
                    if (isHidden) {
                        replyForm.querySelector('.comment-input').focus();
                    }
                }
            }
        });
    });

    // (Futuramente, o código para editar e excluir comentários também será adicionado aqui)

});