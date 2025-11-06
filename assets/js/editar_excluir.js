document.addEventListener('DOMContentLoaded', function() {

    // LÓGICA PARA EXCLUIR POSTS
    document.body.addEventListener('click', function(event) {
        const deleteBtn = event.target.closest('.post-delete-btn');
        if (deleteBtn) {
            event.preventDefault();
            if (confirm('Tem certeza que deseja excluir esta postagem?')) {
                const postId = deleteBtn.dataset.postid;
                const formData = new FormData();
                formData.append('post_id', postId);

                fetch('api/postagens/excluir_post.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('post-' + postId).remove();
                        if(window.showToast) window.showToast('Postagem excluída com sucesso.');
                    } else {
                        alert(data.error || 'Ocorreu um erro.');
                    }
                });
            }
        }
    });

    // LÓGICA PARA EDITAR POSTS
    // (Esta lógica é um pouco mais complexa, envolvendo múltiplos botões)
    document.body.addEventListener('click', function(event) {
        const editBtn = event.target.closest('.post-edit-btn');
        const cancelBtn = event.target.closest('.post-edit-cancel-btn');
        const saveBtn = event.target.closest('.post-edit-save-btn');
        
        if (editBtn) {
            event.preventDefault();
            const postId = editBtn.dataset.postid;
            const postView = document.getElementById('post-view-' + postId);
            const postEdit = document.getElementById('post-edit-' + postId);

            postView.classList.add('is-hidden');
            postEdit.classList.remove('is-hidden');

            // --- LÓGICA ATUALIZADA AQUI ---
            const textarea = postEdit.querySelector('.post-edit-textarea');
            // Move o cursor para o final do texto
            const textLength = textarea.value.length;
            textarea.focus();
            textarea.setSelectionRange(textLength, textLength);
        }

        if (cancelBtn) {
            event.preventDefault();
            const postId = cancelBtn.dataset.postid;
            document.getElementById('post-view-' + postId).classList.remove('is-hidden');
            document.getElementById('post-edit-' + postId).classList.add('is-hidden');
        }

        if (saveBtn) {
            event.preventDefault();
            const postId = saveBtn.dataset.postid;
            const textarea = document.querySelector(`#post-edit-${postId} .post-edit-textarea`);
            const newText = textarea.value;

            const formData = new FormData();
            formData.append('post_id', postId);
            formData.append('new_text', newText);

            fetch('api/postagens/editar_post.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`#post-view-${postId} .post-content p`).innerHTML = data.new_text_html;
                    document.getElementById('post-view-' + postId).classList.remove('is-hidden');
                    document.getElementById('post-edit-' + postId).classList.add('is-hidden');
                    if(window.showToast) window.showToast('Postagem atualizada!');
                } else {
                    alert(data.error || 'Ocorreu um erro.');
                }
            });
        }
    });

    // (Lógica similar para comentários pode ser adicionada aqui no futuro)

});