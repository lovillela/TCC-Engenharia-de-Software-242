<?php 
  if (!empty($errorMessage)) {
    echo($errorMessage);
  }

  if (!empty($generalMessage)) {
    echo($generalMessage);
  }
?>

<h1 class="mb-4 text-primary"><?php echo($headerText);?></h1>

<?php if(!isset($posts)): ?>
  <div class="alert alert-info">Nenhum post publicado ainda. Volte em breve!</div>

<?php else: ?>

  <div class="row">
    <?php foreach ($posts as $post):?>
      <div class="card h-100 border-light shadow-sm">
        <div class="card-body">
           <a href="/post/<?php echo($post['slug']); ?>/" class="text-decoration-none text-dark">
            <?php echo($post['title']); ?>
          </a>
        </div>
      </div>
    <?php endforeach;?>
  </div>




<?php endif; ?>