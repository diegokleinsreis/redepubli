document.addEventListener('DOMContentLoaded', function() {

    // Função genérica para lidar com o envio de formulários via Fetch API
    const handleFormSubmit = (form, successCallback) => {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Impede o recarregamento da página

            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.innerHTML = 'A guardar...';
            submitButton.disabled = true;

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (window.showToast) {
                        window.showToast(data.message || 'Alterações guardadas com sucesso!');
                    }
                    if (successCallback) {
                        successCallback(data); // Executa uma função específica de sucesso se necessário
                    }
                } else {
                    alert(data.error || 'Ocorreu um erro desconhecido.');
                }
            })
            .catch(error => {
                console.error('Erro de rede:', error);
                alert('Não foi possível conectar ao servidor. Verifique a sua conexão com a internet.');
            })
            .finally(() => {
                // Restaura o botão ao seu estado original
                submitButton.innerHTML = originalButtonText;
                submitButton.disabled = false;
            });
        });
    };

    // --- Aplica a função aos três formulários ---

    const formPerfil = document.getElementById('form-perfil');
    if (formPerfil) {
        handleFormSubmit(formPerfil, (data) => {
            // Callback de sucesso para o formulário de perfil
            if (data.new_avatar_url) {
                // Se uma nova foto de perfil foi enviada, atualiza a imagem na página
                document.getElementById('avatar-preview-img').src = data.new_avatar_url;
                // Atualiza também o avatar no menu lateral
                const sidebarAvatar = document.querySelector('.sidebar-nav .nav-avatar img');
                if(sidebarAvatar) {
                    sidebarAvatar.src = data.new_avatar_url;
                }
            }
        });
    }

    const formConta = document.getElementById('form-conta');
    if (formConta) {
        handleFormSubmit(formConta, () => {
            // Limpa os campos de senha após o sucesso
            document.getElementById('senha_atual').value = '';
            document.getElementById('nova_senha').value = '';
            document.getElementById('confirmar_nova_senha').value = '';
        });
    }

    const formPrivacidade = document.getElementById('form-privacidade');
    if (formPrivacidade) {
        handleFormSubmit(formPrivacidade);
    }

    // --- Lógica para o preview da imagem de perfil ---
    const inputFile = document.getElementById('foto_perfil');
    const previewImg = document.getElementById('avatar-preview-img');
    if (inputFile && previewImg) {
        inputFile.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
});