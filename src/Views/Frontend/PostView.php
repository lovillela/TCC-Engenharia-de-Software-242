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
    <h1><?php echo($title);?></h1>
    <?php 
      if (isset($content)) {
          echo('<br>');
          echo($content);
          echo('<br>');
      }
    ?>
  </div>
</div>