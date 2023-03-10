<?php
	class Http {
		
		public $options;
		public $headers = [];
		
		public function __construct() {
			array_push($this->headers, "Accept: application/json");
		}
		
		public function setAuth($APIAuthToken) {
			array_push($this->headers, "Authorization: Bearer ".$APIAuthToken);
		}
		
		public function curlGET($url) {
			// set default options
			$options = [
						  CURLOPT_URL => $url,
						  CURLOPT_RETURNTRANSFER => 1,
						  CURLOPT_TIMEOUT => 5,
						  CURLOPT_HTTPHEADER => $this->headers
						];
			
			$this->options = $options;
		}
		
		public function curlPOST($url, $params = []) {
			// set default options
			$options = [
						  CURLOPT_URL => $url,
						  CURLOPT_RETURNTRANSFER => 1,
						  CURLOPT_TIMEOUT => 5,
						  CURLOPT_HTTPHEADER => $this->headers,
						  CURLOPT_POST => 1,
						  CURLOPT_POSTFIELDS => $params,
						];
			
			$this->options = $options;
		}
	
		public function run() {
			$requestResponse['response'] = "";;
			$requestResponse['status_code'] = 0;
			$requestResponse['status'] = false;
			
			try {
				if ($curl = curl_init()) {
					if (curl_setopt_array($curl, $this->options)) {
						if ($response = curl_exec($curl)) {
							$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
							
							$requestResponse['status_code'] = $httpCode;
							$requestResponse['response'] = $response;
							
							if($httpCode === 200) {
								$requestResponse['status'] = true;
							}
							curl_close($curl);
						} else {
							throw new Exception(curl_error($curl));
						}
					} else {
						throw new Exception(curl_error($curl));
					}
				} else {
					throw new Exception('unable to initialize cURL');
				}
			} 
			finally {
				if (is_resource($curl)) {
					curl_close($curl);
				}
			}
		
			return $requestResponse;
		}
	}
?>