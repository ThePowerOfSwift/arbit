<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_exchanges_data extends MY_Controller {
    
    public function index_old(){
        $exchange = 'hitbtc';
        $pair = 'BTCUSDT';
        $key = '23cda657c47b738c96bd404ecb6524ef472f2d7e';
        $secret = 'fe3b8d848eb081300d4bf4c5e4b2f10e1d404d47';
        $date = $this->bibox_user_data($key, $secret, $pair);
        echo '<pre>';
        print_r($date);
        
    }
    
    
    public function index(){
        //$u_id = $this->input->post('u_id'); 
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $postdata = file_get_contents("php://input");
            $request = json_decode($postdata);
            $exchange = strtolower($request->exchange); //'binance';
            $pair = $request->pair; //'XRPUSDT';
            if($keys = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ? AND exchange = ?", array($u_id, $exchange))->row()){
                $key = $keys->api_key; //'pHYjwwv5hSvWTJSWK8lPM7Xv3J302bHZFSOlfi5o1hE2Fga6rJD0VWlFh5ihem48';
                $secret = $this->encrypt->decode($keys->sec_key); //'LC38DSmPl6hZRlFU3lZZi9VUiJijIzZNHFTzVwwwesjtZ61IUNngElwylK8qRJKr';
                $fun_name = $exchange.'_user_data';
                $data = $this->$fun_name($key, $secret, $pair);
                echo json_encode($data);
            }else{
                echo json_encode(array('error'=>'1', 'msg'=>'Keys not found'));
            }
            
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'session expired'));
        }
    }
    
    public function get_exchange_keys(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $postdata = file_get_contents("php://input");
            $request = json_decode($postdata);
            $exchange = strtolower($request->exchange);
            if($keys = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ? AND exchange = ?", array($u_id, $exchange))->row()){
                $arr = array('api' => $keys->api_key, 'sec' => $this->encrypt->decode($keys->sec_key));
                echo json_encode($arr); exit;
            }else{
                $arr = array('api' => '', 'sec' => '');
                echo json_encode($arr); exit;
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'session expired')); exit;
        }
    }
    
    
    public function cancel_order_exchange(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $postdata = file_get_contents("php://input");
            $request = json_decode($postdata);
            $exchange = strtolower($request->exchange); //'binance';
            $pair = $request->pair; //'XRPUSDT';
            $order_id =  $request->orderId;
            if($keys = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ? AND exchange = ?", array($u_id, $exchange))->row()){
                $key = $keys->api_key; 
                $secret = $this->encrypt->decode($keys->sec_key); 
                $fun_name = $exchange.'_cancel_order';
                if($data = $this->$fun_name($key, $secret, $pair, $order_id)){
                    echo json_encode(array('success'=>'1', 'msg'=>'Order deleted')); exit;
                }else{
                    echo json_encode(array('error'=>'1', 'msg'=>'Request fail')); exit;
                }
            }else{
                echo json_encode(array('error'=>'1', 'msg'=>'Keys not found')); exit;
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'session expired')); exit;
        }
        
    }
    
    
    public function place_trade(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $postdata = file_get_contents("php://input");
            $request = json_decode($postdata);
            
            //print_r($request); exit;
            $exchange = strtolower($request->exchange); //'binance';
            $pair = $request->pair; //'XRPUSDT';
            if($keys = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ? AND exchange = ?", array($u_id, $exchange))->row()){
                $key = $keys->api_key; //'pHYjwwv5hSvWTJSWK8lPM7Xv3J302bHZFSOlfi5o1hE2Fga6rJD0VWlFh5ihem48';
                $secret = $this->encrypt->decode($keys->sec_key); //'LC38DSmPl6hZRlFU3lZZi9VUiJijIzZNHFTzVwwwesjtZ61IUNngElwylK8qRJKr';
                $side = strtolower($request->side);
                $type =  strtolower($request->type);
                if($type == 'limit'){
                    $price = number_format(doubleval($request->price), 8);
                }else{
                    $price = 0;
                }
                $qty = doubleval($request->qty);
                $fun_name = $exchange.'_place_order';
                if($data = $this->$fun_name($key, $secret, $pair, $side, $type, $price, $qty)){
                    echo json_encode(array('success'=>'1', 'msg'=>'Order placed')); exit;
                }else{
                    echo json_encode(array('error'=>'1', 'msg'=>'Order fail')); exit;
                }
            }else{
                echo json_encode(array('error'=>'1', 'msg'=>'Keys not found')); exit;
            }
            
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'session expired')); exit;
        }
        
    }
    

    
    public function add_fav_pairs(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
             $postdata = file_get_contents("php://input");
             $request = json_decode($postdata);
            $exchange = $request->exchange;
            $pair = $request->pair;
            $base = $request->base;
            $quote = $request->quote;
            if($pair == '' || $base == '' || $quote == '' || $exchange == ''){exit;}
            
            $this->load->helper('cookie');
            if($this->input->cookie('fav_ex_pairs_'.$u_id, TRUE)){
                $old_cookie = json_decode($this->input->cookie('fav_ex_pairs_'.$u_id, TRUE), TRUE);
                delete_cookie('fav_ex_pairs_'.$u_id);
                if(isset($old_cookie[$exchange])){
                    if($old_cookie[$exchange][$pair]){
                        unset($old_cookie[$exchange][$pair]);
                    }else{
                        $old_cookie[$exchange][$pair] = array('symbol' => $pair, 'base'=>$base, 'quote'=>$quote);
                    }
                    
                }else{
                    //echo 'in_else';
                    $old_cookie[$exchange] = array($pair => array('symbol' => $pair, 'base'=>$base, 'quote'=>$quote));
                }
                
                // echo "<pre>";
                // print_r($old_cookie);
                // exit;
                $cook = array(
        		    'name' => 'fav_ex_pairs_'.$u_id,
        		    'value' => json_encode($old_cookie),
        		    'expire' => time() + (10 * 365 * 24 * 60 * 60),
                    'secure' => false
        		    );
        		$this->input->set_cookie($cook);
                
            }else{
                $cookarr = array($exchange => array($pair => array('symbol' => $pair, 'base'=>$base, 'quote'=>$quote)));
                $cook = array(
        		    'name' => 'fav_ex_pairs_'.$u_id,
        		    'value' => json_encode($cookarr),
        		    'expire' => time() + (10 * 365 * 24 * 60 * 60),
                    'secure' => false
        		    );
        		$this->input->set_cookie($cook);
            }
            
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'session expired')); exit;
        }
    }
    
     public function get_fav_pairs(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $this->load->helper('cookie');
            //delete_cookie('fav_ex_pairs_'.$u_id);
            if($this->input->cookie('fav_ex_pairs_'.$u_id, TRUE)){
               echo $old_cookie = $this->input->cookie('fav_ex_pairs_'.$u_id, TRUE);
            }else{
                echo json_encode(array());
            }
            
        }
     }
    
    
