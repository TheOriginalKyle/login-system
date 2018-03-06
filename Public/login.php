
<?php require_once("../Private/initialize.php");?>


<?php
/* User login process, checks if user exists and password is correct */
$db = DBAccess::getMysqliConnection();

// Escape email to protect against SQL injections
$dirtyEmail = trim($_POST['email']);
$dirtyPassword = trim($_POST['password']);

//First Check if they even gave us anything.
if(has_presence($dirtyEmail) && has_presence($dirtyPassword))
{
  $throttle_delay = throttle_failed_logins(mysqli_real_escape_string($db, $dirtyEmail));

  //If they had too many failed attempts refuse to proceed and let them know how long until they can retry.
  if($throttle_delay > 0)
  {
    $message = " Too many failed attempts please wait " . $throttle_delay . " minutes and try again.";
    $_SESSION['message'] = $message;
    header("location: error.php");
  }
  else
  {
    //Lets run through the same validation checks as we did on the register page.
    //Overkill is under-rated.
    if(has_length($dirtyEmail, ['min' => 3, 'max' => 50]) && has_length($dirtyPassword, ['min' => 8]))
    {
      //ALLWAYS sanitize your inputs we don't want people breaking the page on purpose.
      $noHTMLemail = h($dirtyEmail);
      if(filter_var($noHTMLEmail, FILTER_VALIDATE_EMAIL) && has_format_matching($dirtyPassword, '/[^A-Za-z0-9]/'))
      {
        $cleanEmail = mysqli_real_escape_string($db, $noHTMLEmail);
        //valid as in the format is correct and has not been escaped.
        $validPassword = $diryPassword;

        //Okay so we validated and sanitized the input lets see if their credentials check out.
        $result = $db->query("SELECT * FROM users WHERE email='$cleanEmail'");

        if ( $result->num_rows == 0 )
        { // User doesn't exist
          record_failed_login($cleanEmail);
          $_SESSION['message'] = "You have entered in the wrong combination, try again!";
          header("location: error.php");
        }
        else
        { // User exists
          $user = $result->fetch_assoc();

          //The escape string is only because we did that on the register page.
          if ( password_verify(mysqli_real_escape_string($db, $validPassword), $user['password']) )
          {
            //Lets put everything in the user session.
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['active'] = $user['active'];

            //Lets clear the failed attempts as it looks like somebody forgot their password.
            //or worse.
            clear_failed_logins($cleanEmail);

            // This is how we'll know the user is logged in
            $_SESSION['logged_in'] = true;

            header("location: profile.php");
          }
          else
          {
            //They passed all the checks just the password wasn't correct.
            record_failed_login($cleanEmail);
            $_SESSION['message'] = "You have entered in the wrong combination, try again!";
            header("location: error.php");
          }
        }
      }
      else
      {
        //They didn't give us valid input, I'd tell them but what if they're malicious?
        record_failed_login(mysqli_real_escape_string($db, $noHTMLemail));
        $_SESSION['message'] = "You have entered in the wrong combination, try again!";
        header("location:error.php");
      }
    }
    else
    {
      //They didn't give us any input thats remotely valid.
      //Ima go with malicious or they accidentally hit enter.
      record_failed_login(mysqli_real_escape_string($db, $dirtyEmail));
      $_SESSION['message'] = "You have entered in the wrong combination, try again!";
      header("location:error.php");
    }
  }
}
else
{
  //This is blank input. Like what am I supposed to do with that?!?
  record_failed_login(mysqli_real_escape_string($db, $dirtyEmail));
  $_SESSION['message'] = "You have entered in the wrong combination, try again!";
  header("location: error.php");
}
