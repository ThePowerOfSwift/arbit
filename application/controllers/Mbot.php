<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mbot extends MY_Controller {

// public function ip_add(){
//     print_r(file_get_contents("http://www.geoplugin.net/json.gp?ip=198.13.63.110"));
//}
    
/////////////////////////////////////////////////////////// trade functions ///////////////////////////////////////////////////////////////
// initial function to hit trade
public function auto_hit_trade(){
    $u_id = $this->session->userdata('u_id'); 
    // if($u_id != 13870){
    //     echo json_encode(array("error"=>'true', "msg"=>"This function is currenctly not available.")); exit;
    // }
    if($u_id != ''){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        
        //get exchanges name
        $buy_exchange = strtolower($request->buy_exchange);
        $sell_exchange = strtolower($request->sell_exchange);
        
        // if any of echange is btcmarket change it into btcmarkets
        if($buy_exchange == 'btcmarket'){$buy_exchange = 'btcmarkets';}
        if($sell_exchange == 'btcmarket'){$sell_exchange = 'btcmarkets';}
        
        // get trade currency and volume
        $trade_currency = strtoupper($request->currency);
        $main_currency = strtoupper($request->mainCurrency);
        $volume = $request->volume;
        
        // truncate extra decimal points after 3 decimal places
        if($trade_currency == 'XRP'){$volume = intval($volume).'';}
        else{$volume = $this->getTruncatedValue($volume, 3).'';}
        
        // user filters like volume not in negative, is user mbot member, user mbot membership limit and expiry checks
        // volume is not less then equal to 0 
        if($volume <= 0){echo json_encode(array("error"=>'true', "msg"=>"Volume must be greater then 0.")); exit;}
        
        // not mbot member
        $mbot_member = $this->db->query("SELECT * FROM mbot_members WHERE user_id = ? AND status = ?", array($u_id, 1))->row();
        if(!isset($mbot_member->id)){echo json_encode(array("error"=>'true', "msg"=>"Please activate your mBOT Membership.")); exit;}
        
        // if expiry date excedded
        $today = date("Y-m-d H:i:s");
        if($today > $mbot_member->expire_date){
            $this->db->query("UPDATE mbot_members SET status = ? WHERE user_id = ?", array(0, $u_id));
           echo json_encode(array("error"=>'true', "msg"=>"Your mBOT Membership is expired. Please renew your Membership."));exit;
        }
        
        // if $ limit exceded 
        $file_data = file_get_contents('https://api.coinmarketcap.com/v2/ticker/');
        $file_data = json_decode($file_data);
        $unit_price = 0;
        foreach($file_data->data as $val){
         if($val->symbol == $trade_currency){
           $unit_price =  $val->quotes->USD->price;
         }
        }
        $expected_limit = 0;
        if($unit_price > 0){ $expected_limit = $volume * $unit_price;}
        else{echo "not get unit price"; exit;}
        if($mbot_member->max_trade_limit < $expected_limit){ echo json_encode(array("error"=>'true', "msg"=>"Your Membership limit Reached. Please renew your Membership.")); exit;}
        
        // get user api keys
        // buy exchange keys
        if($buy_ex_cred = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ? AND exchange = ?", array($u_id, $buy_exchange))->row()){
            $buy_exchange_key = $buy_ex_cred->api_key;
            $buy_exchange_sec = $this->encrypt->decode($buy_ex_cred->sec_key);
        }else{
            echo json_encode(array("error"=>'true', "msg"=>"Your ".$buy_exchange." keys not found. Please add your api keys in Account -> mBOT tab."));
            exit;
        }
        // sell exchange keys
        if($sell_ex_cred = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ? AND exchange = ?", array($u_id, $sell_exchange))->row()){
            $sell_exchange_key = $sell_ex_cred->api_key;
            $sell_exchange_sec = $this->encrypt->decode($sell_ex_cred->sec_key);
        }else{
            echo json_encode(array("error"=>'true', "msg"=>"Your ".$sell_exchange." keys not found. Please add your api keys in Account -> mBOT tab."));
            exit;
        }
        
        // insert data into db
         $data = array(
             'buy_exchange' => $buy_exchange, 
             'sell_exchange' => $sell_exchange,
             'buy_exchange_key' => $buy_exchange_key,
             'sell_exchange_key' => $sell_exchange_key,
             'buy_exchange_sec' => $buy_exchange_sec,
             'sell_exchange_sec' => $sell_exchange_sec,
             'trade_currency' => $trade_currency,
             'main_currency' => $main_currency,
             'volume' => $volume,
             'account_limit' => $mbot_member->max_trade_limit
             );
         
        $jdata = json_encode($data);
        if($this->db->query('select * from mbot_temp WHERE user_id = ?', array($u_id))->row()){
            echo json_encode(array("error"=>'true', "msg"=>"Your Trade is already placed."));
            exit;
        }else{
            $this->db->query('insert into mbot_temp (user_id, data, current_state) values (?,?,?)', array($u_id, $jdata, 'initial'));
        }
        
        $addresstag = null;
        // get sell exchange deposit address
        if($sell_exchange == 'btcmarkets'){
             if($add = $this->db->query("SELECT * FROM mbot_ex_no_dp_address_api WHERE user_id = ? AND exchange = ? AND coin = ?", array($u_id, 'btcmarkets', $trade_currency))->row()){
                 $withdraw_key = $add->deposit_address;
                 if($trade_currency == 'XRP'){
                     $addresstag = $add->coin_tag;
                 }
             }else{
                 echo json_encode(array("error"=>'true', "msg"=>"Btcmarkets ".$trade_currency." Deposit address not found. Please submit your Deposit address in Account -> mBOT tab."));
                 exit;
              }
        }else if($sell_exchange == 'huobi'){
             if($add = $this->db->query("SELECT * FROM mbot_ex_no_dp_address_api WHERE user_id = ? AND exchange = ? AND coin = ?", array($u_id, 'huobi', $trade_currency))->row()){
                 $withdraw_key = $add->deposit_address;
                 if($trade_currency == 'XRP' || $trade_currency == 'EOS' || $trade_currency == 'XLM' || $trade_currency == 'XMR'){
                     $addresstag = $add->coin_tag;
                 }
             }else{
                 echo json_encode(array("error"=>'true', "msg"=>"Huobi ".$trade_currency." Deposit address not found. Please submit your Deposit address in Account -> mBOT tab."));
                 exit;
             }
        }else{
            
            if($trade_currency == 'XRP' || $trade_currency == 'EOS' || $trade_currency == 'XLM' || $trade_currency == 'XMR'){
                if($add = $this->db->query("SELECT * FROM mbot_ex_no_dp_address_api WHERE user_id = ? AND exchange = ? AND coin = ?", array($u_id, $sell_exchange, $trade_currency))->row()){
                    $withdraw_key = $add->deposit_address;
                    $addresstag = $add->coin_tag;
                }else{
                     echo json_encode(array("error"=>'true', "msg"=>$sell_exchange." ".$trade_currency." Deposit address not found. Please submit your Deposit address and Destination Tag in Account -> mBOT tab."));
                     exit;
                 }
            }else{
                $fun = 'get_deposit_address_'.$sell_exchange;
                $withdraw_key = $this->$fun($sell_exchange_key, $sell_exchange_sec, $trade_currency, $u_id);
                if($withdraw_key == ''){
                    echo json_encode(array("error"=>'true', "msg"=>"No Deposit address received from ".$sell_exchange.". Please generate your deposit addresses."));
                    exit;
                }
            }
        }
        
        // only if buy exchange is kraken
        if($buy_exchange == 'kraken'){
            $withdraw_key = 'mbot_'.$sell_exchange.'_deposit_'.$trade_currency;
        }
        
        // add withdraw fee library to get fee by passing exchange name and coin symbol
        require_once APPPATH . 'libraries/Withdraw_fee.php';
        $fee_obj = new withdraw_fee();
        $fee = $fee_obj->get_fee($buy_exchange , $trade_currency);
        if($fee == 'no exchange'){
            echo json_encode(array("error"=>'true', "msg"=>"No such exchange (".$buy_exchange.") exist for trade. Please select one of listed exchanges for trade."));
            exit;
        }else if($fee == 'no coin match'){
            echo json_encode(array("error"=>'true', "msg"=>"No such coin pair (".$trade_currency.") match in ".$buy_exchange."."));
            exit;
        }
        
        
        // work from here $main_currency
        //exit;
        
        //place buy order on buy exchange
        $tx_id = null;
        $bid = null;
        if(isset($buy_exchange)){
            $price_fun = 'get_price_'.$buy_exchange;
            $bid = $this->$price_fun($buy_exchange_key, $buy_exchange_sec, $trade_currency, 'bid', $main_currency);
            $order_fun = 'place_order_'.$buy_exchange;
            $res = $this->$order_fun($buy_exchange_key, $buy_exchange_sec, $trade_currency, $volume, 'buy', $u_id, $main_currency);
           if(isset($res) && $res != ''){
                $tx_id = $res;
           }
           
        }
        
        
        // check buy order place on buy exchange successfully
        // $tx_id = 123;
        
        if($tx_id != ''){
            // merge arrays
            $new_data = array('withdraw_key' => $withdraw_key, 'addresstag' => $addresstag, 'txt_id' => $tx_id, 'bid'=>$bid.'','withdraw_fee' => $fee);
            $data = array_merge($data, $new_data);
            // push new data in DB
            $jdata = json_encode($data);
            $this->db->query('update mbot_temp set data = ?, current_state = ? WHERE user_id = ?', array($jdata,'buy_place', $u_id));
            
             // deduct limit 
             $file_data = file_get_contents('https://api.coinmarketcap.com/v2/ticker/');
             $file_data = json_decode($file_data);
             $unit_price = 0;
             foreach($file_data->data as $val){
                 if($val->symbol == $trade_currency){
                   $unit_price =  $val->quotes->USD->price;
                 }
             }
             $expected_limit = 0;
             if($unit_price > 0){
                 //account_limit
                $expected_limit = $volume * $unit_price;
                $new_limit = $mbot_member->max_trade_limit - $expected_limit;
                if($new_limit <= 0){
                    $this->db->query('update mbot_members set max_trade_limit = ?, status = ? WHERE user_id = ?', array($new_limit, 0, $u_id));
                }else{
                    $this->db->query('update mbot_members set max_trade_limit = ? WHERE user_id = ?', array($new_limit, $u_id));
                }
             }
            
            echo json_encode(array("success"=>'true', "msg"=>"Your buy order for ".$trade_currency." placed on ".$buy_exchange."."));
            exit;
        }else{
            echo json_encode(array("error"=>'true', "msg"=>"Balance not sufficient, please check your ".$buy_exchange." balance."));
            exit;
        }
        
         
        
    }else{
        echo json_encode(array("login"=>'false'));
        exit;
    }
}

 

// mbot cron to perform trade step by step (duration 1 min)
public function mbot_cron(){
    $qry = $this->db->query("SELECT * FROM mbot_temp WHERE current_state != ? AND status = ?", array('initial', 0))->result();
    
    foreach($qry as $result){
        // to convert object into array
        $data = json_decode($result->data);
        $data = json_decode(json_encode($data),true);
    
        if($result->current_state == 'buy_place'){
            if(isset($data['buy_exchange'])){
                $blnc_fun = 'get_balance_'.$data['buy_exchange'];
                $blnc = $this->$blnc_fun($data['buy_exchange_key'], $data['buy_exchange_sec'], $data['trade_currency'], $result->user_id);
                
                //echo $blnc; exit;
                
                // only for binance exchange
                if($data['buy_exchange'] == 'binance'){
                   $tradefee = $data['volume'] * 0.001;
                   $after_tradefee = $data['volume']-$tradefee;
                   $after_tradefee = $this->getTruncatedValue($after_tradefee, 4);
                   $data['volume'] = $after_tradefee.'';
                }
                // only for bithumb exchange
                else if($data['buy_exchange'] == 'bithumb'){
                   $tradefee = $data['volume'] * 0.0016;
                   $after_tradefee = $data['volume']-$tradefee;
                   $after_tradefee = $this->getTruncatedValue($after_tradefee, 4);
                   $data['volume'] = $after_tradefee.'';
                }
                // only for poloniex exchange
                else if($data['buy_exchange'] == 'poloniex'){
                   $tradefee = $data['volume'] * 0.002;
                   $after_tradefee = $data['volume']-$tradefee;
                   $after_tradefee = $this->getTruncatedValue($after_tradefee, 4);
                   $data['volume'] = $after_tradefee.'';
                }
               
                if($blnc >= $data['volume']){
                   $withdraw_fun = 'withdraw_'.$data['buy_exchange'];
                   $withdraw = $this->$withdraw_fun($data['buy_exchange_key'], $data['buy_exchange_sec'], $data['withdraw_key'], $data['trade_currency'], $data['volume'], $result->user_id, $data['addresstag']);
                    
                    if($withdraw == ''){
                      echo $data['buy_exchange']." withdraw stuck<br>";
                      continue;
                    }

                    // merge arrays
                    $new_data = array('withdraw_ref' => $withdraw);
                    $data = array_merge($data, $new_data);
                    // push new data in DB
                    $jdata = json_encode($data);
                    $this->db->query('update mbot_temp set data = ?, current_state = ? WHERE user_id = ?', array($jdata,'in_withdraw', $result->user_id));
                    echo "in withdraw state ".$data['buy_exchange']."<br>";
                }
            }
            
        }else if($result->current_state == 'in_withdraw'){
            $new_volume = $data['volume'] - $data['withdraw_fee'];
            $new_data = null;
            if($new_volume > 0){
                if(isset($data['sell_exchange'])){
                    $blnc_fun = 'get_balance_'.$data['sell_exchange'];
                    if($this->$blnc_fun($data['sell_exchange_key'], $data['sell_exchange_sec'], $data['trade_currency'], $result->user_id) >= $new_volume){
                       $price_fun = 'get_price_'.$data['sell_exchange'];
                       $ask = $this->$price_fun($data['sell_exchange_key'], $data['sell_exchange_sec'], $data['trade_currency'], 'ask', $data['main_currency']);
                       $order_fun = 'place_order_'.$data['sell_exchange'];
                       $res = $this->$order_fun($data['sell_exchange_key'], $data['sell_exchange_sec'], $data['trade_currency'], $new_volume, 'sell', $result->user_id, $data['main_currency']);
                       if($res != ''){
                            $new_data = array('sell_order' => $res, 'sell_volume' => $new_volume.'', 'ask'=>$ask.'');
                       }
                    }else{
                        echo "not enough balance in ".$data['sell_exchange']." please wait for withdraw<br>";
                    }
                }
                
                if(is_array($new_data)){
                    $data = array_merge($data, $new_data);
                    
                    unset($data['buy_exchange_key']);
                    unset($data['buy_exchange_sec']);
                    unset($data['sell_exchange_key']);
                    unset($data['sell_exchange_sec']);
                    $jdata = json_encode($data);
                    sleep(3);
                    $this->db->query('insert into  mbot_history (user_id, data, api_res) values (?,?,?)', array($result->user_id, $jdata, $result->api_res));
                    $this->db->query('DELETE FROM mbot_temp WHERE user_id = ?', array($result->user_id));
                    echo "success";
                }else{
                    echo 'null data<br>';
                }
                           
            }
               
        }
        
        
    }
   
}


    

////////////////////////////////////////////////////////////////////////// POLONIEX EXCHANGE ///////////////////////////////////////////////////////
  public function get_price_poloniex($key, $sec, $currency, $type, $main_currency = null){
        require_once APPPATH . 'libraries/Poloniex.php';
        
        if($main_currency == 'USD-USDT'){
            $main_currency = 'USDT';
        }
        
        if($currency == 'XLM'){
           $currency = 'STR'; 
        }
        
        $poloniex_key = $key; //"U99MHVH8-1QL38JP2-NV5NDNAS-E1OU0PNL";
        $poloniex_secret = $sec; //"8c1c015e42cb711ac835b98fb04f016e5873805b4a9842886ef01d4158c324e33d3b87d22e0ad74605b25fbd08c2d282a123ae30d4d6388f00d61db954d02911";
            
        $poloniex_obj = new poloniex($poloniex_key , $poloniex_secret);
        $poloniex_balance_result = $poloniex_obj->get_ticker();
        
        if($main_currency == 'ETH' && $currency == 'BTC' && $type = 'ask'){
            $type = 'bid';
            $pair = 'BTC_ETH';
        }else if($main_currency == 'ETH' && $currency == 'BTC' && $type = 'bid'){
            $type = 'ask';
            $pair = 'BTC_ETH';
        }else{
            $pair =  $main_currency.'_'.$currency;
        }
        
        if($type == 'ask'){
            return $poloniex_balance_result[$pair]['lowestAsk'];
        }else{
            return $poloniex_balance_result[$pair]['highestBid'];
        }
    }
 
 
 public function withdraw_poloniex($key, $sec, $address, $currency, $amount, $user_id, $addresstag = null){
    require_once APPPATH . 'libraries/Poloniex.php';
    
    $poloniex_key = $key; //"U99MHVH8-1QL38JP2-NV5NDNAS-E1OU0PNL";
    $poloniex_secret = $sec; //"8c1c015e42cb711ac835b98fb04f016e5873805b4a9842886ef01d4158c324e33d3b87d22e0ad74605b25fbd08c2d282a123ae30d4d6388f00d61db954d02911";
        
    $poloniex_obj = new poloniex($poloniex_key , $poloniex_secret);
        
    $poloniex_balance_result = $poloniex_obj->withdraw($currency, $amount, $address, $addresstag);
    
    //update app responce
    $api_res = json_encode(array('Exchange' => 'Poloniex', 'apiResponse' => $poloniex_balance_result));
    $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
    
    return $poloniex_balance_result['response'];
    
 }

 public function get_deposit_address_poloniex($key, $sec, $currency, $user_id = null){
    require_once APPPATH . 'libraries/Poloniex.php';
    $poloniex_obj = new poloniex($key , $sec);
    
    if($currency == 'XLM'){
           $currency = 'STR'; 
        }
    
    $poloniex_balance_result = $poloniex_obj->getDepositAddresses();
    
    //update app responce
    $api_res = json_encode(array('Exchange' => 'Poloniex', 'apiResponse' => $poloniex_balance_result));
    $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
    
    return $poloniex_balance_result[$currency];
    
  }

 public function get_balance_poloniex($key, $sec, $cur, $user_id)
   {
        require_once APPPATH . 'libraries/Poloniex.php';
        
        if($cur == 'XLM'){
           $cur = 'STR'; 
        }
        
        $poloniex_key = $key; //"U99MHVH8-1QL38JP2-NV5NDNAS-E1OU0PNL";
        $poloniex_secret = $sec; //"8c1c015e42cb711ac835b98fb04f016e5873805b4a9842886ef01d4158c324e33d3b87d22e0ad74605b25fbd08c2d282a123ae30d4d6388f00d61db954d02911";
        
        $poloniex_obj = new poloniex($poloniex_key , $poloniex_secret);
        
        $poloniex_balance_result = $poloniex_obj->get_balances();
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Poloniex', 'apiResponse' => $poloniex_balance_result[$cur]));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $poloniex_balance_result[$cur];

 }
 public function place_order_poloniex($key, $sec, $currency, $volume, $type, $user_id, $main_currency = null)
   {
       if($main_currency == 'USD-USDT'){
            $main_currency = 'USDT';
        }
        require_once APPPATH . 'libraries/Poloniex.php';
        if($currency == 'XLM'){
           $currency = 'STR'; 
        }

        $poloniex_key = $key; //"U99MHVH8-1QL38JP2-NV5NDNAS-E1OU0PNL";
        $poloniex_secret = $sec; //"8c1c015e42cb711ac835b98fb04f016e5873805b4a9842886ef01d4158c324e33d3b87d22e0ad74605b25fbd08c2d282a123ae30d4d6388f00d61db954d02911";
        
        $poloniex_obj = new poloniex($poloniex_key , $poloniex_secret);
        
        if($main_currency == 'ETH' && $currency == 'BTC' && $type = 'sell'){
            $type = 'buy';
            $pair = 'BTC_ETH';
            $type_2 = 'ask';
        }else if($main_currency == 'ETH' && $currency == 'BTC' && $type = 'buy'){
            $type = 'sell';
            $pair = 'BTC_ETH';
            $type_2 = 'bid';
        }else{
            $pair =  $main_currency.'_'.$currency;
            if($type == 'sell'){$type_2 = 'bid';}else{$type_2 = 'ask';}
        }
        
        
    
        $data = $poloniex_obj->get_order_book($pair);
        $rate = $data[$type_2][0][0];
        if($type == 'buy'){
            $orderResponse = $poloniex_obj->buy($pair, $rate , $volume);
        }else{
            $orderResponse = $poloniex_obj->sell($pair, $rate , $volume);
        }
        
        //update app responce
       $api_res = json_encode(array('Exchange' => 'Poloniex', 'apiResponse' => $orderResponse));
       $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $orderResponse['orderNumber'];
   
   }
   



////////////////////////////////////////////////////////////////////////// KRAKEN EXCHANGE ////////////////////////////////////////////////////////////////
    
    public function get_price_kraken($key, $sec, $currency, $type, $main_currency = null){
        // $type = ask/bid
        if($main_currency == 'USD-USDT'){
            $main_currency = 'USD';
        }
        require_once APPPATH . 'libraries/Kraken.php';
        $key = $key; //'EHgGDnOOJ0qGD6Vo44u3PBo/rBbG18+H6nBaiyu6T8M/BApU8V22btCY';
        $secret = $sec; //'veUL2iuus/HmZCBIgnkh1Aiwl32SHQWdJSsUURLugrXRFRyDiz5b3c+kvmw59Hd3aESTin9MWHATtT4l5zUjAA==';
    
            
        $beta = false;
        $url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
        $sslverify = $beta ? false : true;
        $version = 0;
        $kraken = new Kraken($key, $secret, $url, $version, $sslverify);
        $pair = null;
        if($main_currency == 'USD'){
            if($currency == 'BTC'){
                $pair = 'XXBTZUSD';
            }
            else if($currency == 'BCH' || $currency == 'EOS' || $currency == 'XMR' || $currency == 'DASH' || $currency == 'XLM'){
                $pair = $currency.'USD';
            }
            else{
                $pair = 'X'.$currency.'ZUSD';
            }
        }else if($main_currency == 'BTC'){
            if($currency == 'BCH' || $currency == 'EOS' || $currency == 'XMR' || $currency == 'DASH' || $currency == 'XLM'){
                $pair = $currency.'XBT';
            }
            else{
                $pair = 'X'.$currency.'XXBT';
            }
        }else if($main_currency == 'ETH'){
            if($currency == 'EOS'){
                $pair = $currency.'ETH';
            }
            else if($currency == 'BTC'){
                $pair = 'XETHXXBT';
                if($type == 'ask'){$type = 'bid';}
                else if($type == 'bid'){$type = 'ask';}
            }
        }
        
        $res = $kraken->QueryPublic('Ticker', array('pair' => $pair));
        if($type == 'ask'){
            return $res['result'][$pair]['a'][0];
        }else{
            return $res['result'][$pair]['b'][0];
        }
           
    }
    
    public function get_balance_kraken($key, $sec, $currency, $user_id){
       
       require_once APPPATH . 'libraries/Kraken.php';

       $key = $key; //'EHgGDnOOJ0qGD6Vo44u3PBo/rBbG18+H6nBaiyu6T8M/BApU8V22btCY';
       $secret = $sec; //'veUL2iuus/HmZCBIgnkh1Aiwl32SHQWdJSsUURLugrXRFRyDiz5b3c+kvmw59Hd3aESTin9MWHATtT4l5zUjAA==';


       $beta = false;
       $url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
       $sslverify = $beta ? false : true;
       $version = 0;
       $kraken = new Kraken($key, $secret, $url, $version, $sslverify);
       $res = $kraken->QueryPrivate('Balance');
       
       //update app responce
       $api_res = json_encode(array('Exchange' => 'Kraken', 'apiResponse' => $res));
       $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
       
        $pair = null;
        if($currency == 'BTC'){
            $pair = 'XXBT';
        }
        else if($currency == 'BCH' || $currency == 'EOS' || $currency == 'XMR' || $currency == 'DASH' || $currency == 'XLM'){
            $pair = $currency;
        }
        else{
            $pair = 'X'.$currency;
        }
       return $res['result'][$pair];
       
   }

   public function place_order_kraken($key, $sec, $currency, $volume, $type, $user_id, $main_currency = null)
   {
       if($main_currency == 'USD-USDT'){
            $main_currency = 'USD';
        }
       require_once APPPATH . 'libraries/Kraken.php';

       $key = $key; //'EHgGDnOOJ0qGD6Vo44u3PBo/rBbG18+H6nBaiyu6T8M/BApU8V22btCY';
       $secret = $sec; //'veUL2iuus/HmZCBIgnkh1Aiwl32SHQWdJSsUURLugrXRFRyDiz5b3c+kvmw59Hd3aESTin9MWHATtT4l5zUjAA==';
        
        if($main_currency == 'USD'){
            if($currency == 'BTC'){
                $pair = 'XXBTZUSD';
            }
            else if($currency == 'BCH' || $currency == 'EOS' || $currency == 'XMR' || $currency == 'DASH' || $currency == 'XLM'){
                $pair = $currency.'USD';
            }
            else{
                $pair = 'X'.$currency.'ZUSD';
            }
        }else if($main_currency == 'BTC'){
            if($currency == 'BCH' || $currency == 'EOS' || $currency == 'XMR' || $currency == 'DASH' || $currency == 'XLM'){
                $pair = $currency.'XBT';
            }
            else{
                $pair = 'X'.$currency.'XXBT';
            }
        }else if($main_currency == 'ETH'){
            if($currency == 'EOS'){
                $pair = $currency.'ETH';
            }
            else if($currency == 'BTC'){
                $pair = 'XETHXXBT';
                if($type == 'sell'){$type = 'buy';}
                else if($type == 'buy'){$type = 'sell';}
            }
        }

       $beta = false;
       $url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
       $sslverify = $beta ? false : true;
       $version = 0;
       $kraken = new Kraken($key, $secret, $url, $version, $sslverify);

       $res = $kraken->QueryPrivate('AddOrder', array(
           'pair' => $pair, //'XETHZUSD',
           'type' => $type, //'buy',
           'ordertype' => 'market',
           'volume' => $volume
       ));
       
       //update app responce
       $api_res = json_encode(array('Exchange' => 'Kraken', 'apiResponse' => $res));
       $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
       
       return $res['result']['txid'][0];
   }

   public function get_deposit_address_kraken($key, $secret, $cur, $user_id){
       require_once APPPATH . 'libraries/Kraken.php';

       //$key = 'LX7LxMLLiPyMkZYoFoiEaHeR7aXIl35czESG1hJ3yIQ+zBPDAKa//ChD';
       //$secret = 'HafvaUCD77UedH0ga+l+wNFM+HB1YyEav4dxKsM4r3W7qazj0Gn1SF9JX2zPQTsC5foAHhAWVIbU4NhQIcSAPQ==';


       $beta = false;
       $url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
       $sslverify = $beta ? false : true;
       $version = 0;
       $kraken = new Kraken($key, $secret, $url, $version, $sslverify);

       $res1 = $kraken->QueryPrivate('DepositMethods', array(
           'asset' => $cur // asset being deposited
        ));
        
        $method = $res1['result'][0]['method'];
        
        $res = $kraken->QueryPrivate('DepositAddresses', array(
           'asset' => $cur, // asset being deposited
           'method' => $method
        ));
        
         
        //update app responce
       $api_res = json_encode(array('Exchange' => 'Kraken', 'apiResponse' => $res));
       $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        
       return $res['result'][0]['address'];
       
   }

   public function withdraw_kraken($key, $sec, $withdrew_key, $currency, $amount, $user_id, $addresstag = null)
   {
       require_once APPPATH . 'libraries/Kraken.php';

      $key = $key; //'EHgGDnOOJ0qGD6Vo44u3PBo/rBbG18+H6nBaiyu6T8M/BApU8V22btCY';
       $secret = $sec; //'veUL2iuus/HmZCBIgnkh1Aiwl32SHQWdJSsUURLugrXRFRyDiz5b3c+kvmw59Hd3aESTin9MWHATtT4l5zUjAA==';


       $beta = false;
       $url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
       $sslverify = $beta ? false : true;
       $version = 0;
       $kraken = new Kraken($key, $secret, $url, $version, $sslverify);

       $res = $kraken->QueryPrivate('Withdraw', array(
           'asset' => $currency, // asset to be withdrawn
           'key' => $withdrew_key, //as set up on your account
           'amount' => $amount
          // refid = reference id (result of withdraw transaction)
       ));
       
       //update app responce
       $api_res = json_encode(array('Exchange' => 'Kraken', 'apiResponse' => $res));
       $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
       
      return $res['result']['refid'];
   }
   
   
   
//////////////////////////////////////////////////////////////// Binance Exchange ////////////////////////////////////////////////////////////////////
   
   public function get_price_binance($key, $sec, $currency, $type, $main_currency = null){
       if($main_currency == 'USD-USDT'){
            $main_currency = 'USDT';
        }
       require_once APPPATH . 'libraries/Binance.php';

       $key = $key; //'sSOwdatSS6goMSEe6PmkpzN83Ec11XfPGMFXJtPDROEy6isyMAlR98uZKBw2OrsQ'; //'GBUtLHvAFUslJ6jrZD3EMGTlVBaNrBMaM83vSSGuAa5b0sK98ibfSC6kfze7aTOz';
       $secret = $sec; //'Cbt06DTCZSSQgw8Q98emn2hbDLtkO1PpcYKEruO2GBNUzrSznmbpXZQ380mGg7vS'; //'QXtv01sYHCm71zW7jK5yRvFiFqpCc8TgVEbPdDOE1nba2IWXffCrFKCkRNS0SlZc';

       $binance = new Binance($key, $secret);
       $res = $binance->bookPrices();
       if($currency == 'BCH'){
           $currency='BCC';
       }
       
       if($main_currency == 'ETH' && $currency == 'BTC' && $type == 'ask'){
           $type = 'bid';
           $pair = 'ETHBTC';
       }else if($main_currency == 'ETH' && $currency == 'BTC' && $type == 'bid'){
           $type = 'ask';
           $pair = 'ETHBTC';
       }else{
           $pair =  $currency."".$main_currency;
       }
       
       if($type == 'ask'){
            return $res[$pair]['ask'];
       }else{
           return $res[$pair]['bid'];
       }
       
   }
   
   public function get_balance_binance($key, $sec, $currency, $user_id){
       require_once APPPATH . 'libraries/Binance.php';

       $key = $key; //'sSOwdatSS6goMSEe6PmkpzN83Ec11XfPGMFXJtPDROEy6isyMAlR98uZKBw2OrsQ'; //'GBUtLHvAFUslJ6jrZD3EMGTlVBaNrBMaM83vSSGuAa5b0sK98ibfSC6kfze7aTOz';
       $secret = $sec; //'Cbt06DTCZSSQgw8Q98emn2hbDLtkO1PpcYKEruO2GBNUzrSznmbpXZQ380mGg7vS'; //'QXtv01sYHCm71zW7jK5yRvFiFqpCc8TgVEbPdDOE1nba2IWXffCrFKCkRNS0SlZc';

       $binance = new Binance($key, $secret);

       $res = $binance->account();
       if($currency == 'BCH'){
           $currency='BCC';
       }
       
       $blnc = 0;
       foreach($res['balances'] as $asset){
           if($asset['asset'] == $currency){
               //update app responce
               $api_res = json_encode(array('Exchange' => 'Binance', 'apiResponse' => $asset));
               $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
               $blnc = $asset['free'];
           }
       }
      
      return $blnc;
      
   }
   
   public function get_deposit_address_binance($key, $secret, $cur, $user_id){
       
       require_once APPPATH . 'libraries/Binance.php';
       
       //$key = 'NFkw83lZrtupppYqE1Ur6hH1H8Bb8UuTVRL34hBeSfZlWGCmPl3BlBxcOUudZjNi';
       //$secret = 'K4XYMEZ6eldYyfpe77GZin5G0Qe06xBPNvT4Pw4FPIDrkOe4LZapYf6kNZPhYGRn'; 
       
       if($cur == 'BCH'){
           $cur='BCC';
       }
       
       $binance = new Binance($key, $secret);
       $res = $binance->depositAddress($cur);
       
       //update app responce
       $api_res = json_encode(array('Exchange' => 'Binance', 'apiResponse' => $res));
       $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
       
       return $res['address'];
   }
   
   public function place_order_binance($key, $sec, $cur, $volume, $type, $user_id, $main_currency = null)
   {
       if($main_currency == 'USD-USDT'){
            $main_currency = 'USDT';
        }
       if($cur == 'BCH'){
           $cur = 'BCC';
       }
       require_once APPPATH . 'libraries/Binance.php';

       $key = $key; //'sSOwdatSS6goMSEe6PmkpzN83Ec11XfPGMFXJtPDROEy6isyMAlR98uZKBw2OrsQ';
       $secret = $sec; // 'Cbt06DTCZSSQgw8Q98emn2hbDLtkO1PpcYKEruO2GBNUzrSznmbpXZQ380mGg7vS'; 
       
       if($main_currency == 'ETH' && $cur == 'BTC' && $type == 'sell'){
           $type = 'buy';
           $pair = 'ETHBTC';
       }else if($main_currency == 'ETH' && $cur == 'BTC' && $type == 'buy'){
           $type = 'sell';
           $pair = 'ETHBTC';
       }else{
           $pair =  $cur."".$main_currency;
       }
       $binance = new Binance($key, $secret);
        if($type == 'sell'){
           $res = $binance->marketSell($pair, $volume);
        }else if($type == 'buy'){
           $res = $binance->marketBuy($pair, $volume);
        }
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Binance', 'apiResponse' => $res));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
             return $res['orderId'];
   }
   
   
   public function withdraw_binance($key, $sec, $withdrew_key, $currency, $amount, $user_id, $addresstag = null)
   {
       require_once APPPATH . 'libraries/Binance.php';

       $key = $key; //'sSOwdatSS6goMSEe6PmkpzN83Ec11XfPGMFXJtPDROEy6isyMAlR98uZKBw2OrsQ';
       $secret = $sec; //'Cbt06DTCZSSQgw8Q98emn2hbDLtkO1PpcYKEruO2GBNUzrSznmbpXZQ380mGg7vS'; 
       
       if($currency == 'BCH'){
           $currency='BCC';
       }
       
       $binance = new Binance($key, $secret);
        if($addresstag != ''){
            $tag = $addresstag;
        }else{
            $tag = false;
        }
       $res = $binance->withdraw($currency, $withdrew_key, $amount, $tag);
       
       //update app responce
       $api_res = json_encode(array('Exchange' => 'Binance', 'apiResponse' => $res));
       $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
       
       //if($res['success'] == 1){
        return $res['id'];
       //}else{
       //   return false;
       //}
   }

  
/////////////////////////////////////////////////////////// Btcmarkets Exchange ///////////////////////////////////////////////////////////
    public function get_price_btcmarkets($key, $sec, $currency, $type, $main_currency = null){
        $public_key = $key; //"c4ae2d7f-ba1c-4fbc-9b4f-90451d1b993b";
        $secret_key = $sec; //"YR+c8pHmdXMbgIMuaVFk/ZszaMvTfV0yzio9R17NTjbM5ZwYu9iqO9bmrzVkeG0xAA24P4ZFFM/xAFJXldbywQ==";
        $secret_key_encoded = base64_decode($secret_key);
        $milliseconds = round(microtime(true) * 1000);
        $msg = "/market/".$currency."/AUD/tick\n" . $milliseconds . "\n";
        
        $encodedMsg =   hash_hmac('sha512', $msg, $secret_key_encoded, true);
        $base64Msg = base64_encode($encodedMsg); 
        
        // Create a stream
        $opts = array(
          'http'=>array(
                'method'=>"GET",
                'header'=>      "Accept: */*\r\n" .
                                "Accept-Charset: UTF-8\r\n" .
                                "Content-Type: application/json\r\n" .
                                "apikey: " . $public_key . "\r\n" .
                                "timestamp: " . $milliseconds . "\r\n" .
                                "User-Agent: btc markets php client\r\n" .
                                "signature: " . $base64Msg . "\r\n"
          )
        );
        
        $context = stream_context_create($opts);
        $json = file_get_contents('https://api.btcmarkets.net/market/'.$currency.'/AUD/tick', false, $context);
        $arr = json_decode($json);
        
        if($type == 'ask'){
            return $arr->bestAsk;
        }else{
            return $arr->bestBid;
        }
        
        
    }
    
    public function get_balance_btcmarkets($key, $sec, $currancy, $user_id){
        
        $public_key = $key; //"c4ae2d7f-ba1c-4fbc-9b4f-90451d1b993b";
        $secret_key = $sec; //"YR+c8pHmdXMbgIMuaVFk/ZszaMvTfV0yzio9R17NTjbM5ZwYu9iqO9bmrzVkeG0xAA24P4ZFFM/xAFJXldbywQ==";
        $secret_key_encoded = base64_decode($secret_key);
        $milliseconds = round(microtime(true) * 1000);
        $msg = "/account/balance\n" . $milliseconds . "\n";
        
        $encodedMsg =   hash_hmac('sha512', $msg, $secret_key_encoded, true);
        $base64Msg = base64_encode($encodedMsg); 
        
        // Create a stream
        $opts = array(
          'http'=>array(
                'method'=>"GET",
                'header'=>      "Accept: */*\r\n" .
                                "Accept-Charset: UTF-8\r\n" .
                                "Content-Type: application/json\r\n" .
                                "apikey: " . $public_key . "\r\n" .
                                "timestamp: " . $milliseconds . "\r\n" .
                                "User-Agent: btc markets php client\r\n" .
                                "signature: " . $base64Msg . "\r\n"
          )
        );
        
        $context = stream_context_create($opts);
        $json = file_get_contents('https://api.btcmarkets.net/account/balance', false, $context);
        
        $blnc = 0;
        $arr = json_decode($json);
        
        //update app responce
       $api_res = json_encode(array('Exchange' => 'Btcmarkets', 'apiResponse' => $arr));
       $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        
        foreach($arr as $a){
            if($a->currency == $currancy){
                $blnc = $a->balance/100000000; 
            }
        }
        
       return $blnc;
    }
    
    public function place_order_btcmarkets($key, $sec, $cur, $volume, $side, $user_id, $main_currency = null)
       {
            if($side == 'sell'){
                $side = 'Ask';
            }else{
                $side = 'Bid';
            }
            $volume = $volume * 100000000;
            $public_key = $key; //"c4ae2d7f-ba1c-4fbc-9b4f-90451d1b993b";
            $secret_key = $sec; //"YR+c8pHmdXMbgIMuaVFk/ZszaMvTfV0yzio9R17NTjbM5ZwYu9iqO9bmrzVkeG0xAA24P4ZFFM/xAFJXldbywQ==";
            $postdata = '{"currency":"AUD","instrument":"'.$cur.'","price":13000000000,"volume":'.$volume.',"orderSide":"'.$side.'","ordertype":"Market","clientRequestId":"abc-cdf-1000"}';
            
            $secret_key_encoded = base64_decode($secret_key);
            $milliseconds = round(microtime(true) * 1000);
            $msg = "/order/create\n" . $milliseconds . "\n".$postdata;
            
            $encodedMsg =   hash_hmac('sha512', $msg, $secret_key_encoded, true);
            $base64Msg = base64_encode($encodedMsg); 
            
            // Create a stream
            $opts = array(
              'http'=>array(
                    'method'=>"POST",
                    'header'=>      "Accept: */*\r\n" .
                                    "Accept-Charset: UTF-8\r\n" .
                                    "Content-Type: application/json\r\n" .
                                    "apikey: " . $public_key . "\r\n" .
                                    "timestamp: " . $milliseconds . "\r\n" .
                                    "User-Agent: btc markets php client\r\n" .
                                    "signature: " . $base64Msg . "\r\n",
                    'content' => $postdata
              )
            );
            
            $context = stream_context_create($opts);
            $json = file_get_contents('https://api.btcmarkets.net/order/create', false, $context);
            
            $blnc = 0;
            $arr = json_decode($json);
            
            //update app responce
            $api_res = json_encode(array('Exchange' => 'Btcmarkets', 'apiResponse' => $arr));
            $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
            
            if($arr->id != ''){
                return $arr->id;
            }else{
                return false;
            }
          
           
       }
    
    
    public function withdraw_btcmarkets($key, $sec, $address, $cur, $amount, $user_id, $addresstag = null){
            $amount = $amount * 100000000;
            $public_key = $key; //"c4ae2d7f-ba1c-4fbc-9b4f-90451d1b993b";
            $secret_key = $sec; //"YR+c8pHmdXMbgIMuaVFk/ZszaMvTfV0yzio9R17NTjbM5ZwYu9iqO9bmrzVkeG0xAA24P4ZFFM/xAFJXldbywQ==";
            
            if($addresstag != ''){
                $address = $address."?dt=".$addresstag;
            }
            
            $postdata = '{"amount":'.$amount.',"address":"'.$address.'","currency":"'.$cur.'"}';
            
            $secret_key_encoded = base64_decode($secret_key);
            $milliseconds = round(microtime(true) * 1000);
            $msg = "/fundtransfer/withdrawCrypto\n" . $milliseconds . "\n".$postdata;
            
            $encodedMsg =   hash_hmac('sha512', $msg, $secret_key_encoded, true);
            $base64Msg = base64_encode($encodedMsg); 
            
            // Create a stream
            $opts = array(
              'http'=>array(
                    'method'=>"POST",
                    'header'=>      "Accept: */*\r\n" .
                                    "Accept-Charset: UTF-8\r\n" .
                                    "Content-Type: application/json\r\n" .
                                    "apikey: " . $public_key . "\r\n" .
                                    "timestamp: " . $milliseconds . "\r\n" .
                                    "User-Agent: btc markets php client\r\n" .
                                    "signature: " . $base64Msg . "\r\n",
                    'content' => $postdata
              )
            );
            
            $context = stream_context_create($opts);
            $json = file_get_contents('https://api.btcmarkets.net/fundtransfer/withdrawCrypto', false, $context);
            $arr = json_decode($json);
            
            //update app responce
            $api_res = json_encode(array('Exchange' => 'Btcmarkets', 'apiResponse' => $arr));
            $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
            
            if($arr->success){
                return 'Success';
            }else{
                return 'Fail';
            }
            
    }
    
//////////////////////////////////////////////////////// HITBTC EXCHANGE //////////////////////////////////////////////////////////////////
  public function get_price_hitbtc($key, $sec, $currency, $type, $main_currency = null){
        
        if($main_currency == 'USD-USDT'){
            $main_currency = 'USDT';
        }
        require_once APPPATH . 'libraries/hitBtc.php';
        $hitbtc = new hitBtc($key, $sec);
        
        $pair = $currency.''.$main_currency;
        $res = json_decode($hitbtc->getPrice($pair));
        
        if($type == 'bid'){
            return $res->bid;
        }else{
            return $res->ask;
        }
        
  }
  
  public function get_balance_hitbtc($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/hitBtc.php';
        //$key = 'e7c777f718ddc71b907c4258ee48d106';
        //$sec =  'e77dbec975ccf50b721f0cf4da4b725c';
        $hitbtc = new hitBtc($key, $sec);
        $res = json_decode($hitbtc->getBalance());
        
        
        
        foreach($res as $a){
            if($a->currency == $currency){
                //update app responce
                $api_res = json_encode(array('Exchange' => 'Hitbtc', 'apiResponse' => $a));
                $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
                return $a->available;
            }
        }
  }
  
  public function get_deposit_address_hitbtc($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/hitBtc.php';
        //$key = '05856ac822382df95c9c587701c112d3';
        //$sec =  '625a84e83324cadfb8ba325261b6ec34';
        $hitbtc = new hitBtc($key, $sec);
        $res = json_decode($hitbtc->getDepositAddress($currency));
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Hitbtc', 'apiResponse' => $res));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $res->address;
    }
    
  
  public function place_order_hitbtc($key, $sec, $cur, $volume, $type, $user_id, $main_currency = null){
      if($main_currency == 'USD-USDT'){
            $main_currency = 'USDT';
        }
      
        require_once APPPATH . 'libraries/hitBtc.php';
        //$key = 'e7c777f718ddc71b907c4258ee48d106';
        //$sec = 'e77dbec975ccf50b721f0cf4da4b725c';
        $hitbtc = new hitBtc($key, $sec);
        $pair = $cur.''.$main_currency;
        if($this->get_balance_hitbtc($key, $sec, $cur, $user_id) >= $volume){
            $hitbtc->switch_amount($cur, $volume, 'bankToExchange');
            sleep(8);
        }
        $res = json_decode($hitbtc->place_order($pair, $type, $volume));
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Hitbtc', 'apiResponse' => $res));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $res->clientOrderId;
        
  }
  
//   public function track_order_hitbtc($key, $sec, $txt_id){
       
//   }
  
  public function withdraw_hitbtc($key, $sec, $address, $cur, $amount, $user_id, $addresstag = null){
        require_once APPPATH . 'libraries/hitBtc.php';
        //$key = 'e7c777f718ddc71b907c4258ee48d106';
        //$sec = 'e77dbec975ccf50b721f0cf4da4b725c';
        $hitbtc = new hitBtc($key, $sec);
        if($this->get_balance_hitbtc($key, $sec, $cur, $user_id) < $volume){
            $hitbtc->switch_amount($cur, $volume, 'exchangeToBank');
            sleep(8);
        }
        $res = json_decode($hitbtc->withdraw ($cur, $amount, $address, $addresstag));
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Hitbtc', 'apiResponse' => $res));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $res->id;
      
  }
  
  
//////////////////////////////////////////////////////// BITTREX EXCHANGE /////////////////////////////////////////////////////////////////
  public function get_price_bittrex($key, $sec, $currency, $type, $main_currency = null){
      if($main_currency == 'USD-USDT'){
            $main_currency = 'USDT';
        }
      
        if($main_currency == 'ETH' && $currency == 'BTC' && $type == 'ask'){
            $pair = 'BTC-ETH';
            $type = 'bid';
        }else if($main_currency == 'ETH' && $currency == 'BTC' && $type == 'bid'){
            $pair = 'BTC-ETH';
            $type = 'ask';
        }else{
            $pair = $main_currency.'-'.$currency;
        }
        $apikey= $key; //'0a839d84c30e44ec9d459f0174d631de';
        $apisecret= $sec; //'a0ddff786256416a83972c91fbe5a6a3'; 
        $nonce=time(); 
        $uri='https://bittrex.com/api/v1.1/public/getticker?apikey='.$apikey.'&nonce='.$nonce.'&market='.$pair; 
        $sign=hash_hmac('sha512',$uri,$apisecret); 
        $ch = curl_init($uri); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign)); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $execResult = curl_exec($ch); 
        $obj = json_decode($execResult, true);
        if($type == 'bid'){
            return $obj['result']['Bid'];
        }else{
            return $obj['result']['Ask'];
        }
  }
 public function get_balance_bittrex($key, $sec, $currency, $user_id){
        $apikey= $key; // '0a839d84c30e44ec9d459f0174d631de';
        $apisecret= $sec; // 'a0ddff786256416a83972c91fbe5a6a3'; 
        $nonce=time(); 
        $uri='https://bittrex.com/api/v1.1/account/getbalance?apikey='.$apikey.'&nonce='.$nonce.'&currency='.$currency; 
        $sign=hash_hmac('sha512',$uri,$apisecret); 
        $ch = curl_init($uri); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign)); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $execResult = curl_exec($ch); 
        $obj = json_decode($execResult, true);
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Bittrex', 'apiResponse' => $obj));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $obj['result']['Available'];
 }
 public function get_deposit_address_bittrex($key, $sec, $currency, $user_id){
        //$key= '38a26580eb79457195f6f111175f7f35';
        //$sec= '9a485cee95f5498ea05cd6665b3c1b67'; 
        $nonce=time(); 
        $uri='https://bittrex.com/api/v1.1/account/getdepositaddress?apikey='.$key.'&nonce='.$nonce.'&currency='.$currency; 
        $sign=hash_hmac('sha512',$uri,$sec); 
        $ch = curl_init($uri); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign)); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $execResult = curl_exec($ch); 
        $obj = json_decode($execResult, true);
        
        if($obj['message'] == 'ADDRESS_GENERATING'){
            sleep(5);
            $this->get_deposit_address_bittrex($apikey, $sec, $currency, $user_id);
            exit;
        }
        
         //update app responce
         $api_res = json_encode(array('Exchange' => 'Bittrex', 'apiResponse' => $obj));
         $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
       
        return $obj['result']['Address'];
       
 }
 public function place_order_bittrex($key, $sec, $cur, $volume, $type, $user_id, $main_currency = null){
     if($main_currency == 'USD-USDT'){
            $main_currency = 'USDT';
        }
        $apikey= $key; //'0a839d84c30e44ec9d459f0174d631de';
        $apisecret= $sec; //'a0ddff786256416a83972c91fbe5a6a3';
        if($main_currency == 'ETH' && $cur == 'BTC' && $type == 'sell'){
            $pair = 'BTC-ETH';
            $type = 'buy';
        }else if($main_currency == 'ETH' && $cur == 'BTC' && $type == 'buy'){
            $pair = 'BTC-ETH';
            $type = 'sell';
        }else{
            $pair = $main_currency.'-'.$cur;
        }
        
        
        if($type == 'sell'){$side = 'bid'; $fun = 'selllimit';}else{ $side = 'ask'; $fun = 'buylimit';}
        $price = $this->get_price_bittrex($apikey, $apisecret, $cur, $side, $main_currency);
        sleep(2);
        $nonce=time(); 
        $uri='https://bittrex.com/api/v1.1/market/'.$fun.'?apikey='.$apikey.'&nonce='.$nonce.'&market=U'.$pair.'&quantity='.$volume.'&rate='.$price; 
        $sign=hash_hmac('sha512',$uri,$apisecret); 
        $ch = curl_init($uri); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign)); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $execResult = curl_exec($ch); 
        $obj = json_decode($execResult, true);
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Bittrex', 'apiResponse' => $obj));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        if($obj['message'] == 'INSUFFICIENT_FUNDS'){
            return false;
        }else{
            return $obj['result']['uuid'];
        }
        
 }
 public function withdraw_bittrex($key, $sec, $address, $cur, $amount, $user_id, $addresstag = null){
        $apikey= $key; //'0a839d84c30e44ec9d459f0174d631de';
        $apisecret= $sec; //'a0ddff786256416a83972c91fbe5a6a3'; 
        $nonce=time();
        if($addresstag != ''){
            $uri='https://bittrex.com/api/v1.1/account/withdraw?apikey='.$apikey.'&nonce='.$nonce.'&currency='.$cur.'&quantity='.$amount.'&address='.$address.'&paymentid='.$addresstag;
        }else{
            $uri='https://bittrex.com/api/v1.1/account/withdraw?apikey='.$apikey.'&nonce='.$nonce.'&currency='.$cur.'&quantity='.$amount.'&address='.$address;
        }
        $sign=hash_hmac('sha512',$uri,$apisecret); 
        $ch = curl_init($uri); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign)); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $execResult = curl_exec($ch); 
        $obj = json_decode($execResult, true);
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Bittrex', 'apiResponse' => $obj));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        if($obj['message'] == 'INSUFFICIENT_FUNDS'){
            return false;
        }else{
            return $obj['result']['uuid'];
        }
 }

