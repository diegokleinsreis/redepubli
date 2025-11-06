// Aguarda o documento HTML ser completamente carregado
document.addEventListener('DOMContentLoaded', function() {

    // --- FUNÇÃO GLOBAL PARA NOTIFICAÇÕES 'TOAST' ---
    window.showToast = function(message) {
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) {
            existingToast.remove();
        }
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
        setTimeout(() => {
            toast.remove();
        }, 3500);
    }

    // --- LÓGICA PARA O MENU MOBILE ---
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


    // --- LÓGICA ATUALIZADA PARA O DROPDOWN DE CONFIGURAÇÕES (AGORA COM CLASSES) ---
    // Encontra TODOS os gatilhos de dropdown (um no desktop, um no mobile)
    const configToggles = document.querySelectorAll('.config-dropdown-toggle');

    configToggles.forEach(toggleBtn => {
        
        toggleBtn.addEventListener('click', function(event) {
            event.preventDefault(); // Impede o link de navegar
            
            // Encontra o submenu específico relativo a ESTE botão
            // Assume que o submenu é o próximo elemento irmão
            const configSubmenu = toggleBtn.closest('.nav-dropdown-toggle').nextElementSibling;

            if (configSubmenu && configSubmenu.classList.contains('config-submenu')) {
                // Alterna o estado do submenu (mostra/esconde)
                configSubmenu.classList.toggle('is-hidden');
                
                // Adiciona/remove a classe 'active' ao link para girar a seta
                toggleBtn.classList.toggle('active');
            }
        });
    });
    // --- FIM DA NOVA LÓGICA ---


    // --- OUVINTE DE CLIQUES ÚNICO E CENTRALIZADO PARA TODA A PÁGINA ---
    document.body.addEventListener('click', function(event) {
        
        // --- LÓGICA PARA MENUS DROPDOWN (TRÊS PONTINHOS) ---
        const clickedMenuBtn = event.target.closest('.post-options-btn, .comment-options-btn');
        let activeMenu = null;

        if (clickedMenuBtn) {
            // Previne que o clique se propague e feche o menu imediatamente
            event.stopPropagation(); 
            activeMenu = clickedMenuBtn.nextElementSibling;
            activeMenu.classList.toggle('is-hidden');
        }

        // Fecha todos os outros menus que não o que foi clicado
        document.querySelectorAll('.post-options-menu, .comment-options-menu').forEach(menu => {
            if (menu !== activeMenu) {
                menu.classList.add('is-hidden');
            }
        });

        // --- LÓGICA PARA O BOTÃO DE DENÚNCIA ---
        const reportBtn = event.target.closest('.report-btn');
        if (reportBtn) {
            event.preventDefault();
            // Chama a função do denuncia.js
            if (typeof openReportModal === 'function') {
                openReportModal(reportBtn);
            }
        }

        // --- LÓGICA PARA FOCAR NO CAMPO DE COMENTÁRIO ---
        const commentBtn = event.target.closest('#focus-comment-btn');
        if(commentBtn) {
            if (!window.location.pathname.includes('postagem.php')) {
                event.preventDefault();
                const postCard = commentBtn.closest('.post-card');
                if (postCard) {
                    const commentInput = postCard.querySelector('.comment-input');
                    if (commentInput) {
                        commentInput.focus();
                    }
                }
            }
        }
    });

    // --- OUVINTE ADICIONAL PARA FECHAR MENUS AO CLICAR EM QUALQUER LUGAR ---
    document.addEventListener('click', function(event) {
        // Se o clique NÃO foi num botão de abrir menu E NÃO foi dentro de um menu aberto, fecha todos.
        if (!event.target.closest('.post-options-btn, .comment-options-btn') && !event.target.closest('.post-options-menu, .comment-options-menu')) {
            document.querySelectorAll('.post-options-menu, .comment-options-menu').forEach(menu => {
                menu.classList.add('is-hidden');
            });
        }
    });


    // --- [INÍCIO DA NOVA LÓGICA - HEARTBEAT DE STATUS ONLINE] ---
    
    /**
     * Função Heartbeat (Pulsação)
     * Chama a API para atualizar o 'ultimo_acesso' do usuário.
     * Isso garante que o status "Online" permaneça ativo enquanto a página 
     * estiver aberta, mesmo sem navegação.
     */
    function enviarHeartbeat() {
        
        // ===== ALTERAÇÃO FEITA AQUI =====
        // Adicionado um "cache buster" (o timestamp) para garantir que o navegador
        // não use uma versão em cache da chamada.
        const cacheBuster = new Date().getTime();
        fetch('api/usuarios/atualizar_status_online.php?t=' + cacheBuster, {
        // ===== FIM DA ALTERAÇÃO =====
            method: 'POST', 
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        })
        .then(response => {
            if (!response.ok) {
                // Se a API falhar (ex: erro 500), regista no console
                console.error('Falha no heartbeat do status online.');
            }
            // Não precisamos fazer nada com a resposta se for bem-sucedida
        })
        .catch(error => {
            // Se houver um erro de rede
            console.error('Erro de rede no heartbeat:', error);
        });
    }

    // 1. Envia um heartbeat imediatamente ao carregar a página
    // (Garante que o status seja atualizado assim que o JS carregar, 
    // caso o usuário fique parado na primeira página que abriu)
    enviarHeartbeat(); 

    // 2. Configura o intervalo (Interval)
    // Define para executar a função 'enviarHeartbeat' a cada 2 minutos
    // 2 minutos = 120.000 milissegundos
    setInterval(enviarHeartbeat, 120000); 

    // --- [FIM DA NOVA LÓGICA - HEARTBEAT DE STATUS ONLINE] ---

}); // Fim do DOMContentLoaded