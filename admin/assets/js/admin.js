document.addEventListener('DOMContentLoaded', function() {

    // --- LÓGICA PARA O MENU MOBILE DO ADMIN ---
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const mobileNav = document.getElementById('mobile-nav-panel');
    const overlay = document.getElementById('overlay');
    const closeBtn = document.getElementById('close-mobile-menu');

    function openMenu() {
        if (mobileNav) mobileNav.classList.add('is-open');
        if (overlay) overlay.classList.add('is-visible');
    }
    function closeMenu() {
        if (mobileNav) mobileNav.classList.remove('is-open');
        if (overlay) overlay.classList.remove('is-visible');
    }
    
    if (menuToggle) menuToggle.addEventListener('click', openMenu);
    if (overlay) overlay.addEventListener('click', closeMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);


    // --- LÓGICA PARA O MODAL DE DENÚNCIAS ---
    const modal = document.getElementById('denunciaModal');
    const span = document.getElementsByClassName('admin-modal-close')[0];
    const viewButtons = document.querySelectorAll('.view-btn');

    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const denunciaId = this.dataset.denunciaId;
            const modalContent = document.getElementById('denunciaConteudo');
            
            // --- INÍCIO DA MODIFICAÇÃO ---
            // Seleciona os novos contêineres de botões (cabeçalho e rodapé)
            const modalHeaderActions = document.getElementById('admin-modal-header-actions');
            const modalFooterActions = document.getElementById('denunciaAcoes');
            // --- FIM DA MODIFICAÇÃO ---

            // Limpa o conteúdo anterior
            modalContent.innerHTML = '<p>Carregando...</p>';
            modalHeaderActions.innerHTML = '';
            modalFooterActions.innerHTML = '';
            modal.style.display = "block";

            fetch(`../api/admin/obter_detalhes_denuncia.php?id=${denunciaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modalContent.innerHTML = data.html;
                        
                        // --- LÓGICA DOS BOTÕES ATUALIZADA ---
                        const d = data.denuncia;
                        
                        // 1. Adiciona o botão "Ver Publicação" no contêiner do cabeçalho
                        if (data.post_id_referencia) {
                            modalHeaderActions.innerHTML = `<a href="../postagem.php?id=${data.post_id_referencia}" class="action-btn view-post-btn" target="_blank">Ver Publicação</a>`;
                        }

                        // 2. Adiciona os botões de ação no contêiner do rodapé
                        let linkOcultar = '';
                        if (d.tipo_conteudo === 'post') {
                            linkOcultar = `../api/admin/toggle_post_status.php?id=${d.id_conteudo}&denuncia_id=${d.id}`;
                        } else if (d.tipo_conteudo === 'comentario') {
                            linkOcultar = `../api/admin/toggle_comment_status.php?id=${d.id_conteudo}&denuncia_id=${d.id}`;
                        }
                        
                        modalFooterActions.innerHTML = `
                            <a href="../api/admin/atualizar_status_denuncia.php?id=${d.id}&status=ignorado" class="action-btn ignore-btn" onclick="return confirmAction(event, 'Tem a certeza de que deseja IGNORAR esta denúncia?');">Ignorar</a>
                            <a href="../api/admin/atualizar_status_denuncia.php?id=${d.id}&status=revisado" class="action-btn approve-btn" onclick="return confirmAction(event, 'Deseja marcar como REVISADA sem ocultar o conteúdo?');">Manter Conteúdo (Revisado)</a>
                            <a href="${linkOcultar}" class="action-btn hide-btn" onclick="return confirmAction(event, 'Atenção: Isto irá OCULTAR o conteúdo e marcar a denúncia como REVISADA. Deseja continuar?');">Ocultar Conteúdo</a>
                        `;
                        // --- FIM DA LÓGICA ATUALIZADA ---

                    } else {
                        modalContent.innerHTML = `<p><b>Erro ao carregar detalhes:</b> ${data.message}</p>`;
                    }
                })
                .catch(error => {
                    modalContent.innerHTML = `<p>Ocorreu um erro de comunicação com o servidor. Verifique a consola do navegador para mais detalhes.</p>`;
                    console.error('Fetch Error:', error);
                });
        });
    });

    // Função para confirmar e recarregar a página
    window.confirmAction = function(event, message) {
        if (!confirm(message)) {
            event.preventDefault();
            return false;
        }
        setTimeout(() => {
            location.reload();
        }, 500);
    }

    // Lógica para fechar o Modal
    if (span) {
        span.onclick = function() {
            modal.style.display = "none";
        }
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

});