<?php
class Config {
	public $configs = [
				'db_host' => "localhost",
				'db_username' => "veen",
				'db_password' => "admin",
				'db_name' => "radiusdb",
				'db_charset' => 'utf8',
				'disconnect_users_url' => "http://www.bytesbay.com/api/disconnects",
				'disconnected_url' => "http://www.bytesbay.com/api/disconnected",
				'hotspot_disconnect_url' => "http://localhost/captiveportal/disconnect-user.php",
				'API_KEY' => "427|vwxOFjDjDzynYsYN8gsiZ0gqWSKehpgtV7f6q3cT",
				'portal_zone' => "lan"
				];
	
	public function get($config = null) {
		return $config == null ? $this->configs : $this->configs[$config];
	}
}
?>