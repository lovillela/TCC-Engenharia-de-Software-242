<h2><?php echo ($loginHeaderText);?></h2>
<div name="loginForm">
  <form action="" method="post">
  
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