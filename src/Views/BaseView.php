<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/css/main.css" rel="stylesheet">
  
  <title><?php echo($headTitle) ?></title>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">TCC - Engenharia de Software 242</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/post/">Artigos</a></li>
                    <li class="nav-item"><a class="nav-link" href="/signup/">Cadastro</a></li>
                    <li class="nav-item"><a class="nav-link" href="/login/">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

  <div class="container content-wrapper mb-5">
    <?php echo($renderedContent) ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>