//------------------------------------ BIBOX FUNCTION --------------------------------------//
    private function bibox_user_data($key, $secret, $pair){
        exit;
        require_once APPPATH . 'libraries/Bibox.php';
        $bibox = new Bibox($key, $secret);
        $balances = $bibox->balances();
        return $balances;
    }

//------------------------------------- COINBENE FUNCTION ----------------------------------//
    private function coinbene_place_order($key, $secret, $pair, $side, $type, $price, $qty){
        require_once APPPATH . 'libraries/Coinbene.php';
        $coinbene = new Coinbene($key, $secret);
        $type = strtolower($type);
        $side = strtolower($side);
        if($type == 'market'){
            return false;
        }else{
            if($coinbene->place_order($pair, $side, $price, $qty)){return true;}else{return false;}
        }
    }
    
    private function coinbene_cancel_order($key, $secret, $pair, $orderId){
        require_once APPPATH . 'libraries/Coinbene.php';
        $coinbene = new Coinbene($key, $secret);
        if($coinbene->cancel($orderId)){
            return true;
        }else{
            return false;
        }
    }
    
    private function coinbene_user_data($key, $secret, $pair){
        require_once APPPATH . 'libraries/Coinbene.php';
        $coinbene = new Coinbene($key, $secret);
        $balances = $coinbene->balance();
        $blnc_array = array();
        foreach($balances->balance as $key => $blnc){
            if($blnc->available > 0){
                $blnc_array[] = array('currency' => $blnc->asset, 'balance' => $blnc->available.'');
            }
        }
        
        
        $user_success_trades = array();
        
        
        $open_orders = $coinbene->get_open_orders($pair);
        $user_open_trades = array();
        foreach($open_orders->orders->result as $c_order){
            $type_sep = explode('-', $c_order->type);
            $user_open_trades[] = array(
                    'pair' => $pair,
                    'order_id' => $c_order->orderid,
                    'price' => $c_order->price,
                    'qty' => $c_order->orderquantity,
                    'type' => $type_sep[1],
                    'side' => $type_sep[0],
                    'time' => $c_order->createtime,
                    'status' => 'Open'
                    );
        }
        
        $final = array(
            'success' => '1',
            'balances' => $blnc_array,
            'trades' => array(
                'open' => $user_open_trades,
                'success' => $user_success_trades
                )
            );
        
        return $final;

    }


