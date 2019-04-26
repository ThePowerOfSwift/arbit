<?php

class hitBtc
{
	private $baseUrl;
	private $apiVersion = '2';
	private $apiKey;
	private $apiSecret;
	public function __construct ($apiKey, $apiSecret)
	{
		$this->apiKey    = $apiKey;
		$this->apiSecret = $apiSecret;
		$this->baseUrl   = 'https://api.hitbtc.com/api/'.$this->apiVersion.'/';
	}

	private function call ($method, $params=null, $request='GET')
	{
		$uri  = $this->baseUrl.$method;
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
        
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ":" . $this->apiSecret);
        
        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        return $result;
        curl_close ($ch);

	}
	
	public function getPrice($symbol){
	    return $this->call ('public/ticker/'.$symbol);
	}
	
	public function switch_amount($surrency, $amount, $type){
	    $params = "currency=".$surrency."&amount=".$amount."&type=".$type;
	    return $this->call ('account/transfer', $params, "POST");
	}
	
	public function place_order ($symbol, $side, $quantity)
	{
		$params = "symbol=".$symbol."&side=".$side."&type=market&quantity=".$quantity;
		return $this->call ('order', $params, "POST");
	}
	
	public function place_order_tradingPro ($symbol, $side, $quantity, $type, $price = null)
	{
	    if($type == 'market'){
	        $params = "symbol=".$symbol."&side=".$side."&type=".$type."&quantity=".$quantity;
	    }else{
	        $params = "symbol=".$symbol."&side=".$side."&type=".$type."&price=".$price."&quantity=".$quantity;
	    }
		
		return $this->call ('order', $params, "POST");
	}
	
	public function getBalance ()
	{
		return $this->call ('account/balance');
	}
	
	public function getTradingBalance ()
	{
		return $this->call ('trading/balance');
	}
	
	public function getDepositAddress ($currency)
	{
		return $this->call ('account/crypto/address/'.$currency);
	}
	
	public function withdraw ($currency, $amount, $address, $tag = null)
	{
		$params = "currency=".$currency."&amount=".$amount."&address=".$address;
		if($tag){ $params = $params."&paymentId=".$tag; }
		return $this->call ('account/crypto/withdraw', $params, "POST");
		
	}
	
	public function cancel_order ($orderId)
	{
		return $this->call ('order/'.$orderId, '', 'DELETE');
	}
	
	public function get_open_order (){
		//$params = "symbol=".$symbol;
		return $this->call ('order');
	}
	
	public function get_history ()
	{
		return $this->call ('history/trades');
	}
	
}
?>