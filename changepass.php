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

    if (isset($_POST['submit'])) {
      // Connect to the database
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

      // Grab the user-entered log-in data
	  $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
      $username = mysqli_real_escape_string($dbc, trim(strtolower($_POST['username'])));
	  $oldPass = mysqli_real_escape_string($dbc, trim($_POST['oldPass']));
	  $newPass1 = mysqli_real_escape_string($dbc, trim($_POST['newPass1']));
	  $newPass2 = mysqli_real_escape_string($dbc, trim($_POST['newPass2']));
	  
      if (!empty($username) && !empty($email) && !empty($oldPass) && ($newPass1 == $newPass2) && (strlen($newPass1) >= 8)) {
        // Look up the username and password in the database
		$oldPassCrypt = crypt($username . $oldPass, SALT);
		$newPassCrypt = crypt($username . $newPass1, SALT);
        $query = "UPDATE users SET password = '$newPassCrypt' WHERE user_name = '$username' AND email = '$email' AND password = '$oldPassCrypt'";
        $data = mysqli_query($dbc, $query);
		
		$body = "Your password has been successfully changed.";
		echo $body;
		
		mail($_POST['email'], 'Your password change.', $body, 'From:tabletopdb@adamhollock.com');

      }
      else {
        // The username/email weren't entered so set an error message
        echo 'Sorry, you must enter your username, email, and your old password to reset your password. You new password must match in both fields.';
		
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
	  <label for="oldPass">Old Password:</label>
      <input type="text" name="oldPass" value="" /><br />
	  <label for="newPass1">New Password (8 chars):</label>
      <input type="text" name="newPass1" value="" /><br />
	  <label for="newPass2">New Password (again):</label>
      <input type="text" name="newPass2" value="" /><br />
    </fieldset>
    <input type="submit" value="Change" name="submit" />
  </form>

<?php
  // Inser the page footer
  require_once('footer.php');
?>
