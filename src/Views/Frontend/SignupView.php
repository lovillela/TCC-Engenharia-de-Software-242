<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo($title); ?></title>
</head>
<body>
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
</body>
</html>