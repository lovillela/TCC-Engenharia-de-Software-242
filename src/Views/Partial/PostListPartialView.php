<div id="postListContainer">
  <input type="hidden" name="csrfToken" value="<?php echo($csrfToken); ?>">
  <?php if (empty($userPosts)): ?>

    <p>Usuário não tem posts</p>
  
  <?php else: ?>
    <?php foreach ($userPosts as $post): ?>
      <?php echo($post['title'] . ' ');?> <a href="/dashboard/post/edit/<?php echo($post['id']);?>">Editar</a>
      <form action="/dashboard/post/delete/<?php echo($post['id']); ?>" method="POST">
        <input type="hidden" name="csrfToken" value="<?php echo($csrfToken); ?>">
        <button type="submit">
          Deletar
        </button>
      </form>
      <br>
    <?php endforeach; ?>

  <?php endif; ?>
</div>