<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exchanges_pairs extends MY_Controller {

public function index(){
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $exchange = $request->exchange;
    //$exchange = $this->input->post('exchange');
    if($exchange != ''){
    	$get_exchange = strtolower($exchange);
    	$fun_name = $get_exchange.'_pair_list';
    	$data = $this->$fun_name();
    	print_r(json_encode($data));
    }else{
        echo json_encode(array('error'=>'1', 'msg'=>'Please select exchange'));
    }
}


private function coinbasepro_pair_list(){
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('coinbasepro_pairs')){
    	$json_data = json_decode(file_get_contents('https://api.pro.coinbase.com/products'));
    	$pair_array = array();
    	$marketes = array();
    	foreach($json_data as $single){
    		$arr = array(
    				'symbol' => $single->id,
    				'base' => $single->base_currency,
    				'quote' => $single->quote_currency,
    				);
    		$pair_array[] = $arr;
    		if(!in_array($single->quote_currency, $marketes)){
    			$marketes[] = $single->quote_currency;
    		}
    	}
    	$final = array(
    				'pairs' => $pair_array,
    				'markets' => $marketes
    				);
		$this->cache->save('coinbasepro_pairs', $final, 21600);
    }
	return $final;
}


private function bibox_pair_list(){
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('bibox_pairs')){
    	$json_data = json_decode(file_get_contents("https://api.bibox.com/v1/mdata?cmd=pairList"));
    	$pair_array = array();
    	$marketes = array();
    	foreach($json_data->result as $single){
    		$pair = explode('_', $single->pair);
    		$arr = array(
    				'symbol' => $single->pair,
    				'base' => $pair[0],
    				'quote' => $pair[1],
    				);
    		$pair_array[] = $arr;
    		if(!in_array($pair[1], $marketes)){
    			$marketes[] = $pair[1];
    		}
    		
    	}
    	$final = array(
    				'pairs' => $pair_array,
    				'markets' => $marketes
    				);
		$this->cache->save('bibox_pairs', $final, 21600);
    }
	return $final;
}



private function coinbene_pair_list(){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('coinbene_pairs')){
    	$json_data = json_decode(file_get_contents("https://api.coinbene.com/v1/market/symbol"));
    	$pair_array = array();
    	$marketes = array();
    	foreach($json_data->symbol as $single){
    		$arr = array(
    				'symbol' => $single->ticker,
    				'base' => $single->baseAsset,
    				'quote' => $single->quoteAsset,
    				);
    		$pair_array[] = $arr;
    		if(!in_array($single->quoteAsset, $marketes)){
    			$marketes[] = $single->quoteAsset;
    		}
    		
    	}
    	$final = array(
    				'pairs' => $pair_array,
    				'markets' => $marketes
    				);
		$this->cache->save('coinbene_pairs', $final, 21600);
    }
	return $final;
}


private function exmo_pair_list(){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('exmo_pairs')){
    	$json_data = json_decode(file_get_contents("https://api.exmo.com/v1/ticker/"));
    	$pair_array = array();
    	$marketes = array();
    	foreach($json_data as $key => $single){
    		$pair = explode('_', $key);
    		$arr = array(
    				'symbol' => $key,
    				'base' => $pair[0],
    				'quote' => $pair[1],
    				);
    		$pair_array[] = $arr;
    		if(!in_array($pair[1], $marketes)){
    			$marketes[] = $pair[1];
    		}
    		
    	}
    	$final = array(
    				'pairs' => $pair_array,
    				'markets' => $marketes
    				);
		$this->cache->save('exmo_pairs', $final, 21600);
    }
	return $final;
}



private function livecoin_pair_list(){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('livecoin_pairs')){
    	$json_data = json_decode(file_get_contents("https://api.livecoin.net/exchange/ticker"));
    	$pair_array = array();
    	$marketes = array();
    	foreach($json_data as $single){
    		$pair = explode('/', $single->symbol);
    		$arr = array(
    				'symbol' => $single->symbol,
    				'base' => $pair[0],
    				'quote' => $pair[1],
    				);
    		$pair_array[] = $arr;
    		if(!in_array($pair[1], $marketes)){
    			$marketes[] = $pair[1];
    		}
    		
    	}
    	$final = array(
    				'pairs' => $pair_array,
    				'markets' => $marketes
    				);
		$this->cache->save('livecoin_pairs', $final, 21600);
    }
	return $final;
	
}



