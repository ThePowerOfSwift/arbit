<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exchange_v2 extends MY_Controller {
    public $exchange_allow_user_in_lock = array(13870, 791138);
    public $allow_users = array(9, 82, 43, 846, 1001, 59570, 77777, 77836, 78572, 78365, 83632);
    
    public $double_limit = array(13516, 790540, 790542);
    
    
    private function get_24hrs_price($u_id, $amount){
        $before_24_hour_db = date('Y-m-d H:i:s', strtotime('-28 hours'));
        $get_sum_of24 = $this->DB2->query("Select sum(amount) as tot_amount from orders WHERE user_id = ? AND price < 0.01 AND order_type = 'Sell' AND remark != 'cancel' AND create_time >= ?", array($u_id, $before_24_hour_db))->row()->tot_amount;
        $check_amount_24 = $get_sum_of24 + $amount;
        if($check_amount_24 > 100){
            echo json_encode(array('error'=>'1', 'msg'=>"You can't place more than 100 ARB per 24 hours under 0.01 ETH price.")); return false;
        }else{
            return true;
        }
    }
    
    public function saveOrder(){
        $u_id = $this->session->userdata('u_id');
        
        $auto_buy = 0;
        if($this->input->post("get_from") == 'sdfhihf@klhjglk*'){
            $u_id = $this->input->post("u_id");
            $auto_buy = 1;
        }
        if($u_id != ''){
        //check exchange lock 
        if(!$this->exchange_lock($u_id)){exit;}
        //get eth usd value
        if(!$eth_value = $this->get_eth_value()){exit;}
        
        //get user post data
        $order_type = $this->input->post("order_type");
        $wallet = $this->input->post("wallet");
        if($this->input->post("check_flag") == 1){$undercut_flag = 1;}else{$undercut_flag = 0;}
        $amount = $this->getTruncatedValue(doubleval($this->input->post("amount")), 3);
        $price = $this->getTruncatedValue(doubleval($this->input->post("price")), 6);
        
        $price_limit_arb_in_usd = 1/$eth_value;
        
        //check price
        // if($order_type == 'Sell'){
        //     if($price < $price_limit_arb_in_usd){echo json_encode(array('error'=>'1', 'msg' => 'Price is less then 1$.')); exit;}
        // }
        
        if($price < 0.0001){echo json_encode(array('error'=>'1', 'msg' => 'Order not placed.')); exit;}
        //min max amount limit 
        if(!$this->min_max_amount($amount, $order_type)){exit;}
        //max open orders 
        if(!$this->max_open_orders($u_id)){exit;}
        
        //get user package data
        if(!$pkg_data = $this->get_package_details($u_id)){exit;}
        
        //get fee
        $fee_arr = $this->get_order_fee($pkg_data->fee_discount);
        
        $price_filter_users = array(43, 77836, 13516, 790540, 790542);
        if(in_array($u_id, $price_filter_users)){$undercut_flag = 0;}
        
        // for sell order
        if($order_type == 'Sell'){
            
            // if($price < 0.01 && !in_array($u_id, $price_filter_users)){
            //     if(!$this->get_24hrs_price($u_id, $amount)){exit;}
            // }
            //fee for 1001 pro plus user auto sell
            if($u_id == 1001){
                $fee_arr['Sell']['auto'] = 5;
                $fee_arr['Sell']['market'] = 5;
            }
            
            //max open sell orders
            if(!$this->max_sell_open_orders($u_id)){exit;}
            
            //update limits
            //  if($wallet == 'exchange_earned'){
            //      if(!$this->exchange_earned_limit($u_id, $pkg_data->exchange_er_sell_limit, $amount, $price, $eth_value)){exit;}
            //  }else{
                if(!$this->exchange_limit($u_id, $pkg_data->exchange_sell_limit, $amount, $price, $eth_value)){exit;}
            //}
            //seller wallet table name 
            if($wallet == 'exchange_earned'){$wallet_table = 'exchange_earned_wallet';}
            else if($wallet == 'stop_abot'){$wallet_table = 'stop_abot_wallet';}
            else{$wallet_table = 'exchange_wallet';}
             
            //check seller balance 
            if(!$this->check_seller_balance($wallet_table, $u_id, $amount, $wallet, true)){exit;}
            
            //get all open buy orders
            $all_buy_orders = $this->DB2->query("SELECT * FROM orders WHERE order_type = ? AND user_id != ? AND status = ? ORDER BY price DESC",
                array('Buy', $u_id, 0))->result();
                
            foreach($all_buy_orders as $buy_order){
                if($buy_order->price >= $price){
                    //again get buy order for fresh data
                    $buy_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($buy_order->id))->row();
                    if($buy_order->status == 1){continue;}
                    
                    if($buy_order->amount > $amount){
                        if($this->check_buyer_balance($buy_order->user_id, $amount, $price)){
                            $expected_buyer_eth = $amount * $price;
                            // check buy order
                            $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($buy_order->id))->row();
                            if($check_order->status == 1){continue;}
                            
                            //get seller sell wallet and exchange wallet 
                            $seller_sell_wallet = $this->get_seller_wallet($wallet_table, $u_id);
                            $seller_exchange_wallet = $this->get_seller_wallet('exchange_wallet', $u_id);
                            //get buyer exchange wallet 
                            $buyer_exchange_wallet = $this->get_buyer_wallet($buy_order->user_id);
                            // update seller exchange wallet
                            $in_eth_pool = false;
                            if($auto_buy == 1){
                                $cal_eth_fee = ($expected_buyer_eth / 100) * $fee_arr['Sell']['auto'];
                            }else{
                                if($undercut_flag == 0){
                                    $cal_eth_fee = ($expected_buyer_eth / 100) * $fee_arr['Sell']['market'];
                                    $in_eth_pool = true;
                                }else{
                                    $cal_eth_fee = ($expected_buyer_eth / 100) * 10;
                                }
                            }
                            
                            $seller_new_eth = $seller_exchange_wallet->activeEth + ($expected_buyer_eth - $cal_eth_fee);
                            
                            $seller_new_arb = $seller_sell_wallet->activeArb - $amount;
                            $this->db->query("UPDATE ".$wallet_table." SET activeArb = ? WHERE user_id = ?", array($seller_new_arb, $u_id));
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ? WHERE user_id = ?", array($seller_new_eth, $u_id));

                            // update buyer exchange wallet
                            $cal_arb_fee = ($amount / 100) * $fee_arr['Buy']['limit'];
                            $buyer_new_eth = $buyer_exchange_wallet->activeEth - $expected_buyer_eth;
                            $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount - $cal_arb_fee);
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $buy_order->user_id));

                            //update admin wallet
                            $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                            $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                            if($in_eth_pool){
                                $get_eth_pool = $this->db->query("SELECT * FROM eth_bonus_pool WHERE id = 1 LIMIT 1")->row();
                                $new_eth_pool = $get_eth_pool->activeEth + $cal_eth_fee;
                                $this->db->query("UPDATE eth_bonus_pool SET activeEth = ? WHERE id = ?", array($new_eth_pool, 1));
                                $admin_new_eth = $get_admin_wallet->eth;
                            }else{
                                $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                            }
                            $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_new_arb, $admin_new_eth, 1));

                            // sell order completely buy
                            $this->DB2->query("INSERT INTO orders(user_id, order_type, amount, price, order_id, status, order_from, order_category, eth_usd, undercut_flag) VALUES (?,?,?,?,?,?,?,?,?,?)",
                                array($u_id, 'Sell', $amount, $price, $buy_order->id, 1, $wallet, 'market', $eth_value, $undercut_flag));
                            $insert_id = $this->DB2->insert_id();

                            // break original buy order to an other and mark this as complete
                            $this->DB2->query("UPDATE orders SET amount = ?, price = ?, status = ?, order_id = ?, eth_usd = ? WHERE id = ?", array($amount, $price, 1, $insert_id, $eth_value, $buy_order->id));

                            //create new buy order of remaining amount
                            $new_order_amount = $buy_order->amount - $amount;
                                
                            if($new_order_amount >= 5){
                                $new_order_amount = $this->getTruncatedValue($new_order_amount, 4);
                                $this->DB2->query("INSERT INTO orders(user_id, order_type, amount, price, eth_usd, order_category) VALUES (?,?,?,?,?,?)",
                                array($buy_order->user_id, 'Buy', $new_order_amount, $buy_order->price, $eth_value, $buy_order->order_category));
                            }
                            //logs of seller
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id, $wallet_table.'_SellARB','-'.$amount, $insert_id, $seller_sell_wallet->activeArb, "Sell ARB from ".$wallet_table." (market)"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_BuyETH',$expected_buyer_eth, $insert_id, $seller_exchange_wallet->activeEth, "Buy ETH in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $insert_id, ($seller_exchange_wallet->activeEth + $expected_buyer_eth), "Fee"));
                            if($in_eth_pool){
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'ethpool_feeAdd_ETH',$cal_eth_fee, $insert_id, $get_eth_pool->activeEth, "Fee"));
                            }else{
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $insert_id, $get_admin_wallet->eth, "Fee"));
                            }

                            //logs of buyer
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeWallet_SellETH','-'.$expected_buyer_eth, $buy_order->id,$buyer_exchange_wallet->activeEth, "Sell Eth in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeWallet_BuyARB',$amount, $buy_order->id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $buy_order->id, ($buyer_exchange_wallet->activeArb + $amount),"Fee"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $buy_order->id, $get_admin_wallet->arb, "Fee"));

                            $amount = $amount - $buy_order->amount;
                            $amount = $this->getTruncatedValue($amount, 4);
                            echo json_encode(array('success'=>'1', 'msg'=>'Order successfully completed')); exit;
                            
                        }
                    }else if($buy_order->amount < $amount){
                        if($this->check_buyer_balance($buy_order->user_id, $buy_order->amount, $price)){
                            $expected_buyer_eth = $buy_order->amount * $price;
                            // check buy order
                            $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($buy_order->id))->row();
                            if($check_order->status == 1){continue;}
                            
                            //get seller sell wallet and exchange wallet 
                            $seller_sell_wallet = $this->get_seller_wallet($wallet_table, $u_id);
                            $seller_exchange_wallet = $this->get_seller_wallet('exchange_wallet', $u_id);
                            //get buyer exchange wallet 
                            $buyer_exchange_wallet = $this->get_buyer_wallet($buy_order->user_id);
                            
                            // update seller exchange wallet
                            $in_eth_pool = false;
                            if($auto_buy == 1){
                                $cal_eth_fee = ($expected_buyer_eth / 100) * $fee_arr['Sell']['auto'];
                            }else{
                                if($undercut_flag == 0){
                                    $cal_eth_fee = ($expected_buyer_eth / 100) * $fee_arr['Sell']['market'];
                                    $in_eth_pool = true;
                                }else{
                                    $cal_eth_fee = ($expected_buyer_eth / 100) * 10;
                                }
                            }
                            $seller_new_eth = $seller_exchange_wallet->activeEth + ($expected_buyer_eth - $cal_eth_fee);
                            $seller_new_arb = $seller_sell_wallet->activeArb - $buy_order->amount;
                            $this->db->query("UPDATE ".$wallet_table." SET activeArb = ? WHERE user_id = ?", array($seller_new_arb, $u_id));
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ? WHERE user_id = ?", array($seller_new_eth, $u_id));

                            // update buyer exchange wallet
                            $cal_arb_fee = ($buy_order->amount / 100) * $fee_arr['Buy']['limit'];
                            $buyer_new_eth = $buyer_exchange_wallet->activeEth - $expected_buyer_eth;
                            $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($buy_order->amount - $cal_arb_fee);
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $buy_order->user_id));

                            //update admin wallet
                            $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                            $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                            if($in_eth_pool){
                                $get_eth_pool = $this->db->query("SELECT * FROM eth_bonus_pool WHERE id = 1 LIMIT 1")->row();
                                $new_eth_pool = $get_eth_pool->activeEth + $cal_eth_fee;
                                $this->db->query("UPDATE eth_bonus_pool SET activeEth = ? WHERE id = ?", array($new_eth_pool, 1));
                                $admin_new_eth = $get_admin_wallet->eth;
                            }else{
                                $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                            }
                            $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_new_arb, $admin_new_eth, 1));

                            // sell order create which amount is buyed
                            $this->DB2->query("INSERT INTO orders(user_id, order_type, amount, price, order_id, status, order_category, order_from, eth_usd, undercut_flag) VALUES (?,?,?,?,?,?,?,?,?,?)",
                                array($u_id, 'Sell', $buy_order->amount, $price, $buy_order->id, 1, 'market', $wallet, $eth_value, $undercut_flag));
                            $insert_id = $this->DB2->insert_id();


                            // buy order completed
                            $this->DB2->query("UPDATE orders SET status = ?, price = ? , order_id = ?, eth_usd = ? WHERE id = ?", array(1, $price, $insert_id, $eth_value, $buy_order->id));


                            //logs of seller
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id, $wallet_table.'_SellARB','-'.$buy_order->amount, $insert_id, $seller_sell_wallet->activeArb, "Sell ARB from ".$wallet_table." (market)"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_BuyETH',$expected_buyer_eth, $insert_id, $seller_exchange_wallet->activeEth, "Buy ETH in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $insert_id, ($seller_exchange_wallet->activeEth + $expected_buyer_eth), "Fee"));
                            if($in_eth_pool){
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'ethpool_feeAdd_ETH',$cal_eth_fee, $insert_id, $get_eth_pool->activeEth, "Fee"));
                            }else{
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $insert_id, $get_admin_wallet->eth, "Fee"));
                            }

                            //logs of buyer
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeWallet_SellETH','-'.$expected_buyer_eth, $buy_order->id,$buyer_exchange_wallet->activeEth, "Sell ETH in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeWallet_BuyARB',$buy_order->amount, $buy_order->id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $buy_order->id, ($buyer_exchange_wallet->activeArb + $buy_order->amount), "Fee"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $buy_order->id, $get_admin_wallet->arb, "Fee"));

                            $amount = $amount - $buy_order->amount;
                            $amount = $this->getTruncatedValue($amount, 4);
                        }
                    }else if($buy_order->amount == $amount){
                        if($this->check_buyer_balance($buy_order->user_id, $amount, $price)){
                            $expected_buyer_eth = $amount * $price;
                            // check buy order
                            $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($buy_order->id))->row();
                            if($check_order->status == 1){continue;}
                            
                            //get seller sell wallet and exchange wallet 
                            $seller_sell_wallet = $this->get_seller_wallet($wallet_table, $u_id);
                            $seller_exchange_wallet = $this->get_seller_wallet('exchange_wallet', $u_id);
                            //get buyer exchange wallet 
                            $buyer_exchange_wallet = $this->get_buyer_wallet($buy_order->user_id);
                            
                            
                            $in_eth_pool = false;
                            // update seller exchange wallet
                            if($auto_buy == 1){
                                $cal_eth_fee = ($expected_buyer_eth / 100) * $fee_arr['Sell']['auto'];
                            }else{
                                if($undercut_flag == 0){
                                    $cal_eth_fee = ($expected_buyer_eth / 100) * $fee_arr['Sell']['market'];
                                    $in_eth_pool = true;
                                }else{
                                    $cal_eth_fee = ($expected_buyer_eth / 100) * 10;
                                }
                            }
                            $seller_new_eth = $seller_exchange_wallet->activeEth + ($expected_buyer_eth - $cal_eth_fee);
                            $seller_new_arb = $seller_sell_wallet->activeArb - $amount;
                            $this->db->query("UPDATE ".$wallet_table." SET activeArb = ? WHERE user_id = ?", array($seller_new_arb, $u_id));
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ? WHERE user_id = ?", array($seller_new_eth, $u_id));

                            // update buyer exchange wallet
                            $cal_arb_fee = ($amount / 100) * $fee_arr['Buy']['limit'];
                            $buyer_new_eth = $buyer_exchange_wallet->activeEth - $expected_buyer_eth;
                            $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount - $cal_arb_fee);
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $buy_order->user_id));
                            
                            
                            //update admin wallet
                            $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                            $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                            if($in_eth_pool){
                                $get_eth_pool = $this->db->query("SELECT * FROM eth_bonus_pool WHERE id = 1 LIMIT 1")->row();
                                $new_eth_pool = $get_eth_pool->activeEth + $cal_eth_fee;
                                $this->db->query("UPDATE eth_bonus_pool SET activeEth = ? WHERE id = ?", array($new_eth_pool, 1));
                                $admin_new_eth = $get_admin_wallet->eth;
                            }else{
                                $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                            }
                            $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_new_arb, $admin_new_eth, 1));

                            // sell order completely buy
                            $this->DB2->query("INSERT INTO orders(user_id, order_type, amount, price, order_id, status, order_from, order_category, eth_usd, undercut_flag) VALUES (?,?,?,?,?,?,?,?,?,?)",
                                array($u_id, 'Sell', $amount, $price, $buy_order->id, 1, $wallet, 'market', $eth_value, $undercut_flag));
                            $insert_id = $this->DB2->insert_id();


                            // break original buy order to an other and mark this as complete
                            $this->DB2->query("UPDATE orders SET status = ?, order_id = ?, eth_usd = ?, price = ? WHERE id = ?", array(1, $insert_id, $eth_value, $price, $buy_order->id));


                            //logs of seller
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id, $wallet_table.'_SellARB','-'.$amount, $insert_id, $seller_sell_wallet->activeArb, "Sell ARB from ".$wallet_table." (market)"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_BuyETH',$expected_buyer_eth, $insert_id, $seller_exchange_wallet->activeEth, "Buy ETH in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $insert_id, ($seller_exchange_wallet->activeEth + $expected_buyer_eth), "Fee"));
                            if($in_eth_pool){
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'ethpool_feeAdd_ETH',$cal_eth_fee, $insert_id, $get_eth_pool->activeEth, "Fee"));
                            }else{
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $insert_id, $get_admin_wallet->eth, "Fee"));
                            }

                            //logs of buyer
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeWallet_SellETH','-'.$expected_buyer_eth, $buy_order->id,$buyer_exchange_wallet->activeEth, "Sell ETH in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeWallet_BuyARB',$amount, $buy_order->id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $buy_order->id, ($buyer_exchange_wallet->activeArb + $amount),"Fee"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($buy_order->user_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $buy_order->id, $get_admin_wallet->arb, "Fee"));

                            $amount = $amount - $buy_order->amount;
                            $amount = $this->getTruncatedValue($amount, 4);
                            echo json_encode(array('success'=>'1', 'msg'=>'Order successfully completed')); exit;
                        }
                    }
                    
                }
            }
            
            if($amount >= 10){
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
                    $this->DB2->query("INSERT INTO orders (user_id, order_type, amount, price, order_from, order_category, eth_usd, undercut_flag) VALUES (?,?,?,?,?,?,?,?)",
                    array($u_id, 'Sell', $amount, $newwwPrice,  $wallet, 'limit', $eth_value, $undercut_flag));
                    echo json_encode(array('success'=>'1', 'msg'=>'Order is placed')); exit;
            }
            
        }else if($order_type == 'Buy'){
            
            if($auto_buy != 1 && $u_id != 43){
                $before_1hour_db = date("Y-m-d H:i:s", strtotime("-6 hour"));
                $orders_buy_check = $this->DB2->query("SELECT * FROM orders WHERE user_id = ? AND order_type = ? AND status = ? AND remark != ? AND auto_buy != ? AND created_at > ?", array($u_id, 'Buy', 1, 'cancel', 1, $before_1hour_db))->result();
                //print_r($orders_buy_check); exit;
                $total_sum = 0;
                foreach($orders_buy_check as $orderr){
                    $aa = $orderr->amount * $orderr->price;
                    $total_sum = $total_sum + $aa;
                }
                $aba = $amount * $price;
                $total_sum = $total_sum + $aba;
                if($total_sum >= 400){
                    echo json_encode(array('error'=>'1', 'msg' => 'Your per hour Buy limit exceeded.')); exit;
                }
             }
            
            
            
           // $auto_buy = 0;
            //check buyer balance 
            if(!$this->check_buyer_balance($u_id, $amount, $price, true)){exit;}
            $all_sell_orders = $this->DB2->query("SELECT * FROM orders WHERE order_type = ? AND user_id != ? AND status = ? ORDER BY price ASC",array('Sell', $u_id, 0))->result();
            foreach($all_sell_orders as $sell_order){
                if($sell_order->user_id == 1001){
                    $fee_arr['Sell']['limit'] = 5;
                }
                if($sell_order->price <= $price){
                    //check order
                    $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($sell_order->id))->row();
                    if($check_order->status == 1){
                        continue;
                    }
                    
                    if($sell_order->order_from == 'exchange_earned'){$wallet_table = 'exchange_earned_wallet';}
                    else if($sell_order->order_from == 'stop_abot'){$wallet_table = 'stop_abot_wallet';}
                    else{$wallet_table = 'exchange_wallet';}
                    
                    if($sell_order->amount > $amount){
                        $expected_buyer_eth = $amount * $sell_order->price;
                        if($this->check_seller_balance($wallet_table, $sell_order->user_id, $amount, $sell_order->order_from)){
                            
                            //check order
                            $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($sell_order->id))->row();
                            if($check_order->status == 1){continue;}
                            
                            //get seller sell wallet and exchange wallet 
                            $seller_sell_wallet = $this->get_seller_wallet($wallet_table, $sell_order->user_id);
                            $seller_exchange_wallet = $this->get_seller_wallet('exchange_wallet', $sell_order->user_id);
                            //get buyer exchange wallet 
                            $buyer_exchange_wallet = $this->get_buyer_wallet($u_id);
                            
                            // update seller exchange wallet
                            if($sell_order->undercut_flag == 1){
                                $cal_eth_fee = ($expected_buyer_eth / 100) * ($fee_arr['Sell']['limit'] + 9.5);
                            }else{
                                $cal_eth_fee = ($expected_buyer_eth / 100) * $fee_arr['Sell']['limit'];
                            }
                            
                            $seller_new_eth = $seller_exchange_wallet->activeEth + ($expected_buyer_eth - $cal_eth_fee);
                            $seller_new_arb = $seller_sell_wallet->activeArb - $amount;
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ? WHERE user_id = ?", array($seller_new_eth, $sell_order->user_id));
                            $this->db->query("UPDATE ".$wallet_table." SET activeArb = ? WHERE user_id = ?", array($seller_new_arb, $sell_order->user_id));

                            // update buyer exchange wallet
                            //$cal_arb_fee = ($amount / 100)*$arb_market_fee;
                            //$buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount - $cal_arb_fee);\
                            $cal_arb_fee = 0;
                            $bonus3per = 0;
                            $buyer_new_eth = $buyer_exchange_wallet->activeEth - $expected_buyer_eth;
                            
                            if($auto_buy == 1){
                                $cal_arb_fee = ($amount / 100) * $fee_arr['Buy']['auto'];
                                $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount - $cal_arb_fee);
                            }else{
                                // 3% buy market bonus
                                // $bonus3per = ($amount / 100) * 3;
                                // $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount + $bonus3per);
                                $cal_arb_fee = ($amount / 100) * $fee_arr['Buy']['market'];
                                $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount - $cal_arb_fee);
                            
                            }
                            
                            
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $u_id));

                            //update admin wallet
                            $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                            $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                            $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                            $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_new_arb, $admin_new_eth, 1));

                            // Buy order completely Sell
                            $this->DB2->query("INSERT INTO orders(user_id, order_type, amount, price, order_id, status, order_category, auto_buy, eth_usd) VALUES (?,?,?,?,?,?,?,?,?)",
                            array($u_id, 'Buy', $amount, $sell_order->price, $sell_order->id, 1, 'market', $auto_buy, $eth_value));
                            $insert_id = $this->DB2->insert_id();


                            // break original Sell order to an other and mark this as complete
                            $this->DB2->query("UPDATE orders SET amount = ?, status = ?, order_id = ?, eth_usd = ? WHERE id = ?", array($amount, 1, $insert_id, $eth_value, $sell_order->id));

                            //create new Sell order of remaining amount
                            $new_order_amount = $sell_order->amount - $amount;
                            if($new_order_amount >= 5){
                                $new_order_amount = $this->getTruncatedValue($new_order_amount, 4);
                                $this->DB2->query("INSERT INTO orders(user_id, order_type, amount, price, eth_usd, order_from, order_category) VALUES (?,?,?,?,?,?,?)",
                                array($sell_order->user_id, 'Sell', $new_order_amount, $sell_order->price, $eth_value, $sell_order->order_from, $sell_order->order_category));
                            }
                            //logs of seller
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,$wallet_table.'_SellARB','-'.$amount, $sell_order->id, $seller_sell_wallet->activeArb, "Sell ARB from ".$wallet_table));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeWallet_BuyETH',$expected_buyer_eth, $sell_order->id, $seller_exchange_wallet->activeEth, "Buy ETH in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $sell_order->id, ($seller_exchange_wallet->activeEth + $expected_buyer_eth), "Fee"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $sell_order->id, $get_admin_wallet->eth, "Fee"));
                            
                            //logs of buyer
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_SellETH','-'.$expected_buyer_eth, $insert_id,$buyer_exchange_wallet->activeEth,"Sell ETH in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_BuyARB',$amount, $insert_id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange (market)"));
                                
                            
                            if($cal_arb_fee > 0){
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $insert_id, ($buyer_exchange_wallet->activeARB + $amount), "Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $insert_id, $get_admin_wallet->arb, "Fee"));
                            
                            }else if($bonus3per > 0){
                                // 3% bonus log  
                                $bonus_last_blnc = ($buyer_exchange_wallet->activeArb + $amount);
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_ARB_bonus',$bonus3per, $insert_id, $bonus_last_blnc, "3% bonus of Market buy"));
                                
                                //update bonus table 
                                $get_bonus = $this->db->query("SELECT * FROM bonus WHERE id = 1")->row();
                                $update_arb_bonus = $get_bonus->arb + $bonus3per;
                                $this->db->query("UPDATE bonus SET arb = ? WHERE id = 1", array($update_arb_bonus));
                            }
                            $amount = $amount - $sell_order->amount;
                            $amount = $this->getTruncatedValue($amount, 4);
                            echo json_encode(array('success'=>'1', 'msg'=>'Order successfully completed ')); exit;

                        }
                    }else if($sell_order->amount < $amount){
                        $expected_buyer_eth = $sell_order->amount * $sell_order->price;
                        if($this->check_seller_balance($wallet_table, $sell_order->user_id,  $sell_order->amount, $sell_order->order_from)){
                            //check order
                            $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($sell_order->id))->row();
                            if($check_order->status == 1){continue;}
                            
                            //get seller sell wallet and exchange wallet 
                            $seller_sell_wallet = $this->get_seller_wallet($wallet_table, $sell_order->user_id);
                            $seller_exchange_wallet = $this->get_seller_wallet('exchange_wallet', $sell_order->user_id);
                            //get buyer exchange wallet 
                            $buyer_exchange_wallet = $this->get_buyer_wallet($u_id);
                           
                            
                            // update seller exchange wallet
                            //$cal_eth_fee = ($expected_buyer_eth / 100)*$eth_fee;
                            if($sell_order->undercut_flag == 1){
                                $cal_eth_fee = ($expected_buyer_eth / 100) * ($fee_arr['Sell']['limit'] + 9.5);
                            }else{
                                $cal_eth_fee = ($expected_buyer_eth / 100) * $fee_arr['Sell']['limit'];
                            }
                            
                            $seller_new_eth = $seller_exchange_wallet->activeEth + ($expected_buyer_eth - $cal_eth_fee);
                            $seller_new_arb = $seller_sell_wallet->activeArb - $sell_order->amount;
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ? WHERE user_id = ?", array($seller_new_eth, $sell_order->user_id));
                            $this->db->query("UPDATE ".$wallet_table." SET activeArb = ? WHERE user_id = ?", array($seller_new_arb, $sell_order->user_id));

                            // update buyer exchange wallet
                            $cal_arb_fee = 0; //($sell_order->amount / 100) * $arb_market_fee;
                            $bonus3per = 0;
                            //$buyer_new_arb = $buyer_exchange_wallet->activeArb + ($sell_order->amount - $cal_arb_fee);
                            
                            $buyer_new_eth = $buyer_exchange_wallet->activeEth - $expected_buyer_eth;
                            if($auto_buy == 1){
                                $cal_arb_fee = ($sell_order->amount / 100) * $fee_arr['Buy']['auto'];
                                $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($sell_order->amount - $cal_arb_fee);
                            }else{
                                // 3% buy market bonus
                                // $bonus3per = ($sell_order->amount / 100) * 3;
                                // $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($sell_order->amount + $bonus3per);
                                
                                $cal_arb_fee = ($sell_order->amount / 100) * $fee_arr['Buy']['market'];
                                $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($sell_order->amount - $cal_arb_fee);
                            }
                            
                            
                            
                            
                            // 3% buy market bonus
                            //$bonus3per = ($sell_order->amount / 100) * 3;
                            //$buyer_new_arb = $buyer_exchange_wallet->activeArb + ($sell_order->amount + $bonus3per);
                            
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $u_id));

                            //update admin wallet
                            $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                            $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                            $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                            $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_new_arb, $admin_new_eth, 1));

                            // buy order create which amount is selled
                            $this->DB2->query("INSERT INTO orders(user_id, order_type, amount, price, order_id, status, order_category, auto_buy, eth_usd) VALUES (?,?,?,?,?,?,?,?,?)",
                                array($u_id, 'Buy', $sell_order->amount, $sell_order->price, $sell_order->id, 1, 'market', $auto_buy, $eth_value));
                            $insert_id = $this->DB2->insert_id();


                            // Sell order completed
                            $this->DB2->query("UPDATE orders SET status = ?, order_id = ?, eth_usd = ? WHERE id = ?", array(1, $insert_id, $eth_value, $sell_order->id));


                            //logs of seller
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,$wallet_table.'_SellARB','-'.$sell_order->amount, $sell_order->id, $seller_sell_wallet->activeArb, "Sell ARB from ".$wallet_table));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeWallet_BuyETH',$expected_buyer_eth, $sell_order->id, $seller_exchange_wallet->activeEth, "Buy Eth in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $sell_order->id, ($seller_exchange_wallet->activeEth + $expected_buyer_eth), "Fee"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $sell_order->id, $get_admin_wallet->eth, "Fee"));


                            //logs of buyer
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_SellETH','-'.$expected_buyer_eth, $insert_id,$buyer_exchange_wallet->activeEth, "Sell ETH in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_BuyARB',$sell_order->amount, $insert_id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange (market)"));
                            
                            if($cal_arb_fee > 0){
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $insert_id, ($buyer_exchange_wallet->activeARB + $sell_order->amount), "Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $insert_id, $get_admin_wallet->arb, "Fee"));
                            
                            }else if($bonus3per > 0){
                                // 3% bonus log  
                                $bonus_last_blnc = ($buyer_exchange_wallet->activeArb + $sell_order->amount);
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_ARB_bonus',$bonus3per, $insert_id, $bonus_last_blnc, "3% bonus of Market buy"));
                                
                                //update bonus table 
                                $get_bonus = $this->db->query("SELECT * FROM bonus WHERE id = 1")->row();
                                $update_arb_bonus = $get_bonus->arb + $bonus3per;
                                $this->db->query("UPDATE bonus SET arb = ? WHERE id = 1", array($update_arb_bonus));
                            }
                            
                            $amount = $amount - $sell_order->amount;
                            $amount = $this->getTruncatedValue($amount, 4);

                        }

                    }
                    else if($sell_order->amount == $amount){
                        $expected_buyer_eth = $amount * $sell_order->price;
                        if($this->check_seller_balance($wallet_table, $sell_order->user_id,  $amount, $sell_order->order_from)){
                            //check order
                            $check_order = $this->DB2->query("SELECT * FROM orders WHERE id = ?", array($sell_order->id))->row();
                            if($check_order->status == 1){continue;}
                            
                            //get seller sell wallet and exchange wallet 
                            $seller_sell_wallet = $this->get_seller_wallet($wallet_table, $sell_order->user_id);
                            $seller_exchange_wallet = $this->get_seller_wallet('exchange_wallet', $sell_order->user_id);
                            //get buyer exchange wallet 
                            $buyer_exchange_wallet = $this->get_buyer_wallet($u_id);
                           
                            
                            // update seller exchange wallet
                            //$cal_eth_fee = ($expected_buyer_eth / 100)*$eth_fee;
                            if($sell_order->undercut_flag == 1){
                                $cal_eth_fee = ($expected_buyer_eth / 100) * ($fee_arr['Sell']['limit'] + 9.5);
                            }else{
                                $cal_eth_fee = ($expected_buyer_eth / 100) * $fee_arr['Sell']['limit'];
                            }
                            
                            $seller_new_eth = $seller_exchange_wallet->activeEth + ($expected_buyer_eth - $cal_eth_fee);
                            $seller_new_arb = $seller_sell_wallet->activeArb - $amount;
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ? WHERE user_id = ?", array($seller_new_eth, $sell_order->user_id));
                            $this->db->query("UPDATE ".$wallet_table." SET activeArb = ? WHERE user_id = ?", array($seller_new_arb, $sell_order->user_id));

                            // update buyer exchange wallet
                            $cal_arb_fee = 0; //($amount / 100)*$arb_market_fee;
                            $bonus3per = 0;
                            //$buyer_new_arb = $buyer_exchange_wallet->activeArb + ($sell_order->amount - $cal_arb_fee);
                            
                            $buyer_new_eth = $buyer_exchange_wallet->activeEth - $expected_buyer_eth;
                            if($auto_buy == 1){
                                $cal_arb_fee = ($amount / 100) * $fee_arr['Buy']['auto'];
                                $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount - $cal_arb_fee);
                            }else{
                                // 3% buy market bonus
                                // $bonus3per = ($amount / 100) * 3;
                                // $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount + $bonus3per);
                                $cal_arb_fee = ($amount / 100) * $fee_arr['Buy']['market'];
                                $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount - $cal_arb_fee);
                            }
                            
                            
                            //$buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount - $cal_arb_fee);
                            
                            // 3% buy market bonus
                            // $bonus3per = ($amount / 100) * 3;
                            // $buyer_new_arb = $buyer_exchange_wallet->activeArb + ($amount + $bonus3per);
                            
                            $this->db->query("UPDATE exchange_wallet SET activeEth = ?, activeArb = ? WHERE user_id = ?", array($buyer_new_eth, $buyer_new_arb, $u_id));

                            //update admin wallet
                            $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                            $admin_new_arb = $get_admin_wallet->arb + $cal_arb_fee;
                            $admin_new_eth = $get_admin_wallet->eth + $cal_eth_fee;
                            $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_new_arb, $admin_new_eth, 1));

                            // buy order completely buy
                            $this->DB2->query("INSERT INTO orders(user_id, order_type, amount, price, order_id, status, order_category, auto_buy, eth_usd) VALUES (?,?,?,?,?,?,?,?,?)",
                            array($u_id, 'Buy', $amount, $sell_order->price, $sell_order->id, 1, 'market', $auto_buy, $eth_value));
                            $insert_id = $this->DB2->insert_id();


                            // break original buy order to an other and mark this as complete
                            $this->DB2->query("UPDATE orders SET status = ?, order_id = ?, eth_usd = ? WHERE id = ?", array(1, $insert_id, $eth_value, $sell_order->id));


                            //logs of seller
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,$wallet_table.'_SellARB','-'.$amount, $sell_order->id, $seller_sell_wallet->activeArb, "Sell ARB from ".$wallet_table));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeWallet_BuyETH',$expected_buyer_eth, $sell_order->id, $seller_exchange_wallet->activeEth, "Buy ETH in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $sell_order->id, ($seller_exchange_wallet->activeEth + $expected_buyer_eth), "Fee"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($sell_order->user_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $sell_order->id, $get_admin_wallet->eth, "Fee"));


                            //logs of buyer
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_SellETH','-'.$expected_buyer_eth, $insert_id,$buyer_exchange_wallet->activeEth, "Sell ETH in Exchange"));
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_BuyARB',$amount, $insert_id, $buyer_exchange_wallet->activeArb, "Buy ARB in Exchange (market)"));
                            
                            if($cal_arb_fee > 0){
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $insert_id, ($buyer_exchange_wallet->activeARB + $amount), "Fee"));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $insert_id, $get_admin_wallet->arb, "Fee"));
                            
                            }else if($bonus3per > 0){
                                // 3% bonus log  
                                $bonus_last_blnc = ($buyer_exchange_wallet->activeArb + $amount);
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id, last_blnc, comment) values (?,?,?,?,?,?)", array($u_id,'exchangeWallet_ARB_bonus',$bonus3per, $insert_id, $bonus_last_blnc, "3% bonus of Market buy"));
                                
                                //update bonus table 
                                $get_bonus = $this->db->query("SELECT * FROM bonus WHERE id = 1")->row();
                                $update_arb_bonus = $get_bonus->arb + $bonus3per;
                                $this->db->query("UPDATE bonus SET arb = ? WHERE id = 1", array($update_arb_bonus));
                            }
                            
                            $amount = $amount - $sell_order->amount;
                            $amount = $this->getTruncatedValue($amount, 4);
                            echo json_encode(array('success'=>'1', 'msg'=>'Order successfully completed ')); exit;
                        }
                    }
                }


            }

            if($amount >= 10){
                //if($price >= $price_limit_arb_in_usd){
                    $amount = $this->getTruncatedValue($amount, 4);
                    $this->DB2->query("INSERT INTO orders (user_id, order_type, amount, price, eth_usd, auto_buy, order_category) VALUES (?,?,?,?,?,?,?)",
                        array($u_id, 'Buy', $amount, $price, $eth_value, $auto_buy, 'limit'));
                    echo json_encode(array('success'=>'1', 'msg'=>'Order placed successfully ')); exit;
                // }else{
                //     echo json_encode(array('error'=>'1', 'msg'=>'Limit order less then 1$ not placed')); exit;
                // }
            }


        }else{
            return false;
        }
        
        }
        
    }
    
    
    
