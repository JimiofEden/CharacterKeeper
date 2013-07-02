<?php
	// Start the session
	require_once('startsession.php');
	
	// Redirect in case they're not logged in
	require_once('redirect.php');
	
	// Insert the page header
	$page_title = 'read stories';
	require_once('header.php');
	require_once('navmenu.php');
	
	require_once('appvars.php');
	require_once('connectvars.php');
	
	// Connect to the database
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	
	$query = "SELECT stories.storyName, stories.game_id, stories.story, games.game_name FROM stories INNER JOIN games ON games.game_id WHERE games.game_id = stories.game_id ORDER BY stories.date";
	
	$stories = mysqli_query($dbc, $query);

	$oldGame = '';
	while($data = mysqli_fetch_array($stories)){
		if ($data['game_name'] != $oldGame){
			echo '<h3>' . $data['game_name'] . '</h3>';
		}
		echo '<p><strong>' . $data['storyName'] . '</strong>: ' . $data['story'] . '</p>';
		$oldGame = $data['game_name'];
	}
	
	// Insert the page footer
	require_once('footer.php');
?>