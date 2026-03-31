<div class="container mt-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="display-6"><?php echo($headerText) ?></h1>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="/dashboard/post/add/" class="btn btn-primary shadow-sm">
                + Adicionar Novo Post
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php require_once $postList; ?>
        </div>
    </div>
</div>