<?php
	// This will redirect the user in the case that they are not logged in
	if (!isset($_SESSION['username'])){
		$_SESSION['login'] = true;
		header('location:login.php');
		die();
	}
?>