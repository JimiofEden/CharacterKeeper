<?php
	// Start the session
	require_once('startsession.php');
	
	// Redirect in case they're not logged in
	require_once('redirect.php');
	
	// Insert the page header
	$page_title = 'add a character';
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
	$gameOldName = mysqli_real_escape_string($dbc, trim($_POST['game']));
	$gameQuery = "SELECT game_id FROM games WHERE game_name = '$gameOldName'";
	$row = mysqli_fetch_array(mysqli_query($dbc, $gameQuery));
	
	$game = $row[game_id];
	$race = mysqli_real_escape_string($dbc, trim($_POST['race']));
	$class = mysqli_real_escape_string($dbc, trim($_POST['class']));
	$level = mysqli_real_escape_string($dbc, trim($_POST['level']));
	$charsheet = mysqli_real_escape_string($dbc, trim($_FILES['charSheet']['name']));
	$charsheet_type = $_FILES['charSheet']['type'];
	$charsheet_size = $_FILES['charSheet']['size'];
	$error = false;
	
	// Validate and move the uploaded character sheet, if necessary
	if (is_uploaded_file($_FILES['charSheet']['tmp_name'])) {
		if (($charsheet_type == 'application/pdf') && ($charsheet_size > 0) && ($charsheet_size <= MM_MAXFILESIZE)){
			if ($_FILES['file']['error'] == 0) {
				$target = MM_UPLOADPATH . $name . $game . basename($charsheet);
				// Move the file to the target upload folder
				if (move_uploaded_file($_FILES['charSheet']['tmp_name'], $target)) {
					//This will run if file move is successful
					echo '<p>Character sheet successfully uploaded.</p>';
					echo '<p>Find it in a few minutes <a href="http://www.adamhollock.com/characterkeeper/' . $target .
						'">here.</a></p>';
				}
				else{
					@unlink($_FILES['charSheet']['tmp_name']);
					$error = true;
					echo '<p class="error">Sorry, there was a problem uploading your character sheet.</p>';
				}
			}
		}
		else{
			// The sheet file is not valid, so delete the temp file & set the error flag
			@unlink($_FILES['charsheet']['tmp_name']);
			$error = true;
			echo '<p class="error">Your character sheet must be a PDF file no greater than ' .
			'1 MB in size.</p>';
		}
	}
	
	if (!error){
    if (!empty($name) && !empty($game) && !empty($race) && !empty($class) && !empty($level)) {
		// Write the data into the database
		$query = "INSERT INTO characters VALUES (0, NOW(), '$user', '$game', '$name', '$class', '$race', '$level', 0, '$charsheet')";
		// Makes the query to the database, and returns an error message if there's a failure.
		mysqli_query($dbc, $query)
		or die('Error querying database');
		
		// Confirm successful addition to user
		echo 'Excellent! Your character has been added!<br/>';
		echo 'The ' . $race . ', ' . $name . ', is a level ' . $level . ' ' . $class . '<br />';
		echo 'Now you just need to wait for your DM to approve this character.';
		echo '<br />';
		
		//Clears the character's data to clear the form.
		$name = "";
		$race = "";
		$game = "";
		$class = "";
		$level = "";
		$background = "";
		$charsheet = "";
	
		// Closes the database connection
		mysqli_close($dbc);
    } else {
	echo '<p class="error">Please enter all of the information to add your character</p>';
    }
	}
  }
?>
	
	
	<hr />
	<form enctype="multipart/form-data" method="post" action"<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MM_MAXFILESIZE; ?>" />
	<fieldset>
		<legend>Character Upload</legend>
		
		<label for="name">Name: </label>
		<input type="text" id="name" name="name" 
		value="<?php if (!empty($name)) echo $name; ?>" /><br />
		
		<label for="race">Race: </label>
		<input type="text" id="race" name="race" 
		value="<?php if (!empty($race)) echo $race; ?>" /><br />
		
		<label for="class">Class: </label>
		<input type="text" id="class" name="class" 
		value="<?php if (!empty($class)) echo $class; ?>" /><br />
		
		<label for="level">Level: </label>
		<input type="text" id="level" name="level" 
		value="<?php if (!empty($level)) echo $level; ?>" /><br />
		
		<label for="game">Game: </label>
		<select name="game">
			<option value="-1">Please select...</option>
		<?
			while($gameName = mysqli_fetch_array($games)){
				echo '<option>' . $gameName['game_name'] . '</option>';
			}
		?>
		</select><br />
		
		<label for="charSheet">Character Sheet (PDF, 1MB Maximum, Optional):</label>
		<input type="file" id="charSheet" name="charSheet" /><br />
	</fieldset>
	<input type="submit" value="Add" name="submit" />
	</form>

<?php
	// Insert the page footer
	require_once('footer.php');
?>