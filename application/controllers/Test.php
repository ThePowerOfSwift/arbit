<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends MY_Controller {
    
    public function testing_1(){
        print_r(json_encode(array('index1' => 1, 'index2' => 3)));
    }
    
    public function enc_all(){
        exit;
        $a = $this->db->query("SELECT * FROM mbot_cred")->result();
        foreach($a as $b){
            $enc = $this->encrypt->encode($b->sec_key);
            $this->db->query("UPDATE mbot_cred SET sec_key = ? WHERE id = ?", array($enc, $b->id));
            echo $b->id.'<br>'; 
            //sleep(1);
        }
    }
    
    public function test_address(){
        $ch = curl_init('http://www.arbblock.com/test_beta/ip_check');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        print_r($result);
        curl_close ($ch);
    }
    
    public function testing_call(){
        header('Access-Control-Allow-Origin: *');
        $main_cur = $_GET['currency'];
        $value = floatval($_GET['value']);
        $u_id = $_GET['id'];//$this->session->userdata('u_id');
        $a = file_get_contents("http://arbitraging.net/mBOT/main1/".$value."/".$main_cur."/".$u_id);
        print_r($a);
    }
    
    
    public function get_abot_profitdetail(){
        $digit = $this->input->get('digit');
        if($digit == ''){
            echo 'digit not empty';
            exit;
        }
        $auto_re = $this->input->get('reinvest');
        if($auto_re == ''){
            echo 'reinvest status not empty';
            exit;
        }
        $users = $this->db->query("SELECT * FROM abot_wallet WHERE active >= 250  AND auto_reinvest = ? AND user_id LIKE ?", array($auto_re, '%'.$digit))->result();
        
        //get arb usd value
        $arb_in_usd = doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
        if($arb_in_usd <= 0){
            exit;
        }
        
        // get today commission value
        $d = Date('d');
        $c_value = $this->db->query("SELECT value FROM commission WHERE date = ?", array($d))->row()->value;
	    if($c_value == ''){
	        return false;
	    }
        
        $data=array();
        $sum_active = 0;
        $sum_of_total = 0;
        $sum_reinvest = 0;
        $sum_remaining = 0;
        $sum_of_total_dollor = 0;
        foreach($users as $user){
            // add profit of user
	       if($user->auto_reinvest == 1){
    	       $active_to_arb = ($user->active / $arb_in_usd);
               $today_earned = ($active_to_arb/100)*$c_value;
               $per_arb = ($today_earned / 100) * $user->auto_reinvest_per;
    	       if($user->auto_reinvest_per == 100){
    	           $remain_earned = 0;
    	       }else{
    	           $remain_earned = $today_earned - $per_arb;
    	       }
	       }else{
	           $active_to_arb = ($user->active / $arb_in_usd);
               $today_earned = ($active_to_arb/100)*$c_value;
               $per_arb = 0;
               $remain_earned = $today_earned;
               
	       }
	       $today_earned_dollor = $today_earned * $arb_in_usd;
	       
	       $sum_active = $sum_active + $user->active;
	       $sum_of_total = $sum_of_total + $today_earned;
	       $sum_reinvest = $sum_reinvest + $per_arb;
	       $sum_remaining = $sum_remaining + $remain_earned;
	       $sum_of_total_dollor = $sum_of_total_dollor + $today_earned_dollor;
	       
	       $data[] = array(
	           'user_id' => $user->user_id,
	           'active_investment' => $user->active,
	           'arb_in_usd' => $arb_in_usd,
	           'day' => $d,
	           'commission_of_day_%age' => $c_value,
	           'reinvest_status' => $user->auto_reinvest,
	           'reinvest_%age' => $user->auto_reinvest_per,
	           'today_total_profit_arb' => $today_earned,
	           'today_total_profit_$' => $today_earned_dollor,
	           'reinvest_arb' => $per_arb,
	           'remaining_arb' => $remain_earned
	           );
        }
        
        echo "<h3>Total sum of active Investment in $: ".$sum_active."</h3>";
        echo "<h3>Total sum of today profit in arb: ".$sum_of_total."</h3>";
        echo "<h3>Total sum of today profit in $: ".$sum_of_total_dollor."</h3>";
        echo "<h3>Total sum of today auto reinvest in arb: ".$sum_reinvest."</h3>";
        echo "<h3>Total sum of today remaining after auto reinvest in arb: ".$sum_remaining."</h3>";
        
        
        echo "<pre>";
        print_r($data);
    }
    
    
    
    
    
    
    public function index(){
        
        echo phpinfo();
        exit;
        
        $get_sell_orders = $this->DB2->query("SELECT * FROM orders WHERE order_type = ? and status = ? order by price", array('Sell', 0))->result();
        $get_buy_orders = $this->DB2->query("SELECT * FROM orders WHERE order_type = ? and status = ? order by price", array('Buy', 0))->result();
        
        foreach($get_buy_orders as $buy){
            foreach($get_sell_orders as $sell){
                if($sell->user_id != $buy->user_id){
                    if($sell->sell_currency_id == $buy->buy_currency_id && $buy->sell_currency_id == $sell->buy_currency_id){
                        if($sell->price == $buy->price){
                            
                            $sell_after_update = $this->DB2->query("SELECT * FROM orders WHERE order_type = ? and id = ? AND status = ? order by price", array('Sell', $sell->id, 0))->row();
                            $buy_after_update = $this->DB2->query("SELECT * FROM orders WHERE order_type = ? and id = ? AND status = ? order by price", array('Buy', $buy->id, 0))->row();
                            
                            if($sell_after_update->amount > $buy_after_update->amount){
                                $get_seller_exchangeWallet = $this->db->query("SELECT activeArb, activeEth from exchange_wallet WHERE user_id = ?", array($sell->user_id))->row();
                                $get_buyer_exchangeWallet = $this->db->query("SELECT activeArb, activeEth from exchange_wallet WHERE user_id = ?", array($buy->user_id))->row();
                                if($get_seller_exchangeWallet->activeArb >0 && $get_seller_exchangeWallet->activeArb >= $sell_after_update->amount){
                                    $buyer_expected_eth = $buy_after_update->amount * $buy_after_update->price;
                                    if($buyer_expected_eth > 0 && $get_buyer_exchangeWallet->activeEth >= $buyer_expected_eth){
                                        $get_eth_fee = $this->DB2->query("SELECT fee FROM order_fee WHERE currency_id = 2 AND status = 1 AND type='exchange' LIMIT 1")->row()->fee;
                                        $cal_eth_fee = ($buyer_expected_eth/100) * $get_eth_fee;
                                        
                                        $seller_arb = $get_seller_exchangeWallet->activeArb - $buy_after_update->amount; 
                                        $seller_eth = $get_seller_exchangeWallet->activeEth + ($buyer_expected_eth - $cal_eth_fee); 
                                        
                                        $this->db->query("UPDATE exchange_wallet SET activeArb = ?, activeEth = ? WHERE user_id = ?", array($seller_arb, $seller_eth, $sell_after_update->user_id));
                                        
                                       
                                        
                                        $get_arb_fee = $this->DB2->query("SELECT fee FROM order_fee WHERE currency_id = 1 AND status = 1 AND type='exchange' LIMIT 1")->row()->fee;
                                        $cal_arb_fee = ($buy_after_update->amount/100) * $get_arb_fee;
                                        
                                        $buyer_arb = $get_buyer_exchangeWallet->activeArb + ($buy_after_update->amount - $cal_arb_fee);
                                        $buyer_eth = $get_buyer_exchangeWallet->activeEth - $buyer_expected_eth;
                                        
                                        $this->db->query("UPDATE exchange_wallet SET activeArb = ?, activeEth = ? WHERE user_id = ?", array($buyer_arb, $buyer_eth, $buy_after_update->user_id));
                                        
                                        
                                        $this->DB2->query("INSERT INTO orders (user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id, status, order_id) values (?,?,?,?,?,?,?,?,?)", 
                                        array($sell_after_update->user_id, 'Sell', $sell_after_update->sell_currency_id, $sell_after_update->buy_currency_id, $buy_after_update->amount, $sell_after_update->price, $sell_after_update->fee_id, 1, $buy_after_update->id ));
                                       
                                        $insert_id = $this->DB2->insert_id();
                                        $this->DB2->query("UPDATE orders SET status = ?, order_id = ? WHERE id = ?", array(1,$insert_id,$buy_after_update->id));
                                        
                                       
                                        $sell_remain_amount = $sell_after_update->amount - $buy_after_update->amount;
                                        $this->DB2->query("UPDATE orders SET amount = ? WHERE id = ?", array($sell_remain_amount,$sell_after_update->id));
                                        
                                        //update admin wallet
                                        $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                                        $admin_updated_arb = $get_admin_wallet->arb + $cal_arb_fee;
                                        $admin_updated_eth = $get_admin_wallet->eth + $cal_eth_fee;
                                        $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_updated_arb, $admin_updated_eth, 1));
                                        
                                        // logs of seller
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($sell_after_update->user_id,'exchangeWallet_SellARB','-'.$buy_after_update->amount, $insert_id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($sell_after_update->user_id,'exchangeWallet_BuyETH',$buyer_expected_eth, $insert_id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($sell_after_update->user_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $insert_id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($sell_after_update->user_id,'adminwallet_feeAdd_ETH',$cal_eth_fee, $insert_id));
                                        //logs of buyer
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($buy_after_update->user_id,'exchangeWallet_SellETH','-'.$buyer_expected_eth, $buy_after_update->id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($buy_after_update->user_id,'exchangeWallet_BuyARB',$buy_after_update->amount, $buy_after_update->id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($buy_after_update->user_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $buy_after_update->id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($buy_after_update->user_id,'adminwallet_feeAdd_ARB',$cal_arb_fee, $buy_after_update->id));
                                        
                                        echo "$buy_after_update->id Buy Order completed<br>";
                                        
                                    }else{
                                        echo "Buyer Eth not enough to trade in exchange wallet<br>";
                                    }
                                }else{
                                    echo "Seller ARB not enough to trade in exchange wallet<br>";
                                }
                                
                                
                            }
                            else if($sell_after_update->amount < $buy_after_update->amount){
                                
                                $get_seller_exchangeWallet = $this->db->query("SELECT activeArb, activeEth from exchange_wallet WHERE user_id = ?", array($sell_after_update->user_id))->row();
                                $get_buyer_exchangeWallet = $this->db->query("SELECT activeArb, activeEth from exchange_wallet WHERE user_id = ?", array($buy_after_update->user_id))->row();
                                if($get_seller_exchangeWallet->activeArb >0 && $get_seller_exchangeWallet->activeArb >= $sell_after_update->amount){
                                    $buyer_expected_eth = $sell_after_update->amount * $sell_after_update->price;
                                    if($get_buyer_exchangeWallet->activeEth >= $buyer_expected_eth){
                                        
                                        $get_eth_fee = $this->DB2->query("SELECT fee FROM order_fee WHERE currency_id = 2 AND status = 1 AND type='exchange' LIMIT 1")->row()->fee;
                                        $cal_eth_fee = ($buyer_expected_eth/100) * $get_eth_fee;
                                        
                                        $seller_arb = $get_seller_exchangeWallet->activeArb - $sell_after_update->amount; 
                                        $seller_eth = $get_seller_exchangeWallet->activeEth + ($buyer_expected_eth - $cal_eth_fee); 
                                       
                                        $this->db->query("UPDATE exchange_wallet SET activeArb = ?, activeEth = ? WHERE user_id = ?", array($seller_arb, $seller_eth, $sell_after_update->user_id));
                                        
                                        $get_arb_fee = $this->DB2->query("SELECT fee FROM order_fee WHERE currency_id = 1 AND status = 1 AND type='exchange' LIMIT 1")->row()->fee;
                                        $cal_arb_fee = ($buy_after_update->amount/100) * $get_arb_fee;
                                        
                                        $buyer_arb = $get_buyer_exchangeWallet->activeArb + ($sell_after_update->amount - $cal_arb_fee);
                                        $buyer_eth = $get_buyer_exchangeWallet->activeEth - $buyer_expected_eth;
                                        $this->db->query("UPDATE exchange_wallet SET activeArb = ?, activeEth = ? WHERE user_id = ?", array($buyer_arb, $buyer_eth, $buy_after_update->user_id));
                                        
                                        $this->DB2->query("INSERT INTO orders (user_id, order_type, sell_currency_id, buy_currency_id, amount, price, fee_id, status, order_id) values (?,?,?,?,?,?,?,?,?)", 
                                        array($buy_after_update->user_id, 'Buy', $buy_after_update->sell_currency_id, $buy_after_update->buy_currency_id, $sell_after_update->amount, $buy_after_update->price, $buy_after_update->fee_id, 1, $sell_after_update->id ));
                                        
                                        $insert_id = $this->DB2->insert_id();
                                        $this->DB2->query("UPDATE orders SET status = ?, order_id = ? WHERE id = ?", array(1,$insert_id,$sell_after_update->id));
                                        
                                        $buy_remain_amount = $buy_after_update->amount - $sell_after_update->amount;
                                        $this->DB2->query("UPDATE orders SET amount = ? WHERE id = ?", array($buy_remain_amount,$buy_after_update->id));
                                        
                                        //update admin wallet
                                        $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                                        $admin_updated_arb = $get_admin_wallet->arb + $cal_arb_fee;
                                        $admin_updated_eth = $get_admin_wallet->eth + $cal_eth_fee;
                                        $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_updated_arb, $admin_updated_eth, 1));
                                        
                                        //logs of seller
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($sell->user_id,'exchangeWallet_SellARB','-'.$sell_after_update->amount, $sell->id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($sell->user_id,'exchangeWallet_BuyETH',$buyer_expected_eth, $sell->id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($sell->user_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $sell->id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($sell->user_id,'adminWallet_feeAdd_ETH',$cal_eth_fee, $sell->id));
                                        //logs of buyer 
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($buy->user_id,'exchangeWallet_SellETH','-'.$buyer_expected_eth, $insert_id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($buy->user_id,'exchangeWallet_BuyARB',$sell_after_update->amount, $insert_id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($buy->user_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee, $insert_id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($buy->user_id,'adminWallet_feeAdd_ARB',$cal_arb_fee, $insert_id));
                                        
                                        echo "$sell->id Sell Order completed<br>";
                                        
                                    }else{
                                        echo "Buyer Eth not enough to trade in exchange wallet<br>";
                                    }
                                }else{
                                    echo "Seller ARB not enough to trade in exchange wallet<br>";
                                }
                                
                            }
                            else if($sell_after_update->amount == $buy_after_update->amount){
                                
                                $get_seller_exchangeWallet = $this->db->query("SELECT activeArb, activeEth from exchange_wallet WHERE user_id = ?", array($sell->user_id))->row();
                                $get_buyer_exchangeWallet = $this->db->query("SELECT activeArb, activeEth from exchange_wallet WHERE user_id = ?", array($buy->user_id))->row();
                                if($get_seller_exchangeWallet->activeArb >0 && $get_seller_exchangeWallet->activeArb >= $sell_after_update->amount){
                                    $buyer_expected_eth = $sell_after_update->amount * $sell_after_update->price;
                                    if($get_buyer_exchangeWallet->activeEth >= $buyer_expected_eth){
                                        $get_eth_fee = $this->DB2->query("SELECT fee FROM order_fee WHERE currency_id = 2 AND status = 1 AND type='exchange' LIMIT 1")->row()->fee;
                                        $cal_eth_fee = ($buyer_expected_eth/100) * $get_eth_fee;
                                        
                                        $seller_arb = $get_seller_exchangeWallet->activeArb - $sell_after_update->amount; 
                                        $seller_eth = $get_seller_exchangeWallet->activeEth + ($buyer_expected_eth - $cal_eth_fee); 
                                        $this->db->query("UPDATE exchange_wallet SET activeArb = ?, activeEth = ? WHERE user_id = ?", array($seller_arb, $seller_eth, $sell->user_id));
                                       
                                        $get_arb_fee = $this->DB2->query("SELECT fee FROM order_fee WHERE currency_id = 1 AND status = 1 AND type='exchange' LIMIT 1")->row()->fee;
                                        $cal_arb_fee = ($buy_after_update->amount/100) * $get_arb_fee;
                                        
                                        $buyer_arb = $get_buyer_exchangeWallet->activeArb + ($sell_after_update->amount - $cal_arb_fee);
                                        $buyer_eth = $get_buyer_exchangeWallet->activeEth - $buyer_expected_eth;
                                        $this->db->query("UPDATE exchange_wallet SET activeArb = ?, activeEth = ? WHERE user_id = ?", array($buyer_arb, $buyer_eth, $buy->user_id));
                                        
                                       
                                        $this->DB2->query("UPDATE orders SET status = ?, order_id = ? WHERE id = ?", array(1,$sell->id,$buy->id));
                                        $this->DB2->query("UPDATE orders SET status = ?, order_id = ? WHERE id = ?", array(1,$buy->id,$sell->id));
                                        echo "$sell->id Sell &amp; $buy->id Buy both completed<br>";
                                        
                                        //update admin wallet
                                        $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                                        $admin_updated_arb = $get_admin_wallet->arb + $cal_arb_fee;
                                        $admin_updated_eth = $get_admin_wallet->eth + $cal_eth_fee;
                                        $this->db->query("UPDATE admin_wallet SET arb = ?, eth = ? WHERE id = ?", array($admin_updated_arb, $admin_updated_eth, 1));
                                        
                                        //logs of seller
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($sell->user_id,'exchangeWallet_SellARB','-'.$buy_after_update->amount, $sell->id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($sell->user_id,'exchangeWallet_BuyETH',$buyer_expected_eth, $sell->id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($sell->user_id,'exchangeTrade_fee_ETH','-'.$cal_eth_fee, $sell->id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($sell->user_id,'adminwallet_feeAdd_ETH',$cal_eth_fee, $sell->id));
                                        //logs of buyer
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($buy->user_id,'exchangeWallet_SellETH','-'.$buyer_expected_eth,$buy->id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($buy->user_id,'exchangeWallet_BuyARB',$buy_after_update->amount,$buy->id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($buy->user_id,'exchangeTrade_fee_ARB','-'.$cal_arb_fee,$buy->id));
                                        $this->db->query("INSERT into wallet_logs (user_id, type, value, order_id) values (?,?,?,?)", array($buy->user_id,'adminwallet_feeAdd_ARB',$cal_arb_fee,$buy->id));
                                        
                                                
                                    }else{
                                        echo "Buyer Eth not enough to trade in exchange wallet<br>";
                                    }
                                }else{
                                    echo "Seller ARB not enough to trade in exchange wallet<br>";
                                }
                                
                               
                            }
                            
                        }
                        else{
                           // echo "price not matched<br>";
                        }
                    }else{
                        //echo "no currency match<br>";
                    }
                }else{
                    //echo "Same User<br>";
                }
            }
        }
    }
    
    
    // database backup 
public function db_backup(){
   // error_reporting(-1);
        ini_set('memory_limit', '-1');
        $timee = date("Y-m-d H:i:s");
        $prefs = array(
                'filename' => 'backup_platform.sql'
            );
        $this->load->dbutil();
        $backup = $this->dbutil->backup($prefs);
        $this->load->helper('file');
        write_file('/home/arbitrage/public_html/backup_db/platform/backup_platform('.$timee.').gz', $backup);
        
        $prefs = array(
                'filename' => 'backup_exchange.sql'
            );
        $db222 = $this->load->dbutil($this->DB2, TRUE);
        $backup = $db222->backup($prefs);
        $this->load->helper('file');
        write_file('/home/arbitrage/public_html/backup_db/exchange/backup_exchange('.$timee.').gz', $backup);
        echo "Donee";
        
    }

    public function ip_addressss() 
    {
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
 
        print_r($ipaddress); 
    }
    
    
   /* public function test2(){
       $str = $this->input->get('str');
       
       $encode = $this->encrypt->encode($str);
       echo $encode."<br>";
       
       $decode = $this->encrypt->decode($encode);
       echo $decode."<br>";
       
       
    }*/
    
    
  /*  public function test3(){
        $tokens = $this->db->query("SELECT * FROM system_wallet")->result();
        foreach($tokens as $key => $token){
            $this->db->query("INSERT into exchange_wallet (user_id, activeArb, activeEth)values(?,?,?)", array($token->user_id,0,0));
            
            echo $key." DONE ".$token->user_id."<br>";                          
            
        }
    }
    
    */
    public function remove_u_wallets(){
        $u_wallets = $this->db->query("SELECT u_wallet, COUNT(*) FROM users GROUP BY u_wallet HAVING COUNT(*) > 1")->result();
        print_r($u_wallets);
        exit;
        foreach($u_wallets as $key => $u_wallet){
            $this->db->query("UPDATE users SET u_wallet = ? WHERE u_wallet = ?", array(null, $u_wallet->u_wallet));
        }
    } 
    
    public function shift_data(){
        $stop_abot_data = $this->db->query("SELECT * FROM stop_abot_data WHERE wallet = ?", array('Earned Wallet'))->result();

        foreach($stop_abot_data as $one){
            $user_ex_earned_data = $this->db->query("SELECT activeArb FROM exchange_earned_wallet WHERE user_id = ".$one->user_id." AND activeArb > 0")->row()->activeArb;
            
            if($user_ex_earned_data >= $one->value){
                // update to ex_earned wallet
                $updated_value = $user_ex_earned_data - $one->value;
                echo "user_id = ".$one->user_id." --- ".$updated_value." --- ".$one->value."<br>";
                $this->db->query("UPDATE exchange_earned_wallet SET activeArb = ? WHERE user_id = ?", array($updated_value, $one->user_id));
                
                // update to stop abot wallet
                if($user_stop_abot_wallet_arb = $this->db->query("SELECT activeArb FROM stop_abot_wallet WHERE user_id = ".$one->user_id)->row()->activeArb){
                    
                    $updated_arb = $user_stop_abot_wallet_arb + $one->value;
                    $this->db->query("UPDATE stop_abot_wallet SET activeArb = ? WHERE user_id = ?", array($updated_arb, $one->user_id));
                    
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($one->user_id,'ex_earnedWallet_ARB', '-'.$one->value, "Debit Arb from exchange earned to stop abot wallet", $user_ex_earned_data));
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($one->user_id,'stop_abotWallet_ARB', $one->value, "Credit Arb from exchange earned to stop abot wallet", $user_stop_abot_wallet_arb));

                }
                else{
                    $this->db->query("INSERT into stop_abot_wallet (user_id, activeArb) values (?,?)", array($one->user_id, $one->value));
                    
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($one->user_id,'ex_earnedWallet_ARB', '-'.$one->value, "Debit Arb from exchange earned to stop abot wallet", $user_ex_earned_data));
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($one->user_id,'stop_abotWallet_ARB', $one->value, "Credit Arb from exchange earned to stop abot wallet", 0));
                }
            }
            else{
                // update to ex_earned wallet
                echo "user_id = ".$one->user_id." --- ".$user_ex_earned_data."<br>";
                $this->db->query("UPDATE exchange_earned_wallet SET activeArb = ? WHERE user_id = ?", array(0, $one->user_id));
                
                // update to stop abot wallet
                if($user_stop_abot_wallet_arb = $this->db->query("SELECT activeArb FROM stop_abot_wallet WHERE user_id = ".$one->user_id)->row()->activeArb){
                    
                    $updated_arb = $user_stop_abot_wallet_arb + $user_ex_earned_data;
                    $this->db->query("UPDATE stop_abot_wallet SET activeArb = ? WHERE user_id = ?", array($updated_arb, $one->user_id));
                    
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($one->user_id,'ex_earnedWallet_ARB', '-'.$user_ex_earned_data, "Debit Arb from exchange earned to stop abot wallet", $user_ex_earned_data));
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($one->user_id,'stop_abotWallet_ARB', $user_ex_earned_data, "Credit Arb from exchange earned to stop abot wallet", $user_stop_abot_wallet_arb));

                }
                else{
                    $this->db->query("INSERT into stop_abot_wallet (user_id, activeArb) values (?,?)", array($one->user_id, $user_ex_earned_data));
                    
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($one->user_id,'ex_earnedWallet_ARB', '-'.$user_ex_earned_data, "Debit Arb from exchange earned to stop abot wallet", $user_ex_earned_data));
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($one->user_id,'stop_abotWallet_ARB', $user_ex_earned_data, "Credit Arb from exchange earned to stop abot wallet", 0));
                }
            }
        }
    }
    
    
}