//-------------------------------------- LIVECOIN FUNCTIONS -------------------------------//

    private function livecoin_place_order($key, $secret, $pair, $side, $type, $price, $qty){
        require_once APPPATH . 'libraries/Livecoin.php';
        $livecoin = new Livecoin($key, $secret);
        if($livecoin->place_order_tradePro ($type, $side, $pair, $qty, $price)){
            return true;
        }else{
            return false;
        }
    }
    
    private function livecoin_cancel_order($key, $secret, $pair, $orderId){
        require_once APPPATH . 'libraries/Livecoin.php';
        $livecoin = new Livecoin($key, $secret);
        if($livecoin->cancel_order($pair, $orderId)){
            return true;
        }else{
            return false;
        }
    }
    
    private function livecoin_user_data($key, $secret, $pair){
        require_once APPPATH . 'libraries/Livecoin.php';
        $livecoin = new Livecoin($key, $secret);
        $balances = json_decode($livecoin->getBalances());
        $blnc_array = array();
        foreach($balances as $key => $blnc){
            if($blnc->type == 'available' && $blnc->value > 0){
                $blnc_array[] = array('currency' => $blnc->currency, 'balance' => $blnc->value.'');
            }
        }
        
        $close_orders = json_decode($livecoin->get_close_orders($pair));
        $user_success_trades = array();
        foreach($close_orders as $c_order){
            $user_success_trades[] = array(
                    'pair' => $c_order->symbol,
                    'order_id' => $c_order->id,
                    'price' => $c_order->price,
                    'qty' => $c_order->quantity,
                    'type' => 'Limit',
                    'side' => $c_order->type,
                    'time' => date("y-m-d H:i:s", $c_order->datetime),
                    'status' => 'Closed'
                    );
        }
        
        $open_orders = json_decode($livecoin->get_open_orders());
        $user_open_trades = array();
        foreach($open_orders->data as $c_order){
            $type_sep = explode('_', $c_order->type);
            $user_open_trades[] = array(
                    'pair' => $c_order->currencyPair,
                    'order_id' => $c_order->id,
                    'price' => $c_order->price,
                    'qty' => $c_order->quantity,
                    'type' => $type_sep[0],
                    'side' => $type_sep[1],
                    'time' => date("Y-m-d H:i:s", $c_order->issueTime),
                    'status' => 'Open'
                    );
        }
        
        $final = array(
            'success' => '1',
            'balances' => $blnc_array,
            'trades' => array(
                'open' => $user_open_trades,
                'success' => $user_success_trades
                )
            );
        
        return $final;
        
    }

