<?php
	// Start the session
	require_once('startsession.php');
	
	// Redirect in case they're not logged in
	require_once('redirect.php');
	
	// Insert the page header
	$page_title = 'edit your game';
	require_once('header.php');
	require_once('navmenu.php');
	
	require_once('appvars.php');
	require_once('connectvars.php');
	
	// Connect to the database
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$done = false;
  // activates when the submit button is clicked
  
  if (isset($_POST['submit'])) {
	$oldGameName = mysqli_real_escape_string($dbc, trim($_POST['game']));
	if ($oldGameName != 'Please select...'){
		$gameChosen = true;
		$query = "SELECT game_name, game_setting, game_description, user_id, game_id FROM games WHERE game_name = '$oldGameName' AND user_id = '" . $_SESSION['user_id'] . "'";
		$row = mysqli_fetch_array(mysqli_query($dbc, $query));
		
		$gameName = $row['game_name'];
		$gameSetting = $row['game_setting'];
		$gameDescription = $row['game_description'];
		$oldGameMat = $row['gameMaterials'];
		$game = $row['game_id'];
	}
	else{
		$oldGameName = 'Please select...';
		echo 'You must choose a game from the list.';
		$query = "SELECT game_name FROM games WHERE user_id = '" . $_SESSION['user_id'] . "'";
		$gamesOld = mysqli_query($dbc, $query);
	}
	
	if ($gameChosen){
		// Grab the character data from the POST
		$name = mysqli_real_escape_string($dbc, trim($_POST['name']));
		$setting = mysqli_real_escape_string($dbc, trim($_POST['setting']));
		$description = mysqli_real_escape_string($dbc, trim($_POST['description']));
		$gameMat = mysqli_real_escape_string($dbc, trim($_FILES['gameMat']['name']));
		$gameMat_type = $_FILES['gameMat']['type'];
		$gameMat_size = $_FILES['gameMat']['size'];
		$error = false;
	
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
						@unlink(MAT_UPLOADPATH . $setting . $name . basename($oldGameMat));
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
			$update = "UPDATE games SET game_name = '$name', game_setting = '$setting', game_description = '$description', gameMaterials = '$gameMat'
						WHERE game_name = '$oldGameName' AND user_id = '" . $_SESSION['user_id'] . "'";
			// Makes the query to the database, and returns an error message if there's a failure.
			mysqli_query($dbc, $update)
			or die('Error querying database');
			
			// Confirm successful addition to user
			echo 'Excellent! Your game has been revised!<br/>';
			echo $name . ' takes place in ' . $setting;
			echo '<br />';
			
			//Clears the character's data to clear the form.
			$name = "";
			$setting = "";
			$description = "";
		
			// Closes the database connection
			mysqli_close($dbc);
			$done = true;
		} else {
			echo '<p class="error">Please enter all of the information to add your character</p>';
			$query = "SELECT game_name FROM games WHERE user_id = '" . $_SESSION['user_id'] . "'";
			$gamesOld = mysqli_query($dbc, $query);
		}
		}
	}
  }
  else{
	$gameChosen = false;
	$query = "SELECT game_name FROM games WHERE user_id = '" . $_SESSION['user_id'] . "'";
	$gamesOld = mysqli_query($dbc, $query);
  }	
?>

</script>
	
	<hr />
	<?php
		if (!$done){
	?>
	<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAT_MAXFILESIZE; ?>" />
	<fieldset>
		<legend>Game Edit</legend>
		
		<label for="game">Game: </label>
		<select name="game"">
			<option><?php if(isset($name) AND (!empty($name))){echo $name;}elseif(isset($oldGameName)){echo $oldGameName;}else{echo 'Please select...';}?></option>
		<?php
			while($gameName = mysqli_fetch_array($gamesOld)){
				echo '<option>' . $gameName['game_name'] . '</option>';
			}
		?>
		</select><br />
		
		<?php
		if ($gameChosen){
		echo '<label for="name">Name: </label>';
		echo '<input type="text" id="name" name="name" value="' . $oldGameName . '" /><br />';
		
		echo '<label for="setting">Setting: </label>';
		echo '<input type="text" id="setting" name="setting" value="'. $gameSetting .'" /><br />';
		
		echo '<label for="description">Desc.: </label>';
		echo '<textarea class="bigText" id="description" name="description" value="'. $gameDescription .'" />'. $gameDescription .'</textarea><br />';
		
		echo '<label for="gameMat">Game materials(ZIP, RAR, or PDF, 7MB Maximum, Optional):</label>';
		echo '<input type="file" id="gameMat" name="gameMat" /><br />';
		}
		?>
		
		
	</fieldset>
	<input type="submit" value="Edit" name="submit" />
	</form>
	<?php
	}
	?>
<?php
	// Insert the page footer
	require_once('footer.php');
?>