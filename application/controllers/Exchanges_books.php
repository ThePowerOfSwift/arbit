<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exchanges_books extends MY_Controller {
public function index(){
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $exchange = $request->exchange;
    $pair = $request->pair;
    //$exchange = $this->input->post('exchange');
    //$pair = $this->input->post('pair');
    if($exchange != ''){
    	$get_exchange = strtolower($exchange);
    	if($pair != ''){
        	$fun_name = $get_exchange.'_book';
        	$data = $this->$fun_name($pair);
        	print_r(json_encode($data));
    	}else{
    	    echo json_encode(array('error'=>'1', 'msg'=>'Please select pair'));
    	}
    }else{
        echo json_encode(array('error'=>'1', 'msg'=>'Please select exchange'));
    }
}

private function coinbasepro_book($pair){
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('coinbasepro_book'.$pair)){
    	if(!$json_data = file_get_contents('https://api.pro.coinbase.com/products/'.$pair.'/book?level=2')){
    		return array('error'=>1, 'msg'=>'Invalid Pair');
    	}
    	$json_data = json_decode($json_data);
    	$bids = array();
    	$asks = array();
    	foreach($json_data->bids as $bid){
    		$bids[] = array('price'=>$bid[0], 'qty'=>$bid[1]);
    	}
    	foreach($json_data->asks as $ask){
    		$asks[] = array('price'=>$ask[0], 'qty'=>$ask[1]);
    	}
    	$ticker = json_decode(file_get_contents('https://api.pro.coinbase.com/products/'.$pair.'/ticker'));
    	if(!isset($ticker->price)){$this->coinbasepro_book($pair);}
    	$final = array(
    		'success'=>1,
    		'bids'=>$bids,
    		'asks'=>$asks,
    		'last_price'=> $ticker->price
    	);
		$this->cache->save('coinbasepro_book'.$pair, $final, 15);
    }
	return $final;
}


private function bibox_book($pair){
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('bibox_book'.$pair)){
    	if($pair == ''){return array('error'=>1, 'msg'=>'Please select a pair');}
    	$json_data = json_decode(file_get_contents('https://api.bibox.com/v1/mdata?cmd=depth&pair='.$pair.'&size=100'));
    	
    	if(isset($json_data->error)){
    		return array('error'=>1, 'msg'=>'Invalid Pair');
    	}
    	$json_data = $json_data->result;
    	$bids = array();
    	$asks = array();
    	foreach($json_data->bids as $bid){
    		$bids[] = array('price'=>$bid->price.'', 'qty'=>$bid->volume.'');
    	}
    	foreach($json_data->asks as $ask){
    		$asks[] = array('price'=>$ask->price.'', 'qty'=>$ask->volume.'');
    	}
    	$ticker = json_decode(file_get_contents('https://api.bibox.com/v1/mdata?cmd=ticker&pair='.$pair));
    	$final = array(
    		'success'=>1,
    		'bids'=>$bids,
    		'asks'=>$asks,
    		'last_price'=>$ticker->result->last
    	);
		$this->cache->save('bibox_book'.$pair, $final, 15);
    }

	return $final;
}


private function coinbene_book($pair){
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('coinbene_book'.$pair)){
    	if($pair == ''){return array('error'=>1, 'msg'=>'Please select a pair');}
    	$json_data = json_decode(file_get_contents('http://api.coinbene.com/v1/market/orderbook?symbol='.$pair.'&depth=100'));
    	
    	if(isset($json_data->status) && $json_data->status=='error'){
    		return array('error'=>1, 'msg'=>'Invalid Pair');
    	}
    	$json_data = $json_data->orderbook;
    	$bids = array();
    	$asks = array();
    	foreach($json_data->bids as $bid){
    		$bids[] = array('price'=>$bid->price.'', 'qty'=>$bid->quantity.'');
    	}
    	foreach($json_data->asks as $ask){
    		$asks[] = array('price'=>$ask->price.'', 'qty'=>$ask->quantity.'');
    	}
    	$ticker = json_decode(file_get_contents('http://api.coinbene.com/v1/market/ticker?symbol='.$pair));
    	$final = array(
    		'success'=>1,
    		'bids'=>$bids,
    		'asks'=>$asks,
    		'last_price'=>$ticker->ticker[0]->last
    		
    	);
		$this->cache->save('coinbene_book'.$pair, $final, 15);
    }

	return $final;
}


