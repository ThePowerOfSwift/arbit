<?php

class Coinbene
{
    private $baseUrl;
	private $apiKey;
	private $apiSecret;
	
	public function __construct ($apiKey, $apiSecret){
		$this->apiKey    = $apiKey;
		$this->apiSecret = $apiSecret;
		$this->baseUrl   = 'http://api.coinbene.com/';
	}
	
	private function call ($method, $params=null, $request='GET'){
	    $paramsForget= $params;
	    $url  = $this->baseUrl.$method;
        $postFields = $this->build_params($params);
        $headers = array(
            "Content-Type: application/json;charset=utf-8",
            "Connection: keep-alive"
        );
       
        
        if($request == 'POST'){
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, $request);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data_string = json_encode($postFields);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }else{
            $post_data = http_build_query($paramsForget, '', '&');
            $ch = curl_init($url."?".$post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
         
        $result = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);
        return json_decode($result);
	}
	
	
    private function build_params($data){
        $data['apiid'] = $this->apiKey;
        $data['secret'] = $this->apiSecret;
        $data['timestamp'] = $this->get_millisecond();
        ksort($data);
        $data['sign'] = $this->create_sig(http_build_query($data));
        unset($data['secret']);
        return $data;
    }
    // Generate signature
    // 生成签名
    private function create_sig($param) {
        $signature = md5(strtoupper($param));
        return $signature;
    }
    private function get_millisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
    
    
    
    
    public function balance($account = 'exchange'){
        $data = array(
            'account' => $account,
        );
        $res = $this->call('v1/trade/balance', $data, 'POST');
        return $res;
    }

    public function cancel($orderid){
        $data = array(
            'orderid' => $orderid,
        );
        $orders = $this->call('v1/trade/order/cancel', $data, 'POST');
        return $orders;
    }

	
	public function get_open_orders($pair){
        $data = [
            'symbol' => $pair,
        ];
        $orders = $this->call('v1/trade/order/open-orders', $data, 'POST');
        return $orders;
    }
    
    public function place_order($symbol, $type, $price, $quantity){
        $data = array(
            'symbol' => $symbol,
            'type' => $type,
            'price' => $price,
            'quantity' => $quantity,
        );
        $orders = $this->call('v1/trade/order/place', $data, 'POST');
        return $orders;
    }
}