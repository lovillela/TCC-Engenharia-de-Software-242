<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo($title); ?></title>
</head>
<body>
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

    <h1><?php echo($headerText);?></h1>

    <div name="posts">
      <?php 
        if (isset($data)) {

          foreach ($data as $post) {
            echo('<h2>'. $post['title'] . '</h2>'); 
            echo('<br>');
            echo($post['content']);
            echo('<br> <br>');
          }
        }
      ?>
    </div>


</body>
</html>