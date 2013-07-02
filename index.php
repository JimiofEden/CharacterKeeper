<?php
	// Start the session
	require_once('startsession.php');
	
	// Insert the page header
	$page_title = 'character tracker';
	require_once('header.php');
	require_once('navmenu.php');
	
	require_once('appvars.php');
	require_once('connectvars.php');
	
	// Connect to the database 
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 
	
	$charQuery = "SELECT name, class, race, level FROM characters ORDER BY date DESC LIMIT 3";
	$chars = mysqli_query($dbc, $charQuery);
	
	$gameQuery = "SELECT game_name, game_setting, game_description FROM games ORDER BY date DESC LIMIT 3";
	$games = mysqli_query($dbc, $gameQuery);
	
	$gameQuery = "SELECT storyName, story FROM stories ORDER BY date DESC LIMIT 3";
	$stories = mysqli_query($dbc, $gameQuery);
	
?>
<p>Ever left your character sheet at home? Ever simply forget materials or what your adventure was about? Ever want to share your imaginary exploits with the world?</p>
<p>Well, look no further. With the Tabletop Character Tracker, you can upload your character sheets and your adventures at any time.</p>

<h3>latest characters</h3>
<ul>
<?php
	while($charName = mysqli_fetch_array($chars)){
		echo '<li>The ' . $charName['race'] . ', <strong>' . $charName['name'] . '</strong>, a level ' . $charName['level'] . ' ' . $charName['class'] . '</li>';
	}
?>
</ul>


<h3>latest games</h3>
<ul>
<?php
	
	while($gameName = mysqli_fetch_array($games)){
		echo '<li><strong>'. $gameName['game_name'] . '</strong>, takes place in ' . $gameName['game_setting'] . ' --- ' . $gameName['game_description'] . '</li>';
	}
?>
</ul>

<h3>latest stories</h3>
<ul>
<?php
	
	while($storyName = mysqli_fetch_array($stories)){
		echo '<li><strong>'. $storyName['storyName'] . ' --- </strong> ' . $storyName['story'] . '</li>';
	}
?>
</ul>

<?php
	// Close the database
	mysqli_close($dbc);
	
	// Insert the page footer
	require_once('footer.php');
?>