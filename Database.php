<?php
	require('Config.php');
	
	class Database {
		public $pdo;
		
		public function start() {
			$config = new Config;
			$configs = $config->get();
			$host = $configs['db_host'];
			$dbName = $configs['db_name'];
			$charset = $configs['db_charset'];
			$username = $configs['db_username'];
			$password = $configs['db_password'];
			$dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";
			
			$opt = [
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_EMULATE_PREPARES => false,
					];
					
			$this->pdo = new PDO($dsn,$username,$password,$opt);
			return $this->pdo;
		}
		
		public function close() {
			$this->pdo = null;
		}
	}
	
?>