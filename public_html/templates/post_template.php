<?php
/**
 * Template para exibir uma única postagem.
 * Espera que as variáveis $post e $user_id (ID do usuário logado) estejam disponíveis.
 */
?>
<div class="post-header">
    <div class="post-author-avatar">
        <a href="perfil.php?id=<?php echo $post['autor_id']; ?>">
            <?php if (!empty($post['foto_perfil_url'])): ?>
                <img src="<?php echo htmlspecialchars($post['foto_perfil_url']); ?>" alt="Foto de <?php echo htmlspecialchars($post['nome']); ?>">
            <?php else: ?>
                <i class="fas fa-user"></i>
            <?php endif; ?>
        </a>
    </div>
    <div class="post-author-info">
        <a href="perfil.php?id=<?php echo $post['autor_id']; ?>" class="post-author-name-link">
            <span class="post-author-name"><?php echo htmlspecialchars($post['nome'] . ' ' . $post['sobrenome']); ?></span>
        </a>
        <span class="post-timestamp"><?php echo date("d/m/Y \à\s H:i", strtotime($post['data_postagem'])); ?></span>
    </div>
    
    <div class="post-options">
        <button class="post-options-btn" data-postid="<?php echo $post['id']; ?>"><i class="fas fa-ellipsis-h"></i></button>
        <div class="post-options-menu is-hidden" id="post-options-menu-<?php echo $post['id']; ?>">
            
            <a href="#" class="post-save-btn" data-postid="<?php echo $post['id']; ?>">
                <i class="far fa-bookmark"></i> 
                <span class="save-text"><?php echo ($post['usuario_salvou'] > 0) ? 'Remover dos Salvos' : 'Salvar Post'; ?></span>
            </a>

            <?php if ($post['autor_id'] != $user_id): ?>
                <a href="#" class="report-btn" data-content-type="post" data-content-id="<?php echo $post['id']; ?>">
                    <i class="fas fa-flag"></i> Denunciar
                </a>
            <?php endif; ?>

            <?php if ($post['autor_id'] == $user_id): ?>
                <a href="#" class="post-edit-btn" data-postid="<?php echo $post['id']; ?>"><i class="fas fa-edit"></i> Editar</a>
                <a href="#" class="post-delete-btn" data-postid="<?php echo $post['id']; ?>"><i class="fas fa-trash-alt"></i> Excluir</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="post-view-mode" id="post-view-<?php echo $post['id']; ?>">
    <div class="post-content">
        <p><?php echo nl2br(htmlspecialchars($post['conteudo_texto'])); ?></p>
    </div>
    
    <?php if (!empty($post['url_media'])): ?>
        <div class="post-media-container">
            <?php if ($post['tipo_media'] === 'imagem'): ?>
                <?php // --- INÍCIO DA CORREÇÃO --- ?>
                <div class="post-image-container post-image-clickable" data-postid="<?php echo $post['id']; ?>">
                    <img src="<?php echo htmlspecialchars($post['url_media']); ?>" alt="Imagem da postagem">
                </div>
                <?php // --- FIM DA CORREÇÃO --- ?>
            <?php elseif ($post['tipo_media'] === 'video'): ?>
                <div class="post-video-container">
                    <video controls>
                        <source src="<?php echo htmlspecialchars($post['url_media']); ?>" type="video/mp4">
                        Seu navegador não suporta a tag de vídeo.
                    </video>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<div class="post-edit-mode is-hidden" id="post-edit-<?php echo $post['id']; ?>">
    <textarea class="post-edit-textarea"><?php echo htmlspecialchars($post['conteudo_texto']); ?></textarea>
    <div class="post-edit-actions">
        <button class="post-edit-cancel-btn" data-postid="<?php echo $post['id']; ?>">Cancelar</button>
        <button class="post-edit-save-btn primary-btn-small" data-postid="<?php echo $post['id']; ?>">Salvar Alterações</button>
    </div>
</div>

<div class="post-stats">
    <span class="like-count"><?php echo $post['total_curtidas']; ?> curtida<?php echo ($post['total_curtidas'] != 1) ? 's' : ''; ?></span>
    <a href="postagem.php?id=<?php echo $post['id']; ?>" class="comment-count"><?php echo $post['total_comentarios']; ?> comentário<?php echo ($post['total_comentarios'] != 1) ? 's' : ''; ?></a>
</div>

<div class="post-actions">
    <button class="action-btn like-btn <?php echo ($post['usuario_curtiu'] > 0) ? 'active' : ''; ?>" data-postid="<?php echo $post['id']; ?>"><i class="far fa-thumbs-up"></i> Curtir</button>
    <a href="postagem.php?id=<?php echo $post['id']; ?>" class="action-btn" id="focus-comment-btn"><i class="far fa-comment"></i> Comentar</a>
    <button class="action-btn"><i class="fas fa-share"></i> Compartilhar</button>
</div>

<div class="post-comments-section">
    <?php if (!empty($post['ultimos_comentarios'])): ?>
        <div class="comment-preview-list">
            <?php foreach ($post['ultimos_comentarios'] as $comment_preview): ?>
                <div class="comment-preview-item">
                    <div class="comment-preview-avatar">
                        <?php if (!empty($comment_preview['foto_perfil_url'])): ?>
                            <img src="<?php echo htmlspecialchars($comment_preview['foto_perfil_url']); ?>" alt="Foto de <?php echo htmlspecialchars($comment_preview['autor_nome']); ?>">
                        <?php else: ?>
                            <i class="fas fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <div class="comment-preview-main">
                        <div class="comment-preview-content">
                            <span class="comment-author"><?php echo htmlspecialchars($comment_preview['autor_nome']); ?>:</span>
                            <span class="comment-text"><?php echo htmlspecialchars($comment_preview['conteudo_texto']); ?></span>
                        </div>
                        <div class="comment-preview-actions">
                            <a href="#" class="comment-like-btn <?php echo ($comment_preview['usuario_curtiu_comentario'] > 0) ? 'active' : ''; ?>" data-comment-id="<?php echo $comment_preview['id']; ?>">Curtir</a>
                            <a href="postagem.php?id=<?php echo $post['id']; ?>#comment-wrapper-<?php echo $comment_preview['id']; ?>">Responder</a>
                            <span class="comment-like-count" <?php if($comment_preview['total_curtidas_comentario'] == 0) echo 'style="display:none;"'; ?>>
                                <i class="fas fa-thumbs-up"></i> <?php echo $comment_preview['total_curtidas_comentario']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($post['total_comentarios'] > count($post['ultimos_comentarios'])): ?>
            <a href="postagem.php?id=<?php echo $post['id']; ?>" class="view-all-comments">
                Ver todos os <?php echo $post['total_comentarios']; ?> comentários
            </a>
        <?php endif; ?>
    <?php endif; ?>

    <div class="add-comment-form-container">
        <form action="api/postagens/criar_comentario.php" method="POST">
            <input type="hidden" name="id_postagem" value="<?php echo $post['id']; ?>">
            <input type="text" name="conteudo_texto" class="comment-input" id="main-comment-input" placeholder="Escreva um comentário..." required>
            <button type="submit" class="comment-submit-btn"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>