//-------------------------------------- HUOBI FUNCTIONS --------------------------------//
   private function huobi_place_order($key, $secret, $pair, $side, $type, $price, $qty){
        require_once APPPATH . 'libraries/Huobi.php';
        $ho = new huobi($key, $secret);
        $acc_id = $ho->get_account_accounts();
        $id = $acc_id->data[0]->id;
        
        
        $type = strtolower($type);
        $side = strtolower($side);
        if($type == 'market'){ 
            if($side == 'buy'){
                $type_side = 'buy-market';
            }else{
                $type_side = 'sell-market';
            }
        }else{ 
           if($side == 'buy'){
                $type_side = 'buy-limit';
            }else{
                $type_side = 'sell-limit';
            }
        }
        
        if($type == 'market'){ 
            if($ho->place_order($id, $qty, null, $pair, $type_side)){return true;}else{return false;}
        }else{
            if($ho->place_order($id, $qty, $price, $pair, $type_side)){return true;}else{return false;}
        }
    }
    
    private function huobi_cancel_order($key, $secret, $pair, $orderId){
        require_once APPPATH . 'libraries/Huobi.php';
        $ho = new huobi($key, $secret);
        if($ho->cancel_order($orderId)){
            return true;
        }else{
            return false;
        }
    }

    private function huobi_user_data($key, $secret, $pair){
        require_once APPPATH . 'libraries/Huobi.php';
        $ho = new huobi($key, $secret);
        $acc_id = $ho->get_account_accounts();
        $id = $acc_id->data[0]->id;
        $balances = $ho->get_account_balance($id);
        $blnc_array = array();
        foreach($balances->data->list as $key => $blnc){
            if($blnc->type == 'trade' && $blnc->balance > 0){
                $blnc_array[] = array('currency' => $blnc->currency, 'balance' => $blnc->balance.'');
            }
        }
        
        
        $close_orders = $ho->get_history_trade($pair);
        $user_success_trades = array();
        foreach($close_orders->data as $c_order){
            $user_success_trades[] = array(
                    'pair' => $pair,
                    'order_id' => $c_order->id,
                    'price' => $c_order->data[0]->price,
                    'qty' => $c_order->data[0]->amount,
                    'type' => 'Limit',
                    'side' => $c_order->data[0]->direction,
                    'time' => date("y-m-d H:i:s", $c_order->ts/1000),
                    'status' => 'Closed'
                    );
        }
        
        $open_orders = $ho->get_open_orders($id, $pair);
        $user_open_trades = array();
        foreach($open_orders->data as $c_order){
            $type_sep = explode('-', $c_order->type);
            $time_name = 'created-at';
            $user_open_trades[] = array(
                    'pair' => $c_order->symbol,
                    'order_id' => $c_order->id,
                    'price' => $c_order->price,
                    'qty' => $c_order->amount,
                    'type' => $type_sep[1],
                    'side' => $type_sep[0],
                    'time' => date("Y-m-d H:i:s", $c_order->$time_name),
                    'status' => 'Open'
                    );
        }
        
        $final = array(
            'success' => '1',
            'balances' => $blnc_array,
            'trades' => array(
                'open' => $user_open_trades,
                'success' => $user_success_trades
                )
            );
        
        return $final;
        
    }
    
//------------------------------ BITTREX FUNCTIONS ----------------------------------//
    private function bittrex_place_order($key, $secret, $pair, $side, $type, $price, $qty){
        require_once APPPATH . 'libraries/bittrex.php';
        $bittrex = new Bittrex($key, $secret);
        $type = strtolower($type);
        $side = strtolower($side);
        if($type == 'market'){
            if($side == 'buy'){
                if($bittrex->buyMarket($pair, $qty)){return true;}else{return false;}
            }else{
                if($bittrex->sellMarket($pair, $qty, $price)){return true;}else{return false;}
            }
        }else{
            if($side == 'buy'){
                if($bittrex->buyLimit($pair, $qty)){return true;}else{return false;}
            }else{
                if($bittrex->sellLimit($pair, $qty, $price)){return true;}else{return false;}
            }
        }
    }
    
    private function bittrex_cancel_order($key, $secret, $pair, $orderId){
        require_once APPPATH . 'libraries/bittrex.php';
        $bittrex = new Bittrex($key, $secret);
        if($bittrex->cancel($orderId)){
            return true;
        }else{
            return false;
        }
        
    }

    private function bittrex_user_data($key, $secret, $pair){
        require_once APPPATH . 'libraries/bittrex.php';
        $bittrex = new Bittrex($key, $secret);
        $balances = $bittrex->getBalances();
        $blnc_array = array();
        foreach($balances as $key => $blnc){
            if($blnc->Available > 0){
                $blnc_array[] = array('currency' => $blnc->Currency, 'balance' => $blnc->Available.'');
            }
        }
        
        $close_orders = $bittrex->getOrderHistory($pair, 20);
        $user_success_trades = array();
        foreach($close_orders as $c_order){
            $user_success_trades[] = array(
                    'pair' => $c_order->Exchange,
                    'order_id' => $c_order->OrderUuid,
                    'price' => $c_order->Price,
                    'qty' => $c_order->Quantity,
                    'type' => 'Limit',
                    'side' => $c_order->OrderType,
                    'time' => $c_order->TimeStamp,
                    'status' => 'Closed'
                    );
        }
        $open_orders = $bittrex->getOpenOrders();
        $user_open_trades = array();
        foreach($open_orders as $c_order){
            $user_open_trades[] = array(
                    'pair' => $c_order->Exchange,
                    'order_id' => $c_order->OrderUuid,
                    'price' => $c_order->Price,
                    'qty' => $c_order->QuantityRemaining,
                    'type' => 'Limit',
                    'side' => $c_order->OrderType,
                    'time' => $c_order->Opened,
                    'status' => 'Open'
                    );
        }
        
        
        $final = array(
            'success' => '1',
            'balances' => $blnc_array,
            'trades' => array(
                'open' => $user_open_trades,
                'success' => $user_success_trades
                )
            );
        
        return $final;
        
    }


