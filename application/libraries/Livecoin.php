<?php

class Livecoin
{
    private $baseUrl;
	private $apiKey;
	private $apiSecret;
	
	public function __construct ($apiKey, $apiSecret){
		$this->apiKey    = $apiKey;
		$this->apiSecret = $apiSecret;
		$this->baseUrl   = 'https://api.livecoin.net/';
	}

	private function call ($method, $params=null, $request='GET'){
	    $url  = $this->baseUrl.$method;
        ksort($params);
        $postFields = http_build_query($params, '', '&');
        $signature = strtoupper(hash_hmac('sha256', $postFields, $this->apiSecret));
         
        $headers = array(
            "Api-Key: ".$this->apiKey,
            "Sign: $signature"
        );
         
        if($request == 'POST'){
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, $request);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }else{
            $ch = curl_init($url."?".$postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
         
        $result = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        return $result;
        curl_close ($ch);

	}
	
	public function getPrice($symbol){
	    $params = array(
            'currencyPair'=> $symbol
        );
	    return $this->call ('exchange/ticker', $params, 'GET');
	}
	
	public function getBalance ($currency){
	    $params = array(
            'currency'=> $currency
        );
		return $this->call ('payment/balance', $params, 'GET');
	}
	
	public function getBalances (){
		return $this->call ('payment/balances', null, 'GET');
	}
	
	public function get_close_orders($pair){
	    $params = array(
            'currencyPair'=> $pair
            );
		return $this->call ('exchange/trades', $params, 'GET');
	}
	
	public function get_open_orders(){
	    $params = array(
            'openClosed'=> 'OPEN'
            );
		return $this->call ('exchange/client_orders', $params, 'GET');
	}
	
	public function getDepositAddress ($currency){
	    $params = array(
            'currency'=> $currency
        );
		return $this->call ('payment/get/address', $params, 'GET');
	}
	
	public function cancel_order ($pair, $order_id){
	    $params = array(
            'currencyPair'=> $pair,
            'orderId' => $order_id
        );
		return $this->call ('exchange/cancellimit', $params, 'POST');
	}
	
	public function place_order ($type, $symbol, $quantity){
		$params = array(
            'currencyPair'=> $symbol,
            'quantity'=> $quantity
        );
        if($type == 'sell'){
		    return $this->call ('exchange/sellmarket', $params, "POST");
        }else{
            return $this->call ('exchange/buymarket', $params, "POST");
        }
	}
	
	public function withdraw ($currency, $amount, $address){
		$params = array(
            'amount'=> $amount,
            'currency'=> $currency,
            'wallet'=> $address
        );
		return $this->call ('payment/out/coin', $params, 'POST');
	}
	
}