///////////////////////////////////////////////////////// Independent Reserve Exchange ///////////////////////////////////////////////////  

 public function get_price_independentreserve($key, $sec, $currency, $type, $main_currency = null){
     if($currency == 'BTC'){
        $currency = 'xbt';
     }else{
        $currency = strtolower($currency);
     }
     $res = json_decode(file_get_contents('https://api.independentreserve.com/Public/GetMarketSummary?primaryCurrencyCode='.$currency.'&secondaryCurrencyCode=usd'));
     if($type == 'bid'){
         return $res->CurrentHighestBidPrice;
     }else{
         return $res->CurrentLowestOfferPrice;
     }
   }
 
 public function get_balance_independentreserve($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/IndepedentReserve.php';
        //$key = 'a0f158c7-6c86-40f7-ae4f-d36c56e9f1f2';
        //$sec = '9d87f80424d04bb49ad9549f974cbd11';
        $ir = new independentreserve($key, $sec);
        $res = $ir->get_accounts();
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'IndependentReserve', 'apiResponse' => $res));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        if($currency == 'BTC'){
            $currency = 'Xbt';
        }else{
            $currency = ucfirst(strtolower($currency));
        }
        foreach($res as $re){
            if($re->CurrencyCode==$currency && $re->AccountStatus=='Active'){
                return $re->AvailableBalance;
                
            }
        }
       
 }
  
 public function get_deposit_address_independentreserve($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/IndepedentReserve.php';
        //$key = '105e0eb3-19d3-4750-87b0-52a01e49aad7';
        //$sec = '7f660e6b8352404497aa186744d9dccc';
        if($currency == 'BTC'){
            $currency = 'Xbt';
        }else{
            $currency = ucfirst(strtolower($currency));
        }
        $ir = new independentreserve($key, $sec);
        $res = $ir->get_deposit_address($currency);
        //update app responce
        $api_res = json_encode(array('Exchange' => 'IndependentReserve', 'apiResponse' => $res));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $res->DepositAddress;
 } 
 
 public function place_order_independentreserve($key, $sec, $currency, $volume, $type, $user_id, $main_currency = null){
        require_once APPPATH . 'libraries/IndepedentReserve.php';
        //$key = 'a0f158c7-6c86-40f7-ae4f-d36c56e9f1f2';
        //$sec = '9d87f80424d04bb49ad9549f974cbd11';
        $ir = new independentreserve($key, $sec);
        if($currency == 'BTC'){
            $currency = 'Xbt';
        }else{
            $currency = ucfirst(strtolower($currency));
        }
        //["MarketBid","MarketOffer"]
        if($type == 'sell'){
            $orderType = 'MarketOffer';
        }else{
            $orderType = 'MarketBid';
        }
        $res = $ir->place_order($currency, $orderType, $volume);
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'IndependentReserve', 'apiResponse' => $res));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        
        //return $res;
        return $res->OrderGuid;
 }
 
 public function withdraw_independentreserve($key, $sec, $address, $currency, $amount, $user_id, $addresstag = null){
        require_once APPPATH . 'libraries/IndepedentReserve.php';
        //$key = '105e0eb3-19d3-4750-87b0-52a01e49aad7';
        //$sec = '7f660e6b8352404497aa186744d9dccc';
        $ir = new independentreserve($key, $sec);
        if($currency == 'BTC'){
            $currency = 'Xbt';
        }else{
            $currency = ucfirst(strtolower($currency));
        }
        if($addresstag != ''){
            $res = $ir->withdraw($amount, $address, $currency, $addresstag);
        }else{
            $res = $ir->withdraw($amount, $address, $currency);
        }
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'IndependentReserve', 'apiResponse' => $res));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        
        if($res == null){
            return 'in_withdraw';
        }else{
            return false;
        }
 }

