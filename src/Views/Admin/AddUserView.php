<div class="row justify-content-center mt-4">
  <div class="col-md-8 col-lg-6">

    <a href="/admin/dashboard/" class="btn btn-sm btn-secondary mb-3">« <?php echo($returnToDashboardLinkText); ?></a>

    <div class="card shadow-sm border-0 border-top border-primary border-4">
      <div class="card-body p-4 p-md-5">

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
          <input type="hidden" name="csrfToken" id="csrfToken" value="<?php echo($csrfToken); ?>">

          <div class="mb-3">
            <label for="newUser_Admin" class="form-label fw-semibold"><?php echo($userLabel); ?></label>
            <input type="text" name="newUser" id="newUser_Admin" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="newUserEmail_Admin" class="form-label fw-semibold"><?php echo($emailLabel); ?></label>
            <input type="email" name="newUserEmail" id="newUserEmail_Admin" class="form-control" required>
          </div>

          <div class="mb-4">
            <label for="newUserPassword_Admin" class="form-label fw-semibold"><?php echo($passwordLabel); ?></label>
            <input type="password" name="newUserPassword" id="newUserPassword_Admin" class="form-control" required>
          </div>

          <div class="mb-4 p-3 bg-light rounded border">
            <label class="form-label fw-semibold d-block mb-3"><?php echo($roleLevelLabel); ?></label>
            
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="userRole" id="role-admin" value="1" required>
              <label class="form-check-label text-danger fw-bold" for="role-admin"><?php echo($adminLabel); ?></label>
            </div>
            
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="userRole" id="role-moderator" value="2">
              <label class="form-check-label text-warning fw-bold" for="role-moderator"><?php echo($moderatorLabel); ?></label>
            </div>
            
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="userRole" id="role-user" value="3">
              <label class="form-check-label text-primary fw-bold" for="role-user"><?php echo($regularUserLabel); ?></label>
            </div>
          </div>

          <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg"><?php echo($createAccountButtonText); ?></button>
          </div>

        </form>

      </div>
    </div>
  </div>
</div>