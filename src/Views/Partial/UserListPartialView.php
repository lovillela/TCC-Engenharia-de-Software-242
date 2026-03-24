<div id="userListContainer">
  <input type="hidden" name="csrfToken" value="<?php echo($csrfToken); ?>">
  <?php if (empty($users)): ?>

    <p>Não há usuários</p>
  
  <?php else: ?>
    <?php foreach ($users as $user): ?>
      <?php echo($user['username'] . ' ' . $user['email']);?>
      <form action="<?php echo($deleteUrlAction) ?>delete/<?php echo($user['id']); ?>" method="POST">
        <input type="hidden" name="csrfToken" value="<?php echo($csrfToken); ?>">
        <button type="submit">
          Deletar
        </button>
      </form>
      <br>
    <?php endforeach; ?>

  <?php endif; ?>
</div>