//-------------------------------------- EXMO FUNCTIONS ---------------------------------//
    private function exmo_place_order($key, $secret, $pair, $side, $type, $price, $qty){
        require_once APPPATH . 'libraries/Exmo.php';
        $api = new Exmo($key, $secret);
        $type = strtolower($type);
        $side = strtolower($side);
        if($type == 'market'){
            $side_type = 'market_'.$side;
            if($api->place_order($side_type, $pair, $qty)){return true;}else{return false;}
        }else{
            if($api->place_order($side, $pair, $qty, $price)){return true;}else{return false;}
        }
        
    }
    
    private function exmo_cancel_order($key, $secret, $pair, $orderId){
        require_once APPPATH . 'libraries/Exmo.php';
        $api = new Exmo($key, $secret);
        if($api->cancel_order($orderId)){
            return true;
        }else{
            return false;
        }
    }
    
    private function exmo_user_data($key, $secret, $pair){
        require_once APPPATH . 'libraries/Exmo.php';
        $api = new Exmo($key, $secret);
        $balances = $api->getBalance();
        $blnc_array = array();
        foreach($balances['balances'] as $key => $blnc){
            if($blnc > 0){
                $blnc_array[] = array('currency' => $key, 'balance' => $blnc.'');
            }
        }
        
        $close_orders = $api->close_orders($pair);
        $user_success_trades = array();
        foreach($close_orders[$pair] as $c_order){
            $user_success_trades[] = array(
                    'pair' => $pair,
                    'order_id' => $c_order['trade_id'],
                    'price' => $c_order['price'],
                    'qty' => $c_order['quantity'],
                    'type' => 'Limit',
                    'side' => $c_order['type'],
                    'time' => date('Y-m-d H:i:s', $c_order['date']),
                    'status' => 'Closed',
                    'epoch' => $c_order['date'],
                    );
        }
        $open_orders = $api->open_orders();
        $user_open_trades = array();
        foreach($open_orders[$pair] as $c_order){
            $user_open_trades[] = array(
                    'pair' => $pair,
                    'order_id' => $c_order['order_id'],
                    'price' => $c_order['price'],
                    'qty' => $c_order['quantity'],
                    'type' => 'Limit',
                    'side' => $c_order['type'],
                    'time' => date('Y-m-d H:i:s', $c_order['created']),
                    'status' => 'Open',
                    'epoch' => $c_order['created'],
                    );
        }
        
        
        $final = array(
            'success' => '1',
            'balances' => $blnc_array,
            'trades' => array(
                'open' => $user_open_trades,
                'success' => $user_success_trades
                )
            );
        
        return $final;
    }
//----------------------------------- HITBTC FUNCTIONS --------------------------------------//
    
    private function hitbtc_place_order($key, $secret, $pair, $side, $type, $price, $qty){
        require_once APPPATH . 'libraries/hitBtc.php';
        $hitbtc = new hitBtc($key, $secret);
        
        if($hitbtc->place_order_tradingPro($pair, $side, $qty, $type, $price)){
            return true;
        }else{
            return false;
        }
        
    }
    
    private function hitbtc_cancel_order($key, $secret, $pair, $orderId){
        require_once APPPATH . 'libraries/hitBtc.php';
        $hitbtc = new hitBtc($key, $secret);
        if($hitbtc->cancel_order($orderId)){
            return true;
        }else{
            return false;
        }
    }

    private function hitbtc_user_data($key, $secret, $pair){
        require_once APPPATH . 'libraries/hitBtc.php';
        $hitbtc = new hitBtc($key, $secret);
        $balances = json_decode($hitbtc->getTradingBalance());
        $blnc_array = array();
        foreach($balances as $key => $blnc){
            if($blnc->available > 0){
                $blnc_array[] = array('currency' => $blnc->currency, 'balance' => $blnc->available.'');
            }
        }
        $close_orders = json_decode($hitbtc->get_history());
        $user_success_trades = array();
        foreach($close_orders as $c_order){
            $user_success_trades[] = array(
                    'pair' => $c_order->symbol,
                    'order_id' => $c_order->orderId,
                    'price' => $c_order->price,
                    'qty' => $c_order->quantity,
                    'type' => 'Limit',
                    'side' => $c_order->side,
                    'time' => $c_order->timestamp,
                    'status' => 'Closed',
                    'epoch' => strtotime($c_order->timestamp),
                    );
        }
        $open_orders = json_decode($hitbtc->get_open_order());
        $user_open_trades = array();
        foreach($open_orders as $c_order){
            $user_open_trades[] = array(
                    'pair' => $c_order->symbol,
                    'order_id' => $c_order->id,
                    'price' => $c_order->price,
                    'qty' => $c_order->quantity,
                    'type' => $c_order->type,
                    'side' => $c_order->side,
                    'time' => $c_order->createdAt,
                    'status' => 'Open',
                    'epoch' => strtotime($c_order->createdAt),
                    );
        }
        
        $final = array(
            'success' => '1',
            'balances' => $blnc_array,
            'trades' => array(
                'open' => $user_open_trades,
                'success' => $user_success_trades
                )
            );
        
        return $final;
    }
