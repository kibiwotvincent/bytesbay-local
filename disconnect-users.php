<?php
	require('User.php');
	$user = new User;
	$subscribedUsers = $user->getSubscribedUsers();
	if(! is_array($subscribedUsers)) {
		/*
		* There was an error while fetching subscribed users. 
		* Don't proceed as we are not sure which users to disconnect. 
		* May be online server cannot be reached due to network issues.
		* The only downside is users that were to be disconnected will enjoy a minute of free internet.
		*/
		print($response = $subscribedUsers);
		exit;
	}
	
	$connectedUsers = $user->getConnectedUsers();
	if(! is_array($connectedUsers)) {
		/*
		* There was an error while fetching connected users from pfsense. 
		* Don't proceed as we are not sure which users to disconnect. 
		* May be pfsense cannot be reached due to network issues.
		* The only downside is users that were to be disconnected will enjoy a minute of free internet.
		*/
		print($response = $connectedUsers);
		exit;
	}
	
	if(empty($connectedUsers)) {
		print("There are no connected users in pfsense.");
		exit;
	}
	
	foreach($connectedUsers as $row) {
		$username = $row[4];
		$sessionID = $row[5];
		if(! in_array($username, $subscribedUsers)) {
			//disconnect
			print("DISCONNECTING ".$username." - ".$sessionID."............");
			$response = $user->disconnectUser($sessionID);
			if($response == "DISCONNECTED") {
				print("DISCONNECTED <br/>");
			}
			else {
				print("FAILED <br/>");
			}
		}
	}
	
?>