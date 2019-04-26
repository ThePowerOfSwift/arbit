<?php
class Huobi {
	private $api = 'api.huobi.pro';
	public $api_method = '';
	public $req_method = '';
	private $apikey = '';
	private $seckey = '';
	function __construct($apik, $seck) {
	    $this->apikey = $apik;
	    $this->seckey = $seck;
		date_default_timezone_set("Etc/GMT+0");
	}
	
	function get_history_kline($symbol = '', $period='',$size=0) {
		$this->api_method = "/market/history/kline";
		$this->req_method = 'GET';
		$param = [
			'symbol' => $symbol,
			'period' => $period
		];
		if ($size) $param['size'] = $size;
		$url = $this->create_sign_url($param);
		return json_decode($this->curl($url));
	}
	
	function get_detail_merged($symbol = '') {
		$this->api_method = "/market/detail/merged";
		$this->req_method = 'GET';
		$param = [
			'symbol' => $symbol,
		];
		$url = $this->create_sign_url($param);
		return json_decode($this->curl($url));
	}
	
	function get_market_depth($symbol = '', $type = '') {
		$this->api_method = "/market/depth";
		$this->req_method = 'GET';
		$param = [
			'symbol' => $symbol,
			'type' => $type
		];
		$url = $this->create_sign_url($param);
		return json_decode($this->curl($url));
	}
	
	function get_market_trade($symbol = '') {
		$this->api_method = "/market/trade";
		$this->req_method = 'GET';
		$param = [
			'symbol' => $symbol
		];
		$url = $this->create_sign_url($param);
		return json_decode($this->curl($url));
	}
	
	function get_history_trade($symbol = '', $size = '') {
		$this->api_method = "/market/history/trade";
		$this->req_method = 'GET';
		$param = [
			'symbol' => $symbol
		];
		if ($size) $param['size'] = $size;
		$url = $this->create_sign_url($param);
		return json_decode($this->curl($url));
	}
	
	function get_market_detail($symbol = '') {
		$this->api_method = "/market/detail";
		$this->req_method = 'GET';
		$param = [
			'symbol' => $symbol
		];
		$url = $this->create_sign_url($param);
		return json_decode($this->curl($url));
	}
	
	function get_common_symbols() {
		$this->api_method = '/v1/common/symbols';
		$this->req_method = 'GET';
		$url = $this->create_sign_url([]);
		return json_decode($this->curl($url));
	}
	
	function get_common_currencys() {
		$this->api_method = "/v1/common/currencys";
		$this->req_method = 'GET';
		$url = $this->create_sign_url([]);
		return json_decode($this->curl($url));
	}
	
	function get_common_timestamp() {
		$this->api_method = "/v1/common/timestamp";
		$this->req_method = 'GET';
		$url = $this->create_sign_url([]);
		return json_decode($this->curl($url));
	}
	
	function get_account_accounts() {
		$this->api_method = "/v1/account/accounts";
		$this->req_method = 'GET';
		$url = $this->create_sign_url([]);
		return json_decode($this->curl($url));
	}
	
	function get_account_balance($acc_id) {
		$this->api_method = "/v1/account/accounts/".$acc_id."/balance";
		$this->req_method = 'GET';
		$url = $this->create_sign_url([]);
		return json_decode($this->curl($url));
	}
	
	function place_order($account_id=0,$amount=0,$price=null,$symbol='',$type='') {
		$source = 'api';
		$this->api_method = "/v1/order/orders/place";
		$this->req_method = 'POST';
	
		$postdata = [
			'account-id' => $account_id,
			'amount' => $amount,
			'source' => $source,
			'symbol' => $symbol,
			'type' => $type
		];
		if ($price) {
			$postdata['price'] = $price;
		}
		$url = $this->create_sign_url();
		$return = $this->curl($url,$postdata);
		return json_decode($return);
	}
	
