<div>

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

  <h1><?php echo($headerText) ?></h1>

  <form action="/signup/" method="post">

    Username: <input type="text" name="newUser" id="newUser_Regular" required> <br>
    Password: <input type="password" name="newUserPassword" id="newUserPassword_Regular" required> <br>
    Email: <input type="email" name="newUserEmail" id="newUserEmail_Regular" required> <br>
    
    <button type="submit">Signup</button>

  </form>

</div>