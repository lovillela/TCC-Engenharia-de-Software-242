<div class="table-responsive shadow-sm rounded">
  <table class="table table-hover table-striped align-middle mb-0">
    <thead class="table-dark">
      <tr>
        <th><?php echo($tableHeaderIdText); ?></th>
        <th><?php echo($tableHeaderUsernameText); ?></th>
        <th><?php echo($tableHeaderEmailText); ?></th>
        <th><?php echo($tableHeaderPermissionText); ?></th>
        <th class="text-end"><?php echo($tableHeaderActionText); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($users)): ?>
        <tr>
          <td colspan="2" class="text-center text-muted"><?php echo($noUsersNoticeText); ?></td>
        </tr>
      <?php else: ?>
        <?php foreach ($users as $user): ?>
          <tr>
            <td class="fw-medium"><?php echo($user['id']); ?></td>
            <td><strong><?php echo(htmlspecialchars($user['username'])); ?></strong></td>
            <td><?php echo(htmlspecialchars($user['email'])); ?></td>
            <td><?php echo(htmlspecialchars($user['permissions'])); ?></td>
            <td class="text-end">
              <form action="<?php echo($deleteUrlAction); ?>delete/<?php echo($user['id']); ?>" method="post" class="d-inline" onsubmit="return confirm('ATENÇÃO: Deseja mesmo deletar o usuário <?php echo($user['username']); ?>? TODOS os posts dele serão destruídos no banco de dados!')">
                <input type="hidden" name="csrfToken" value="<?php echo($csrfToken); ?>">
                <button type="submit" class="btn btn-sm btn-danger"> <?php echo($deleteButtonText); ?></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>