<div class="row justify-content-center mt-5">
  <div class="col-md-6 col-lg-5">
    
    <a href="/" class="btn btn-sm btn-secondary mb-3">« <?php echo($returnHomeLinkText); ?></a>

    <div class="card shadow-sm border-0 border-top border-primary border-4">
      <div class="card-body p-5">
        
        <h2 class="text-center mb-4 fw-bold text-primary"><?php echo($headerText); ?></h2>

        <?php if (!empty($errorMessage)): ?>
          <div class="alert alert-danger" role="alert">
            <?php echo($errorMessage); ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($generalMessage)): ?>
          <div class="alert alert-success" role="alert">
            <?php echo($generalMessage); ?>
          </div>
        <?php endif; ?>

        <form action="" method="post">
          <div class="mb-3">
            <label for="newUser_Regular" class="form-label fw-semibold"><?php echo($userLabel); ?></label>
            <input type="text" name="newUser" id="newUser_Regular" class="form-control"required>
          </div>

          <div class="mb-3">
            <label for="newUserEmail_Regular" class="form-label fw-semibold"><?php echo($emailLabel); ?></label>
            <input type="email" name="newUserEmail" id="newUserEmail_Regular" class="form-control"required>
          </div>

          <div class="mb-4">
            <label for="newUserPassword_Regular" class="form-label fw-semibold"><?php echo($passwordLabel); ?></label>
            <input type="password" name="newUserPassword" id="newUserPassword_Regular" class="form-control" required>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg"><?php echo($signUpButtonText); ?></button>
          </div>
        </form>

        <div class="text-center mt-4">
          <a href="/login/" class="text-decoration-none link-secondary">
            <small><?php echo($alreadyRegisteredLoginText); ?></small>
          </a>
        </div>

      </div>
    </div>
  </div>
</div>