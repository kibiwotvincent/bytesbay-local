<?php
	require('User.php');
	
	//disconnect from pfsense
	$user = new User;
	
	$sessionID = $_GET['session'];
	$response = $user->disconnectUser($sessionID);
	if($response == "DISCONNECTED") {
		print("DISCONNECTED");
	}
	else {
		print("FAILED");
	}
?>