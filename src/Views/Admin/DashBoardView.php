<div class="container mt-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="display-6"><?php echo($headerText); ?></h1>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?php echo($adminCreateUserFormUrl) ?>" class="btn btn-primary shadow-sm">
                <?php echo($addNewUserText) ?>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column text-center p-4">
                    <div class="mb-3">
                        <span style="font-size: 3rem;">👥</span>
                    </div>
                    <h4 class="card-title fw-bold"><?php echo($userModerationText) ?></h4>
                    <p class="card-text text-muted mb-4">
                        <?php echo($adminUserActionsText) ?>
                    </p>
                    <a href="<?php echo($adminListUsersUrl) ?>" class="btn btn-outline-secondary mt-auto w-100">
                        <?php echo($listUsersText) ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column text-center p-4">
                    <div class="mb-3">
                        <span style="font-size: 3rem;">📝</span>
                    </div>
                    <h4 class="card-title fw-bold"><?php echo($postModerationText) ?></h4>
                    <p class="card-text text-muted mb-4">
                        <?php echo($adminPostActionsText) ?>
                    </p>
                    <a href="<?php echo($adminListPostsUrl) ?>" class="btn btn-outline-secondary mt-auto w-100">
                        <?php echo($listPostText) ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>