document.addEventListener('DOMContentLoaded', function() {

    // Função genérica para enviar pedidos para a API de amizade
    const enviarRequisicaoAmizade = (url, formData) => {
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.showToast) {
                    window.showToast(data.message);
                }
                // Recarrega a página para mostrar o estado atualizado dos botões
                setTimeout(() => {
                    location.reload();
                }, 1500); // Dá tempo para o utilizador ler o toast
            } else {
                alert(data.error || 'Ocorreu um erro desconhecido.');
            }
        })
        .catch(error => {
            console.error('Erro de rede:', error);
            alert('Não foi possível conectar ao servidor.');
        });
    };

    // "Ouvinte" de cliques para todas as ações de amizade
    document.body.addEventListener('click', function(event) {
        
        // --- Ação: Adicionar Amigo ---
        const addBtn = event.target.closest('#add-friend-btn');
        if (addBtn) {
            event.preventDefault();
            const destinatarioId = addBtn.dataset.destinatarioId;
            const formData = new FormData();
            formData.append('id_usuario_recebe', destinatarioId);
            enviarRequisicaoAmizade('api/amizade/enviar_pedido.php', formData);
        }

        // --- Ação: Aceitar Pedido ---
        const acceptBtn = event.target.closest('.aceitar-pedido-btn');
        if (acceptBtn) {
            event.preventDefault();
            const amizadeId = acceptBtn.dataset.amizadeId;
            const formData = new FormData();
            formData.append('id_amizade', amizadeId);
            enviarRequisicaoAmizade('api/amizade/aceitar_pedido.php', formData);
        }

        // --- Ação: Recusar Pedido ---
        const refuseBtn = event.target.closest('.recusar-pedido-btn');
        if (refuseBtn) {
            event.preventDefault();
            const amizadeId = refuseBtn.dataset.amizadeId;
            const formData = new FormData();
            formData.append('id_amizade', amizadeId);
            enviarRequisicaoAmizade('api/amizade/recusar_pedido.php', formData);
        }
        
        // --- NOVA AÇÃO ADICIONADA: Cancelar Pedido Enviado ---
        const cancelRequestBtn = event.target.closest('.cancelar-pedido-btn');
        if (cancelRequestBtn) {
            event.preventDefault();
            if (confirm('Tem a certeza de que deseja cancelar este pedido de amizade?')) {
                const amizadeId = cancelRequestBtn.dataset.amizadeId;
                const formData = new FormData();
                formData.append('id_amizade', amizadeId);
                enviarRequisicaoAmizade('api/amizade/cancelar_pedido.php', formData);
            }
        }

        // --- Ação: Cancelar Amizade ---
        const cancelBtn = event.target.closest('.cancelar-amizade-btn');
        if (cancelBtn) {
            event.preventDefault();
            if (confirm('Tem a certeza de que deseja desfazer esta amizade?')) {
                const amizadeId = cancelBtn.dataset.amizadeId;
                const formData = new FormData();
                formData.append('id_amizade', amizadeId);
                enviarRequisicaoAmizade('api/amizade/cancelar_amizade.php', formData);
            }
        }
    });
});