	function cancel_order($order_id) {
		$source = 'api';
		$this->api_method = '/v1/order/orders/'.$order_id.'/submitcancel';
		$this->req_method = 'POST';
		$postdata = [];
		$url = $this->create_sign_url();
		$return = $this->curl($url,$postdata);
		return json_decode($return);
	}
	
	function cancel_orders($order_ids = []) {
		$source = 'api';
		$this->api_method = '/v1/order/orders/batchcancel';
		$this->req_method = 'POST';
		$postdata = [
			'order-ids' => $order_ids
		];
		$url = $this->create_sign_url();
		$return = $this->curl($url,$postdata);
		return json_decode($return);
	}
	
	function get_order($order_id) {
		$this->api_method = '/v1/order/orders/'.$order_id;
		$this->req_method = 'GET';
		$url = $this->create_sign_url();
		$return = $this->curl($url);
		return json_decode($return);
	}
	
	function get_order_matchresults($order_id = 0) {
		$this->api_method = '/v1/order/orders/'.$order_id.'/matchresults';
		$this->req_method = 'GET';
		$url = $this->create_sign_url();
		$return = $this->curl($url,$postdata);
		return json_decode($return);
	}
	
	function get_order_orders($symbol = '', $types = '',$start_date = '',$end_date = '',$states = '',$from = '',$direct='',$size = '') {
		$this->api_method = '/v1/order/orders';
		$this->req_method = 'GET';
		$postdata = [
			'symbol' => $symbol,
			'states' => $states
		];
		if ($types) $postdata['types'] = $types;
		if ($start_date) $postdata['start-date'] = $start_date;
		if ($end_date) $postdata['end-date'] = $end_date;
		if ($from) $postdata['from'] = $from;
		if ($direct) $postdata['direct'] = $direct;
		if ($size) $postdata['size'] = $size;
		$url = $this->create_sign_url($postdata);
		$return = $this->curl($url);
		return json_decode($return);
	}
	
	function get_orders_matchresults($symbol = '', $types = '',$start_date = '',$end_date = '',$from = '',$direct='',$size = '') {
		$this->api_method = '/v1/order/matchresults';
		$this->req_method = 'GET';
		$postdata = [
			'symbol' => $symbol
		];
		if ($types) $postdata['types'] = $types;
		if ($start_date) $postdata['start-date'] = $start_date;
		if ($end_date) $postdata['end-date'] = $end_date;
		if ($from) $postdata['from'] = $from;
		if ($direct) $postdata['direct'] = $direct;
		if ($size) $postdata['size'] = $size;
		$url = $this->create_sign_url();
		$return = $this->curl($url,$postdata);
		return json_decode($return);
	}
	
	function get_balance($account_id=ACCOUNT_ID) {
		$this->api_method = "/v1/account/accounts/{$account_id}/balance";
		$this->req_method = 'GET';
		$url = $this->create_sign_url();
		$return = $this->curl($url);
		$result = json_decode($return);
		return $result;
	}
	
	function dw_transfer_in($symbol = '',$currency='',$amount='') {
		$this->api_method = "/v1/dw/transfer-in/margin";
		$this->req_method = 'POST';
		$postdata = [
			'symbol	' => $symbol,
			'currency' => $currency,
			'amount' => $amount
		];
		$url = $this->create_sign_url($postdata);
		$return = $this->curl($url);
		$result = json_decode($return);
		return $result;
	}
	
	function dw_transfer_out($symbol = '',$currency='',$amount='') {
		$this->api_method = "/v1/dw/transfer-out/margin";
		$this->req_method = 'POST';
		$postdata = [
			'symbol	' => $symbol,
			'currency' => $currency,
			'amount' => $amount
		];
		$url = $this->create_sign_url($postdata);
		$return = $this->curl($url);
		$result = json_decode($return);
		return $result;
	}
	
	function margin_orders($symbol = '',$currency='',$amount='') {
		$this->api_method = "/v1/margin/orders";
		$this->req_method = 'POST';
		$postdata = [
			'symbol	' => $symbol,
			'currency' => $currency,
			'amount' => $amount
		];
		$url = $this->create_sign_url($postdata);
		$return = $this->curl($url);
		$result = json_decode($return);
		return $result;
	}
	
