<div class="overlay" id="overlay"></div>
<aside class="mobile-nav-panel" id="mobile-nav-panel">
    <button class="close-btn" id="close-mobile-menu">&times;</button>
    <?php 
    // Inclui os links do menu (que AGORA já não têm o painel)
    include 'menu_links.php'; 
    ?>

    <div id="notifications-panel-mobile" class="notifications-panel is-hidden">
        <div class="notifications-header">
            <h3>Notificações</h3>
        </div>
        <div class="notifications-list">
            </div>
        <div class="notifications-footer">
            <a href="historico_notificacoes.php" class="primary-btn">Ver todas as notificações</a>
        </div>
    </div>
    </aside>