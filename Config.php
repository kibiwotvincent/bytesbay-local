<?php
class Config {
	public $configs = [
				'db_host' => "localhost",
				'db_username' => "veen",
				'db_password' => "admin",
				'db_name' => "radiusdb",
				'db_charset' => 'utf8',
				'sync_user_url' => "https://bytesbay.naet-tech.com/api/sync-user",
				'disconnect_users_url' => "http://www.bytesbay.com/api/disconnects",
				'disconnected_url' => "http://www.bytesbay.com/api/disconnected",
				'API_KEY' => "427|vwxOFjDjDzynYsYN8gsiZ0gqWSKehpgtV7f6q3cT",
				'hotspot_disconnect_url' => "http://192.168.1.1:8002/index.php?zone=lan",
				'portal_zone' => "lan"
				];
	
	public function get($config = null) {
		return $config == null ? $this->configs : $this->configs[$config];
	}
}
?>