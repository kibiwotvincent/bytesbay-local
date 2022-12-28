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
		
		public function getUsersToDisconnect() {
			//fetch users to disconnect from online database
			$http = new Http;
			$config = new Config;
			$http->setAuth($config->get('API_KEY'));
			$http->curlGET($config->get('disconnect_users_url'));
			$response = $http->run();
			
			if($response['status_code'] == 200) {
				return json_decode($response['response'], true);
			}
			else {
				return [];
			}
		}
		
		public function getActiveUserSessions($username) {
			$db = new Database;
			$pdo = $db->start();
			$stmt = $pdo->prepare("SELECT radacctid,acctsessionid,username FROM radacct WHERE username=:username AND acctstoptime=:acctstoptime");
			$stmt->execute(['username' => $username, 'acctstoptime' => null]);
			
			//loop through all active session ids and logout session passing session id along
			$activeSessions = [];
			while($row = $stmt->fetch()) {
				array_push($activeSessions, $row['acctsessionid']);
			}
			$db->close();
			
			return $activeSessions;
		}
		
		public function disconnectUser($username) {
			$activeSessions = $this->getActiveUserSessions($username);
			$config = new Config;
			
			foreach($activeSessions as $sessionID) {
				
				// set default options
				$options = array(
				  CURLOPT_URL => $config->get('hotspot_disconnect_url'),
				  CURLOPT_HEADER => 1,
				  CURLOPT_RETURNTRANSFER => 1,
				  CURLOPT_POSTFIELDS => null,
				  CURLOPT_TIMEOUT => 10
				);
				// set logout_id, zone to mimick logout form
				$data = [
							'logout_id' => $sessionID, 
							'zone' => $config->get('captivezone_name'),
							'logout' => "Disconnect", 
							'Content-Type' => 'application/x-www-form-urlencoded',
							'Accept' => 'application/json'
						];
						
				$requestBody = ' ';
				foreach ($data as $key => $value) {
					$requestBody .= "&$key=$value";
				}
			
				$options[CURLOPT_POST] = 1;
				$options[CURLOPT_POSTFIELDS] = trim($requestBody);
					
				$curl = NULL;
				try {
					if ($curl = curl_init()) {
						if (curl_setopt_array($curl, $options)) {
							if ($response = curl_exec($curl)) {
								$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
								
								curl_close($curl);
								return ($status === 200) ? TRUE : FALSE;
							} else {
								throw new Exception(curl_error($curl));
								return FALSE;
							}
						} else {
							throw new Exception(curl_error($curl));
							return FALSE;
						}
					} else {
						throw new Exception('unable to initialize cURL');
						return FALSE;
					}
				} catch (Exception $e) {
					if (is_resource($curl)) {
						curl_close($curl);
					}
					throw $e;
					return FALSE;
				}
			}
			
			return true;
		}
		
		public function disconnected($users) {
			//update disconnected users in online database
			$http = new Http;
			$config = new Config;
			$http->setAuth($config->get('API_KEY'));
			$http->curlPOST($config->get('disconnected_url'), ['users' => implode("-", $users)]);
			$response = $http->run();
			
			return ($response['status_code'] == 200);
		}
		
	}
?>