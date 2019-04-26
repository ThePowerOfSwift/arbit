<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Exchange extends MY_Controller {


    //truncate extra decimal values
    public function getTruncatedValue ( $value, $precision )
    {
        $value = number_format($value, 20, '.', '');
        if($value < 0.00001){return 0;}
        //Casts provided value
        $value = ( string )$value;

        //Gets pattern matches
        preg_match( "/(-+)?\d+(\.\d{1,".$precision."})?/" , $value, $matches );

        //Returns the full pattern match
        return $matches[0];            
    }
    
    
    public function auto_order_cron(){
        $users = $this->db->query("SELECT * FROM exchange_wallet WHERE auto_buy_status = 1 AND activeEth >= 0.005")->result();
        if($users != null){
            foreach($users as $user){
                //echo $user->auto_buy_last.'<br>';
                $next_time = date("Y-m-d H:i:s", strtotime('+'.$user->auto_buy_min.' min', strtotime($user->auto_buy_last)));
                $now = date("Y-m-d H:i:s");
                //echo $next_time.'<br>';
                if($now >= $next_time){
                    $this->db->query("UPDATE exchange_wallet SET auto_buy_last = ? WHERE user_id = ?", array($now, $user->user_id));
                    if($user->auto_buy_limit_order_status == 1 && $user->auto_buy_limit_last_order == 'market'){
                        $this->db->query('UPDATE exchange_wallet SET auto_buy_limit_last_order = ? WHERE user_id = ?', array('market', $user->user_id));
                        $type2 = 'limit';
                    }else if($user->auto_buy_limit_order_status == 1 && $user->auto_buy_limit_last_order == 'limit'){
                        $this->db->query('UPDATE exchange_wallet SET auto_buy_limit_last_order = ? WHERE user_id = ?', array('market', $user->user_id));
                        $type2 = 'market';
                    }else if($user->auto_buy_limit_order_status == 0){
                        $type2 = 'market';
                    }
                    $this->auto_order($user->user_id, $user->auto_buy_amount, 'Buy');
                    sleep(0.5);
                }
            }
        }
    }
    
    
    public function auto_order_cron_sell(){
        //echo 'runinggg';
        //exit;
        $users = $this->db->query("SELECT * FROM exchange_wallet WHERE auto_sell_status = 1 AND activeArb >= 5")->result();
        if($users != null){
            foreach($users as $user){
                //echo $user->auto_buy_last.'<br>';
                $next_time = date("Y-m-d H:i:s", strtotime('+'.$user->auto_sell_min.' min', strtotime($user->auto_sell_last)));
                $now = date("Y-m-d H:i:s");
                //echo $next_time.'<br>';
                if($now >= $next_time){
                    $this->db->query("UPDATE exchange_wallet SET auto_sell_last = ? WHERE user_id = ?", array($now, $user->user_id));
                    if($user->user_id == 1001){
                        if($this->db->query("SELECT value FROM pro_plus_crons_setting WHERE name = 'orders_cron_status'")->row()->value == 1){
                            $this->auto_order($user->user_id, $user->auto_sell_amount, 'Sell');
                        }else{
                            continue;
                        }
                    }else{
                        $this->auto_order($user->user_id, $user->auto_sell_amount, 'Sell');
                    }
                    sleep(0.5);
                }
            }
        }
    }
    
    
    
    public function auto_order($u_id, $qty, $type){
        if($type == 'Buy'){
            $orders = $this->DB2->query("SELECT * FROM orders WHERE order_type = 'Sell' AND status = 0 order by price asc")->result();
            //$sell_cur = 'ETH'; $buy_cur = 'ARB';
        }else{
            $orders = $this->DB2->query("SELECT * FROM orders WHERE order_type = 'Buy' AND status = 0 order by price desc")->result();
            //$sell_cur = 'ARB'; $buy_cur = 'ETH';
        }
        $a_count = 0;
        $price = $orders[0]->price;
        foreach($orders as $order){
            $a_count = $a_count + $order->amount;
            if($a_count >= $qty){
                $price = $order->price;
                break;
            }
        }
        
        $params = array(
            'u_id' => $u_id,
            'wallet' => 'exchange',
            'order_type' => $type,
            'amount' => $qty,
            'price' => $price,
            'get_from' => 'sdfhihf@klhjglk*'
            );
        //print_r($params); exit;
            
        $postFields = http_build_query($params, '', '&');
        $ch = curl_init(base_url().'/saveOrder');
        curl_setopt($ch, CURLOPT_POST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        print_r($result);
        curl_close ($ch);
    }
    
    
    
    //--------------Exchange Functions--------------//
    public function saveOrder(){
        exit;
        $exchange_status = $this->db->query("SELECT * FROM admin_locks WHERE name = 'exchange_lock'")->row();
        if($exchange_status->lock_status == 1 && $this->session->userdata('u_id') != 13870){ //&& $this->session->userdata('u_id') != 13870
            echo json_encode(array('error'=>'1', 'msg' => 'Sorry for inconvenience this feature is temporarily locked.'));
			exit;
        }
        //cache ETH value for one min
        // $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        // if ( ! $eth_value = $this->cache->get('eth_value')){
        //     $eth_value_t = json_decode(file_get_contents('https://api.etherscan.io/api?module=stats&action=ethprice'));
        //     $eth_value = $eth_value_t->result->ethusd;
        //     $this->cache->save('eth_value', $eth_value, 120);
        // }
        
        // check ETH value
        $eth_value = $this->db->query("SELECT * FROM api_settings WHERE name = ?", array('eth_dollor_value'))->row()->value;
		if($eth_value < 70){
            echo json_encode(array('error'=>'1', 'msg' => 'Unexpected ETH value.'));
            exit;
        }
        
        $excepion_users = array(9, 43, 846, 19724, 59570, 61081, 80394,78359, 77836, 59570);
        $u_id = $this->session->userdata('u_id');
        
        $auto_buy = 0;
        if($this->input->post("get_from") == 'sdfhihf@klhjglk*'){
            $u_id = $this->input->post("u_id");
            $auto_buy = 1;
        }
        
        if($this->input->post("wallet") == 'exchange_earned'){
            $log_exchange = 'Earned Exchange';
        }else{
            $log_exchange = 'Exchange';
        }
        
        
        if($u_id !=''){
            $exch= $this->input->post("wallet");
            
            // is it undercut
            if($this->input->post("check_flag") == 1){
                $undercut_flag = 1;
            }else{
                $undercut_flag = 0;
            }
            
            
            //get user data 
            $user_data = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
            // get package detail 
            $pkg_data = $this->db->query("SELECT * FROM packages WHERE name = ?", array($user_data->package))->row();
            
            
            
            
            //amount limit 750/hour on exchange earned wallet ..
            if($this->input->post('order_type') == 'Sell' && $exch == 'exchange_earned'){
                
                $before1hour = date("Y-m-d H:i:s", strtotime('-6 hour'));
                $amm = 0;
                if($total_orders1 = $this->DB2->query("SELECT * FROM orders WHERE order_type = ? AND remark != ? AND user_id = ? AND order_category LIKE ? AND create_time > ? ", array('Sell', 'cancel', $u_id, 'exchange_earned%', $before1hour))->result()){
                    foreach($total_orders1 as $ord){
                        $order_arb_price_usd = $ord->price * $ord->eth_usd;
                        $amm = $amm + ($ord->amount * $order_arb_price_usd);
                    }
                }
                $amm = $this->getTruncatedValue($amm, 4);
                $this->db->query("UPDATE exchange_wallet SET er_order_amount_limit = ? WHERE user_id = ?", array($amm, $u_id));
                
                $limit = $pkg_data->exchange_sell_limit;
                
                $order_amount_limit = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row()->er_order_amount_limit;
                $amount = doubleval($this->input->post("amount"));
                $cr_arb_price_usd = $eth_value * $this->input->post("price");
                $total_order_ammount = $order_amount_limit + ($amount * $cr_arb_price_usd);
                if($total_order_ammount > $limit){
                    echo json_encode(array('error'=>'1', 'msg' => 'Your earned wallet per hour limit exceeded.'));
					exit;
                }
                else {
                    $total_order_ammount = $this->getTruncatedValue($total_order_ammount, 4);
                    $this->db->query("UPDATE exchange_wallet SET er_order_amount_limit = ? WHERE user_id = ?", array($total_order_ammount, $u_id));
                }
            }
            
            
            // buy order not more then 400 eth in one hour per user
             if($this->input->post("get_from") != 'sdfhihf@klhjglk*' && $this->input->post("order_type") == 'Buy' && $u_id != 43){
                $before_1hour_db = date("Y-m-d H:i:s", strtotime("-6 hour"));
                $orders_buy_check = $this->DB2->query("SELECT * FROM orders WHERE user_id = ? AND order_type = ? AND status = ? AND remark != ? AND auto_buy != ? AND created_at > ?", array($u_id, 'Buy', 1, 'cancel', 1, $before_1hour_db))->result();
                //print_r($orders_buy_check); exit;
                $total_sum = 0;
                foreach($orders_buy_check as $orderr){
                    $aa = $orderr->amount * $orderr->price;
                    $total_sum = $total_sum + $aa;
                }
                $aba = $this->input->post("amount") * $this->input->post("price");
                $total_sum = $total_sum + $aba;
                if($total_sum >= 400){
                    echo json_encode(array('error'=>'1', 'msg' => 'Your per hour Buy limit exceeded.'));
                    exit;
                }
                 
             }
            
            
             //exchange earned wallet
            if($this->input->post("wallet") == 'exchange_earned' && $this->input->post("order_type") == 'Sell'){
                $amount = doubleval($this->input->post("amount"));
                $amount = $this->getTruncatedValue($amount, 4);
                if($exEarned_wallet = $this->db->query("SELECT * FROM exchange_earned_wallet WHERE user_id = ?", array($u_id))->row()){
                    if($exEarned_wallet->activeArb >= $amount){
                        //update exchange wallet
                        $exchange_arb = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
                        $new_exchange_arb =  $exchange_arb + $amount;
                        $this->db->query("UPDATE exchange_wallet SET activeArb = ? WHERE user_id = ?", array($new_exchange_arb, $u_id));
                        
                        // update exchange earned
                        $new_exchange_earned_arb = $exEarned_wallet->activeArb - $amount;
                        $this->db->query("UPDATE exchange_earned_wallet SET activeArb = ? WHERE user_id = ?", array($new_exchange_earned_arb, $u_id));
                        
                        //create two logs
                        $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchangeEarnedWallet_ARB','-'.$amount, $exEarned_wallet->activeArb, "Transfer ARB from Earned Exchange Wallet to Exchange wallet"));
                        $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchangeWallet_ARB',$amount, $exchange_arb, "Credit ARB in Exchange Wallet from Earned Exchange Wallet"));
                        
                    }else{
						echo json_encode(array('error'=>'1', 'msg' => 'Not enough balance in exchange for trade.'));
                        exit;
                    }
                }else{
                 echo json_encode(array('error'=>'1', 'msg' => 'Not enough balance in earned wallet for trade.')); 
                 exit;
                }
            }
            
            
            $allow_ids = array(9, 43, 846, 59570, 61081, 77777);
            
            
            $total_orders = $this->DB2->query("SELECT count(*) as orders FROM orders WHERE user_id = ? AND status = ?", array($u_id, 0))->row()->orders;
            if($total_orders >= 3 && !in_array($u_id,$allow_ids) && $this->input->post("wallet") != 'exchange_earned'){
                echo json_encode(array('error'=>'1', 'msg' => 'Maximun 3 open orders placed.'));
				exit;
            }
            
            $sell_orders = $this->DB2->query("SELECT count(*) as orders FROM orders WHERE order_type = ? AND user_id = ? AND status = ? AND order_category = ?", array('Sell',$u_id, 0, 'exchangeLimit'))->row()->orders;
            if($sell_orders >= 2 && !in_array($u_id,$allow_ids) && $this->input->post("wallet") != 'exchange_earned' && $this->input->post("order_type") != "Buy"){
                echo json_encode(array('error'=>'1', 'msg' => 'Not more then 2 open sell orders are allowed.'));
                exit;
            }

            $order_type = $this->input->post("order_type");
            $sell_currency = $this->input->post("sell_currency");
            $buy_currency = $this->input->post("buy_currency");
            
            $amount = doubleval($this->input->post("amount"));
            $amount = $this->getTruncatedValue($amount, 4);
            
            $price = doubleval($this->input->post("price"));
            $price = $this->getTruncatedValue($price, 6);
            

            if($amount < 5 || $price < 0.0001){
				echo json_encode(array('error'=>'1', 'msg' => 'Your amount or price is below limit.'));
                exit;
            }


            $fee = $this->DB2->query("SELECT * FROM order_fee WHERE type='exchange' AND status = 1")->result();
            // fee discount acording to package
            if(isset($pkg_data->fee_discount)){
                $eth_fee = $fee[1]->fee - (($fee[1]->fee/100) * $pkg_data->fee_discount);
                $arb_fee = $fee[0]->fee - (($fee[0]->fee/100) * $pkg_data->fee_discount);
                // fees for market orders in %age
                $eth_market_fee = 13.5 - ((13.5/100) * $pkg_data->fee_discount);
                if($this->input->post("get_from") == 'sdfhihf@klhjglk*'){
                    $arb_market_fee = 1 - ((1/100) * $pkg_data->fee_discount);
                }else{
                    $arb_market_fee = 0.25 - ((0.25/100) * $pkg_data->fee_discount);
                }
            }else{ echo json_encode(array('error'=>'1', 'msg' => 'Your package not defined.')); exit;}

            
            if($order_type == 'Sell'){
                if($amount > 500){
                    echo json_encode(array('error'=>'1', 'msg' => 'Maximum Sell amount limit is 500 ARB.'));
                    exit;
                }
                $seller_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($u_id))->row();

                $total_amount = $this->DB2->query("SELECT sum(amount) as total_amount FROM orders WHERE user_id = ? AND order_type = ? AND status = ?", array($u_id, 'Sell', 0))->row()->total_amount;
                if($seller_exchange_wallet->activeArb < $amount || $seller_exchange_wallet->activeArb < ($total_amount + $amount)){
                    echo json_encode(array('error'=>'1', 'msg' => 'Your account balance is not enough.'));
                    exit;
                }
                
                //amount limit 500 on exchange wallet ..
                if($this->input->post('order_type') == 'Sell' && $exch != 'exchange_earned'){
                    if(in_array($u_id , $excepion_users)){
                        $limit = 40000;
                    }
                    // else if($u_id == 79522){ // orde_limit issue creater
                    //     $limit = 50;
                    // }
                    else{
                        $limit = $pkg_data->exchange_sell_limit;
                    }
                    $order_amount_limit = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row()->order_amount_limit;
                    $amount = doubleval($this->input->post("amount"));
                    $cr_arb_price_usd = $eth_value * $this->input->post("price");
                    $total_order_ammount = $order_amount_limit + ($amount * $cr_arb_price_usd);
                    if($total_order_ammount > $limit){
                       echo json_encode(array('error'=>'1', 'msg' => 'Your per day order limit is exceeded.'));
                       exit;
                    }
                    else {
                        $total_order_ammount = $this->getTruncatedValue($total_order_ammount, 6);
                        $this->db->query("UPDATE exchange_wallet SET order_amount_limit = ? WHERE user_id = ?", array($total_order_ammount, $u_id));
                    }
                }
                
                
                
                
                $all_buy_orders = $this->DB2->query("SELECT * FROM orders WHERE order_type = ? AND user_id != ? AND status = ? AND sell_currency_id = ? ORDER BY price DESC",array('Buy', $u_id, 0, 2))->result();
                foreach($all_buy_orders as $buy_order){
                    if($buy_order->price >= $price){

                        $seller_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($u_id))->row();
                        // check order
                        $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($buy_order->id))->row();
                        if($check_order->status == 1){
                            continue;
                        }
                        
                        if($buy_order->amount > $amount){
                            $buyer_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($buy_order->user_id))->row();
                            $expected_buyer_eth = $amount * $price;
                            if($buyer_exchange_wallet->activeEth >= $expected_buyer_eth){
                                // check buy order
                                $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($buy_order->id))->row();
                                if($check_order->status == 1){continue;}
                                
                                // update seller exchange wallet
                                $cal_eth_fee = ($expected_buyer_eth / 100)*$eth_market_fee;
                                $seller_new_eth = $seller_exchange_wallet->activeEth + ($expected_buyer_eth - $cal_eth_fee);
                                $seller_new_arb = $seller_exchange_wallet->activeArb - $amount;
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($seller_new_eth, $seller_new_arb, $u_id));

                                // update buyer exchange wallet
                                $cal_arb_fee = ($amount / 100)*$arb_fee;
                                $buyer_new_eth = $buyer_exchange_wallet->activeEth - $expected_buyer_eth;
                                $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount - $cal_arb_fee);
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $buy_order->user_id));

                                //update admin wallet
                                $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                                $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                                $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                                $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_new_arb, $admin_new_eth, 1));

                                // sell order completely buy
                                $this->DB2->query("INSERT INTO orders(user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id, order_id, status, order_category, eth_usd) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
                                    array($u_id, 'Sell', 1, 2, $amount, $price, $fee[1]->id, $buy_order->id, 1, $this->input->post("wallet").'Market', $eth_value));
                                $insert_id = $this->DB2->insert_id();


                                // break original buy order to an other and mark this as complete
                                $this->DB2->query("UPDATE orders SET amount = ?, price = ?, status = ?, order_id = ?, eth_usd = ? WHERE id = ?", array($amount, $price, 1, $insert_id, $eth_value, $buy_order->id));

                                //create new buy order of remaining amount
                                $new_order_amount = $buy_order->amount - $amount;
                                
                                if($new_order_amount >= 1){
                                    $new_order_amount = $this->getTruncatedValue($new_order_amount, 4);
                                    $this->DB2->query("INSERT INTO orders(user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id, eth_usd) VALUES (?,?,?,?,?,?,?,?)",
                                    array($buy_order->user_id, 'Buy', 2, 1, $new_order_amount, $buy_order->price, $buy_order->fee_id, $eth_value));
                                }
                                //logs of seller
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_SellARB','-'.$amount, $insert_id, $seller_exchange_wallet->activeArb, "Sell ARB in ".$log_exchange." (market)"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_BuyETH',$expected_buyer_eth, $insert_id, $seller_exchange_wallet->activeEth, "Buy ETH in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $insert_id, ($seller_exchange_wallet->activeEth + $expected_buyer_eth), "Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $insert_id, $get_admin_wallet->eth), "Fee");


                                //logs of buyer
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeWallet_SellETH','-'.$expected_buyer_eth, $buy_order->id,$buyer_exchange_wallet->activeEth, "Sell Eth in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeWallet_BuyARB',$amount, $buy_order->id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $buy_order->id, ($buyer_exchange_wallet->activeArb + $amount),"Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $buy_order->id, $get_admin_wallet->arb, "Fee"));

                                $amount = $amount - $buy_order->amount;
                                $amount = $this->getTruncatedValue($amount, 4);
                                break;

                            }
                        }
                        else if($buy_order->amount < $amount){
                            $buyer_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($buy_order->user_id))->row();
                            $expected_buyer_eth = $buy_order->amount * $price;
                            if($buyer_exchange_wallet->activeEth >= $expected_buyer_eth){
                                
                                // check buy order
                                $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($buy_order->id))->row();
                                if($check_order->status == 1){continue;}
                                
                                // update seller exchange wallet
                                $cal_eth_fee = ($expected_buyer_eth / 100)*$eth_market_fee;
                                $seller_new_eth = $seller_exchange_wallet->activeEth + ($expected_buyer_eth - $cal_eth_fee);
                                $seller_new_arb = $seller_exchange_wallet->activeArb - $buy_order->amount;
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($seller_new_eth, $seller_new_arb, $u_id));

                                // update buyer exchange wallet
                                $cal_arb_fee = ($buy_order->amount / 100)*$arb_fee;
                                $buyer_new_eth = $buyer_exchange_wallet->activeEth - $expected_buyer_eth;
                                $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($buy_order->amount - $cal_arb_fee);
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $buy_order->user_id));

                                //update admin wallet
                                $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                                $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                                $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                                $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_new_arb, $admin_new_eth, 1));

                                // sell order create which amount is buyed
                                $this->DB2->query("INSERT INTO orders(user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id, order_id, status, order_category, eth_usd) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
                                    array($u_id, 'Sell', 1, 2, $buy_order->amount, $price, $fee[1]->id, $buy_order->id, 1, $this->input->post("wallet").'Market', $eth_value));
                                $insert_id = $this->DB2->insert_id();


                                // buy order completed
                                $this->DB2->query("UPDATE orders SET status = ?, price = ? , order_id = ?, eth_usd = ? WHERE id = ?", array(1, $price, $insert_id, $eth_value, $buy_order->id));



                                //logs of seller
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_SellARB','-'.$buy_order->amount, $insert_id, $seller_exchange_wallet->activeArb, "Sell ARB in ".$log_exchange." (market)"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_BuyETH',$expected_buyer_eth, $insert_id, $seller_exchange_wallet->activeEth, "Buy ETH in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $insert_id, ($seller_exchange_wallet->activeEth + $expected_buyer_eth), "Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $insert_id, $get_admin_wallet->eth, "Fee"));


                                //logs of buyer
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeWallet_SellETH','-'.$expected_buyer_eth, $buy_order->id,$buyer_exchange_wallet->activeEth, "Sell ETH in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeWallet_BuyARB',$buy_order->amount, $buy_order->id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $buy_order->id, ($buyer_exchange_wallet->activeArb + $buy_order->amount), "Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $buy_order->id, $get_admin_wallet->arb, "Fee"));

                                $amount = $amount - $buy_order->amount;
                                $amount = $this->getTruncatedValue($amount, 4);
                            }

                        }
                        else if($buy_order->amount == $amount){
                            $buyer_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($buy_order->user_id))->row();
                            $expected_buyer_eth = $amount * $price;
                            if($buyer_exchange_wallet->activeEth >= $expected_buyer_eth){
                                
                                // check buy order
                                $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($buy_order->id))->row();
                                if($check_order->status == 1){continue;}
                                
                                // update seller exchange wallet
                                $cal_eth_fee = ($expected_buyer_eth / 100)*$eth_market_fee;
                                $seller_new_eth = $seller_exchange_wallet->activeEth + ($expected_buyer_eth - $cal_eth_fee);
                                $seller_new_arb = $seller_exchange_wallet->activeArb - $amount;
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($seller_new_eth, $seller_new_arb, $u_id));

                                // update buyer exchange wallet
                                $cal_arb_fee = ($amount / 100)*$arb_fee;
                                $buyer_new_eth = $buyer_exchange_wallet->activeEth - $expected_buyer_eth;
                                $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount - $cal_arb_fee);
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $buy_order->user_id));

                                //update admin wallet
                                $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                                $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                                $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                                $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_new_arb, $admin_new_eth, 1));

                                // sell order completely buy
                                $this->DB2->query("INSERT INTO orders(user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id, order_id, status, order_category, eth_usd) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
                                    array($u_id, 'Sell', 1, 2, $amount, $price, $fee[1]->id, $buy_order->id, 1, $this->input->post("wallet").'Market', $eth_value));
                                $insert_id = $this->DB2->insert_id();


                                // break original buy order to an other and mark this as complete
                                $this->DB2->query("UPDATE orders SET status = ?, order_id = ?, eth_usd = ? WHERE id = ?", array(1, $insert_id, $eth_value, $buy_order->id));


                                //logs of seller
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_SellARB','-'.$amount, $insert_id, $seller_exchange_wallet->activeArb, "Sell ARB in ".$log_exchange." (market)"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_BuyETH',$expected_buyer_eth, $insert_id, $seller_exchange_wallet->activeEth, "Buy ETH in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $insert_id, ($seller_exchange_wallet->activeEth + $expected_buyer_eth), "Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $insert_id, $get_admin_wallet->eth, "Fee"));


                                //logs of buyer
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeWallet_SellETH','-'.$expected_buyer_eth, $buy_order->id,$buyer_exchange_wallet->activeEth, "Sell ETH in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeWallet_BuyARB',$amount, $buy_order->id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $buy_order->id, ($buyer_exchange_wallet->activeArb + $amount),"Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $buy_order->id, $get_admin_wallet->arb, "Fee"));

                                $amount = $amount - $buy_order->amount;
                                 $amount = $this->getTruncatedValue($amount, 4);
                                break;
                            }
                        }
                    }

                    $seller_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($u_id))->row();

                }

                if($amount >= 5){
                    $amount = $this->getTruncatedValue($amount, 4);
                    $sellPrice = $this->DB2->query("SELECT * FROM orders WHERE order_type = 'Sell' AND status = 0 AND order_category = 'exchangeLimit' ORDER BY price ASC")->row()->price;
                    $pricec = $this->getTruncatedValue($sellPrice, 6);
                    
                    $one_per = ($pricec / 100) * 0.05;
                    $chkprice = $pricec - $one_per;
                    
                    
                  //  if($chkprice > $price) { // && $this->input->post("wallet") != 'exchange_earned'){
                        //echo  'false'; exit;
                    //    $newwwPrice = $chkprice;
                    //}else{
                        $newwwPrice = $price;
                    //}
                    $newwwPrice = $this->getTruncatedValue($newwwPrice, 6);
                    $this->DB2->query("INSERT INTO orders (user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id, order_category, eth_usd, undercut_flag) VALUES (?,?,?,?,?,?,?,?,?,?)",
                    array($u_id, 'Sell', 1, 2, $amount, $newwwPrice, $fee[1]->id, $this->input->post("wallet") . 'Limit', $eth_value, $undercut_flag));
                        
                        /*
                        $amount = $this->getTruncatedValue($amount, 4);
                    $this->DB2->query("INSERT INTO orders (user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id) VALUES (?,?,?,?,?,?,?)",
                        array($u_id, 'Sell', 1, 2, $amount, $price, $fee[1]->id));
                        */

                }


            }


            else if($order_type == 'Buy'){

                $old_order_values = 0;
                $open_buy_order = $this->DB2->query("SELECT * FROM orders WHERE user_id = ? AND order_type = ? AND status = ?", array($u_id, 'Buy', 0))->result();
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
                $all_sell_orders = $this->DB2->query("SELECT * FROM orders WHERE order_type = ? AND user_id != ? AND status = ? AND sell_currency_id = ? ORDER BY price ASC",array('Sell', $u_id, 0, 1))->result();
                foreach($all_sell_orders as $sell_order){
                    if($sell_order->price <= $price){
                        
                        if($sell_order->order_category == 'exchange_earnedMarket' || $sell_order->order_category == 'exchange_earnedLimit'){
                            $log_exchange_buy = 'Earned Exchange';
                        }else{
                            $log_exchange_buy = 'Exchange';
                        }
                        
                        $buyer_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($u_id))->row();
                        //check order
                        $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($sell_order->id))->row();
                        if($check_order->status == 1){
                            continue;
                        }
                        if($sell_order->amount > $amount){
                            $seller_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($sell_order->user_id))->row();
                            $expected_buyer_eth = $amount * $sell_order->price;
                            if($seller_exchange_wallet->activeArb >= $amount){
                                
                                //check order
                                $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($sell_order->id))->row();
                                if($check_order->status == 1){continue;}
                                
                                // update seller exchange wallet
                                //$cal_eth_fee = ($expected_buyer_eth / 100)*$eth_fee;
                                
                                if($sell_order->undercut_flag == 1){
                                    $cal_eth_fee = ($expected_buyer_eth / 100) * ($eth_fee + 10);
                                }else{
                                    $cal_eth_fee = ($expected_buyer_eth / 100) * $eth_fee;
                                }
                                
                                $seller_new_eth = $seller_exchange_wallet->activeEth + ($expected_buyer_eth - $cal_eth_fee);
                                $seller_new_arb = $seller_exchange_wallet->activeArb - $amount;
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($seller_new_eth, $seller_new_arb, $sell_order->user_id));

                                // update buyer exchange wallet
                                $cal_arb_fee = ($amount / 100)*$arb_market_fee;
                                $buyer_new_eth = $buyer_exchange_wallet->activeEth - $expected_buyer_eth;
                                //$buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount - $cal_arb_fee);\
                                
                                // 3% buy market bonus
                                $bonus3per = ($amount / 100) * 3;
                                $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount + $bonus3per);
                                
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $u_id));

                                //update admin wallet
                                $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                                $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                                $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                                $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_new_arb, $admin_new_eth, 1));

                                // Buy order completely Sell
                                $this->DB2->query("INSERT INTO orders(user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id, order_id, status, order_category, auto_buy, eth_usd) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
                                array($u_id, 'Buy', 2, 1, $amount, $sell_order->price, $fee[0]->id, $sell_order->id, 1, $this->input->post("wallet").'Market', $auto_buy, $eth_value));
                                $insert_id = $this->DB2->insert_id();


                                // break original Sell order to an other and mark this as complete
                                $this->DB2->query("UPDATE orders SET amount = ?, status = ?, order_id = ?, eth_usd = ? WHERE id = ?", array($amount, 1, $insert_id, $eth_value, $sell_order->id));

                                //create new Sell order of remaining amount
                                $new_order_amount = $sell_order->amount - $amount;
                                if($new_order_amount >= 1){
                                    $new_order_amount = $this->getTruncatedValue($new_order_amount, 4);
                                    $this->DB2->query("INSERT INTO orders(user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id, eth_usd) VALUES (?,?,?,?,?,?,?,?)",
                                    array($sell_order->user_id, 'Sell', 1, 2, $new_order_amount, $sell_order->price, $sell_order->fee_id, $eth_value));
                                }
                                //logs of seller
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeWallet_SellARB','-'.$amount, $sell_order->id, $seller_exchange_wallet->activeArb, "Sell ARB in ".$log_exchange_buy));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeWallet_BuyETH',$expected_buyer_eth, $sell_order->id, $seller_exchange_wallet->activeEth, "Buy ETH in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $sell_order->id, ($seller_exchange_wallet->activeEth + $expected_buyer_eth), "Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $sell_order->id, $get_admin_wallet->eth, "Fee"));


                                //logs of buyer
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_SellETH','-'.$expected_buyer_eth, $insert_id,$buyer_exchange_wallet->activeEth,"Sell ETH in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_BuyARB',$amount, $insert_id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange (market)"));
                                //$this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $insert_id, ($buyer_exchange_wallet->activeArb + $amount), "Fee"));
                                //$this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $insert_id, $get_admin_wallet->arb, "Fee"));
                                
                                // 3% bonus log  
                                $bonus_last_blnc = ($buyer_exchange_wallet->activeArb + $amount);
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_ARB_bonus',$bonus3per, $insert_id, $bonus_last_blnc, "3% bonus of Market buy"));
                                
                                //update bonus table 
                                $get_bonus = $this->db->query("SELECT * FROM bonus WHERE id = 1")->row();
                                $update_arb_bonus = $get_bonus->arb + $bonus3per;
                                $this->db->query("UPDATE bonus SET arb = ? WHERE id = 1", array($update_arb_bonus));

                                $amount = $amount - $sell_order->amount;
                                $amount = $this->getTruncatedValue($amount, 4);
                                break;

                            }
                        }
                        else if($sell_order->amount < $amount){
                            $seller_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($sell_order->user_id))->row();
                            $expected_buyer_eth = $sell_order->amount * $sell_order->price;
                            if($seller_exchange_wallet->activeArb >= $sell_order->amount){
                                
                                //check order
                                $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($sell_order->id))->row();
                                if($check_order->status == 1){continue;}
                                
                                
                                // update seller exchange wallet
                                //$cal_eth_fee = ($expected_buyer_eth / 100)*$eth_fee;
                                if($sell_order->undercut_flag == 1){
                                    $cal_eth_fee = ($expected_buyer_eth / 100) * ($eth_fee + 10);
                                }else{
                                    $cal_eth_fee = ($expected_buyer_eth / 100) * $eth_fee;
                                }
                                
                                $seller_new_eth = $seller_exchange_wallet->activeEth + ($expected_buyer_eth - $cal_eth_fee);
                                $seller_new_arb = $seller_exchange_wallet->activeArb - $sell_order->amount;
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($seller_new_eth, $seller_new_arb, $sell_order->user_id));

                                // update buyer exchange wallet
                                $cal_arb_fee = ($sell_order->amount / 100)*$arb_market_fee;
                                $buyer_new_eth = $buyer_exchange_wallet->activeEth - $expected_buyer_eth;
                                //$buyer_new_arb = $buyer_exchange_wallet->activeArb + ($sell_order->amount - $cal_arb_fee);
                                
                                // 3% buy market bonus
                                $bonus3per = ($sell_order->amount / 100) * 3;
                                $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($sell_order->amount + $bonus3per);
                                
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $u_id));

                                //update admin wallet
                                $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                                $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                                $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                                $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_new_arb, $admin_new_eth, 1));

                                // buy order create which amount is selled
                                $this->DB2->query("INSERT INTO orders(user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id, order_id, status, order_category, auto_buy, eth_usd) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
                                array($u_id, 'Buy', 2, 1, $sell_order->amount, $sell_order->price, $fee[0]->id, $sell_order->id, 1, $this->input->post("wallet").'Market', $auto_buy, $eth_value));
                                $insert_id = $this->DB2->insert_id();


                                // Sell order completed
                                $this->DB2->query("UPDATE orders SET status = ?, order_id = ?, eth_usd = ? WHERE id = ?", array(1, $insert_id, $eth_value, $sell_order->id));



                                //logs of seller
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeWallet_SellARB','-'.$sell_order->amount, $sell_order->id, $seller_exchange_wallet->activeArb, "Sell ARB in ".$log_exchange_buy));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeWallet_BuyETH',$expected_buyer_eth, $sell_order->id, $seller_exchange_wallet->activeEth, "Buy Eth in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $sell_order->id, ($seller_exchange_wallet->activeEth + $expected_buyer_eth), "Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $sell_order->id, $get_admin_wallet->eth, "Fee"));


                                //logs of buyer
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_SellETH','-'.$expected_buyer_eth, $insert_id,$buyer_exchange_wallet->activeEth, "Sell ETH in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_BuyARB',$sell_order->amount, $insert_id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange (market)"));
                                //$this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $insert_id, ($buyer_exchange_wallet->activeArb + $sell_order->amount), "Fee"));
                                //$this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $insert_id, $get_admin_wallet->arb, "Fee"));

                                // 3% bonus log  
                                $bonus_last_blnc = ($buyer_exchange_wallet->activeArb + $sell_order->amount);
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_ARB_bonus',$bonus3per, $insert_id, $bonus_last_blnc, "3% bonus of Market buy"));
                                
                                //update bonus table 
                                $get_bonus = $this->db->query("SELECT * FROM bonus WHERE id = 1")->row();
                                $update_arb_bonus = $get_bonus->arb + $bonus3per;
                                $this->db->query("UPDATE bonus SET arb = ? WHERE id = 1", array($update_arb_bonus));
                                
                                $amount = $amount - $sell_order->amount;
                                $amount = $this->getTruncatedValue($amount, 4);

                            }

                        }
                        else if($sell_order->amount == $amount){
                            $seller_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($sell_order->user_id))->row();
                            $expected_buyer_eth = $amount * $sell_order->price;
                            if($seller_exchange_wallet->activeArb >= $amount){
                                
                                //check order
                                $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($sell_order->id))->row();
                                if($check_order->status == 1){continue;}
                                
                                
                                // update seller exchange wallet
                                //$cal_eth_fee = ($expected_buyer_eth / 100)*$eth_fee;
                                if($sell_order->undercut_flag == 1){
                                    $cal_eth_fee = ($expected_buyer_eth / 100) * ($eth_fee + 10);
                                }else{
                                    $cal_eth_fee = ($expected_buyer_eth / 100) * $eth_fee;
                                }
                                
                                $seller_new_eth = $seller_exchange_wallet->activeEth + ($expected_buyer_eth - $cal_eth_fee);
                                $seller_new_arb = $seller_exchange_wallet->activeArb - $amount;
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($seller_new_eth, $seller_new_arb, $sell_order->user_id));

                                // update buyer exchange wallet
                                $cal_arb_fee = ($amount / 100)*$arb_market_fee;
                                $buyer_new_eth = $buyer_exchange_wallet->activeEth - $expected_buyer_eth;
                                //$buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount - $cal_arb_fee);
                                
                                // 3% buy market bonus
                                $bonus3per = ($amount / 100) * 3;
                                $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount + $bonus3per);
                                
                                $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $u_id));

                                //update admin wallet
                                $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                                $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                                $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                                $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_new_arb, $admin_new_eth, 1));

                                // buy order completely buy
                                $this->DB2->query("INSERT INTO orders(user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id, order_id, status, order_category, auto_buy, eth_usd) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
                                array($u_id, 'Buy', 2, 1, $amount, $sell_order->price, $fee[0]->id, $sell_order->id, 1, $this->input->post("wallet").'Market', $auto_buy, $eth_value));
                                $insert_id = $this->DB2->insert_id();


                                // break original buy order to an other and mark this as complete
                                $this->DB2->query("UPDATE orders SET status = ?, order_id = ?, eth_usd = ? WHERE id = ?", array(1, $insert_id, $eth_value, $sell_order->id));


                                //logs of seller
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeWallet_SellARB','-'.$amount, $sell_order->id, $seller_exchange_wallet->activeArb, "Sell ARB in ".$log_exchange_buy));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeWallet_BuyETH',$expected_buyer_eth, $sell_order->id, $seller_exchange_wallet->activeEth, "Buy ETH in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $sell_order->id, ($seller_exchange_wallet->activeEth + $expected_buyer_eth), "Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $sell_order->id, $get_admin_wallet->eth, "Fee"));


                                //logs of buyer
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_SellETH','-'.$expected_buyer_eth, $insert_id,$buyer_exchange_wallet->activeEth, "Sell ETH in Exchange"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_BuyARB',$amount, $insert_id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange (market)"));
                                //$this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $insert_id, ($buyer_exchange_wallet->activeArb + $amount), "Fee"));
                                //$this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'adminWallet_feeAdd_ARB',$cal_arb_fee,$insert_id, $get_admin_wallet->arb, "Fee"));

                                // 3% bonus log  
                                $bonus_last_blnc = ($buyer_exchange_wallet->activeArb + $amount);
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_ARB_bonus',$bonus3per, $insert_id, $bonus_last_blnc, "3% bonus of Market buy"));
                                
                                //update bonus table 
                                $get_bonus = $this->db->query("SELECT * FROM bonus WHERE id = 1")->row();
                                $update_arb_bonus = $get_bonus->arb + $bonus3per;
                                $this->db->query("UPDATE bonus SET arb = ? WHERE id = 1", array($update_arb_bonus));
                                
                                $amount = $amount - $sell_order->amount;
                                $amount = $this->getTruncatedValue($amount, 4);
                                break;
                            }
                        }
                    }

                    $seller_exchange_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ? ", array($u_id))->row();

                }

                if($amount >= 5){
                    $amount = $this->getTruncatedValue($amount, 4);
                    $this->DB2->query("INSERT INTO orders (user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id, eth_usd, auto_buy) VALUES (?,?,?,?,?,?,?,?,?)",
                        array($u_id, 'Buy', 2, 1, $amount, $price, $fee[0]->id, $eth_value, $auto_buy));

                }


            }



        }else{
            return false;
        }
    }
    
    
    public function orderBook(){
      $u_id = $this->session->userdata('u_id');
      // change here
      if($u_id != ''){
        
        $sellArray = array();
        $buyArray = array();
        $openOrders = array();
        $closeOrders = array();
        $marketHistory = array();
        
        // for all sell open orders
        $sellArray = $this->DB2->query("SELECT order_type, TRUNCATE(SUM(amount), 4) as amount, price, order_id, status, remark, order_category, order_from, auto_buy, created_at, create_time FROM orders WHERE order_type = 'Sell' AND status = 0 GROUP BY price ORDER BY created_at ASC")->result();
        
        // for all buy open orders
        $buyArray = $this->DB2->query("SELECT order_type, TRUNCATE(SUM(amount), 4) as amount, price, order_id, status, remark, order_category, order_from, auto_buy, created_at, create_time FROM orders WHERE order_type = 'Buy' AND status = 0 GROUP BY price")->result();
        
        // current user open orders
        $openOrders =  $this->DB2->query("SELECT * FROM orders WHERE user_id = $u_id AND status = 0")->result();
        $openOrders = array_reverse($openOrders);
        
        // current user close orders
        $closeOrders =  $this->DB2->query("SELECT * FROM orders WHERE user_id = $u_id AND status = 1 ORDER BY created_at DESC LIMIT 40")->result();
        
        //market history
        $marketHistory = $this->DB2->query("SELECT order_type, amount, price, order_id, status, remark, order_category, auto_buy, order_from, created_at, create_time FROM orders WHERE order_type = 'Buy' AND status = 1 AND remark = '' ORDER BY created_at DESC LIMIT 20")->result();
        
        $result = [
            "sellArray" => $sellArray,
            "buyArray" => $buyArray,
            "openOrders" => $openOrders,
            "closeOrders" => $closeOrders,
            "marketHistory" => $marketHistory
        ];
        $result = json_encode($result);
        print_r($result);
        
        
      }
    }
    
    public function all_user_orders(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            // current user close orders
            $closeOrders =  $this->DB2->query("SELECT * FROM orders WHERE user_id = $u_id AND status = 1")->result();
            $closeOrders = array_reverse($closeOrders);
            $result = json_encode($closeOrders);
            print_r($result);
        }else{
            echo 'false';
        }
    }
    
    

  

   
    public function close_order_by_id(){
        $exchange_status = $this->db->query("SELECT * FROM admin_locks WHERE name = 'exchange_lock'")->row();
        $allow_idss = array(13870, 791138);
        if($exchange_status->lock_status == 1 && !in_array($this->session->userdata('u_id'), $allow_idss)){
            echo json_encode(array('error'=>'1', 'msg' => 'Sorry for inconvenience this feature is temporarily locked.'));
            exit;
        }

        $u_id = $this->session->userdata('u_id');
        $order_id = $this->input->post('order_id');
        //get user data
        $user_data = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
        // get package detail
        $pkg_data = $this->db->query("SELECT * FROM packages WHERE name = ?", array($user_data->package))->row();
        
        if($order_id != ''){
            //$this->DB2->query("UPDATE orders SET status = ?, remark = ? WHERE id=? AND user_id = ? AND status = ?", array(1,'cancel', $order_id, $u_id, 0));
            $order_cat = $this->DB2->query("SELECT * FROM orders WHERE id=? AND user_id = ? ", array($order_id, $u_id))->row();
            
            $allow_ids = array(77836, 846, 9,  43, 59570);
           // $order_cat = $this->DB2->query("SELECT * FROM orders WHERE id=? AND user_id = ? ", array($order_id, $u_id))->row();
            if($order_cat->order_type == "Sell" && !in_array($u_id, $allow_ids)){
                $before_24hour = date("Y-m-d H:i:s", strtotime('-28 hour'));
                $co = $this->DB2->query("SELECT count(*) as co FROM orders WHERE user_id = ? AND order_type = ? AND status = ? AND remark = ? AND created_at > ?", array($u_id, 'Sell', 1, 'cancel', $before_24hour))->row()->co;
                if($co >= $pkg_data->allow_orders){
                    echo json_encode(array('error'=>'1', 'msg' => "You are not able to cancel more then ".$pkg_data->allow_orders." orders in 24 hours."));
					exit;
                }
                
                if($order_cat->undercut_flag == 1){
                    $ex_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row();
                    $eth_worth = $order_cat->amount * $order_cat->price;
                    $eth_fee_deducted = ($eth_worth/100) * 10;
                    if($ex_wallet->activeEth >= $eth_fee_deducted){
                        $fee_deducted = $eth_fee_deducted;
                        if($fee_deducted >= 0.0001){
                            $update_eth_ex = $ex_wallet->activeEth - $fee_deducted;
                            if($update_eth_ex < 0.001){$update_eth_ex = 0;}
                            // update wallet
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ? WHERE user_id = ?", array($update_eth_ex, $u_id));
                            // update admin_wallet
                            $ad_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = ?", array(1))->row();
                            $update_eth_ad = $ad_wallet->eth + $fee_deducted;
                            $this->db->query("UPDATE admin_wallet SET eth = ? WHERE id = ?", array($update_eth_ad, 1));
                            //insert log
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchangeWallet_cancelorder_ETH','-'.$fee_deducted, $ex_wallet->activeEth, "Fee"));
                        }
                    }else{
                        $fee_deducted = ($order_cat->amount/100) * 5;
                        
                        if($order_cat->order_from == 'exchnage_earned'){
                            $table_ded = 'exchnage_earned_wallet' ;
                        }else if($order_cat->order_from = 'stop_abot'){
                            $table_ded = 'stop_abot_wallet' ;
                        }else{
                            $table_ded = 'exchange_wallet' ;
                        }
                        $ex_wallet = $this->db->query("SELECT * FROM ".$table_ded." WHERE user_id = ?", array($u_id))->row();
                        
                        if($fee_deducted >= 0.001){
                            $update_arb_ex = $ex_wallet->activeArb - $fee_deducted;
                            // update wallet
                            if($update_arb_ex < 0.001){$update_arb_ex = 0;}
                            $this->db->query("UPDATE ".$table_ded." SET activeArb = ? WHERE user_id = ?", array($update_arb_ex, $u_id));
                            // update admin_wallet
                            $ad_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = ?", array(1))->row();
                            $update_arb_ad = $ad_wallet->arb + $fee_deducted;
                            $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = ?", array($update_arb_ad, 1));
                            //insert log
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, $table_ded.'_cancelorder_ARB','-'.$fee_deducted, $ex_wallet->activeArb, "Fee"));
                        }
                    }
                        
                }
                
            }
            $this->DB2->query("UPDATE orders SET status = ?, remark = ? WHERE id=? AND user_id = ? AND status = ?", array(1,'cancel', $order_id, $u_id, 0));
            //exchange_earned
            if($order_cat->order_from != "" && $order_cat->order_type == "Sell"){
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
            
            // if($order_cat->order_from == "exchange_earned" && $order_cat->order_type == "Sell"){
            //     // if order is from exchange wallet .. 
            //     $before1hour = date("Y-m-d H:i:s", strtotime('-6 hour'));
            //     if($order_cat->create_time > $before1hour){
            //         $order_amount_limit = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row()->er_order_amount_limit;
                    
            //         $order_arb_price_usd = $order_cat->price * $order_cat->eth_usd;
            //         $total_order_ammount = $order_amount_limit - ($order_cat->amount * $order_arb_price_usd);
                    
            //         if($total_order_ammount < 0){
            //             $this->db->query("UPDATE exchange_wallet SET er_order_amount_limit = ? WHERE user_id = ?", array(0, $u_id));
            //         }
            //         else{
            //             $this->db->query("UPDATE exchange_wallet SET er_order_amount_limit = ? WHERE user_id = ?", array($total_order_ammount, $u_id));
            //         }
            //     }
            // }
            
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
        $open_orders_amount = $this->DB2->query("SELECT sum(amount) as total_amount FROM orders WHERE user_id = ? AND order_type = ? AND status = ? AND order_from = 'exchange'", array($u_id, 'Sell', 0))->row()->total_amount;
        if(!empty($open_orders_amount) && $open_orders_amount > 0){
            $arb_value = $result->activeArb - $open_orders_amount;
        }else{
            $arb_value = $result->activeArb;
        }

        // get eth value
        $old_order_values = 0;
        $open_buy_order = $this->DB2->query("SELECT * FROM orders WHERE user_id = ? AND order_type = ? AND status = ?", array($u_id, 'Buy', 0))->result();
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
            $open_orders_amount_er = $this->DB2->query("SELECT sum(amount) as total_amount FROM orders WHERE user_id = ? AND order_type = ? AND status = ? AND order_from = 'exchange_earned'", array($u_id, 'Sell', 0))->row()->total_amount;
            if(!empty($open_orders_amount_er) && $open_orders_amount_er > 0){
                $active_arb_earned = $exEarned_wallet->activeArb - $open_orders_amount_er;
            }else{
                $active_arb_earned = $exEarned_wallet->activeArb;
            }
        }
        
        $active_arb_stop = 0;
        if($stop_wallet = $this->db->query("SELECT * FROM stop_abot_wallet WHERE user_id = ?", array($u_id))->row()){
            $open_orders_amount_stop = $this->DB2->query("SELECT sum(amount) as total_amount FROM orders WHERE user_id = ? AND order_type = ? AND status = ? AND order_from = 'stop_abot'", array($u_id, 'Sell', 0))->row()->total_amount;
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
    
   

    public function arb_price_stats(){
        //created_at > date_add(now(), INTERVAL -7 DAY)
        
        $buy_orders = $this->DB2->query("SELECT price*eth_usd as price FROM orders WHERE order_type = ? AND status = ? AND remark = '' order by created_at desc limit 500 ", array('Buy', 1))->result();
        $arr = array();
        foreach($buy_orders as $order){
            $arr[] = $this->getTruncatedValue($order->price, 4);
        }
        $arr = json_encode($arr);
        print_r($arr);
    }
    
    
    //cron to close sell orders more then 48 hours
    public function closeOrderAuto(){
        //exit;
        echo 'running';
        $now = date("Y-m-d H:i:s");
        $orders = $this->DB2->query("SELECT * FROM orders WHERE order_type = 'Sell' AND status = 0")->result();
        //echo count($orders).'<br>';
        foreach($orders as $order){
            $bypass_users = array(77836, 78355, 77827, 78451, 13516, 790540, 790542, 787088, 82);
            if(in_array($order->user_id, $bypass_users)){
                continue;
            }
            $after72hour = date("Y-m-d H:i:s", strtotime('+29 hour', strtotime($order->created_at)));
            if($now > $after72hour){
                $this->DB2->query("UPDATE orders SET status = 1, remark = 'cancel' WHERE id = ".$order->id." AND status = 0");
                echo $order->id.' -- Canceled <br>';
            }
        }
        
    }
    
    public function eth_dollor_value(){
        $eth_value_t = json_decode(file_get_contents('https://api.etherscan.io/api?module=stats&action=ethprice'));
        $eth_value = $eth_value_t->result->ethusd;
        $arb_usd_value = doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
        if($eth_value > 70){
            $this->db->query("UPDATE api_settings SET value = ? WHERE name = ?", array($eth_value, 'eth_dollor_value'));
        }
        if($arb_usd_value > 1){
            $arb_usd_value = $this->getTruncatedValue($arb_usd_value, 3);
            $this->db->query("UPDATE api_settings SET value = ? WHERE name = ?", array($arb_usd_value, 'abot_arb'));
        }
    }

    //cron to update order amount limit to 0 after 24 hour
    public function update_order_amount_limit(){
        //, er_order_amount_limit = 0
        $this->db->query("UPDATE exchange_wallet SET order_amount_limit = 0");
    }


}
