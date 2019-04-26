<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transferex extends MY_Controller {
    
    public function transfercoin_initial(){
        $u_id = $this->session->userdata('u_id'); 
        if($u_id != ''){
            $postdata = file_get_contents("php://input");
            $request = json_decode($postdata);
            
            //get exchanges name
            $from_exchange = strtolower($request->from_exchange);
            $to_exchange = strtolower($request->to_exchange);
            
            // if any of echange is btcmarket change it into btcmarkets
            if($from_exchange == 'btcmarket'){$from_exchange = 'btcmarkets';}
            if($to_exchange == 'btcmarket'){$to_exchange = 'btcmarkets';}
            
            // get trade currency and volume
            $currency = strtoupper($request->currency);
            
            // get user api keys
            // buy exchange keys
            if($from_ex_cred = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ? AND exchange = ?", array($u_id, $from_exchange))->row()){
                $from_exchange_key = $from_ex_cred->api_key;
                $from_exchange_sec = $this->encrypt->decode($from_ex_cred->sec_key);
            }else{
                echo "No Source exchange keys found in our record please add your api keys";
                exit;
            }
            // sell exchange keys
            if($to_ex_cred = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ? AND exchange = ?", array($u_id, $to_exchange))->row()){
                $to_exchange_key = $to_ex_cred->api_key;
                $to_exchange_sec = $this->encrypt->decode($to_ex_cred->sec_key);
            }else{
                echo "No Destinantion exchange keys found in our record please add your api keys";
                exit;
            }
            
            // insert data into db
             $data = array(
                 'from_exchange' => $from_exchange, 
                 'to_exchange' => $to_exchange,
                 'from_exchange_key' => $from_exchange_key,
                 'to_exchange_key' => $to_exchange_key,
                 'from_exchange_sec' => $from_exchange_sec,
                 'to_exchange_sec' => $to_exchange_sec,
                 'currency' => $currency
                 );
             
            $jdata = json_encode($data);
            if($this->db->query('select * from transferex_history WHERE user_id = ? AND status = ?', array($u_id, 0))->row()){
                echo "Already transfer request placed";
                exit;
            }else{
                $this->db->query('insert into transferex_history (user_id, data, current_state) values (?,?,?)', array($u_id, $jdata, 'initial'));
            }
            
            // get sell exchange deposit address
            
            $addresstag = null;
            
            if($to_exchange == 'btcmarkets'){
                 if($add = $this->db->query("SELECT * FROM mbot_ex_no_dp_address_api WHERE user_id = ? AND exchange = ? AND coin = ?", array($u_id, 'btcmarkets', $currency))->row()){
                     $withdraw_key = $add->deposit_address;
                     if($currency == 'XRP'){
                         $addresstag = $add->coin_tag;
                     }
                 }else{
                     echo "Btcmarket Deposit address not found. Kindly add your Btcmarkets deposit addresses";
                     exit;
                  }
            }else if($to_exchange == 'huobi'){
                if($add = $this->db->query("SELECT * FROM mbot_ex_no_dp_address_api WHERE user_id = ? AND exchange = ? AND coin = ?", array($u_id, 'huobi', $currency))->row()){
                     $withdraw_key = $add->deposit_address;
                     if($currency == 'XRP' || $currency == 'EOS' || $currency == 'XLM' || $currency == 'XMR'){
                         $addresstag = $add->coin_tag;
                     }
                 }else{
                     echo "Huobi Deposit address not found. Kindly add your Huobi deposit addresses";
                     exit;
                 }
            }else{
                if($currency == 'XRP' || $currency == 'EOS' || $currency == 'XLM' || $currency == 'XMR'){
                    if($add = $this->db->query("SELECT * FROM mbot_ex_no_dp_address_api WHERE user_id = ? AND exchange = ? AND coin = ?", array($u_id, $to_exchange, $currency))->row()){
                        $withdraw_key = $add->deposit_address;
                        $addresstag = $add->coin_tag;
                    }else{
                         echo "No address found in our record. Please add your Address and Destination Tag";
                         exit;
                     }
                }else{
                    $fun = 'get_deposit_address_'.$to_exchange;
                    $withdraw_key = $this->$fun($to_exchange_key, $to_exchange_sec, $currency, $u_id);
                    if($withdraw_key == ''){
                        echo "No address received from Destination exchange";
                        exit;
                    }
                }
            }
            
            // only if from exchange is kraken
            if($from_exchange == 'kraken'){
                $withdraw_key = 'mbot_'.$to_exchange.'_deposit_'.$currency;
            }
            
            // get balance of from exchange
            $fun = 'get_balance_'.$from_exchange;
            $blnc = $this->$fun($from_exchange_key, $from_exchange_sec, $currency, $u_id); 
            
            $new_data = array('withdraw_key' => $withdraw_key, 'addresstag' => $addresstag ,'from_blnc' => json_encode($blnc));
            $data = array_merge($data, $new_data);
            
            $jdata = json_encode($data);
            $this->db->query('update transferex_history set data = ? WHERE user_id = ?', array($jdata, $u_id));
            
            //if($blnc > 0){
                echo json_encode(array('balance' => $blnc));
                exit;
            // }else{
            //     echo 'not enough balance';
            //     exit;
            // }
            
        }
    }
    
    public function transfercoin(){
        $u_id = $this->session->userdata('u_id'); 
        if($u_id != ''){
            $postdata = file_get_contents("php://input");
            $request = json_decode($postdata);
            $volume = $request->volume;
            if($volume == ''){
                echo "volume empty";
                exit;
            }
            $volume = $this->getTruncatedValue($volume, 3).'';
            
            
            if($user_data = $this->db->query('select * from transferex_history WHERE user_id = ? and status = ?', array($u_id, 0))->row()){
                
               if($user_data->current_state !=''){
                   echo "already submited";
                   exit;
               }
                
                // to convert object into array
                $data = json_decode($user_data->data);
                $data = json_decode(json_encode($data),true);
                
                $withdraw_key = str_replace("\n","",$data['withdraw_key']);
                
                // check from exchange balance 
                $fun = 'get_balance_'.$data['from_exchange'];
                $blnc = $this->$fun($data['from_exchange_key'], $data['from_exchange_sec'], $data['currency'], $u_id);
                if($blnc < $volume){
                    echo "not enough balance";
                    exit;
                }
                
                // create function name 
                $fun = 'withdraw_'.$data['from_exchange'];
                //print_r($data); exit;
                $wihtdraw = $this->$fun($data['from_exchange_key'], $data['from_exchange_sec'], $data['currency'], $withdraw_key, $volume, $u_id, $data['addresstag']);
                       
                // merge arrays
                $new_data = array('withdraw_ref' => json_encode($wihtdraw));
                $data = array_merge($data, $new_data);
                // push new data in DB
                
                unset($data['from_exchange_key']);
                unset($data['from_exchange_sec']);
                unset($data['to_exchange_key']);
                unset($data['to_exchange_sec']);
                
                $jdata = json_encode($data);
                $this->db->query('update transferex_history set data = ?, current_state = ?, status = ? WHERE user_id = ?', array($jdata,'withdraw', 1, $u_id));
                echo "withdraw state";
            }
        }
    }
    
    public function cancel_transfer(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($data = $this->db->query("SELECT * FROM transferex_history WHERE user_id = ?", array($u_id))->row()){
                 
                 $data1 = json_decode($data->data);
                 $data1 = json_decode(json_encode($data1), true);
                 
                 // remove user keys
                 unset($data1['from_exchange_key']);
                 unset($data1['from_exchange_sec']);
                 unset($data1['to_exchange_key']);
                 unset($data1['to_exchange_sec']);
                // stamp state when cancel
                 $data1['cancel_state'] = $data->current_state;
                
                 $jdata = json_encode($data1);
                 
                 $this->db->query('update transferex_history set data = ?, current_state = ?, status = ?, remark = ? WHERE user_id = ?', array($jdata,'canceled', 1, 'cancel', $u_id)); 
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
    
    public function user_transfer_history(){
         $u_id = $this->session->userdata('u_id');
         if($u_id != ''){
             if($data = $this->db->query("SELECT * FROM transferex_history WHERE user_id = ? AND status = ?", array($u_id, 1))->result()){
                 
                 $arr = array();
                 foreach($data as $dat){
                     $new_data = null;
                     $data1 = json_decode($dat->data);
                     $data1 = json_decode(json_encode($data1), true);
                     $new_data = array(
                         'user_id' => $dat->user_id,
                         'time' => $dat->created_at,
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
    
    public function user_current_transfer(){
         $u_id = $this->session->userdata('u_id');
         if($u_id != ''){
             if($data = $this->db->query("SELECT * FROM transferex_history WHERE user_id = ? AND status = ?", array($u_id, 0))->row()){
                 
                 $data1 = json_decode($data->data);
                 $data1 = json_decode(json_encode($data1), true);
                 
                 $new_data = array(
                     'user_id' => $data->user_id,
                     'current_state' => $data->current_state,
                     'time' => $data->created_at,
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
    
////////////////////////////////////////////////////////////////////////// POLONIEX EXCHANGE ////////////////////////////////////////////////////////////////
 public function get_balance_poloniex($key, $sec, $cur, $user_id){
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
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        return $poloniex_balance_result[$cur];

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
    $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
    
    return $poloniex_balance_result[$currency];
    
  }
 public function withdraw_poloniex($key, $sec, $currency, $address, $amount, $user_id, $addresstag = null){
    require_once APPPATH . 'libraries/Poloniex.php';
    
    $poloniex_key = $key; //"U99MHVH8-1QL38JP2-NV5NDNAS-E1OU0PNL";
    $poloniex_secret = $sec; //"8c1c015e42cb711ac835b98fb04f016e5873805b4a9842886ef01d4158c324e33d3b87d22e0ad74605b25fbd08c2d282a123ae30d4d6388f00d61db954d02911";
        
    $poloniex_obj = new poloniex($poloniex_key , $poloniex_secret);
        
    $poloniex_balance_result = $poloniex_obj->withdraw($currency, $amount, $address, $addresstag);
    
    //update app responce
    $api_res = json_encode(array('Exchange' => 'Poloniex', 'apiResponse' => $poloniex_balance_result));
    $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
    
    return $poloniex_balance_result['response'];
    
 }

 

////////////////////////////////////////////////////////////////////////// KRAKEN EXCHANGE ////////////////////////////////////////////////////////////////
public function get_balance_kraken($key, $sec, $currency, $user_id)
   {
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
       $api_res = json_encode($res);
       $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
       
        $pair = null;
        if($currency == 'BTC'){
            $pair = 'XXBT';
        }else if($currency == 'BCH' || $currency == 'EOS' || $currency == 'XMR' || $currency == 'DASH' || $currency == 'XLM'){
            $pair = $currency;
        }else{
            $pair = 'X'.$currency;
        }
       return $res['result'][$pair];
       
   }

public function get_deposit_address_kraken($key, $sec, $cur,$user_id)
   {
       require_once APPPATH . 'libraries/Kraken.php';
       $key = $key; //'EHgGDnOOJ0qGD6Vo44u3PBo/rBbG18+H6nBaiyu6T8M/BApU8V22btCY';
       $secret = $sec; //'veUL2iuus/HmZCBIgnkh1Aiwl32SHQWdJSsUURLugrXRFRyDiz5b3c+kvmw59Hd3aESTin9MWHATtT4l5zUjAA==';

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
       $api_res = json_encode($res);
       $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
       return $res['result'][0]['address'];
       
   }
   
public function withdraw_kraken($key, $sec, $currency, $withdrew_key, $amount, $user_id, $addresstag = null)
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
       $api_res = json_encode($res);
       $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
       
      return $res;
   }
   

//////////////////////////////////////////////////////////////// Binance Exchange ////////////////////////////////////////////////////////////////////
public function get_balance_binance($key, $sec, $currency, $user_id){
       require_once APPPATH . 'libraries/Binance.php';
       $key = $key; //'sSOwdatSS6goMSEe6PmkpzN83Ec11XfPGMFXJtPDROEy6isyMAlR98uZKBw2OrsQ'; 
       $secret = $sec; //'Cbt06DTCZSSQgw8Q98emn2hbDLtkO1PpcYKEruO2GBNUzrSznmbpXZQ380mGg7vS';
       if($currency == 'BCH'){
           $currency='BCC';
       }
       $binance = new Binance($key, $secret);
       $res = $binance->account();
       $blnc = 0;
       foreach($res['balances'] as $asset){
           if($asset['asset'] == $currency){
               //update app responce
               $api_res = json_encode($asset);
               $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
               $blnc = $asset['free'];
           }
       }
      
      return $blnc;
    }

public function get_deposit_address_binance($key, $sec, $cur, $user_id){
       
       require_once APPPATH . 'libraries/Binance.php';
       
       $key = $key; // 'sSOwdatSS6goMSEe6PmkpzN83Ec11XfPGMFXJtPDROEy6isyMAlR98uZKBw2OrsQ';
       $secret = $sec; // 'Cbt06DTCZSSQgw8Q98emn2hbDLtkO1PpcYKEruO2GBNUzrSznmbpXZQ380mGg7vS'; 
       if($cur == 'BCH'){
           $cur='BCC';
       }
       $binance = new Binance($key, $secret);
       $res = $binance->depositAddress($cur);
       
        //update app responce
       $api_res = json_encode($res);
       $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
       
       
       return $res['address'];
       
   }
   
public function withdraw_binance($key, $sec, $currency, $withdrew_key, $amount, $user_id, $addresstag = null){
       require_once APPPATH . 'libraries/Binance.php';

       $key = $key; //'sSOwdatSS6goMSEe6PmkpzN83Ec11XfPGMFXJtPDROEy6isyMAlR98uZKBw2OrsQ';
       $secret = $sec; //'Cbt06DTCZSSQgw8Q98emn2hbDLtkO1PpcYKEruO2GBNUzrSznmbpXZQ380mGg7vS'; 
        
       $binance = new Binance($key, $secret);
       if($currency == 'BCH'){
           $currency='BCC';
       }
       if($addresstag != ''){
            $tag = $addresstag;
        }else{
            $tag = false;
        }
       $res = $binance->withdraw($currency, $withdrew_key, $amount, $tag);
       
       //update app responce
       $api_res = json_encode($res);
       $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
       
       //if($res['success'] == 1){
        return $res['id'];
       //}else{
       //   return false;
       //}
   }
   
/////////////////////////////////////////////////////////// Btcmarkets Exchange ///////////////////////////////////////////////////////////
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
        $api_res = json_encode($arr);
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        
        foreach($arr as $a){
            if($a->currency == $currancy){
                $blnc = $a->balance/100000000; 
            }
        }
        
       return $blnc;
    }

public function get_deposit_address_btcmarkets($key, $sec, $cur, $user_id){
    return "get address from btcmarkets exchange";
}

public function withdraw_btcmarkets($key, $sec, $cur, $address, $amount, $user_id, $addresstag = null){
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
            $api_res = json_encode($arr);
            $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
            
            if($arr->success){
                return 'Success';
            }else{
                return 'Fail';
            }
            
    }

//////////////////////////////////////////////////////// HITBTC EXCHANGE //////////////////////////////////////////////////////////////////
public function get_balance_hitbtc($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/hitBtc.php';
        //$key = 'e7c777f718ddc71b907c4258ee48d106';
        //$sec =  'e77dbec975ccf50b721f0cf4da4b725c';
        $hitbtc = new hitBtc($key, $sec);
        $res = json_decode($hitbtc->getBalance());
        
        foreach($res as $a){
            if($a->currency == $currency){
                //update app responce
                $api_res = json_encode($a);
                $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
                return $a->available;
            }
        }
  }

public function get_deposit_address_hitbtc($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/hitBtc.php';
        //$key = 'e7c777f718ddc71b907c4258ee48d106';
        //$sec =  'e77dbec975ccf50b721f0cf4da4b725c';
        $hitbtc = new hitBtc($key, $sec);
        $res = json_decode($hitbtc->getDepositAddress($currency));
        
         //update app responce
       $api_res = json_encode($res);
       $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        return $res->address;
  }

public function withdraw_hitbtc($key, $sec, $cur, $address, $amount, $user_id, $addresstag = null){
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
        $api_res = json_encode($res);
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        return $res->id;
      
  }
  
//////////////////////////////////////////////////////// BITTREX EXCHANGE /////////////////////////////////////////////////////////////////
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
        $api_res = json_encode($obj);
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        return $obj['result']['Available'];
    }
 
public function get_deposit_address_bittrex($key, $sec, $currency, $user_id){
        $apikey= $key; //'0a839d84c30e44ec9d459f0174d631de';
        $apisecret= $sec; // 'a0ddff786256416a83972c91fbe5a6a3'; 
        $nonce=time(); 
        $uri='https://bittrex.com/api/v1.1/account/getdepositaddress?apikey='.$apikey.'&nonce='.$nonce.'&currency='.$currency; 
        $sign=hash_hmac('sha512',$uri,$apisecret); 
        $ch = curl_init($uri); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign)); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $execResult = curl_exec($ch); 
        $obj = json_decode($execResult, true);
        
         //update app responce
       $api_res = json_encode($obj);
       $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        if($obj['message'] == 'ADDRESS_GENERATING'){
            sleep(10);
            $this->get_deposit_address_bittrex($apikey, $apisecret, $currency);
        }else{
            return $obj['result']['Address'];
        }
    }

public function withdraw_bittrex($key, $sec, $cur, $address, $amount, $user_id, $addresstag = null){
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
        $api_res = json_encode($obj);
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        if($obj['message'] == 'INSUFFICIENT_FUNDS'){
            return false;
        }else{
            return $obj['result']['uuid'];
        }
    }
 
///////////////////////////////////////////////////////// Independent Reserve Exchange ///////////////////////////////////////////////////
 public function get_balance_independentreserve($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/IndepedentReserve.php';
        //$key = 'a0f158c7-6c86-40f7-ae4f-d36c56e9f1f2';
        //$sec = '9d87f80424d04bb49ad9549f974cbd11';
        $ir = new independentreserve($key, $sec);
        $res = $ir->get_accounts();
        
        //update app responce
        $api_res = json_encode($res);
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
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
       $api_res = json_encode($res);
       $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        return $res->DepositAddress;
    } 
    
public function withdraw_independentreserve($key, $sec, $currency, $address, $amount, $user_id, $addresstag = null){
        require_once APPPATH . 'libraries/IndepedentReserve.php';
        //$key = '105e0eb3-19d3-4750-87b0-52a01e49aad7';
        //$sec = '7f660e6b8352404497aa186744d9dccc';
        $ir = new independentreserve($key, $sec);
        if($currency == 'BTC'){
            $currency = 'Xbt';
        }else{
            $currency = ucfirst(strtolower($currency));
        }
        $res = $ir->withdraw($amount, $address, $currency);
        
        //update app responce
        $api_res = json_encode($res);
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        
        if($res == null){
            return 'in_withdraw';
        }else{
            return false;
        }
 }

//////////////////////////////////////////////////////// HUOBI EXCHANGE /////////////////////////////////////////////////////////////////
public function get_balance_huobi($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/Huobi.php';
        //$key = 'e921c24a-22d72fa6-b74ef2d0-85827';
        //$sec = 'c2649d08-93e08ccd-678884d9-0dcfa';
        $ho = new huobi($key, $sec);
        $acc_id = $ho->get_account_accounts();
        $id = $acc_id->data[0]->id;
        $res = $ho->get_account_balance($id);
        
        //update app responce
        $api_res = json_encode($res);
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        
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
       return 'get address from huobi exchange';
    }

public function withdraw_huobi($key, $sec, $cur, $address, $amount, $user_id, $addresstag = null){
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
        $api_res = json_encode($res);
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        
        return $res->data;
    }

///////////////////////////////////////////////////////// BITHUMB EXCHANGE /////////////////////////////////////////////////////////////// 
public function get_balance_bithumb($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/BithumbAPI.php';
        $key = $key; //'36c046a2a8fc1fd0c329b0b680720949';
        $sec = $sec; //'37d1fb27969a0607cafe61469b6bb4ae';
        $api = new BithumbAPI($key, $sec);
        $rgParams['currency'] = $currency;
        
        $result = $api->xcoinApiCall("/info/balance", $rgParams);
        
        //update app responce
        $api_res = json_encode($result);
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        
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
       $api_res = json_encode($result);
       $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        return $result->data->wallet_address;
        //echo "<pre>";
        //print_r($result);
       
    }

public function withdraw_bithumb($key, $sec, $cur, $address, $amount, $user_id, $addresstag = null){
        require_once APPPATH . 'libraries/BithumbAPI.php';
        //$key = '8588a2139813525bbbf8b2797c89b1d0';
        //$sec = '62376bce9bc3c334d9f1f4e80ec8e49d';
        $api = new BithumbAPI($key, $sec);
        $rgParams['units'] = $amount;
        $rgParams['address'] = $address;
        $rgParams['currency'] = $cur;
        if($addresstag != ''){
            $rgParams['destination'] = $addresstag;
        }
        
        $result = $api->xcoinApiCall("/trade/btc_withdrawal", $rgParams);
        
        //update app responce
        $api_res = json_encode($result);
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ? and status = 0", array($api_res, $user_id));
        
        
        if($result->status == 0000){
           return 'in_withdrawBithumb'; 
        }else{
           return false; 
        }
    }


//////////////////////////////////////////////////////////// LIVECOIN EXCHANGE ////////////////////////////////////////////////////////////
   
   public function get_balance_livecoin($key, $sec, $currency, $user_id){
       require_once APPPATH . 'libraries/Livecoin.php';
        //$key = 'NY3HPtz3kvFunNjTNZNRcG4pb99kw1bW';
        //$sec = 'r1Y5aKyvWRbWWgeEXn9GHgMmQfCpkz52';
        $api = new Livecoin($key, $sec);
        $result = json_decode($api->getBalance($currency));
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Livecoin', 'apiResponse' => $result));
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
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
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $result->wallet;
   }
  
   public function withdraw_livecoin($key, $sec, $address, $cur, $amount, $user_id, $addresstag = null){
        require_once APPPATH . 'libraries/Livecoin.php';
        //$key = 'NY3HPtz3kvFunNjTNZNRcG4pb99kw1bW';
        //$sec = 'r1Y5aKyvWRbWWgeEXn9GHgMmQfCpkz52';
        $api = new Livecoin($key, $sec);
        $result = json_decode($api->withdraw ($cur, $amount, $address));
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Livecoin', 'apiResponse' => $result));
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        return $result->id;
        
   }
   

//////////////////////////////////////////////////////////// EXMO EXCHANGE ////////////////////////////////////////////////////////////
   
    public function get_balance_exmo($key, $sec, $currency, $user_id){
        require_once APPPATH . 'libraries/Exmo.php';
        //$key = 'K-687e28594ea9758e3d42c756c186e94160c1f637';
        //$sec = 'S-deb1fee0d9bd0cd3dfc876089a8f49bc5e8c8698';
        $api = new Exmo($key, $sec);
        $result = $api->getBalance();
        
        //update app responce
        $api_res = json_encode(array('Exchange' => 'Exmo', 'apiResponse' => $result['balances']));
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
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
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        if(isset($result[$currency])){
            return $result[$currency];
        }else{
            return false;
        }
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
        $this->db->query("UPDATE transferex_history SET api_res = ? WHERE user_id = ?", array($api_res, $user_id));
        
        if(isset($result['task_id'])){
            return $result['task_id'];
        }else{
            return false;
        }
    }



///////////////////////////////////////////////////// other functions ////////////////////////////////////////////////////////
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

    
} // class end 
