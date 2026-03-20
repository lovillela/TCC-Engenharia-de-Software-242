<div name="header">
  <a href="/dashboard/">Dashboard</a>
</div>

<?php 
  if (!empty($errorMessage)) {
    echo($errorMessage);
  }

  if (!empty($generalMessage)) {
    echo($generalMessage);
  }
?>

<h1><?php echo($headerText) ?></h1>

<form action="" method="post">

  <input type="hidden" name="csrfToken" id="csrfToken" value="<?php echo($csrfToken); ?>">

  Title: <input type="text" name="postTitle" id="postTitle" required> <br>
  
  Text: <br>
  <!--<textarea name="blogPost" id="blogPost" cols="30" rows="10"></textarea>-->

  <?php require_once $textEditor ?>
  
  <button type="submit">Create</button>

</form>