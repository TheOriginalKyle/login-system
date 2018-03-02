
<?php require_once("../private/initialize.php"); ?>




<?php


/* Displays user information and some useful messages */
confirm_user_logged_in();


    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
//?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Welcome <?= $first_name.' '.$last_name ?></title>
  <?php include 'css/css.html'; ?>
</head>

<body>
  <div class="form">

          <h1>Welcome</h1>

        




          <h2><?php echo $first_name.' '.$last_name; ?></h2>
          <p><?= $email ?></p>

          <a href="logout.php"><button class="button button-block" name="logout"/>Log Out</button></a>

    </div>


</body>
</html>
