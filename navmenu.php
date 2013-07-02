<?php
	// Generate the navigation menu
	echo '
	<nav class="mainMenu">
		<ul>
			<li><a href="index.php">home</a></li>';
			
	if (isset($_SESSION['username'])) {
		echo '
		
			<li><a href="viewgames.php" rel="gameMenu">games</a>
				<ul>
					<li><a href="addgames.php">add a game</a></li>
					<li><a href="editgames.php">edit your games</a></li>
					<li><a href="viewgames.php">view games</a></li>
				</ul>
			<li><a href="viewchars.php" rel="charMenu">characters</a>
				<ul>
					<li><a href="addchars.php">add characters</a></li>
					<li><a href="editchars.php">edit your characters</a></li>
					<li><a href="viewchars.php">view characters</a></li>
				</ul>
			<li><a href="viewtales.php" rel="taleMenu">stories</a>
				<ul>
					<li><a href="addtales.php">add a story</a></li>
					<li><a href="viewtales.php">view stories</a></li>
				</ul>
			<li><a href="user.php">'. $_SESSION['username'] . '</a>
				<ul>
					<li><a href="changepass.php">change password</a></li>
					<li><a href="logout.php">log out (' . $_SESSION['username'] . ')</a></li>
				</ul>';
	}			
	else {
		echo '
			<li><a href="login.php">log ln</a></li>
			<li><a href="signup.php">sign up</a></li>';
	}
	
	echo '
			<li><a href="about.php">about</a></li>
		</ul>
	</nav>
	</div>
		<div class="fullBody">';
?>