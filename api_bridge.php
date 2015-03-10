<?php
	/*
		Author: DIMH@24-11-2014
		This is the wrapper for any web app to address all api calls.
	*/
	date_default_timezone_set('America/New_York');
	class APIBridge{
		protected $username = 'your-username';
		protected $password = 'your-password';
		protected $link = '';
		protected $cookie = null;
		protected $apiurl_base = 'http://your-api-url';
		protected $login_url = 'your-login-url'; // for example: /login.php
		protected $error_message = 'Invalid request.';
		
		public function __construct(){
			if( strlen($_COOKIE['cookie_session']) > 0 ){
				$this->cookie = $_COOKIE['cookie_session'];
			}
		}
		
		public function apiCheckCookie(){
			if( !empty($this->cookie) )
				return true;
			else
				return false;
		}
        
        // Intented to authenticate yourself in the API. This function should be called at the beginning of the process.
		public function apiConnect(){
			 
			$this->link = curl_init();
			curl_setopt($this->link, CURLOPT_URL, $this->apiurl_base . $this->login_url);
			curl_setopt($this->link, CURLOPT_HEADER, true);
			curl_setopt($this->link, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->link, CURLOPT_POST, 1);
			curl_setopt($this->link, CURLOPT_POSTFIELDS, "user={$this->username}&pass={$this->password}");
			
			$response = curl_exec($this->link);
			$err     = curl_errno($this->link);
			$errmsg  = curl_error($this->link);
			$header  = curl_getinfo($this->link);
			
			$header_size = $header['header_size'];
			$content_length = $header['download_content_length'];
			$header = substr($response, 0, $header_size);
			$body = substr($response, $header_size, $content_length);
			list($v1,$v2,$v3,$v4) = explode(':',$response);
			$this->cookie = $v4;
			$this->apiSetCookie($v4);
			return json_decode($body);
			
		}// end of apiConnect
		
		// set the cookie to last 1 day.
		public function apiSetCookie( $cookie_var ){

			setcookie('cookie_session',chop($cookie_var),mktime(date('G'),date('i'),date('s'),date('m'),date('d')+1,date('Y')),'/');
		}

		public function apiCall( $endpoint, $curl_data = null, $post = false ){

			$this->link = curl_init();
			curl_setopt($this->link, CURLOPT_URL, $this->apiurl_base . $endpoint);
			curl_setopt($this->link, CURLOPT_HEADER, false);
			curl_setopt($this->link, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->link, CURLOPT_COOKIE, $this->cookie);
			
			if( $post ):
				curl_setopt($this->link, CURLOPT_POST, 1);
				curl_setopt($this->link, CURLOPT_POSTFIELDS, $curl_data);
			endif;
			
			$response = curl_exec($this->link);
			$err     = curl_errno($this->link);
			$errmsg  = curl_error($this->link);
			$header  = curl_getinfo($this->link);
			$header['errno']   = $err;
			$header['errmsg']  = $errmsg;
			$header['content'] = $response;
			$content = $this->apiParseResponse($header);
			
			return $content;
			
		}// end of apiCall
		
		public function generalCall( $endpoint ){

			$this->link = curl_init();
			//return json_encode($endpoint);
			curl_setopt($this->link, CURLOPT_URL, $endpoint);
			curl_setopt($this->link, CURLOPT_HEADER, false);
			curl_setopt($this->link, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->link, CURLOPT_FOLLOWLOCATION, true);
			$response = curl_exec($this->link);
			$err     = curl_errno($this->link);
			$errmsg  = curl_error($this->link);
			$header  = curl_getinfo($this->link);
			$header['errno']   = $err;
			$header['errmsg']  = $errmsg;
			$header['content'] = $response;
			$content = $this->apiParseResponse($header);
			
			return $content;
			
		}// end of apiCall
		
		protected function apiParseResponse( $response ){
			
			if( $response['http_code'] != 200 ){
				return json_encode(array('status'=>0,'message'=>$this->error_message));
			}
			return $response['content'];
			
		}// end fo apiParseResponse
	}
	
	// Check the existance of the expected parameters.
	function checkParams(){
		if( !array_key_exists('endpoint', $_GET) ){
			return false;
		}
		if( !array_key_exists('object_id', $_GET) ){
			return false;
		}
		if( !array_key_exists('type', $_GET) ){
			return false;
		}
		return true;
	}
	
	// Process the request that you sent through an Ajax connection. You can send data through GET/POST
	function processRequest(){

		// defaults
		$default = array('endpoint'=>'','object_id'=>'','page'=>'1','per_page'=>'10','mediatype'=>'');
		
		//first check params
		if(checkParams()){
			$apibridge = new APIBridge;
			if( $apibridge->apiCheckCookie() === FALSE ):
				$content = $apibridge->apiConnect();
			endif;
			
			$params = array_replace($default, $_GET);
			// replace the following url with your own API url.
			$response = $apibridge->apiCall( "{$params['endpoint']}/{$params['object_id']}/{$params['page']}/{$params['per_page']}/{$params['type']}" );
		}else{
			$response = json_encode(array('status'=>0,'message'=>'Invalid request.'));
		}	
		return $response;
	}
	
	$response = processRequest();
	header('application/json');
	echo $response;
?>