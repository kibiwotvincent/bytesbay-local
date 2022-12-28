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
						  CURLOPT_TIMEOUT => 20,
						  CURLOPT_HTTPHEADER => $this->headers,
						  CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
						  //for debug only
						  CURLOPT_SSL_VERIFYHOST => false,
						  CURLOPT_SSL_VERIFYPEER => false,
						];
			
			$this->options = $options;
		}
		
		public function curlPOST($url, $params = []) {
			// set default options
			$options = [
						  CURLOPT_URL => $url,
						  CURLOPT_RETURNTRANSFER => 1,
						  CURLOPT_TIMEOUT => 20,
						  CURLOPT_HTTPHEADER => $this->headers,
						  CURLOPT_POST => 1,
						  CURLOPT_POSTFIELDS => $params,
						  CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
						  //for debug only
						  CURLOPT_SSL_VERIFYHOST => false,
						  CURLOPT_SSL_VERIFYPEER => false,
						];
			
			$this->options = $options;
		}
	
		public function run() {
			$requestResponse['response'] = json_encode([]);
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
							$requestResponse['status'] = false;
						}
					} else {
						throw new Exception(curl_error($curl));
						$requestResponse['status'] = false;
					}
				} else {
					throw new Exception('unable to initialize cURL');
					$requestResponse['status'] = false;
				}
			} catch (Exception $e) {
				if (is_resource($curl)) {
					curl_close($curl);
				}
				throw $e;
				$requestResponse['status'] = false;
			}
		
			return $requestResponse;
		}
	}
?>