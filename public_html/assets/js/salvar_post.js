document.addEventListener('DOMContentLoaded', function() {

    // Adiciona um 'ouvinte' de cliques ao corpo do documento
    document.body.addEventListener('click', function(event) {

        // Verifica se o elemento clicado (ou um de seus pais) é o botão de salvar
        const saveBtn = event.target.closest('.post-save-btn');

        // Se não for o botão de salvar, não faz nada
        if (!saveBtn) {
            return;
        }

        // Previne a ação padrão do link/botão
        event.preventDefault();

        const postId = saveBtn.dataset.postid;
        const formData = new FormData();
        formData.append('post_id', postId);

        // Envia a requisição para o script PHP no servidor
        fetch('api/postagens/salvar_post.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const saveTextSpan = saveBtn.querySelector('.save-text');

                if (data.salvo) {
                    // Se a ação foi SALVAR
                    saveTextSpan.textContent = 'Remover dos Salvos';
                    if (window.showToast) {
                        window.showToast('Publicação salva com sucesso!');
                    }
                } else {
                    // Se a ação foi REMOVER DOS SALVOS
                    if (window.location.pathname.includes('salvos.php')) {
                        // --- NOVA LÓGICA ---
                        // Se estiver na página de salvos, remove o card do post da tela
                        saveBtn.closest('.post-card').remove();
                    } else {
                        // Em outras páginas (como o feed), apenas atualiza o texto
                        saveTextSpan.textContent = 'Salvar Post';
                    }
                    
                    if (window.showToast) {
                        window.showToast('Publicação removida dos salvos.');
                    }
                }
            } else {
                // Em caso de erro
                alert(data.error || 'Ocorreu um erro ao salvar a publicação.');
            }
        })
        .catch(error => console.error('Erro na requisição:', error));
    });
});