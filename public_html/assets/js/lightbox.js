document.addEventListener('DOMContentLoaded', function() {

    const overlay = document.getElementById('lightbox-overlay');
    const modal = document.getElementById('lightbox-modal');
    const closeBtn = document.getElementById('lightbox-close-btn');
    const imageWrapper = document.querySelector('.lightbox-image-wrapper');
    const detailsColumn = document.querySelector('.lightbox-details-column');

    function openModal() {
        if (overlay) {
            overlay.classList.remove('is-hidden');
            overlay.classList.add('is-visible');
            document.body.style.overflow = 'hidden';
            const mainCommentForm = detailsColumn?.querySelector('.lightbox-details-footer .add-comment-form-container');
            if (mainCommentForm) {
                // Garante que o form principal esteja escondido ao abrir
                mainCommentForm.classList.add('is-hidden');
            }
        }
    }

    function closeModal() {
        if (overlay) {
            overlay.classList.remove('is-visible');
            overlay.classList.add('is-hidden');
            document.body.style.overflow = '';
        }
        if (imageWrapper) {
            imageWrapper.innerHTML = '<div class="spinner"></div>';
        }
        if (detailsColumn) {
            detailsColumn.innerHTML = '';
        }
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (overlay) overlay.addEventListener('click', (event) => { if (event.target === overlay) closeModal(); });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && overlay.classList.contains('is-visible')) closeModal(); });

    // --- FUNÇÃO AUXILIAR SEM NENHUM COMENTÁRIO INDESEJADO ---
    function buildCommentHTML(comment, postId, isReply = false, mainCommentId = null) {
        const commentLikeActive = comment.usuario_curtiu_comentario > 0 ? 'active' : '';
        const dataComentario = new Date(comment.data_comentario);
        const timestamp = `${dataComentario.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })} ${dataComentario.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`;

        const isReplyClass = isReply ? 'is-reply' : '';

        const authorName = comment.autor_nome || comment.nome;
        const authorSurname = comment.autor_sobrenome || comment.sobrenome;
        const authorPhoto = comment.autor_foto_perfil || comment.foto_perfil_url;
        const commentText = comment.conteudo_texto_formatado || comment.conteudo_texto;
        const authorId = comment.autor_id || comment.id_usuario;

        const parentIdForReplyLink = isReply ? mainCommentId : comment.id;

        let repliesHTML = '';
        if (!isReply && comment.respostas && comment.respostas.length > 0) {
            repliesHTML += '<div class="comment-replies">';
            comment.respostas.forEach(reply => {
                repliesHTML += buildCommentHTML(reply, postId, true, comment.id);
            });
            repliesHTML += '</div>';
        }

        // Retorna a estrutura HTML limpa
        return `
            <div class="comment-item-wrapper" id="comment-wrapper-${comment.id}">
                <div class="comment-view-mode">
                    <div class="comment-item ${isReplyClass}">
                        <div class="comment-author-avatar">
                            <a href="perfil.php?id=${authorId}"><img src="${authorPhoto || 'assets/images/default-avatar.png.png'}" alt="Foto de ${authorName}"></a>
                        </div>
                        <div class="comment-content">
                            <div class="comment-bubble">
                                <a href="perfil.php?id=${authorId}" class="comment-author-name">${authorName} ${authorSurname}</a>
                                <p class="comment-text">${commentText}</p>
                            </div>
                            <div class="comment-actions">
                                <span class="comment-timestamp">${timestamp}</span>
                                <a href="#" class="comment-like-btn ${commentLikeActive}" data-comment-id="${comment.id}">Curtir</a>
                                <a href="#" class="reply-link" data-comment-id="${parentIdForReplyLink}">Responder</a>
                                <span class="comment-like-count" data-comment-id="${comment.id}" style="display: ${comment.total_curtidas_comentario > 0 ? 'inline-flex' : 'none'};">
                                    <i class="fas fa-thumbs-up"></i> ${comment.total_curtidas_comentario || 0}
                                </span>
                            </div>
                        </div>
                    </div>
                    ${repliesHTML}
                </div>
                 ${!isReply ? `
                <div class="reply-form-container is-hidden" id="reply-form-${comment.id}">
                    <form action="api/postagens/criar_comentario_ajax.php" method="POST" class="lightbox-comment-form">
                        <input type="hidden" name="id_postagem" value="${postId}">
                        <input type="hidden" name="id_comentario_pai" value="${comment.id}">
                        <input type="text" name="conteudo_texto" class="comment-input" placeholder="Escreva sua resposta..." required>
                        <button type="submit" class="comment-submit-btn"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>` : ''}
            </div>
        `;
    }
    // --- FIM DA FUNÇÃO AUXILIAR CORRIGIDA ---

    // --- LÓGICA PRINCIPAL DE ABERTURA E RENDERIZAÇÃO DO MODAL ---
    document.body.addEventListener('click', function(event) {
        const imageContainer = event.target.closest('.post-card .post-image-clickable');

        if (imageContainer) {
            event.preventDefault();
            openModal();
            const postId = imageContainer.dataset.postid;

            fetch(`api/postagens/obter_detalhes_post.php?id=${postId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const post = data.post;

                        if (post.tipo_media === 'imagem') {
                            imageWrapper.innerHTML = `<img src="${post.url_media}" alt="Imagem da postagem">`;
                        } else if (post.tipo_media === 'video') {
                             imageWrapper.innerHTML = `<div class="post-video-container" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;"><video controls style="max-width:100%; max-height:100%;"><source src="${post.url_media}" type="video/mp4">Seu navegador não suporta vídeos.</video></div>`;
                        } else {
                            imageWrapper.innerHTML = `<p style="color: white; text-align: center;">Pré-visualização não disponível.</p>`;
                        }

                        const likeBtnActive = post.usuario_curtiu > 0 ? 'active' : '';
                        detailsColumn.innerHTML = `
                            <div class="lightbox-details-header">
                                <div class="post-header">
                                    <div class="post-author-avatar"><a href="perfil.php?id=${post.autor_id}"><img src="${post.autor_foto_perfil || 'assets/images/default-avatar.png.png'}" alt="Foto de ${post.autor_nome}"></a></div>
                                    <div class="post-author-info"><a href="perfil.php?id=${post.autor_id}" class="post-author-name-link"><span class="post-author-name">${post.autor_nome} ${post.autor_sobrenome}</span></a></div>
                                </div>
                                <div class="post-content"><p>${post.conteudo_texto_formatado}</p></div>
                            </div>
                            <div class="lightbox-details-body"><div class="full-comment-list"></div></div>
                            <div class="lightbox-details-footer">
                                <div class="post-stats">
                                    <span class="like-count">${post.total_curtidas} curtida${post.total_curtidas != 1 ? 's' : ''}</span>
                                    <a href="postagem.php?id=${post.id}" class="comment-count">${post.total_comentarios} comentário${post.total_comentarios != 1 ? 's' : ''}</a>
                                </div>
                                <div class="post-actions">
                                    <button class="action-btn like-btn ${likeBtnActive}" data-postid="${post.id}"><i class="far fa-thumbs-up"></i> Curtir</button>
                                    <button class="action-btn focus-comment-btn"><i class="far fa-comment"></i> Comentar</button>
                                    <button class="action-btn"><i class="fas fa-share"></i> Compartilhar</button>
                                </div>
                                <div class="add-comment-form-container is-hidden">
                                    <form action="api/postagens/criar_comentario_ajax.php" method="POST" class="lightbox-comment-form">
                                        <input type="hidden" name="id_postagem" value="${post.id}">
                                        <input type="text" name="conteudo_texto" class="comment-input" placeholder="Escreva um comentário..." required>
                                        <button type="submit" class="comment-submit-btn"><i class="fas fa-paper-plane"></i></button>
                                    </form>
                                </div>
                            </div>`;

                        const commentListContainer = detailsColumn.querySelector('.full-comment-list');
                        if (data.comentarios && data.comentarios.length > 0) {
                            commentListContainer.innerHTML = '';
                            data.comentarios.forEach(comment => {
                                commentListContainer.innerHTML += buildCommentHTML(comment, post.id, false, comment.id);
                            });
                        } else {
                            commentListContainer.innerHTML = '<p class="no-comments-message">Nenhum comentário ainda.</p>';
                        }
                    } else {
                        imageWrapper.innerHTML = '';
                        detailsColumn.innerHTML = `<p style="color: red; text-align: center; padding: 20px;">Erro: ${data.error}</p>`;
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar detalhes do post:', error);
                    imageWrapper.innerHTML = '';
                    detailsColumn.innerHTML = `<p style="color: red; text-align: center; padding: 20px;">Ocorreu um erro de rede ao carregar os detalhes.</p>`;
                });
        }
    });

    // --- OUVINTE GLOBAL DENTRO DO MODAL PARA TODAS AS AÇÕES ---
    detailsColumn.addEventListener('click', function(event) {
        const replyBtn = event.target.closest('.reply-link');
        const focusBtn = event.target.closest('.focus-comment-btn');

        // --- Lógica para o botão RESPONDER (com toggle e stopPropagation) ---
        if (replyBtn) {
            event.preventDefault(); // Impede a ação padrão do link
            event.stopPropagation(); // <<-- ALTERAÇÃO AQUI: Impede que o clique "vaze" para outros listeners

            const commentId = replyBtn.dataset.commentId;
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            if (replyForm) {
                replyForm.classList.toggle('is-hidden');
                const isNowVisible = !replyForm.classList.contains('is-hidden');

                if (isNowVisible) {
                    setTimeout(() => {
                        replyForm.querySelector('.comment-input').focus();
                    }, 50); // Pequeno delay para garantir que o elemento está visível antes de focar
                }
            }
        }

        // --- Lógica para o botão COMENTAR (com toggle) ---
        if (focusBtn) {
            event.preventDefault();
            const footer = focusBtn.closest('.lightbox-details-footer');
            if (footer) {
                const mainCommentForm = footer.querySelector('.add-comment-form-container');
                const mainCommentInput = footer.querySelector('.add-comment-form-container .comment-input');
                if (mainCommentForm && mainCommentInput) {
                    mainCommentForm.classList.toggle('is-hidden');
                    const isNowVisible = !mainCommentForm.classList.contains('is-hidden');

                    if (isNowVisible) {
                        setTimeout(() => {
                            mainCommentInput.focus();
                        }, 50); // Pequeno delay
                    }
                }
            }
        }
    });

    // --- LÓGICA PARA ENVIAR COMENTÁRIOS/RESPOSTAS VIA AJAX ---
    detailsColumn.addEventListener('submit', function(event) {
        // Verifica se o evento de submit veio de um formulário dentro do lightbox
        if (event.target.classList.contains('lightbox-comment-form')) {
            event.preventDefault(); // Impede o recarregamento da página
            const form = event.target;
            const formData = new FormData(form);
            const inputField = form.querySelector('.comment-input');
            const postId = formData.get('id_postagem');
            const parentCommentId = formData.get('id_comentario_pai'); // Será null se for um comentário principal

            // Envia os dados para o script PHP via AJAX
            fetch(form.action, { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Usa a função auxiliar para criar o HTML do novo comentário/resposta
                    const newCommentHTML = buildCommentHTML(data.comment, postId, !!parentCommentId, parentCommentId);

                    if (parentCommentId) {
                        // É uma RESPOSTA: Adiciona dentro do container de respostas do comentário pai
                        const parentCommentWrapper = document.getElementById(`comment-wrapper-${parentCommentId}`);
                        let repliesContainer = parentCommentWrapper.querySelector('.comment-replies');
                        // Se for a primeira resposta, cria o container
                        if (!repliesContainer) {
                             parentCommentWrapper.querySelector('.comment-view-mode').insertAdjacentHTML('beforeend', '<div class="comment-replies"></div>');
                             repliesContainer = parentCommentWrapper.querySelector('.comment-replies');
                        }
                        // Adiciona o HTML da nova resposta
                        repliesContainer.innerHTML += newCommentHTML;
                        // Esconde o formulário de resposta novamente
                        form.closest('.reply-form-container').classList.add('is-hidden');
                    } else {
                        // É um COMENTÁRIO PRINCIPAL: Adiciona no final da lista principal
                        const commentList = detailsColumn.querySelector('.full-comment-list');
                        // Remove a mensagem "Nenhum comentário" se ela existir
                        const noCommentsMsg = commentList.querySelector('.no-comments-message');
                        if(noCommentsMsg) noCommentsMsg.remove();
                        // Adiciona o HTML do novo comentário
                        commentList.innerHTML += newCommentHTML;
                        // Esconde o formulário principal do footer
                        form.closest('.add-comment-form-container').classList.add('is-hidden');
                    }
                    // Limpa o campo de texto
                    inputField.value = '';
                } else {
                    // Mostra um erro se o PHP retornar falha
                    alert('Erro: ' + (data.error || 'Não foi possível enviar o comentário.'));
                }
            })
            .catch(error => {
                console.error('Erro ao enviar formulário:', error);
                alert('Ocorreu um erro de rede ao enviar o comentário.');
            });
        }
    });

});