<?php

class coinbasepro{
    private $baseUrl = 'https://api.pro.coinbase.com/';
	private $apiKey;
	private $apiSecret;
	private $passphrase;
    
    public function __construct ($apiKey, $apiSecret, $passphrase){
		$this->apiKey    = $apiKey;
		$this->apiSecret = $apiSecret;
		$this->passphrase = $passphrase;
		
	}
	
	private function signature($request_path='', $body='', $timestamp=false, $method='GET'){
        $body = is_array($body) ? json_encode($body) : $body;
        $timestamp = $timestamp ? $timestamp : time();

        $what = $timestamp.$method.$request_path.$body;

        return base64_encode(hash_hmac("sha256", $what, base64_decode($this->secret), true));
    }
    
    
    private function call ($function, $req = array(), $method='GET'){
	    $url  = $this->baseUrl.$function;
	    $timestamp = time();
	    $headers = array(
            'CB-ACCESS-KEY: ' . $sign,
            'CB-ACCESS-SIGN: ' . $this->signature($function, $req, $timestamp, $method),
            'CB-ACCESS-TIMESTAMP:' . $timestamp,
            'CB-ACCESS-PASSPHRASE:' . $this->passphrase
        );
        $post_data = http_build_query($req, '', '&');
        // our curl handle (initialize if required)
        static $ch = null;
        if (is_null($ch)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; PHP client; ' . php_uname('s') . '; PHP/' . phpversion() . ')');
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    
        // run the query
        $res = curl_exec($ch);
        if ($res === false) throw new Exception('Could not get reply: ' . curl_error($ch));
       
        $dec = json_decode($res, true);
        if ($dec === null)
            throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
    
        return $dec;
        curl_close ($ch);

	}

    public function getBalance(){
	    return $this->call ('accounts');
	}
    
    
}