<?php
class Config {
	public $configs = [
				'db_host' => "localhost",
				'db_username' => "XXXX",
				'db_password' => "XXXX",
				'db_name' => "XXXX",
				'db_charset' => 'utf8',
				'disconnect_users_url' => "https://bytesbay.naet-tech.com/api/disconnects",
				'disconnected_url' => "https://bytesbay.naet-tech.com/api/disconnected",
				'API_KEY' => "XXXX",
				'hotspot_disconnect_url' => "http://192.168.1.1:8002/disconnect-user.php",
				'portal_zone' => "XXXX"
				];
	
	public function get($config = null) {
		return $config == null ? $this->configs : $this->configs[$config];
	}
}
?>