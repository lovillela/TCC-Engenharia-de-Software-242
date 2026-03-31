<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo($headerText) ?></h1>
        <a href="/dashboard/" class="btn btn-outline-secondary">« Voltar ao Dashboard</a>
    </div>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger"><?php echo($errorMessage); ?></div>
    <?php endif; ?>

    <?php if (!empty($generalMessage)): ?>
        <div class="alert alert-info"><?php echo($generalMessage); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="" method="post" id="postForm">
                <input type="hidden" name="csrfToken" id="csrfToken" value="<?php echo($csrfToken); ?>">

                <div class="mb-3">
                    <label for="postTitle" class="form-label fw-bold">Título do Post</label>
                    <input type="text" class="form-control" name="postTitle" id="postTitle" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Conteúdo</label>
                    <?php require_once $textEditor ?>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">Criar Post</button>
                </div>
            </form>
        </div>
    </div>
</div>