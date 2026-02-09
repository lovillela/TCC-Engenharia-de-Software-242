<div>

  <?php 
    if (!empty($errorMessage)) {
      echo($errorMessage);
    }

    if (!empty($generalMessage)) {
      echo($generalMessage);
    }
  ?>

  <p>Add a new User</p>

  <form action="/admin/create/user/" method="post">

    Username: <input type="text" name="newUser" id="newUser_Admin" required> <br>
    Password: <input type="password" name="newUserPassword" id="newUserPassword_Admin" required> <br>
    email: <input type="email" name="newUserEmail" id="newUserEmail_Admin" required> <br>
    
    Role: <br>
          <input type="radio" name="userRole" id="role-admin" value="1" required> Admin <br>
          <input type="radio" name="userRole" id="role-moderator" value="2"> Moderator <br>
          <input type="radio" name="userRole" id="role-user" value="3">  User <br>
          
    <button type="submit">Create</button>

  </form>

  <a href="/admin/dashboard/">Return to Dashboard</a>

</div>