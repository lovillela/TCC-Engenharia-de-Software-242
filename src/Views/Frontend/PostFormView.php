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

<form action="/post/add/" method="post">

  Title: <input type="text" name="postTitle" id="postTitle" required> <br>
  
  Text: <br>
  <textarea name="blogPost" id="blogPost" cols="30" rows="10"></textarea>
  
  <button type="submit">create</button>

</form>