private function poloniex_pair_list(){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('poloniex_pairs')){
    	$json_data = json_decode(file_get_contents("https://poloniex.com/public?command=returnTicker"));
    	$pair_array = array();
    	$marketes = array();
    	foreach($json_data as $key => $single){
    		$pair = explode('_', $key);
    		$arr = array(
    				'symbol' => $key,
    				'base' => $pair[1],
    				'quote' => $pair[0],
    				);
    		$pair_array[] = $arr;
    		if(!in_array($pair[0], $marketes)){
    			$marketes[] = $pair[0];
    		}
    		
    	}
    	$final = array(
    				'pairs' => $pair_array,
    				'markets' => $marketes
    				);
		$this->cache->save('poloniex_pairs', $final, 21600);
    }
	return $final;
	
}



private function bittrex_pair_list(){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('bittrex_pairs')){
    	$json_data = json_decode(file_get_contents("https://api.bittrex.com/api/v1.1/public/getmarkets"));
    	$pair_array = array();
    	$marketes = array();
    	foreach($json_data->result as $single){
    		if($single->IsActive){
    			$arr = array(
    					'symbol' => $single->MarketName,
    					'base' => $single->MarketCurrency,
    					'quote' => $single->BaseCurrency,
    					);
    			$pair_array[] = $arr;
    			if(!in_array($single->BaseCurrency, $marketes)){
    				$marketes[] = $single->BaseCurrency;
    			}
    		}
    	}
    	$final = array(
    				'pairs' => $pair_array,
    				'markets' => $marketes
    				);
		$this->cache->save('bittrex_pairs', $final, 21600);
    }
	return $final;
	
}




private function huobi_pair_list(){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('huobi_pairs')){
    	$json_data = json_decode(file_get_contents("https://api.huobi.com/v1/common/symbols"));
    	$pair_array = array();
    	$marketes = array();
    	foreach($json_data->data as $single){
    		$single = json_decode(json_encode($single), true);
    		$arr = array(
    				'symbol' => $single['base-currency'].''.$single['quote-currency'],
    				'base' => $single['base-currency'],
    				'quote' => $single['quote-currency'],
    				);
    		$pair_array[] = $arr;
    		if(!in_array($single['quote-currency'], $marketes)){
    			$marketes[] = $single['quote-currency'];
    		}
    	}
    	$final = array(
    				'pairs' => $pair_array,
    				'markets' => $marketes
    				);
		$this->cache->save('huobi_pairs', $final, 21600);
    }
	return $final;
	
}



private function hitbtc_pair_list(){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('hitbtc_pairs')){
    	$json_data = json_decode(file_get_contents("https://api.hitbtc.com/api/2/public/symbol"));
    	$pair_array = array();
    	$marketes = array();
    	foreach($json_data as $single){
    		$arr = array(
    				'symbol' => $single->id,
    				'base' => $single->baseCurrency,
    				'quote' => $single->quoteCurrency,
    				);
    		$pair_array[] = $arr;
    		if(!in_array($single->quoteCurrency, $marketes)){
    			$marketes[] = $single->quoteCurrency;
    		}
    	}
    	$final = array(
    				'pairs' => $pair_array,
    				'markets' => $marketes
    				);
    	//return $final;
    	$this->cache->save('hitbtc_pairs', $final, 21600);
    }
    return $final;
	
}



private function kraken_pair_list(){
	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('kraken_pairs')){
    	$json_data = json_decode(file_get_contents("https://api.kraken.com/0/public/AssetPairs"));
    	$pair_array = array();
    	$marketes = array();
    	foreach($json_data->result as $key => $single){
    		if (strpos($key, '.d') !== false) {continue;}
    		$arr = array(
    				'symbol' => $key,
    				'base' => $single->base,
    				'quote' => $single->quote,
    				);
    		$pair_array[] = $arr;
    		if(!in_array($single->quote, $marketes)){
    			$marketes[] = $single->quote;
    		}
    	}
    	$final = array(
    				'pairs' => $pair_array,
    				'markets' => $marketes
    				);
		$this->cache->save('kraken_pairs', $final, 21600);
    }
	return $final;
	
}


private function binance_pair_list(){
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    if (!$final = $this->cache->get('binance_pairs')){
    	$json_data = json_decode(file_get_contents("https://api.binance.com/api/v1/exchangeInfo"));
    	$pair_array = array();
    	$marketes = array();
    	foreach($json_data->symbols as $single){
    		if($single->status != 'TRADING'){continue;}
    		$arr = array(
    				'symbol' => $single->symbol,
    				'base' => $single->baseAsset,
    				'quote' => $single->quoteAsset,
    				);
    		$pair_array[] = $arr;
    		if(!in_array($single->quoteAsset, $marketes)){
    			$marketes[] = $single->quoteAsset;
    		}
    	}
    	$final = array(
    				'pairs' => $pair_array,
    				'markets' => $marketes
    				);
		$this->cache->save('binance_pairs', $final, 21600);
    }
	return $final;
}

}