//////////////////////////////////////////////////////// HUOBI EXCHANGE /////////////////////////////////////////////////////////////////
  public function get_price_huobi($key, $sec, $currency, $type, $main_currency = null){
      if($main_currency == 'USD-USDT'){
            $main_currency = 'USDT';
        }
        require_once APPPATH . 'libraries/Huobi.php';
        //$key = 'e921c24a-22d72fa6-b74ef2d0-85827';
        //$sec = 'c2649d08-93e08ccd-678884d9-0dcfa';
        $ho = new huobi($key, $sec);
        if($main_currency == 'ETH' && $currency == 'BTC' && $type == 'ask'){
            $pair = 'ethbtc';
            $type = 'bid';
        }else if($main_currency == 'ETH' && $currency == 'BTC' && $type == 'bid'){
            $pair = 'ethbtc';
            $type = 'ask';
        }else{
            $pair = strtolower($currency.''.$main_currency);
        }
        
        $res = $ho->get_market_depth($pair, 'step1');
        if($type == 'bid'){
           return $res->tick->bids[0][0];
        }else{
           return $res->tick->asks[0][0];
        }
    
  }
 public function get_balance_huobi($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/Huobi.php';
        //$key = 'e921c24a-22d72fa6-b74ef2d0-85827';
        //$sec = 'c2649d08-93e08ccd-678884d9-0dcfa';
        $ho = new huobi($key, $sec);
        $acc_id = $ho->get_account_accounts();
        $id = $acc_id->data[0]->id;
        $res = $ho->get_account_balance($id);
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Huobi', 'apiResponse' => $res));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        
        $currency = strtolower($currency);
        foreach($res->data->list as $a){
            if($a->currency == $currency && $a->type == 'trade'){
                return $a->balance;
            }else{
                return false;
            }
        }
 }
 public function get_deposit_address_huobi($key, $sec, $currency, $user_id){
       return 'get from exchange';
 }
 public function place_order_huobi($key, $sec, $cur, $volume, $type, $user_id, $main_currency = null){
     if($main_currency == 'USD-USDT'){
            $main_currency = 'USDT';
        }
        require_once APPPATH . 'libraries/Huobi.php';
        //$key = 'e921c24a-22d72fa6-b74ef2d0-85827';
        //$sec = 'c2649d08-93e08ccd-678884d9-0dcfa';
        $ho = new huobi($key, $sec);
        $acc_id = $ho->get_account_accounts();
        $id = $acc_id->data[0]->id;
        if($main_currency == 'ETH' && $cur == 'BTC' && $type == 'sell'){
            $pair = 'ethbtc';
            $type = 'buy';
        }else if($main_currency == 'ETH' && $cur == 'BTC' && $type == 'buy'){
            $pair = 'ethbtc';
            $type = 'sell';
        }else{
            $pair = strtolower($cur.''.$main_currency);
        }
        
        if($type == 'sell'){ $type = 'sell-market'; } else{ $type = 'buy-market'; }
        $res = $ho->place_order($id, $volume, null, $pair, $type);
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Huobi', 'apiResponse' => $res));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        
        return $res->data;
 }
 public function withdraw_huobi($key, $sec, $address, $cur, $amount, $user_id, $addresstag = null){
        require_once APPPATH . 'libraries/Huobi.php';
        //$key = 'e921c24a-22d72fa6-b74ef2d0-85827';
        //$sec = 'c2649d08-93e08ccd-678884d9-0dcfa';
        $ho = new huobi($key, $sec);
        $currency = strtolower($cur);
        if($addresstag != ''){
            $tag = $addresstag;
        }else{
            $tag = false;
        }
        $res = $ho->withdraw_create($address, $amount, $currency, null, $tag);
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Huobi', 'apiResponse' => $res));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        
        return $res->data;
 }

   
