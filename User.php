<?php
	require('Database.php');
	require('Http.php');

	class User {
		public function syncUser($username, $hotspotLoginToken) {
			$config = new Config;
			
			$synced = false;
				
			//insert or update local user
			$db = new Database;
			$pdo = $db->start();
			
			//first check if user exists
			$stmt = $pdo->prepare("SELECT * FROM radcheck WHERE username=:username LIMIT 1");
			$stmt->execute(['username' => $username]);
			$user = [];
			while($row = $stmt->fetch()) {
				$user = $row;
			}
			
			if(empty($user)) { 
				//insert new user
				$stmt = $pdo->prepare("INSERT INTO radcheck(username,attribute,op,value) VALUES(:username, :attribute, :op, :value)");
				$stmt->execute(['username' => $username,'attribute' => "Cleartext-Password",'op' => ":=",'value' => $hotspotLoginToken]);
				if($stmt->rowCount() > 0) {
					//user synced
					$synced = true;
				}
			}
			else {
				//update user only if hotspot_login_token has changed
				if($user['value'] != $hotspotLoginToken) {
					$stmt = $pdo->prepare("UPDATE radcheck SET value=:value WHERE username=:username");
					$status = $stmt->execute(['value' => $hotspotLoginToken, 'username' => $username]);
					if($stmt->rowCount() > 0) {
						//user synced
						$synced = true;
					}
				}
				else {
					$synced = true;
				}
			}
			$db->close();
			
			if($synced) {
				return ['status' => true, 'username' => $username, 'password' => $hotspotLoginToken];
			}
			else {
				return ['status' => false];
			}
		}
		
		public function getSubscribedUsers() {
			//fetch users to disconnect from online database
			$http = new Http;
			$config = new Config;
			try {
				$http->setAuth($config->get('API_KEY'));
				$http->curlGET($config->get('get_subscribed_users_url'));
				$responseArray = $http->run();
				
				if($responseArray['status'] == true) {
					$subscribedUsers = json_decode($responseArray['response'], true);
					//check if response is valid json
					if(json_last_error() != JSON_ERROR_NONE) {
						throw new Exception("Error: ".$responseArray['response']);
					}
					
					return $subscribedUsers;
				}
				else {
					throw new Exception("Cannot fetch subscribed users.");
				}
			} 
			catch (\Exception $e) {
				return $e->getMessage();
			}
		}
		
		public function getConnectedUsers() {
			//fetch users to disconnect from online database
			$http = new Http;
			$config = new Config;
			
			try {
				$http->curlGET($config->get('get_connected_users_url'));
				$responseArray = $http->run();
				
				if($responseArray['status'] == true) {
					$connectedUsers = json_decode($responseArray['response'], true);
					//check if response is valid json
					if(json_last_error() != JSON_ERROR_NONE) {
						throw new Exception("Error: ".$responseArray['response']);
					}
					
					return $connectedUsers;
				}
				else {
					throw new Exception("Cannot fetch connected users.");
				}
			} 
			catch (\Exception $e) {
				return $e->getMessage();
			}
		}
		
		public function disconnectUser($sessionID) {
			if($sessionID == "") return "Session ID is empty.";
			
			$config = new Config;
			$http = new Http;
				
			// set default options
			$options = array(
			  CURLOPT_URL => $config->get('hotspot_disconnect_url'),
			  CURLOPT_RETURNTRANSFER => 1,
			  CURLOPT_TIMEOUT => 5
			);
			
			$data = [
						'logout_id' => $sessionID, 
						'zone' => $config->get('zone'),
						'logout' => "Disconnect",
						'Content-Type' => 'application/x-www-form-urlencoded',
					];
					
			$requestBody = ' ';
			foreach ($data as $key => $value) {
				$requestBody .= "&$key=$value";
			}
		
			$options[CURLOPT_POST] = 1;
			$options[CURLOPT_POSTFIELDS] = trim($requestBody);
			
			try {
				$http->options = $options;
				$responseArray = $http->run();
				if($responseArray['status_code'] == 200) {
					return $responseArray['response'];
				}
				else {
					throw new Exception($responseArray['response']);
				}
			} 
			catch (\Exception $e) {
				return $e->getMessage();
			}
		}
		
		public function connectUser($portalLoginUrl, $username, $password, $zone, $redirUrl) {
			// set default options
			$options = array(
			  CURLOPT_URL => $portalLoginUrl,
			  CURLOPT_RETURNTRANSFER => 1,
			  CURLOPT_TIMEOUT => 30
			);
			
			// set auth_user, auth_pass, zone, redirurl to mimick login form
			$data = [
						'auth_user' => $username, 
						'auth_pass' => $password, 
						'zone' => $zone,
						'redirurl' => $redirUrl,
						'auth_voucher' => "",
						'accept' => "Login",
						'Content-Type' => 'application/x-www-form-urlencoded',
					];
					
			$requestBody = ' ';
			foreach ($data as $key => $value) {
				$requestBody .= "&$key=$value";
			}
			
			$options[CURLOPT_POST] = 1;
			$options[CURLOPT_POSTFIELDS] = trim($requestBody);
				
			try {
				$http = new Http;
				$http->options = $options;
				$responseArray = $http->run();
				if($responseArray['status_code'] == 200) {
					return $responseArray['response'];
				}
				else {
					throw new Exception($responseArray['response']);
				}
			} 
			catch (\Exception $e) {
				return $e->getMessage();
			}
		}
		
	}
?>