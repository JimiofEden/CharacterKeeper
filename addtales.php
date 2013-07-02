<?php
	// Start the session
	require_once('startsession.php');
	
	// Redirect in case they're not logged in
	require_once('redirect.php');
	
	// Insert the page header
	$page_title = 'add a story';
	require_once('header.php');
	require_once('navmenu.php');
	
	require_once('appvars.php');
	require_once('connectvars.php');
	
	// Connect to the database
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$query = "SELECT game_name, game_id FROM games";
	$games = mysqli_query($dbc, $query);

  // activates when the submit button is clicked
  if (isset($_POST['submit'])) {
	// Grab the character data from the POST
	$user = $_SESSION['user_id'];
	$name = mysqli_real_escape_string($dbc, trim($_POST['name']));
	$game = mysqli_real_escape_string($dbc, trim($_POST['game']));
	$story = mysqli_real_escape_string($dbc, trim($_POST['story']));
	$gameIdQuery = "SELECT game_id FROM games WHERE game_name = '$game'";
	$gameResult = mysqli_query($dbc, $gameIdQuery);
	$gameArray = mysqli_fetch_array($gameResult);
	$gameNumber = $gameArray['game_id'];
	
	$checkStoryName = "SELECT storyName FROM stories WHERE storyName = '$name'";
	$nameCheck = mysqli_query($dbc, $checkStoryName);
	
	if (mysqli_num_rows($nameCheck) == 0) {

		if (!empty($name) && !empty($game) && !empty($story)) {
			// Write the data into the database
			$query = "INSERT INTO stories VALUES (0, NOW(), '$user', '$gameNumber', '$name', '$story', 0)";
			// Makes the query to the database, and returns an error message if there's a failure.
			mysqli_query($dbc, $query)
			or die('Error querying database');
			
			// Confirm successful addition to user
			echo 'Excellent! Your story has been added!<br/>';
			
			//Clears the character's data to clear the form.
			$name = "";
			$game = "";
			$story = "";
		
			// Closes the database connection
			mysqli_close($dbc);
		} else {
		echo '<p class="error">Please enter all of the information to add your story</p>';
		}
	}
	else {
		echo '<p class="error">This game name already exists. Please choose another.</p>';
	}
  }
?>
	
	
	<hr />
	<form enctype="multipart/form-data" method="post" action"<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MM_MAXFILESIZE; ?>" />
	<fieldset>
		<legend>Story Upload</legend>
		
		<label for="name">Story Name: </label>
		<input type="text" id="name" name="name" 
		value="<?php if (!empty($name)) echo $name; ?>" /><br />
		
		<label for="game">Associated Game: </label>
		<select name="game">
			<option value="-1">Please select...</option>
		<?
			while($gameName = mysqli_fetch_array($games)){
				echo '<option>' . $gameName['game_name'] . '</option>';
			}
		?>
		</select><br />
		
		<label for="story">Story: </label>
		<textarea class="bigText" id="story" name="story" 
		value="<?php if (!empty($story)) echo $story; ?>" /></textarea><br />
		
	</fieldset>
	<input type="submit" value="Add" name="submit" />
	</form>

<?php
	// Insert the page footer
	require_once('footer.php');
?>