///////////////////////////////////////////////////////// BITHUMB EXCHANGE ///////////////////////////////////////////////////////////////  
  public function get_price_bithumb($key, $sec, $currency, $type, $main_currency = null){
        require_once APPPATH . 'libraries/BithumbAPI.php';
        //$key = '8588a2139813525bbbf8b2797c89b1d0';
        //$sec = '62376bce9bc3c334d9f1f4e80ec8e49d';
        $api = new BithumbAPI($key, $sec);
        $result = $api->xcoinApiCall("/public/ticker/".$currency);
        
        if($type == 'bid'){
            return $result->data->buy_price;
        }else{
            return $result->data->sell_price;
        }
        
        //echo "<pre>";
        //print_r($result->data->buy_price);
  }
  
  public function get_balance_bithumb($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/BithumbAPI.php';
        $key = $key; //'36c046a2a8fc1fd0c329b0b680720949';
        $sec = $sec; //'37d1fb27969a0607cafe61469b6bb4ae';
        $api = new BithumbAPI($key, $sec);
        $rgParams['currency'] = $currency;
        
        $result = $api->xcoinApiCall("/info/balance", $rgParams);
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Bithumb', 'apiResponse' => $result));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        
        $name = 'available_'.strtolower($currency);
        
        return $result->data->$name;
  }
  public function get_deposit_address_bithumb($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/BithumbAPI.php';
        //$key = 'd59836ccb54ab369bbddbf05c89a60f1';
        //$sec = 'a9c881d4701388bf486bbffd7e977ac1';
        $api = new BithumbAPI($key, $sec);
        $rgParams['currency'] = $currency;
        
        $result = $api->xcoinApiCall("/info/wallet_address", $rgParams);
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Bithumb', 'apiResponse' => $result));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $result->data->wallet_address;
        //echo "<pre>";
        //print_r($result);
       
  }
  public function place_order_bithumb($key, $sec, $cur, $volume, $type, $user_id, $main_currency = null){
        require_once APPPATH . 'libraries/BithumbAPI.php';
        $key = $key; //'8588a2139813525bbbf8b2797c89b1d0';
        $sec = $sec; //'62376bce9bc3c334d9f1f4e80ec8e49d';
        $api = new BithumbAPI($key, $sec);
        $rgParams['units'] = $volume;
        $rgParams['currency'] = $cur;
        if($type == 'buy'){
            $result = $api->xcoinApiCall("/trade/market_buy", $rgParams);
        }else{
            $result = $api->xcoinApiCall("/trade/market_sell", $rgParams);
        }
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Bithumb', 'apiResponse' => $result));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        
        return $result->order_id;
        
  }
  public function withdraw_bithumb($key, $sec, $address, $cur, $amount, $user_id, $addresstag = null){
        require_once APPPATH . 'libraries/BithumbAPI.php';
        //$key = '8588a2139813525bbbf8b2797c89b1d0';
        //$sec = '62376bce9bc3c334d9f1f4e80ec8e49d';
        $api = new BithumbAPI($key, $sec);
        $rgParams['units'] = doubleval($amount);
        $rgParams['address'] = $address;
        $rgParams['currency'] = $cur;
        if($addresstag != ''){
            $rgParams['destination'] = $addresstag;
        }
        
        $result = $api->xcoinApiCall("/trade/btc_withdrawal", $rgParams);
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Bithumb', 'apiResponse' => $result));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        
        if($result->status == 0000){
           return 'in_withdrawBithumb'; 
        }else{
           return false; 
        }
       
    }

