<?php
	// Start the session
	require_once('startsession.php');
	
	// Redirect in case they're not logged in
	require_once('redirect.php');
	
	// Insert the page header
	$page_title = 'add a game';
	require_once('header.php');
	require_once('navmenu.php');
	
	require_once('appvars.php');
	require_once('connectvars.php');
	
	// Connect to the database
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  // activates when the submit button is clicked
  if (isset($_POST['submit'])) {
	// Grab the character data from the POST
	$user = $_SESSION['user_id'];
	$name = mysqli_real_escape_string($dbc, trim($_POST['name']));
	$setting = mysqli_real_escape_string($dbc, trim($_POST['setting']));
	$description = mysqli_real_escape_string($dbc, trim($_POST['description']));
	$gameMat = mysqli_real_escape_string($dbc, trim($_FILES['gameMat']['name']));
	$gameMat_type = $_FILES['gameMat']['type'];
	$gameMat_size = $_FILES['gameMat']['size'];
	$error = false;
	
	$checkGame = "SELECT * FROM games WHERE game_name = '$name'";
	$data = mysqli_query($dbc, $checkGame);
	if (mysqli_num_rows($data) == 0) {
	
		// Validate and move the uploaded character sheet, if necessary
		$allowedCompressedTypes = array("application/x-rar-compressed", "application/zip", "application/x-zip", "application/octet-stream", "application/x-zip-compressed", "application/pdf");
		if (is_uploaded_file($_FILES['gameMat']['tmp_name'])) {
			if (in_array($gameMat_type, $allowedCompressedTypes) && ($gameMat_size > 0) && ($gameMat_size <= MAT_MAXFILESIZE)){
				if ($_FILES['file']['error'] == 0) {
					$target = MAT_UPLOADPATH . $setting . $name . basename($gameMat);
					// Move the file to the target upload folder
					if (move_uploaded_file($_FILES['gameMat']['tmp_name'], $target)) {
						//This will run if file move is successful
						echo '<p>Game materials successfully uploaded.</p>';
						echo '<p>Find it in a few minutes <a href="http://www.adamhollock.com/characterkeeper/' . $target .
							'">here.</a></p>';
					}
					else{
						@unlink($_FILES['gameMat']['tmp_name']);
						$error = true;
						echo '<p class="error">Sorry, there was a problem uploading your game materials.</p>';
					}
				}
			}
			else{
				// The sheet file is not valid, so delete the temp file & set the error flag
				@unlink($_FILES['gameMat']['tmp_name']);
				$error = true;
				echo '<p class="error">Your game materials must be a ZIP, RAR, or PDF file no greater than ' .
				'7 MB in size.</p>';
			}
		}
		
		if (!$error){
		if (!empty($name) && !empty($setting) && !empty($description)) {
			// Write the data into the database
			$query = "INSERT INTO games VALUES (0, NOW(), '$user', '$name', '$setting', '$description', '$gameMat')";
			// Makes the query to the database, and returns an error message if there's a failure.
			mysqli_query($dbc, $query)
			or die('Error querying database');
			
			// Confirm successful addition to user
			echo 'Excellent! Your game has been added!<br/>';
			echo $name . ' takes place in ' . $setting;
			echo '<br />';
			
			//Clears the game's data to clear the form.
			$name = "";
			$setting = "";
			$description = "";
			$gameMat = "";
		
			// Closes the database connection
			mysqli_close($dbc);
		} else {
		echo '<p class="error">Please enter ALL of the information to add your game</p>';
		}
		}
	}
	else {
		echo '<p class="error">This game name already exists. Please choose another.</p>';
	}
  }
?>
	
	
	<hr />
	<form enctype="multipart/form-data" method="post" action"<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAT_MAXFILESIZE; ?>" />
	<fieldset>
		<legend>Game Upload</legend>
		
		<label for="name">Name: </label>
		<input type="text" id="name" name="name" 
		value="<?php if (!empty($name)) echo $name; ?>" /><br />
		
		<label for="setting">Setting: </label>
		<input type="text" id="setting" name="setting" 
		value="<?php if (!empty($setting)) echo $setting; ?>" /><br />
		
		<label for="description">Desc.: </label>
		<textarea class="bigText" id="description" name="description" 
		value="<?php if (!empty($description)) echo $description; ?>" />Add the game description here...</textarea><br />
		
		<label for="gameMat">Game materials (ZIP, RAR or PDF, 7MB Maximum, Optional):</label>
		<input type="file" id="gameMat" name="gameMat" /><br />
	</fieldset>
	<input type="submit" value="Add" name="submit" />
	</form>

<?php
	// Insert the page footer
	require_once('footer.php');
?>