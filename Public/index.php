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
<body>



  <div class="form">



         <div id="login">
          <h1>Login</h1>
          <p class="forgot"><a href="signup.php">Register</a></p>

          <form action="index.php" method="post" autocomplete="off">
             <?php echo csrf_token_tag(); ?>

            <div class="field-wrap">
            <label>
              Email Address<span class="req">*</span>
            </label>
            <input type="email" required autocomplete="off" name="email"   />
          </div>

          <div class="field-wrap">
            <label>
              Password<span class="req">*</span>
            </label>
            <input type="password" required autocomplete="off" name="password"/>
          </div>


          <button class="button button-block" name="login" />Log In</button>

          </form>

        </div>



      </div><!-- tab-content -->

</div> <!-- /form -->

</body>
</html>