//////////////////////////////////////////////////////////// LIVECOIN EXCHANGE ////////////////////////////////////////////////////////////
   public function get_price_livecoin($key, $sec, $currency, $type, $main_currency = null){
       if($main_currency == 'USD-USDT'){
            $main_currency = 'USD';
        }
        require_once APPPATH . 'libraries/Livecoin.php';
        //$key = 'NY3HPtz3kvFunNjTNZNRcG4pb99kw1bW';
        //$sec = 'r1Y5aKyvWRbWWgeEXn9GHgMmQfCpkz52';
        $api = new Livecoin($key, $sec);
        if($main_currency == 'ETH' && $currency == 'BTC' && $type == 'ask'){
            $pair = 'ETH/BTC';
            $type = 'bid';
        }else if($main_currency == 'ETH' && $currency == 'BTC' && $type == 'bid'){
            $pair = 'ETH/BTC';
            $type = 'ask';
        }else{
            $pair = strtoupper($currency).'/'.strtoupper($main_currency);
        }
        $result = json_decode($api->getPrice($pair));
        
        if($type == 'bid'){
            return $result->max_bid;
        }else{
            return $result->min_ask;
        }
    }
   public function get_balance_livecoin($key, $sec, $currency, $user_id){
       require_once APPPATH . 'libraries/Livecoin.php';
        //$key = 'NY3HPtz3kvFunNjTNZNRcG4pb99kw1bW';
        //$sec = 'r1Y5aKyvWRbWWgeEXn9GHgMmQfCpkz52';
        $api = new Livecoin($key, $sec);
        $result = json_decode($api->getBalance($currency));
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Livecoin', 'apiResponse' => $result));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $result->value;
   }
   public function get_deposit_address_livecoin($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/Livecoin.php';
        //$key = 'NY3HPtz3kvFunNjTNZNRcG4pb99kw1bW';
        //$sec = 'r1Y5aKyvWRbWWgeEXn9GHgMmQfCpkz52';
        $api = new Livecoin($key, $sec);
        $result = json_decode($api->getDepositAddress($currency));
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Livecoin', 'apiResponse' => $result));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $result->wallet;
   }
   public function place_order_livecoin($key, $sec, $cur, $volume, $type, $user_id, $main_currency = null){
       if($main_currency == 'USD-USDT'){
            $main_currency = 'USD';
        }
        require_once APPPATH . 'libraries/Livecoin.php';
        //$key = 'NY3HPtz3kvFunNjTNZNRcG4pb99kw1bW';
        //$sec = 'r1Y5aKyvWRbWWgeEXn9GHgMmQfCpkz52';
        $api = new Livecoin($key, $sec);
        if($main_currency == 'ETH' && $cur == 'BTC' && $type == 'sell'){
            $pair = 'ETH/BTC';
            $type = 'buy';
        }else if($main_currency == 'ETH' && $cur == 'BTC' && $type == 'buy'){
            $pair = 'ETH/BTC';
            $type = 'sell';
        }else{
            $pair = strtoupper($cur).'/'.strtoupper($main_currency);
        }
        $result = json_decode($api->place_order ($type, $pair, $volume));
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Livecoin', 'apiResponse' => $result));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $result->orderId;
       
   }
   public function withdraw_livecoin($key, $sec, $address, $cur, $amount, $user_id, $addresstag = null){
        require_once APPPATH . 'libraries/Livecoin.php';
        //$key = 'NY3HPtz3kvFunNjTNZNRcG4pb99kw1bW';
        //$sec = 'r1Y5aKyvWRbWWgeEXn9GHgMmQfCpkz52';
        $api = new Livecoin($key, $sec);
        $result = json_decode($api->withdraw ($cur, $amount, $address));
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Livecoin', 'apiResponse' => $result));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $result->id;
        
   }
   

