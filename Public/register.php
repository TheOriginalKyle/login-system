
<?php require_once("../Private/initialize.php"); ?>



<?php
/* Registration process, inserts user info into the database
   and sends account confirmation email message
 */
 $db = DBAccess::getMysqliConnection();

// Set session variables to be used on profile.php page

// Validate Input
$dirtyEmail = $_POST['email'];
$dirtyFirstName = $_POST['firstname'];
$dirtyLastName = $_POST['lastname'];
$dirtyPassword = $_POST['password'];

if(has_presence($dirtyEmail) && has_presence($dirtyFirstName) && has_presence($dirtyLastName) && has_presence($dirtyPassword))
{
  if(has_length(trim($dirtyEmail), ['min' => 3, 'max' => 50]))
  {
    $noHTMLemail = h(trim($dirtyEmail));

    if(filter_var($noHTMLemail, FILTER_VALIDATE_EMAIL))
    {
      $cleanEmail = mysqli_real_escape_string($db, $noHTMLemail);

      #Stores the now clean and valid email into user session.
      $_SESSION['email'] = $cleanEmail;

      if(has_length(trim($dirtyFirstName), ['min' => 2, 'max' => 50]))
      {
        $noHTMLfirstName = h(trim($dirtyFirstName));
        $cleanFirstName = mysqli_real_escape_string($db, $noHTMLfirstName);

        #Stores the now clean First Name into user session.
        $_SESSION['first_name'] = $cleanFirstName;

        if(has_length(trim($dirtyLastName), ['min' => 3, 'max' => 50]))
        {
          $noHTMLlastName = h(trim($dirtyLastName));
          $cleanLastName = mysqli_real_escape_string($db, $noHTMLlastName);

          #Stores the now clean last name into the user session.
          $_SESSION['last_name'] = $cleanLastName;

          if(has_length(trim($dirtyPassword), ['min' => 8]))
          {
            if(has_format_matching(trim($dirtyPassword), '/[^A-Za-z0-9]/'))
            {
              $validPassword = trim($dirtyPassword);
              $cleanPassword = mysqli_real_escape_string($db, password_hash($validPassword, PASSWORD_BCRYPT));
              $hash = mysqli_real_escape_string($db, md5( rand(0,1000) ) );

              #The password is now clean now we may continue.
              register_user($cleanFirstName, $cleanLastName, $cleanEmail, $cleanPassword, $hash);

            }else{
              $_SESSION['message'] = 'Your password is not strong enought.';
              header("location: error.php");
            }

          }else{
            $_SESSION['message'] = 'Your password is not strong enough.';
            header("location: error.php");
          }

        }else{
          $_SESSION['message'] = 'Your name is too short.';
          header("location: error.php");
        }

      }else{
        $_SESSION['message'] = 'Your name is too short.';
        header("location: error.php");
      }

    }else{
      $_SESSION['message'] = 'Thants not a real email.';
      header("location: error.php");

      var_dump($noHTMLemail);
    }

  }else{
    $_SESSION['message'] = 'Thats not a real email.';
    header("location: error.php");

    var_dump($dirtyEmail);
  }

}else
{
  $_SESSION['message'] = 'You forgot to enter something in!';
  header("location: error.php");
}

//$_SESSION['email'] = $_POST['email'];
//$_SESSION['first_name'] = $_POST['firstname'];
//$_SESSION['last_name'] = $_POST['lastname'];


// Escape all $_POST variables to protect against SQL injections
//$first_name = mysqli_real_escape_string($db,$_POST['firstname']);
//$last_name = mysqli_real_escape_string($db,$_POST['lastname']);
//$email = mysqli_real_escape_string($db,$_POST['email']);
//$password = mysqli_real_escape_string($db,password_hash($_POST['password'], PASSWORD_BCRYPT));
//$hash = mysqli_real_escape_string( $db,md5( rand(0,1000) ) );
// $first_name = $mysqli->escape_string($_POST['firstname']);
// $last_name = $mysqli->escape_string($_POST['lastname']);
// $email = $mysqli->escape_string($_POST['email']);
// $password = $mysqli->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT));
// $hash = $mysqli->escape_string( md5( rand(0,1000) ) );

function register_user($cleanFirstName,$cleanLastName,$cleanEmail,$cleanPassword,$hash){
$db = DBAccess::getMysqliConnection();

// Check if user with that email already exists
$result = $db->query("SELECT * FROM users WHERE email='$cleanEmail'") or die($mysqli->error());

// We know user email exists if the rows returned are more than 0
if ( $result->num_rows > 0 ) {

    $_SESSION['message'] = 'User with this email already exists!';
    header("location: error.php");

}
else { // Email doesn't already exist in a database, proceed...

    // active is 0 by DEFAULT (no need to include it here)
    $sql = "INSERT INTO users (first_name, last_name, email, password, hash) "
            . "VALUES ('$cleanFirstName','$cleanLastName','$cleanEmail','$cleanPassword', '$hash')";

    // Add user to the database
    if ( $db->query($sql) ){

        $_SESSION['active'] = 0; //0 until user activates their account with verify.php
        $_SESSION['logged_in'] = true; // So we know the user has logged in



        header("location: profile.php");

    }

    else {
        $_SESSION['message'] = 'Registration failed!';
        header("location: error.php");
    }

}
}
