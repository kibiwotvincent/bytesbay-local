<?php
class Config {
	public $configs = [
				'db_host' => "localhost",
				'db_username' => "XXXX",
				'db_password' => "XXXX",
				'db_name' => "XXXX",
				'db_charset' => 'utf8',
				'sync_user_url' => "https://bytesbay.naet-tech.com/api/sync-user",
				'disconnect_users_url' => "http://www.bytesbay.com/api/disconnects",
				'disconnected_url' => "http://www.bytesbay.com/api/disconnected",
				'API_KEY' => "XXXX",
				'hotspot_disconnect_url' => "http://192.168.0.1/disconnect_user",
				'captivezone_name' => "XXXX"
				];
	
	public function get($config = null) {
		return $config == null ? $this->configs : $this->configs[$config];
	}
}
?>