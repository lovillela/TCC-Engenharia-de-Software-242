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

<h1><?php echo($headerText);?></h1>

<div name="posts">
  <?php 
    if (isset($posts)) {

      foreach ($posts as $post) {
        echo('<h2>'. $post['title'] . '</h2>'); 
        echo('<br>');
        echo($post['content']);
        echo('<br> <br>');
      }
    }
  ?>
</div>