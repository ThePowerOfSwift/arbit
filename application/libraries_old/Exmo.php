<?php

class Exmo
{
    private $baseUrl;
	private $apiKey;
	private $apiSecret;
	
	public function __construct ($apiKey, $apiSecret){
		$this->apiKey    = $apiKey;
		$this->apiSecret = $apiSecret;
		$this->baseUrl   = 'http://api.exmo.com/v1/';
	}

	private function call ($method, array $req = array()){
	    $url  = $this->baseUrl.$method;
        $mt = explode(' ', microtime());
        $NONCE = $mt[1] . substr($mt[0], 2, 6);
    
        $req['nonce'] = $NONCE;
    
        // generate the POST data string
        $post_data = http_build_query($req, '', '&');
        $sign = hash_hmac('sha512', $post_data, $this->apiSecret);
    
        // generate the extra headers
        $headers = array(
            'Sign: ' . $sign,
            'Key: ' . $this->apiKey,
        );
         
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

	
	public function getPrice(){
	    return $this->call ('ticker');
	}
	
	public function getBalance(){
	    return $this->call ('user_info');
	}
	
	public function getDepositAddress(){
	    return $this->call ('deposit_address');
	}
	
	public function place_order ($type, $symbol, $quantity){
		$params = array(
            'pair'=> $symbol,
            'quantity'=> $quantity,
            'price' => 0,
            'type' => $type
        );
        return $this->call ('order_create', $params);
	}
	
	public function withdraw ($currency, $amount, $address, $invoice = null){
		$params = array(
            'amount'=> $amount,
            'currency'=> $currency,
            'address'=> $address
        );
        if($invoice){$params['invoice'] = $invoice;}
        
		return $this->call ('withdraw_crypt', $params);
	}
	
}