private function exmo_book($pair){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('exmo_book'.$pair)){
    	if($pair == ''){return array('error'=>1, 'msg'=>'Please select a pair');}
    	$json_data = json_decode(file_get_contents('https://api.exmo.com/v1/order_book/?pair='.$pair));
    	if(!isset($json_data->$pair)){
    		return array('error'=>1, 'msg'=>'Invalid Pair');
    	}
    	$json_data = $json_data->$pair;
    	$bids = array();
    	$asks = array();
    	foreach($json_data->bid as $bid){
    		$bids[] = array('price'=>$bid[0].'', 'qty'=>$bid[1].'');
    	}
    	foreach($json_data->ask as $ask){
    		$asks[] = array('price'=>$ask[0].'', 'qty'=>$ask[1].'');
    	}
    	$ticker = json_decode(file_get_contents('https://api.exmo.com/v1/ticker/'));
    	$final = array(
    		'success'=>1,
    		'bids'=>$bids,
    		'asks'=>$asks,
    		'last_price'=>$ticker->$pair->last_trade
    	);
		$this->cache->save('exmo_book'.$pair, $final, 15);
    }
	return $final;
}


private function livecoin_book($pair){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('livecoin_book'.$pair)){
    	if($pair == ''){return array('error'=>1, 'msg'=>'Please select a pair');}
    	$json_data = json_decode(file_get_contents('https://api.livecoin.net/exchange/order_book?currencyPair='.$pair.'&depth=100'));
    	if(isset($json_data->errorCode)){
    		return array('error'=>1, 'msg'=>'Invalid Pair');
    	}
    	$bids = array();
    	$asks = array();
    	foreach($json_data->bids as $bid){
    		$bids[] = array('price'=>$bid[0].'', 'qty'=>$bid[1].'');
    	}
    	foreach($json_data->asks as $ask){
    		$asks[] = array('price'=>$ask[0].'', 'qty'=>$ask[1].'');
    	}
    	$ticker = json_decode(file_get_contents('https://api.livecoin.net/exchange/ticker?currencyPair='.$pair));
    	$final = array(
    		'success'=>1,
    		'bids'=>$bids,
    		'asks'=>$asks,
    		'last_price' => $ticker->last
    	);
		$this->cache->save('livecoin_book'.$pair, $final, 15);
    }
	return $final;
}

private function poloniex_book($pair){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('poloniex_book'.$pair)){
    	if($pair == ''){return array('error'=>1, 'msg'=>'Please select a pair');}
    	$json_data = json_decode(file_get_contents('https://poloniex.com/public?command=returnOrderBook&currencyPair='.$pair.'&depth=100'));
    	if(isset($json_data->error)){
    		return array('error'=>1, 'msg'=>'Invalid Pair');
    	}
    	$bids = array();
    	$asks = array();
    	foreach($json_data->bids as $bid){
    		$bids[] = array('price'=>$bid[0].'', 'qty'=>$bid[1].'');
    	}
    	foreach($json_data->asks as $ask){
    		$asks[] = array('price'=>$ask[0].'', 'qty'=>$ask[1].'');
    	}
    	$ticker = json_decode(file_get_contents('https://poloniex.com/public?command=returnTicker'));
    	$final = array(
    		'success'=>1,
    		'bids'=>$bids,
    		'asks'=>$asks,
    		'last_price'=>$ticker->$pair->last
    	);
		$this->cache->save('poloniex_book'.$pair, $final, 15);
    }
	return $final;
}


private function bittrex_book($pair){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('bittrex_book'.$pair)){
    	if($pair == ''){return array('error'=>1, 'msg'=>'Please select a pair');}
    	$json_data = json_decode(file_get_contents('https://api.bittrex.com/api/v1.1/public/getorderbook?market='.$pair.'&type=both'));
    	if($json_data->success != 1){
    		return array('error'=>1, 'msg'=>'Invalid Pair');
    	}
    	
    	$bids = array();
    	$asks = array();
    	foreach($json_data->result->buy as $bid){
    		$bids[] = array('price'=>$bid->Rate.'', 'qty'=>$bid->Quantity.'');
    	}
    	foreach($json_data->result->sell as $ask){
    		$asks[] = array('price'=>$ask->Rate.'', 'qty'=>$ask->Quantity.'');
    	}
    	$ticker = json_decode(file_get_contents('https://api.bittrex.com/api/v1.1/public/getticker?market='.$pair));
    	$final = array(
    		'success'=>1,
    		'bids'=>$bids,
    		'asks'=>$asks,
    		'last_price'=>$ticker->result->Last
    	);
		$this->cache->save('bittrex_book'.$pair, $final, 15);
    }
	return $final;
}


