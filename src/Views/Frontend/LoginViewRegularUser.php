<div class="row justify-content-center mt-5">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow border-0">
      <div class="card-body p-5">
        <h2 class="text-center mb-4 fw-bold"><?php echo($headerText); ?></h2>

        <?php if (!empty($errorMessage)): ?>
          <div class="alert alert-danger" role="alert">
            <?php echo($errorMessage); ?>
          </div>
        <?php endif; ?>

        <form action="" method="post">
          <input type="hidden" name="csrfToken" id="csrfToken" value="<?php echo($csrfToken); ?>">

          <div class="mb-3">
            <label for="usernameRegularUser" class="form-label fw-semibold"><?php echo($userLabel); ?></label>
            <input type="text" name="username" id="usernameRegularUser" class="form-control" required>
          </div>

          <div class="mb-4">
            <label for="passwordRegularUser" class="form-label fw-semibold"><?php echo($passwordLabel); ?></label>
            <input type="password" name="password" id="passwordRegularUser" class="form-control" required autocomplete="off">
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg"><?php echo($loginButtonText); ?></button>
          </div>
        </form>

        <div class="text-center mt-4">
          <a href="/signup/" class="text-decoration-none link-secondary">
            <small><?php echo($signUpLoginText); ?></small>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>