//////////////////////////////////////////////////////////// EXMO EXCHANGE ////////////////////////////////////////////////////////////
    public function get_price_exmo($key, $sec, $currency, $type, $main_currency = null){
        if($main_currency == 'USD-USDT'){
            $main_currency = 'USD';
        }
        require_once APPPATH . 'libraries/Exmo.php';
        $api = new Exmo($key, $sec);
        if($main_currency == 'ETH' && $currency == 'BTC' && $type == 'ask'){
            $pair = 'ETH_BTC';
            $type = 'bid';
        }else if($main_currency == 'ETH' && $currency == 'BTC' && $type == 'bid'){
            $pair = 'ETH_BTC';
            $type = 'ask';
        }else{
            $pair = strtoupper($currency).'_'.strtoupper($main_currency);
        }
        $result = $api->getPrice();
        if($type == 'bid'){
            return $result[$pair]['buy_price'];
        }else{
            return $result[$pair]['sell_price'];
        }
    }
    public function get_balance_exmo($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/Exmo.php';
        //$key = 'K-687e28594ea9758e3d42c756c186e94160c1f637';
        //$sec = 'S-deb1fee0d9bd0cd3dfc876089a8f49bc5e8c8698';
        $api = new Exmo($key, $sec);
        $result = $api->getBalance();
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Exmo', 'apiResponse' => $result['balances']));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $result['balances'][$currency];
    }
   public function get_deposit_address_exmo($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/Exmo.php';
        //$key = 'K-687e28594ea9758e3d42c756c186e94160c1f637';
        //$sec = 'S-deb1fee0d9bd0cd3dfc876089a8f49bc5e8c8698';
        $api = new Exmo($key, $sec);
        $result = $api->getDepositAddress();
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Exmo', 'apiResponse' => $result));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        if(isset($result[$currency])){
            return $result[$currency];
        }else{
            return false;
        }
    }
    public function place_order_exmo($key, $sec, $cur, $volume, $type, $user_id, $main_currency = null){
        if($main_currency == 'USD-USDT'){
            $main_currency = 'USD';
        }
        require_once APPPATH . 'libraries/Exmo.php';
        //$key = 'K-687e28594ea9758e3d42c756c186e94160c1f637';
        //$sec = 'S-deb1fee0d9bd0cd3dfc876089a8f49bc5e8c8698';
        $api = new Exmo($key, $sec);
        
        if($main_currency == 'ETH' && $cur == 'BTC' && $type == 'sell'){
            $symbol = 'ETH_BTC';
            $type = 'buy';
        }else if($main_currency == 'ETH' && $cur == 'BTC' && $type == 'buy'){
            $symbol = 'ETH_BTC';
            $type = 'sell';
        }else{
            $symbol = strtoupper($cur).'_'.strtoupper($main_currency);
        }
        
        if($type == 'buy'){$type_i = 'market_buy';}else{$type_i = 'market_sell';}
        $result = $api->place_order($type_i, $symbol, $volume);
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Exmo', 'apiResponse' => $result));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $result['order_id'];
    }
    public function withdraw_exmo($key, $sec, $address, $cur, $amount, $user_id, $addresstag = null){
        require_once APPPATH . 'libraries/Exmo.php';
        //$key = 'K-687e28594ea9758e3d42c756c186e94160c1f637';
        //$sec = 'S-deb1fee0d9bd0cd3dfc876089a8f49bc5e8c8698';
        $api = new Exmo($key, $sec);
        if($addresstag != ''){
            $result = $api->withdraw($cur, $amount, $address, $addresstag);
        }else{
            $result = $api->withdraw($cur, $amount, $address);
        }
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Exmo', 'apiResponse' => $result));
        $this->db->query("UPDATE mbot_temp SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        if(isset($result['task_id'])){
            return $result['task_id'];
        }else{
            return false;
        }
    }

