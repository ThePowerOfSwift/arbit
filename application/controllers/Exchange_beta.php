<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exchange_beta extends MY_Controller {
    public $allow_users = array(9, 43, 846, 59570, 61081, 77777, 77836, 78572, 77827, 78365, 78355, 83632, 78451, 78452, 69599, 80844, 787088);
    public static $settings = array();
    private static $ddb = null;
    private static $ddbb = null;
    public function __construct(){
            parent::__construct();
            self::$ddb = $this->DB2;
            self::$ddbb = $this->db;
            self::get_settings();
            //exit;
            //self::check_blockes();
    }
    
    public function index(){
        //$this->check_blockes();
    }
    
    private static function get_settings(){
        $qry = self::$ddb->query("SELECT * FROM block_settings")->result();
        foreach($qry as $q){
            self::$settings[$q->name] = $q->value;
        }
    }
            
    public function check_blockes(){
        $blocks = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 ORDER BY price ASC LIMIT ".self::$settings['no_of_blockes'])->result();
        $run_succss_fun = false;
        foreach($blocks as $block){
            $success_arb_size = $block->arb_size / 25;
            $success_eth_size = $block->eth_size / 25;
            if($block->current_arb_size >= $success_arb_size && $block->current_eth_size >= $success_eth_size){
                $run_succss_fun = true;
                $new_buy_price = $block->price + 0.0000001;
                $this->DB2->query("UPDATE blocks SET status = 2, price = ? WHERE id = ?", array($new_buy_price, $block->id));
                if($this->DB2->query("INSERT INTO blocks (price, arb_size, eth_size, current_arb_size, flag) VALUES (?,?,?,?,?)", array($block->price, $block->arb_size, $block->eth_size, 0, $block->flag))){
                    $last_block = $this->DB2->insert_id();
                     if($last_block > 0){
                        // $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                        //     array(77836, 'Sell', 2, $block->price, $last_block, 500, 'exchange', 'sell_side'));
                        // $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                        //     array(82, 'Sell', 3, $block->price, $last_block, 500, 'exchange', 'sell_side'));
                        // $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                        //     array(783195, 'Sell', 1, $block->price, $last_block, 500, 'exchange', 'sell_side'));
                        // $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                        //     array(83632, 'Sell', 1, $block->price, $last_block, 500, 'exchange', 'sell_side'));
                        
                        // $blk = $this->DB2->query("SELECT * FROM blocks WHERE id = ?", array($last_block))->row();
                        
                        // $amount_1001 = 15 / $blk->price;
                        // $amount_1001 = $this->getTruncatedValue($amount_1001, 3);
                        // $remain_in_blck = $blk->arb_size - $blk->current_arb_size;
                        
                        // if($proplus_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = 1001")->row()){
                        //     $open_orders_amount_pp = $this->DB2->query("SELECT sum(amount) as total_amount FROM orders_beta WHERE user_id = ? AND order_type = ? AND status = ? AND order_from like 'pro_plus%'", array($u_id, 'Sell', 0))->row()->total_amount;
                        //     if($open_orders_amount_pp > 0){
                        //         $allow_1001_amount = $proplus_wallet->activeArb - $open_orders_amount_pp;
                        //     }else{
                        //         $allow_1001_amount = $proplus_wallet->activeArb;
                        //     }
                        // }
                        
                        // if($allow_1001_amount < $amount_1001){
                        //     $amount_1001 = $allow_1001_amount;
                        // }
                        // if($remain_in_blck >= $amount_1001 && $amount_1001 > 0){
                        //     $unique_stamp = $this->db->query("SELECT * FROM pro_plus_crons_setting WHERE name = 'order_unique_stamp'")->row()->value;
                        //     $wallet_name = 'pro_plus_'.$unique_stamp;
                        //     $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                        //         array(1001, 'Sell', $amount_1001, $blk->price, $blk->id, 139, $wallet_name, $blk->flag));
                        //     $cr_arb_size = $blk->current_arb_size + $amount_1001;
                        //     if($cr_arb_size < 0.001){$cr_arb_size = 0;}
                        //     $this->DB2->query("UPDATE blocks SET current_arb_size = ? WHERE id = ?", array($cr_arb_size, $blk->id));
                        // }
                            
                    }
                }
            }
        }
        
        if($run_succss_fun){
            if($this->success_block()){
                return true;
            }else{
                return true;
            }
            
        }else{
            return true;
        }
        
    }
    
   
    
    public function success_block(){
        if(!$get_blocks = $this->DB2->query("SELECT * FROM blocks WHERE status = 2")->result()){
            return true;
        }
        //$run = false;
        foreach($get_blocks as $block){
            //$run = true;
            $price = $this->getTruncatedValue($block->price, 6);
            $block_buy_amout = $block->current_eth_size /  $price;
            if($block_buy_amout > $block->current_arb_size){
                $get_buy_orders = $this->DB2->query("SELECT * FROM orders_beta WHERE order_type = 'Buy' AND status = 0 AND block_id = ?", array($block->id))->result();
                foreach($get_buy_orders as $buy_order){
                    $update_buy_amount = ($buy_order->amount * $block->current_arb_size) / $block_buy_amout;
                    $update_buy_amount = $this->getTruncatedValue($update_buy_amount, 3);
                    $this->DB2->query("UPDATE orders_beta SET amount = ? WHERE id = ? AND block_id = ? AND order_type = 'Buy'", array($update_buy_amount, $buy_order->id, $block->id));
                    $same_block_price = floatval($price);
                    $remain_amount = $buy_order->amount - $update_buy_amount;
                    if($remain_amount > 0.1){
                        if($get_same_block = $this->DB2->query("SELECT * FROM blocks WHERE price = ?", array($same_block_price))->row()){
                            $order_eth_worth = $remain_amount * $get_same_block->price;
                            $new_block_eth_size = $get_same_block->current_eth_size + $order_eth_worth;
                            if($get_same_block->eth_size >= $new_block_eth_size){
                                $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, side) values (?,?,?,?,?,?,?)", 
                                    array($buy_order->user_id, 'Buy', $remain_amount, $get_same_block->price, $get_same_block->id, $buy_order->eth_usd, $buy_order->side));
                                if($new_block_eth_size < 0.001){$new_block_eth_size = 0;}
                                $this->DB2->query("UPDATE blocks SET current_eth_size = ? WHERE id = ?", array($new_block_eth_size, $get_same_block->id));
                            }
                        }
                    }
                }
                $eth_size = $block->current_arb_size * $price;
                
                $get_eth_tot = $this->DB2->query("SELECT * FROM block_settings WHERE name = 'market_block_eth'")->row();
                $new_ethtot_update = $get_eth_tot->value + $eth_size;
                $this->DB2->query("UPDATE block_settings SET value = ? WHERE name = 'market_block_eth'", array($new_ethtot_update));
                if($eth_size < 0.001){$eth_size = 0;}
                $this->DB2->query("UPDATE blocks SET arb_size = ?, eth_size = ?, current_arb_size = ?, current_eth_size = ?, status = 1 WHERE id = ?", 
                    array($block->current_arb_size, $eth_size, $block->current_arb_size, $eth_size, $block->id));
                
            }else if($block_buy_amout < $block->current_arb_size){
                $get_sell_orders = $this->DB2->query("SELECT * FROM orders_beta WHERE order_type = 'Sell' AND status = 0 AND block_id = ?", array($block->id))->result();
                foreach($get_sell_orders as $sell_order){
                    $update_sell_amount = ($sell_order->amount * $block_buy_amout) / $block->current_arb_size;
                    $update_sell_amount = $this->getTruncatedValue($update_sell_amount, 4);
                    $this->DB2->query("UPDATE orders_beta SET amount = ? WHERE id = ? AND block_id = ? AND order_type = 'Sell'", array($update_sell_amount, $sell_order->id, $block->id));
                    $remain_amount = $sell_order->amount - $update_sell_amount;
                    $same_block_price = floatval($price);
                    if($remain_amount > 0.1){
                        if($get_same_block = $this->DB2->query("SELECT * FROM blocks WHERE price = ?", array($same_block_price))->row()){
                            $new_block_arb_size = $get_same_block->current_arb_size + $remain_amount;
                            if($get_same_block->arb_size >= $new_block_arb_size){
                                $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, side, order_from) values (?,?,?,?,?,?,?,?)", 
                                    array($sell_order->user_id, 'Sell', $remain_amount, $get_same_block->price, $get_same_block->id, $sell_order->eth_usd, $sell_order->side, $sell_order->order_from));
                                if($new_block_arb_size < 0.001){$new_block_arb_size = 0;}
                                $this->DB2->query("UPDATE blocks SET current_arb_size = ? WHERE id = ?", array($new_block_arb_size, $get_same_block->id));
                            }
                        }
                    }
                    
                    // if($sell_order->order_from != 'exchange_earned'){
                    //     $remaining_arb_return = $sell_order->amount - $update_sell_amount;
                    //     $price_usd_order = $sell_order->price * $sell_order->eth_usd; 
                    //     $remain_arb_usd = $remaining_arb_return * $price_usd_order;
                        
                    //     $order_amount_limit = self::$ddbb->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($sell_order->user_id))->row()->order_amount_limit;
                    //     $total_order_ammount = $order_amount_limit - $remain_arb_usd;
                        
                    //     if($total_order_ammount < 0){
                    //         self::$ddbb->query("UPDATE exchange_wallet SET order_amount_limit = ? WHERE user_id = ?", array(0, $sell_order->user_id));
                    //     }else{
                    //         self::$ddbb->query("UPDATE exchange_wallet SET order_amount_limit = ? WHERE user_id = ?", array($total_order_ammount, $sell_order->user_id));
                    //     }
                    // }
                }
                
                $get_eth_tot = $this->DB2->query("SELECT * FROM block_settings WHERE name = 'market_block_eth'")->row();
                $new_ethtot_update = $get_eth_tot->value + $block->current_eth_size;
                $this->DB2->query("UPDATE block_settings SET value = ? WHERE name = 'market_block_eth'", array($new_ethtot_update));
                
                if($block_buy_amout < 0.001){$block_buy_amout = 0;}
                $this->DB2->query("UPDATE blocks SET arb_size = ?, eth_size = ?, current_arb_size = ?, current_eth_size = ?, status = 1 WHERE id = ?", 
                    array($block_buy_amout, $block->current_eth_size, $block_buy_amout, $block->current_eth_size, $block->id));
            }else if($block_buy_amout == $block->current_arb_size){
                $get_eth_tot = $this->DB2->query("SELECT * FROM block_settings WHERE name = 'market_block_eth'")->row();
                $new_ethtot_update = $get_eth_tot->value + $block->current_eth_size;
                $this->DB2->query("UPDATE block_settings SET value = ? WHERE name = 'market_block_eth'", array($new_ethtot_update));
                
                $this->DB2->query("UPDATE blocks SET arb_size = ?, eth_size = ?, status = 1 WHERE id = ?", 
                    array($block->current_arb_size, $block->current_eth_size, $block->id));
            }
        }
        
        return true;
    }
    
    
    public function init_block(){
        exit;
        $price = 0.0034;
        for($i=1; $i<=400; $i++){
            if($i<200){$flag='buy_side';}
            //else if($i == 200){$flag = 'market';}
            else{$flag = 'sell_side';}
            $eth_size = self::$settings['block_size'];
            $arb_size = self::$settings['block_size'] / $price;
            if($this->DB2->query("INSERT INTO blocks (price, arb_size, eth_size, flag, current_arb_size) VALUES (?,?,?,?,?)", array($price, round($arb_size, 3), $eth_size, $flag, 7))){
                $last_block = $this->DB2->insert_id();
                if($last_block > 0){
                    $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                        array(77836, 'Sell', 2, $price, $last_block, 500, 'exchange', 'sell_side'));
                    $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                        array(82, 'Sell', 3, $price, $last_block, 500, 'exchange', 'sell_side'));
                    $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                        array(83632, 'Sell', 1, $price, $last_block, 500, 'exchange', 'sell_side'));
                    $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                        array(783195, 'Sell', 1, $price, $last_block, 500, 'exchange', 'sell_side'));
                        
                    // $blk = $this->DB2->query("SELECT * FROM blocks WHERE id = ?", array($last_block))->row();
                        
                    // $amount_1001 = 15 / $blk->price;
                    // $amount_1001 = $this->getTruncatedValue($amount_1001, 3);
                    // $remain_in_blck = $blk->arb_size - $blk->current_arb_size;
                    
                    // if($proplus_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = 1001")->row()){
                    //     $open_orders_amount_pp = $this->DB2->query("SELECT sum(amount) as total_amount FROM orders_beta WHERE user_id = ? AND order_type = ? AND status = ? AND order_from like 'pro_plus%'", array($u_id, 'Sell', 0))->row()->total_amount;
                    //     if($open_orders_amount_pp > 0){
                    //         $allow_1001_amount = $proplus_wallet->activeArb - $open_orders_amount_pp;
                    //     }else{
                    //         $allow_1001_amount = $proplus_wallet->activeArb;
                    //     }
                    // }
                    
                    // if($allow_1001_amount < $amount_1001){
                    //     $amount_1001 = $allow_1001_amount;
                    // }
                    // if($remain_in_blck >= $amount_1001 && $amount_1001 > 0.001){
                    //     $unique_stamp = $this->db->query("SELECT * FROM pro_plus_crons_setting WHERE name = 'order_unique_stamp'")->row()->value;
                    //     $wallet_name = 'pro_plus_'.$unique_stamp;
                    //     $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                    //         array(1001, 'Sell', $amount_1001, $blk->price, $blk->id, 139, $wallet_name, $blk->flag));
                    //     $cr_arb_size = $blk->current_arb_size + $amount_1001;
                    //     if($cr_arb_size < 0.001){$cr_arb_size = 0;}
                    //     $this->DB2->query("UPDATE blocks SET current_arb_size = ? WHERE id = ?", array($cr_arb_size, $blk->id));
                    // }
                }
            }
            
            $price = $price + self::$settings['block_span'];
            echo "created<br>";
        }
    }
    
    public function place_buy_order(){
        exit;
        if($this->input->post("get_from") != 'sdfhihf@klhjglk*'){
            $captcha = $this->input->post('captcha');
            if(!$this->check_captcha($captcha)){exit;}
        }
        
        // get user id from session 
        $u_id = $this->session->userdata('u_id');
        // if order is auto
        $auto_buy = 0;
        if($this->input->post("get_from") == 'sdfhihf@klhjglk*'){
            $u_id = $this->input->post("u_id");
            $auto_buy = 1;
        }
        
        //check exchange lock
        if(!$this->exchange_lock($u_id)){exit;}
        // check ETH value
        if(!$eth_value = $this->get_eth_db_value()){exit;}
       
        
        if($u_id !=''){
            //check if user have arb in pro+ wallet 
            // if($this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = ? AND status =  1 AND activeArb > 1", array($u_id))->row()){
            //     echo json_encode(array('error'=>'1', 'msg' => 'Sorry You are not authorized to do this operation manually.'));
            //     exit;
            // }
            $order_type = 'Buy';
            $amount = doubleval($this->input->post("amount"));
            $amount = $this->getTruncatedValue($amount, 4);
            $price = doubleval($this->input->post("price"));
            $price = $this->getTruncatedValue($price, 6);
            // min amount check
            if(!$this->min_amount($amount)){exit;}
            //get price from blockes 
            if(!$price = $this->get_blockes_for_price($order_type, $price)){exit;}
            $price = $this->getTruncatedValue($price, 6);
            
             //get user data 
            $user_data = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
            // get package detail 
            $pkg_data = $this->db->query("SELECT * FROM packages WHERE name = ?", array($user_data->package))->row();
            
            //maximum open orders 
            if(!$this->totel_open_orders($u_id, $user_data->package, $order_type)){exit;}
            
            $old_order_values = 0;
            $open_buy_order = $this->DB2->query("SELECT * FROM orders_beta WHERE user_id = ? AND order_type = ? AND status = ?", array($u_id, 'Buy', 0))->result();
            foreach($open_buy_order as $order){
                $eth_value_1 = $order->amount * $order->price;
                $old_order_values = $old_order_values + $eth_value_1;
            }

            $buyer_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($u_id))->row();
            $expected_buyer_eth = $amount * $price;
            if($buyer_exchange_wallet->activeEth < $expected_buyer_eth || $buyer_exchange_wallet->activeEth < ($old_order_values + $expected_buyer_eth)){
                echo json_encode(array('error'=>'1', 'msg' => 'Your account balance is not enough.'));
                exit;
            }
            
            if($amount >= 1){
                if($get_block = $this->DB2->query("SELECT * FROM blocks WHERE price = ? AND status = 0", array($price))->row()){
                    $current_eth_value = $amount * $price;
                    $block_remaining_eth = $get_block->eth_size - $get_block->current_eth_size;
                    if($block_remaining_eth >= $current_eth_value){
                        $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, eth_usd, auto_buy, block_id, side) VALUES (?,?,?,?,?,?,?,?)",
                        array($u_id, 'Buy', $this->getTruncatedValue($amount, 4), $price, $eth_value, $auto_buy, $get_block->id, $get_block->flag));
                        
                        //update block current eth size
                        $update_block_eth_size = $get_block->current_eth_size + ($amount * $price);
                        $this->DB2->query("UPDATE blocks SET current_eth_size = ? WHERE id = ?", array($update_block_eth_size, $get_block->id));
                        echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));
                        exit;
                    }else{
                        $order_place_msg = 0;
                        if($block_remaining_eth > 0.001){
                            $amount_of_arb = $block_remaining_eth / $price;
                            
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, eth_usd, auto_buy, block_id, side) VALUES (?,?,?,?,?,?,?,?)",
                            array($u_id, 'Buy', $this->getTruncatedValue($amount_of_arb, 4), $price, $eth_value, $auto_buy, $get_block->id, $get_block->flag));
                            
                            //update block current eth size
                            $update_block_eth_size = $get_block->current_eth_size + $block_remaining_eth;
                            $this->DB2->query("UPDATE blocks SET current_eth_size = ? WHERE id = ?", array($update_block_eth_size, $get_block->id));
                            $order_place_msg++;
                        }
                        if($order_place_msg > 0){
                            echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));
                            exit;
                        }else{
                            echo json_encode(array('error'=>'1', 'msg' => 'This Block is Full.'));
                            exit;
                        }
                        
                       
                        $next_block = $this->DB2->query("SELECT * FROM blocks WHERE price > ? AND status = 0 ORDER BY price ASC", array($price))->row();
                        $return_amount = ($current_eth_value - $block_remaining_eth) / $next_block->price;
                        if($return_amount < 0.001){
                            echo json_encode(array('error'=>'1', 'msg' => 'This Block is Full.'));
                            exit;
                        }
                        
                        $old_order_values = 0;
                        $open_buy_order = $this->DB2->query("SELECT * FROM orders_beta WHERE user_id = ? AND order_type = ? AND status = ?", array($u_id, 'Buy', 0))->result();
                        foreach($open_buy_order as $order){
                            $eth_value_1 = $order->amount * $order->price;
                            $old_order_values = $old_order_values + $eth_value_1;
                        }
        
                        $buyer_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($u_id))->row();
                        $expected_buyer_eth = $return_amount * $next_block->price;
                        if($buyer_exchange_wallet->activeEth < $expected_buyer_eth || $buyer_exchange_wallet->activeEth < ($old_order_values + $expected_buyer_eth)){
                            echo json_encode(array('error'=>'1', 'msg' => 'balance not.'));
                            exit;
                        }
                        
                        $next_block_remaining_size = $next_block->eth_size - $next_block->current_eth_size;
                        $return_amount_eth_value  = $return_amount * $next_block->price;
                        if($next_block_remaining_size >= $return_amount_eth_value){
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, eth_usd, auto_buy, block_id, side) VALUES (?,?,?,?,?,?,?,?)",
                                array($u_id, 'Buy', $this->getTruncatedValue($return_amount, 4), $next_block->price, $eth_value, $auto_buy, $next_block->id, $next_block->flag));
                                
                            //update block current eth size
                            $update_block_eth_size = $next_block->current_eth_size + ($return_amount * $next_block->price);
                            $this->DB2->query("UPDATE blocks SET current_eth_size = ? WHERE id = ?", array($update_block_eth_size, $next_block->id));
                            $order_place_msg++;
                        }
                        
                        if($order_place_msg == 1){
                            echo json_encode(array('success'=>'1', 'msg' => 'Some amount of order is placed.'));exit;
                        }else if($order_place_msg == 2){
                            echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));exit;
                        }else{
                            echo json_encode(array('error'=>'1', 'msg' => 'Order not placed.'));exit;
                        }
                        
                    }
                }else{
                    echo json_encode(array('error'=>'1', 'msg' => 'No block match with this price.'));
                    exit;
                }

            }
            
        }
            
    }
    
    public function sellOrder_exchange(){
        exit;
        if($this->input->post("get_from") != 'sdfhihf@klhjglk*'){
            $captcha = $this->input->post('captcha');
    	    if(!$this->check_captcha($captcha)){exit;}
        }
        // get user id from session 
        $u_id = $this->session->userdata('u_id');
         // if order is auto
        $auto_buy = 0;
        if($this->input->post("get_from") == 'sdfhihf@klhjglk*'){
            $u_id = $this->input->post("u_id");
            $auto_buy = 1;
        }
        //check exchange lock
        if(!$this->exchange_lock($u_id)){exit;}
        // check ETH value
        if(!$eth_value = $this->get_eth_db_value()){exit;}
        
        if($u_id !=''){
            //check if user have arb in pro+ wallet 
            // if($this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = ? AND status =  1 AND activeArb > 1", array($u_id))->row()){
            //     echo json_encode(array('error'=>'1', 'msg' => 'Sorry You are not authorized to do this operation manually.'));
            //     exit;
            // }
            $order_type = 'Sell';
            $amount = doubleval($this->input->post("amount"));
            $amount = $this->getTruncatedValue($amount, 4);
            $price = doubleval($this->input->post("price"));
            $price = $this->getTruncatedValue($price, 6);
            
            $exch= $this->input->post("wallet");
            if($order_type == 'Sell' && $exch != 'exchange'){
                echo json_encode(array('error'=>'1', 'msg' => 'Sell order Only place from exchange wallet.')); exit;
            }
            
            // min amount chechk
            if(!$this->min_amount($amount)){exit;}
            //max amount 
            if(!$this->max_amount($amount)){exit;}
            //get price from blockes 
            
            if($this->input->post("koibe") == 123){ 
                if(!$price = $this->get_blockes_for_price($order_type, $price, $amount, 'from_best_sell')){exit;}
            }else{
                if(!$price = $this->get_blockes_for_price($order_type, $price, $amount)){exit;}
            }
            
            //user cannot place buy and sell in same golden block
            if(!$this->buy_sell_golden_block($u_id, $price, $order_type)){exit;}
            
            //get user data 
            $user_data = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
            // get package detail 
            $pkg_data = $this->db->query("SELECT * FROM packages WHERE name = ?", array($user_data->package))->row();
            
            //maximum open orders 
            if(!$this->totel_open_orders($u_id, $user_data->package, $order_type)){exit;}
            
            
            //check 20% earned
            /*$get_block_to_check = $this->DB2->query("SELECT * FROM blocks WHERE price = ?", array($price))->row();
            $earned_portion = ($get_block_to_check->arb_size / 100) * 20;
            if($exch == 'exchange' && !in_array($u_id, $this->allow_users)){
                $earned_sell_amount = $this->DB2->query("SELECT sum(amount) as total FROM orders_beta WHERE block_id = ? AND status = 0 AND order_from LIKE 'exchange'", array($get_block_to_check->id))->row();
                if($earned_sell_amount->total >= $earned_portion){
                    echo json_encode(array('error'=>'1', 'msg' => 'Exchange Wallet portion of block is full, please use next open block.')); exit;
                }else{
                    $remaining_earned_portion = $earned_portion - $earned_sell_amount->total;
                    if($amount > $remaining_earned_portion){
                        $amount = $this->getTruncatedValue($remaining_earned_portion, 4);
                    }
                }
            }*/
                
            //check seller account balance                
            if(!$this->check_seller_wallet_balance($u_id, $amount, $exch)){exit;}
           
            if($amount >= 1){
                if($get_block = $this->DB2->query("SELECT * FROM blocks WHERE price = ? AND status = 0", array($price))->row()){
                        // 33% check add here
                        $allow_size = $this->get_block_sell_size($price);
                        $block_remaining_size = $allow_size - $get_block->current_arb_size;
                        if($block_remaining_size >= $amount){
                            if(!$this->exchange_limits($u_id, $pkg_data->exchange_sell_limit, $amount, $eth_value, $price)){exit;}
                            $this->place_sell_order($u_id, $amount, $price, $exch, $eth_value, $get_block->id, $get_block->current_arb_size, $get_block->flag);
                            echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));
                            exit;
                        }else{
                            $order_place_msg = 0;
                            if($block_remaining_size > 0.001){
                                if(!$this->exchange_limits($u_id, $pkg_data->exchange_sell_limit, $block_remaining_size, $eth_value, $price)){exit;}
                                $this->place_sell_order($u_id, $block_remaining_size, $price, $exch, $eth_value, $get_block->id, $get_block->current_arb_size, $get_block->flag);
                                $order_place_msg++;
                            }
                            
                            if($order_place_msg > 0){
                                echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));
                                exit;
                            }else{
                                echo json_encode(array('error'=>'1', 'msg' => 'This Block is Full.'));
                                exit;
                            }
                        
                            
                            if($get_block->flag == 'market'){
                                if($order_place_msg > 0){
                                    echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));exit;
                                }else{
                                    echo json_encode(array('error'=>'1', 'msg' => 'Market Block is full, Please place your order in next block.'));exit;
                                }
                            }
                            $return_amount = $amount - $block_remaining_size;
                            if($return_amount < 0.001){
                                echo json_encode(array('error'=>'1', 'msg' => 'This Block is Full.'));
                                exit;
                            }
                            $next_block = $this->DB2->query("SELECT * FROM blocks WHERE price > ? AND status = 0 ORDER BY price ASC", array($price))->row();
                            // 33% check add here
                            $next_allow_size = $this->get_block_sell_size($next_block->price);
                            $next_block_remaining_size = $next_allow_size - $next_block->current_arb_size;
                            
                            if($next_block_remaining_size >= $return_amount){
                                if(!$this->exchange_limits($u_id, $pkg_data->exchange_sell_limit, $return_amount, $eth_value, $next_block->price)){exit;}
                                $this->place_sell_order($u_id, $return_amount, $next_block->price, $exch, $eth_value, $next_block->id, $next_block->current_arb_size, $next_block->flag);
                                $order_place_msg++;
                            }
                            
                            if($order_place_msg == 1){
                                echo json_encode(array('success'=>'1', 'msg' => 'Some amount of order is placed.'));exit;
                            }else if($order_place_msg == 2){
                                echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));exit;
                            }else{
                                echo json_encode(array('error'=>'1', 'msg' => 'Order not placed'));exit;
                            }
                        }
                }else{
                    echo json_encode(array('error'=>'1', 'msg' => 'No block match with this price.'));
                    exit;
                }
            }

        }else{
            echo json_encode(array('error'=>'1', 'msg' => "session expired")); exit;
        }
    }
    
    
    
    public function sellOrder_exchange_earned(){
        exit;
        $captcha = $this->input->post('captcha');
	    if(!$this->check_captcha($captcha)){exit;}
        // get user id from session 
        $u_id = $this->session->userdata('u_id');
        //check exchange lock
        if(!$this->exchange_lock($u_id)){exit;}
        // check ETH value
        if(!$eth_value = $this->get_eth_db_value()){exit;}
        
        if($u_id !=''){
            //check if user have arb in pro+ wallet 
            // if($this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = ? AND status =  1 AND activeArb > 1", array($u_id))->row()){
            //     echo json_encode(array('error'=>'1', 'msg' => 'Sorry You are not authorized to do this operation manually.'));
            //     exit;
            // }
            $order_type = 'Sell';
            $amount = doubleval($this->input->post("amount"));
            $amount = $this->getTruncatedValue($amount, 4);
            $price = doubleval($this->input->post("price"));
            $price = $this->getTruncatedValue($price, 6);
            
            $exch= $this->input->post("wallet");
            if($order_type == 'Sell' && $exch != 'exchange_earned'){
                echo json_encode(array('error'=>'1', 'msg' => 'Sell order Only place from exchange earned wallet.')); exit;
            }
            
            // min amount chechk
            if(!$this->min_amount($amount)){exit;}
            //max amount 
            if(!$this->max_amount($amount)){exit;}
            //get price from blockes 
            if($this->input->post("koibe") == 123){ 
                if(!$price = $this->get_blockes_for_price($order_type, $price, $amount, 'from_best_sell')){exit;}
            }else{
                if(!$price = $this->get_blockes_for_price($order_type, $price, $amount)){exit;}
            }
            //user cannot place buy and sell in same golden block
            if(!$this->buy_sell_golden_block($u_id, $price, $order_type)){exit;}
            
            //get user data 
            $user_data = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
            // get package detail 
            $pkg_data = $this->db->query("SELECT * FROM packages WHERE name = ?", array($user_data->package))->row();
            
            //maximum open orders 
            if(!$this->totel_open_orders($u_id, $user_data->package, $order_type)){exit;}
            
            //user can only sell limit from earned wallet equal to today earned
            // if(!in_array($u_id, $this->allow_users)){
            //     $user_abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
            //     if($user_abot_wallet->auto_reinvest == 1){
            //         $auto_reinvest_amount = ($user_abot_wallet->profit/100)*$user_abot_wallet->auto_reinvest_per;
            //         $allow_amount = $user_abot_wallet->profit - $auto_reinvest_amount;
            //     }else{
            //         $allow_amount = $user_abot_wallet->profit;
            //     }
            //     //if allow amount is 0
            //     if($allow_amount <= 0){
            //         echo json_encode(array('error'=>'1', 'msg' => 'Your remaining limit from earned wallet is 0.')); exit;
            //     }
                
            //     if($allow_amount < 5){
            //         $allow_amount = 5;
            //     }
                
            //     $time_before24 = date("Y-m-d H:i:s", strtotime("-29 hours"));
            //     $earned_sell_amount = $this->DB2->query("SELECT sum(amount) as total_earned_sell FROM orders_beta WHERE order_from LIKE 'exchange_earned' AND remark != 'cancel' AND create_time > ? AND user_id = ?", array($time_before24, $u_id))->row();
            //     $check_amount = $earned_sell_amount->total_earned_sell + $amount;
            //     if($check_amount >= $allow_amount){
            //         $remaining_limit = $allow_amount - $earned_sell_amount->total_earned_sell;
            //         if($remaining_limit < 0){$remaining_limit = 0;}
            //         echo json_encode(array('error'=>'1', 'msg' => 'Your remaining limit from earned wallet is '.$this->getTruncatedValue($remaining_limit, 4).' ARB...')); exit;
            //     }
                
            // }
            
                
            //check seller account balance                
            if(!$this->check_seller_wallet_balance($u_id, $amount, $exch)){exit;}
            
            if($amount >= 1){
                if($get_block = $this->DB2->query("SELECT * FROM blocks WHERE price = ? AND status = 0", array($price))->row()){
                    // 33% check add here
                    $allow_size = $this->get_block_sell_size($price);
                    $block_remaining_size = $allow_size - $get_block->current_arb_size;
                    
                    if($block_remaining_size >= $amount){
                        if(!$this->exchange_earned_limits($u_id, $pkg_data->exchange_er_sell_limit, $amount, $eth_value, $price)){exit;}
                        $this->place_sell_order($u_id, $amount, $price, $exch, $eth_value, $get_block->id, $get_block->current_arb_size, $get_block->flag);
                        echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));
                        exit;
                    }else{
                        $order_place_msg = 0;
                        if($block_remaining_size > 0.001){
                            if(!$this->exchange_earned_limits($u_id, $pkg_data->exchange_er_sell_limit, $block_remaining_size, $eth_value, $price)){exit;}
                            $this->place_sell_order($u_id, $block_remaining_size, $price, $exch, $eth_value, $get_block->id, $get_block->current_arb_size, $get_block->flag);
                            $order_place_msg++;
                        }
                        
                        if($order_place_msg > 0){
                            echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));
                            exit;
                        }else{
                            echo json_encode(array('error'=>'1', 'msg' => 'This Block is Full.'));
                            exit;
                        }
                        
                        
                        if($get_block->flag == 'market'){
                            if($order_place_msg > 0){
                                echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));exit;
                            }else{
                                echo json_encode(array('error'=>'1', 'msg' => 'Market Block is full, Please place your order in next block.'));exit;
                            }
                        }
                        
                        $return_amount = $amount - $block_remaining_size;
                        if($return_amount < 0.001){
                            echo json_encode(array('error'=>'1', 'msg' => 'This Block is Full.'));
                            exit;
                        }
                        $next_block = $this->DB2->query("SELECT * FROM blocks WHERE price > ? AND status = 0 ORDER BY price ASC", array($price))->row();
                        // 33% check add here
                        $next_allow_size = $this->get_block_sell_size($next_block->price);
                        $next_block_remaining_size = $next_allow_size - $next_block->current_arb_size;
                        
                        if($next_block_remaining_size >= $return_amount){
                            if(!$this->exchange_earned_limits($u_id, $pkg_data->exchange_er_sell_limit, $return_amount, $eth_value, $next_block->price)){exit;}
                            $this->place_sell_order($u_id, $return_amount, $next_block->price, $exch, $eth_value, $next_block->id, $next_block->current_arb_size, $next_block->flag);
                            $order_place_msg++;
                        }
                        
                        if($order_place_msg == 1){
                            echo json_encode(array('success'=>'1', 'msg' => 'Some amount of order is placed.'));exit;
                        }else if($order_place_msg == 2){
                            echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));exit;
                        }else{
                            echo json_encode(array('error'=>'1', 'msg' => 'Order not placed.'));exit;
                        }
                    }
                }else{
                    echo json_encode(array('error'=>'1', 'msg' => 'No block match with this price.'));
                    exit;
                }
            }

        }else{
            echo json_encode(array('error'=>'1', 'msg' => "session expired")); exit;
        }
    }
    
    public function sellOrder_stop_abot(){
        exit;
        $captcha = $this->input->post('captcha');
	    if(!$this->check_captcha($captcha)){exit;}
        // get user id from session 
        $u_id = $this->session->userdata('u_id');
        //check exchange lock
        if(!$this->exchange_lock($u_id)){exit;}
        // check ETH value
        if(!$eth_value = $this->get_eth_db_value()){exit;}
        
        if($u_id !=''){
            //check if user have arb in pro+ wallet 
            // if($this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = ? AND status =  1 AND activeArb > 1", array($u_id))->row()){
            //     echo json_encode(array('error'=>'1', 'msg' => 'Sorry You are not authorized to do this operation manually.'));
            //     exit;
            // }
            $order_type = 'Sell';
            $amount = doubleval($this->input->post("amount"));
            $amount = $this->getTruncatedValue($amount, 4);
            $price = doubleval($this->input->post("price"));
            $price = $this->getTruncatedValue($price, 6);
            
            $exch= $this->input->post("wallet");
            if($order_type == 'Sell' && $exch != 'stop_abot'){
                echo json_encode(array('error'=>'1', 'msg' => 'Sell order Only place from Stop abot wallet.')); exit;
            }
            
            // min amount chechk
            if(!$this->min_amount($amount)){exit;}
            //max amount 
            if(!$this->max_amount($amount)){exit;}
            //get price from blockes 
            if($this->input->post("koibe") == 123){ 
                if(!$price = $this->get_blockes_for_price($order_type, $price, $amount, 'from_best_sell')){exit;}
            }else{
                if(!$price = $this->get_blockes_for_price($order_type, $price, $amount)){exit;}
            }
            //user cannot place buy and sell in same golden block
            if(!$this->buy_sell_golden_block($u_id, $price, $order_type)){exit;}
            
            //get user data 
            $user_data = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
            // get package detail 
            $pkg_data = $this->db->query("SELECT * FROM packages WHERE name = ?", array($user_data->package))->row();
            
            //maximum open orders 
            if(!$this->totel_open_orders($u_id, $user_data->package, $order_type)){exit;}
                
            //check 20% stop abot
            /*$get_block_to_check = $this->DB2->query("SELECT * FROM blocks WHERE price = ?", array($price))->row();
            $earned_portion = ($get_block_to_check->arb_size / 100) * 20;
            if($exch == 'stop_abot'){
                $earned_sell_amount = $this->DB2->query("SELECT sum(amount) as total FROM orders_beta WHERE block_id = ? AND status = 0 AND order_from LIKE 'stop_abot'", array($get_block_to_check->id))->row();
                if($earned_sell_amount->total >= $earned_portion){
                    echo json_encode(array('error'=>'1', 'msg' => 'Stop abot Wallet portion of block is full, please use next open block.')); exit;
                }else{
                    $remaining_earned_portion = $earned_portion - $earned_sell_amount->total;
                    if($amount > $remaining_earned_portion){
                        $amount = $this->getTruncatedValue($remaining_earned_portion, 4);
                    }
                }
            }*/
                
            //check seller account balance                
            if(!$this->check_seller_wallet_balance($u_id, $amount, $exch)){exit;}
           
            if($amount >= 1){
                if($get_block = $this->DB2->query("SELECT * FROM blocks WHERE price = ? AND status = 0", array($price))->row()){
                    // 33% check add here
                    $allow_size = $this->get_block_sell_size($price);
                    $block_remaining_size = $allow_size - $get_block->current_arb_size;
                    
                    if($block_remaining_size >= $amount){
                        if(!$this->exchange_limits($u_id, $pkg_data->exchange_sell_limit, $amount, $eth_value, $price)){exit;}
                        $this->place_sell_order($u_id, $amount, $price, $exch, $eth_value, $get_block->id, $get_block->current_arb_size, $get_block->flag);
                        echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));
                        exit;
                    }else{
                        $order_place_msg = 0;
                        if($block_remaining_size > 0.001){
                            if(!$this->exchange_limits($u_id, $pkg_data->exchange_sell_limit, $block_remaining_size, $eth_value, $price)){exit;}
                            $this->place_sell_order($u_id, $block_remaining_size, $price, $exch, $eth_value, $get_block->id, $get_block->current_arb_size, $get_block->flag);
                            $order_place_msg++;
                        }
                        
                        if($order_place_msg > 0){
                            echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));
                            exit;
                        }else{
                            echo json_encode(array('error'=>'1', 'msg' => 'This Block is Full.'));
                            exit;
                        }
                        
                        
                        if($get_block->flag == 'market'){
                            if($order_place_msg > 0){
                                echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));exit;
                            }else{
                                echo json_encode(array('error'=>'1', 'msg' => 'Market Block is full, Please place your order in next block.'));exit;
                            }
                        }
                        
                        $return_amount = $amount - $block_remaining_size;
                        if($return_amount < 0.001){
                            echo json_encode(array('error'=>'1', 'msg' => 'This Block is Full.'));
                            exit;
                        }
                        $next_block = $this->DB2->query("SELECT * FROM blocks WHERE price > ? AND status = 0 ORDER BY price ASC", array($price))->row();
                        // 33% check add here
                        $next_allow_size = $this->get_block_sell_size($next_block->price);
                        $next_block_remaining_size = $next_allow_size - $next_block->current_arb_size;
                        
                        if($next_block_remaining_size >= $return_amount){
                            if(!$this->exchange_limits($u_id, $pkg_data->exchange_sell_limit, $return_amount, $eth_value, $next_block->price)){exit;}
                            $this->place_sell_order($u_id, $return_amount, $next_block->price, $exch, $eth_value, $next_block->id, $next_block->current_arb_size, $next_block->flag);
                            $order_place_msg++;
                        }
                        
                        if($order_place_msg == 1){
                            echo json_encode(array('success'=>'1', 'msg' => 'Some amount of order is placed.'));exit;
                        }else if($order_place_msg == 2){
                            echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));exit;
                        }else{
                            echo json_encode(array('error'=>'1', 'msg' => 'Order not placed.'));exit;
                        }
                    }
                }else{
                    echo json_encode(array('error'=>'1', 'msg' => 'No block match with this price.'));
                    exit;
                }
            }

        }else{
            echo json_encode(array('error'=>'1', 'msg' => "session expired")); exit;
        }
    }
    
    public function sellOrder_pro_plus(){
        exit;
        echo 'runing';
        if($this->db->query("SELECT * FROM pro_plus_crons_setting WHERE name = 'orders_cron_status'")->row()->value == 0){exit;}
        //get user id from session 
        $unique_stamp = $this->db->query("SELECT * FROM pro_plus_crons_setting WHERE name = 'order_unique_stamp'")->row()->value;
        $u_id = 1001;
        //check exchange lock
        if(!$this->exchange_lock($u_id)){exit;}
        // check ETH value
        if(!$eth_value = $this->get_eth_db_value()){exit;}
        
        if($u_id !=''){
            $order_type = 'Sell';
            
            $active_arb_proplus = 0;
            if($proplus_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = 1001")->row()){
                $open_orders_amount_pp = $this->DB2->query("SELECT sum(amount) as total_amount FROM orders_beta WHERE user_id = ? AND order_type = ? AND status = ? AND order_from like 'pro_plus%'", array($u_id, 'Sell', 0))->row()->total_amount;
                if(!empty($open_orders_amount_pp) && $open_orders_amount_pp > 0){
                    $active_arb_proplus = $proplus_wallet->activeArb - $open_orders_amount_pp;
                }else{
                    $active_arb_proplus = $proplus_wallet->activeArb;
                }
            }
            
            //$amount = doubleval($this->input->post("amount"));
            $amount = $this->getTruncatedValue($active_arb_proplus, 4);
            $price = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 AND current_eth_size > 0")->row()->price;
            $price = $this->getTruncatedValue($price, 6);
            
            //print_r($price); exit;
            
            $exch= 'exchange';
            if($order_type == 'Sell' && $exch != 'exchange'){
                echo json_encode(array('error'=>'1', 'msg' => 'Sell order Only place from exchange.')); exit;
            }
            // min amount chechk
            if(!$this->min_amount($amount)){exit;}
            
            //get price from blockes 
            if(!$price = $this->get_blockes_for_price($order_type, $price)){exit;}
                
            //check seller account balance                
            if(!$this->check_seller_wallet_balance($u_id, $amount, $exch)){exit;}
            
            if($amount >= 1){
                if($get_block = $this->DB2->query("SELECT * FROM blocks WHERE price = ? AND status = 0", array($price))->row()){
                    // 33% check add here
                    $allow_size = $this->get_block_sell_size($price);
                    $block_remaining_size = $allow_size - $get_block->current_arb_size;
                    
                    $exch = 'pro_plus_'.$unique_stamp;
                    
                    if($block_remaining_size >= $amount){
                        $order_id = $this->place_sell_order($u_id, $amount, $price, $exch, $eth_value, $get_block->id, $get_block->current_arb_size, $get_block->flag);
                        echo json_encode(array('success'=>'1', 'order_id' => $order_id, 'order_amount' => $amount));
                        exit;
                    }else{
                        $order_place_msg = 0;
                        if($block_remaining_size > 1){
                            $order_id = $this->place_sell_order($u_id, $block_remaining_size, $price, $exch, $eth_value, $get_block->id, $get_block->current_arb_size, $get_block->flag);
                            $order_place_msg++;
                        }
                        if($order_place_msg == 1){
                            echo json_encode(array('success'=>'1', 'order_id' => $order_id, 'order_amount' => $block_remaining_size));exit;
                        }else{
                            echo json_encode(array('error'=>'1', 'msg' => 'Order not placed'));exit;
                        }
                    }
                }else{
                    echo json_encode(array('error'=>'1', 'msg' => 'No block match with this price.'));
                    exit;
                }
            }

        }else{
            echo json_encode(array('error'=>'1', 'msg' => "session expired")); exit;
        }
    }
    
    public function buy_for_abot(){
        $u_id = $this->session->userdata('u_id');
        //check exchange lock
        if(!$this->exchange_lock($u_id)){exit;}
        // check ETH value
        if(!$eth_value = $this->get_eth_db_value()){exit;}
        if($u_id !=''){
            $total_eth = $this->input->post('total_eth');
            if($total_eth <= 0){exit;}
            $order_type = 'Buy';
            $market_block = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 AND flag = 'sell_side' ORDER BY price ASC LIMIT 1")->row();
            $remaining_eth_size = $market_block->eth_size - $market_block->current_eth_size;
            if($remaining_eth_size <= 0.001 ){
                echo json_encode(array('error'=>'1', 'msg' => "Not enough space in top sell Block.")); exit;
            }
            if($remaining_eth_size < $total_eth){
                $total_eth = $remaining_eth_size;
            }
            //check balance of system wallet 
            $sys_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($u_id))->row();
            if($sys_wallet->activeEth < $total_eth){
                echo json_encode(array('error'=>'1', 'msg' => "Balance not enough")); exit;
            }
            
            // get from system wallet 
            $update_system_wallet = $sys_wallet->activeEth - $total_eth;
            $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($update_system_wallet, $u_id));
            
            //credit into exchange wallet 
            $ex_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row();
            $update_ex_wallet = $ex_wallet->activeEth + $total_eth;
            $this->db->query("UPDATE exchange_wallet SET activeEth = ? WHERE user_id = ?", array($update_ex_wallet, $u_id));
            
            $amount = $total_eth / $market_block->price;
            
            $ex_wallet_2 = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row();
            if($ex_wallet_2->activeEth >= $total_eth){
                $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, eth_usd, auto_buy, block_id, side, order_from) VALUES (?,?,?,?,?,?,?,?,?)",
                    array($u_id, 'Buy', $this->getTruncatedValue($amount, 4), $market_block->price, $eth_value, 0, $market_block->id, $market_block->flag, 'for_abot'));
                //update block current eth size
                $update_block_eth_size = $market_block->current_eth_size + $total_eth;
                $this->DB2->query("UPDATE blocks SET current_eth_size = ? WHERE id = ?", array($update_block_eth_size, $market_block->id));
            }
            
             
            //logs
            $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'systemWallet_ETH','-'.$total_eth, $sys_wallet->activeEth, "Transfer ETH from wallet to exchange for aBOT"));
            $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchangeWallet_ETH',$total_eth, $ex_wallet->activeEth, "Credit ETH in exchange for aBOT"));
                        
            echo json_encode(array('success'=>'1', 'msg' => 'Order is placed.'));
            
        }else{
            echo json_encode(array('error'=>'1', 'msg' => "session expired")); exit;
        }
    }
    
    
    public function close_order_by_id(){
        $exchange_status = $this->db->query("SELECT * FROM admin_locks WHERE name = 'exchange_lock'")->row();
        $unlock_user = array(13870, 791138);
        if($exchange_status->lock_status == 1 && !in_array($u_id, $unlock_user)){
            echo json_encode(array('error'=>'1', 'msg' => 'Sorry for inconvenience this feature is temporarily locked.'));
            exit;
        }
        
        $u_id = $this->session->userdata('u_id');
        //check if user have arb in pro+ wallet 
        // if($this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = ? AND status =  1 AND activeArb > 1", array($u_id))->row()){
        //     echo json_encode(array('error'=>'1', 'msg' => 'Sorry You are not authorized to do this operation manually.'));
        //     exit;
        // }

        $order_id = $this->input->post('order_id');
        //get user data
        $user_data = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
        // get package detail
        $pkg_data = $this->db->query("SELECT * FROM packages WHERE name = ?", array($user_data->package))->row();
        
        if($order_id != ''){
            $order_cat = $this->DB2->query("SELECT * FROM orders_beta WHERE id=? AND user_id = ? ", array($order_id, $u_id))->row();
            // if($order_cat->order_type == 'Buy'){
            //     echo json_encode(array('error'=>'1', 'msg' => "You are not able to cancel buy order.")); exit;
            // }
            $get_current_block = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 AND flag = 'market'")->row();
            
            if($get_current_block->id == $order_cat->block_id){
                echo json_encode(array('error'=>'1', 'msg' => "You are not able to cancel order from market block.")); exit;
            }
            $get_block_by_id = $this->DB2->query("SELECT * FROM blocks WHERE id = ?", array($order_cat->block_id))->row();
            if($get_block_by_id->status != 0){
                echo json_encode(array('error'=>'1', 'msg' => "You are not able to cancel this order because Block is under execution state.")); exit;
            }
            // else if($get_block_by_id->arb_size == $get_block_by_id->current_arb_size){
            //     echo json_encode(array('error'=>'1', 'msg' => "You are not able to cancel this order because this Block is Full.")); exit;
            // }
            
            if($this->DB2->query("SELECT count(*) as tot FROM blocks WHERE id = ?", array($order_cat->block_id))->row()->tot < 1){
                echo json_encode(array('error'=>'1', 'msg' => "You are not able to cancel this order because Block is under execution state.")); exit;
            }
            
            $allow_ids = array(77836, 846, 9,  43, 59570);
            if($order_cat->order_type == "Sell" && !in_array($u_id, $allow_ids)){
                $before_24hour = date("Y-m-d H:i:s", strtotime('-28 hour'));
                $co = $this->DB2->query("SELECT count(*) as co FROM orders_beta WHERE user_id = ? AND order_type = ? AND status = ? AND remark = ? AND created_at > ?", array($u_id, 'Sell', 1, 'cancel', $before_24hour))->row()->co;
                if($co >= $pkg_data->allow_orders){
                    echo json_encode(array('error'=>'1', 'msg' => "You are not able to cancel more then ".$pkg_data->allow_orders." orders in 24 hours."));
					exit;
                }
                
            }
            
            $this->DB2->query("UPDATE orders_beta SET status = ?, remark = ? WHERE id = ? AND user_id = ? AND status = ? AND order_type = ?", array(1,'cancel', $order_id, $u_id, 0, $order_cat->order_type));
            
            // $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'close_order', $amount, 'Close order return amount', $user_plus_wallet->freeArb));
                        
            
            //get block
            $block_cancel = $this->DB2->query("SELECT * FROM blocks WHERE id = ?", array($order_cat->block_id))->row();
            if($order_cat->order_type == 'Sell'){
                $return_block_arb_size = $block_cancel->current_arb_size - $order_cat->amount;
                if($return_block_arb_size < 0.001){$return_block_arb_size = 0;}
                $this->DB2->query("UPDATE blocks SET current_arb_size = ? WHERE id = ?", array($return_block_arb_size, $order_cat->block_id));
            }else if($order_cat->order_type == 'Buy'){
                $order_eth_amount = $order_cat->amount * $order_cat->price;
                $return_block_eth_size = $block_cancel->current_eth_size - $order_eth_amount;
                if($return_block_eth_size < 0.001){$return_block_eth_size = 0;}
                $this->DB2->query("UPDATE blocks SET current_eth_size = ? WHERE id = ?", array($return_block_eth_size, $order_cat->block_id));
            }
            
            if($order_cat->order_from != "exchange_earned" && $order_cat->order_type == "Sell"){
                // if order is from exchange wallet .. 
                $order_amount_limit = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row()->order_amount_limit;
                $order_arb_price_usd = $order_cat->price * $order_cat->eth_usd;
                $total_order_ammount = $order_amount_limit - ($order_cat->amount * $order_arb_price_usd);
                
                if($total_order_ammount < 0){
                    $this->db->query("UPDATE exchange_wallet SET order_amount_limit = ? WHERE user_id = ?", array(0, $u_id));
                }
                else{
                    $this->db->query("UPDATE exchange_wallet SET order_amount_limit = ? WHERE user_id = ?", array($total_order_ammount, $u_id));
                }
            }
            
            if($order_cat->order_from == "exchange_earned" && $order_cat->order_type == "Sell"){
                //if order is from exchange wallet .. 
                $before1hour = date("Y-m-d H:i:s", strtotime('-6 hour'));
                if($order_cat->create_time > $before1hour){
                    $order_amount_limit = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row()->er_order_amount_limit;
                    
                    $order_arb_price_usd = $order_cat->price * $order_cat->eth_usd;
                    $total_order_ammount = $order_amount_limit - ($order_cat->amount * $order_arb_price_usd);
                    
                    if($total_order_ammount < 0){
                        $this->db->query("UPDATE exchange_wallet SET er_order_amount_limit = ? WHERE user_id = ?", array(0, $u_id));
                    }
                    else{
                        $this->db->query("UPDATE exchange_wallet SET er_order_amount_limit = ? WHERE user_id = ?", array($total_order_ammount, $u_id));
                    }
                }
            }
            
            echo json_encode(array('success'=>'1', 'msg' => "Your order cancel successfully."));
            exit;
        }else{
            echo json_encode(array('error'=>'1', 'msg' => "User session expired")); exit;
        }
    }
    
    public function exchange_wallet_balance(){
        $u_id = $this->session->userdata('u_id');
        if($u_id == ''){
            echo 'needlogin';
            exit;
        }
        $arb_usd = json_decode(file_get_contents('https://www.arbitraging.co/platform/arb_valueLive'));
        
        //get user data 
        $user_data = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
        // get package detail 
        $pkg_data = $this->db->query("SELECT * FROM packages WHERE name = ?", array($user_data->package))->row();
        
        $result = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row();
        $arb_value = 0;
        $eth_value = 0;

        // get arb value
        $open_orders_amount = $this->DB2->query("SELECT sum(amount) as total_amount FROM orders_beta WHERE user_id = ? AND order_type = ? AND status = ? AND order_from = 'exchange'", array($u_id, 'Sell', 0))->row()->total_amount;
        if(!empty($open_orders_amount) && $open_orders_amount > 0){
            $arb_value = $result->activeArb - $open_orders_amount;
        }else{
            $arb_value = $result->activeArb;
        }

        // get eth value
        $old_order_values = 0;
        $open_buy_order = $this->DB2->query("SELECT * FROM orders_beta WHERE user_id = ? AND order_type = ? AND status = ?", array($u_id, 'Buy', 0))->result();
        foreach($open_buy_order as $order){
            $eth_value = $order->amount * $order->price;
            $old_order_values = $old_order_values + $eth_value;
        }

        if($old_order_values > 0){
            $eth_value = $result->activeEth - $old_order_values;
        }else{
            $eth_value = $result->activeEth;
        }
        
        $active_arb_earned = 0;
        if($exEarned_wallet = $this->db->query("SELECT * FROM exchange_earned_wallet WHERE user_id = ?", array($u_id))->row()){
            $open_orders_amount_er = $this->DB2->query("SELECT sum(amount) as total_amount FROM orders_beta WHERE user_id = ? AND order_type = ? AND status = ? AND order_from = 'exchange_earned'", array($u_id, 'Sell', 0))->row()->total_amount;
            if(!empty($open_orders_amount_er) && $open_orders_amount_er > 0){
                $active_arb_earned = $exEarned_wallet->activeArb - $open_orders_amount_er;
            }else{
                $active_arb_earned = $exEarned_wallet->activeArb;
            }
        }
        
        $active_arb_stop = 0;
        if($stop_wallet = $this->db->query("SELECT * FROM stop_abot_wallet WHERE user_id = ?", array($u_id))->row()){
            $open_orders_amount_stop = $this->DB2->query("SELECT sum(amount) as total_amount FROM orders_beta WHERE user_id = ? AND order_type = ? AND status = ? AND order_from = 'stop_abot'", array($u_id, 'Sell', 0))->row()->total_amount;
            if(!empty($open_orders_amount_stop) && $open_orders_amount_stop > 0){
                $active_arb_stop = $stop_wallet->activeArb - $open_orders_amount_stop;
            }else{
                $active_arb_stop = $stop_wallet->activeArb;
            }
        }
        
        $remain_limit_usd = $pkg_data->exchange_sell_limit - $result->order_amount_limit;
        $remain_limit_arb = $remain_limit_usd/$arb_usd->USD;
        if($remain_limit_arb < 0){
            $remain_limit_arb = 0;
        }else if($remain_limit_arb > $arb_value){
            $remain_limit_arb = $arb_value;
        }
        
        $er_remain_limit_usd = $pkg_data->exchange_er_sell_limit - $result->er_order_amount_limit;
        $er_remain_limit_arb = $er_remain_limit_usd/$arb_usd->USD;
        if($er_remain_limit_arb < 0){
            $er_remain_limit_arb = 0;
        }else if($er_remain_limit_arb > $active_arb_earned){
            $er_remain_limit_arb = $active_arb_earned;
        }
        
        
        // add into array
        if($eth_value < 0.001){
           $eth_value = 0; 
        }
        if($arb_value < 0.001){
           $arb_value = 0; 
        }
        if($active_arb_stop < 0.001){
            $active_arb_stop = 0;
        }
        if($active_arb_earned < 0.001){
            $active_arb_earned = 0;
        }
        
        $arr = array('activeArb' => $this->getTruncatedValue($arb_value,4) , 'activeEth' => $this->getTruncatedValue($eth_value,6).'', 'activeArb_earned' => $this->getTruncatedValue($active_arb_earned,4).'', 'activeArb_stop_abot'=>$this->getTruncatedValue($active_arb_stop,4).'', 'ex_limit' => $this->getTruncatedValue($remain_limit_arb, 4),'ex_er_limit' => $this->getTruncatedValue($er_remain_limit_arb, 4));

        $arr = json_encode($arr);
        print_r($arr);
    }
    
    public function user_orders(){
        $u_id = $this->session->userdata('u_id');
        if($u_id == ''){
            echo 'needlogin';
            exit;
        }
        
        // current user open orders
        $openOrders =  $this->DB2->query("SELECT * FROM orders_beta WHERE user_id = $u_id AND status = 0")->result();
        $openOrders = array_reverse($openOrders);
        
        // current user close orders
        $closeOrders =  $this->DB2->query("SELECT * FROM orders_beta WHERE user_id = $u_id AND status = 1 ORDER BY created_at DESC LIMIT 40")->result();
        
         $result = [
            "openOrders" => $openOrders,
            "closeOrders" => $closeOrders
        ];
        $result = json_encode($result);
        print_r($result);
        
    }
    
    public function distribute_cron(){
        echo 'runing';
        $arb_usd_value = $this->db->query("SELECT * FROM api_settings WHERE name = ?", array('abot_arb'))->row()->value;
        if($this->DB2->query("SELECT * FROM cron_checks WHERE name = 'distribute_cron'")->row()->status == 0){
            $this->DB2->query("UPDATE cron_checks SET status = 1 WHERE name = 'distribute_cron'");
            
            $complete_blocks = $this->DB2->query("SELECT * FROM blocks WHERE status = 1")->result();
            foreach($complete_blocks as $block){
                 $this->DB2->query("DELETE from blocks WHERE id = ?", array($block->id));
                 $block->price = $this->getTruncatedValue($block->price, 6);
                 //$block->price = doubleval($block->price).'';
                 $this->DB2->query("INSERT INTO blocks_history (block_id, data, status, flag) VALUES (?,?,?,?)", array($block->id, json_encode($block), $block->status, $block->flag));
                $bonus_per = 0;
                if($block->feature == 'gold'){
                    $bonus_per = $block->bonus; 
                }
                
                $block_orders = $this->DB2->query("SELECT * FROM orders_beta WHERE status = ? AND block_id = ?", array(0, $block->id))->result();
                foreach($block_orders as $order){
                    $order = $this->DB2->query("SELECT * FROM orders_beta WHERE id = ?", array($order->id))->row();
                    if($order->status == 1){continue;}
                    
                    //get user data 
                    $user_data = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($order->user_id))->row();
                    // get package detail 
                    $pkg_data = $this->db->query("SELECT * FROM packages WHERE name = ?", array($user_data->package))->row();
                    
                    $fee = $this->DB2->query("SELECT * FROM order_fee WHERE type='exchange' AND status = 1")->result();
                    // fee discount acording to package
                    $eth_fee = $arb_fee = 0.5;
                    if(isset($pkg_data->fee_discount)){
                        $eth_fee = $fee[1]->fee - (($fee[1]->fee/100) * $pkg_data->fee_discount);
                        $arb_fee = $fee[0]->fee - (($fee[0]->fee/100) * $pkg_data->fee_discount);
                    }
                    // auto buy fee +1%
                    if($order->auto_buy == 1){$arb_fee = $arb_fee + 1;}
                    
                    if($order->order_type == 'Sell'){
                        if($order->side == 'buy_side'){$eth_fee = 5;}
                        if($order->side == 'sell_side'){$eth_fee = 0;}
                        
                        if($order->order_from == 'exchange'){$table_name = 'exchange_wallet';}
                        else if($order->order_from == 'exchange_earned'){$table_name = 'exchange_earned_wallet';}
                        else if($order->order_from == 'stop_abot'){$table_name = 'stop_abot_wallet';}
                        else{$table_name = 'exchange_wallet'; $eth_fee = 2.5;}
                        
                        $seller_exchange_wallet = $this->db->query("SELECT * FROM ".$table_name." WHERE user_id = ?", array($order->user_id))->row();
                        if(($seller_exchange_wallet->activeArb + 0.00015) >= $order->amount){
                            
                            //update seller account
                            $order_eth = $order->amount * $order->price;
                            $cal_eth_fee = ($order_eth / 100) * $eth_fee;
                            $seller_exchange_wallet_eth = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($order->user_id))->row();
                            $seller_new_eth = $seller_exchange_wallet_eth->activeEth + ($order_eth - $cal_eth_fee);
                            $seller_new_arb = $seller_exchange_wallet->activeArb - $order->amount;
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ? WHERE user_id = ?", array($seller_new_eth, $order->user_id));
                            $this->db->query("UPDATE ".$table_name." SET activeArb = ? WHERE user_id = ?", array($seller_new_arb, $order->user_id));
                            
                            
                            //print_r($seller_exchange_wallet_eth); exit;
                            if($order->side == 'buy_side'){
                                //update eth_bonus_pool
                                $get_eth_pool_wallet = $this->db->query("SELECT * FROM eth_bonus_pool WHERE id = 1 LIMIT 1")->row();
                                $pool_new_eth = $get_eth_pool_wallet->activeEth + $cal_eth_fee;
                                $this->db->query("UPDATE eth_bonus_pool SET activeEth = ? WHERE id = ?", array($pool_new_eth, 1));
                            }else if($cal_eth_fee > 0){
                                    //update admin wallet
                                    $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                                    $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                                    $this->db->query("UPDATE admin_wallet SET eth = ? WHERE id = ?", array($admin_new_eth, 1));
                            }
                            //update order
                            $this->DB2->query("UPDATE orders_beta SET status = 1 WHERE id= ? AND user_id = ?", array($order->id, $order->user_id));
                            
                            //logs of seller
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,$order->order_from.'_SellARB','-'.$order->amount, $order->id, $seller_exchange_wallet->activeArb, "Sell ARB in ".$order->order_from));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'exchangeWallet_BuyETH',$order_eth, $order->id, $seller_exchange_wallet_eth->activeEth, "Buy Eth in Exchange"));
                            if($cal_eth_fee > 0 && $order->side != 'buy_side'){
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $order->id, ($seller_exchange_wallet->activeEth + $order_eth), "Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $order->id, $get_admin_wallet->eth, "Fee"));
                            }else if($cal_eth_fee > 0 && $order->side == 'buy_side'){
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $order->id, ($seller_exchange_wallet->activeEth + $order_eth), "Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'ethBonusPool_ETH',$cal_eth_fee, $order->id, $get_eth_pool_wallet->activeEth, "Fee"));
                            }
                        }
    
                        
                    }else if($order->order_type == 'Buy'){
                        $buyer_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($order->user_id))->row();
                        $order_eth = $order->amount * $order->price;
                        if($buyer_exchange_wallet->activeEth >= $order_eth){
                            // update buyer exchange wallet
                            if($order->order_from == 'for_abot'){
                                $extar_bonus = ($order->amount / 100) * 5;
                                $arb_amount = $order->amount + $extar_bonus;
                                $arb_amount_usd = $arb_amount * $arb_usd_value;
                                //update_abot
                                $abot = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($order->user_id))->row();
                                $update_abot = $abot->pending + $arb_amount_usd;
                                $this->db->query("UPDATE abot_wallet SET pending = ? WHERE user_id = ?", array($update_abot, $order->user_id));
                                //update exchange wallet 
                                $buyer_new_eth = $buyer_exchange_wallet->activeEth - $order_eth;
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ? WHERE user_id = ?", array($buyer_new_eth, $order->user_id));
                                
                            }else{
                                $bonus_flag = false;
                                if($order->side == 'buy_side'){
                                    $cal_arb_fee = ($order->amount / 100) * 1;
                                    $buyer_new_eth = $buyer_exchange_wallet->activeEth - $order_eth;
                                    $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($order->amount - $cal_arb_fee);
                                    $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $order->user_id));
                                    
                                    //update admin wallet
                                    $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                                    $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                                    $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = ?", array($admin_new_arb, 1));
                                }else{
                                    $bonus_flag = true;
                                    $market_bonus = ($order->amount / 100) * 3;
                                    $buyer_new_eth = $buyer_exchange_wallet->activeEth - $order_eth;
                                    $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($order->amount + $market_bonus);
                                    $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $order->user_id));
                                }
                            }
                            //update order
                            $this->DB2->query("UPDATE orders_beta SET status = 1 WHERE id= ? AND user_id = ?", array($order->id, $order->user_id));
                            
                            
                            if($order->order_from == 'for_abot'){
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'exchangeWallet_SellETH','-'.$order_eth, $order->id,$buyer_exchange_wallet->activeEth, "Sell ETH in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'exchangeWallet_BuyARB',$order->amount, $order->id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange"));
                                //bonus log  
                                $bonus_last_blnc = ($buyer_exchange_wallet->activeArb + $order->amount);
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'exchangeWallet_ARB_bonus',$extar_bonus, $order->id, $bonus_last_blnc, "5% bonus to move in aBOT."));
                                $last_blnc_after_bonus = $bonus_last_blnc + $extar_bonus;
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", array($order->user_id,'abot_pending_$',$arb_amount_usd, $last_blnc_after_bonus, "Credit $ in aBOT pending.", $arb_usd_value));
                                        
                                //update bonus table 
                                $get_bonus = $this->db->query("SELECT * FROM bonus WHERE id = 1")->row();
                                $update_arb_bonus = $get_bonus->arb + $extar_bonus;
                                $this->db->query("UPDATE bonus SET arb = ? WHERE id = 1", array($update_arb_bonus));
                                
                            }else{
                                //logs of buyer
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'exchangeWallet_SellETH','-'.$order_eth, $order->id,$buyer_exchange_wallet->activeEth, "Sell ETH in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'exchangeWallet_BuyARB',$order->amount, $order->id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange"));
                                if($bonus_flag){
                                    $bonus_last_blnc = ($buyer_exchange_wallet->activeArb + $order->amount);
                                    $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'exchangeWallet_ARB_bonus',$market_bonus, $order->id, $bonus_last_blnc, "3% bonus to place in Sell block."));
                                
                                }else{
                                    $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $order->id, ($buyer_exchange_wallet->activeArb + $order->amount),"Fee"));
                                    $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($order->user_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $order->id, $get_admin_wallet->arb, "Fee"));
                                }
                                    
                            }
                        }
                    }
                }
                
            }
            
        $this->DB2->query("UPDATE cron_checks SET status = 0 WHERE name = 'distribute_cron'");
        }
        
    }
    
    public function get_blocks_data(){
        $u_id = $this->session->userdata('u_id');
        if($u_id == ''){
            echo 'user session expired';
            exit;
        }
        
         $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
         if (!$blocks_data = $this->cache->get('blocks_data')){
        
            $blocks = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 ORDER BY price ASC LIMIT 215 offset 185")->result();
            //print_r($blocks); exit;
            $open_blocks =  array();
            foreach($blocks as $block){
                // if($block->flag == 'market'){
                //     $market_create = $block->created_at;
                // }
                
                if($block->flag == 'buy_side'){
                    $arb_allow_size = $block->current_eth_size / $block->price;
                    $block->arb_size = $arb_allow_size;
                    if($block->current_arb_size > $arb_allow_size){
                        $block->current_arb_size = $arb_allow_size;
                    }
                }
                //else if($block->flag == 'sell_side'){
                //     $block->arb_size = $block->arb_size / 2;
                //     $block->eth_size = $block->eth_size / 2;
                //     if($block->current_arb_size > $block->arb_size){
                //         $block->current_arb_size = $block->arb_size;
                //     }
                //     if($block->current_eth_size > $block->eth_size){
                //         $block->current_eth_size = $block->eth_size;
                //     }
                    
                    if($block->current_arb_size <= 7){
                        $block->current_arb_size = 0;
                    }
                    
                    
                // }
                
                
                
                $open_blocks[] = array(
                    'price' => $block->price,
                    'arb_size' => $this->getTruncatedValue($block->arb_size, 3).'',
                    'eth_size' => $block->eth_size,
                    'current_arb_size' => $this->getTruncatedValue($block->current_arb_size, 3),
                    'current_eth_size' => $this->getTruncatedValue($block->current_eth_size, 3), 
                    'flag' => $block->flag,
                    );
               
            }
            
            $last_5_blocks = $this->DB2->query("SELECT * FROM blocks_history WHERE status = 1 ORDER BY complete_at DESC LIMIT 5")->result();
            $close_blocks = array();
            foreach($last_5_blocks as $blk){
                $aaa = strtotime($blk->complete_at);
                $bbb = strtotime("-5 hour");
                
                $diff = round(($bbb - $aaa)/60, 2);
                
                $decode = json_decode($blk->data);
                $close_blocks[] = array(
                                'price' => $this->getTruncatedValue($decode->price, 6).'',
                                'complete_at' => date("Y-m-d H:i", strtotime($blk->complete_at))
                               );
            }
            
            $aaa = strtotime(self::$settings['block_time_stamp']);
            $bbb = strtotime(date("Y-m-d H:i:s"));
            $market_time_diff = ($bbb - $aaa);
            $rev_min = $this->DB2->query("SELECT * FROM block_settings WHERE name = 'block_revert_min'")->row()->value;
            $market_time_diff = ($rev_min * 60) - $market_time_diff;
            //echo $market_time_diff; exit;
            
            if($market_time_diff < 0){
                $market_time_diff = 0;
            }
            
            $get_market_eth = $this->DB2->query("SELECT * FROM block_settings WHERE name = 'market_block_eth'")->row()->value;
            
            $arr = array(
                'open_blocks' => $open_blocks,
                'close_blocks' => $close_blocks,
                'market_time_diff' => gmdate("i:s", $market_time_diff).'',
                'eth_sold_market' => $get_market_eth.''
                );
            
            $blocks_data = json_encode($arr);
            $this->cache->save('blocks_data', $blocks_data, 10);
         }
        print_r($blocks_data);
    }
    
    // public function get_block_info(){
    //     exit;
    //     $price = $this->input->post('price');
        
    //     if(!$block = $this->DB2->query("select * from blocks WHERE price = ? AND status = 0", array($price))->row()){
    //         echo json_encode(array('error'=>'1', 'msg'=>'price not valid')); exit;
    //     }
        
    //     $stop_abot = $this->DB2->query("SELECT sum(amount) as total FROM orders_beta WHERE block_id = ? AND status = 0 AND order_type = 'Sell' AND order_from = 'stop_abot'", array($block->id))->row()->total;
    //     $exchange = $this->DB2->query("SELECT sum(amount) as total FROM orders_beta WHERE block_id = ? AND status = 0 AND order_type = 'Sell' AND order_from = 'exchange'", array($block->id))->row()->total;
    //     $exchange_earned = $this->DB2->query("SELECT sum(amount) as total FROM orders_beta WHERE block_id = ? AND status = 0 AND order_type = 'Sell' AND order_from = 'exchange_earned'", array($block->id))->row()->total;
               
    //           //echo $stop_abot.'<br>'.$exchange.'<br>'.$exchange_earned.'<br>'.$blocks->arb_size; exit;
               
    //     if($stop_abot > 0){$stop_abot_per = ($stop_abot * 100)/$block->arb_size; }else{$stop_abot_per = 0;}
    //     if($exchange > 0){$exchange_per = ($exchange * 100)/$block->arb_size;}else{$exchange_per = 0;}
    //     if($exchange_earned > 0){$exchange_earned_per = ($exchange_earned * 100)/$block->arb_size;}else{$exchange_earned_per = 0;}
    //     if($stop_abot_per >= 20) $stop_abot_comment = 'Full'; else $stop_abot_comment = 'Available';
    //     if($exchange_per >= 20) $exchange_comment = 'Full'; else $exchange_comment = 'Available';
        
    //     $open_blocks = array(
    //                 'stop_abot' => $stop_abot_comment,
    //                 'exchange' => $exchange_comment
    //                 );
    //     print_r(json_encode($open_blocks));
    // }

    public function one_hour_blocks(){
        //exit;
        echo "runing";
        //sleep(8);
        if($this->check_blockes()){
            $current_time = date("Y-m-d H:i:s");
            $last_time_stamp = $this->DB2->query("SELECT value FROM block_settings WHERE name = 'block_time_stamp'")->row()->value;
            $rev_text = '+'.self::$settings['block_revert_min'].' min';
            $limit_time = date("Y-m-d H:i:s", strtotime($rev_text, strtotime($last_time_stamp)));
            
            $market_eth = 0;
            $market_eth = $this->DB2->query("SELECT value FROM block_settings WHERE name = 'market_block_eth'")->row()->value;
            $market_eth = floatval($market_eth);
            if($current_time > $limit_time){
                $this->DB2->query("UPDATE block_settings SET value = ? WHERE name = 'market_block_eth'", array(0));
                $this->DB2->query("UPDATE block_settings SET value = ? WHERE name = 'block_time_stamp'", array($current_time));
                // down
                if($market_eth < 49.99){
                    $lowest_price = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 ORDER BY price ASC")->row();
                    $new_price = $lowest_price->price - self::$settings['block_span'];
                    if($new_price < 0.000001){return false;}
                    $eth_size = self::$settings['block_size'];
                    $arb_size = self::$settings['block_size'] / $new_price;
                    if($this->DB2->query("INSERT INTO blocks (price, arb_size, eth_size, current_arb_size, flag) VALUES (?,?,?,?,?)", array($new_price, $this->getTruncatedValue($arb_size, 2), $eth_size, 7, 'buy_side'))){
                        $last_inserted = $this->DB2->insert_id();
                        if($last_inserted > 0){
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                                array(77836, 'Sell', 2, $new_price, $last_inserted, 500, 'exchange', 'sell_side'));
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                                array(82, 'Sell', 3, $new_price, $last_inserted, 500, 'exchange', 'sell_side'));
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                                array(783195, 'Sell', 1, $new_price, $last_inserted, 500, 'exchange', 'sell_side'));
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                                array(83632, 'Sell', 1, $new_price, $last_inserted, 500, 'exchange', 'sell_side'));
                                
                            
                        }
                    }
                    
                    //remove highest block
                    $highest_block = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 order by price DESC LIMIT 1")->row();
                    if($highest_block->current_arb_size > 0){
                            $this->cancel_orders($highest_block->id);   
                    }
                    $this->DB2->query("DELETE FROM blocks WHERE id = ?", array($highest_block->id));
                    
                    //move top buy to top sell (self::$settings['no_of_blockes'] / 2) - 1;
                    if($top_buy = $this->DB2->query("SELECT * FROM blocks WHERE flag = 'buy_side' ORDER BY price DESC LIMIT 1")->row()){
                        // update to sell side
                        $this->DB2->query("UPDATE blocks SET flag = ? WHERE id = ?", array('sell_side', $top_buy->id));
                    }
                    
                }else if($market_eth >= 49.99){
                    $high_price = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 ORDER BY price DESC")->row();
                    $new_price = $high_price->price + self::$settings['block_span'];
                    if($new_price < 0.000001){return false;}
                    $eth_size = self::$settings['block_size'];
                    $arb_size = self::$settings['block_size'] / $new_price;
                    if($this->DB2->query("INSERT INTO blocks (price, arb_size, eth_size, current_arb_size, flag) VALUES (?,?,?,?,?)", array($new_price, $this->getTruncatedValue($arb_size, 2), $eth_size, 7, 'sell_side'))){
                        $last_inserted = $this->DB2->insert_id();
                        if($last_inserted > 0){
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                                array(77836, 'Sell', 2, $new_price, $last_inserted, 500, 'exchange', 'sell_side'));
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                                array(82, 'Sell', 3, $new_price, $last_inserted, 500, 'exchange', 'sell_side'));
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                                array(783195, 'Sell', 1, $new_price, $last_inserted, 500, 'exchange', 'sell_side'));
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                                array(83632, 'Sell', 1, $new_price, $last_inserted, 500, 'exchange', 'sell_side'));
                                
                            
                        }
                    }
                    
                    //remove highest block
                    $lowest_block = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 order by price ASC LIMIT 1")->row();
                    if($lowest_block->current_arb_size > 0){
                            $this->cancel_orders($lowest_block->id);   
                    }
                    $this->DB2->query("DELETE FROM blocks WHERE id = ?", array($lowest_block->id));
                    
                    //move top sell to top buy (self::$settings['no_of_blockes'] / 2) - 1;
                    if($top_sell = $this->DB2->query("SELECT * FROM blocks WHERE flag = 'sell_side' ORDER BY price ASC LIMIT 1")->row()){
                        // update to buy side
                        $this->DB2->query("UPDATE blocks SET flag = ? WHERE id = ?", array('buy_side', $top_sell->id));
                    }
                }
                
                echo 'updated';
            }else if($market_eth >= 99.99){
                    $this->DB2->query("UPDATE block_settings SET value = ? WHERE name = 'market_block_eth'", array(0));
                    $this->DB2->query("UPDATE block_settings SET value = ? WHERE name = 'block_time_stamp'", array($current_time));
                    $high_price = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 ORDER BY price DESC")->row();
                    $new_price = $high_price->price + self::$settings['block_span'];
                    if($new_price < 0.000001){return false;}
                    $eth_size = self::$settings['block_size'];
                    $arb_size = self::$settings['block_size'] / $new_price;
                    if($this->DB2->query("INSERT INTO blocks (price, arb_size, eth_size, current_arb_size, flag) VALUES (?,?,?,?,?)", array($new_price, $this->getTruncatedValue($arb_size, 2), $eth_size, 7, 'sell_side'))){
                        $last_inserted = $this->DB2->insert_id();
                        if($last_inserted > 0){
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                                array(77836, 'Sell', 2, $new_price, $last_inserted, 500, 'exchange', 'sell_side'));
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                                array(82, 'Sell', 3, $new_price, $last_inserted, 500, 'exchange', 'sell_side'));
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                                array(783195, 'Sell', 1, $new_price, $last_inserted, 500, 'exchange', 'sell_side'));
                            $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, block_id, eth_usd, order_from, side) values (?,?,?,?,?,?,?,?)", 
                                array(83632, 'Sell', 1, $new_price, $last_inserted, 500, 'exchange', 'sell_side'));
                                
                                
                            
                        }
                    }
                    
                    //remove highest block
                    $lowest_block = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 order by price ASC LIMIT 1")->row();
                    if($lowest_block->current_arb_size > 0){
                            $this->cancel_orders($lowest_block->id);   
                    }
                    $this->DB2->query("DELETE FROM blocks WHERE id = ?", array($lowest_block->id));
                    
                    //move top sell to top buy (self::$settings['no_of_blockes'] / 2) - 1;
                    if($top_sell = $this->DB2->query("SELECT * FROM blocks WHERE flag = 'sell_side' ORDER BY price ASC LIMIT 1")->row()){
                        // update to buy side
                        $this->DB2->query("UPDATE blocks SET flag = ? WHERE id = ?", array('buy_side', $top_sell->id));
                    }
                
                echo $current_time .' -- '.$limit_time;
            }
        }
        
    }
    
    
    
    
    private static function cancel_orders($block_id){
        $get_orders = self::$ddb->query("SELECT * FROM orders_beta WHERE block_id = ? AND status = 0", array($block_id))->result();
        foreach($get_orders as $order){
            if($order->order_type == 'Sell'){
                self::$ddb->query("UPDATE orders_beta SET status = 1, remark = 'cancel' WHERE id = ? AND status = 0", array($order->id));
                if($order->order_from != 'exchange_earned'){
                    $price_usd_order = $order->price * $order->eth_usd; 
                    $remain_arb_usd = $order->amount * $price_usd_order;
                    
                    $order_amount_limit = self::$ddbb->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($order->user_id))->row()->order_amount_limit;
                    $total_order_ammount = $order_amount_limit - $remain_arb_usd;
                    
                    if($total_order_ammount < 0){
                        self::$ddbb->query("UPDATE exchange_wallet SET order_amount_limit = ? WHERE user_id = ?", array(0, $order->user_id));
                    }else{
                        self::$ddbb->query("UPDATE exchange_wallet SET order_amount_limit = ? WHERE user_id = ?", array($total_order_ammount, $order->user_id));
                    }
                }
            }else if($order->order_type == 'Buy'){
                self::$ddb->query("UPDATE orders_beta SET status = 1, remark = 'cancel' WHERE id = ? AND status = 0", array($order->id));
            }
        }
    }
    
    
    //truncate extra decimal values
    public static function getTruncatedValue ( $value, $precision )
    {
        //Casts provided value
        $value = ( string )$value;

        //Gets pattern matches
        preg_match( "/(-+)?\d+(\.\d{1,".$precision."})?/" , $value, $matches );

        //Returns the full pattern match
        return $matches[0];            
    }
    
    
    //checks here for exchange
    private function exchange_lock($u_id){
        $exchange_status = $this->db->query("SELECT * FROM admin_locks WHERE name = 'exchange_lock'")->row();
        $unlock_user = array(13870, 791138);
        if($exchange_status->lock_status == 1 && !in_array($u_id, $unlock_user)){
            echo json_encode(array('error'=>'1', 'msg' => 'Sorry for inconvenience this feature is temporarily locked.'));
			return false;
        }else{
            return true;
        }
    }
    private function get_eth_db_value(){
        $eth_value = $this->db->query("SELECT * FROM api_settings WHERE name = ?", array('eth_dollor_value'))->row()->value;
		if($eth_value < 70){
            echo json_encode(array('error'=>'1', 'msg' => 'Unexpected ETH value.'));
            return false;
        }else{
            return $eth_value;
        }
    }
    private function get_blockes_for_price($order_type, $price, $amount = null, $testing = null){
        if($order_type == 'Buy'){
            // $get_block_with_price = $this->DB2->query("SELECT * FROM blocks WHERE price = ? and status = 0", array($price))->row();
            // if($get_block_with_price->flag == 'sell_side'){
            //     // get top sell block where at least 0.1 eth space is availabel 
            //     $get_top_sell_block = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 AND flag = 'sell_side' order by price ASC LIMIT 1")->row();
            //     if($get_top_sell_block->current_eth_size >= ($get_top_sell_block->eth_size - 0.01)){
            //         echo json_encode(array('error'=>'1', 'msg' => 'Top sell block is full.'));
            //         return false;
            //     }else{
            //         return $get_top_sell_block->price;
            //     }
            // }else{  //AND flag = 'buy_side'  
                $buy_block = $this->DB2->query("SELECT * FROM blocks WHERE current_eth_size < eth_size AND status = 0 order by price ASC limit 400")->result();
                $block_prices = array();
                foreach($buy_block as $block){
                    $block_prices[] = $block->price;
                }
                
                if(!in_array($price, $block_prices)){
                    if(isset($block_prices[0])){
                        $price = $this->getTruncatedValue($block_prices[0], 6);
                        return $price;
                    }else{
                        echo json_encode(array('error'=>'1', 'msg' => 'No such block exist.'));
                        return false;
                    }
                }else{
                    return $price;
                }
            //}
        }else if($order_type == 'Sell'){
            $get_block_with_price = $this->DB2->query("SELECT * FROM blocks WHERE price = ? and status = 0", array($price))->row();
            if($get_block_with_price->current_arb_size >= ($get_block_with_price->arb_size - 0.001)){
                echo json_encode(array('error'=>'1', 'msg' => 'Block size is full, Please try in next block.'));
                        return false;
            }
            
            // if($get_block_with_price->flag == 'buy_side'){
            //     // get top buy block where at least 2 arb space is availabel 
            //     $get_top_buy_block = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 AND flag = 'buy_side' order by price DESC LIMIT 1")->row();
            //     $allow_size = $get_top_buy_block->current_eth_size / $get_top_buy_block->price;
            //     if($get_top_buy_block->current_arb_size >= $allow_size){
            //         echo json_encode(array('error'=>'1', 'msg' => 'No place is available in top buy block.'));
            //         return false;
            //     }else{
            //         return $get_top_buy_block->price;
            //     }
             //}else{  // AND flag = 'sell_side'
                $sell_block = $this->DB2->query("SELECT * FROM blocks WHERE current_arb_size < arb_size AND status = 0  order by price ASC limit 215 offset 185")->result();
                $block_prices = array();
                foreach($sell_block as $block){
                    $block_prices[] = $block->price;
                }
                
                if(!in_array($price, $block_prices)){
                    if(isset($block_prices[0])){
                        $price = $this->getTruncatedValue($block_prices[0], 6);
                        return $price;
                    }else{
                        echo json_encode(array('error'=>'1', 'msg' => 'No such block exist.'));
                        return false;
                    }
                }else{
                    return $price;
                }
            //}
            // $blockes = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 order by price ASC LIMIT 400")->result();
            // $block_prices = array();
            // $block_price_full = array();
            // foreach($blockes as $block){
            //     if($block->flag == 'buy_side' && $block->current_eth_size > 0 && $block->current_arb_size < $block->arb_size){
            //         $block_prices[] = $block->price;
            //     }else if($block->flag != 'buy_side' && $block->current_arb_size < $block->arb_size){
            //         $block_prices[] = $block->price;
            //         if($block->flag == 'sell_side'){
            //             $remain = ($block->arb_size / 2) - $block->current_arb_size;
            //             if($remain < 0){$remain = 0;}
            //             $block_price_full[] = array('price'=>$block->price, 'remain' => $remain);
            //         }
                    
            //     }
            // }
            
            // if($testing == 'from_best_sell'){
            //     foreach($block_price_full as $b_price){
            //         if($b_price['price'] > $price && $b_price['remain'] >= 10){
            //             return $b_price['price'];
            //         }
            //     }
            // }
            
            
            // if(!in_array($price, $block_prices)){
            //     if($amount != null){
            //         foreach($block_price_full as $b_price){
            //             if($b_price['price'] > $price && $b_price['remain'] >= 10){
            //                 return $b_price['price'];
            //             }
            //         }
            //     }
            //     echo json_encode(array('error'=>'1', 'msg' => 'No price matched.'));
            //     return false;
                
            // }else{
            //     return $price;
            // }
        }else{
            echo json_encode(array('error'=>'1', 'msg' => 'No such order type exist.'));
            return false;
        }
    }
    
    private function buy_sell_golden_block($u_id, $price, $order_type){
        return true;
        // $get_block_with_price = $this->DB2->query("SELECT * FROM blocks WHERE price = ? AND feature = ?", array($price, 'gold'))->row();
        // if($get_first_order_of_block = $this->DB2->query('SELECT * FROM orders_beta WHERE user_id= ? AND block_id = ? limit 1', array($u_id, $get_block_with_price->id))->row()){
        //     if($get_first_order_of_block->order_type != $order_type){
        //         echo json_encode(array('error'=>'1', 'msg' => 'Sell / Buy in same gold block is not allowed.'));
        //         return false;
        //     }else{
        //         return true;
        //     }
        // }else{
        //     return true;
        // }
    }
    
    private function totel_open_orders($u_id, $pkg, $type){
    //     if($last_order =  $this->DB2->query("SELECT * FROM orders_beta WHERE user_id = ? AND status = ?", array($u_id, 0))->row()){
    //         if($last_order->order_type != $type){
    //             echo json_encode(array('error'=>'1', 'msg' => 'Selling and Buying at same time not allowed.'));
    // 			return false;
    //         }
    //     }
        $limit = 1;
        if($pkg == 'Basic'){
            if($type == 'Sell'){$limit = 1;}else if($type == 'Buy'){ $limit = 3;}
        }else if($pkg == 'Pro' || $pkg == 'Advance'){
            if($type == 'Sell'){$limit = 2;}else if($type == 'Buy'){ $limit = 3;}
        }
            
        $total_orders = $this->DB2->query("SELECT count(*) as orders FROM orders_beta WHERE user_id = ? AND status = ?", array($u_id, 0))->row()->orders;
        if($total_orders >= $limit && !in_array($u_id, $this->allow_users)){
            echo json_encode(array('error'=>'1', 'msg' => 'Maximum '.$limit.' '.$type.' open orders placed.'));
			return false;
        }else{
            return true;
        }
    
    }
    
    private function min_amount($amount){
        if($amount < 0.1){
			echo json_encode(array('error'=>'1', 'msg' => 'Your minimum amount is 0.1.'));
            return false;
        }else{
            return true;
        }
    }
    private function max_amount($amount){
        if($amount > 500){
			echo json_encode(array('error'=>'1', 'msg' => 'Maximum Sell amount limit is 500 ARB.'));
            return false;
        }else{
            return true;
        }
    }
    
    private function check_seller_wallet_balance($u_id, $amount, $wallet){
        if($wallet == 'exchange'){$table_name = 'exchange_wallet';}
        else if($wallet == 'exchange_earned'){$table_name = 'exchange_earned_wallet';}
        else if($wallet == 'stop_abot'){$table_name = 'stop_abot_wallet';}
        else{echo json_encode(array('error'=>'1', 'msg' => 'No such wallet exist.')); return false;}
        
        $seller_exchange_wallet = $this->db->query("SELECT * FROM ".$table_name." WHERE user_id = ? ", array($u_id))->row();
        
        if($res = $this->DB2->query("SELECT sum(amount) as total_amount FROM orders_beta WHERE user_id = ? AND order_type = ? AND status = ? AND order_from = ?", array($u_id, 'Sell', 0, $wallet))->row()){
            $total_amount = $res->total_amount;
            if($seller_exchange_wallet->activeArb < $amount || $seller_exchange_wallet->activeArb < ($total_amount + $amount)){
                echo json_encode(array('error'=>'1', 'msg' => 'Your account balance is not enough.'));
                return false;
            }else{
                return true;
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg' => 'Something went wrong.'));
            return false;
        }
    }
    
    private function exchange_limits($u_id, $pkg_limit, $amount, $eth_value, $price){
        if(in_array($u_id , $this->allow_users)){
            $limit = 4000000;
        }
        else{
            $limit = $pkg_limit; //$pkg_data->exchange_sell_limit;
        }
        $order_amount_limit = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row()->order_amount_limit;
        $cr_arb_price_usd = $eth_value * $price;
        $total_order_ammount = $order_amount_limit + ($amount * $cr_arb_price_usd);
        if($total_order_ammount > $limit){
           echo json_encode(array('error'=>'1', 'msg' => 'Your per day order limit is exceeded.'));
           return false;
        }
        else {
            $total_order_ammount = $this->getTruncatedValue($total_order_ammount, 6);
            $this->db->query("UPDATE exchange_wallet SET order_amount_limit = ? WHERE user_id = ?", array($total_order_ammount, $u_id));
            return true;
        }
    }
    
    private function exchange_earned_limits($u_id, $pkg_limit, $amount, $eth_value, $price){
        //$pkg_limit = 500;
        if(!in_array($u_id, $this->allow_users)){
            $before1hour = date("Y-m-d H:i:s", strtotime('-6 hour'));
            $amm = 0;
            if($total_orders1 = $this->DB2->query("SELECT * FROM orders_beta WHERE order_type = ? AND remark != ? AND user_id = ? AND order_from LIKE ? AND create_time > ? ", array('Sell', 'cancel', $u_id, 'exchange_earned', $before1hour))->result()){
                foreach($total_orders1 as $ord){
                    $order_arb_price_usd = $ord->price * $ord->eth_usd;
                    $amm = $amm + ($ord->amount * $order_arb_price_usd);
                }
            }
            $amm = $this->getTruncatedValue($amm, 4);
            $this->db->query("UPDATE exchange_wallet SET er_order_amount_limit = ? WHERE user_id = ?", array($amm, $u_id));
            
            $limit = $pkg_limit;
            
            $order_amount_limit = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row()->er_order_amount_limit;
            $amount = doubleval($this->input->post("amount"));
            $cr_arb_price_usd = $eth_value * $price;
            $total_order_ammount = $order_amount_limit + ($amount * $cr_arb_price_usd);
            if($total_order_ammount > $limit){
                echo json_encode(array('error'=>'1', 'msg' => 'Your earned wallet per hour limit exceeded.'));
				return false;
            }
            else {
                $total_order_ammount = $this->getTruncatedValue($total_order_ammount, 4);
                $this->db->query("UPDATE exchange_wallet SET er_order_amount_limit = ? WHERE user_id = ?", array($total_order_ammount, $u_id));
                return true;
            }
        }else{return true;}
    }
    
    private function place_sell_order($u_id, $amount, $price, $wallet, $eth_value, $block_id, $current_arb_size, $flag){
        $block = $this->DB2->query("SELECT * FROM blocks WHERE id = ?", array($block_id))->row();
        $remain_in_block = $block->arb_size - $block->current_arb_size;
        if($remain_in_block >= $amount){
            //update block current arb size
            $update_block_arb_size = $block->current_arb_size + $amount;
            if($update_block_arb_size < 0.001){$update_block_arb_size = 0;}
                if($amount > 0.001){
                    if($this->DB2->query("UPDATE blocks SET current_arb_size = ? WHERE id = ? and current_arb_size = ?", array($update_block_arb_size, $block_id, $block->current_arb_size))){
                        $this->DB2->query("INSERT INTO orders_beta (user_id, order_type, amount, price, order_from, eth_usd, block_id, side) VALUES (?,?,?,?,?,?,?,?)",
                             array($u_id, 'Sell', $this->getTruncatedValue($amount, 3), $price, $wallet, $eth_value, $block_id, $flag));
                        $order_id = $this->DB2->insert_id();
                        return $order_id;
                    }else{
                        echo json_encode(array('error'=>'1', 'msg' => 'Block is full, Please place your order in next block.'));
                        return false;
                    }
                }
        }else{
            echo json_encode(array('error'=>'1', 'msg' => 'Block is full, Please place your order in next block.'));
            return false;
        }
    }
    
    
    private function get_block_sell_size($price){
        $get_block = $this->DB2->query("SELECT * FROM blocks WHERE price = ? AND status = 0", array($price))->row();
        if($get_block->flag == 'market'){
            return $get_block->arb_size;
        }
        // 50% check add here
        $top_12 = $this->DB2->query("SELECT * FROM blocks WHERE status = 0 ORDER BY price ASC LIMIT 201")->result();
        $after_12 = 1;
        foreach($top_12 as $top_block){
            if($top_block->price == $price){$after_12 = 0; break;}
        }
        // if($after_12 == 1){
        //     $per_allow = ($get_block->arb_size / 100) * 50;
        //     $allow_size = $get_block->arb_size - $per_allow;
        //     return $allow_size;
        // }else{
            return $get_block->arb_size;
        //}
        
    }
    
    private function check_captcha($responce = null){
        return true;
        $sec = '6LcmlIQUAAAAAGVBMOM81juC5BwmmNvImdXjsema';
	    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify?secret='.$sec.'&response='.$responce);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($res);
        if($res->success != 1){
            echo json_encode(array('error'=>'1', 'msg' => 'Invalid Captcha')); 
            return false;
        }else{
            return true;
        }
    }
}