private function huobi_book($pair){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('huobi_book'.$pair)){
    	if($pair == ''){return array('error'=>1, 'msg'=>'Please select a pair');}
    	$json_data = json_decode(file_get_contents('https://api.huobi.pro/market/depth?symbol='.$pair.'&type=step0'));
    	if($json_data->status == 'error'){
    		return array('error'=>1, 'msg'=>'Invalid Pair');
    	}
    	
    	$bids = array();
    	$asks = array();
    	foreach($json_data->tick->bids as $key => $bid){
    		if($key>99){break;}
    		$bids[] = array('price'=>$bid[0].'', 'qty'=>$bid[1].'');
    	}
    	foreach($json_data->tick->asks as $key => $ask){
    		if($key>99){break;}
    		$asks[] = array('price'=>$ask[0].'', 'qty'=>$ask[1].'');
    	}
    	$ticker = json_decode(file_get_contents('https://api.huobi.pro/market/trade?symbol='.$pair));
    	$final = array(
    		'success'=>1,
    		'bids'=>$bids,
    		'asks'=>$asks,
    		'last_price'=>$ticker->tick->data[0]->price
    	);
		$this->cache->save('huobi_book'.$pair, $final, 15);
    }
	return $final;
}


private function hitbtc_book($pair){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('hitbtc_book'.$pair)){
    	if($pair == ''){return array('error'=>1, 'msg'=>'Please select a pair');}
    	
    	if(!$json_data = json_decode(file_get_contents('https://api.hitbtc.com/api/2/public/orderbook/'.$pair))){
    		return array('error'=>1, 'msg'=>'Invalid Pair');
    	}
    	$bids = array();
    	$asks = array();
    	foreach($json_data->bid as $bid){
    		$bids[] = array('price'=>$bid->price.'', 'qty'=>$bid->size.'');
    	}
    	foreach($json_data->ask as $ask){
    		$asks[] = array('price'=>$ask->price.'', 'qty'=>$ask->size.'');
    	}
    	$ticker = json_decode(file_get_contents('https://api.hitbtc.com/api/2/public/ticker/'.$pair));
    	$final = array(
    		'success'=>1,
    		'bids'=>$bids,
    		'asks'=>$asks,
    		'last_price'=>$ticker->last
    	);
		$this->cache->save('hitbtc_book'.$pair, $final, 15);
    }
	return $final;
}


private function kraken_book($pair){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('kraken_book'.$pair)){
    	if($pair == ''){return array('error'=>1, 'msg'=>'Please select a pair');}
    	$json_data = json_decode(file_get_contents('https://api.kraken.com/0/public/Depth?pair='.$pair));
    	if(isset($json_data->error[0])){
    		return array('error'=>1, 'msg'=>'Invalid Pair');
    	}
    	$bids = array();
    	$asks = array();
    	foreach($json_data->result->$pair->bids as $bid){
    		$bids[] = array('price'=>$bid[0].'', 'qty'=>$bid[1].'');
    	}
    	foreach($json_data->result->$pair->asks as $ask){
    		$asks[] = array('price'=>$ask[0].'', 'qty'=>$ask[1].'');
    	}
    	$ticker = json_decode(file_get_contents('https://api.kraken.com/0/public/Ticker?pair='.$pair));
        $final = array(
    		'success'=>1,
    		'bids'=>$bids,
    		'asks'=>$asks,
    		'last_price' => $ticker->result->$pair->a[0]
    	);
		$this->cache->save('kraken_book'.$pair, $final, 15);
    }	
	return $final;
}


private function binance_book($pair){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('binance_book'.$pair)){
    	if($pair == ''){return array('error'=>1, 'msg'=>'Please select a pair');}
    	if(!$json_data = json_decode(file_get_contents('https://api.binance.com/api/v1/depth?symbol='.$pair))){
    		return array('error'=>1, 'msg'=>'Invalid Pair');
    	}
    	$bids = array();
    	$asks = array();
    	foreach($json_data->bids as $bid){
    		$bids[] = array('price'=>$bid[0].'', 'qty'=>$bid[1].'');
    	}
    	foreach($json_data->asks as $ask){
    		$asks[] = array('price'=>$ask[0].'', 'qty'=>$ask[1].'');
    	}
    	//ticker 
    	$ticker = json_decode(file_get_contents('https://api.binance.com/api/v3/ticker/price?symbol='.$pair));
    	
    	$final = array(
    		'success'=>1,
    		'bids'=>$bids,
    		'asks'=>$asks,
    		'last_price' => $ticker->price
    	);
    	$this->cache->save('binance_book'.$pair, $final, 15);
    }
	return $final;
}
}