<div name="header">
  <a href="/">Home</a>
</div>

<?php 
  if (!empty($errorMessage)) {
    echo($errorMessage);
  }

  if (!empty($generalMessage)) {
    echo($generalMessage);
  }
?>

<h1><?php echo($title);?></h1>

<div name="post">
  <?php 
    if (isset($content)) {
        echo('<br>');
        echo($content);
        echo('<br> <br>');
    }
  ?>
</div>