<?php
	header('Access-Control-Allow-Origin: http://www.captiveportal.com');/*allow being called from captive portal login*/
	
	require('User.php');
	$user = new User;
	$response = $user->syncUser($_POST['token']);
	if($response['status'] === true) {
		print json_encode(['status' => 1, 'username' => $response['username'], 'password' => $response['password']]);
	}
	else {
		print json_encode(['status' => 0]);
	}
?>