//////////////////////////////////////////////////////////// current user functions ///////////////////////////////////////////////////////
public function cancel_trade(){
    $u_id = $this->session->userdata('u_id');
    if($u_id != ''){
        if($data = $this->db->query("SELECT * FROM mbot_temp WHERE user_id = ?", array($u_id))->row()){
             
             $data1 = json_decode($data->data);
             $data1 = json_decode(json_encode($data1), true);
             
             // remove user keys
             unset($data1['buy_exchange_key']);
             unset($data1['buy_exchange_sec']);
             unset($data1['sell_exchange_key']);
             unset($data1['sell_exchange_sec']);
            // stamp state when cancel
             $data1['cancel_state'] = $data->current_state;
            
             $jdata = json_encode($data1);
             
             $this->db->query('insert into  mbot_history (user_id, data, remark, api_res) values (?,?,?,?)', array($u_id, $jdata, 'cancel', $data->api_res));
             $this->db->query('DELETE FROM mbot_temp WHERE user_id = ?', array($u_id)); 
             echo 'deleted';
             exit;
            
         }else{
             echo "no record found";
             exit;
         }
    }else{
         echo "user not loged in";
         exit;
     }
}

public function user_current_trade(){
     $u_id = $this->session->userdata('u_id');
     if($u_id != ''){
         if($data = $this->db->query("SELECT * FROM mbot_temp WHERE user_id = ?", array($u_id))->row()){
             
             $data1 = json_decode($data->data);
             $data1 = json_decode(json_encode($data1), true);
             
             $new_data = array(
                 'user_id' => $data->user_id,
                 'current_state' => $data->current_state,
                 'time' => $data->create_at,
                 'api_res' => $data->api_res,
                 'data' => $data1
                 );
             
             echo json_encode($new_data); //print_r($new_data);
             exit;
         }else{
             echo "no record found";
         }
     }else{
         echo "user not loged in";
         exit;
     }
}


