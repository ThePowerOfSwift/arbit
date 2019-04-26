<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Amount extends MY_Controller {
	
	//(16-05-2018) = 0x478D863DA7768a45c0F0BD8aC3fb25DF3Ba9903F
	//public $deposit_address = '0x29AA9730B11950A311FEbe0a566AFA2DbD804BDB';
	public $deposit_address = '0x6705120db9Fb682deC120cDEcC8220385D25fC50';
	
	
	
	
	public function send_abot_price(){
	    $u_id = $this->session->userdata('u_id');
	    if($u_id != ''){
	        $stop_abot_data = $this->db->query("SELECT sum(value*price) as usd_sum, sum(value) as amount_sum FROM stop_abot_data where user_id = ?", array($u_id))->row();
	        if($stop_abot_data->amount_sum > 0){
    	        $usd_avg_price = $stop_abot_data->usd_sum / $stop_abot_data->amount_sum;
    	        $usd_avg_price = $this->getTruncatedValue($usd_avg_price, 3);
	        }else{
	            $usd_avg_price = 0;
	        }
	        if($usd_avg_price <= 0){
	            $usd_avg_price = 0;
	        }
	        $abot_arb = file_get_contents('https://arbitraging.co/platform/abot_arb');
	        $abot_arb = $this->getTruncatedValue($abot_arb, 3);
	        $arr = array('success'=>'1', 'avg_price' => $usd_avg_price, 'abot_usd' => $abot_arb);
	        echo json_encode($arr); exit;
	    }else{
	        echo json_encode(array('error'=>'1', 'msg'=>'Session Expired')); exit;
	    }
	}
	
	public function get_support_pin(){
	    $u_id = $this->session->userdata('u_id');
	    if($u_id != ''){
	        if($res = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row()){
	            echo $res->support_pin;
	            exit;
	        }else{
	            echo "no record exist";
	            exit;
	        }
	    }
	}
	
	
	

    // send withdraw
    
    public function sendWithdraw(){
        if($this->db->query("SELECT * FROM admin_locks WHERE name = 'withdraw_lock'")->row()->lock_status == 1){
            echo json_encode(array('error'=>'1', 'msg' => 'Withdraws are Locked by admin. Please try again later')); exit; 
        }
        
        $no_withdraw_limit = array(78572, 80394);
        $users_array = array(); //array(79741,16748,26337,53088,95,81733,78513,79333,79389,79522,57991,83436,83299,69899,69975);
        $data = $this->db->query("SELECT user_id from suspected_users WHERE status = 1")->result();   //search with user ID
        $suspected_users_array = array();
        if(isset($data)){
            foreach($data as $suspected_user){
                $suspected_users_array[] = $suspected_user->user_id;  
            }
        }
        
        $u_id = $this->session->userdata('u_id');
        if($u_id == ''){echo json_encode(array('error'=>'1', 'msg'=>'Session expired')); exit;}
        $currency = $this->input->post('currency');
	    $google_code = $this->input->post('google_code');
	    if($google_code == ''){
	        echo json_encode(array('error'=>'1', 'msg'=>'Please enter your 2FA code.')); exit;
	    }else{
	        $this->load->library('GoogleAuthenticator');
            $ga = new GoogleAuthenticator();
	        
	        $get_code = $this->db->query("SELECT code FROM users WHERE u_id = ?", array($u_id))->row()->code;
	        $secret = $this->encrypt->decode($get_code);
	        // $secret = hash('sha256', $secret_key);
	        
            $checkResult = $ga->verifyCode($secret, $google_code, 2);    // 2 = 2*30sec clock tolerance
            if (!$checkResult) {
                echo json_encode(array('error'=>'1', 'msg'=>'Sorry your 2FA not matched.')); exit;
            }
	    }
	    
	    if($currency == ''){
	        echo json_encode(array('error'=>'1', 'msg'=>'Please select surrency')); exit;
	    }
	    
	     $withdraw_status = $this->db->query("select withdraw_status from users WHERE u_id = ?", array($u_id))->row()->withdraw_status;
	     if($withdraw_status == 0){
	         echo json_encode(array('error'=>'1', 'msg'=>'Only one withdraw request is allowed at a time')); exit;
	     }
	            $u_email = $this->session->userdata('u_email');
        	    
        	    $wallet = $this->db->query("SELECT u_wallet FROM users WHERE u_id = ?", array($u_id))->row()->u_wallet;
        	    //$this->input->post("wallet");
        	    if(strlen($wallet) > 42 || strlen($wallet) < 42){
        	        echo json_encode(array('error'=>'1', 'msg'=>'Your wallet is not valid. Please check your registered wallet')); exit;
        	    }
        		$earnednew = doubleval($this->input->post("amount"));
        		
        	    $sql = "SELECT * FROM system_wallet WHERE user_id = $u_id";
                $data = $this->db->query($sql)->row();
        	   	
        		
        		if($currency == 'ARB'){
        		    $earned = $data->activeArb;
        		    if($earnednew < 5 || $earnednew > $earned){
            		    echo json_encode(array('error'=>'1', 'msg'=>'Minmum amount is 5 ARB or You dont have sufficient balance in wallet.')); exit;
            		}
            		else if($earnednew >= 5){
            		    // per day arb withdraw limit
            		    $before_24_db = date('Y-m-d H:i:s', strtotime('-28 hour'));
            		    $tot_sum = $this->db->query("SELECT sum(value) as tot_sum FROM withdraw_request WHERE user_id = ? AND currency = ? AND create_time > ? AND transection_id != ?", array($u_id, 'ARB', $before_24_db, 'Reverted'))->row();
            		    $crunt_befor = $tot_sum->tot_sum + $earnednew;
            		    if($crunt_befor > 10000){
            		        echo json_encode(array('error'=>'1', 'msg'=>'Your daily withdraw limit is 10000 ARB')); exit;
            		    }
            		    
            		    $get_fee = $this->DB2->query("SELECT fee from order_fee WHERE currency_id = 1 AND status = 1 AND type = 'withdraw' LIMIT 1")->row()->fee;
            		   
            		    $ew_update_time = $this->db->query("SELECT ew_status FROM users WHERE u_id = ?", array($u_id))->row()->ew_status;
            		    $time_24hour_before = date("Y-m-d H:i:s", strtotime("-28 hour"));
            		    $flag = 0;
            		    if(strtotime($ew_update_time) > strtotime($time_24hour_before)){
            		        $flag = 1;
            		        //flag = 1
            		        $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id,'systemWallet_ArbWithdraw','-'.$earnednew, 'User Request for ARB Withdraw (Pending)', $data->activeArb));
                            $insert_id = $this->db->insert_id();
                		    
                		    $this->db->query("INSERT INTO withdraw_request (user_id, wallet, value, currency, status, fee, log_id, approvel_flag, comment)VALUES(?,?,?,?,?,?,?,?,?)",array($u_id, $wallet, $earnednew, 'ARB', 0, $get_fee, $insert_id, $flag, "Credentials Changed"));
            		    }
            		    else{
                		    //pending log //flag = 0
                		    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id,'systemWallet_ArbWithdraw','-'.$earnednew, 'User Request for ARB Withdraw (Pending)', $data->activeArb));
                            $insert_id = $this->db->insert_id();
                		    
                		    $this->db->query("INSERT INTO withdraw_request (user_id, wallet, value, currency, status, fee, log_id, approvel_flag)VALUES(?,?,?,?,?,?,?,?)",array($u_id, $wallet, $earnednew, 'ARB', 0, $get_fee, $insert_id, $flag));
            		    }
            		    
            		    
            		    $get_systemwallet_arb = $this->db->query("SELECT activeArb FROM system_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
                        $updated_arb = $get_systemwallet_arb - $earnednew;
                        $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($updated_arb, $u_id));
            		    
            		    $this->db->query("update users set withdraw_status = 0 WHERE u_id = ?", array($u_id));
            		   
            		    echo json_encode(array('success'=>'1', 'msg'=>'Withdraw Request Generated Successfully.')); exit;
            		}
            		else{
            		    echo json_encode(array('error'=>'1', 'msg'=>'Something went wrong.')); exit;
            		}

        		}else if($currency == 'ETH'){
        		    $earned = $data->activeEth;
        		    if(!in_array($u_id, $no_withdraw_limit) && $earnednew > 100){
        		       echo json_encode(array('error'=>'1', 'msg'=>'Maximum ETH withdraw limit is 100.')); exit;
        		    }
        		    
        		    if($earnednew < 0.05 || $earnednew > $earned){
            		    echo json_encode(array('error'=>'1', 'msg'=>'Minimum ETH withdraw limit is 0.05 or You dont have sufficient balance in wallet.')); exit;
            		}	
            		else{
            		    $get_fee = $this->DB2->query("SELECT fee from order_fee WHERE currency_id = 2 AND status = 1 AND type = 'withdraw' LIMIT 1")->row()->fee;
            		    
            		    $ew_update_time = $this->db->query("SELECT ew_status FROM users WHERE u_id = ?", array($u_id))->row()->ew_status;
            		    $time_24hour_before = date("Y-m-d H:i:s", strtotime("-28 hour"));
            		    
            		    $get_systemwallet_eth = $this->db->query("SELECT activeEth FROM system_wallet WHERE user_id = ?", array($u_id))->row()->activeEth;
                        $updated_eth = $get_systemwallet_eth - $earnednew;
                        $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($updated_eth, $u_id));
                        
            		    
            		    $flag = 0;
            		    
            		    
            		  //  if(strtotime($ew_update_time) > strtotime($time_24hour_before) || in_array($u_id , $users_array) || in_array($u_id , $suspected_users_array)){
            		      if(strtotime($ew_update_time) > strtotime($time_24hour_before)){
            		        $flag = 1;
                            //flag = 1
                		    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id,'systemWallet_EthWithdraw','-'.$earnednew, 'User Request for ETH Withdraw (Pending)', $data->activeEth));
                            $insert_id = $this->db->insert_id();
                            
                		    $this->db->query("INSERT INTO withdraw_request (user_id, wallet, value, currency, status, fee, log_id, approvel_flag, comment)VALUES(?,?,?,?,?,?,?,?,?)",array($u_id, $wallet, $earnednew, 'ETH', 0, $get_fee, $insert_id, $flag, "Credentials Changed"));
            		    
            		    }
            		    else{
                		    //pending log //flag = 0
                		    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id,'systemWallet_EthWithdraw','-'.$earnednew, 'User Request for ETH Withdraw (Pending)', $data->activeEth));
                            $insert_id = $this->db->insert_id();
                            
                		    $this->db->query("INSERT INTO withdraw_request (user_id, wallet, value, currency, status, fee, log_id, approvel_flag)VALUES(?,?,?,?,?,?,?,?)",array($u_id, $wallet, $earnednew, 'ETH', 0, $get_fee, $insert_id, $flag));
            		    }
            		    
            		    		    
            		    $this->db->query("update users set withdraw_status = 0 WHERE u_id = ?", array($u_id));
            		    echo json_encode(array('success'=>'1', 'msg'=>'Withdraw Request Generated Successfully.')); exit;
            		}
        		
        		}else{
        		    echo json_encode(array('error'=>'1', 'msg'=>'Something went wrong.')); exit;
        		}
    }
    
    
	
    // request for manual withdraw
    public function manual_withdraw(){
        header('Access-Control-Allow-Origin: *');
        
        $req_id = $this->input->post('request_id');
        $trans_id = $this->input->post('transection_id');
        $admin_id = $this->input->post('admin_id');
        $career = $this->input->post('career');
        // echo $req_id.' ------- '.$trans_id;
        if($req_id == ''){
            echo "request id null";
            exit;
        }
        
        if($trans_id == ''){
            echo "transection id null";
            exit;
        }
        
        $request = $this->db->query("SELECT * FROM withdraw_request WHERE id = ? AND status = ?", array($req_id , 0))->row();
        
        if($request->currency == 'ARB'){
            
            $log_amount = $request->value - $request->fee;
            if($request->log_id > 0){
                $this->db->query("UPDATE wallet_logs SET comment = ?, value = ? WHERE id = ?", array('User Request for ARB Withdraw ('.$trans_id.')', '-'.$log_amount, $request->log_id));
            }else{
                $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($request->user_id,'systemWallet_ArbWithdraw','-'.$log_amount, 'User Request for ARB Withdraw'));
            }
            $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($request->user_id,'systemWallet_ArbWithdraw_fee','-'.$request->fee,'Fee'));
            $this->db->query("INSERT into wallet_logs (user_id, type, value,comment) values (?,?,?,?)", array($request->user_id,'adminWallet_ArbWithdraw_fee',$request->fee,'Fee'));
            //update admin wallet
            $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 3 LIMIT 1")->row();
            $admin_updated_arb = $get_admin_wallet->arb + $request->fee;
            $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = ?", array($admin_updated_arb, 3));
            //transection log
            $this->db->query("INSERT INTO logs (user_id, tx_id, value, type) VALUES (?,?,?,?)", array($request->user_id, $trans_id, $request->value, 'Wallet_withdrawArb'));
            //withdraw status active
            $this->db->query("update users set withdraw_status = 1 WHERE u_id = ?", array($request->user_id));
            
                                            
        }else if($request->currency == 'ETH'){
            
            $log_amount = $request->value - $request->fee;
            if($request->log_id > 0){
                $this->db->query("UPDATE wallet_logs SET comment = ?, value = ? WHERE id = ?", array('User Request for ETH Withdraw ('.$trans_id.')', '-'.$log_amount, $request->log_id));
            }else{
                $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($request->user_id,'systemWallet_EthWithdraw','-'.$log_amount, 'User Request for ETH Withdraw'));
            }
            $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($request->user_id,'systemWallet_EthWithdraw_fee','-'.$request->fee,'Fee'));
            $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($request->user_id,'adminWallet_EthWithdraw_fee',$request->fee,"Fee"));
            //update admin wallet
            $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 3 LIMIT 1")->row();
            $admin_updated_eth = $get_admin_wallet->eth + $request->fee;
            $this->db->query("UPDATE admin_wallet SET eth = ? WHERE id = ?", array($admin_updated_eth, 3));
            //transection log
            $this->db->query("INSERT INTO logs (user_id, tx_id, value, type) VALUES (?,?,?,?)", array($request->user_id, $trans_id, $request->value, 'Wallet_withdrawEth'));
            //withdraw status active
            $this->db->query("update users set withdraw_status = 1 WHERE u_id = ?", array($request->user_id));
        }else{
            echo "not valid currency";
            exit;
        }
        
        $this->db->query("insert into admin_activity_logs (admin_id, user_id, comment) values (?,?,?)", array($admin_id, 0, 'ARB manual withdraw ('.$trans_id.')'));
        
        $this->db->query("UPDATE withdraw_request SET status = ?, transection_id = ? WHERE id = ?", array(1, $trans_id, $req_id));
        if(!isset($career)) { 
            redirect("https://www.arbblock.com/admin_panel/withdraw_requests_list_arb");                       
        }
        
        exit;
        
    }
	
	
	public function bulk_manual_withdraw(){
        header('Access-Control-Allow-Origin: *');
        
        $req_id = $this->input->get('request_id');
        $trans_id = $this->input->get('transection_id');
        //$career = $this->input->post('career');
        // echo $req_id.' ------- '.$trans_id;
        if($req_id == ''){
            echo "request id null";
            exit;
        }
        
        if($trans_id == ''){
            echo "transection id null";
            exit;
        }
        
        $request = $this->db->query("SELECT * FROM withdraw_request WHERE id = ? AND status = ?", array($req_id , 0))->row();
        
        if($request->currency == 'ARB'){
            
            $log_amount = $request->value - $request->fee;
            if($request->log_id > 0){
                $this->db->query("UPDATE wallet_logs SET comment = ?, value = ? WHERE id = ?", array('User Request for ARB Withdraw ('.$trans_id.')', '-'.$log_amount, $request->log_id));
            }else{ // WRITE ADMIN consideration note... 
                $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($request->user_id,'systemWallet_ArbWithdraw','-'.$log_amount, 'User Request for ARB Withdraw'));
            }
            $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($request->user_id,'systemWallet_ArbWithdraw_fee','-'.$request->fee,'Fee'));
            $this->db->query("INSERT into wallet_logs (user_id, type, value,comment) values (?,?,?,?)", array($request->user_id,'adminWallet_ArbWithdraw_fee',$request->fee,'Fee'));
            //update admin wallet
            $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 3 LIMIT 1")->row();
            $admin_updated_arb = $get_admin_wallet->arb + $request->fee;
            $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = ?", array($admin_updated_arb, 3));
            //transection log
            $this->db->query("INSERT INTO logs (user_id, tx_id, value, type) VALUES (?,?,?,?)", array($request->user_id, $trans_id, $request->value, 'Wallet_withdrawArb'));
            //withdraw status active
            $this->db->query("update users set withdraw_status = 1 WHERE u_id = ?", array($request->user_id));
            
                                            
        }else if($request->currency == 'ETH'){
            
            $log_amount = $request->value - $request->fee;
            if($request->log_id > 0){
                $this->db->query("UPDATE wallet_logs SET comment = ?, value = ? WHERE id = ?", array('User Request for ETH Withdraw ('.$trans_id.')', '-'.$log_amount, $request->log_id));
            }else{
                $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($request->user_id,'systemWallet_EthWithdraw','-'.$log_amount, 'User Request for ETH Withdraw'));
            }
            $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($request->user_id,'systemWallet_EthWithdraw_fee','-'.$request->fee,'Fee'));
            $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($request->user_id,'adminWallet_EthWithdraw_fee',$request->fee,"Fee"));
            //update admin wallet
            $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 3 LIMIT 1")->row();
            $admin_updated_eth = $get_admin_wallet->eth + $request->fee;
            $this->db->query("UPDATE admin_wallet SET eth = ? WHERE id = ?", array($admin_updated_eth, 3));
            //transection log
            $this->db->query("INSERT INTO logs (user_id, tx_id, value, type) VALUES (?,?,?,?)", array($request->user_id, $trans_id, $request->value, 'Wallet_withdrawEth'));
            //withdraw status active
            $this->db->query("update users set withdraw_status = 1 WHERE u_id = ?", array($request->user_id));
        }else{
            echo "not valid currency";
            exit;
        }
        
        $this->db->query("UPDATE withdraw_request SET transection_id = ? , status = ? WHERE id = ?", array($trans_id, 1, $req_id));
       // if(!isset($career)) { 
          //  redirect("https://www.arbblock.com/admin_panel/withdraw_requests_list");                       
       // }
        
       // exit;
        
    }
	
	
   
	
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
    
	public function calculate_arb_abot_price($amount, $type){
        
        $arb_value = doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
        if($arb_value > 0)
         return $arb_value;
       
    }
	
	
	public function user_select_audit(){
	    exit;
	    $u_id = $this->session->userdata('u_id');
	    if($u_id != ''){
	        $percentage = intval($this->input->post('percentage'));
	        if($percentage == 0 || $percentage == 10 || $percentage == 25 || $percentage == 33 || $percentage == 50){
	            $abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
	            if($abot_wallet->audit_per != null){
	                echo json_encode(array('error'=>'1', 'msg'=>'You already select audit option')); exit;
	            }else{
                    $now = date("Y-m-d H:i:s");
	                $this->db->query("UPDATE abot_wallet SET audit_per = ?, audit_time = ? WHERE user_id = ?", array($percentage, $now, $u_id));
	                echo json_encode(array('success'=>'1', 'msg'=>'Updated Successfully')); exit;
	            }
	            
	        }else{
	            echo json_encode(array('error'=>'1', 'msg'=>'Please select valid input value')); exit;
	        }
	    }else{
	        echo json_encode(array('error'=>'1', 'msg'=>'Session Expired')); exit;
	    }
	}
	
	
	public function abot_flag_zero(){
	    //exit;
	    echo '24_flag_cron';
	    $this->db->query("UPDATE abot_wallet SET flag_24 = 0");
	    $this->db->query("UPDATE abot_cron_ids SET flag = 0");

	    echo 'Done';
	}
	
	public function profit_abot_cc(){
	    echo 'strating cron';
	    $get_last_digit = $this->input->get('last_digit');
	    if($get_last_digit == ''){
	        echo 'please enter last digit';
	        exit;
	    }
	    $vol_24_hour = 0;
	    $get_data_24 = file_get_contents('https://www.arbitraging.co/platform/arb_valueLive');
	    if($get_data_24 != ''){
	        $get_data_24 = json_decode($get_data_24);
	        $vol_24_hour = floatval($get_data_24->vol24);
	    }
	     // decide limit on 24 hour volume
        if($vol_24_hour < 5000){
           $earning_limit = 500;
        }else if($vol_24_hour >= 5000 && $vol_24_hour < 10000){
           $earning_limit = 1000;
        }else if($vol_24_hour >= 10000){
           $earning_limit = 1500;
        }else{
           $earning_limit = 500;
        }
	    
	    // cron_id flag check
	    if($this->db->query("SELECT flag FROM abot_cron_ids WHERE cron_id = $get_last_digit")->row()->flag == 1){
	        echo $get_last_digit.'id already done';
	        exit;
	    }
	    else{
	        $this->db->query("UPDATE abot_cron_ids SET flag = 1 WHERE cron_id = $get_last_digit");
	    }
	    
	    //get arb usd value
        $arb_in_usd = doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
        if($arb_in_usd <= 0){
            exit;
        }
        
	    //update today profit to 0
        $this->db->query("UPDATE abot_wallet SET profit = 0 WHERE profit > 0 AND user_id like ?", array('%'.$get_last_digit));
	    
	    // get today commission value using current date
        $d = Date('d');
        $c_value_base = $this->db->query("SELECT value FROM commission WHERE date = ?", array($d))->row()->value;
	    if($c_value_base == ''){
	        return false;
	    }
	    
	    // get abot users for profit who have active greater then 250
	    $users_abot_profit = $this->db->query("SELECT * FROM abot_wallet WHERE active >= 250 AND user_id like ? AND flag_24 = 0", array('%'.$get_last_digit))->result();
	    foreach($users_abot_profit as $user1){
	        //if user acount is in auction
	        if($this->db->query("SELECT * FROM auction_req WHERE user_id = ?", array($user1->user_id))->row()){continue;}
	        
	        // for 10% stop abot 
	        $current_timee = date('Y-m-d H:i:s');
	        $add_48hour = date('Y-m-d 23:59:55', strtotime('+48 hours', strtotime($user1->stop_abot_time_10)));
	        if($current_timee < $add_48hour){continue;}
	        
	        
	        $c_value = $c_value_base;
	        
	        
	       // get user data again due to parent profit because of old data in loop
	       $user = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($user1->user_id))->row();
	       
	       
	       //audit user comission plus here
	       $cur_time = date('Y-m-d H:i:s');
	       //$user->audit_time
	       $plus_90_days = date('Y-m-d H:i:s', strtotime('+90 days', strtotime('2019-02-19 00:00:00')));
	       $allow_per = array(60);
	       if($cur_time <= $plus_90_days){
	           if($user->audit_per == 100){continue;}
	           
	           if(in_array($user->audit_per, $allow_per)){
	               $audit_deduct = ($c_value / 100) * $user->audit_per;
	               $c_value = $c_value - $audit_deduct;
	           }
	       }
	       
	       
	        //gas for below 100k investment
	       if($user->active < 25000){
	           $gas_per_2 = 3;
	           $cal_gas = ($c_value/100) * 3;
               $gas_percent = $cal_gas;
	       }
	       //gas for greater 100k and less 250k
	       else if($user->active >= 25000 && $user->active < 100000){
	           $gas_per_2 = 5;
	           $cal_gas = ($c_value/100) * 5;
               $gas_percent = $cal_gas;
	       }
	       //gas for greater then 250k
	       else{
	           $gas_per_2 = 7;
	           $cal_gas = ($c_value/100) * 7;
               $gas_percent = $cal_gas;
	       }
	       
	       
	       //get user vault balance
	       if($vault = $this->db->query("SELECT * FROM vault_wallet WHERE user_id = ?", array($user->user_id))->row()){
    	        $in_vault = $vault->activeArb;
    	   }else{
    	        $in_vault = 0;
    	   }
    	      
	       //gas here
	        if($user->gas < 0.1){continue;}
	        $valid_amount = $user->gas * 100;
    	    $valid_amount = $valid_amount / $gas_percent;
    	    // if gas is more than active
	        if($valid_amount >= $user->active){
        	    $active_to_arb = ($user->active / $arb_in_usd);
        	    $today_earned = ($active_to_arb/100)*$c_value;
        	    if($in_vault < 50000){
        	        if($today_earned > $earning_limit){$today_earned = $earning_limit;}
        	    }
        	    $total_earned = $user->earned + $today_earned;
        	    $gas_deduct = ($today_earned / 100) * $gas_per_2;
        	    $gas_deduct = $gas_deduct * $arb_in_usd;
        	    $new_gas = $user->gas - $gas_deduct;
        	    
        	    //echo '<br>'.$user->user_id.'-- if -- comission: '.$c_value.' -- arb_usd:'.$arb_in_usd.' -- active:'.$active_to_arb." - today_earned:".$today_earned." - total_earned:".$total_earned." - gas_deduct:".$gas_deduct." - new_gas:".$new_gas."<br>";
        	    
    	    }else{
    	        $active_to_arb = ($valid_amount / $arb_in_usd);
        	    $today_earned = ($active_to_arb/100)*$c_value;
        	    if($in_vault < 50000){
        	        if($today_earned > $earning_limit){$today_earned = $earning_limit;}
        	    }
        	    $total_earned = $user->earned + $today_earned;
        	    $gas_deduct = ($today_earned / 100) * $gas_per_2;
        	    $gas_deduct = $gas_deduct * $arb_in_usd;
        	    $new_gas = $user->gas - $gas_deduct;
        	    //echo '<br>'.$user->user_id.'-- else -- comission: '.$c_value.' -- arb_usd:'.$arb_in_usd.' -- active:'.$active_to_arb." - today_earned:".$today_earned." - total_earned:".$total_earned." - gas_deduct:".$gas_deduct." - new_gas:".$new_gas."<br>";
        	    
    	   }
    	   if($new_gas < 0.05){$new_gas = 0;}
            
    	   // add parents account profit
    	   if($today_earned > 0){
	            
	            $parent_id = $this->db->query("SELECT parent_u_id FROM child_affiliate WHERE child_u_id = ?", array($user->user_id))->row()->parent_u_id;
	            if($parent_id != '' && $parent_id != null){
	               $two_persent = ($today_earned * 0.05); // 5% from today child profit
    	           $parent_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($parent_id))->row();
    	           
    	           $newp_earned = $parent_wallet->earned + $two_persent;
    	           $aff_ern = $parent_wallet->aff_earned + $two_persent;
    	           //reinvest every one
    	           //$parent_wallet->auto_reinvest = 1;
    	           if($parent_wallet->auto_reinvest == 1){
    	                $two_persent_usd = $two_persent * $arb_in_usd;
    	                $new_active_of_parent = $parent_wallet->active + $two_persent_usd;
    	                $this->db->query("UPDATE abot_wallet SET active = ?, aff_earned = ? WHERE user_id = ?", array($new_active_of_parent, $aff_ern, $parent_id));
    	           }else{
    	                $this->db->query("UPDATE abot_wallet SET earned = ?, aff_earned = ? WHERE user_id = ?", array($newp_earned, $aff_ern, $parent_id));
    	           }
    	           $this->db->query("INSERT into wallet_logs_abot (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($parent_id, 'abotWallet_aff_profit_'.$user->user_id, $two_persent, 'Affiliate Profit - Current aBot Balance ('.$parent_wallet->active.')', $parent_wallet->earned));
	               $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($parent_id, 'abotWallet_aff_profit_'.$user->user_id, $two_persent, 'Affiliate Profit - Current aBot Balance ('.$parent_wallet->active.')', $parent_wallet->earned));
	               
				   // deduct 2% from user account to parent
	               $today_earned = $today_earned - $two_persent;
	               $total_earned = $user->earned + $today_earned;
	               
	                // exit;
	            }
	            
	        }
	        // check auto reinvest
	        //reinvest every one
	        //$user->auto_reinvest = 1;
	        //$user->auto_reinvest_per = 100;
	        if($user->auto_reinvest == 1){
	            $per_arb = ($today_earned / 100) * $user->auto_reinvest_per;
	            if($user->auto_reinvest_per == 100){
	                $remain_earned = 0;
	            }else{
	                $remain_earned = $today_earned - $per_arb;
	            }
	            
	            $arb_in_usd_re = $arb_in_usd; //$this->calculate_arb_abot_price($per_arb, 'in');
	            
	            $today_earned_usd = $per_arb * $arb_in_usd_re;
	            $auto_reinvest_ative = $user->active + $today_earned_usd;
	            $user_tot_earned = $user->earned + $remain_earned;
	            
	            
	            $this->db->query("UPDATE abot_wallet SET active = ?, profit = ?, earned = ?, flag_24 = 1, gas = ? WHERE user_id = ?", 
	                array($auto_reinvest_ative, $today_earned, $user_tot_earned, $new_gas, $user->user_id));
	                
    	        $this->db->query("INSERT into wallet_logs_abot (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($user->user_id, 'abotWallet_earned', $today_earned, "aBot active investment Profit - Day ".$d." (".$c_value."%) - aBot Price ( " .$arb_in_usd. " ) - Current aBot Balance (".$user->active.")", $user->earned));
    	        $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($user->user_id, 'abotWallet_earned', $today_earned, "aBot active investment Profit - Day ".$d." (".$c_value."%) - aBot Price ( " .$arb_in_usd. " ) - Current aBot Balance (".$user->active.")", $user->earned));
    	        $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($user->user_id, 'abotWallet_gas', '-'.$gas_deduct, "aBot gas deducted - Day ".$d." (".$gas_percent."%)", $user->gas));
	        
	            
	           
	        }else{
	            $this->db->query("UPDATE abot_wallet SET profit = ?, earned = ?, flag_24 = 1, gas = ? WHERE user_id = ?", 
	                array($today_earned, $total_earned, $new_gas, $user->user_id));
	            $this->db->query("INSERT into wallet_logs_abot (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($user->user_id, 'abotWallet_earned', $today_earned, "aBot active investment Profit - Day ".$d." (".$c_value."%) - aBot Price ( " .$arb_in_usd. " ) - Current aBot Balance (".$user->active.")", $user->earned));
    	        $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($user->user_id, 'abotWallet_earned', $today_earned, "aBot active investment Profit - Day ".$d." (".$c_value."%) - aBot Price ( " .$arb_in_usd. " ) - Current aBot Balance (".$user->active.")", $user->earned));
    	        $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($user->user_id, 'abotWallet_gas', '-'.$gas_deduct, "aBot gas deducted - Day ".$d." (".$gas_percent."%)", $user->gas));
    	        
	           
	        }
	        
	            
	        
	          echo $user->user_id.' -- '.$arb_in_usd.' -- '.$active_to_arb." - ".$today_earned." - ".$total_earned."<br>";
	        
	        
	    }
	    
	    echo "<br><br><h3>Pending to active</h3><br><br>";
	    // get abot users for pending to active
	    $users_abot_pending = $this->db->query("SELECT * FROM abot_wallet WHERE pending >= 250 AND user_id like ?", array('%'.$get_last_digit))->result();
	    foreach($users_abot_pending as $user){
	        $now_active = $user->active + $user->pending;
	        $pending = 0;
	        
	        $time_p =  date("Y-m-d H:i:s");
	        $this->db->query("UPDATE abot_wallet SET pending = ?, active = ?, pending_date = ? WHERE user_id = ?", array(0, $now_active,$time_p, $user->user_id));
	        
	        $this->db->query("INSERT into wallet_logs_abot (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($user->user_id, 'abotWallet_pending', '-'.$user->pending, "aBot pending to active investment", $user->pending));
    	    $this->db->query("INSERT into wallet_logs_abot (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($user->user_id, 'abotWallet_active', $user->pending, "aBot pending to active investment", $user->active));
	        
			$this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($user->user_id, 'abotWallet_pending', '-'.$user->pending, "aBot pending to active investment", $user->pending));
    	    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($user->user_id, 'abotWallet_active', $user->pending, "aBot pending to active investment", $user->active));
	        
			echo $user->user_id." -- ".$user->pending.' -- '.$user->active."<br>";
	    }
	    
	    
	    echo 'done';
	 	exit;
	 	
	 	//$this->db->query("UPDATE cron_status SET value = 0 WHERE name = 'abot_cron'");
	 	
	}
	

    
    // cron of hold arbs
    public function hold_arbs(){
        //echo 'working'; exit;
        echo '.dfsdf.';
        if($holds = $this->db->query("SELECT * FROM arb_deposit_hold WHERE status = 0")->result()){
            foreach($holds as $hold){
                $before24 = date("Y-m-d H:i:s", strtotime('-28 hour'));
                if($before24 >= $hold->created_at){
                    if($get_external_wallet = $this->db->query("Select * from system_wallet WHERE user_id = ?", array($hold->user_id))->row()){
                        $new_active_external_wallet = $get_external_wallet->activeArb + $hold->value;
                        $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($new_active_external_wallet, $hold->user_id));
                        $last_blnc = $get_external_wallet->activeArb;
                    }else{
                        $this->db->query("INSERT INTO system_wallet (user_id, activeArb) values (?,?)", array($hold->user_id, $hold->value));
                        $last_blnc = 0;
                    }
                    $this->db->query("UPDATE wallet_logs SET comment = ?, last_blnc = ? WHERE id = ?", array('User Deposit ARB txt_id = '.$hold->txid, $last_blnc, $hold->log_id));
                    $this->db->query("UPDATE arb_deposit_hold SET status = ? WHERE id = ?", array(1, $hold->id));
                    echo "Done".$hold->id."<br>";
                }
            }
        }
    }
    
    
    
    public function sendWithdraw_external(){
        if($this->db->query("SELECT * FROM admin_locks WHERE name = 'withdraw_lock'")->row()->lock_status == 1){
            echo json_encode(array('error'=>'1', 'msg' => 'Withdraws are Locked by admin. Please try again later')); exit; 
        }
        
        $u_id = $this->session->userdata('u_id');
        if($u_id == ''){echo json_encode(array('error'=>'1', 'msg'=>'Session expired')); exit;}
        $currency = 'ARB';
	    $google_code = $this->input->post('google_code');
	    if($google_code == ''){
	        echo json_encode(array('error'=>'1', 'msg'=>'Please enter your 2FA code.')); exit;
	    }else{
	        $this->load->library('GoogleAuthenticator');
            $ga = new GoogleAuthenticator();
	        
	        $get_code = $this->db->query("SELECT code FROM users WHERE u_id = ?", array($u_id))->row()->code;
	        $secret = $this->encrypt->decode($get_code);
	        // $secret = hash('sha256', $secret_key);
	        
            $checkResult = $ga->verifyCode($secret, $google_code, 2);    // 2 = 2*30sec clock tolerance
            if (!$checkResult) {
                echo json_encode(array('error'=>'1', 'msg'=>'Sorry your 2FA not matched.')); exit;
            }
	    }
	    
	    if($currency == ''){
	        echo json_encode(array('error'=>'1', 'msg'=>'Please select currency')); exit;
	    }
	    
	     $withdraw_status = $this->db->query("select withdraw_status from users WHERE u_id = ?", array($u_id))->row()->withdraw_status;
	     if($withdraw_status == 0){
	         echo json_encode(array('error'=>'1', 'msg'=>'Only one withdraw request is allowed at a time')); exit;
	     }
	            $u_email = $this->session->userdata('u_email');
        	    
        	    $wallet = $this->db->query("SELECT u_wallet FROM users WHERE u_id = ?", array($u_id))->row()->u_wallet;
        	    //$this->input->post("wallet");
        	    if(strlen($wallet) > 42 || strlen($wallet) < 42){
        	        echo json_encode(array('error'=>'1', 'msg'=>'Your wallet is not valid. Please check your registered wallet')); exit;
        	    }
        		$earnednew = doubleval($this->input->post("amount"));
        		
        	    $sql = "SELECT * FROM external_wallet WHERE user_id = $u_id";
                $data = $this->db->query($sql)->row();
        	   	
        		
        		if($currency == 'ARB'){
        		    $earned = $data->activeArb;
        		    if($earnednew < 5 || $earnednew > $earned){
            		    echo json_encode(array('error'=>'1', 'msg'=>'Minmum amount is 5 ARB or You dont have sufficient balance in wallet.')); exit;
            		}
            		else if($earnednew >= 5){
            		    $get_fee = $this->DB2->query("SELECT fee from order_fee WHERE currency_id = 1 AND status = 1 AND type = 'withdraw' LIMIT 1")->row()->fee;
            		   
            		    $ew_update_time = $this->db->query("SELECT ew_status FROM users WHERE u_id = ?", array($u_id))->row()->ew_status;
            		    $time_24hour_before = date("Y-m-d H:i:s", strtotime("-28 hour"));
            		    $flag = 0;
            		    if(strtotime($ew_update_time) > strtotime($time_24hour_before)){
            		        $flag = 1;
            		        //flag = 1
            		        $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id,'externalWallet_ArbWithdraw','-'.$earnednew, 'User Request for ARB Withdraw (Pending)', $data->activeArb));
                            $insert_id = $this->db->insert_id();
                		    
                		    $this->db->query("INSERT INTO withdraw_request (user_id, wallet, value, currency, status, fee, log_id, approvel_flag, comment)VALUES(?,?,?,?,?,?,?,?,?)",array($u_id, $wallet, $earnednew, 'ARB', 0, $get_fee, $insert_id, $flag, "external_wallet"));
            		    }
            		    else{
                		    //pending log //flag = 0
                		    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id,'externalWallet_ArbWithdraw','-'.$earnednew, 'User Request for ARB Withdraw (Pending)', $data->activeArb));
                            $insert_id = $this->db->insert_id();
                		    
                		    $this->db->query("INSERT INTO withdraw_request (user_id, wallet, value, currency, status, fee, log_id, approvel_flag, comment)VALUES(?,?,?,?,?,?,?,?,?)",array($u_id, $wallet, $earnednew, 'ARB', 0, $get_fee, $insert_id, $flag, 'external_wallet'));
            		    }
            		    
            		    
            		    $get_systemwallet_arb = $this->db->query("SELECT activeArb FROM external_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
                        $updated_arb = $get_systemwallet_arb - $earnednew;
                        $this->db->query("UPDATE external_wallet SET activeArb = ? WHERE user_id = ?", array($updated_arb, $u_id));
            		    
            		    $this->db->query("update users set withdraw_status = 0 WHERE u_id = ?", array($u_id));
            		   
            		    echo json_encode(array('success'=>'1', 'msg'=>'Withdraw Request Generated Successfully.')); exit;
            		}
            		else{
            		    echo json_encode(array('error'=>'1', 'msg'=>'Something went wrong.')); exit;
            		}

        		}else{
        		    echo json_encode(array('error'=>'1', 'msg'=>'Something went wrong.')); exit;
        		}
    }
      
    
    //cron for transections and update user balance
    public function get_address_trans_c(){
        
        $black_qry = $this->db->query("SELECT address FROM blacklist_addresses")->result();
		$black_list = array();
		foreach($black_qry as $q){
		    $black_list[] = $q->address;
		}
        //if(1==0){
            echo "<h4>ARB detail</h4>";
            //for ARB transections
            $res = file_get_contents("https://api.ethplorer.io/getAddressHistory/".$this->deposit_address."?apiKey=freekey");
            $result = json_decode($res);
            
            
            foreach($result->operations as $re){
                //continue;
                //'0x59a5208b32e627891c389ebafc644145224006e8'
        		if(in_array($re->from, $black_list)){
        		    continue;
        		}
                
                $get_trans = $this->db->query("SELECT txt_id FROM deposit_log WHERE txt_id = ?", array($re->transactionHash))->row()->txt_id;
                if($get_trans == $re->transactionHash){
                    echo "already exist <br>";
                }else{
                    if($re->tokenInfo->symbol == 'ARB' && $re->tokenInfo->name =='ARBITRAGE'){
                        if($re->to == strtolower($this->deposit_address)) { 
                            $u_id = $this->db->query("SELECT * FROM users WHERE u_wallet LIKE ? LIMIT 1", array($re->from))->row()->u_id;
                            if($u_id != ''){
                                
                                
                                $value = $re->value / pow(10,18);
                                if($value < 1){
                                    continue;
                                }
                                $time = date("Y-m-d H:i:s", $re->timestamp);
                                $this->db->query("INSERT INTO deposit_log (txt_id, user_id, from_address, value, currency, created_at) VALUES (?,?,?,?,?,?)", 
                                array($re->transactionHash, $u_id, $re->from, $value, $re->tokenInfo->symbol, $time));
                                
                                
                                //0.5 arb fee on deposit
                                $value_after_fee = $value - 0.5;
                                
                                
                                //update admin wallet
                                $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 2 LIMIT 1")->row();
                                $admin_new_arb = $get_admin_wallet->arb + 0.5;
                                $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = ?", array($admin_new_arb, 2));
                                
                                $user_package = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
                                // if($user_package->package == 'Pro+'){
                                //     $get_system_wallet = $this->db->query("Select * from system_wallet WHERE user_id = ?", array($u_id))->row();
                                //     //update user system wallet
                                //     $new_active_system_wallet = $get_system_wallet->activeArb + $value_after_fee;
                                //     $new_active = $this->db->query("update system_wallet SET activeArb = ? WHERE user_id = ?", array($new_active_system_wallet, $u_id));
                                //     // add wallet logs
                                //     $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($u_id,'systemWallet_ARB', $value, 'User Deposit ARB txt_id = '.$re->transactionHash));
                                //     $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($u_id,'ARB_depositFee','-0.5', "Fee"));
                                    
                                // }else{
                                    //wallet logs
                                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($u_id,'ARB_depositFee','-0.5', "Fee"));
                                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($u_id,'systemWallet_ARB', $value, 'User Deposit ARB (Processing) txt_id = '.$re->transactionHash));
                                    $last_insert = $this->db->insert_id();
                                    
                                    // for hold deposit
                                     $this->db->query("INSERT INTO arb_deposit_hold (user_id, value, txid, log_id) VALUES (?,?,?,?)", array($u_id, $value_after_fee, $re->transactionHash, $last_insert));
                                //}
                                
                                echo $u_id." done<br>";
                                
                                //start
                                // $value = $re->value / pow(10,18);
                                // $time = date("Y-m-d H:i:s", $re->timestamp);
                                // $this->db->query("INSERT INTO deposit_log (txt_id, user_id, from_address, value, currency, created_at) VALUES (?,?,?,?,?,?)", 
                                // array($re->transactionHash, $u_id, $re->from, $value, $re->tokenInfo->symbol, $time));
                                
                                // //add ARB into user system wallet
                                // $get_system_wallet = $this->db->query("Select * from system_wallet WHERE user_id = ?", array($u_id))->row();
                                // //0.5 arb fee on deposit
                                // $value_after_fee = $value - 0.25;
                                
                                // $new_active_system_wallet = $get_system_wallet->activeArb + $value_after_fee;
                                // $new_active = $this->db->query("update system_wallet SET activeArb = ? WHERE user_id = ?", array($new_active_system_wallet, $u_id));
                                
                                // //update admin wallet
                                //   $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                                //   $admin_new_arb = $get_admin_wallet->arb + 0.25;
                                //   $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = ?", array($admin_new_arb, 1));
                               
                                // //wallet logs 
                                // $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($u_id,'systemWallet_ARB', $value, 'User Deposit ARB txt_id = '.$re->transactionHash));
                                // $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($u_id,'ARB_depositFee','-0.25', "Fee"));
                          
                                
                                // echo $u_id." done<br>";
                                //end
                                
                                
                            }else{
                                echo "no user found<br>";
                            }
                        }else{
                            echo "to address not match with ours<br>";
                        }
                    }else{
                        echo "token symbol not matched<br>";
                    }
                }
            }
        //}
        echo "<h4>ETH detail</h4>";
        // for ETH transections
        //https://api.ethplorer.io/getAddressTransactions/".$this->deposit_address."?apiKey=freekey
        $res2 = file_get_contents("https://api.etherscan.io/api?module=account&action=txlist&address=".$this->deposit_address."&page=1&offset=50&sort=desc");
        //print_r($res2);
        //$res2 = file_get_contents("https://api.ethplorer.io/getAddressTransactions/0x29AA9730B11950A311FEbe0a566AFA2DbD804BDB?apiKey=freekey");
        $result2 = json_decode($res2);
        
        
        
        foreach($result2->result as $re2){
            if($re2->confirmations < 10){
                continue;
            }
            //'0x59a5208b32e627891c389ebafc644145224006e8'
    		if(in_array($re2->from, $black_list)){
    		    continue;
    		}
            
            $get_trans = $this->db->query("SELECT txt_id FROM deposit_log WHERE txt_id = ?", array($re2->hash))->row()->txt_id;
            if($get_trans == $re2->hash){
                echo "already exist <br>";
                continue;
            }else{
                if($re2->txreceipt_status == 1){
                    if($re2->to == strtolower($this->deposit_address)){
                         $u_id = $this->db->query("SELECT * FROM users WHERE u_wallet LIKE ? LIMIT 1", array($re2->from))->row()->u_id;
                            if($u_id != ''){
                                
                                $value = $re2->value / pow(10,18);
                                if($value <= 0.0015){
                                    continue;
                                }
                                
                                $time = date("Y-m-d H:i:s", $re2->timeStamp);
                                $this->db->query("INSERT INTO deposit_log (txt_id, user_id, from_address, value, currency, created_at) VALUES (?,?,?,?,?,?)", 
                                array($re2->hash, $u_id, $re2->from, $value, 'ETH', $time));
                                
                                //add ETH into user system wallet
                                $get_system_wallet = $this->db->query("Select * from system_wallet WHERE user_id = ?", array($u_id))->row();
                                //ETH Deposit fee 0.0015 ETH
                                $value_after_fee = $value - 0.0015;
                                
                                $new_active_system_wallet = $get_system_wallet->activeEth + $value_after_fee;
                                $new_active = $this->db->query("update system_wallet SET activeEth = ? WHERE user_id = ?", array($new_active_system_wallet, $u_id));
                                
                                //update admin wallet
                               $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 2 LIMIT 1")->row();
                               $admin_new_eth = $get_admin_wallet->eth + 0.0015;
                               $this->db->query("UPDATE admin_wallet SET eth = ? WHERE id = ?", array($admin_new_eth, 2));
                                
                                //wallet logs 
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($u_id,'systemWallet_ETH', $value, 'User Deposit ETH txt_id = '.$re2->hash));
                                $this->db->query("INSERT into wallet_logs (user_id, type, value, comment) values (?,?,?,?)", array($u_id,'ETH_depositFee',"-0.0015", "Fee"));
                                
                                echo $u_id." done<br>";
                                
                            }else{
                                echo "no user found<br>";
                            }
                    }else{
                        echo "To address not match<br>";
                    }
                }else{
                    echo "not success<br>";
                }
            }
        }
        
         
    }
    

    
    
    
    public function mbot_register_form_system(){
        $u_id = $this->session->userdata('u_id');
        if($u_id!=''){
           //$arb_selected = abs($this->input->post('arb_selected'));
           
           $arb_in_usd = doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
           if($arb_in_usd <= 0){
                exit;
            }
           $arb_selected = abs(500 / $arb_in_usd);
           $arb_selected = sprintf('%0.3f', $arb_selected);
           
           if($arb_selected > 0){
               $mbot_member = $this->db->query("SELECT * FROM mbot_members WHERE user_id = ?", array($u_id))->row();
               if(!isset($mbot_member->id)){
                    $get_active_system_wallet = $this->db->query("Select * from system_wallet WHERE user_id = ?", array($u_id))->row();
                    if($get_active_system_wallet->activeArb >= $arb_selected){
                        
                        $new_active_system_wallet = $get_active_system_wallet->activeArb - $arb_selected;
                        $new_active = $this->db->query("update system_wallet SET activeArb = ? WHERE id = ?", array($new_active_system_wallet, $get_active_system_wallet->id));
                        
                        $time = date("Y-m-d H:i:s");
                        $ex_time = date("Y-m-d H:i:s", strtotime('+1 year',strtotime($time)));
                        $sql = $this->db->query("INSERT INTO mbot_members (user_id, fee, max_trade_limit, expire_date) VALUES (?,?,?,?)", array($u_id, $arb_selected, '500000', $ex_time));
                        if($sql){
                            $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, 'systemWallet_ARB', '-'.$arb_selected, $get_active_system_wallet->activeArb, "Transfer ARB for Mbot Membership"));
                            echo 'true'; exit;
                        }
                        
                    }else{echo 'not enough amount in wallet'; exit;}
                   
               }else{
                   $today = date('Y-m-d H:i:s');
                   if($mbot_member->max_trade_limit == 0 || $mbot_member->expire_date < $today){
                        $get_active_system_wallet = $this->db->query("Select * from system_wallet WHERE user_id = ?", array($u_id))->row();
                        if($get_active_system_wallet->activeArb >= $arb_selected){
                            
                            $new_active_system_wallet = $get_active_system_wallet->activeArb - $arb_selected;
                            $new_active = $this->db->query("update system_wallet SET activeArb = ? WHERE id = ?", array($new_active_system_wallet, $get_active_system_wallet->id));
                            
                            $time = date("Y-m-d H:i:s");
                            $ex_time = date("Y-m-d H:i:s", strtotime('+1 year',strtotime($time)));
                            $sql = $this->db->query("UPDATE mbot_members SET fee = ?, max_trade_limit = ?, expire_date = ?, status = ? WHERE user_id = ?", array($arb_selected, '500000', $ex_time, 1, $u_id));
                            if($sql){
                                $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, 'systemWallet_ARB', '-'.$arb_selected, $get_active_system_wallet->activeArb, "Transfer ARB for Mbot Membership"));
                                echo 'true'; exit;
                            }
                            
                        }else{echo 'not enough amount in wallet'; exit;}
                   }
               }
           }else{
               echo "min arb 500";
               exit;
           }
        }
    }
    
    
    
    //  public function change_package(){
    //     $u_id = $this->session->userdata('u_id');
    //     if($u_id!=''){
    //         $pkg = $this->input->post('packagee');
    //         if($pkg_detail = $this->db->query("SELECT * FROM packages WHERE name = ?", array($pkg))->row()){
                
    //             $arb_in_usd = doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
    //             if($arb_in_usd <= 0){
    //                  exit;
    //              }
    //             $arb_selected = abs($pkg_detail->fee / $arb_in_usd);
    //             $arb_selected = sprintf('%0.3f', $arb_selected);
                
    //             // check user details
    //             $user_detail = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
    //             if($user_detail->package == $pkg){
    //                 echo json_encode(array('error'=>'1', 'msg'=>'Your package is already activated.')); exit;
    //             }
    //             if($user_detail->package == 'Pro'){
    //                 echo json_encode(array('error'=>'1', 'msg'=>'You are already on Pro package.')); exit;
    //             }
    //             // check user wallet
    //             $user_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($u_id))->row();
    //             if($user_wallet->activeArb < $arb_selected){
    //                 echo json_encode(array('error'=>'1', 'msg'=>'You have not sufficient Balance in your wallet.')); exit;
    //             }
                
    //             //update user wallet 
    //             $new_user_arb = $user_wallet->activeArb - $arb_selected;
    //             $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($new_user_arb, $u_id));
                
    //             // update admin wallet 
    //             $admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1")->row();
    //             $new_admin_arb = $admin_wallet->arb + $arb_selected;
    //             $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = 1", array($new_admin_arb));
                
    //             // add logs 
    //             $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'systemWallet_ARB', '-'.$arb_selected, 'Membership Package activation fee ('.$arb_in_usd.')', $user_wallet->activeArb));
    //             $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'adminWallet_ARB', $arb_selected, 'fee', $admin_wallet->arb));
                
    //             // update user data
    //             $this->db->query("UPDATE users SET package = ? WHERE u_id = ?", array($pkg_detail->name, $u_id));
                
    //             echo json_encode(array('success'=>'1', 'msg'=>'Your '.$pkg_detail->name.' membership successfully activated.'));
    //             exit;
                
                
    //         }else{
    //             echo json_encode(array('error'=>'1', 'msg'=>'No such package offered.')); exit;
    //         }
    //     }else{
    //         echo json_encode(array('error'=>'1', 'msg'=>'User session expired.')); exit;
    //     }
    // }
    
    
   public function change_package(){
        $u_id = $this->session->userdata('u_id');
        if($u_id!=''){
            $pkg = $this->input->post('packagee');
            if($pkg_detail = $this->db->query("SELECT * FROM packages WHERE name = ?", array($pkg))->row()){
                
                $arb_in_usd = json_decode(file_get_contents("https://www.arbitraging.co/platform/arb_valueLive"));
                $arb_in_usd = $arb_in_usd->USD;
                if($arb_in_usd <= 0){
                    exit;
                }
                // check user details
                $user_detail = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
                if($user_detail->package == $pkg){
                    echo json_encode(array('error'=>'1', 'msg'=>'Your package is already activated.')); exit;
                }
                if($user_detail->package == "Pro"){
                    echo json_encode(array('error'=>'1', 'msg'=>'Your package is Pro, you cant go back to Basic.')); exit;
                }
                
                // if($pkg_detail->name == 'Pro+' && $user_detail->package == 'Pro'){
                    
                //     $current_pkg_detail = $this->db->query("SELECT * FROM packages WHERE name = ?", array($user_detail->package))->row();
                //     $updated_fee = $pkg_detail->fee - $current_pkg_detail->fee;
                //     $arb_selected = abs($updated_fee / $arb_in_usd);
                //     $arb_selected = sprintf('%0.3f', $arb_selected);
                    
                //     // check user wallet
                //     $user_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($u_id))->row();
                //     if($user_wallet->activeArb < $arb_selected){
                //         echo json_encode(array('error'=>'1', 'msg'=>'You have not sufficient Balance in your wallet.')); exit;
                //     }
                    
                //     //update user wallet 
                //     $new_user_arb = $user_wallet->activeArb - $arb_selected;
                //     $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($new_user_arb, $u_id));
                    
                //     // update admin wallet 
                //     $admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1")->row();
                //     $new_admin_arb = $admin_wallet->arb + $arb_selected;
                //     $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = 1", array($new_admin_arb));
                // }
                // else {
                    
                    $arb_selected = abs($pkg_detail->fee / $arb_in_usd);
                    $arb_selected = sprintf('%0.3f', $arb_selected);
                    
                    // check user wallet
                    $user_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($u_id))->row();
                    if($user_wallet->activeArb < $arb_selected){
                        echo json_encode(array('error'=>'1', 'msg'=>'You have not sufficient Balance in your wallet.')); exit;
                    }
                    
                    //update user wallet 
                    $new_user_arb = $user_wallet->activeArb - $arb_selected;
                    $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($new_user_arb, $u_id));
                    
                    // update admin wallet 
                    $admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1")->row();
                    $new_admin_arb = $admin_wallet->arb + $arb_selected;
                    $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = 1", array($new_admin_arb));
                // }

                // add logs 
                $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'systemWallet_ARB', '-'.$arb_selected, 'Membership Package activation fee ('.$arb_in_usd.')', $user_wallet->activeArb));
                $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'adminWallet_ARB', $arb_selected, 'fee', $admin_wallet->arb));
                
                // update user data
                // if($pkg_detail->name == 'Pro+'){
                //     $this->db->query("UPDATE users SET package = ?, pkg_updated = ? WHERE u_id = ?", array($pkg_detail->name, date('Y-m-d'), $u_id));
                //     echo json_encode(array('success'=>'1', 'msg'=>'Your '.$pkg_detail->name.' membership successfully activated.'));
                //     exit;
                // }
                // else{
                    $this->db->query("UPDATE users SET package = ? WHERE u_id = ?", array($pkg_detail->name, $u_id));
                    echo json_encode(array('success'=>'1', 'msg'=>'Your '.$pkg_detail->name.' membership successfully activated.'));
                    exit;
                // }
            }else{
                echo json_encode(array('error'=>'1', 'msg'=>'No such package offered.')); exit;
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'User session expired.')); exit;
        }
    }
    
    
    public function activate_add_on(){
        $u_id = $this->session->userdata('u_id');
        if($u_id!=''){
            $add_on_name = $this->input->post('add_on_name');
            
            if($add_on_details = $this->db->query("SELECT * FROM add_ons WHERE add_on_name = ?", array($add_on_name))->row()){
                $arb_in_usd = json_decode(file_get_contents("https://www.arbitraging.co/platform/arb_valueLive"));
                $arb_in_usd = $arb_in_usd->USD;
                if($arb_in_usd <= 0){
                    exit;
                }
                // check user details
                if($this->db->query("SELECT * FROM user_add_ons WHERE user_id = ? AND add_on_name = ?", array($u_id, $add_on_details->add_on_name))->row()){
                    echo json_encode(array('error'=>'1', 'msg'=>'Your add_on is already activated.')); exit;
                }
                
                $arb_selected = abs($add_on_details->fee / $arb_in_usd);
                $arb_selected = sprintf('%0.3f', $arb_selected);
                
                // check user wallet
                $user_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($u_id))->row();
                if($user_wallet->activeArb < $arb_selected){
                    echo json_encode(array('error'=>'1', 'msg'=>'You have not sufficient Balance in your wallet.')); exit;
                }
                
                //update user wallet 
                $new_user_arb = $user_wallet->activeArb - $arb_selected;
                $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($new_user_arb, $u_id));
                
                // update admin wallet 
                $admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1")->row();
                $new_admin_arb = $admin_wallet->arb + $arb_selected;
                $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = 1", array($new_admin_arb));


                // add logs 
                $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'systemWallet_ARB', '-'.$arb_selected, 'Add On activation fee ('.$arb_in_usd.')', $user_wallet->activeArb));
                $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'adminWallet_ARB', $arb_selected, 'fee', $admin_wallet->arb));
                
                $today = date('Y-m-d');
                
                // Insert user data
                $this->db->query("INSERT INTO user_add_ons (user_id, add_on_name, fee, updated_at) VALUES (?,?,?,?)", array($u_id, $add_on_details->add_on_name, $add_on_details->fee, $today));
                echo json_encode(array('success'=>'1', 'msg'=>'Your '.$add_on_details->add_on_name.' add_on successfully activated.'));
                exit;
            }else{
                echo json_encode(array('error'=>'1', 'msg'=>'No such add_on offered.')); exit;
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'User session expired.')); exit;
        }
    }
    
    public function add_on_cron(){
        exit;
        $date_30_days_ago =  date('Y-m-d',strtotime('-30 days'));
        $today = date('Y-m-d');
        
        if($users = $this->db->query("SELECT * FROM user_add_ons WHERE  updated_at <= '$date_30_days_ago'")->result()){
            $arb_in_usd = json_decode(file_get_contents("https://www.arbitraging.co/platform/arb_valueLive"));
            $arb_in_usd = $arb_in_usd->USD;
            if($arb_in_usd <= 0){
                exit;
            }
            foreach($users as $user){
                
                $arb_selected = abs($user->fee / $arb_in_usd);
                $arb_selected = sprintf('%0.3f', $arb_selected);
        
                // check user wallet
                $user_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($user->user_id))->row();
                
                if($user_wallet->activeArb < $arb_selected){
                    // update user data
                    $this->db->query("DELETE FROM user_add_ons WHERE user_id = ? AND add_on_name = ?", array($user->u_id, $user->add_on_name));
                    $this->db->query("INSERT INTO wallet_logs (user_id,comment,last_blnc) VALUES (?,?,?)", array($user->u_id, 'Add_On Expired, Not enough balance.', $user_wallet->activeArb));
                    continue;
                }
                
                //update user wallet 
                $new_user_arb = $user_wallet->activeArb - $arb_selected;
                $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($new_user_arb, $user->user_id));
                
                // update admin wallet 
                $admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1")->row();
                $new_admin_arb = $admin_wallet->arb + $arb_selected;
                $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = 1", array($new_admin_arb));
            
                // add logs 
                $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($user->user_id, 'systemWallet_ARB', '-'.$arb_selected, 'Add On '.$user->add_on_name.' activation fee ('.$arb_in_usd.')', $user_wallet->activeArb));
                $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($user->user_id, 'adminWallet_ARB', $arb_selected, 'fee', $admin_wallet->arb));
                
                // update user data
                $this->db->query("UPDATE user_add_ons SET updated_at = ? WHERE user_id = ?", array($today, $user->user_id));
            }
        }
    }
    
	 
}
