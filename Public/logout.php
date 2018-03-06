


<?php require_once("../Private/initialize.php"); ?>


<?php
after_successful_logout();

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Error</title>
  <?php include 'css/css.html'; ?>
</head>

<body>
    <div class="form">
          <h1>Thanks for stopping by</h1>

          <p><?= 'You have been logged out!'; ?></p>

          <a href="index.php"><button class="button button-block"/>Home</button></a>

    </div>
</body>
</html>
