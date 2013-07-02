<?php
	// Start the session
	require_once('startsession.php');
	
	require_once('appvars.php');
	require_once('connectvars.php');

	// Clear the error message
	$error_msg = "";
	
	// Insert the page header
	$page_title = 'password reset';
	require_once('header.php');

	//Show the navigation menu
	require_once('navmenu.php');

  // If the user isn't logged in, try to log them in
  if (!isset($_SESSION['user_id'])) {
    if (isset($_POST['submit'])) {
      // Connect to the database
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

      // Grab the user-entered log-in data
	  $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
      $username = mysqli_real_escape_string($dbc, trim(strtolower($_POST['username'])));
      if (!empty($username) && !empty($email)) {
        // Look up the username and password in the database
		$newPass = md5(uniqid(rand(),1));
		$pass = crypt($username . $newPass, SALT);
        $query = "UPDATE users SET password = '$pass' WHERE user_name = '$username' AND email = '$email'";
        $data = mysqli_query($dbc, $query);
		
		$body = "Your password to log into the character tracker has been temporarily changed to 
		". $newPass ."
		Please log in using this password and your username. At that time you may change your password 
		to something more familiar.";
		mail($_POST['email'], 'Your temporary password.', $body, 'From:tabletopdb@adamhollock.com');
		
		echo '<h3>Your password has been changed. You will receive the new, temporary password at the e-mail address
				at which you registered.</h3>';

      }
      else {
        // The username/email weren't entered so set an error message
        $error_msg = 'Sorry, you must enter your username and email to reset your password.';
      }
    }
  }

?>

  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <fieldset>
      <legend>Password Reset</legend>
      <label for="email">Email:</label>
      <input type="text" name="email" value="<?php if (!empty($email)) echo $email; ?>" /><br />
	  <label for="username">Username:</label>
      <input type="text" name="username" value="<?php if (!empty($username)) echo $username; ?>" /><br />
    </fieldset>
    <input type="submit" value="Reset" name="submit" />
  </form>

<?php
  // Inser the page footer
  require_once('footer.php');
?>
