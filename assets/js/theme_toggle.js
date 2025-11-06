/* ==========================================================
   LÓGICA DE ALTERNÂNCIA DE TEMA (MODO ESCURO) - v3.0 CORRIGIDA
   ========================================================== */
(function() {
    
    // --- CORREÇÃO APLICADA AQUI ---
    // Procura TODOS os botões de tema (desktop e mobile) pela CLASSE
    const themeToggleBtns = document.querySelectorAll('.theme-toggle-btn-menu');
    
    // Se nenhum botão for encontrado, não faz nada
    if (themeToggleBtns.length === 0) {
        return;
    }
    
    // Modifica o 'documentElement' (a tag <html>)
    const htmlElement = document.documentElement;

    // Função para atualizar os ícones em TODOS os botões
    function updateIcons(theme) {
        themeToggleBtns.forEach(btn => {
            const moonIcon = btn.querySelector('.theme-icon-moon');
            const sunIcon = btn.querySelector('.theme-icon-sun');

            if (theme === 'dark') {
                moonIcon.classList.add('is-hidden');
                sunIcon.classList.remove('is-hidden');
            } else {
                moonIcon.classList.remove('is-hidden');
                sunIcon.classList.add('is-hidden');
            }
        });
    }

    // Adiciona o 'ouvinte' de clique a CADA botão encontrado
    themeToggleBtns.forEach(btn => {
        btn.addEventListener('click', function(event) {
            event.preventDefault(); // Impede que o link '#' navegue
            
            // Verifica o tema lendo a classe da tag <html>
            const isDark = htmlElement.classList.contains('dark-mode');
            const newTheme = isDark ? 'light' : 'dark';

            // 1. Salva a preferência no localStorage
            localStorage.setItem('theme', newTheme);
            
            // 2. Aplica a classe à tag <html>
            htmlElement.classList.toggle('dark-mode', newTheme === 'dark');
            
            // 3. Atualiza os ícones em ambos os menus
            updateIcons(newTheme);
        });
    });


    // --- Sincronização Inicial ---
    // Sincroniza os ícones de todos os botões no carregamento da página
    const isDarkOnLoad = htmlElement.classList.contains('dark-mode');
    const currentThemeOnLoad = isDarkOnLoad ? 'dark' : 'light';
    updateIcons(currentThemeOnLoad);

})();