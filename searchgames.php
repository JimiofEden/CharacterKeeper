<?php
	// Start the session
	require_once('startsession.php');
	
	// Redirect in case they're not logged in
	require_once('redirect.php');
	
	// Insert the page header
	$page_title = 'view games';
	require_once('header.php');
	require_once('navmenu.php');
	
	require_once('appvars.php');
	require_once('connectvars.php');
	
	// Connect to the database
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	
	$query = "SELECT game_name, game_setting, game_description, game_id, gameMaterials FROM games ORDER BY date";
	
	$chars = mysqli_query($dbc, $query);

	while($data = mysqli_fetch_array($chars)){
		echo '<p><strong>' . $data['game_name'] . '</strong> - ' . $data['game_setting'] . '<br/>' . $data['game_description'] . '<br/>Game Materials: ';
		if (!empty($data['gameMaterials'])){
			echo '<a href="games/' . $data['game_setting'] . $data['game_name'] . $data['gameMaterials'] . '">' . $data['gameMaterials'] . '</a>';
		}
		else{
			echo 'N/A';
		}
		echo '</p>';
	}
	
	// Insert the page footer
	require_once('footer.php');
?>