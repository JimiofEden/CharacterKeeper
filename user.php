<?php
	// Start the session
	require_once('startsession.php');
	
	// Redirect in case they're not logged in
	require_once('redirect.php');
	
	// Insert the page header
	$page_title = 'your profile';
	require_once('header.php');
	require_once('navmenu.php');
	
	require_once('appvars.php');
	require_once('connectvars.php');
	
	// Connect to the database
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$user = $_SESSION['user_id'];
	
	$charQuery = "SELECT name, class, race, level, char_sheet, game_id FROM characters WHERE user_id = '$user' ORDER BY date";
	$gameQuery = "SELECT game_name, game_setting, game_description, gameMaterials FROM games WHERE user_id = '$user' ORDER BY date";
	$storyQuery = "SELECT storyName, story FROM stories WHERE user_id = '$user' ORDER BY date";
	$charData = mysqli_query($dbc, $charQuery);
	$gameData = mysqli_query($dbc, $gameQuery);
	$storyData = mysqli_query($dbc, $storyQuery);
	
	echo '<p>Hi, <strong>' . $_SESSION['username'] . '</strong>. Here are your uploads to the tracker.</p>';
	
	echo '<h2>characters</h2>';
	$count = 1;
	while($row = mysqli_fetch_array($charData)){
		echo '<p><strong>' . $count . '. ' . $row['name'] . '</strong><br/>' . $row['race'] . ' ' . $row['class'] .
			'<br/>Level ' . $row['level'] . '<br/>';
		if (!empty($row['charSheet'])){
			echo '<a href="characters/' . $row['name'] . $row['game_id'] . $row['char_sheet'] . '">' . $row['char_sheet'] . '</a>';
		}
		else{
			echo 'N/A';
		}
		echo '<br/>Currently playing in ' . $row['name'] . '</p>';
		$count = $count + 1;
	}
	
	echo '<h2>games</h2>';
	$count = 1;
	while($row = mysqli_fetch_array($gameData)){
		echo '<p><strong>' . $count . '. ' . $row['game_name'] . '</strong> -- ' . $row['game_setting'] . '<br/>' . $row['game_description'] .
			'</p>';
		if (!empty($row['gameMaterials'])){
			echo '<a href="games/' . $row['game_setting'] . $row['game_name'] . $row['gameMaterials'] . '">' . $row['gameMaterials'] . '</a>';
		}
		else{
			echo 'N/A';
		}
		$count = $count + 1;
	}
	
	echo '<h2>stories</h2>';
	$count = 1;
	while($row = mysqli_fetch_array($storyData)){
		echo '<p><strong>' . $count . '. ' . $row['storyName'] . '</strong><br/>' . $row['story'] . '</p>';
		$count = $count + 1;
	}
	
	// Insert the page footer
	require_once('footer.php');
?>