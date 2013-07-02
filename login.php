<?php
	// Start the session
	require_once('startsession.php');
	
	require_once('appvars.php');
	require_once('connectvars.php');

	// Clear the error message
	$error_msg = "";

  // If the user isn't logged in, try to log them in
  if (!isset($_SESSION['user_id'])) {
    if (isset($_POST['submit'])) {
      // Connect to the database
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

      // Grab the user-entered log-in data
      $username = mysqli_real_escape_string($dbc, trim(strtolower($_POST['username'])));
      $password = mysqli_real_escape_string($dbc, trim($_POST['password']));
      if (!empty($username) && !empty($password)) {
        // Look up the username and password in the database
		$pass = crypt($username . $password, SALT);
        $query = "SELECT user_id, user_name FROM users WHERE user_name = '$username' AND password = '$pass'";
		
        $data = mysqli_query($dbc, $query);

        if (mysqli_num_rows($data) == 1) {
          // The log-in is OK so set the user ID and username session vars (and cookies), and redirect to the home page
          $row = mysqli_fetch_array($data);
          $_SESSION['user_id'] = $row['user_id'];
          $_SESSION['username'] = $row['user_name'];
          //setcookie('user_id', $row['user_id'], time() + (60 * 60 * 24 * 30));    // expires in 30 days
          //setcookie('username', $row['user_name'], time() + (60 * 60 * 24 * 30));  // expires in 30 days
          $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
          header('Location: ' . $home_url);
        }
        else {
          // The username/password are incorrect so set an error message
          $error_msg = 'Sorry, you must enter a valid username and password to log in.';
        }
      }
      else {
        // The username/password weren't entered so set an error message
        $error_msg = 'Sorry, you must enter your username and password to log in.';
      }
    }
  }
  
  // Insert the page header
  $page_title = 'log in';
  require_once('header.php');
  
  if ($_SESSION['login'] == true){
	echo '<p><em>Please log in first.</em></p>';
  }
  
  //Show the navigation menu
  require_once('navmenu.php');
?>

<?php
  // If the session var is empty, show any error message and the log-in form; otherwise confirm the log-in
  if (empty($_SESSION['user_id'])) {
    echo '<p class="error">' . $error_msg . '</p>';
?>

  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <fieldset>
      <legend>Log In</legend>
      <label for="username">Username:</label>
      <input type="text" name="username" value="<?php if (!empty($username)) echo $username; ?>" /><br />
      <label for="password">Password:</label>
      <input type="password" name="password" />
    </fieldset>
    <input type="submit" value="Log In" name="submit" />
  </form>
	<a href="forgot.php">(Forgot your password?)</a>

<?php
  }
  else {
    // Confirm the successful log-in
    echo('<p class="login">You are logged in as ' . $_SESSION['username'] . '.</p>');
  }
?>

<?php
  // Inser the page footer
  require_once('footer.php');
?>
