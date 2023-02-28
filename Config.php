<?php
class Config {
	public $configs = [
				'db_host' => "localhost",
				'db_username' => "veen",
				'db_password' => "admin",
				'db_name' => "radiusdb",
				'db_charset' => 'utf8',
				'get_subscribed_users_url' => "http://www.bytesbay.com/api/active-users", /*fetch online users with active subscription*/
				'get_connected_users_url' => "http://localhost/captiveportal/users.php", /*fetch connected pfsense users*/
				'hotspot_disconnect_url' => "http://localhost/captiveportal/disconnect-user.php",
				'API_KEY' => "628|lQNxx7fZIpu2hyoF8rJTeG7vnsqS5Z4p09Es1G8o",
				'zone' => "lan"
				];
	
	public function get($config = null) {
		return $config == null ? $this->configs : $this->configs[$config];
	}
}
?>