//------------------------------------ POLINIEX FUNXTIONS ---------------------------------//
    
    private function poloniex_place_order($key, $secret, $pair, $side, $type, $price, $qty){
        require_once APPPATH . 'libraries/Poloniex.php';
        $poloniex_obj = new poloniex($key , $secret);
        if($side == 'sell'){$side_2 = 'bid';}else{$side_2 = 'ask';}
        $type = strtolower($type);
        if($type == 'market'){
            $data = $poloniex_obj->get_order_book($pair);
            $price = $data[$type_2][0][0];
        }
        
        if($type == 'buy'){
           if($poloniex_obj->buy($pair, $price , $qty)){
               return true;
           }else{
               return false;
           }
        }else{
           if($poloniex_obj->sell($pair, $price , $qty)){
               return true;
           }else{
               return false;
           }
        }
        
    }
    
    private function poloniex_cancel_order($key, $secret, $pair, $orderId){
        require_once APPPATH . 'libraries/Poloniex.php';
        $poloniex_obj = new poloniex($key , $secret);
        if($poloniex_obj->cancel_order($pair, $orderId)){
            return true;
        }else{
            return false;
        }
    }

    private function poloniex_user_data($key, $secret, $pair){
        require_once APPPATH . 'libraries/Poloniex.php';
        $poloniex_obj = new poloniex($key , $secret);
        $balances = $poloniex_obj->get_balances();
        $blnc_array = array();
        foreach($balances as $key => $blnc){
            if($blnc > 0){
                $blnc_array[] = array('currency' => $key, 'balance' => $blnc.'');
            }
        }
        $close_orders = $poloniex_obj->get_my_trade_history($pair);
        $user_success_trades = array();
        foreach($close_orders as $key => $c_order){
            $user_success_trades[] = array(
                    'pair' => $pair,
                    'order_id' => $c_order['tradeID'],
                    'price' => $c_order['rate'],
                    'qty' => $c_order['amount'],
                    'type' => 'Limit',
                    'side' => $c_order['type'],
                    'time' => $c_order['date'],
                    'status' => 'Closed',
                    'epoch' => strtotime($c_order['date']),
                    );
        }
        
        $open_orders = $poloniex_obj->get_my_trade_history($pair);
        $user_open_trades = array();
        foreach($open_orders as $key => $c_order){
            $user_open_trades[] = array(
                    'pair' => $pair,
                    'order_id' => $c_order['orderNumber'],
                    'price' => $c_order['rate'],
                    'qty' => $c_order['amount'],
                    'type' => 'Limit',
                    'side' => $c_order['type'],
                    'time' => $c_order['date'],
                    'status' => 'Open',
                    'epoch' => strtotime($c_order['date']),
                    );
        }
        
        $final = array(
            'success' => '1',
            'balances' => $blnc_array,
            'trades' => array(
                'open' => $user_open_trades,
                'success' => $user_success_trades
                )
            );
        
        return $final;
    }

