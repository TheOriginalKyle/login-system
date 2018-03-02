
<?php require_once("../Private/initialize.php"); ?>




<!DOCTYPE html>
<html>
<head>
  <title>Sign-Up/Login Form</title>
  <?php include 'css/css.html'; ?>
</head>

<?php
if(request_is_post() && request_is_same_domain()) {

  if(!csrf_token_is_valid() || !csrf_token_is_recent()) {
  	$message = "Sorry, request was not valid.";
  } else {
    // CSRF tests passed--form was created by us recently.

		// retrieve the values submitted via the form

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['login'])) { //user logging in

        require 'login.php';

    }

    elseif (isset($_POST['register'])) { //user registering

        require 'register.php';

    }
}
}
}
?>
?>

<body>
  <div class="form">




<div id="signup">
  <h1>Sign Up </h1>
  <p class="forgot"><a href="index.php">Go to Login</a></p>

  <form action="index.php" method="post" autocomplete="off">
    <?php echo csrf_token_tag(); ?>

  <div class="top-row">
    <div class="field-wrap">
      <label>
        First Name<span class="req">*</span>
      </label>
      <input type="text" required autocomplete="off" name='firstname' />
    </div>

    <div class="field-wrap">
      <label>
        Last Name<span class="req">*</span>
      </label>
      <input type="text"required autocomplete="off" name='lastname' />
    </div>
  </div>

  <div class="field-wrap">
    <label>
      Email Address<span class="req">*</span>
    </label>
    <input type="email"required autocomplete="off" name='email' />
  </div>

  <div class="field-wrap">
    <label>
      Set A Password<span class="req">*</span>
    </label>
    <input type="password"required autocomplete="off" name='password'/>
  </div>

  <button type="submit" class="button button-block" name="register" />Register</button>

  </form>

</div>

</div><!-- tab-content -->

</div> <!-- /form -->


</body>
</html>
