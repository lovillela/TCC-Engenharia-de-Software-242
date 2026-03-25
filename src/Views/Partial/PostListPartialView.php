<div class="table-responsive">
  <table class="table table-hover align-middle">
    <thead class="table-light">
      <tr>
        <th> <?php echo($tableHeaderPostTitleText); ?> </th>
        <th class="text-end"><?php echo($tableHeaderActionText); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($userPosts)): ?>
        <tr>
          <td colspan="2" class="text-center text-muted"><?php echo($noPostsNoticeText); ?> </td>
        </tr>
      <?php else: ?>
        <?php foreach ($userPosts as $post): ?>
          <tr>
            <td>
              <span class="fw-medium"><?php echo($post['title']); ?></span>
            </td>
            <td class="text-end">
              
              <?php if (!isset($hideEditButton) || $hideEditButton === false): ?>
                <a href="/dashboard/post/edit/<?php echo($post['id_post']); ?>" class="btn btn-sm btn-outline-primary">
                  <?php echo($editButtonText); ?>
                </a>
              <?php endif; ?>

              <form action="<?php echo($deleteActionUrl) ?>delete/<?php echo($post['id']); ?>" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar este post?')">
                <input type="hidden" name="csrfToken" value="<?php echo($csrfToken); ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger"><?php echo($deleteButtonText) ?></button>
              </form>

            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>