//----------------------------------- KRAKEN FUNCTIONS ---------------------------------------------//
    private function kraken_place_order($key, $secret, $pair, $side, $type, $price, $qty){
        require_once APPPATH . 'libraries/Kraken.php';
        $beta = false;
        $url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
        $sslverify = $beta ? false : true;
        $version = 0;
        $kraken = new Kraken($key, $secret, $url, $version, $sslverify);
        if($type == 'market'){
            $param = array(
               'pair' => $pair,
               'type' => $side,
               'ordertype' => strtolower($type),
               'volume' => $qty
           ); 
        }else if($type == 'limit'){
            $param = array(
               'pair' => $pair,
               'type' => $side,
               'ordertype' => strtolower($type),
               'volume' => $qty,
               'price' => $price
           );
        }else{
            return false;
        }
        
        if($kraken->QueryPrivate('AddOrder', $param)){
            return true;
        }else{
            return false;
        }
    }
    
    private function kraken_cancel_order($key, $secret, $pair, $orderId){
        require_once APPPATH . 'libraries/Kraken.php';
        $beta = false;
        $url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
        $sslverify = $beta ? false : true;
        $version = 0;
        $kraken = new Kraken($key, $secret, $url, $version, $sslverify);
        if($kraken->QueryPrivate('CancelOrder', array('txid'=>$orderId))){
            return true;
        }else{
            return false;
        }
    }
    
    private function kraken_user_data($key, $secret, $pair){
        require_once APPPATH . 'libraries/Kraken.php';
        $beta = false;
        $url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
        $sslverify = $beta ? false : true;
        $version = 0;
        $kraken = new Kraken($key, $secret, $url, $version, $sslverify);
        $res = $kraken->QueryPrivate('Balance');
        $blnc_array = array();
        foreach($res['result'] as $key => $blnc){
            if($blnc > 0){
                $blnc_array[] = array('currency' => $key, 'balance' => $blnc.'');
            }
        }
        $close_orders = $kraken->QueryPrivate('ClosedOrders');
        $user_success_trades = array();
        foreach($close_orders['result']['closed'] as $key => $c_order){
            $user_success_trades[] = array(
                    'pair' => $c_order['descr']['pair'],
                    'order_id' => $key,
                    'price' => $c_order['price'],
                    'qty' => $c_order['vol'],
                    'type' =>$c_order['descr']['ordertype'],
                    'side' => $c_order['descr']['type'],
                    'time' => date('Y-m-d H:i:s', $c_order['closetm']),
                    'status' => $c_order['status'],
                    'epoch' => $c_order['closetm'],
                    );
        }
        $open_orders = $kraken->QueryPrivate('OpenOrders');
        $user_open_trades = array();
        foreach($open_orders['result']['open'] as $key => $c_order){
            $user_open_trades[] = array(
                    'pair' => $c_order['descr']['pair'],
                    'order_id' => $key,
                    'price' => $c_order['price'],
                    'qty' => $c_order['vol'],
                    'type' =>$c_order['descr']['ordertype'],
                    'side' => $c_order['descr']['type'],
                    'time' => date('Y-m-d H:i:s', $c_order['opentm']),
                    'status' => $c_order['status'],
                    'epoch' => $c_order['opentm'],
                    );
        }
        
        $final = array(
            'success' => '1',
            'balances' => $blnc_array,
            'trades' => array(
                'open' => $user_open_trades,
                'success' => $user_success_trades
                )
            );
        
        return $final;
    }
    
