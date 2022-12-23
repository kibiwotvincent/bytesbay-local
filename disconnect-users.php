<?php

	require('User.php');
	$user = new User;
	$users = $user->getUsersToDisconnect();
	
	$disconnectedUsers = [];
	foreach($users as $username) {
		$status = $user->disconnectUser($username);
		
		if($status === true) {
			array_push($disconnectedUsers, $username);
			print($username." has been disconnected.<br/>");
		}
		else {
			print("Error disconnecting ".$username."<br/>");
		}
	}
	
	if(! empty($disconnectedUsers)) {
		//tell online server users have been disconnected
		$user->disconnected($disconnectedUsers);
	}
	
?>