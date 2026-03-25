<div class="table-responsive shadow-sm rounded">
  <table class="table table-hover table-striped align-middle mb-0">
    <thead class="table-dark">
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
                <a href="/dashboard/post/edit/<?php echo($post['id_post']); ?>" class="btn btn-sm btn-primary">
                  <?php echo($editButtonText); ?>
                </a>
              <?php endif; ?>

              <form action="<?php echo($deleteActionUrl) ?>delete/<?php echo($post['id_post']); ?>" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja deletar este post?')">
                <input type="hidden" name="csrfToken" value="<?php echo($csrfToken); ?>">
                <button type="submit" class="btn btn-sm btn-danger"><?php echo($deleteButtonText) ?></button>
              </form>

            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>