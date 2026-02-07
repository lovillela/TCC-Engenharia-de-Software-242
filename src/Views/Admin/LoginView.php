<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo ($title); ?></title>
</head>
<body>
  <h2><?php echo ($loginHeaderText);?></h2>
  <div name="loginForm">
    <form action="/admin/login/" method="post">
    
      <input type="hidden" name="csrfToken" id="csrfToken" value="<?php echo($csrfToken); ?>">
      
      <p>
        Username:
        <input type="text" name="username" id="usernameAdmin" required>
      </p>

      <p>
        Password:
        <input type="password" name="password" id="passwordAdmin" required autocomplete="off">
      </p>

      <button type="submit">Login</button>
    </form>
  </div>
  
  <div name="errors">
    <?php if (isset($errorMessage)): echo $errorMessage; endif; ?>
  </div>
</body>
</html>