	function repay_margin_orders($order_id='',$amount='') {
		$this->api_method = "/v1/margin/orders/{$order_id}/repay";
		$this->req_method = 'POST';
		$postdata = [
			'amount' => $amount
		];
		$url = $this->create_sign_url($postdata);
		$return = $this->curl($url);
		$result = json_decode($return);
		return $result;
	}
	
	function get_loan_orders($symbol='',$currency='',$start_date,$end_date,$states,$from,$direct,$size) {
		$this->api_method = "/v1/margin/loan-orders";
		$this->req_method = 'GET';
		$postdata = [
			'symbol' => $symbol,
			'currency' => $currency,
			'states' => $states
		];
		if ($currency) $postdata['currency'] = $currency;
		if ($start_date) $postdata['start-date'] = $start_date;
		if ($end_date) $postdata['end-date'] = $end_date;
		if ($from) $postdata['from'] = $from;
		if ($direct) $postdata['direct'] = $direct;
		if ($size) $postdata['size'] = $size;
		$url = $this->create_sign_url($postdata);
		$return = $this->curl($url);
		$result = json_decode($return);
		return $result;
	}
	
	function margin_balance($symbol='') {
		$this->api_method = "/v1/margin/accounts/balance";
		$this->req_method = 'POST';
		$postdata = [
		];
		if ($symbol) $postdata['symbol'] = $symbol;
		$url = $this->create_sign_url($postdata);
		$return = $this->curl($url);
		$result = json_decode($return);
		return $result;
	}

	function withdraw_create($address='',$amount='',$currency='',$fee=null,$addr_tag=null) {
		$this->api_method = "/v1/dw/withdraw/api/create";
		$this->req_method = 'POST';
		$postdata = [
			'address' => $address,
			'amount' => $amount,
			'currency' => $currency
		];
		if ($fee) $postdata['fee'] = $fee;
		if ($addr_tag) $postdata['addr-tag'] = $addr_tag;
		$url = $this->create_sign_url($postdata);
		$return = $this->curl($url);
		$result = json_decode($return);
		return $result;
	}
	
	function withdraw_cancel($withdraw_id='') {
		$this->api_method = "/v1/dw/withdraw-virtual/{$withdraw_id}/cancel";
		$this->req_method = 'POST';
		$url = $this->create_sign_url();
		$return = $this->curl($url);
		$result = json_decode($return);
		return $result;
	}
	
	function create_sign_url($append_param = []) {
		$param = [
			'AccessKeyId' => $this->apikey,
			'SignatureMethod' => 'HmacSHA256',
			'SignatureVersion' => 2,
			'Timestamp' => date('Y-m-d\TH:i:s', time())
		];
		if ($append_param) {
			foreach($append_param as $k=>$ap) {
				$param[$k] = $ap; 
			}
		}
		return 'https://'.$this->api.$this->api_method.'?'.$this->bind_param($param);
	}
	
	function bind_param($param) {
		$u = [];
		$sort_rank = [];
		foreach($param as $k=>$v) {
			$u[] = $k."=".urlencode($v);
			$sort_rank[] = ord($k);
		}
		asort($u);
		$u[] = "Signature=".urlencode($this->create_sig($u));
		return implode('&', $u);
	}
	
	function create_sig($param) {
		$sign_param_1 = $this->req_method."\n".$this->api."\n".$this->api_method."\n".implode('&', $param);
		$signature = hash_hmac('sha256', $sign_param_1, $this->seckey, true);
		return base64_encode($signature);
	}
	function curl($url,$postdata=[]) {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		if ($this->req_method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
		}
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_TIMEOUT,60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
		curl_setopt ($ch, CURLOPT_HTTPHEADER, [
			"Content-Type: application/json",
			]);
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		return $output;
	}
}
?>