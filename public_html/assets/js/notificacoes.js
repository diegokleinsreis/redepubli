document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. SELECIONAR ELEMENTOS ---
    const notificationCountSpans = document.querySelectorAll('.notification-count');
    
    // --- NOVOS SELETORES UNIFICADOS ---
    // Encontra TODOS os botões de sino (desktop e mobile)
    const notificationToggleBtns = document.querySelectorAll('.notification-dropdown-toggle');
    // Encontra TODOS os painéis (desktop e mobile)
    const notificationPanels = document.querySelectorAll('.notification-panel-wrapper');
    // Encontra TODAS as listas de notificações
    const notificationLists = document.querySelectorAll('.notifications-list');

    // --- 2. FUNÇÕES PRINCIPAIS ---
    function fetchNotifications() {
        fetch('api/notificacoes/buscar_notificacoes.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationUI(data.nao_lidas);
                    buildNotificationPanels(data.notificacoes);
                }
            })
            .catch(error => console.error('Erro de rede:', error));
    }

    function updateNotificationUI(count) {
        notificationCountSpans.forEach(span => {
            span.style.display = count > 0 ? 'block' : 'none';
            if (count > 0) span.textContent = count;
        });
    }

    function buildNotificationPanels(notifications) {
        let contentHTML = '';
        if (notifications.length === 0) {
            contentHTML = '<p class="no-notifications-message">Nenhuma notificação nova.</p>';
        } else {
            notifications.forEach(notif => {
                const avatarHTML = notif.remetente_foto ? `<img src="${notif.remetente_foto}" alt="Avatar">` : `<div class="default-avatar-icon"><i class="fas fa-user"></i></div>`;
                let text = '';
                let link = '';
                
                switch (notif.tipo) {
                    case 'curtida_post':
                        text = 'curtiu a sua publicação.';
                        link = `postagem.php?id=${notif.id_referencia}`;
                        break;
                    case 'comentario_post':
                        text = 'comentou na sua publicação.';
                        link = `postagem.php?id=${notif.id_referencia}`;
                        break;
                    case 'curtida_comentario':
                        text = 'curtiu o seu comentário.';
                        link = `postagem.php?id=${notif.id_referencia}`;
                        break;
                    case 'pedido_amizade':
                        text = 'enviou-lhe um pedido de amizade.';
                        link = `perfil.php?id=${notif.id_referencia}`;
                        break;
                    default:
                        text = 'interagiu consigo.';
                        link = '#';
                }

                contentHTML += `
                    <a href="${link}" class="notification-item ${notif.lida == 0 ? 'unread' : ''}" data-id="${notif.id}">
                        <div class="notification-avatar">${avatarHTML}</div>
                        <div class="notification-text"><p><span class="user-name">${notif.remetente_nome} ${notif.remetente_sobrenome}</span> ${text}</p></div>
                    </a>`;
            });
        }
        // Popula TODAS as listas encontradas
        notificationLists.forEach(list => {
            list.innerHTML = contentHTML;
        });
    }

    function markOneAsRead(notificationId, clickedElement) {
        clickedElement.classList.remove('unread');
        const currentCount = parseInt(notificationCountSpans[0]?.textContent || '0');
        updateNotificationUI(currentCount - 1);

        const formData = new FormData();
        formData.append('id', notificationId);

        fetch('api/notificacoes/marcar_uma_como_lida.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                clickedElement.classList.add('unread');
                updateNotificationUI(currentCount);
                console.error('Falha ao marcar notificação como lida:', data.error);
            }
        });
    }


    // --- 3. NOVOS EVENTOS DE CLIQUE UNIFICADOS ---

    // Adiciona um listener para CADA botão de sino
    notificationToggleBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Encontra o painel que é "irmão" do "pai" do botão
            const panel = btn.closest('.nav-dropdown-toggle').nextElementSibling;

            if (panel && panel.classList.contains('notification-panel-wrapper')) {
                // Alterna o painel
                panel.classList.toggle('is-hidden');
                // Alterna a seta
                btn.classList.toggle('active');
            }
        });
    });

    // Adiciona o listener para CADA painel (para marcar como lida)
    notificationPanels.forEach(panel => {
        panel.addEventListener('click', function(event) {
            const clickedItem = event.target.closest('.notification-item');
            if (clickedItem && clickedItem.classList.contains('unread')) {
                const notificationId = clickedItem.dataset.id;
                markOneAsRead(notificationId, clickedItem);
                // O link continuará a ser seguido
            }
        });
    });

    // Fecha os painéis de notificação se clicar fora
    document.addEventListener('click', function(event) {
        // Se o clique NÃO foi num botão de sino E NÃO foi dentro de um painel de notificação
        if (!event.target.closest('.notification-dropdown-toggle') && !event.target.closest('.notification-panel-wrapper')) {
            notificationPanels.forEach(panel => {
                panel.classList.add('is-hidden');
            });
            notificationToggleBtns.forEach(btn => {
                btn.classList.remove('active');
            });
        }
    });

    // --- 4. EXECUÇÃO INICIAL ---
    fetchNotifications();
    setInterval(fetchNotifications, 30000); // 30 segundos
});