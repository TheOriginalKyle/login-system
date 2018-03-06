
<?php require_once("../Private/initialize.php");


?>


<?php
/* User login process, checks if user exists and password is correct */
$db = DBAccess::getMysqliConnection();

// Escape email to protect against SQL injections
$dirtyEmail = trim($_POST['email']);
$dirtyPassword = trim($_POST['password']);

if(has_presence($dirtyEmail) && has_presence($dirtyPassword))
{
  $throttle_delay = throttle_failed_logins(mysqli_real_escape_string($db, $dirtyEmail));

  if($throttle_delay > 0)
  {
    $message = " Too many failed attempts please wait " + $throttle_delay + " minutes and try again.";
    $_SESSION['message'] = $message;
    header("location: error.php");
  }
  else
  {
    if(has_length($dirtyEmail, ['min' => 3, 'max' => 50]) && has_length($dirtyPassword, ['min' => 8]))
    {
      $noHTMLemail = h($dirtyEmail);
      if(filter_var($noHTMLEmail, FILTER_VALIDATE_EMAIL) && has_format_matching($dirtyPassword, '/[^A-Za-z0-9]/'))
      {
        $cleanEmail = mysqli_real_escape_string($db, $noHTMLEmail);
        #valid as in the format is correct and has not been escaped.
        $validPassword = $diryPassword;

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

          if ( password_verify(mysqli_real_escape_string($db, $validPassword), $user['password']) )
          {
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['active'] = $user['active'];

            clear_failed_logins($cleanEmail);

            // This is how we'll know the user is logged in
            $_SESSION['logged_in'] = true;

            header("location: profile.php");
          }
          else
          {
            record_failed_login($cleanEmail);
            $_SESSION['message'] = "You have entered in the wrong combination, try again!";
            header("location: error.php");
          }
        }
      }
      else
      {
        record_failed_login(mysqli_real_escape_string($db, $noHTMLemail));
        $_SESSION['message'] = "You have entered in the wrong combination, try again!";
        header("location:error.php");
      }
    }
    else
    {
      record_failed_login(mysqli_real_escape_string($db, $dirtyEmail));
      $_SESSION['message'] = "You have entered in the wrong combination, try again!";
      header("location:error.php");
    }
  }
}
else
{
  record_failed_login(mysqli_real_escape_string($db, $dirtyEmail));
  $_SESSION['message'] = "You have entered in the wrong combination, try again!";
  header("location: error.php");
}
