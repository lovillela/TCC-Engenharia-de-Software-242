<div name="header">
  <a href="/">Home</a>
</div>

<?php 
  if (!empty($messages['errorMessage'])) {
    echo($messages['errorMessage']);
  }

  if (!empty($messages['generalMessage'])) {
    echo($messages['generalMessage']);
  }
?>

<h1><?php echo($title);?></h1>

<div name="post">
  <?php 
    if (isset($data)) {
        echo('<br>');
        echo($content);
        echo('<br> <br>');
    }
  ?>
</div>