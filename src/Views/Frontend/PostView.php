<?php 
  if (!empty($errorMessage)) {
    echo($errorMessage);
  }

  if (!empty($generalMessage)) {
    echo($generalMessage);
  }
?>

<div class="row justify-content-center">
  <div class="col-lg-12">
    <a href="/post/" class="btn btn-sm btn-secondary mb-4">« Voltar para Artigos</a>
    <h1 class="display-3 text-center mb-5"><?php echo($title);?></h1>
    
    <div class="post-content">
      <?php 
        if (isset($content)) {
          echo('<br>');
          echo($content);
          echo('<br>');
        }
      ?>

      <?php require_once $commentView ?>
    </div>
  </div>
</div>