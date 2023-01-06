<?php

	require('User.php');
	$user = new User;
	
	$username = $_GET['u'];
	$status = $user->disconnectUser($username);
	
	if($status === true) {
		print($username." has been disconnected.<br/>");
	}
	else {
		print("Error disconnecting ".$username."<br/>");
	}
	
?>