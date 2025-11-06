/**
 * denuncia.js - Módulo para gerir o modal de denúncia.
 * Contém as funções para abrir, fechar e processar o envio de uma denúncia.
 */

// Função para ABRIR o modal de denúncia. Será chamada pelo main.js
function openReportModal(reportBtn) {
    const modal = document.getElementById('report-modal');
    const overlay = document.getElementById('report-modal-overlay');
    const title = modal.querySelector('.report-modal-title');
    const contentReportList = document.getElementById('content-report-options');
    const userReportList = document.getElementById('user-report-options');

    // Se o modal não existir na página, não faz nada
    if (!modal) return;

    // Pega as informações do botão que foi clicado
    const contentType = reportBtn.dataset.contentType;
    const contentId = reportBtn.dataset.contentId;

    // Guarda as informações no próprio modal para uso posterior
    modal.dataset.contentType = contentType;
    modal.dataset.contentId = contentId;

    // Ajusta o título e a lista de motivos com base no tipo de conteúdo
    if (contentType === 'usuario') {
        title.textContent = 'Por que você está denunciando esse perfil?';
        userReportList.style.display = 'block';
        contentReportList.style.display = 'none';
    } else { // Para 'post' ou 'comentario'
        title.textContent = 'Por que você está denunciando isso?';
        userReportList.style.display = 'none';
        contentReportList.style.display = 'block';
    }

    // Torna o modal e o fundo escuro visíveis
    modal.classList.add('is-visible');
    overlay.classList.add('is-visible');
}

// Função para FECHAR o modal de denúncia
function closeReportModal() {
    const modal = document.getElementById('report-modal');
    const overlay = document.getElementById('report-modal-overlay');
    
    if (modal) modal.classList.remove('is-visible');
    if (overlay) overlay.classList.remove('is-visible');
}

// Este bloco de código é executado uma vez quando a página carrega.
// Ele prepara a lógica interna do modal (botão de fechar e opções de denúncia).
document.addEventListener('DOMContentLoaded', function() {
    const reportModal = document.getElementById('report-modal');
    // Se não houver modal nesta página, o script não faz mais nada.
    if (!reportModal) return;

    const reportModalOverlay = document.getElementById('report-modal-overlay');
    const closeReportModalBtn = document.getElementById('close-report-modal');
    
    // Atribui a função de fechar aos elementos corretos
    if (closeReportModalBtn) closeReportModalBtn.addEventListener('click', closeReportModal);
    if (reportModalOverlay) reportModalOverlay.addEventListener('click', closeReportModal);

    // Adiciona a lógica para processar o clique numa opção de denúncia
    reportModal.querySelectorAll('.report-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            const contentType = reportModal.dataset.contentType;
            const contentId = reportModal.dataset.contentId;
            const motivo = this.dataset.motivo;

            const formData = new FormData();
            formData.append('content_type', contentType);
            formData.append('content_id', contentId);
            formData.append('motivo', motivo);

            fetch('api/denuncias/criar_denuncia.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success && window.showToast) {
                        window.showToast(data.message || 'Denúncia enviada com sucesso!');
                    } else {
                        alert(data.error || 'Ocorreu um erro ao enviar a denúncia.');
                    }
                })
                .catch(error => console.error('Erro na requisição de denúncia:', error))
                .finally(closeReportModal); // Fecha o modal após a tentativa de envio
        });
    });
});