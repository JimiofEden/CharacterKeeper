<?php
	// Start the session
	require_once('startsession.php');
	
	// Redirect in case they're not logged in
	require_once('redirect.php');
	
	// Insert the page header
	$page_title = 'view characters';
	require_once('header.php');
	require_once('navmenu.php');
	
	require_once('appvars.php');
	require_once('connectvars.php');
	
	// Connect to the database
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	
	$query = "SELECT characters.name, characters.class, characters.race, characters.level,
	characters.char_sheet, games.game_name AS gn, games.game_id as gameNo FROM characters INNER JOIN games ON
	characters.game_id = games.game_id ORDER BY characters.date";
	
	$chars = mysqli_query($dbc, $query);

	while($data = mysqli_fetch_array($chars)){
		echo '<p><strong>' . $data['name'] . '</strong><br/>' . $data['race'] . ' ' . $data['class'] .
			'<br/>Level ' . $data['level'] . '<br/>';
		if (!empty($data['char_sheet'])){
			echo '<a href="characters/' . $data['name'] . $data['gameNo'] . $data['char_sheet'] . '">' . $data['char_sheet'] . '</a>';
		}
		else{
			echo 'N/A';
		}
		echo '<br/>Currently playing in ' . $data['gn'] . '</p>';
	}
	
	// Insert the page footer
	require_once('footer.php');
?>