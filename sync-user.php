<?php
	require('User.php');
	$user = new User;
	$username = $_GET['u'];
	$password = $_GET['p'];
	$url = $_GET['url'];
	$zone = $_GET['zone'];
	$redirurl = $_GET['redirurl'];
	
	$response = $user->syncUser($username, $password);
	//$loginResponse = $user->connectUser($url, $username, $password, $zone, $redirurl);
	//print($loginResponse);die;
	
	if($response['status'] === true) {
		//redirect to pfsense login to auto-login user
		/*do not encode url*/
		$loginUrl = $url."?zone=".$zone."&redirurl=".$redirurl."&u=".$username."&p=".$password;
		
		header('Location: '.$loginUrl);
	}
	else {
		print("Failed to sync user. Try again.");
	}
?>