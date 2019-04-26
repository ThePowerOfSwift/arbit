<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Automation extends MY_Controller {

    //automate abot active
    public function auto_abot_active(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($this->db->query("SELECT * FROM user_add_ons WHERE user_id = ? AND add_on_name = ?", array($u_id, 'Pro+'))->row()){
                $arb_value = doubleval($this->input->post('arb_value'));
                $arb_amount = doubleval($this->input->post('arb_amount'));
                if(!empty($arb_value) && $arb_value > 0){
                    if(!empty($arb_amount) && $arb_amount > 0){
                        if($this->db->query("SELECT * FROM pp_auto_abot_active WHERE u_id = ?", array($u_id))->row()){
                            $this->db->query("UPDATE pp_auto_abot_active SET arb_value = ?, arb_amount = ? WHERE u_id = ?", array($arb_value, $arb_amount, $u_id));
                            echo "Updated";
                            exit;
                        }
                        else{
                           $this->db->query("INSERT into pp_auto_abot_active (u_id, arb_value, arb_amount) values (?,?,?)", array($u_id, $arb_value, $arb_amount));
                           echo "Successfull";
                           exit;
                        }
                    }
                }
            }
        }else{
            echo "Session Expired";
            exit;
        }
    }
    
    //automate stop abot
    public function auto_stop_abot(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($this->db->query("SELECT * FROM user_add_ons WHERE user_id = ? AND add_on_name = ?", array($u_id, 'Pro+'))->row()){
                $arb_value = doubleval($this->input->post('arb_value'));
                $active_arb_per = $this->input->post('active_arb_per');
                if(!empty($arb_value) && $arb_value > 0){
                    if(!empty($active_arb_per) && $active_arb_per > 0 && $active_arb_per <= 100){
                        if($this->db->query("SELECT * FROM pp_auto_stop_abot WHERE u_id = ?", array($u_id))->row()){
                            $this->db->query("UPDATE pp_auto_stop_abot SET arb_value = ?, active_arb_per = ? WHERE u_id = ?", array($arb_value, $active_arb_per, $u_id));
                            echo "Updated";
                            exit;
                        }
                        else{
                           $this->db->query("INSERT into pp_auto_stop_abot (u_id, arb_value, active_arb_per) values (?,?,?)", array($u_id, $arb_value, $active_arb_per));
                           echo "Successfull";
                           exit;
                        }
                    }
                }
            }
        }else{
            echo "Session Expired";
            exit;
        }
    }
    
    //automate selling_or_buying
    public function auto_selling_or_buying(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($this->db->query("SELECT * FROM user_add_ons WHERE user_id = ? AND add_on_name = ?", array($u_id, 'Pro+'))->row()){
                $per_array = array(25, 50, 75, 100);
                $auto_sell_per = $this->input->post('auto_sell_per');
                $withdraw = $this->input->post('withdraw');
                $to_sw = $this->input->post('to_sw');
                if($auto_sell_per == '' && !in_array($auto_sell_per, $per_array)){ echo json_encode(array('error'=>'1', 'msg'=>'Please select valid auto sell percentage.')); exit; }
                if($withdraw == 1){
                    if($this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = ?", array($u_id))->row()){
                        $this->db->query("UPDATE pro_plus_wallet SET auto_sell_per = ?, withdraw = ?, to_sw = 0, status = 1 WHERE user_id = ?", array($auto_sell_per, 1, $u_id));
                        echo json_encode(array('success'=>'1', 'msg'=>'Successfully updated.')); exit;
                    }
                    else{
                       $this->db->query("INSERT into pro_plus_wallet (user_id, auto_sell_per, withdraw, to_sw, status) values (?,?,?,?,?)", array($u_id, $auto_sell_per, 1, 0, 1));
                       echo json_encode(array('success'=>'1', 'msg'=>'Successfully saved.')); exit;
                    }
                }else{
                    if($this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = ?", array($u_id))->row()){
                        $this->db->query("UPDATE pro_plus_wallet SET auto_sell_per = ?, to_sw = ?, withdraw = 0, status = 1 WHERE user_id = ?", array($auto_sell_per, 1, $u_id));
                        echo json_encode(array('success'=>'1', 'msg'=>'Successfully updated.')); exit;
                    }
                    else{
                       $this->db->query("INSERT into pro_plus_wallet (user_id, auto_sell_per, to_sw, withdraw, status) values (?,?,?,?,?)", array($u_id, $auto_sell_per, 1, 0, 1));
                       echo json_encode(array('success'=>'1', 'msg'=>'Successfully saved.')); exit;
                    }
                }
                
            }else{
                echo json_encode(array('error'=>'1', 'msg'=>'Please buy Pro+ addon to use this feature.')); exit;
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'session expired')); exit;
        }
    }
    
    public function deactive_auto_selling(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($proplus = $this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = ?", array($u_id))->row()){
                $this->db->query("UPDATE pro_plus_wallet SET status = 0 WHERE user_id = ?", array($u_id));
                echo json_encode(array('success'=>'1', 'msg'=>'Successfully Deactivated.')); exit;
            }else{
                echo json_encode(array('error'=>'1', 'msg'=>'You dont have Pro+ feature access.')); exit;
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'session expired')); exit;
        }
    }
    
    public function proplus_eth_setting(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $eth_deposit_in_abot = $this->input->post('eth_deposit_in_abot');
           
            
            if($this->db->query("SELECT * FROM pp_auto_selling_or_buying WHERE u_id = ?", array($u_id))->row()){
                $this->db->query("UPDATE pp_auto_selling_or_buying SET eth_deposit_in_abot = ? WHERE u_id = ?", array($eth_deposit_in_abot, $u_id));
                echo "Updated";
                exit;
            }else{
              $this->db->query("INSERT into pp_auto_selling_or_buying (u_id, eth_deposit_in_abot) values (?,?)", array($u_id, $eth_deposit_in_abot));
              echo "Successfull";
              exit;
            }
        }else{
            echo "Session Expired";
            exit;
        }
    }
    
    
    public function freearb_to_wallet(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $amount = abs($this->input->post('amount'));
            if($amount < 0.0001){
                echo json_encode(array('error'=>'1', 'msg'=>'Minimum Transfer 0.0001')); exit;
            }
            if($user_plus_wallet = $this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = ?", array($u_id))->row()){
                if($user_plus_wallet->freeArb >= $amount){
                    //update free pool
                    $update_pool_amount = $user_plus_wallet->freeArb - $amount;
                    if($update_pool_amount < 0.0001){$update_pool_amount = 0;}
                    if($this->db->query("UPDATE pro_plus_wallet SET freeArb = ? WHERE user_id = ?", array($update_pool_amount, $u_id))){
                        //update ex_earned wallet 
                        if($exchange_earned_wallet = $this->db->query("SELECT * FROM exchange_earned_wallet WHERE user_id = ?", array($u_id))->row()){
                            $update_exchange_earned_amount = $exchange_earned_wallet->activeArb + $amount;
                            $this->db->query("UPDATE exchange_earned_wallet SET activeArb = ? WHERE user_id = ?", array($update_exchange_earned_amount, $u_id));
                            // add logs 
                            $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'poolFree_ARB', '-'.$amount, 'Plus Pool Free ARB transfer to exEarned wallet', $user_plus_wallet->freeArb));
                            $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'exEarnedWallet_ARB', $amount, 'Credit ARB in wallet from Plus+ free ARB', $exchange_earned_wallet->activeArb));
                            echo json_encode(array('success'=>'1', 'msg'=>'Transfer Successfully.')); exit;
                        }else{
                            $this->db->query("INSERT INTO exchange_earned_wallet (user_id, activeArb) VALUES (?,?)", array($u_id, $amount));
                            // add logs 
                            $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'poolFree_ARB', '-'.$amount, 'Plus Pool Free ARB transfer to exEarned wallet', $user_plus_wallet->freeArb));
                            $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'exEarnedWallet_ARB', $amount, 'Credit ARB in wallet from Plus+ free ARB', 0));
                            echo json_encode(array('success'=>'1', 'msg'=>'Transfer Successfully.')); exit;
                        }
                    }else{
                        echo json_encode(array('error'=>'1', 'msg'=>'Something went wrong')); exit;
                    }
                }else{
                    echo json_encode(array('error'=>'1', 'msg'=>'Not enough amount is available')); exit;
                }
            }else{
                 echo json_encode(array('error'=>'1', 'msg'=>'Your Plus+ feature not activated')); exit;
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'Session Expired')); exit;
        }
    }
    
    
    
    //truncate extra decimal values
    public function getTruncatedValue ( $value, $precision ){
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
	