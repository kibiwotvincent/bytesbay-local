<?php
	require('User.php');
	$user = new User;
	$response = $user->syncUser($_GET['u'], $_GET['p']);
	if($response['status'] === true) {
		//do login
		$user->connectUser($_GET['loginurl'], $response['username'], $response['password'], $_GET['zone'], $_GET['redirurl']);
	}
	
	//redirect to google.com 
	//if user was logged in successfully then google.com will load, 
	//otherwise they will be redirected to captiveportal page
	header('Location: http://www.google.com');
?>