//------------------------------ BINANCE FUNCTIONS -------------------------------------//
    
    private function binance_pair_info($key, $secret, $pair){
        require_once APPPATH . 'libraries/Binance.php';
        $binance = new Binance($key, $secret);
        $info = $binance->exchangeInfo();
        $symbol_info = array();
        foreach($info['symbols'] as $res){
            if($res['symbol'] == $pair){
                $symbol_info['symbol'] = $res['symbol'];
                $symbol_info['status'] = $res['status'];
                foreach($res['filters'] as $filter){
                    if($filter['filterType'] == 'PRICE_FILTER'){
                        $symbol_info['min_price'] = $filter['tickSize'];
                    }else if($filter['filterType'] == 'LOT_SIZE'){
                        $symbol_info['min_qty'] = $filter['minQty'];
                        $symbol_info['max_qty'] = $filter['maxQty'];
                    }else if($filter['filterType'] == 'MIN_NOTIONAL'){
                        $symbol_info['min_not'] = $filter['minNotional'];
                    }
                }
            }
        }
        
        return $symbol_info;
    }
    
    
    private function binance_place_order($key, $secret, $pair, $side, $type, $price, $qty){
        $pair_info = $this->binance_pair_info($key, $secret, $pair);
        if($pair_info['status'] != 'TRADING'){
            echo json_encode(array('error'=>'1', 'msg'=>$pair.' Trading is currently locked')); exit;
        }
        if($qty < $pair_info['min_qty']){
            echo json_encode(array('error'=>'1', 'msg'=>'Minimum quantity allowed is '.$pair_info['min_qty'])); exit;
        }
        if($qty > $pair_info['max_qty']){
            echo json_encode(array('error'=>'1', 'msg'=>'Maximum quantity allowed is '.$pair_info['max_qty'])); exit;
        }
        if($type == 'limit' && $price < $pair_info['min_price']){
            echo json_encode(array('error'=>'1', 'msg'=>'Minimum price allowed is '.$pair_info['min_price'])); exit;
        }
        if($type == 'limit' && ($price*$qty) < $pair_info['min_not']){
            echo json_encode(array('error'=>'1', 'msg'=>'Minimum notional allowed is '.$pair_info['min_not'])); exit;
        }
        
        
        
        require_once APPPATH . 'libraries/Binance.php';
        $binance = new Binance($key, $secret);
        if($side == 'sell' && $type == 'limit'){
            $return_date = $binance->sell($pair, $qty, $price);
        }else if($side == 'sell' && $type == 'market'){
            $return_date = $binance->marketSell($pair, $qty);
        }else if($side == 'buy' && $type == 'limit'){
            $return_date = $binance->buy($pair, $qty, $price);
        }else if($side == 'buy' && $type == 'market'){
            $return_date = $binance->marketBuy($pair, $qty);
        }else{
            return false;
        }
        
        return true;
        
    }
    
    private function binance_cancel_order($key, $secret, $pair, $orderId){
        require_once APPPATH . 'libraries/Binance.php';
        $binance = new Binance($key, $secret);
        if($binance->cancel($pair, $orderId)){
            return true;
        }else{
            return false;
        }
    }
    
    private function binance_user_data($key, $secret, $pair){
        require_once APPPATH . 'libraries/Binance.php';
        $binance = new Binance($key, $secret);
        $balances = $binance->account();
        $blnc_array = array();
        foreach($balances['balances'] as $key => $blnc){
            if($blnc['free'] > 0){
                $blnc_array[] = array('currency' => $blnc['asset'], 'balance' => $blnc['free'].'');
            }
        }
        
        $trades = $binance->orders($pair);
        $user_success_trades = array();
        $user_open_trades = array();
        $user_cancel_trades = array();
        foreach($trades as $trade){
           if($trade['status'] == 'FILLED' || $trade['status'] == 'CANCELED'){
                $into_sec = $trade['updateTime']/1000;
                if($trade['status'] == 'CANCELED'){$qtyy = $trade['origQty']; }else{$qtyy = $trade['executedQty'];}
                $user_success_trades[] = array(
                    'pair' => $trade['symbol'],
                    'order_id' => $trade['orderId'],
                    'price' => $trade['price'],
                    'qty' => $qtyy,
                    'type' =>$trade['type'],
                    'side' => $trade['side'],
                    'time' => date('Y-m-d H:i:s', $into_sec),
                    'status' => $trade['status'],
                    'epoch' => $trade['updateTime'],
                    );
            }else{
                $into_sec = $trade['updateTime']/1000;
                $user_open_trades[] = array(
                    'pair' => $trade['symbol'],
                    'order_id' => $trade['orderId'],
                    'price' => $trade['price'],
                    'qty' => $trade['origQty'],
                    'type' =>$trade['type'],
                    'side' => $trade['side'],
                    'time' => date('Y-m-d H:i:s', $into_sec),
                    'epoch' => $trade['updateTime'],
                    );
            }
            
        }
        
  
        usort($user_open_trades, 'epoch');
        usort($user_success_trades, 'epoch');
        
        $final = array(
            'success' => '1',
            'balances' => $blnc_array,
            'trades' => array(
                'open' => array_reverse($user_open_trades),
                'success' => array_reverse($user_success_trades)
                )
            );
        
        return $final;
    }
    
    
public function date_compare($a, $b)
{
    $t1 = $a['datetime'];
    $t2 = $b['datetime'];
    return $t1 - $t2;
}  
    
}