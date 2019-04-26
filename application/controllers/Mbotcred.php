<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mbotcred extends MY_Controller {
     public function get_exchanges(){
        $u_id = $this->session->userdata('u_id'); 
        if($u_id != ''){
            $aa = json_encode($this->input->post('exchanges'));
            if($this->DB3->query("SELECT * FROM mbot_user_exchanges WHERE user_id = ?", array($u_id))->row()){
                if($this->DB3->query("UPDATE mbot_user_exchanges SET exchanges = ? WHERE user_id = ?", array($aa, $u_id))){
                    echo "success";
                }else{
                    echo "fail";
                }
            }else{
                if($this->DB3->query("INSERT INTO mbot_user_exchanges (exchanges, user_id) values (?,?)", array($aa, $u_id))){
                    echo "success";
                }else{
                    echo "fail";
                }
            }
            
            exit;
        }
    }
    
    
    public function getCred(){
        $u_id = $this->session->userdata('u_id'); 
        if($u_id != ''){
        
            $exchange = $this->input->post('exchange');
            $apikey = $this->input->post('apikey');
            $seckey = $this->input->post('seckey');
            $coins = $this->input->post('coins');
          
            if($exchange != null && $apikey != null && $seckey != null){
                // $this->check_api_keys($u_id,$exchange,$apikey,$seckey);
                // exit;
                
                // if($exchange == 'binance' && strpos($this->check_api_keys($u_id,$exchange,$apikey,$seckey),"Request error") > 0){
                //     print_r($this->check_api_keys($u_id,$exchange,$apikey,$seckey));
                //     exit; 
                // }
            
                // else 
                if(!$this->check_api_keys($u_id,$exchange,$apikey,$seckey)){
                    echo "Api Key not valid";
                    exit;
                }
                $seckey = $this->encrypt->encode($seckey);
                
                if($this->db->query("SELECT * FROM mbot_cred WHERE user_id = ? AND exchange = ?", array($u_id, $exchange))->row()){
                    $this->db->query("UPDATE mbot_cred SET api_key = ?, sec_key = ? WHERE user_id = ? AND exchange = ?", array($apikey, $seckey, $u_id, $exchange));
                }else{
                    $this->db->query("insert into mbot_cred (user_id, exchange, api_key, sec_key) values (?,?,?,?)", array($u_id, $exchange, $apikey, $seckey));
                }
                
                if($coins != null){
                    foreach($coins as $key => $coin){
                        if($this->db->query("SELECT * FROM mbot_ex_no_dp_address_api WHERE user_id = ? AND exchange = ? AND coin = ?", array($u_id, $exchange, $key))->row()){
                            $this->db->query("UPDATE mbot_ex_no_dp_address_api SET deposit_address = ? , coin_tag = ? WHERE user_id = ? AND exchange = ? AND coin = ?", array($coin['address'], $coin['tag'], $u_id, $exchange, $key));
                        }else{
                            $this->db->query("INSERT into mbot_ex_no_dp_address_api (user_id, exchange, coin, deposit_address, coin_tag) values (?,?,?,?,?)", array($u_id, $exchange, $key, $coin['address'], $coin['tag']));
                        }
                    } 
                }
                echo "success";
                exit;
            }
            else{
                echo "failed to save keys";
                exit;
            }
        }
    }
    
    
    public function getCred_trading(){
        $u_id = $this->session->userdata('u_id'); 
        if($u_id != ''){
            
            $postdata = file_get_contents("php://input");
            $request = json_decode($postdata);
            
            $exchange = $request->exchange;
            $apikey = $request->apikey;
            $seckey = $request->seckey;
            if($exchange != null && $apikey != null && $seckey != null){
                
                if(!$this->check_api_keys($u_id,$exchange,$apikey,$seckey)){
                    echo json_encode(array('error'=>'1', 'msg'=>'Api Key not valid')); exit;
                }
                $seckey = $this->encrypt->encode($seckey);
                if($this->db->query("SELECT * FROM mbot_cred WHERE user_id = ? AND exchange = ?", array($u_id, $exchange))->row()){
                    $this->db->query("UPDATE mbot_cred SET api_key = ?, sec_key = ? WHERE user_id = ? AND exchange = ?", array($apikey, $seckey, $u_id, $exchange));
                }else{
                    $this->db->query("insert into mbot_cred (user_id, exchange, api_key, sec_key) values (?,?,?,?)", array($u_id, $exchange, $apikey, $seckey));
                }
                
                echo json_encode(array('success'=>'1', 'msg'=>'Successfully added')); exit;
            }
            else{
                echo json_encode(array('error'=>'1', 'msg'=>'Faild to save keys')); exit;
            }
        }
    }

    public function getBlnc(){
        $u_id = $this->session->userdata('u_id'); 
        //$u_id = 13870;
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $exchange = strtolower($request->exchange);
        //$exchange = 'exmo';
        // if any of echange is btcmarket change it into btcmarkets
        if($exchange == 'btcmarket'){$exchange = 'btcmarkets';}
        if($u_id != '' && $exchange != ''){
           if($res = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ? AND exchange = ?", array($u_id, $exchange))->row()){
                $fun = 'get_balance_'.$exchange;
                $arr = $this->$fun($res->api_key, $this->encrypt->decode($res->sec_key));
                if(!is_array($arr)){
                    echo 'empty';
                    exit;
                }else{
                    echo json_encode($arr);
                }
            }else{
                echo "User have no exchange data";
                exit;
            }
          //print_r($arr);  
        }
        else{
            echo "Invalid Exchange.";
        }
    }

    // public function getUsersMbotCred(){
    //     $u_id = $this->session->userdata('u_id');
    //     if($u_id != ''){
    //         $res = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ?", array($u_id))->result();
    //         $main_arr = array();
    //         foreach($res as $key => $one){
    //             $dp_addresses = $this->db->query("SELECT * FROM mbot_ex_no_dp_address_api WHERE user_id = ? AND exchange = ?", array($u_id, $one->exchange))->result();
    //             $coin_arr = array();
    //             foreach($dp_addresses as $a){
    //                 $coin_arr[$a->coin]['address'] = $a->deposit_address;
    //                 $coin_arr[$a->coin]['tag'] = $a->coin_tag;
    //             }
    //             $main_arr[$one->exchange]['apikey'] = $one->api_key;
    //             $main_arr[$one->exchange]['seckey'] = $one->sec_key;
    //             $main_arr[$one->exchange]['coins'] = $coin_arr;
    //         }
    //         $main_arr = json_encode($main_arr);
    //         print_r($main_arr);  
    //     }
    //     else{
    //         echo "Invalid User.";
    //     }
    // }
        
    public function check_api_keys($u_id, $exchange, $apikey, $seckey){
        if($u_id != ''){
            $fun = "get_balance_".$exchange;
            //if($exchange == 'binance'){return json_decode($this->$fun($apikey,$seckey));}
            $responce = $this->$fun($apikey,$seckey);
            if(!is_array($responce)){
                return false;
            }else{
                return true;
            }
        }
    }

//------------------------------------------ EXCHANGES GET BALANCE FUNCTIONS ------------------------------------------------------------//

public function get_balance_poloniex($key, $sec){
        require_once APPPATH.'libraries/Poloniex.php';
        $poloniex_key = $key; //"U99MHVH8-1QL38JP2-NV5NDNAS-E1OU0PNL";
        $poloniex_secret = $sec; //"8c1c015e42cb711ac835b98fb04f016e5873805b4a9842886ef01d4158c324e33d3b87d22e0ad74605b25fbd08c2d282a123ae30d4d6388f00d61db954d02911";
        $poloniex_obj = new poloniex($poloniex_key, $poloniex_secret);
        
        $poloniex_balance_result = $poloniex_obj->get_balances();
        
        if($poloniex_balance_result['error'] != ''){return 'false';}
        else{return $poloniex_balance_result;}
 }
 
 public function get_balance_kraken($key, $sec){
       require_once APPPATH . 'libraries/Kraken.php';
       $key = $key; //'hnXMLwjdBAGzCcYcKPQLvwt9+1tcO46APELYjYJmWN/oz06oVPDHmJwK';
       $secret = $sec; //'Uy3bl2UtgpKrMFgg4/jahK/bSHPiEZixNIo+81OUL2O2JkTJh5Z6sqMsCBPonEg/g/BxgdBX5R44Cn3M8Bik/Q==';

       $beta = false;
       $url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
       $sslverify = $beta ? false : true;
       $version = 0;
       $kraken = new Kraken($key, $secret, $url, $version, $sslverify);
       $res = $kraken->QueryPrivate('Balance');
       
       return $res['result'];
   }

public function get_balance_binance($key, $sec){
       require_once APPPATH . 'libraries/Binance.php';
       $key = $key; //'sSOwdatSS6goMSEe6PmkpzN83Ec11XfPGMFXJtPDROEy6isyMAlR98uZKBw2OrsQ'; //'GBUtLHvAFUslJ6jrZD3EMGTlVBaNrBMaM83vSSGuAa5b0sK98ibfSC6kfze7aTOz';
       $secret = $sec; //'Cbt06DTCZSSQgw8Q98emn2hbDLtkO1PpcYKEruO2GBNUzrSznmbpXZQ380mGg7vS'; //'QXtv01sYHCm71zW7jK5yRvFiFqpCc8TgVEbPdDOE1nba2IWXffCrFKCkRNS0SlZc';
       $binance = new Binance($key, $secret);

       $res = $binance->account();
       if(isset($res['msg'])){
           return 'error';
       }
       $blnc = 0;
       $arr = array();
       foreach($res['balances'] as $asset){
           $arr[$asset['asset']] = $asset['free'];
       }
       return $arr;
   }
   
public function get_balance_btcmarkets($key, $sec){
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
        $arr2 = array();
        foreach($arr as $a){
            if(!isset($a->currency)){
                return false;
            }
            $arr2[$a->currency] = $a->balance/100000000;
            /*if($a->currency == $currancy){
                $blnc = $a->balance/100000000; 
            }*/
        }
        
       return $arr2;
    }

public function get_balance_hitbtc($key, $sec){
        require_once APPPATH . 'libraries/hitBtc.php';
        //$key = 'e7c777f718ddc71b907c4258ee48d106';
        //$sec =  'e77dbec975ccf50b721f0cf4da4b725c';
        $hitbtc = new hitBtc($key, $sec);
        $res = json_decode($hitbtc->getBalance());
        
        $arr = array();
        foreach($res as $a){
            if(!isset($a->currency)){
                return false;
            }
            $arr[$a->currency] = $a->available;
        }
        return $arr;
  }
  
public function get_balance_bithumb($key, $sec){
        require_once APPPATH . 'libraries/BithumbAPI.php';
        $key = $key; //'36c046a2a8fc1fd0c329b0b680720949';
        $sec = $sec; //'37d1fb27969a0607cafe61469b6bb4ae';
        $api = new BithumbAPI($key, $sec);
        $arr = array();
        $result = $api->xcoinApiCall("/info/balance", array('currency' => 'BTC'));
        if(!isset($result->data->available_krw)){
            return false;
        }
        $arr['KRW'] = $result->data->available_krw;
        $arr['BTC'] = $result->data->available_btc;
       
        $result = $api->xcoinApiCall("/info/balance", array('currency' => 'ETH'));
        $arr['ETH'] = $result->data->available_eth;
        $result = $api->xcoinApiCall("/info/balance", array('currency' => 'BCH'));
        $arr['BCH'] = $result->data->available_bch;
        sleep(1);
        $result = $api->xcoinApiCall("/info/balance", array('currency' => 'LTC'));
        $arr['LTC'] = $result->data->available_ltc;
        $result = $api->xcoinApiCall("/info/balance", array('currency' => 'XRP'));
        $arr['XRP'] = $result->data->available_xrp;
        $result = $api->xcoinApiCall("/info/balance", array('currency' => 'EOS'));
        $arr['EOS'] = $result->data->available_eos;
        $result = $api->xcoinApiCall("/info/balance", array('currency' => 'XMR'));
        sleep(1);
        $arr['XMR'] = $result->data->available_xmr;
        $result = $api->xcoinApiCall("/info/balance", array('currency' => 'DASH'));
        $arr['DASH'] = $result->data->available_dash;
        $result = $api->xcoinApiCall("/info/balance", array('currency' => 'ADA'));
        $arr['ADA'] = $result->data->available_ada;
        //$name = 'available_'.strtolower($currency);
        
        return $arr;
  }
  
public function get_balance_bittrex($key, $sec){
        //$apikey=  '38a26580eb79457195f6f111175f7f35';
        //$apisecret= '9a485cee95f5498ea05cd6665b3c1b67'; 
        $nonce=time(); 
        $uri='https://bittrex.com/api/v1.1/account/getbalances?apikey='.$key.'&nonce='.$nonce; 
        $sign=hash_hmac('sha512',$uri,$sec); 
        $ch = curl_init($uri); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign)); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $execResult = curl_exec($ch); 
        $obj = json_decode($execResult, true);
        if(!isset($obj['result'])){
               return false;;
            }
        $arr = array();
        foreach($obj['result'] as $a){
            
            $arr[$a['Currency']] = $a['Available'];
        }
        
        return $arr;
        
 }
  
