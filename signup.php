<?php	
   // Start the session
  require_once('startsession.php');
  
  // Insert the page header
  $page_title = 'create an account';
  require_once('header.php');
  
  require_once('appvars.php');
  require_once('connectvars.php');
  
  //Show the navigation menu
  require_once('navmenu.php');

  // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  if (isset($_POST['submit'])) {
    // Grab the profile data from the POST
    $username = mysqli_real_escape_string($dbc, trim(strtolower($_POST['username'])));
	$email = mysqli_real_escape_string($dbc, trim($_POST['email']));
    $password1 = mysqli_real_escape_string($dbc, trim($_POST['password1']));
    $password2 = mysqli_real_escape_string($dbc, trim($_POST['password2']));

    if (!empty($username) && !empty($password1) && !empty($password2) && !empty($email) && ($password1 == $password2) && (strlen($password1) >= 8 )) {
      // Make sure someone isn't already registered using this username
      $query = "SELECT * FROM users WHERE user_name = '$username'";
      $data = mysqli_query($dbc, $query);
      if (mysqli_num_rows($data) == 0) {
        // The username is unique, so insert the data into the database
		$pass = crypt($username . $password1, SALT);
        $query = "INSERT INTO users (user_id, date, user_name, password, email) VALUES (0, NOW(), '$username', '$pass', '$email')";
        mysqli_query($dbc, $query);
		

		
		echo $pass;
        // Confirm success with the user
        echo '<p>Your new account has been successfully created. You\'re now ready to <a href="login.php">log in</a>.</p>';
		
		// Send an e-mail to confirm success
		$from = 'tabletopdb@adamhollock.com';
		$subject = 'Tabletop Database Signup Confirmation [DO NOT REPLY]';
		$text = 'Thank you for signing up for the Tabletop Database, ' . $username . 
			'. We look forward to seeing your characters and their tales.';
		mail($email, $subject, $text, 'From:' . $from);
		echo 'Email sent to: ' . $email . '<br />';
		
        mysqli_close($dbc);
        exit();
      }
      else {
        // An account already exists for this username, so display an error message
        echo '<p class="error">An account already exists for this username. Please use a different address.</p>';
        $username = "";
      }
    }
    else {
      echo '<p class="error">You must enter all of the sign-up data, including the desired password twice. Your password must also be 8 characters in length.</p>';
    }
  }

  mysqli_close($dbc);
?>

  <p>Please enter your username and desired password to sign up to the character tracker</p>
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <fieldset>
      <legend>Registration Info</legend>
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" value="<?php if (!empty($username)) echo $username; ?>" /><br />
	  <label for="email">E-mail Address:</label>
	  <input type="text" id="email" name="email" value="<?php if (!empty($email)) echo $email; ?>" /><br />
      <label for="password1">Password (8 characters):</label>
      <input type="password" id="password1" name="password1" /><br />
      <label for="password2">Password (retype):</label>
      <input type="password" id="password2" name="password2" /><br />
    </fieldset>
    <input type="submit" value="Sign Up" name="submit" />
  </form>

<?php
  // Inser the page footer
  require_once('footer.php');
?>