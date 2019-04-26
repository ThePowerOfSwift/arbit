<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Wallet extends MY_Controller {
   
   public $no_restriction_abot = array(82319);
   public $users_300 = array(79082,778267,78833,79227,13516,55879,83443,61284,55645,79375,60299,19724,53812,82848,17506,
        17543,902,33464,79441,69786,80193,78570,80687,177,946,82149,78461,80226,78546,57668,82941,81934,170,770,
        69633,69588,56837,81731,81292,69766,780537,81312,13706,79493,79065,61081,69589,13761,70001,80640,45215,
        59927,79906,78069,78007,55913,59819,78685,81559,79657,21448,53344,81456,780749,82014,79372,69944,59844,78885,
        77936,779354,55469,77814,21286,778806,78705,780683,32959,69662,778772,79943,69829,82780,78746,79866,37129,
        58580,58664,82831,1072,80200,69548,79322,69637,80857,55479,53104,780071,779428,78856,83254,57952,79898,78682,
        780165,80520,55985,780512,55852,778426,81396,78369,82677,29681,60319,59936,778276,79582,78652,59120,80260,
        55644,78644,78590,59857,81264,79758,64899,80408,53810,13852,77840,778896,78626,78808,78573,778869,81640,
        79056,79925,79297,52537,81128,69692,1043,59772,64771,83267,79867,69974,79749,82790,78633,82665,779372,14178,
        78096,80796,15709,69750,69539,69742,65635,78971,59925,82534,83175,69700,779291,79635,78663,79625,55925,69547,
        80979,57984,53035,69914,77902,78125,69763,59914,56746,13791,779077,780872,80987,78489,79612,82851,78500,80918,
        55408,83779,59686,80423,80420,81425,778326,80238,83719,80022,78785,53377,13637,79863,58194,777888,777975,78480,
        61403,83380,80559,489,61429,83744,80659,64902,69768,69933,782530,61630,61697,15728,53147,55516,778547,846,83520,
        80178,79630,81575,61436,69915,83542,55964,59908,80516,69733,78933,82067,69757,78359,82931,779740,78430,80989,
        13670,81315,79337,80599,780112,77985,79858,79076,59889,13721,78365,13796,52397,81432,83739,78017,19360,778613,
        81023,779787,779513,59301,80233,78473,83372,797,779049,53077,55632,778897,80729,78521,83518,55924,778110,78769,
        81171,78352,79540,79437,82737,79485,55872,81511,69995,79893,80560,778654,779275,69655,79565,79141,81647);
   
   
   public function eth_payout_status(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $abot = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
            if($abot->eth_payout == 0){
                if($abot->auto_reinvest != 1){echo json_encode(array('error'=>'1', 'msg'=>'You must need to activate auto reinvest for eth payout')); exit;}
                $this->db->query("UPDATE abot_wallet SET eth_payout = 1 WHERE user_id = ?", array($u_id));
                echo json_encode(array('success'=>'1', 'msg'=>'Activated Successfully')); exit;
            }else if($abot->eth_payout == 1){
                $this->db->query("UPDATE abot_wallet SET eth_payout = 0 WHERE user_id = ?", array($u_id));
                echo json_encode(array('success'=>'1', 'msg'=>'Deactivated Successfully')); exit;
            }else{
                echo json_encode(array('error'=>'1', 'msg'=>'Something went wrong.')); exit;
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'Session Expired')); exit;
        }
    }
    
   
    //$amount in doller and ARB
    // $type (in, out)
    public function calculate_arb_abot_price($amount, $type){
        
        $arb_value = doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
        if($arb_value > 0)
         return $arb_value;
        
    }
    
    public function external_to_abot(){
        if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('abot_lock'))->row()->lock_status == 1){
            echo json_encode(array('error'=>'1', 'msg'=>'Lock by admin.'));exit;
        } 
        
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $amount = abs($this->input->post('amount'));
            $external_wallet = $this->db->query("SELECT * FROM external_wallet WHERE user_id = ?", array($u_id))->row();
            if($external_wallet->activeArb >= $amount){
                $ce_usd_price = $this->db->query("SELECT * FROM api_settings WHERE name = 'coinexchange_arb_usd'")->row()->value;
                if($ce_usd_price < 0.005){echo json_encode(array('error'=>'1', 'msg'=>'Unexpected $ price.'));exit;}
                $usd_value_of_amount = $amount * $ce_usd_price;
                
                //update external wallet
                $update_external_arb = $external_wallet->activeArb - $amount;
                $this->db->query("UPDATE external_wallet SET activeArb = ? WHERE user_id = ?", array($update_external_arb, $u_id));
                
                //update abot pending
                $abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
                $update_abot_pending = $abot_wallet->pending + $usd_value_of_amount;
;                $this->db->query("UPDATE abot_wallet SET pending = ? WHERE user_id = ?", array($update_abot_pending, $u_id));
                
                //logs here 
                $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id, 'externalWallet_ARB', '-'.$amount, 'Transfer ARB from external wallet to aBOT', $external_wallet->activeArb));
                $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, abot_price, last_blnc) values (?,?,?,?,?,?)", 
                    array($u_id, 'abotPending_$', $usd_value_of_amount, 'Credit $ in abot pending from external wallet ('.$ce_usd_price.')', $ce_usd_price, $abot_wallet->pending));
                
                echo json_encode(array('success'=>'1', 'msg'=>'Transfer Successfully.'));exit;
            }else{
                echo json_encode(array('error'=>'1', 'msg'=>'Your wallet balance is not enough.'));exit;
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'Session Expired.'));exit;
        }
    }
    
    
    // transfer to abot
    public function system_to_abot(){
        $u_id = $this->session->userdata('u_id');
        if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('abot_lock'))->row()->lock_status == 1 && $u_id != 13870){
            echo 'Lock by admin';exit;
        } 
        
        // for pro plus user request
         if(!empty($this->input->post('get_from')) && $this->input->post('get_from') == 'reqfromproplususerstartabot'){
             $token = hash('sha256', $this->input->post('time_stamp').$this->input->post('u_id'));
             if($token == $this->input->post('token')){
                 $req_amount = $this->input->post('arb_selected');
                 $u_id = $this->input->post('u_id');
                 if($req_amount <= 0){
                     echo 'invalid amount'; exit;
                 }
                 
             }else{
                 echo 'unknown request';
                 exit;
             }
         }else{
             $u_id = $this->session->userdata('u_id');
             $user_package = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
             if($user_package->package == 'Pro+'){
                 $pro_data = $this->db->query('SELECT * FROM pp_auto_selling_or_buying WHERE u_id = ?', array($u_id))->row();
                 if($pro_data->trns_to_abot == 1 && $pro_data->status == 1){
                     echo "You are not authorized to do this operation manually"; exit;
                 }
             }
         }
     
       $allow_ids = array(80043,82319,82844);
       if($u_id != ''){
           
           
           $now = date('Y-m-d H:i:s');
           $abot_wallet_userr = $this->db->query('SELECT * FROM abot_wallet WHERE user_id = ?', array($u_id))->row();
           $after_7days = date('Y-m-d H:i:s', strtotime('+7 days', strtotime($abot_wallet_userr->stop_abot_time_10)));
           if($now < $after_7days){
               echo 'false'; exit;
           }
           
           $arb_selected = abs($this->input->post('arb_selected'));
           if($arb_selected <= 0){
                exit;
            }
            
           $arb_in_usd = $this->calculate_arb_abot_price($arb_selected, 'in');
           if($arb_in_usd <= 0){
                exit;
            }
            
            
           if($user_stop_abot_data = $this->db->query("SELECT * FROM stop_abot_data WHERE user_id = ? ORDER BY price ASC", array($u_id))->result()){
                $arb_selected_left = $arb_selected;
            
                foreach($user_stop_abot_data as $one){
                    if($arb_selected_left >= $one->value){
                        $arb_selected_left = $arb_selected_left - $one->value;
                        
                        $abot_amount = $one->value * $one->price;
                        $abot_amountadded = $abot_amount;
                        
                        
                        
                        $this->start_abot($u_id, $one->value, $abot_amount, $abot_amountadded, $one->price, 'system_wallet');
                        
                        $this->db->query("DELETE FROM stop_abot_data WHERE id = $one->id");
                    }
                    else{
                        $value_left = $one->value - $arb_selected_left;
                        $abot_amount = $arb_selected_left * $one->price;
                        $abot_amountadded = $abot_amount;
                        
                        $this->start_abot($u_id, $arb_selected_left, $abot_amount, $abot_amountadded, $one->price, 'system_wallet');
                        
                        $arb_selected_left=0;
                        $this->db->query("UPDATE stop_abot_data SET value = ? WHERE id = ?", array($value_left, $one->id));
                    }
                }
                if($arb_selected_left > 0){
                    $abot_amount = $arb_selected_left * $arb_in_usd;
                    $abot_amountadded = $abot_amount;
                
                    $this->start_abot($u_id, $arb_selected_left, $abot_amount, $abot_amountadded, $arb_in_usd, 'system_wallet');
                }
                echo 'true'; exit;
           }
            else{
                $abot_amount = $arb_selected * $arb_in_usd;
                $abot_amountadded = $abot_amount;
                
                $this->start_abot($u_id, $arb_selected, $abot_amount, $abot_amountadded, $arb_in_usd, 'system_wallet');
                
                echo 'true'; exit;
            }
       }else{
           echo 'false';
           exit;
       }
       
       
   }
   
   public function system_eth_to_gas_choise(){
        $u_id = $this->session->userdata('u_id');
        
        $user_abot_data = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
        $current_gas = $user_abot_data->gas;
        $active = $user_abot_data->active;
        $required_gas = 0;
        if($active < 25000 && $current_gas < 150){
            $required_gas = 150 - $current_gas;
        }
        else if($active >= 25000 &&  $active < 100000 && $current_gas < 250){
            $required_gas = 250 - $current_gas;
        }
        else if($active >= 100000 && $current_gas < 500){
            $required_gas = 500 - $current_gas;
        }
        $choice_arr = array(25, 50, 100);
        $user_choice = intval($this->input->post('user_selection'));
        
        if(!in_array($user_choice, $choice_arr)){
            echo json_encode(array('error' => '1', 'msg' => 'Something went wrong')); exit;
        }
        if($user_choice > $required_gas){
            if($required_gas <= 0){
                echo json_encode(array('error'=>'1', 'msg'=>'Your tank is full')); exit;
            }else{
                echo json_encode(array('error' => '1', 'msg' => 'Your tank required '.round($required_gas, 2).'$')); exit;
            }
        }else{
            $required_gas = $user_choice;
        }
        

        $eth_usd_value = $this->db->query("SELECT * FROM api_settings WHERE name = 'eth_dollor_value'")->row()->value;
        $eth = $required_gas / $eth_usd_value;
        
        $system_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($u_id))->row();

        if($u_id != ''){
            
          if($eth > 0){
            if($system_wallet->activeEth < 0.001){
                echo json_encode(array('error'=>'1', 'msg'=>"You don't have sufficient ETH in wallet")); exit;
            }
            
            if($eth > $system_wallet->activeEth){
                $eth = $system_wallet->activeEth;
                $required_gas = $eth * $eth_usd_value;
            }
            
            $new_eth = $system_wallet->activeEth - $eth;
            $new_gas = $current_gas + $required_gas;
            $this->db->query("UPDATE abot_wallet SET gas = ? WHERE user_id = ?", array($new_gas, $u_id));
            $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($new_eth, $u_id));
            
            //add 75% to userId 43 wallet
            $eth_per_75 = $eth * 0.75;
            $system_wallet_43 = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array(43))->row();
            $new_eth_43 = $system_wallet_43->activeEth + $eth_per_75;
            $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($new_eth_43, 43));
            // log
            $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array(43,'systemWallet_activeEth',$eth_per_75, $system_wallet_43->activeEth, "Credit Eth from user_.$u_id._system wallet for (Gas)"));

            //add 25% to admin wallet
            $eth_per_25 = $eth * 0.25;
            $admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE name = 'eth_from_gas' LIMIT 1")->row();
            $new_eth_admin = $admin_wallet->eth + $eth_per_25;
            
            $this->db->query("UPDATE admin_wallet SET eth = ? WHERE name = ?", array($new_eth_admin, 'eth_from_gas'));
            // log
            $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array(1,'eth_to_gas_fee',$eth_per_25, $admin_wallet->eth, "Fee Eth from user_.$u_id._admin wallet for (Gas)"));

            // logs
            $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'systemWallet_activeEth','-'.$eth, $system_wallet->activeEth, "Transfer Eth from system wallet as Eth to Gas (".$eth_usd_value.")"));
            $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'abotWallet_gas',$required_gas, $user_abot_data->gas, "Credit Gas as $ from system wallet (".$eth_usd_value.")"));
            echo json_encode(array('success'=>'1', 'msg'=>'Transfer to your aBOT Gas successfull.')); exit;
          }else{
              echo json_encode(array('error'=>'1', 'msg'=>'Your tank is full')); exit;
          }
        }else{
                echo json_encode(array('error'=>'1', 'msg'=>'Session expired')); exit;
        }
    }
   
    
    public function new_system_eth_to_gas(){
        $u_id = $this->session->userdata('u_id');
        
        $user_abot_data = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
        $current_gas = $user_abot_data->gas;
        $active = $user_abot_data->active;
        $required_gas = 0;
        if($active < 25000 && $current_gas < 150){
            $required_gas = 150 - $current_gas;
        }
        else if($active >= 25000 &&  $active < 100000 && $current_gas < 250){
            $required_gas = 250 - $current_gas;
        }
        else if($active >= 100000 && $current_gas < 500){
            $required_gas = 500 - $current_gas;
        }

        $eth_usd_value = $this->db->query("SELECT * FROM api_settings WHERE name = 'eth_dollor_value'")->row()->value;
        $eth = $required_gas / $eth_usd_value;
        
        $system_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($u_id))->row();

        if($u_id != ''){
            
          if($eth > 0){
            if($system_wallet->activeEth < 0.001){
                echo json_encode(array('error'=>'1', 'msg'=>"You don't have sufficient ETH in wallet")); exit;
            }
            
            if($eth > $system_wallet->activeEth){
                $eth = $system_wallet->activeEth;
                $required_gas = $eth * $eth_usd_value;
            }
            
            $new_eth = $system_wallet->activeEth - $eth;
            $new_gas = $current_gas + $required_gas;
            $this->db->query("UPDATE abot_wallet SET gas = ? WHERE user_id = ?", array($new_gas, $u_id));
            $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($new_eth, $u_id));
            
            //add 75% to userId 43 wallet
            $eth_per_75 = $eth * 0.75;
            $system_wallet_43 = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array(43))->row();
            $new_eth_43 = $system_wallet_43->activeEth + $eth_per_75;
            $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($new_eth_43, 43));
            // log
            $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array(43,'systemWallet_activeEth',$eth_per_75, $system_wallet_43->activeEth, "Credit Eth from user_.$u_id._system wallet for (Gas)"));

            //add 25% to admin wallet
            $eth_per_25 = $eth * 0.25;
            $admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE name = 'eth_from_gas' LIMIT 1")->row();
            $new_eth_admin = $admin_wallet->eth + $eth_per_25;
            
            $this->db->query("UPDATE admin_wallet SET eth = ? WHERE name = ?", array($new_eth_admin, 'eth_from_gas'));
            // log
            $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array(1,'eth_to_gas_fee',$eth_per_25, $admin_wallet->eth, "Fee Eth from user_.$u_id._admin wallet for (Gas)"));

            // logs
            $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'systemWallet_activeEth','-'.$eth, $system_wallet->activeEth, "Transfer Eth from system wallet as Eth to Gas (".$eth_usd_value.")"));
            $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'abotWallet_gas',$required_gas, $user_abot_data->gas, "Credit Gas as $ from system wallet (".$eth_usd_value.")"));
            echo json_encode(array('success'=>'1', 'msg'=>'Transfer to your aBOT Gas successfull.')); exit;
          }else{
              echo json_encode(array('error'=>'1', 'msg'=>'Your tank is full')); exit;
          }
        }else{
                echo json_encode(array('error'=>'1', 'msg'=>'Session expired')); exit;
        }
    }
    
    // transfer to exchange
    public function system_to_exchange(){
        $u_id = $this->session->userdata('u_id');
        
        $unlock_user = array(13870, 791138);
        if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('exchange_lock'))->row()->lock_status == 1 && !in_array($u_id, $unlock_user)){
            echo json_encode(array('error'=>'1', 'msg' => 'Lock by admin.')); exit;
        }
         
        
        if($u_id != ''){
            $ex_amount = abs($this->input->post('ex_amount'));
            $ex_amountadded = abs($this->input->post('ex_amount'));
            $currency = $this->input->post('currency');
            if(!empty($ex_amount) && $ex_amount > 0){
                
                if($currency == 'ARB'){
                    //echo 'false'; exit; // Made to cancel all arb deposits. 
                    
                    $get_active_system_wallet = $this->db->query("Select * from system_wallet WHERE user_id = ?", array($u_id))->row();
                    if($get_active_system_wallet->activeArb >= $ex_amount){
                        
                        $new_active_system_wallet = $get_active_system_wallet->activeArb - $ex_amount;
                        $new_active = $this->db->query("update system_wallet SET activeArb = ? WHERE id = ?", array($new_active_system_wallet, $get_active_system_wallet->id));
                        
                        $qry1 = $this->db->query("Select * from exchange_wallet WHERE user_id = ?", array($u_id))->row();
                        if(isset($qry1->id) && $qry1->id != ''){
                            $ex_amount = $ex_amount + $qry1->activeArb;
                            $sql = $this->db->query("update exchange_wallet SET activeArb = ? WHERE id = ?", array($ex_amount, $qry1->id));
                            if($sql){
                                $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, 'systemWallet_ARB', '-'.$ex_amountadded, $get_active_system_wallet->activeArb, "Transfer ARB from Wallet to Exchange"));
                                $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, 'exchangeWallet_ARB', $ex_amountadded, $qry1->activeArb, "Credit ARB in Exchange"));
                                echo json_encode(array('success'=>'1', 'msg'=>'Transfer Successfully')); exit;
                            }
                        }else{
                            $sql = $this->db->query("INSERT into exchange_wallet (user_id, activeArb) values (?,?)", array($u_id, $ex_amount));
                            if($sql){
                                $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, 'systemWallet_ARB', '-'.$ex_amountadded, $get_active_system_wallet->activeArb, "Transfer ARB from Wallet to Exchange"));
                                $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, 'exchangeWallet_ARB', $ex_amountadded, 0, "Credit ARB in Exchange"));
                                echo json_encode(array('success'=>'1', 'msg'=>'Transfer Successfully')); exit;
                            }
                        }
                    }else{ echo json_encode(array('error'=>'1', 'msg'=>'Transfer Fail')); exit;}
                    
                }else if($currency == 'ETH'){
                    
                    $get_active_system_wallet = $this->db->query("Select * from system_wallet WHERE user_id = ?", array($u_id))->row();
                    if($get_active_system_wallet->activeEth >= $ex_amount){
                        
                        $new_active_system_wallet = $get_active_system_wallet->activeEth - $ex_amount;
                        $new_active = $this->db->query("update system_wallet SET activeEth = ? WHERE id = ?", array($new_active_system_wallet, $get_active_system_wallet->id));
                        
                        $qry1 = $this->db->query("Select * from exchange_wallet WHERE user_id = ?", array($u_id))->row();
                        if(isset($qry1->id) && $qry1->id != ''){
                            $ex_amount = $ex_amount + $qry1->activeEth;
                            $sql = $this->db->query("update exchange_wallet SET activeEth = ? WHERE id = ?", array($ex_amount, $qry1->id));
                            if($sql){
                                $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, 'systemWallet_ETH', '-'.$ex_amountadded, $get_active_system_wallet->activeEth, "Transfer ETH from Wallet to Exchange"));
                                $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, 'exchangeWallet_ETH', $ex_amountadded, $qry1->activeEth, "Credit ETH in Exchange"));
                                 echo json_encode(array('success'=>'1', 'msg'=>'Transfer Successfully')); exit;
                            }
                        }else{
                            $sql = $this->db->query("INSERT into exchange_wallet (user_id, activeEth) values (?,?)", array($u_id, $ex_amount));
                            if($sql){
                                $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, 'systemWallet_ETH', '-'.$ex_amountadded, $get_active_system_wallet->activeEth, "Transfer ETH from Wallet to Exchange"));
                                $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, 'exchangeWallet_ETH', $ex_amountadded, 0, "Credit ETH in Exchange"));
                                echo json_encode(array('success'=>'1', 'msg'=>'Transfer Successfully')); exit;
                            }
                        }
                    }else{ echo json_encode(array('error'=>'1', 'msg'=>'Transfer Fail')); exit;}
                    
                }else{
                    echo json_encode(array('error'=>'1', 'msg'=>'Transfer Fail')); exit;
                }
                
                
            }
            else{
                echo json_encode(array('error'=>'1', 'msg'=>'Transfer Fail')); exit;
            }
        }else{
           echo json_encode(array('error'=>'1', 'msg'=>'Transfer Fail')); exit;
        }
        
        
    }
    
    // transfer to gas
    public function system_arb_to_gas(){
        if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('abot_lock'))->row()->lock_status == 1){
            echo 'Lock by admin';exit;
        } 
        $min = 25;
        $u_id = $this->session->userdata('u_id');
        
        $user_package = $this->db->query("SELECT package FROM users WHERE u_id = ?", array($u_id))->row()->package;
        
        if($user_package == 'Basic'){
            $max = 50;
        }
        else if($user_package == 'Pro'){
            $max = 250;
        }

        $arb =  doubleval($this->input->post('arb'));
        
        $arb_value = file_get_contents("https://www.arbitraging.co/platform/abot_arb");
    
        $usd = $arb*$arb_value;

        if($usd < $min){
            echo "Minimum value of gas is ".$min."$";
            exit;
        }else if($usd > $max){
            echo "Maximum value of gas is ".$max."$";
            exit;
        }
        
        if($u_id != '' && $arb > 0){
            $system_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($u_id))->row();
            $abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
            if($arb < $system_wallet->activeArb){
                $new_arb = $system_wallet->activeArb - $arb;
                $new_gas = $abot_wallet->gas + $usd;
                $this->db->query("UPDATE abot_wallet SET gas = ? WHERE user_id = ?", array($new_gas, $u_id));
                $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($new_arb, $u_id));
                // logs
                $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'systemWallet_activeArb','-'.$arb, $system_wallet->activeArb, "Transfer Arb from system wallet as ARB to Gas"));
                $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'abotWallet_gas',$usd, $abot_wallet->gas, "Credit Gas as $ from system wallet"));
                echo "Success";
                exit;
                
            }else{
                echo "Gas not greater then active investment";
                exit;
            }
            
        }
    }
    
    // public function system_eth_to_gas(){
    //     $min = 25;
    //     $u_id = $this->session->userdata('u_id');
        
    //     $user_package = $this->db->query("SELECT package FROM users WHERE u_id = ?", array($u_id))->row()->package;
        
    //     if($user_package == 'Basic'){
    //         $max = 100;
    //     }
    //     else if($user_package == 'Pro'){
    //         $max = 500;
    //     }
    //     else if($user_package == 'Advance'){
    //         $max = 250;
    //     }

    //     $eth =  doubleval($this->input->post('eth'));
        
    //     //$responce = json_decode(file_get_contents("https://api.coinmarketcap.com/v1/ticker/ethereum/"));
    //     //$eth_usd_value = $responce[0]->price_usd;
    //      $eth_usd_value = $this->db->query("SELECT * FROM api_settings WHERE name = 'eth_dollor_value'")->row()->value;
    
    
    //     $eth_usd = $eth * $eth_usd_value;
        
    //     if($eth_usd < $min){
    //         echo json_encode(array('error'=>'1', 'msg'=>'Minimum value of gas is '.$min.'$')); exit;
    //     }else if($eth_usd > $max){
    //         echo json_encode(array('error'=>'1', 'msg'=>'Maximum value of gas is '.$max.'$')); exit;
    //     }
        
    //     if($u_id != '' && $eth > 0){
    //         $system_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($u_id))->row();
    //         $abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
    //         if($eth < $system_wallet->activeEth){
    //             $new_eth = $system_wallet->activeEth - $eth;
    //             $new_gas = $abot_wallet->gas + $eth_usd;
    //             $this->db->query("UPDATE abot_wallet SET gas = ? WHERE user_id = ?", array($new_gas, $u_id));
    //             $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($new_eth, $u_id));
                
    //             //add 75% to userId 43 wallet
    //             $eth_per_75 = $eth * 0.75;
    //             $system_wallet_43 = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array(43))->row();
    //             $new_eth_43 = $system_wallet_43->activeEth + $eth_per_75;
    //             $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($new_eth_43, 43));
    //             // log
    //             $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array(43,'systemWallet_activeEth',$eth_per_75, $system_wallet_43->activeEth, "Credit Eth from user_.$u_id._system wallet for (Gas)"));

    //             //add 25% to admin wallet
    //             $eth_per_25 = $eth * 0.25;
    //             $admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 5 LIMIT 1")->row();
    //             $new_eth_admin = $admin_wallet->eth + $eth_per_25;
                
    //             $this->db->query("UPDATE admin_wallet SET eth = ? WHERE id = ?", array($new_eth_admin, 5));
    //             // log
    //             $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array(5,'eth_to_gas_fee',$eth_per_25, $admin_wallet->eth_from_gas, "Fee Eth from user_.$u_id._admin wallet for (Gas)"));

    //             // logs
    //             $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'systemWallet_activeEth','-'.$eth, $system_wallet->activeEth, "Transfer Eth from system wallet as Eth to Gas"));
    //             $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'abotWallet_gas',$eth_usd, $abot_wallet->gas, "Credit Gas as $ from system wallet"));
    //             echo json_encode(array('success'=>'1', 'msg'=>'Transfer to your aBOT Gas successfull.')); exit;
                
    //         }else{
    //             echo json_encode(array('error'=>'1', 'msg'=>'Gas not greater then active investment')); exit;
    //         }
    //     }
    // }
    
    
    //transfer abot to system active tokens
    public function abot_active_to_systemWallet(){
        exit;
        // for pro plus user request
         if(!empty($this->input->post('get_from')) && $this->input->post('get_from') == 'reqfromproplususerstopabot'){
             $token = hash('sha256', $this->input->post('time_stamp').$this->input->post('u_id'));
             if($token == $this->input->post('token')){
                 $per_age = $this->input->post('active_arb_per');
                 $u_id = $this->input->post('u_id');
                 if($per_age < 0 || $per_age > 100){
                     echo 'invalid %age'; exit;
                 }
                 if($user_abot_date = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row()){
                     $_POST['dollar_selected'] = ($user_abot_date->active / 100) * $per_age;
                 }else{
                     echo 'no abot record'; exit;
                 }
                 
                 //dollar_selected
                 //print_r($_POST);
             }else{
                 echo 'unknown request';
                 exit;
             }
         }else{
             $u_id = $this->session->userdata('u_id');
         }
        
         
        $arr = str_split($u_id); // convert string to an array
        $last_digit  = end($arr);
        
       
         
        if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('abot_lock'))->row()->lock_status == 1 ){
           
            echo "Lock by admin";
            exit;
        } 
        
        
         if($u_id != ''){
            
            // check lock time here 
            $abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
            if($abot_wallet->abot_lock_status == 1){
                $now = date('Y-m-d H:i:s');
                $block_days = '+'.$abot_wallet->lock_days.' days';
                $block_time = date('Y-m-d H:i:s', strtotime($block_days, strtotime($abot_wallet->lock_time)));
                if($now < $block_time){
                    echo 'false'; exit;
                }else{
                    $this->db->query("UPDATE abot_wallet SET abot_lock_status = ? WHERE user_id = ?", array(0, $u_id));
                }
            }
            
            $allow_ids = array(80043,82319,82844);
            
            //$abot_active_transfer_amount = doubleval($this->input->post('abot_active_transfer_amount'));
            
               $dollar_selected = doubleval($this->input->post('dollar_selected'));
               if($dollar_selected <= 0){
                    exit;
                }
               
               $arb_in_usd = $this->calculate_arb_abot_price($dollar_selected, 'out');
               if($arb_in_usd <= 0){
                    exit;
                }
               
               $abot_active_transfer_amount = $dollar_selected / $arb_in_usd;
               
               if(!empty($dollar_selected) && $dollar_selected > 0 && $abot_active_transfer_amount > 0){
                   $get_active_from_abot = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
                   if($get_active_from_abot->active > 0 && $get_active_from_abot->active >= $dollar_selected){
                       
                       $before_24hour = date("Y-m-d H:i:s", strtotime('-24 hour'));
                       if($get_active_from_abot->pending_date > $before_24hour && !in_array($u_id, $allow_ids)){
                          echo "false"; exit;
                       }
                       
                       $abot_update_amount = $get_active_from_abot->active - $dollar_selected;
                       $now = date('Y-m-d H:i:s');
                       $this->db->query("UPDATE abot_wallet SET active = ?, stop_abot_time = ? WHERE user_id = ?", array($abot_update_amount, $now, $u_id));
                       
                       $exist_earned = $this->db->query("SELECT count(*) as exist FROM exchange_earned_wallet WHERE user_id = ?", array($u_id))->row();
                       if($exist_earned->exist <= 0){
                           $this->db->query("INSERT INTO exchange_earned_wallet (user_id, activeArb) VALUES (?,?)", array($u_id, 0));
                       }
                       
                       $get_system_active_arb = $this->db->query("SELECT activeArb FROM exchange_earned_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
                       
                       // 4 % fee on stop abot
                       $stopabot_fee = $abot_active_transfer_amount * 0.04;
                       $abot_arb_afterfee = $abot_active_transfer_amount - $stopabot_fee;
                       
                       $system_update_arb = $get_system_active_arb + $abot_arb_afterfee;
                       $this->db->query("UPDATE exchange_earned_wallet SET activeArb = ? WHERE user_id = ?", array($system_update_arb, $u_id));
                       
                       /// stop_abot_data_record
                    
                       $this->db->query("INSERT into stop_abot_data (user_id, comment, wallet, value, price, date_time) values (?,?,?,?,?,?)", array($u_id, 'stop aBot', 'Earned Wallet', $abot_active_transfer_amount, $arb_in_usd, $now));
                       
                       //update admin wallet
                       $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
                       $admin_new_arb = $get_admin_wallet->arb + $stopabot_fee;
                       $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = ?", array($admin_new_arb, 1));
                       
                       
                       $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'abotWallet_active','-'.$dollar_selected, $get_active_from_abot->active, "Transfer $ from aBot active to Earned Wallet"));
                       $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", array($u_id,'exchangeEarnedWallet_ARB',$abot_arb_afterfee, $get_system_active_arb, "Credit ARB in Earned Wallet stop aBot", $arb_in_usd));
                       $log3 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'stop_aBotFee','-'.$stopabot_fee, ($get_system_active_arb+$abot_active_transfer_amount), "Fee"));
                       
                       echo "true"; exit;
                       
                   }else{
                       echo "false"; exit;
                   }
               }else{
                   echo 'false'; exit;
               }
               
         }else{
             echo 'false'; exit;
         }
    }
    
    //transfer abot to system earned tokens
    public function abot_earned_to_systemWallet(){
        $u_id = $this->session->userdata('u_id');
        
             
        //$arr = str_split($u_id); // convert string to an array
        //$last_digit  = end($arr);
        
        //  if($last_digit == 4 || $last_digit == 5){
        //          echo "Lock by admin";
        //         exit;
        //     }
            
        // if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('abot_lock'))->row()->lock_status == 1){
        //     echo "Lock by admin";
        //     exit;
        // } 
         
         if($u_id != ''){
               $abot_earned_transfer_amount = doubleval($this->input->post('abot_earned_transfer_amount'));
               if(!empty($abot_earned_transfer_amount) && $abot_earned_transfer_amount > 0){
                   $get_earned_from_abot = $this->db->query("SELECT earned FROM abot_wallet WHERE user_id = ?", array($u_id))->row()->earned;
                   if($get_earned_from_abot > 0 && $get_earned_from_abot >= $abot_earned_transfer_amount){
                       
                       $abot_update_amount = $get_earned_from_abot - $abot_earned_transfer_amount;
                       $this->db->query("UPDATE abot_wallet SET earned = ? WHERE user_id = ?", array($abot_update_amount, $u_id));
                       
                       $get_system_active_arb = $this->db->query("SELECT activeArb FROM system_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
                       $system_update_arb = $get_system_active_arb + $abot_earned_transfer_amount;
                       $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($system_update_arb, $u_id));
                       
                       $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc,comment) values (?,?,?,?,?)", array($u_id,'abotWallet_earned','-'.$abot_earned_transfer_amount, $get_earned_from_abot, "Transfer aBot Earned ARB to Wallet"));
                       $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc,comment) values (?,?,?,?,?)", array($u_id,'systemWallet_ARB',$abot_earned_transfer_amount, $get_system_active_arb, "Credit ARB in Wallet from aBot Earned"));
                       
                       echo "true"; exit;
                       
                   }else{
                       echo "false"; exit;
                   }
               }else{
                   echo 'false'; exit;
               }
               
         }else{
             echo 'false'; exit;
         }
    }
    
    //transfer abot to system earned tokens
    public function abot_earned_to_externalWallet(){
        $u_id = $this->session->userdata('u_id');
         
         if($u_id != ''){
               $abot_earned_transfer_amount = doubleval($this->input->post('abot_earned_transfer_amount'));
               if(!empty($abot_earned_transfer_amount) && $abot_earned_transfer_amount > 0){
                   $get_earned_from_abot = $this->db->query("SELECT earned FROM abot_wallet WHERE user_id = ?", array($u_id))->row()->earned;
                   if($get_earned_from_abot > 0 && $get_earned_from_abot >= $abot_earned_transfer_amount){
                       
                       $abot_update_amount = $get_earned_from_abot - $abot_earned_transfer_amount;
                       $this->db->query("UPDATE abot_wallet SET earned = ? WHERE user_id = ?", array($abot_update_amount, $u_id));
                       
                       if($res = $this->db->query("SELECT activeArb FROM external_wallet WHERE user_id = ?", array($u_id))->row()){
                           $get_system_active_arb = $res->activeArb;
                           $system_update_arb = $get_system_active_arb + $abot_earned_transfer_amount;
                           $this->db->query("UPDATE external_wallet SET activeArb = ? WHERE user_id = ?", array($system_update_arb, $u_id));
                       }else{
                           $get_system_active_arb = 0;
                           $this->db->query('INSERT INTO external_wallet (user_id, activeArb) VALUES (?,?)', array($u_id, $abot_earned_transfer_amount));
                       }
                       
                       $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc,comment) values (?,?,?,?,?)", array($u_id,'abotWallet_earned','-'.$abot_earned_transfer_amount, $get_earned_from_abot, "Transfer aBot Earned ARB to Wallet"));
                       $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc,comment) values (?,?,?,?,?)", array($u_id,'externalWallet_ARB',$abot_earned_transfer_amount, $get_system_active_arb, "Credit ARB in External Wallet from aBot Earned"));
                       
                       echo "true"; exit;
                       
                   }else{
                       echo "false"; exit;
                   }
               }else{
                   echo 'false'; exit;
               }
               
         }else{
             echo 'false'; exit;
         }
    }
    
    //transfer earned eth to system wallet
    public function eth_earned_to_systemWallet(){
        $u_id = $this->session->userdata('u_id');
 
        if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('abot_lock'))->row()->lock_status == 1){
            echo json_encode(array('error'=>'1', 'msg' => 'Lock by admin.')); exit;
        } 
         
        if($u_id != ''){
            $eth_earned_transfer_amount = doubleval($this->input->post('eth_earned_transfer_amount'));
            if(!empty($eth_earned_transfer_amount) && $eth_earned_transfer_amount > 0){
                $get_eth_earned_from_abot = $this->db->query("SELECT eth_earned FROM abot_wallet WHERE user_id = ?", array($u_id))->row()->eth_earned;
                if($get_eth_earned_from_abot > 0 && $get_eth_earned_from_abot >= $eth_earned_transfer_amount){
                   
                    $update_amount = $get_eth_earned_from_abot - $eth_earned_transfer_amount;
                    $this->db->query("UPDATE abot_wallet SET eth_earned = ? WHERE user_id = ?", array($update_amount, $u_id));
                   
                    $get_system_active_eth = $this->db->query("SELECT activeEth FROM system_wallet WHERE user_id = ?", array($u_id))->row()->activeEth;
                    $system_update_eth = $get_system_active_eth + $eth_earned_transfer_amount;
                    $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($system_update_eth, $u_id));
                   
                    $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc,comment) values (?,?,?,?,?)", array($u_id,'abotWallet_eth_earned','-'.$eth_earned_transfer_amount, $get_eth_earned_from_abot, "Transfer aBot Earned ETH to System Wallet"));
                    $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc,comment) values (?,?,?,?,?)", array($u_id,'systemWallet_ETH',$eth_earned_transfer_amount, $get_system_active_eth, "Credit ETH in System Wallet from aBot Earned Eth"));
                   
                    echo json_encode(array('success'=>'1', 'msg' => 'Transfered to system wallet successfully')); exit;
                   
                }else{
                   echo json_encode(array('error'=>'1', 'msg' => 'Not enough balance in you aBot Earned Eth.')); exit;
                }
            }else{
               echo json_encode(array('error'=>'1', 'msg' => 'Value must be greater then 0.')); exit;
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg' => 'Session Expired.')); exit;
        }
    }
    
    //transfer abot_earned to ex_earned wallet
    public function abot_earned_to_exEarned(){
        // echo 'This feature is currently disabled by admin, be patient.';
        // exit;
        $u_id = $this->session->userdata('u_id');
             
        $arr = str_split($u_id); // convert string to an array
        $last_digit  = end($arr);
        
        //  if($last_digit == 4 || $last_digit == 5){
        //          echo "Lock by admin";
        //         exit;
        //     }
            
        // if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('abot_lock'))->row()->lock_status == 1){
        //     echo "Lock by admin";
        //     exit;
        // } 
         
         if($u_id != ''){
               $abot_earned_transfer_amount = abs($this->getTruncatedValue(doubleval($this->input->post('abot_earned_transfer_amount')), 4));
               if(!empty($abot_earned_transfer_amount) && $abot_earned_transfer_amount > 0){
                   $get_earned_from_abot = $this->db->query("SELECT earned FROM abot_wallet WHERE user_id = ?", array($u_id))->row()->earned;
                   if($get_earned_from_abot > 0 && $get_earned_from_abot >= $abot_earned_transfer_amount){
                       
                       $abot_update_amount = $get_earned_from_abot - $abot_earned_transfer_amount;
                       $this->db->query("UPDATE abot_wallet SET earned = ? WHERE user_id = ?", array($abot_update_amount, $u_id));
                       $exEarned_last = 0;
                       if($get_exEarned_active_arb = $this->db->query("SELECT activeArb FROM exchange_earned_wallet WHERE user_id = ?", array($u_id))->row()){
                           $exEarned_last = $get_exEarned_active_arb->activeArb;
                           $exEarned_update_arb = $get_exEarned_active_arb->activeArb + $abot_earned_transfer_amount;
                           $this->db->query("UPDATE exchange_earned_wallet SET activeArb = ? WHERE user_id = ?", array($exEarned_update_arb, $u_id));
                       }else{
                           $exEarned_update_arb = $abot_earned_transfer_amount;
                           $this->db->query("INSERT INTO exchange_earned_wallet (user_id, activeArb) VALUES (?,?)", array($u_id, $exEarned_update_arb));
                       }
                       
                       
                       $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc,comment) values (?,?,?,?,?)", array($u_id,'abotWallet_earned','-'.$abot_earned_transfer_amount, $get_earned_from_abot, "Transfer aBot Earned ARB to Exchange Earned Wallet"));
                       $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc,comment) values (?,?,?,?,?)", array($u_id,'exEarnedWallet_ARB',$abot_earned_transfer_amount, $exEarned_last, "Credit ARB in Exchange Earned Wallet from aBot earned"));
                       
                       echo "true"; exit;
                       
                   }else{
                       echo "false"; exit;
                   }
               }else{
                   echo 'false'; exit;
               }
               
         }else{
             echo 'false'; exit;
         }
    }
    
    // public function exEarned_to_wallet(){
    //     exit;
    //      $u_id = $this->session->userdata('u_id');
    //      if($u_id != ''){
    //           $ex_earned_transfer_amount = abs($this->getTruncatedValue(doubleval($this->input->post('amount')), 4));
    //           if(!empty($ex_earned_transfer_amount) && $ex_earned_transfer_amount > 0){
    //               $get_earned_from_ex = $this->db->query("SELECT activeArb FROM exchange_earned_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
    //               if($get_earned_from_ex > 0 && $get_earned_from_ex >= $ex_earned_transfer_amount){
                       
    //                   $ex_update_amount = $get_earned_from_ex - $ex_earned_transfer_amount;
    //                   $this->db->query("UPDATE exchange_earned_wallet SET activeArb = ? WHERE user_id = ?", array($ex_update_amount, $u_id));
    //                   $wallet_last = 0;
    //                   if($get_active_arb = $this->db->query("SELECT activeArb FROM system_wallet WHERE user_id = ?", array($u_id))->row()){
    //                       $wallet_update_arb = $get_active_arb->activeArb + $ex_earned_transfer_amount;
    //                       $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($wallet_update_arb, $u_id));
    //                   }
                       
    //                   $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc,comment) values (?,?,?,?,?)", array($u_id,'exEarnedWallet_ARB','-'.$ex_earned_transfer_amount, $get_earned_from_ex, "Transfer exchange Earned ARB to Wallet"));
    //                   $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc,comment) values (?,?,?,?,?)", array($u_id,'systemWallet_ARB',$ex_earned_transfer_amount, $get_active_arb->activeArb, "Credit ARB in Wallet from exchange earned"));
                       
    //                   echo json_encode(array('success'=>'1', 'msg' => 'Transfered to wallet successfully.')); exit;
                       
    //               }else{
    //                   echo json_encode(array('error'=>'1', 'msg' => 'Not enough balance in your earned wallet.')); exit;
    //               }
    //           }else{
    //               echo json_encode(array('error'=>'1', 'msg' => 'Ammount not empty.')); exit;
    //           }  
    //      }else{
    //          echo json_encode(array('error'=>'1', 'msg' => 'User session expired.')); exit;
    //      }
    // }
    
    public function exEarned_to_abot(){
        exit;
       $u_id = $this->session->userdata('u_id');
            
        $arr = str_split($u_id); // convert string to an array
        $last_digit  = end($arr);
        
        //  if($last_digit == 4 || $last_digit == 5){
        //          echo "Lock by admin";
        //         exit;
        //     }
            
            
       if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('abot_lock'))->row()->lock_status == 1){
            echo json_encode(array('error'=>'1', 'msg' => 'Lock by admin.')); exit;
        } 
       if($u_id != ''){
           $arb_selected = abs($this->input->post('amount'));
           $arb_in_usd = $this->calculate_arb_abot_price($arb_selected, 'in');
           if($arb_in_usd <= 0){
                exit;
            }
            
            if($user_stop_abot_data = $this->db->query("SELECT * FROM stop_abot_data WHERE user_id = ? ORDER BY price ASC", array($u_id))->result()){
                $arb_selected_left = $arb_selected;
            
                foreach($user_stop_abot_data as $one){
                    if($arb_selected_left >= $one->value){
                        $arb_selected_left = $arb_selected_left - $one->value;
                        
                        $abot_amount = $one->value * $one->price;
                        $abot_amountadded = $abot_amount;
                        
                        if(!empty($one->value) && $one->value > 0 && $abot_amount > 0){
                           $get_active_exchange_earned_wallet = $this->db->query("Select * from exchange_earned_wallet WHERE user_id = ?", array($u_id))->row();
                           
                           if($get_active_exchange_earned_wallet->activeArb >= $one->value){
                               
                               $new_active_exchange_earned_wallet = $get_active_exchange_earned_wallet->activeArb - $one->value;
                               $new_active = $this->db->query("update exchange_earned_wallet SET activeArb = ? WHERE id = ?", array($new_active_exchange_earned_wallet, $get_active_exchange_earned_wallet->id));
                               
                               $qry1 = $this->db->query("Select * from abot_wallet WHERE user_id = ?", array($u_id))->row();
                               if(isset($qry1->id) && $qry1->id != ''){
                                   $abot_amount = $abot_amount + $qry1->pending;
                                   $sql = $this->db->query("update abot_wallet SET pending = ? WHERE id = ?", array($abot_amount, $qry1->id));
                                   if($sql){
                                       $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchange_earned_Wallet_ARB', '-'.$one->value, $get_active_exchange_earned_wallet->activeArb, "Transfer ARB from earned Wallet to aBot"));
                                       $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", array($u_id,'abotWallet_pending',$abot_amountadded, $qry1->pending, "Credit $ to aBot Pending", $one->price));
                                   }
                               }else{
                                   $sql = $this->db->query("INSERT into abot_wallet (user_id, pending) values (?,?)", array($u_id,$abot_amount));
                                   if($sql){
                                       $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchange_earned_Wallet_ARB', '-'.$one->value, $get_active_exchange_earned_wallet->activeArb, "Transfer ARB from earned Wallet to aBot"));
                                       $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", array($u_id,'abotWallet_pending',$abot_amountadded, 0, "Credit $ to aBot Pending",$one->price));
                                   }
                               }
                           }
                       }
                    $this->db->query("DELETE FROM stop_abot_data WHERE id = $one->id");
                    
                    }
                    else{
                        $value_left = $one->value - $arb_selected_left;
                        $abot_amount = $arb_selected_left * $one->price;
                        $abot_amountadded = $abot_amount;
                        
                        if(!empty($arb_selected_left) && $arb_selected_left > 0 && $abot_amount > 0){
                           $get_active_exchange_earned_wallet = $this->db->query("Select * from exchange_earned_wallet WHERE user_id = ?", array($u_id))->row();
                           
                           if($get_active_exchange_earned_wallet->activeArb >= $arb_selected_left){
                               
                               $new_active_exchange_earned_wallet = $get_active_exchange_earned_wallet->activeArb - $arb_selected_left;
                               $new_active = $this->db->query("update exchange_earned_wallet SET activeArb = ? WHERE id = ?", array($new_active_exchange_earned_wallet, $get_active_exchange_earned_wallet->id));
                               
                               $qry1 = $this->db->query("Select * from abot_wallet WHERE user_id = ?", array($u_id))->row();
                               if(isset($qry1->id) && $qry1->id != ''){
                                   $abot_amount = $abot_amount + $qry1->pending;
                                   $sql = $this->db->query("update abot_wallet SET pending = ? WHERE id = ?", array($abot_amount, $qry1->id));
                                   if($sql){
                                       $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchange_earned_Wallet_ARB', '-'.$arb_selected_left, $get_active_exchange_earned_wallet->activeArb, "Transfer ARB from earned Wallet to aBot"));
                                       $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", array($u_id,'abotWallet_pending',$abot_amountadded, $qry1->pending, "Credit $ to aBot Pending", $one->price));
                                   }
                               }else{
                                   $sql = $this->db->query("INSERT into abot_wallet (user_id, pending) values (?,?)", array($u_id,$abot_amount));
                                   if($sql){
                                       $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchange_earned_Wallet_ARB', '-'.$arb_selected_left, $get_active_exchange_earned_wallet->activeArb, "Transfer ARB from earned Wallet to aBot"));
                                       $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", array($u_id,'abotWallet_pending',$abot_amountadded, 0, "Credit $ to aBot Pending", $one->price));
                                   }
                               }
                           }
                       }
                    $arb_selected_left=0;
                    $this->db->query("UPDATE stop_abot_data SET value = ? WHERE id = ?", array($value_left, $one->id));
                    }
                }
                
                if($arb_selected_left > 0){
                    $abot_amount = $arb_selected_left * $arb_in_usd;
                    $abot_amountadded = $abot_amount;
                    
                    if(!empty($arb_selected_left) && $arb_selected_left > 0 && $abot_amount > 0){
                       $get_active_exchange_earned_wallet = $this->db->query("Select * from exchange_earned_wallet WHERE user_id = ?", array($u_id))->row();
                       
                       if($get_active_exchange_earned_wallet->activeArb >= $arb_selected_left){
                           
                           $new_active_exchange_earned_wallet = $get_active_exchange_earned_wallet->activeArb - $arb_selected_left;
                           $new_active = $this->db->query("update exchange_earned_wallet SET activeArb = ? WHERE id = ?", array($new_active_exchange_earned_wallet, $get_active_exchange_earned_wallet->id));
                           
                           $qry1 = $this->db->query("Select * from abot_wallet WHERE user_id = ?", array($u_id))->row();
                           if(isset($qry1->id) && $qry1->id != ''){
                               $abot_amount = $abot_amount + $qry1->pending;
                               $sql = $this->db->query("update abot_wallet SET pending = ? WHERE id = ?", array($abot_amount, $qry1->id));
                               if($sql){
                                   $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchange_earned_Wallet_ARB', '-'.$arb_selected_left, $get_active_exchange_earned_wallet->activeArb, "Transfer ARB from earned Wallet to aBot"));
                                   $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", array($u_id,'abotWallet_pending',$abot_amountadded, $qry1->pending, "Credit $ to aBot Pending", $arb_in_usd));
                               }
                           }else{
                               $sql = $this->db->query("INSERT into abot_wallet (user_id, pending) values (?,?)", array($u_id,$abot_amount));
                               if($sql){
                                   $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchange_earned_Wallet_ARB', '-'.$arb_selected_left, $get_active_exchange_earned_wallet->activeArb, "Transfer ARB from earned Wallet to aBot"));
                                   $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", array($u_id,'abotWallet_pending',$abot_amountadded, 0, "Credit $ to aBot Pending", $arb_in_usd));
                               }
                           }
                       }
                   }
                }
                echo json_encode(array('success'=>'1', 'msg' => 'Transfered to abot successfully.')); exit;
            }
            else{
                $abot_amount = $arb_selected * $arb_in_usd;
                $abot_amountadded = $abot_amount;
                
                if(!empty($arb_selected) && $arb_selected > 0 && $abot_amount > 0){
                   $get_active_exchange_earned_wallet = $this->db->query("Select * from exchange_earned_wallet WHERE user_id = ?", array($u_id))->row();
                   
                   if($get_active_exchange_earned_wallet->activeArb >= $arb_selected){
                       
                       $new_active_exchange_earned_wallet = $get_active_exchange_earned_wallet->activeArb - $arb_selected;
                       $new_active = $this->db->query("update exchange_earned_wallet SET activeArb = ? WHERE id = ?", array($new_active_exchange_earned_wallet, $get_active_exchange_earned_wallet->id));
                       
                       $qry1 = $this->db->query("Select * from abot_wallet WHERE user_id = ?", array($u_id))->row();
                       if(isset($qry1->id) && $qry1->id != ''){
                           $abot_amount = $abot_amount + $qry1->pending;
                           $sql = $this->db->query("update abot_wallet SET pending = ? WHERE id = ?", array($abot_amount, $qry1->id));
                           if($sql){
                               $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchange_earned_Wallet_ARB', '-'.$arb_selected, $get_active_exchange_earned_wallet->activeArb, "Transfer ARB from earned Wallet to aBot"));
                               $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", array($u_id,'abotWallet_pending',$abot_amountadded, $qry1->pending, "Credit $ to aBot Pending", $arb_in_usd));
                           }
                       }else{
                           $sql = $this->db->query("INSERT into abot_wallet (user_id, pending) values (?,?)", array($u_id,$abot_amount));
                           if($sql){
                               $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchange_earned_Wallet_ARB', '-'.$arb_selected, $get_active_exchange_earned_wallet->activeArb, "Transfer ARB from earned Wallet to aBot"));
                               $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", array($u_id,'abotWallet_pending',$abot_amountadded, 0, "Credit $ to aBot Pending", $arb_in_usd));
                           }
                       }
                   }
               }
            }
            echo json_encode(array('success'=>'1', 'msg' => 'Transfered to abot successfully.')); exit;
       }else{
           echo json_encode(array('error'=>'1', 'msg' => 'Session Expired.')); exit;
       }
    }
    
    // transfer from exchange to system wallet
    public function exchange_to_wallet(){
        // echo 'This feature is currently disabled by admin, be patient.';
        // exit;
        // for pro plus user request
        
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $currency = $this->input->post('currency');
            $amount = doubleval($this->input->post('amount'));
            // if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('exchange_lock'))->row()->lock_status == 1 && $u_id != 13870){
                //         echo json_encode(array('error'=>'1', 'msg' => 'Lock by admin.')); exit;
                // } 
                
            
            if($currency == 'ARB'){
                if($amount < 1){echo json_encode(array('error'=>'1', 'msg' => 'Minimum amount of transfer is 1 ARB.')); exit;}
                $get_exchange_arb = $this->db->query("SELECT activeArb FROM exchange_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
                
                if($get_open_sellorders = $this->DB2->query("SELECT sum(amount) as orders_arb_sum FROM orders WHERE user_id = ? AND order_type = ? AND status = ? AND order_from = 'exchange'", array($u_id, 'Sell', 0))->row()){
                    $expected_amount =  $get_exchange_arb - $get_open_sellorders->orders_arb_sum;
                    
                    if($expected_amount >= $amount){
                        $update_exchange_arb = $get_exchange_arb - $amount;
                        $this->db->query("UPDATE exchange_wallet SET activeArb = ? WHERE user_id = ?", array($update_exchange_arb, $u_id));
                        
                        $get_system_active_arb = $this->db->query("SELECT activeArb FROM system_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
                        $system_update_arb = $get_system_active_arb + $amount;
                        $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($system_update_arb, $u_id));
                        
                        $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchangeWallet_ARB','-'.$amount,$get_exchange_arb, "Transfer ARB from Exchange to Wallet"));
                        $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'systemWallet_ARB',$amount,$get_system_active_arb, "Credit ARB in Wallet from Exchange"));
                        
                        echo json_encode(array('success'=>'1', 'msg' => 'Transfered to wallet successfully.')); exit;
                    }else{
                        echo json_encode(array('error'=>'1', 'msg' => 'Not enough balance in your exchange.')); exit;
                    }
                }else{echo json_encode(array('error'=>'1', 'msg' => 'Something went wrong.')); exit;}
            }else if($currency == 'ETH'){
                if($amount < 0.01){echo json_encode(array('error'=>'1', 'msg' => 'Minimum amount of transfer is 0.01 ETH.')); exit;}
                $get_exchange_eth = $this->db->query("SELECT activeEth FROM exchange_wallet WHERE user_id = ?", array($u_id))->row()->activeEth;
                
                if($get_open_buyorders = $this->DB2->query("SELECT sum(price*amount) as orders_eth_sum FROM orders WHERE user_id = ? AND order_type = ? AND status = ?", array($u_id, 'Buy', 0))->row()){
                    $expected_amount = $get_exchange_eth - $get_open_buyorders->orders_eth_sum;
                    
                    if($expected_amount >= $amount){
                        $update_exchange_eth = $get_exchange_eth - $amount;
                        $this->db->query("UPDATE exchange_wallet SET activeEth = ? WHERE user_id = ?", array($update_exchange_eth, $u_id));
                        
                        $get_system_active_eth = $this->db->query("SELECT activeEth FROM system_wallet WHERE user_id = ?", array($u_id))->row()->activeEth;
                        $system_update_eth = $get_system_active_eth + $amount;
                        $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($system_update_eth, $u_id));
                        
                        $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'exchangeWallet_ETH','-'.$amount, $get_exchange_eth, "Transfer ETH from Exchange to Wallet"));
                        $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'systemWallet_ETH',$amount, $get_system_active_eth, "Credit ETH in Wallet from Exchange"));
                        
                        echo json_encode(array('success'=>'1', 'msg' => 'Transfered to wallet successfully.')); exit;
                    }else{
                        echo json_encode(array('error'=>'1', 'msg' => 'Not enough balance in your exchange.')); exit;
                    }
                }else{echo json_encode(array('error'=>'1', 'msg' => 'Something went wrong.')); exit;}
            }else{
                echo json_encode(array('error'=>'1', 'msg' => 'No such a curreny math in our system.')); exit;
            }
            
        }else{
            echo json_encode(array('error'=>'1', 'msg' => 'User session expired.')); exit;
        }
    }
    
    // transfer from exearnedWallet to system wallet
    public function exEarned_to_system_wallet(){
        
         $u_id = $this->session->userdata('u_id');
         if($u_id != ''){
               $exearned_transfer_amount = abs($this->getTruncatedValue(doubleval($this->input->post('exearned_transfer_amount')), 4));
               if(!empty($exearned_transfer_amount) && $exearned_transfer_amount > 0){
                   $get_activeArb_from_exearned_wallet = $this->db->query("SELECT activeArb FROM exchange_earned_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
                   if($get_activeArb_from_exearned_wallet > 0 && $get_activeArb_from_exearned_wallet >= $exearned_transfer_amount){
                       
                       $update_amount = $get_activeArb_from_exearned_wallet - $exearned_transfer_amount;
                       $this->db->query("UPDATE exchange_earned_wallet SET activeArb = ? WHERE user_id = ?", array($update_amount, $u_id));
                      
                       if($get_system_active_arb = $this->db->query("SELECT activeArb FROM system_wallet WHERE user_id = ?", array($u_id))->row()){
                           $updated_system_active_arb = $get_system_active_arb->activeArb + $exearned_transfer_amount;
                           $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($updated_system_active_arb, $u_id));
                       }else{
                           $updated_system_active_arb = $exearned_transfer_amount;
                           $this->db->query("INSERT INTO system_wallet (user_id, activeArb) VALUES (?,?)", array($u_id, $updated_system_active_arb));
                       }
                       
                       $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc,comment) values (?,?,?,?,?)", array($u_id,'exEarnedWallet_ARB','-'.$exearned_transfer_amount, $get_activeArb_from_exearned_wallet, "Transfer exEarned ARB to System Wallet"));
                       $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc,comment) values (?,?,?,?,?)", array($u_id,'systemWallet_ARB',$exearned_transfer_amount, $get_system_active_arb->activeArb, "Credit ARB in System Wallet from exEarnedWallet"));
                       
                       echo json_encode(array('success'=>'1', 'msg' => 'Transfered to wallet successfully.')); exit;
                       
                   }else{
                       echo json_encode(array('error'=>'1', 'msg' => 'Not enough balance in your wallet.')); exit;
                   }
               }else{
                   echo json_encode(array('error'=>'1', 'msg' => 'Invalid Amount')); exit;
               }
               
         }else{
             echo json_encode(array('error'=>'1', 'msg' => 'Session Expired')); exit;
         }
    }
    
    // transfer from abot_earned to vault
    public function abot_earned_to_vault(){
        // if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('abot_lock'))->row()->lock_status == 1){
        //     echo 'Lock by admin'; exit;
        // } 
        
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($this->db->query("SELECT count(*) as tot FROM vault_req WHERE user_id = ?", array($u_id))->row()->tot > 0){
                echo  'false';
                exit;
            }
            // get ARB to transfer from wallet to Vault min amount 5 ARB
            $amount = $this->getTruncatedValue(abs($this->input->post('abot_earned_transfer_amount')), 4);
            if($amount < 5){
                echo  'false';
                exit;
            }
            
            // check user abot wallet
            $user_abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
            if($user_abot_wallet->earned >= $amount){
                // update user abot wallet
                $user_updated_arb = $user_abot_wallet->earned - $amount;
                $user_updated_arb = $this->getTruncatedValue($user_updated_arb, 4);
                $this->db->query("UPDATE abot_wallet SET earned = ? WHERE user_id = ?", array($user_updated_arb, $u_id));
                
                // insert logs
                $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'abotWallet_ARB','-'.$amount, $user_abot_wallet->earned, "Transfer abot_earned from abot_wallet to vault (Processing...)"));
                $log_id = $this->db->insert_id();
                // generate request
                $this->db->query("INSERT INTO vault_req (user_id, amount, type, log_id, wallet) VALUES (?,?,?,?,?)", array($u_id, $amount, 'in', $log_id, 'internal'));
                
                echo  'true';
                exit;
                
            }else{
                echo  'false';
                exit;
            }
            
        }else{
            echo  'false';
                exit;
        }
        
    }
    
    
    
    public function abot_active_to_stop_abot_wallet(){
         $u_id = $this->session->userdata('u_id');
        if($this->db->query("SELECT * FROM admin_locks WHERE name = 'abot_lock'")->row()->lock_status == 1 && $u_id != 13870){
            echo json_encode(array('error'=>'1', 'msg'=>'This feature is locked')); exit;
        }
       
        $per = $this->input->post('stop_percent');
        if($per == 1){
            if($this->db->query("SELECT * FROM auction_req WHERE user_id = ?", array($u_id))->row()){
                echo json_encode(array('error'=>'1', 'msg' => "Your account is under auction process.")); exit;
            }
            $abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
            $now = date('Y-m-d H:i:s');
            $add_1days = date('Y-m-d 00:00:00', strtotime('+24 hour', strtotime($abot_wallet->stop_abot_time)));
            if($add_1days > $now){
                echo json_encode(array('error'=>'1', 'msg'=>'You are not able to stop aBOT before '.date('d M Y H:i', strtotime($add_1days)))); exit;
            }else{
                if($abot_wallet->active <= 0){echo json_encode(array('error'=>'1', 'msg' => "Not enough balance.")); exit;}
                $get_1per = ($abot_wallet->active/100);
                if($get_1per <= 0.001){echo json_encode(array('error'=>'1', 'msg' => "Something went wrong.")); exit;}
                if($get_1per > 2000){$get_1per = 2000;}
                //deduct from abot wallet
                $update_abot_wallet = $abot_wallet->active - $get_1per;
                if($update_abot_wallet < 0.001){$update_abot_wallet = 0;}
                $this->db->query("UPDATE abot_wallet SET active = ?, stop_abot_time = ? WHERE user_id = ?", array($update_abot_wallet, $now, $u_id));
                
                // add into stop abot wallet 
                $arb_in_usd = $this->calculate_arb_abot_price($dollar_selected, 'out');
                $get_1per_arb = $get_1per/$arb_in_usd;
                $fee = ($get_1per_arb/100)*5;
                
                if($stop_abot_wallet = $this->db->query("SELECT * FROM stop_abot_wallet WHERE user_id = ?", array($u_id))->row()){
                    $stop_update_arb = $stop_abot_wallet->activeArb + ($get_1per_arb - $fee);
                    $this->db->query("UPDATE stop_abot_wallet SET activeArb = ? WHERE user_id = ?", array($stop_update_arb, $u_id));
                    $last_blnc = $stop_abot_wallet->activeArb;
                }else{
                    $this->db->query("INSERT INTO stop_abot_wallet (user_id, activeArb) values (?,?)", array($u_id, ($get_1per_arb - $fee)));
                    $last_blnc = 0;
                }
                
                $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", 
                    array($u_id,'abotWallet_active','-'.$get_1per, $abot_wallet->active, "Transfer $ from aBot active to Stop aBOT Wallet (1%)", $arb_in_usd));
    			$log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", 
    			    array($u_id,'stop_abot_Wallet_ARB',$get_1per_arb, $last_blnc, "Credit ARB in Stop aBot Wallet", $arb_in_usd));
    			    
    			$log3 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", 
    			    array($u_id,'stop_abot_Wallet_ARB_fee',$fee, ($last_blnc + $get_1per_arb), "Fee"));
    		    
    		    $this->db->query("INSERT INTO stop_abot_data (user_id, comment, wallet, value, price) VALUES (?,?,?,?,?)", array($u_id, 'stop abot', 'Stop aBOT wallet', ($get_1per_arb - $fee), $arb_in_usd));
    		    
    		    echo json_encode(array('success'=>'1', 'msg'=>'1% transfer to your stop aBOT wallet')); exit;
            }
        }else if($per == 10){
            if($this->db->query("SELECT * FROM auction_req WHERE user_id = ?", array($u_id))->row()){
                echo json_encode(array('error'=>'1', 'msg' => "Your account is under auction process.")); exit;
            }
            $abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
            $now = date('Y-m-d H:i:s');
            $add_30days = date('Y-m-d H:i:s', strtotime('+30 days', strtotime($abot_wallet->stop_abot_time_10)));
            if($add_30days > $now){
                echo json_encode(array('error'=>'1', 'msg'=>'You are not able to stop aBOT before '.date('d M Y H:i', strtotime($add_30days)))); exit;
            }else{
                if($abot_wallet->active <= 0){echo json_encode(array('error'=>'1', 'msg' => "Not enough balance.")); exit;}
                $get_10per = ($abot_wallet->active/100)*10;
                if($get_10per <= 0.001){echo json_encode(array('error'=>'1', 'msg' => "Something went wrong.")); exit;}
                if($get_10per > 20000){$get_10per = 20000;}
                //deduct from abot wallet
                $update_abot_wallet = $abot_wallet->active - $get_10per;
                if($update_abot_wallet < 0.001){$update_abot_wallet = 0;}
                $this->db->query("UPDATE abot_wallet SET active = ?, stop_abot_time_10 = ? WHERE user_id = ?", array($update_abot_wallet, $now, $u_id));
                
                // add into stop abot wallet 
                $arb_in_usd = $this->calculate_arb_abot_price($dollar_selected, 'out');
                $get_10per_arb = $get_10per/$arb_in_usd;
                $fee = ($get_10per_arb/100)*5;
                
                if($stop_abot_wallet = $this->db->query("SELECT * FROM stop_abot_wallet WHERE user_id = ?", array($u_id))->row()){
                    $stop_update_arb = $stop_abot_wallet->activeArb + ($get_10per_arb - $fee);
                    $this->db->query("UPDATE stop_abot_wallet SET activeArb = ? WHERE user_id = ?", array($stop_update_arb, $u_id));
                    $last_blnc = $stop_abot_wallet->activeArb;
                }else{
                    $this->db->query("INSERT INTO stop_abot_wallet (user_id, activeArb) values (?,?)", array($u_id, ($get_10per_arb - $fee)));
                    $last_blnc = 0;
                }
                
                $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", 
                    array($u_id,'abotWallet_active','-'.$get_10per, $abot_wallet->active, "Transfer $ from aBot active to Stop aBOT Wallet (10%)", $arb_in_usd));
    			$log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", 
    			    array($u_id,'stop_abot_Wallet_ARB',$get_10per_arb, $last_blnc, "Credit ARB in Stop aBot Wallet", $arb_in_usd));
    			    
    			$log3 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", 
    			    array($u_id,'stop_abot_Wallet_ARB_fee',$fee, ($last_blnc + $get_10per_arb), "Fee"));
    		    
    		    $this->db->query("INSERT INTO stop_abot_data (user_id, comment, wallet, value, price) VALUES (?,?,?,?,?)", array($u_id, 'stop abot', 'Stop aBOT wallet', ($get_10per_arb - $fee), $arb_in_usd));
    		    
    		    
    		    echo json_encode(array('success'=>'1', 'msg'=>'10% transfer to your stop aBOT wallet')); exit;
            }
        }else if($per == 100){
            // block for 300 users that are at audit 50%
            if(in_array($u_id, $this->users_300)){
                echo json_encode(array('error'=>'1', 'msg'=>'You are not able to use this feature.')); exit;
            }
            
            if($this->db->query("SELECT * FROM admin_locks WHERE name = 'auction_lock'")->row()->lock_status == 1 && $u_id != 13870){
                echo json_encode(array('error'=>'1', 'msg'=>'This feature is locked')); exit;
            }
            
            if($this->db->query("SELECT * FROM auction_req WHERE user_id = ?", array($u_id))->row()){
                echo json_encode(array('error'=>'1', 'msg' => "Request already Submitted.")); exit;
            }else{
                if($user = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row()){
                    $account_id = $user->u_username."-".$user->u_id;
                    
                    $user_abot = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($user->u_id))->row();
                    if($user_abot->active < 250){
                        echo json_encode(array('error'=>'1', 'msg' => "Your active balance is less then 250.")); exit;
                    }
                    //deduct 5 % fee
                    if($user_abot->auction_count == 1 || $user_abot->auction_count == 2){
                        if($user_abot->auction_count == 1){$new_auction_count = 2;}else if($user_abot->auction_count == 2){$new_auction_count = 0;}
                        $this->db->query("UPDATE abot_wallet SET auction_count = ? WHERE user_id = ?", array($new_auction_count, $u_id));
                        $this->db->query("INSERT into auction_logs (user_id, type, value, comment) values (?,?,?,?)", array($u_id, 'Auction_request', 0, 'User request for auction without fee'));
                        $fee = 0;
                    }else{
                        //deduct 5 % fee
                        $fee = ($user_abot->active / 100) * 5;
                        $this->db->query("UPDATE abot_wallet SET active = ?, auction_count = ? WHERE user_id = ?", array(($user_abot->active - $fee), 1, $u_id));
                        //log
                        $this->db->query("INSERT into auction_logs (user_id, type, value, comment) values (?,?,?,?)", array($u_id, 'Auction_request', $fee, 'User request for auction with fee'));
                        $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id, 'abot_active', '-'.$fee, '5% fee to place aBOT account in auction', $user_abot->active));
                    }
                    if($user_abot){$abot_worth = ($user_abot->active - $fee);}
                    else{$abot_worth = 0;}
                    $system_arb = 0;
                    $exchange_arb = 0;
                    $ex_earned_arb = 0;
                    $pp_arb = 0;
                    $stop_abot_arb = 0;
                    $vault_arb = 0;
                    
                    if($user_abot->audit_per == null){
                        $user_abot->audit_per = 0;
                    }
                    
                    
                    $this->db->query("INSERT into auction_req (user_id, account_id, abot_worth, system_arb, exchange_arb, ex_earned_arb, pp_arb, stop_abot_arb, vault_arb, fee_deducted, audit) values (?,?,?,?,?,?,?,?,?,?,?)", 
                    array($user->u_id, $account_id, $this->getTruncatedValue($abot_worth, 4), $this->getTruncatedValue($system_arb, 4), $this->getTruncatedValue($exchange_arb, 4), 
                    $this->getTruncatedValue($ex_earned_arb, 4), $this->getTruncatedValue($pp_arb, 4), $this->getTruncatedValue($stop_abot_arb, 4), $this->getTruncatedValue($vault_arb, 4), $fee, $user_abot->audit_per));
                    //$this->db->query("INSERT into auction_logs (user_id, type, comment) values (?,?,?)", array($user->u_id, 'account_auction_request', 'User requested for auction his/her account.'));

                    echo json_encode(array('success'=>'1', 'msg' => "Your auction request is submitted successfully."));exit;
                }
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'Please select correct input')); exit;
        }
        
    }
    
    
    
    
    ////transfer abot to stop_abot_wallet
    public function abot_active_to_stop_abot_wallet_oold(){
        echo 'Lock by admin';
        exit;
        
        $u_id = $this->session->userdata('u_id');
        if(empty($this->db->query("SELECT * FROM stop_abot_wallet WHERE user_id = ?", array($u_id))->result())){
            $this->db->query("INSERT INTO stop_abot_wallet (user_id) values ($u_id)");
        }

    	// for pro plus user request
    	if(!empty($this->input->post('get_from')) && $this->input->post('get_from') == 'reqfromproplususerstopabot'){
    		$token = hash('sha256', $this->input->post('time_stamp').$this->input->post('u_id'));
    		if($token == $this->input->post('token')){
    			$per_age = $this->input->post('active_arb_per');
    			$u_id = $this->input->post('u_id');
    			if($per_age < 0 || $per_age > 100){
    				echo 'invalid %age'; exit;
    			}
    			if($user_abot_date = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row()){
    				$_POST['dollar_selected'] = ($user_abot_date->active / 100) * $per_age;
    			}else{
    				echo 'no abot record'; exit;
    			}
    		}else{
    			echo 'unknown request';
    			exit;
    		}
    	}else{
    		$u_id = $this->session->userdata('u_id');
    	}
    
    	if($u_id != ''){
    		// check lock time here 
    		$abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
    		if($abot_wallet->abot_lock_status == 1){
    			$now = date('Y-m-d H:i:s');
    			$block_days = '+'.$abot_wallet->lock_days.' days';
    			$block_time = date('Y-m-d H:i:s', strtotime($block_days, strtotime($abot_wallet->lock_time)));
    			if($now < $block_time){
    				echo 'false'; exit;
    			}else{
    				$this->db->query("UPDATE abot_wallet SET abot_lock_status = ? WHERE user_id = ?", array(0, $u_id));
    			}
    		}
    		$dollar_selected = doubleval($this->input->post('dollar_selected'));
    
    		$arb_in_usd = $this->calculate_arb_abot_price($dollar_selected, 'out');
    
    		if($arb_in_usd <= 0){
    			exit;
    		}
    
    		$abot_active_transfer_amount = $dollar_selected / $arb_in_usd;
    
    		if(!empty($dollar_selected) && $dollar_selected > 0 && $abot_active_transfer_amount > 0){
    			$get_active_from_abot = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
    			if($get_active_from_abot->active > 0 && $get_active_from_abot->active >= $dollar_selected){
    				$before_24hour = date("Y-m-d H:i:s", strtotime('-24 hour'));
    				if($get_active_from_abot->pending_date > $before_24hour){
    					echo "false"; exit;
    				}
    				$abot_update_amount = $get_active_from_abot->active - $dollar_selected;
    				$now = date('Y-m-d H:i:s');
    				$this->db->query("UPDATE abot_wallet SET active = ?, stop_abot_time = ? WHERE user_id = ?", array($abot_update_amount, $now, $u_id));
    
    				$get_stop_active_arb = $this->db->query("SELECT activeArb FROM stop_abot_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
    
    				// 0.5 % fee on stop abot
    				$stopabot_fee = $abot_active_transfer_amount * 0.04;
    				$abot_arb_afterfee = $abot_active_transfer_amount - $stopabot_fee;
    
    				$stop_update_arb = $get_stop_active_arb + $abot_arb_afterfee;
    				$this->db->query("UPDATE stop_abot_wallet SET activeArb = ? WHERE user_id = ?", array($stop_update_arb, $u_id));
    
    				// stop_abot_data_record
    				$this->db->query("INSERT into stop_abot_data (user_id, comment, wallet, value, price, date_time) values (?,?,?,?,?,?)", array($u_id, 'stop aBot', 'Earned Wallet', $abot_arb_afterfee, $arb_in_usd, $now));
    
    				//update admin wallet
    				$get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1 LIMIT 1")->row();
    				$admin_new_arb = $get_admin_wallet->arb + $stopabot_fee;
    				$this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = ?", array($admin_new_arb, 1));
    
    				$log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'abotWallet_active','-'.$dollar_selected, $get_active_from_abot->active, "Transfer $ from aBot active to Stop aBOT Wallet"));
    				$log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'systemWallet_ARB',$abot_active_transfer_amount, $get_stop_active_arb, "Credit ARB in Stop aBot Wallet"));
    				$log3 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'stop_aBotFee','-'.$stopabot_fee, ($get_stop_active_arb+$abot_active_transfer_amount), "Fee"));
    
    				echo "true"; exit;
    
    			}else{
    				echo "false"; exit;
    			}
    		}else{
    			echo 'false'; exit;
    		}
    	}else{
    		echo 'false'; exit;
    	}
    }
    
    //// stop_abot_wallet_to_abot pending
    public function stop_abot_wallet_to_abot(){
    //echo 'Lock by admin';
    //exit;
    
	$u_id = $this->session->userdata('u_id');
	
	     
        $arr = str_split($u_id); // convert string to an array
        $last_digit  = end($arr);
        
        //  if($last_digit == 4 || $last_digit == 5){
        //          echo "Lock by admin";
        //         exit;
        //     } 
            
	if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('abot_lock'))->row()->lock_status == 1){
		echo json_encode(array('error'=>'1', 'msg' => 'Lock by admin.')); exit;
	} 
	if($u_id != ''){
		$arb_selected = abs($this->input->post('amount'));
		$arb_in_usd = $this->calculate_arb_abot_price($arb_selected, 'in');
		if($arb_in_usd <= 0){
			exit;
		}
		if($user_stop_abot_data = $this->db->query("SELECT * FROM stop_abot_data WHERE user_id = ? ORDER BY price ASC", array($u_id))->result()){
			$arb_selected_left = $arb_selected;
		
			foreach($user_stop_abot_data as $one){
				if($arb_selected_left >= $one->value){
					$arb_selected_left = $arb_selected_left - $one->value;
					
					$abot_amount = $one->value * $one->price;
					$abot_amountadded = $abot_amount;
					
					$this->start_abot($u_id, $one->value, $abot_amount, $abot_amountadded, $one->price, 'stop_abot_wallet');
					
					$this->db->query("DELETE FROM stop_abot_data WHERE id = $one->id");
				
				}
				else{
					$value_left = $one->value - $arb_selected_left;
					$abot_amount = $arb_selected_left * $one->price;
					$abot_amountadded = $abot_amount;
					
					$this->start_abot($u_id, $arb_selected_left, $abot_amount, $abot_amountadded, $one->price, 'stop_abot_wallet');
					
					$arb_selected_left=0;
					$this->db->query("UPDATE stop_abot_data SET value = ? WHERE id = ?", array($value_left, $one->id));
				}
			}
			if($arb_selected_left > 0){
				$abot_amount = $arb_selected_left * $arb_in_usd;
				$abot_amountadded = $abot_amount;
				
				$this->start_abot($u_id, $arb_selected_left, $abot_amount, $abot_amountadded, $arb_in_usd, 'stop_abot_wallet');
				
				echo json_encode(array('success'=>'1', 'msg' => 'Transfered to abot successfully.')); exit;
			}
			
			echo json_encode(array('success'=>'1', 'msg' => 'Transfered to abot successfully.')); exit;
		}
		else{
			$abot_amount = $arb_selected * $arb_in_usd;
			$abot_amountadded = $abot_amount;
			
			$this->start_abot($u_id, $arb_selected, $abot_amount, $abot_amountadded, $arb_in_usd, 'stop_abot_wallet');
			
			echo json_encode(array('success'=>'1', 'msg' => 'Transfered to abot successfully.')); exit;
		}
	}else{
	   echo json_encode(array('error'=>'1', 'msg' => 'Session Expired.')); exit;
	}
}

    // stop_abot_wallet_to_external wallet
    public function stop_abot_wallet_to_external_wallet(){
	    $u_id = $this->session->userdata('u_id');
	
    	if($u_id != ''){
    		$amount = abs($this->input->post('amount'));
    		if($amount < 1){echo json_encode(array('error'=>'1', 'msg' => 'Minimum amount of transfer is 1 ARB.')); exit;}
                $stop_abot_arb = $this->db->query("SELECT activeArb FROM stop_abot_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
                
                if($get_open_sellorders = $this->DB2->query("SELECT sum(amount) as orders_arb_sum FROM orders WHERE user_id = ? AND order_type = ? AND status = ? AND order_from = ?", array($u_id, 'Sell', 0, 'stop_abot'))->row()){
                    $get_stop_abot_arb =  $stop_abot_arb - $get_open_sellorders->orders_arb_sum;
                    
                    if($get_stop_abot_arb >= $amount){
                        $update_stop_abot_arb = $stop_abot_arb - $amount;
                        if($update_stop_abot_arb < 0 ){$update_stop_abot_arb = 0;} //echo json_encode(array('error'=>'1', 'msg' => 'Something went wrong.')); exit;}
                        
                        if($this->db->query("UPDATE stop_abot_wallet SET activeArb = ? WHERE user_id = ?", array($update_stop_abot_arb, $u_id))){
                            $get_external_wallet_arb = $this->db->query("SELECT activeArb FROM external_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
                            $external_wallet_update_arb = $get_external_wallet_arb + $amount;
                            $this->db->query("UPDATE external_wallet SET activeArb = ? WHERE user_id = ?", array($external_wallet_update_arb, $u_id));
                            
                            $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'stop_abot_Wallet_ARB','-'.$amount,$stop_abot_arb, "Transfer ARB from Stop aBot to External Wallet"));
                            $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'externalWallet_ARB',$amount,$get_external_wallet_arb, "Credit ARB in External Wallet from Stop aBot Wallet"));
                            
                            echo json_encode(array('success'=>'1', 'msg' => 'Transfered to external wallet successfully.')); exit;
                        }else{
                            echo json_encode(array('error'=>'1', 'msg' => 'Something went wrong.')); exit;
                        }
                    }else{
                        echo json_encode(array('error'=>'1', 'msg' => 'Not enough balance in your stop aBot wallet.')); exit;
                    }
                }else{echo json_encode(array('error'=>'1', 'msg' => 'Something went wrong.')); exit;}
    	}else{
    	   echo json_encode(array('error'=>'1', 'msg' => 'Session Expired.')); exit;
    	}
    }
    

    public function start_abot($u_id, $arb_selected, $abot_amount, $abot_amountadded, $abot_price, $from_wallet){
        
        if($this->db->query("SELECT * FROM auction_req WHERE user_id = ?", array($u_id))->row()){
            echo 'false'; exit;
        }
        
    	if(!empty($arb_selected) && $arb_selected > 0 && $abot_amount > 0){
    		$get_active_exchange_earned_wallet = $this->db->query("Select * from ".$from_wallet." WHERE user_id = ?", array($u_id))->row();
    
    		if($get_active_exchange_earned_wallet->activeArb >= $arb_selected){
    		   // max 10000 $ in panding at a time
    		   $testqry = $this->db->query("Select * from abot_wallet WHERE user_id = ?", array($u_id))->row();
    		   $abot_amount_test = $abot_amount + $testqry->pending;
    		   if($abot_amount_test > 10000){
    		       echo 'You are not able to add more then 10000$ in aBOT pending'; exit;
    		   }
    		   
    		   $new_active_exchange_earned_wallet = $get_active_exchange_earned_wallet->activeArb - $arb_selected;
    		   $new_active = $this->db->query("update ".$from_wallet." SET activeArb = ? WHERE id = ?", array($new_active_exchange_earned_wallet, $get_active_exchange_earned_wallet->id));
    		   
    		   $qry1 = $this->db->query("Select * from abot_wallet WHERE user_id = ?", array($u_id))->row();
    		   if(isset($qry1->id) && $qry1->id != ''){
    			   $abot_amount = $abot_amount + $qry1->pending;
    			   $sql = $this->db->query("update abot_wallet SET pending = ? WHERE id = ?", array($abot_amount, $qry1->id));
    			   if($sql){
    				   $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, $from_wallet.'Wallet_ARB', '-'.$arb_selected, $get_active_exchange_earned_wallet->activeArb, "Transfer ARB from ".$from_wallet." to aBot"));
                       $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", array($u_id,'abotWallet_pending',$abot_amountadded, $qry1->pending, "Credit $ to aBot Pending", $abot_price));
    			   }
    		   }else{
    			   $sql = $this->db->query("INSERT into abot_wallet (user_id, pending) values (?,?)", array($u_id,$abot_amount));
    			   if($sql){
    				   $log1 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id, $from_wallet.'Wallet_ARB', '-'.$arb_selected, $get_active_exchange_earned_wallet->activeArb, "Transfer ARB from ".$from_wallet." to aBot"));
                       $log2 = $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment, abot_price) values (?,?,?,?,?,?)", array($u_id,'abotWallet_pending',$abot_amountadded, $qry1->pending, "Credit $ to aBot Pending", $abot_price));
    			   }
    		   }
    		}
    	}
    }



    // admin cradit in system wallet
    public function admin_to_systemwallet(){
       
        $currency = $this->input->post('currency');
        $amount = doubleval($this->input->post('amount'));
        $comment = $this->input->post('comment');
        $u_id = intval($this->input->post('user_id'));
        $admin_name = $this->input->post('adminName');
        $admin_arr = array('Admin', 'Ozi');
        if(!in_array($admin_name, $admin_arr))
        {
            redirect('http://arbblock.com/admin_panel/creditView');
            exit;
        }
        //echo $u_id;
        //exit;
        else
        {
            if($u_id =='' && $u_id <= 0){
            echo 'false'; exit;
            }
            
            if($currency == 'ARB'){
                if($amount > 0){
                    $get_system_active_arb = $this->db->query("SELECT activeArb FROM system_wallet WHERE user_id = ?", array($u_id))->row()->activeArb;
                    $system_update_arb = $get_system_active_arb + $amount;
                    $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($system_update_arb, $u_id));
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id,'systemWallet_admin_add_ARB',$amount, $comment, $get_system_active_arb));
                    // echo "true"; exit;
                    
                    return redirect('http://arbblock.com/admin_panel/creditView');
                    
                }else{
                    echo 'false'; exit;
                }
            }else if($currency == 'ETH'){
                if($amount > 0){
                    $get_system_active_eth = $this->db->query("SELECT activeEth FROM system_wallet WHERE user_id = ?", array($u_id))->row()->activeEth;
                    $system_update_eth = $get_system_active_eth + $amount;
                    $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($system_update_eth, $u_id));
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id,'systemWallet_admin_add_ETH',$amount, $comment, $get_system_active_eth));
                    // echo "true"; exit;
                     return redirect('http://arbblock.com/admin_panel/creditView');
                    
                }else{
                    echo 'false'; exit;
                }
                
            }else{
                echo "false"; exit;
            }    
        }
        
        
    }
    
    // abot reinvest earned to active
    public function abot_reinvest(){
        $u_id = $this->session->userdata('u_id');
             
        $arr = str_split($u_id); // convert string to an array
        $last_digit  = end($arr);
        
        //  if($last_digit == 4 || $last_digit == 5){
        //          echo "Lock by admin";
        //         exit;
        //     }
            
        if($this->db->query("SELECT * FROM admin_locks WHERE name = ?", array('abot_lock'))->row()->lock_status == 1 && $u_id != '13870'){
            echo "Lock by admin";
            exit;
        } 
        // $arb_in_usd = ARB_VALUE_IN_USD;
        
        // $arb_in_usd = doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
        // if($arb_in_usd <= 0){
        //     exit;
        // }
        
        if($u_id != ''){
            $abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
            if($abot_wallet->earned <= 0){
                echo "false"; exit;
            }
            
            $earned_in_db = $abot_wallet->earned;
            
            $arb_in_usd = $this->calculate_arb_abot_price($earned_in_db, 'in');
            if($arb_in_usd <= 0){
                exit;
            }
            
            $earned_arb_to_dollar = ($earned_in_db * $arb_in_usd);
            $new_pending = $abot_wallet->pending + $earned_arb_to_dollar;
            
            $this->db->query("UPDATE abot_wallet SET pending = ?, earned = ? WHERE user_id = ?", array($new_pending, 0, $u_id));
            $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id, 'abotWallet_earned', '-'.$abot_wallet->earned, "aBot reinvest Earned ARB to pending $", $abot_wallet->earned));
            $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id, 'abotWallet_pending', $earned_arb_to_dollar, "Credit $ in aBot pending  arb in usd ( " .$arb_in_usd. " )", $abot_wallet->pending));
	        echo "true"; exit;
        }else{
            echo "false"; exit;
        }
    }
    
    
    /*public function arb_valueLive2(){
        $arb_in_eth = $this->DB2->query("SELECT price FROM orders WHERE order_type = 'Buy' AND status = 1 AND remark = '' ORDER BY created_at DESC")->row()->price;
        $res = file_get_contents("https://api.coinmarketcap.com/v1/ticker/ethereum/");
        $result = json_decode($res);
        
        $arr = array('ETH' => abs($arb_in_eth), 'USD' => abs($arb_in_eth * $result[0]->price_usd));
        
        $arr = json_encode($arr);
        print_r($arr);
    }*/
    
    // public function arb_value(){
        
    //     $limit = 50; 
    //     $arb_in_eth = $this->DB2->query("SELECT price FROM orders WHERE order_type = 'Buy' AND status = 1 AND remark = '' ORDER BY created_at DESC LIMIT ".$limit)->result();
    //     $res = file_get_contents("https://api.coinmarketcap.com/v1/ticker/ethereum/");
    //     $result = json_decode($res);
    //     $total = 0;
    //     foreach($arb_in_eth as $a){
    //         $total = $total + $a->price;
    //     }
        
    //     $avg = $total / $limit;
        
    //     $arb_in_eth = round($avg,6);
    //     $arr = array('ETH' => abs($arb_in_eth), 'USD' => abs($avg * $result[0]->price_usd));
        
    //     $arr = json_encode($arr);
    //     print_r(file_get_contents("https://api.coinmarketcap.com/v1/ticker/ethereum/"));
    // }
    
    
    
    public function abot_arb(){
        header('Access-Control-Allow-Origin: *');
        //less then 100 market 30%        less then 100 limit 5%               
       //greater then 100 market 55%       greater then 100 limit 10%
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        
        if ( ! $foo_abot = $this->cache->get('foo_abot'))
        {
     
       
       //less then 50 limit orders 1
        if($lessthen50limit = $this->DB2->query("SELECT * FROM orders WHERE order_type = 'Buy' AND order_category LIKE 'limit' AND status = 1 AND remark = '' AND amount < 50 ORDER BY created_at DESC LIMIT 70")->result()){
            $sum1 = 0;
            $count1 = 0;
            foreach($lessthen50limit as $order){
                $sum1 = $sum1 + $order->price;
                $count1++;
            }
            $lessthen50limitavg = $sum1 /$count1;
        }else{
            $lessthen50limitavg = 0;
        }
        
        //less then 50 market orders 6
        if($lessthen50market = $this->DB2->query("SELECT * FROM orders WHERE order_type = 'Buy' AND order_category LIKE 'market' AND status = 1 AND remark = '' AND amount < 50 ORDER BY created_at DESC LIMIT 70")->result()){
            $sum2 = 0;
            $count2 = 0;
            foreach($lessthen50market as $order){
                $sum2 = $sum2 + $order->price;
                $count2++;
            }
            $lessthen50marketavg = $sum2 /$count2;
        }else{
            $lessthen50marketavg = 0;
        }
        
        
        //greater then 50 limit orders 2
        if($greaterthen50limit = $this->DB2->query("SELECT * FROM orders WHERE order_type = 'Buy' AND order_category LIKE 'limit'  AND status = 1 AND remark = '' AND amount >= 50 ORDER BY created_at DESC LIMIT 70")->result()){
            $sum3 = 0;
            $count3 = 0;
            foreach($greaterthen50limit as $order){
                $sum3 = $sum3 + $order->price;
                $count3++;
            }
            $greaterthen50limitavg = $sum3 /$count3;
        }else{
            $greaterthen50limitavg = 0;
        }
        
        //greater then 50 market orders 11
        if($greaterthen50market = $this->DB2->query("SELECT * FROM orders WHERE order_type = 'Buy' AND order_category LIKE 'market'  AND status = 1 AND remark = '' AND amount >= 50 ORDER BY created_at DESC LIMIT 70")->result()){
            $sum4 = 0;
            $count4 = 0;
            foreach($greaterthen50market as $order){
                $sum4 = $sum4 + $order->price;
                $count4++;
            }
            $greaterthen50marketavg = $sum2 /$count2;
        }else{
            $greaterthen50marketavg = 0;
        }
    
    
        $per25 = ($lessthen50marketavg / 100)*30;
        $per10 = ($lessthen50limitavg / 100)*5;
        $per45 = ($greaterthen50marketavg / 100)*55;
        $per20 = ($greaterthen50limitavg / 100)*10;
        
        
        
        $arb_in_eth = $per25 + $per10 + $per45 + $per20; 
       if($arb_in_eth < 0.005){
           $arb_in_eth = 0.005;
       }
       
        //$arb_in_eth = 0.018; //$this->DB2->query("SELECT * FROM orders WHERE order_type = 'Buy' AND ")
        
        // $last_block = $this->DB2->query("SELECT * FROM blocks_history where status = 1 ORDER BY complete_at DESC LIMIT 1")->row();
        // $last_block = json_decode($last_block->data);
        // $arb_in_eth = $last_block->price;
       
      
       
       $eth_value_db = $this->db->query("SELECT * FROM api_settings WHERE name = ?", array('eth_dollor_value'))->row()->value;
       if($eth_value_db < 70){
           return false; exit;
       }
       $arb_in_usd = $arb_in_eth * $eth_value_db; //$result->data->quotes->USD->price;
        
        
        $foo_abot = $arb_in_usd;
        $this->cache->save('foo_abot', $foo_abot, 60);
        }
        echo  $foo_abot;
        
        
        
         
    }
    
    public function arb_valueLive(){
        header('Access-Control-Allow-Origin: *');
        
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        
        if ( ! $foo = $this->cache->get('foo')){
            
            //less then 50 limit orders 1
            if($lessthen50limit = $this->DB2->query("SELECT * FROM orders WHERE order_type = 'Buy' AND order_category LIKE 'limit' AND status = 1 AND remark = '' AND amount < 25 ORDER BY created_at DESC LIMIT 10")->result()){
                $sum1 = 0;
                $count1 = 0;
                foreach($lessthen50limit as $order){
                    $sum1 = $sum1 + $order->price;
                    $count1++;
                }
                $lessthen50limitavg = $sum1 /$count1;
            }else{
                $lessthen50limitavg = 0;
            }
            
            //less then 50 market orders 6
            if($lessthen50market = $this->DB2->query("SELECT * FROM orders WHERE order_type = 'Buy' AND order_category LIKE 'market' AND status = 1 AND remark = '' AND amount < 25 ORDER BY created_at DESC LIMIT 5")->result()){
                $sum2 = 0;
                $count2 = 0;
                foreach($lessthen50market as $order){
                    $sum2 = $sum2 + $order->price;
                    $count2++;
                }
                $lessthen50marketavg = $sum2 /$count2;
            }else{
                $lessthen50marketavg = 0;
            }
            
            
            //greater then 50 limit orders 2
            if($greaterthen50limit = $this->DB2->query("SELECT * FROM orders WHERE order_type = 'Buy' AND order_category LIKE 'limit'  AND status = 1 AND remark = '' AND amount >= 25 ORDER BY created_at DESC LIMIT 15")->result()){
                $sum3 = 0;
                $count3 = 0;
                foreach($greaterthen50limit as $order){
                    $sum3 = $sum3 + $order->price;
                    $count3++;
                }
                $greaterthen50limitavg = $sum3 /$count3;
            }else{
                $greaterthen50limitavg = 0;
            }
            
            //greater then 50 market orders 11
            if($greaterthen50market = $this->DB2->query("SELECT * FROM orders WHERE order_type = 'Buy' AND order_category LIKE 'market'  AND status = 1 AND remark = '' AND amount >= 25 ORDER BY created_at DESC LIMIT 5")->result()){
                $sum4 = 0;
                $count4 = 0;
                foreach($greaterthen50market as $order){
                    $sum4 = $sum4 + $order->price;
                    $count4++;
                }
                $greaterthen50marketavg = $sum2 /$count2;
            }else{
                $greaterthen50marketavg = 0;
            }
        
        
            $per25 = ($lessthen50marketavg / 100)*25;
            $per10 = ($lessthen50limitavg / 100)*10;
            $per45 = ($greaterthen50marketavg / 100)*55;
            $per20 = ($greaterthen50limitavg / 100)*10;
            
            
            
            $arb_in_eth = $per25 + $per10 + $per45 + $per20; 
        //   if($arb_in_eth < 0.018){
        //       $arb_in_eth = 0.018;
        //   }
            
            
            // $last_block = $this->DB2->query("SELECT * FROM blocks_history where status = 1 AND flag = 'sell_side' ORDER BY complete_at DESC LIMIT 1")->row();
            // $last_block = json_decode($last_block->data);
            
            //$last_block = $this->DB2->query("SELECT * FROM blocks where status = 0 AND flag = 'buy_side' ORDER BY price DESC LIMIT 1")->row();
            // $arb_in_eth = $last_block->price;
            
            //$arb_in_eth = ($lessthen50marketavg + $lessthen50limitavg + $greaterthen50marketavg + $greaterthen50limitavg) / 4;
           
           
            //$res = file_get_contents("https://api.coinmarketcap.com/v2/ticker/1027/");
            //$result = json_decode($res);
            
            $eth_value_db = $this->db->query("SELECT * FROM api_settings WHERE name = ?", array('eth_dollor_value'))->row()->value;
            
            // $price_limit_arb_in_usd = 1/$eth_value_db;
            // if($arb_in_eth < $price_limit_arb_in_usd){
            //     $arb_in_eth = $price_limit_arb_in_usd;
            // }
            
            $arb_in_usd = $arb_in_eth * $eth_value_db; //$result->data->quotes->USD->price;
            
            // if($arb_in_usd < 1){
            //     $res = file_get_contents("https://api.etherscan.io/api?module=stats&action=ethprice");
            //     $result = json_decode($res);
            //     $arb_in_usd = round($arb_in_eth * $result->result->ethusd , 4);
            // }
            
            
            if ( ! $fooVol1 = $this->cache->get('fooVol1')){
           
                $vol1 = $this->DB2->query("SELECT sum(amount*price) as vol1 FROM orders  WHERE order_type = 'Buy' AND status = 1 AND remark != 'cancel' AND created_at > DATE_SUB(NOW(), INTERVAL 1 hour)")->row();
                
                $fooVol1 = $vol1;
                $this->cache->save('fooVol1', $fooVol1, 600);
            }
            
            if ( ! $fooVol24 = $this->cache->get('fooVol24')){
           
                $vol24 = $this->DB2->query("SELECT sum(amount*price) as vol24 FROM orders  WHERE order_type = 'Buy' AND status = 1 AND remark != 'cancel' AND created_at > DATE_SUB(NOW(), INTERVAL 24 hour)")->row();
                $fooVol24 = $vol24;
                $this->cache->save('fooVol24', $fooVol24, 600);
            }

            if($fooVol1->vol1 == '' || $fooVol1->vol1 < 0){$fooVol1->vol1 = 0;}
            if($fooVol24->vol24 == '' || $fooVol24->vol24 < 0){$fooVol24->vol24 = 0;}
            //$arb_abot = doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
            
            /*if($arb_in_usd <= 0){
                $this->arb_valueLive();
                exit;
            }*/
            
           $arb_value = doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
           $arb_abot = $arb_value;
           $arb_db_value = 12;//$this->db->query("SELECT * FROM abot_price WHERE name = ?", array('abot_price'))->row()->value;
           $avg_arb = ($arb_db_value + $arb_value)/2;
           if($arb_value > $arb_db_value){
                $abot_price = $arb_value;
           }else{
               $abot_price = $arb_db_value;
           }
       
            $d = Date('d', strtotime('-5 hour'));
	        $todays_commission = $this->db->query("SELECT value FROM commission WHERE date = ?", array($d))->row()->value;
            
            //vault value total sum
            $vault_sum = $this->db->query("SELECT SUM(activeArb) as total_activeArb FROM vault_wallet")->row()->total_activeArb;
            if($vault_sum < 0.001){
                $vault_sum = 0;
            }
            
            
            if($vault_sum > 300000){
                $above_3 = $vault_sum - 300000;
                $per_80 = ($above_3 /100) * 28;
                $vault_sum = 300000 + $per_80;
            }
            // else if($vault_sum > 500000){
            //     $vault_sum = ($vault_sum /100) * 70;
            // } 'vol24' => $this->getTruncatedValue ($fooVol24->vol24, 3).'', 
            
            $ec_price = $this->db->query("SELECT * FROM api_settings WHERE name = ?", array('coinexchange_arb_usd'))->row()->value;
             
             $fooVol24->vol24 = $fooVol24->vol24 * 1.2;
             
             $arr = array('eth_usd_price' =>$this->getTruncatedValue ($eth_value_db, 3).'', 'ETH' => $this->getTruncatedValue (abs($arb_in_eth), 6).'', 
            'USD' => $this->getTruncatedValue (abs($arb_in_usd), 3).'', 
            'vol24' => $this->getTruncatedValue ($fooVol24->vol24, 3).'',
            'vol1' => $this->getTruncatedValue ($fooVol1->vol1, 3).'', 'abot_usd' => $this->getTruncatedValue ($arb_abot, 3).'' , 
            "c_time" => date('H:i:s') , "abot_stop_price" => $this->getTruncatedValue ($arb_abot, 3).'' , "todays_commission" => $todays_commission, 
            'vault_total_ARB' => $this->getTruncatedValue ($vault_sum, 2), 'coinexchange_price' => $this->getTruncatedValue ($ec_price, 3), 
            "ARB/ETH" => $this->getTruncatedValue (abs($arb_in_eth), 6).'');
            
            
            // $arr = array('eth_usd_price' =>$this->getTruncatedValue ($eth_value_db, 3).'', 'ETH' => $this->getTruncatedValue (abs($arb_in_eth), 6).'', 
            // 'USD' => $this->getTruncatedValue (abs($arb_in_usd), 3).'','abot_usd' => $this->getTruncatedValue ($arb_abot, 3).'' , 
            // "c_time" => date('H:i:s') , "abot_stop_price" => $this->getTruncatedValue ($arb_abot, 3).'' , "todays_commission" => $todays_commission, 
            // 'vault_total_ARB' => $this->getTruncatedValue ($vault_sum, 2), 'coinexchange_price' => $this->getTruncatedValue ($ec_price, 3));
            
            $foo = json_encode($arr);
            $this->cache->save('foo', $foo, 60);
        }
        print_r($foo);

                
    }
    
   public function auto_reinvest(){
        $u_id = $this->session->userdata('u_id');
        $now = date("Y-m-d H:i:s");
        if($u_id != ''){
            $abot = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($u_id))->row();
            if($abot->auto_reinvest == 0){
                $allow_per = array('25','50','75','100');
                $per = $this->input->post('percent');
                if(!in_array($per, $allow_per)){echo json_encode(array('error'=>'1', 'msg'=>'Invalid percentage')); exit;}
                if($this->input->post('lock_reinvest') == 1){
                    $days = $this->input->post('reinvest_lock_days');
                    $allow_days = array('30','60','90');
                    if(!in_array($days, $allow_days)){echo json_encode(array('error'=>'1', 'msg'=>'Invalid days')); exit;}
                    if($per < 50){
                        echo json_encode(array('error'=>'1', 'msg'=>'Reinvest lock not enable below 50% auto reinvest')); exit;
                    }
                    $this->db->query("UPDATE abot_wallet SET auto_reinvest = ?, auto_reinvest_per = ?, auto_reinvest_time = ?, reinvest_lock = ?, reinvest_lock_days = ? WHERE user_id = ?", 
                        array(1, $per, $now, 1, $days, $u_id));
                    if(!$this->db->query("SELECT * FROM abot_eth_bonus WHERE user_id = ?", array($u_id))->row()){
                        $this->db->query("INSERT INTO abot_eth_bonus (user_id, activeEth, status) VALUES (?,?,?)", array($u_id, 0, 1));
                    }
                    echo json_encode(array('success'=>'1', 'msg'=>'Auto reinvest activate successfully')); exit;
                }else{
                    $this->db->query("UPDATE abot_wallet SET auto_reinvest = ?, auto_reinvest_per = ?, auto_reinvest_time = ?, reinvest_lock = ? WHERE user_id = ?", 
                        array(1, $per, $now, 0, $u_id));
                    echo json_encode(array('success'=>'1', 'msg'=>'Auto reinvest activate successfully')); exit;
                }
            }else{
                if($abot->reinvest_lock == 1){$lock_days = $abot->reinvest_lock_days;}else{$lock_days = 7;}
                $afterdays = date('Y-m-d H:i:s', strtotime('+'.$lock_days.' days', strtotime($abot->auto_reinvest_time)));
                if($now > $afterdays){
                    $this->db->query("UPDATE abot_wallet SET eth_payout = 0, auto_reinvest = ?, reinvest_lock = ? WHERE user_id = ?", array(0, 0, $u_id));
                    echo json_encode(array('success'=>'1', 'msg'=>'Auto reinvest deactivate successfully')); exit;
                }else{
                    echo json_encode(array('error'=>'1', 'msg'=>"You are not able to deactivate this feature before ".date('d M,Y H:i:s',strtotime($afterdays)))); exit;
                }
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'Session Expired')); exit;
        }
        
    }
    
    
    public function update_coinexchange_value(){
        echo 'working<br>';
        $content = json_decode(file_get_contents('https://www.coinexchange.io/api/v1/getmarketsummary?market_id=838'), true);
        if(isset($content['result']['AskPrice']) && $content['result']['AskPrice'] > 0.0001){
            $eth_usd_value = $this->db->query("SELECT * FROM api_settings WHERE name = 'eth_dollor_value'")->row()->value;
            $link_price = $content['result']['AskPrice'] * $eth_usd_value;
            
            $db_value = $this->db->query("SELECT * FROM api_settings WHERE name = 'coinexchange_arb_usd'")->row()->value;
            $avg_usd = ($link_price + $db_value) / 2;
            $avg_usd = $this->getTruncatedValue($avg_usd, 3);
            
            
            $this->db->query("UPDATE api_settings SET value = ? WHERE name = ?", array($avg_usd, 'coinexchange_arb_usd'));
            echo 'done';
            
        }
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
    
    
}
	