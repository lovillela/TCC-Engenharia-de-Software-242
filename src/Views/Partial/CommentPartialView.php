<div class="mt-5 pt-4 border-top">
  <h4 class="mb-4"><?php echo($commentsBlockHeaderText); ?></h4>

  <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
    <form action="/post/comment/create/" method="POST" class="mb-4">
      <input type="hidden" name="csrfToken" value="<?php echo($csrfToken); ?>">
      <input type="hidden" name="postId" value="<?php echo($postId); ?>">
      <input type="hidden" name="parentId" value="">
      
      <textarea class="form-control mb-2" name="commentContent" rows="2" required></textarea>
      <button type="submit" class="btn btn-primary btn-sm"><?php echo($commentActionButtonText); ?></button>
    </form>
  <?php else: ?>
    <div class="alert alert-light border text-center text-muted mb-4">
      <?php echo($loginButtonText); ?>
    </div>
  <?php endif; ?>

  <div class="comment-list">
    <?php if (empty($comments)): ?>
      <p class="text-muted"><?php echo($noCommentsText); ?></p>
    <?php else: ?>
      <?php echo($comments); ?> 
    <?php endif; ?>
  </div>
</div>