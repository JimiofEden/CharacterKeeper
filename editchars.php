<?php
	// Start the session
	require_once('startsession.php');
	
	// Redirect in case they're not logged in
	require_once('redirect.php');
	
	// Insert the page header
	$page_title = 'edit your characters';
	require_once('header.php');
	require_once('navmenu.php');
	
	require_once('appvars.php');
	require_once('connectvars.php');
	
	// Connect to the database
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$done = false;
  // activates when the submit button is clicked
  
  if (isset($_POST['submit'])) {
	$oldCharName = mysqli_real_escape_string($dbc, trim($_POST['char']));
	if ($oldCharName != 'Please select...'){
		$charChosen = true;
		$query = "SELECT name, class, race, level, char_sheet, user_id, game_id FROM characters WHERE name = '$oldCharName' AND user_id = '" . $_SESSION['user_id'] . "'";
		$row = mysqli_fetch_array(mysqli_query($dbc, $query));
		
		$charName = $row['name'];
		$charClass = $row['class'];
		$charRace = $row['race'];
		$charLevel = $row['level'];
		$user = $row['user_id'];
		$game = $row['game_id'];
		$oldcharsheet = $row['char_sheet'];
	}
	else{
		$oldCharName = 'Please select...';
		echo 'You must choose a character from the list.';
		$query = "SELECT name FROM characters WHERE user_id = '" . $_SESSION['user_id'] . "'";
		$charsOld = mysqli_query($dbc, $query);
	}
	
	if ($charChosen){
		// Grab the character data from the POST
		$name = mysqli_real_escape_string($dbc, trim($_POST['name']));
		$class = mysqli_real_escape_string($dbc, trim($_POST['class']));
		$race = mysqli_real_escape_string($dbc, trim($_POST['race']));
		$level = mysqli_real_escape_string($dbc, trim($_POST['level']));
		$approval = 0;
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
						@unlink(MM_UPLOADPATH . $user . $game . basename($oldcharsheet));
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
		
		if (!$error){
		if (!empty($name) && !empty($class) && !empty($race) && !empty($level)) {
			// Write the data into the database
			$updateStart = "UPDATE characters SET name = '$name', class = '$class', race = '$race', level = '$level', approved = 0";
				if (!empty($charsheet)){
					$updateEnd = ", char_sheet = '$charsheet' WHERE name = '$oldCharName' AND user_id = '" . $_SESSION['user_id'] . "'";
				}
				else{
					$updateEnd = " WHERE name = '$oldCharName' AND user_id = '" . $_SESSION['user_id'] . "'";
				}
			$update = $updateStart . $updateEnd;
			// Makes the query to the database, and returns an error message if there's a failure.
			mysqli_query($dbc, $update)
			or die('Error querying database');
			
			// Confirm successful addition to user
			echo 'Excellent! Your character has been revised!<br/>';
			echo 'The ' . $race . ', ' . $name . ', is a level ' . $level . ' ' . $class . '<br />';
			echo '<br />';
			
			//Clears the character's data to clear the form.
			$name = "";
			$class = "";
			$race = "";
			$level = "";
			$charsheet = "";
		
			// Closes the database connection
			mysqli_close($dbc);
			$done = true;
		} else {
			echo '<p class="error">Please enter all of the information to edit your character</p>';
			$query = "SELECT game_name FROM games WHERE user_id = '" . $_SESSION['user_id'] . "'";
			$charsOld = mysqli_query($dbc, $query);
		}
		}
	}
  }
  else{
	$charChosen = false;
	$query = "SELECT name FROM characters WHERE user_id = '" . $_SESSION['user_id'] . "'";
	$charsOld = mysqli_query($dbc, $query);
  }	
?>

</script>
	
	<hr />
	<?php
		if (!$done){
	?>
	<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MM_MAXFILESIZE; ?>" />
	<fieldset>
		<legend>Character Edit</legend>
		
		<label for="char">Character: </label>
		<select name="char"">
			<option><?php if(isset($name) AND (!empty($name))){echo $name;}elseif(isset($oldCharName)){echo $oldCharName;}else{echo 'Please select...';}?></option>
		<?php
			while($charName = mysqli_fetch_array($charsOld)){
				echo '<option>' . $charName['name'] . '</option>';
			}
		?>
		</select><br />
		
		<?php
		if ($charChosen){
		echo '<label for="name">Name: </label>';
		echo '<input type="text" id="name" name="name" value="' . $oldCharName . '" /><br />';
		
		echo '<label for="class">Class: </label>';
		echo '<input type="text" id="class" name="class" value="'. $charClass .'" /><br />';
		
		echo '<label for="race">Race: </label>';
		echo '<input type="text" id="race" name="race" value="'. $charRace .'" /><br />';
		
		echo '<label for="level">Level: </label>';
		echo '<input type="text" id="level" name="level" value="'. $charLevel .'" /><br />';
		
		echo '<label for="charSheet">Character Sheet (PDF, 1MB Maximum, Optional):</label>';
		echo '<input type="file" id="charSheet" name="charSheet" /><br />';
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