public function get_balance_huobi($key, $sec){
        require_once APPPATH . 'libraries/Huobi.php';
        //$key = 'e921c24a-22d72fa6-b74ef2d0-85827';
        //$sec = 'c2649d08-93e08ccd-678884d9-0dcfa';
        $ho = new huobi($key, $sec);
        $acc_id = $ho->get_account_accounts();
        $id = $acc_id->data[0]->id;
        $res = $ho->get_account_balance($id);
        //$currency = strtolower($currency);
        
        if($res->status == 'error'){
            return false;
        }
        
        $arr = array();
        foreach($res->data->list as $a){
           $arr[$a->currency] = $a->balance;
            
        }
        return $arr;
 }
 
 
 public function get_balance_livecoin($key, $sec){
       require_once APPPATH . 'libraries/Livecoin.php';
        //$key = 'NY3HPtz3kvFunNjTNZNRcG4pb99kw1bW';
        //$sec = 'r1Y5aKyvWRbWWgeEXn9GHgMmQfCpkz52';
        $api = new Livecoin($key, $sec);
        $result = json_decode($api->getBalances());
        if($result->errorCode != ''){
            return false;
        }
        $arr = array();
        foreach($result as $a){
            if($a->type == 'available'){
                $arr[$a->currency] = $a->value;
            }
        }
        return $arr;
   }
 public function get_balance_exmo($key, $sec){
        require_once APPPATH . 'libraries/Exmo.php';
        //$key = 'K-687e28594ea9758e3d42c756c186e94160c1f637';
        //$sec = 'S-deb1fee0d9bd0cd3dfc876089a8f49bc5e8c8698';
        $api = new Exmo($key, $sec);
        $result = $api->getBalance();
        if($result->error != ''){
            return false;
        }
        return $result['balances'];
    }
    
    
    public function get_balance_coinbene($key, $sec){
        require_once APPPATH . 'libraries/Coinbene.php';
        $coinbene = new Coinbene($key, $sec);
        $balances = $coinbene->balance();
        if($balances->status == 'error'){
            return false;
        }
        return $result['balances'];
    }


 
} // class end here