<?php
/**
 * Template para o cabeçalho das páginas de perfil.
 * Espera que as seguintes variáveis PHP estejam definidas:
 * - $id_do_perfil_a_exibir: O ID do usuário cujo perfil está sendo visto.
 * - $id_usuario_logado: O ID do usuário que está logado.
 * - $perfil_data: Um array com os dados do usuário (AGORA INCLUI 'ultimo_acesso').
 * - $sao_amigos: (NOVO) Booleano que indica se o usuário logado é amigo do perfil exibido.
 * - $status_amizade: O status da amizade ('pendente', 'aceite', null, etc.).
 * - $amizade_id: O ID da linha na tabela Amizades.
 * - $id_remetente_pedido: O ID de quem enviou o pedido pendente.
 */

// --- [LÓGICA ATUALIZADA - PASSO 4.1] ---
if (!function_exists('formatar_status_online')) {
    function formatar_status_online($ultimo_acesso_timestamp) {
        if ($ultimo_acesso_timestamp === null) {
            return null; // Não mostra nada se nunca logou
        }

        try {
            $fuso_horario = new DateTimeZone('America/Sao_Paulo'); // Fuso correto
            $agora = new DateTime("now", $fuso_horario);
            $ultimo_acesso = new DateTime($ultimo_acesso_timestamp, $fuso_horario);
            
            $diferenca_em_minutos = floor(($agora->getTimestamp() - $ultimo_acesso->getTimestamp()) / 60);

            if ($diferenca_em_minutos < 5) {
                // Se for menos de 5 minutos, está online. Retorna SÓ a bolinha.
                return '<span class="status-dot status-online" title="Online"></span>';
            }
            
            // Se estiver offline, não retorna nada.
            return null; 

        } catch (Exception $e) {
            error_log("Erro ao formatar data ultimo_acesso: " . $e->getMessage());
            return null; // Não mostra nada em caso de erro
        }
    }
}
// --- [FIM DA LÓGICA ATUALIZADA] ---
?>
<div class="profile-page-header">
    <div class="profile-avatar-large">
        <?php if (!empty($perfil_data['foto_perfil_url'])): ?>
            <img src="<?php echo htmlspecialchars($perfil_data['foto_perfil_url']); ?>" alt="Foto de Perfil">
        <?php else: ?>
            <i class="fas fa-user"></i>
        <?php endif; ?>
    </div>
    <div class="profile-header-info">
        
        <h1>
            <?php echo htmlspecialchars($perfil_data['nome'] . ' ' . $perfil_data['sobrenome']); ?>
            <?php
            // --- [NOVA REGRA DE PRIVACIDADE ADICIONADA AQUI] ---
            // Só mostra o status (bolinha) se:
            // 1. For o perfil do próprio usuário
            // 2. Ou se eles forem amigos
            if ( ($id_usuario_logado == $id_do_perfil_a_exibir) || $sao_amigos ) {
                echo formatar_status_online($perfil_data['ultimo_acesso'] ?? null);
            }
            // --- [FIM DA NOVA REGRA DE PRIVACIDADE] ---
            ?>
        </h1>
        
        <p>@<?php echo htmlspecialchars($perfil_data['nome_de_usuario']); ?></p>

        <?php
        // --- [LÓGICA DE EXIBIÇÃO ANTERIOR REMOVIDA DAQUI] ---
        ?>

        <?php if (!empty($perfil_data['biografia'])): 
            $limite_caracteres = 100;
            $biografia_completa = $perfil_data['biografia'];
            $biografia_curta = $biografia_completa;
            $precisa_ver_mais = false;

            if (mb_strlen($biografia_completa) > $limite_caracteres) {
                $biografia_curta = mb_strimwidth($biografia_completa, 0, $limite_caracteres, "...");
                $precisa_ver_mais = true;
            }
        ?>
            <p class="profile-bio">
                <?php echo nl2br(htmlspecialchars($biografia_curta)); ?>
                <?php if ($precisa_ver_mais): ?>
                    <a href="sobre_perfil.php?id=<?php echo $id_do_perfil_a_exibir; ?>">ver mais</a>
                <?php endif; ?>
            </p>
        <?php endif; ?>

    </div>
    
    <div class="profile-header-actions">
        <?php // --- BLOCO DE LÓGICA ATUALIZADO PARA OS BOTÕES DE AMIZADE ---
        if ($id_do_perfil_a_exibir != $id_usuario_logado):
        
            switch ($status_amizade) {
                case 'aceite':
                    // Já são amigos
                    echo '<div class="friend-actions-dropdown">';
                    echo '<button class="action-btn-friends"><i class="fas fa-user-check"></i> Amigos</button>';
                    echo '<div class="dropdown-content">';
                    echo '<a href="#" class="cancelar-amizade-btn" data-amizade-id="' . $amizade_id . '"><i class="fas fa-user-times"></i> Desfazer Amizade</a>';
                    echo '</div>';
                    echo '</div>';
                    break;

                case 'pendente':
                    if ($id_remetente_pedido == $id_usuario_logado) {
                        // --- ALTERAÇÃO AQUI ---
                        // O utilizador logado enviou o pedido, agora é um dropdown
                        echo '<div class="friend-actions-dropdown">';
                        echo '<button class="action-btn-pending"><i class="fas fa-user-clock"></i> Pedido Pendente</button>';
                        echo '<div class="dropdown-content">';
                        echo '<a href="#" class="cancelar-pedido-btn" data-amizade-id="' . $amizade_id . '"><i class="fas fa-times-circle"></i> Cancelar Pedido</a>';
                        echo '</div>';
                        echo '</div>';
                    } else {
                        // O utilizador logado recebeu o pedido
                        echo '<div class="friend-actions-dropdown">';
                        echo '<button class="action-btn-respond"><i class="fas fa-user-plus"></i> Responder Pedido</button>';
                        echo '<div class="dropdown-content">';
                        echo '<a href="#" class="aceitar-pedido-btn" data-amizade-id="' . $amizade_id . '">Aceitar</a>';
                        echo '<a href="#" class="recusar-pedido-btn" data-amizade-id="' . $amizade_id . '">Recusar</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                    break;
                
                default:
                    // Não há relação
                    echo '<button class="action-btn-add" id="add-friend-btn" data-destinatario-id="' . $id_do_perfil_a_exibir . '"><i class="fas fa-user-plus"></i> Adicionar Amigo</button>';
                    break;
            }
        
        ?>
        <div class="profile-header-options post-options">
            <button class="post-options-btn"><i class="fas fa-ellipsis-h"></i></button>
            <div class="post-options-menu is-hidden">
                <a href="#" class="report-btn" data-content-type="usuario" data-content-id="<?php echo $id_do_perfil_a_exibir; ?>"><i class="fas fa-flag"></i> Denunciar Perfil</a>
                <a href="#"><i class="fas fa-ban"></i> Bloquear Usuário</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>