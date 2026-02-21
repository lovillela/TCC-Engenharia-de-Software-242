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

<form action="/post/edit/" method="post">

  <input type="hidden" name="csrfToken" id="csrfToken" value="<?php echo($csrfToken); ?>">

  Title: <input type="text" name="postTitle" id="postTitle" value="<?php echo($postTitle);?>" required> <br>

  Slug:
  <input type="text" name="slugUrl" id="slugUrl" value="<?php echo($slugUrl);?>" required> <br>
  
  Text: <br>
  <textarea name="blogPost" id="blogPost" cols="30" rows="10" value="<?php echo($blogPost);?>"></textarea>
  
  <button type="submit">create</button>

</form>