public function user_mbot_history(){
     $u_id = $this->session->userdata('u_id');
     if($u_id != ''){
         if($data = $this->db->query("SELECT * FROM mbot_history WHERE user_id = ?", array($u_id))->result()){
             
             $arr = array();
             foreach($data as $dat){
                 $new_data = null;
                 $data1 = json_decode($dat->data);
                 $data1 = json_decode(json_encode($data1), true);
                 $new_data = array(
                     'user_id' => $dat->user_id,
                     'time' => $dat->create_at,
                     'remark' => $dat->remark,
                     'api_res' => $dat->api_res, 
                     'data' => $data1
                     );
                     $arr[] = $new_data;
             }
             
             echo json_encode($arr); //print_r($new_data);
             exit;
         }else{
             echo "no record found";
         }
     }else{
         echo "user not loged in";
         exit;
     }
}

public function get_apikeys(){
    $u_id = $this->session->userdata('u_id');
    if($u_id != ''){
        $arr = array('buy_key' => '', 'buy_sec' => '', 'sell_key' => '', 'sell_sec' => '');
        $buy_exchange = strtolower($this->input->post('buy_exchange'));
        $sell_exchange = strtolower($this->input->post('sell_exchange'));
        if($buy = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ?  AND exchange = ?", array($u_id, $buy_exchange))->row()){
            $arr['buy_key'] = $buy->api_key;
            $arr['buy_sec'] = $this->encrypt->decode($buy->sec_key);
        }
        if($sell = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ?  AND exchange = ?", array($u_id, $sell_exchange))->row()){
            $arr['sell_key'] = $sell->api_key;
            $arr['sell_sec'] = $this->encrypt->decode($sell->sec_key);
        }
        
        echo json_encode($arr);
        exit;
        
    }else{
        echo "user not loged in";
        exit;
    }
}

//truncate extra decimal values
    public function getTruncatedValue ( $value, $precision )
    {
        $value = number_format($value, 20, '.', '');
        if($value < 0.000001){return 0;}
        //Casts provided value
        $value = ( string )$value;
        //Gets pattern matches
        preg_match( "/(-+)?\d+(\.\d{1,".$precision."})?/" , $value, $matches );
        //Returns the full pattern match
        return $matches[0];            
    }
   
   
}   // class close
