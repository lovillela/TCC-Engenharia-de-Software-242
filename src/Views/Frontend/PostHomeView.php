<?php 
  if (!empty($errorMessage)) {
    echo($errorMessage);
  }

  if (!empty($generalMessage)) {
    echo($generalMessage);
  }
?>

<h1><?php echo($headerText);?></h1>

<?php if(!isset($posts)): ?>
  <div class="alert alert-info">Nenhum post publicado ainda. Volte em breve!</div>

<?php else: ?>

  <div class="row">
    <?php foreach ($posts as $post):?>
      <a href="/post/<?php echo($post['slug']); ?>/" class="text-decoration-none text-dark">
        <?php echo($post['title']); ?>
      </a>
    <?php endforeach;?>
  </div>




<?php endif; ?>