//------------------------------------------------ private function used in exchange-----------------------------------------------------//
    //check exchange is locked or not
    private function exchange_lock($u_id){
        $exchange_status = $this->db->query("SELECT * FROM admin_locks WHERE name = 'exchange_lock'")->row();
        if($exchange_status->lock_status == 1 && !in_array($u_id, $this->exchange_allow_user_in_lock )){
            echo json_encode(array('error'=>'1', 'msg' => 'Sorry for inconvenience this feature is temporarily locked.'));
			return false;
        }else{
            return true;
        }
    }
    
    // get eth value from DB
    private function get_eth_value(){
        $eth_value = $this->db->query("SELECT * FROM api_settings WHERE name = ?", array('eth_dollor_value'))->row()->value;
		if($eth_value < 70){
            echo json_encode(array('error'=>'1', 'msg' => 'Unexpected ETH value.'));
            return false;
        }else{
            return $eth_value;
        }
    }
    
    //get user package detail
    private function get_package_details($u_id){
        //get user data 
        if($user_data = $this->db->query("SELECT package FROM users WHERE u_id = ?", array($u_id))->row()){
            if($pkg_data = $this->db->query("SELECT * FROM packages WHERE name = ?", array($user_data->package))->row()){
                return $pkg_data;
            }else{
                echo json_encode(array('error'=>'1', 'msg' => 'No such package exist in our record.'));
                return false; 
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg' => 'No such user exist in our record.'));
            return false; 
        }
    }
    
    //max open orders 
    private function max_open_orders($u_id){
        if(in_array($u_id, $this->double_limit)){$or_limit = 10;}else{$or_limit = 10;}
        $total_orders = $this->DB2->query("SELECT count(*) as orders FROM orders WHERE user_id = ? AND status = ?", array($u_id, 0))->row()->orders;
        if($total_orders >= $or_limit && !in_array($u_id, $this->allow_users)){
            echo json_encode(array('error'=>'1', 'msg' => 'Maximun '.$or_limit.' open orders placed.'));
			return false;
        }else{
            return true;
        }
    }
    
    //max open sell orders
    private function max_sell_open_orders($u_id){
        if(in_array($u_id, $this->double_limit)){$or_limit = 10;}else{$or_limit = 10;}
        $sell_orders = $this->DB2->query("SELECT count(*) as orders FROM orders WHERE order_type = ? AND user_id = ? AND status = ?", array('Sell',$u_id, 0))->row()->orders;
        if($sell_orders >= $or_limit && !in_array($u_id, $this->allow_users)){
            echo json_encode(array('error'=>'1', 'msg' => 'Not more then '.$or_limit.' open sell orders are allowed.'));
            return false;
        }else{
            return true;
        }
    }
    
    //min_max amount check
    private function min_max_amount($amount, $order_type){
        if($amount < 10){
            echo json_encode(array('error'=>'1', 'msg' => 'Minimum amount allowed is 10.'));
            return false;
        }else if($amount > 500 && $order_type == 'Sell'){
            echo json_encode(array('error'=>'1', 'msg' => 'Maximum amount allowed is 500.'));
            return false;
        }else{
            return true;
        }
    } 
    
    //exchange and stop_abot wallet limits
    private function exchange_limit($u_id, $pkg_limit, $amount, $price, $eth_value){
        if($u_id == 1001){return true;}
        if(in_array($u_id, $this->allow_users)){$limit = 400000;}
        else if(in_array($u_id, $this->double_limit)){$limit = 10000;}
        else{$limit = $pkg_limit;}
        $order_amount_limit = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row()->order_amount_limit;
        $amount = doubleval($amount);
        $cr_arb_price_usd = $eth_value * $price;
        $total_order_ammount = $order_amount_limit + ($amount * $cr_arb_price_usd);
        if($total_order_ammount > $limit){
            echo json_encode(array('error'=>'1', 'msg' => 'Your per day order limit is exceeded.'));
            return false;
        }else{
            $total_order_ammount = $this->getTruncatedValue($total_order_ammount, 6);
            $this->db->query("UPDATE exchange_wallet SET order_amount_limit = ? WHERE user_id = ?", array($total_order_ammount, $u_id));
            return true;
        }
    }
    
    //exchange_earned wallet limite
    private function exchange_earned_limit($u_id, $pkg_limit, $amount, $price, $eth_value){
        $before1hour = date("Y-m-d H:i:s", strtotime('-6 hour'));
        $amm = 0;
        if($total_orders1 = $this->DB2->query("SELECT * FROM orders WHERE order_type = ? AND remark != ? AND user_id = ? AND order_from LIKE ? AND create_time > ? ", array('Sell', 'cancel', $u_id, 'exchange_earned', $before1hour))->result()){
            foreach($total_orders1 as $ord){
                $order_arb_price_usd = $ord->price * $ord->eth_usd;
                $amm = $amm + ($ord->amount * $order_arb_price_usd);
            }
        }
        $amm = $this->getTruncatedValue($amm, 4);
        $this->db->query("UPDATE exchange_wallet SET er_order_amount_limit = ? WHERE user_id = ?", array($amm, $u_id));
                
        if(in_array($u_id, $this->allow_users)){$limit = 400000;}
        else if(in_array($u_id, $this->double_limit)){$limit = 10000;}
        else{$limit = $pkg_limit;}
        $order_amount_limit = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row()->er_order_amount_limit;
        $amount = doubleval($amount);
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
            
    }
   
    
    //get order fee 
    private function get_order_fee($discount){
        $fee = array(
            'Sell' => array(
                'limit' => (0.5 - ((0.5/100) * $discount)),
                'market' => (10 - ((10/100) * $discount)),
                'auto' => (1.5 - ((1.5/100) * $discount))
                ),
            'Buy' => array(
                'limit' => (0.5 - ((0.5/100) * $discount)),
                'market' => (0.25 - ((0.25/100) * $discount)),
                'auto' => (1 - ((1/100) * $discount))
                )
            );
        return $fee;
        
    }
    
    
    //check seller balance
    private function check_seller_balance($table, $u_id, $amount, $order_from, $placing = false){
        if($placing){
            $seller_wallet = $this->db->query("SELECT * FROM ".$table." WHERE user_id = ?", array($u_id))->row();
            $total_amount = $this->DB2->query("SELECT sum(amount) as total_amount FROM orders WHERE user_id = ? AND order_type = ? AND status = ? AND order_from = ?", 
                array($u_id, 'Sell', 0, $order_from))->row()->total_amount;
            if($seller_wallet->activeArb < $amount || $seller_wallet->activeArb < ($total_amount + $amount)){
                echo json_encode(array('error'=>'1', 'msg' => 'Your account balance is not enough.'));
                return false;
            }else{
                return true;
            }
        }else{
            $seller_wallet = $this->db->query("SELECT * FROM ".$table." WHERE user_id = ?", array($u_id))->row();
            if($seller_wallet->activeArb < $amount){
                return false;
            }else{
                return true;
            }
        }
    }
    
    //check buyer balance
    private function check_buyer_balance($u_id, $amount, $price, $placing = false){
        if($placing){
            $buyer_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row();
            $total_eth = $this->DB2->query("SELECT sum(amount*price) as total_eth FROM orders WHERE user_id = ? AND order_type = ? AND status = ?", 
                array($u_id, 'Buy', 0))->row()->total_eth;
            if($total_eth == null){$total_eth = 0;}
            $expected_eth = $amount * $price;
            if($buyer_wallet->activeEth < $expected_eth || $buyer_wallet->activeEth < ($total_eth + $expected_eth)){
                echo json_encode(array('error'=>'1', 'msg' => 'Your account balance is not enough.'));
                return false;
            }else{
                return true;
            }
        }else{
            $buyer_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row();
            $expected_eth = $amount * $price;
            if($buyer_wallet->activeEth < $expected_eth){
                return false;
            }else{
                return true;
            }
        }
    }
    
    //get seller wallet 
    private function get_seller_wallet($table, $u_id){
        $seller_wallet = $this->db->query("SELECT * FROM ".$table." WHERE user_id = ?", array($u_id))->row();
        return $seller_wallet;
    }
    //get buyer wallet 
    private function get_buyer_wallet($u_id){
        $buyer_wallet = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($u_id))->row();
        return $buyer_wallet;
    }
    
    //truncate value
    public function getTruncatedValue ( $value, $precision )
    {
        //$value = number_format($value, 20, '.', '');
        if($value < 0.00001){return 0;}
        //Casts provided value
        $value = ( string )$value;

        //Gets pattern matches
        preg_match( "/(-+)?\d+(\.\d{1,".$precision."})?/" , $value, $matches );

        //Returns the full pattern match
        return $matches[0];            
    }
    
}