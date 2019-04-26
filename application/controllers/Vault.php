<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Vault extends MY_Controller {
    
    public function cron_for_vault(){
        if($this->db->query("SELECT * FROM cron_status WHERE name = ?", array('vault_cron'))->row()->value > 0){
            echo 'cron alrady in working'; exit;
        }else{
            $this->db->query("UPDATE cron_status SET value = ? WHERE name = ?", array(1, 'vault_cron'));
        }
        $records = $this->db->query("SELECT * FROM vault_req ORDER BY created_at ASC")->result();
        foreach($records as $record){
            if($record->type == 'in'){
                // remove 2% of amount
                $per_2 = ($record->amount / 100) * 2;
                
                //distribute 2% share to Vault users
                $total_arb_in_vault = $this->db->query("SELECT sum(activeArb) as total_arb FROM vault_wallet")->row()->total_arb;
                $vault_users = $this->db->query("SELECT * FROM vault_wallet")->result();
                foreach($vault_users as $vault_user){
                    if($vault_user->activeArb >= 0.0001){
                        $profit_percent_current_user = ($vault_user->activeArb / $total_arb_in_vault)*100;
                        $profit_arb_current_user = ($per_2/100)*$profit_percent_current_user;
                        // update current user vault wallet
                        if($profit_arb_current_user >= 0.0001){
                            $updatearb_in_vault = $vault_user->activeArb + $profit_arb_current_user;
                            if($updatearb_in_vault > 0.0001){
                                $updatearb_in_vault = $this->getTruncatedValue($updatearb_in_vault, 6);
                                $this->db->query("UPDATE vault_wallet SET activeArb = ? WHERE user_id = ?", array($updatearb_in_vault, $vault_user->user_id));
                                // create current user log
                                $this->db->query("INSERT into vault_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($vault_user->user_id,'vaultWallet_ARB',$profit_arb_current_user, $vault_user->activeArb, "Vault Share Distribution"));
                            }
                        }
                    }
                }
               
               // update request user Vault Wallet
                $amount_added = $record->amount - $per_2;
                
                $last_vault_blnc = 0;
                if($vault = $this->db->query("SELECT * FROM vault_wallet WHERE user_id = ?", array($record->user_id))->row()){
                    $last_vault_blnc = $vault->activeArb;
                    $updatearb_vault = $vault->activeArb + $amount_added;
                    if($updatearb_vault < 0.0001){$updatearb_vault = 0;}
                    $updatearb_vault = $this->getTruncatedValue($updatearb_vault, 6);
                    if($record->wallet == 'external'){$ex_count = $vault->external_count + $amount_added;}else{$ex_count = $vault->external_count;}
                    $this->db->query("UPDATE vault_wallet SET activeArb = ?, external_count = ? WHERE user_id = ?", array($updatearb_vault, $ex_count, $record->user_id));
                
                }else{
                    if($amount_added < 0.0001){$amount_added = 0;}
                    $addarb_vault = $this->getTruncatedValue($amount_added, 6);
                    if($record->wallet == 'external'){$ex_count = $amount_added;}else{$ex_count = 0;}
                    $this->db->query("INSERT INTO vault_wallet (user_id, activeArb, external_count) VALUES (?,?,?)", array($record->user_id, $addarb_vault, $ex_count));
                }
                
                if($amount_added >= 0.0001){
                    // add logs
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($record->user_id,'vaultWallet_ARB',$record->amount, $last_vault_blnc, "Credit ARB in vault from ".$record->wallet." wallet"));
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($record->user_id,'vaultWallet_ARB','-'.$per_2, ($last_vault_blnc + $record->amount), "2% Fee"));
                    
                    //update log 
                    $this->db->query("UPDATE wallet_logs SET comment = ? WHERE id = ?", array('Transfer ARB from '.$record->wallet.' wallet to vault (Success)', $record->log_id));
                }
                
            }else if($record->type == 'out'){
                // remove 1% of amount
                $per_1 = ($record->amount / 100);
                          
                
                //distribute 1% share to Vault users
                $total_arb_in_vault = $this->db->query("SELECT sum(activeArb) as total_arb FROM vault_wallet")->row()->total_arb;
                $vault_users = $this->db->query("SELECT * FROM vault_wallet")->result();
                foreach($vault_users as $vault_user){
                    if($vault_user->activeArb >= 0.0001){
                        $profit_percent_current_user = ($vault_user->activeArb / $total_arb_in_vault)*100;
                        $profit_arb_current_user = ($per_1/100)*$profit_percent_current_user;
                        if($profit_arb_current_user >= 0.0001){
                            // update current user vault wallet
                            $updatearb_in_vault = $vault_user->activeArb + $profit_arb_current_user;
                            if($updatearb_in_vault > 0.0001){
                                $updatearb_in_vault = $this->getTruncatedValue($updatearb_in_vault, 6);
                                $this->db->query("UPDATE vault_wallet SET activeArb = ? WHERE user_id = ?", array($updatearb_in_vault, $vault_user->user_id));
                                // create current user log
                                $this->db->query("INSERT into vault_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($vault_user->user_id,'vaultWallet_ARB',$profit_arb_current_user, $vault_user->activeArb, "Vault Share Distribution"));
                            }
                                
                        }
                            
                    }
                }
               
               // update request user Vault Wallet
                $amount_added = $record->amount - $per_1;
                
                if($record->wallet == 'external'){
                    $user_wallet = $this->db->query("SELECT * FROM external_wallet WHERE user_id = ?", array($record->user_id))->row();
                    // update user system wallet
                    $user_updated_arb = $user_wallet->activeArb + $amount_added;
                    if($user_updated_arb < 0.0001){$user_updated_arb = 0;}
                    $user_updated_arb = $this->getTruncatedValue($user_updated_arb, 6);
                    $this->db->query("UPDATE external_wallet SET activeArb = ? WHERE user_id = ?", array($user_updated_arb, $record->user_id));
                }else{
                    $user_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($record->user_id))->row();
                    // update user system wallet
                    $user_updated_arb = $user_wallet->activeArb + $amount_added;
                    if($user_updated_arb < 0.0001){$user_updated_arb = 0;}
                    $user_updated_arb = $this->getTruncatedValue($user_updated_arb, 6);
                    $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($user_updated_arb, $record->user_id));
                }
                // add logs
                $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($record->user_id,'systemWallet_ARB',$record->amount, $user_wallet->activeArb, "Credit ARB in ".$record->wallet." wallet from vault"));
                $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($record->user_id,'systemWallet_ARB','-'.$per_1, ($user_wallet->activeArb + $record->amount), "1% vault withdraw Fee"));
                
                //update log 
                $this->db->query("UPDATE wallet_logs SET comment = ? WHERE id = ?", array('Transfer ARB from vault to '.$record->wallet.' wallet (Success)', $record->log_id));
                
            }
            
            $this->db->query("DELETE FROM vault_req WHERE user_id =?", array($record->user_id));
            echo $record->user_id.'<br>';
        }
        $this->db->query("UPDATE cron_status SET value = ? WHERE name = ?", array(0, 'vault_cron'));
    }
    
    public function wallet_to_vault(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($this->db->query("SELECT count(*) as tot FROM vault_req WHERE user_id = ?", array($u_id))->row()->tot > 0){
                echo  json_encode(array('error'=>'1', 'msg'=>'You have already request in queue'));
                exit;
            }
            // get ARB to transfer from wallet to Vault min amount 5 ARB
            $amount = $this->getTruncatedValue(abs($this->input->post('amount')), 4);
            if($amount < 5){
                echo  json_encode(array('error'=>'1', 'msg'=>'Minimum 5 ARB transfer in Vault.'));
                exit;
            }
            
            // check user system wallet
            $user_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($u_id))->row();
            if($user_wallet->activeArb >= $amount){
                // update user system wallet
                $user_updated_arb = $user_wallet->activeArb - $amount;
                if($user_updated_arb < 0.0001){$user_updated_arb = 0;}
                $user_updated_arb = $this->getTruncatedValue($user_updated_arb, 4);
                $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($user_updated_arb, $u_id));
                
                // insert logs
                $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'systemWallet_ARB','-'.$amount, $user_wallet->activeArb, "Transfer ARB from wallet to vault (Processing...)"));
                $log_id = $this->db->insert_id();
                // generate request
                $this->db->query("INSERT INTO vault_req (user_id, amount, type, wallet, log_id) VALUES (?,?,?,?,?)", array($u_id, $amount, 'in','internal', $log_id));
                
                echo  json_encode(array('success'=>'1', 'msg'=>'ARB Transfered to Vault. It may take some time to credit in vault please be patient.'));
                exit;
                
            }else{
                echo  json_encode(array('error'=>'1', 'msg'=>'Not Suffecient ARB in your Wallet.'));
                exit;
            }
            
        }else{
            echo  json_encode(array('error'=>'1', 'msg'=>'User Session Expired.'));
            exit;
        }
        
    }
    
    public function vault_to_wallet(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($this->db->query("SELECT count(*) as tot FROM vault_req WHERE user_id = ?", array($u_id))->row()->tot > 0){
                echo  json_encode(array('error'=>'1', 'msg'=>'You have already request in queue'));
                exit;
            }
            // get ARB to transfer from vault to wallet min amount 5 ARB
            $amount = $this->getTruncatedValue(abs($this->input->post('amount')), 4);
            if($amount < 5){
                echo  json_encode(array('error'=>'1', 'msg'=>'Minimum 5 ARB transfer in Wallet.'));
                exit;
            }
            
            // check user vault wallet
            $vault_wallet = $this->db->query("SELECT * FROM vault_wallet WHERE user_id = ?", array($u_id))->row();
            $internal_amount = $vault_wallet->activeArb - $vault_wallet->external_count; 
            if($internal_amount >= $amount){
                //update user vault wallet
                $vault_updated_arb = $vault_wallet->activeArb - $amount;
                if($vault_updated_arb < 0.0001){$vault_updated_arb = 0;}
                $vault_updated_arb = $this->getTruncatedValue($vault_updated_arb, 4);
                $this->db->query("UPDATE vault_wallet SET activeArb = ? WHERE user_id = ?", array($vault_updated_arb, $u_id));
                
                // insert logs
                $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'vaultWallet_ARB','-'.$amount, $vault_wallet->activeArb, "Transfer ARB from vault to wallet (Processing...)"));
                $log_id = $this->db->insert_id();
                // generate request
                $this->db->query("INSERT INTO vault_req (user_id, amount, type, wallet, log_id) VALUES (?,?,?,?,?)", array($u_id, $amount, 'out','internal', $log_id));
                
                
                echo  json_encode(array('success'=>'1', 'msg'=>'ARB Transfered to Wallet. It may take some time to credit in Wallet please be patient.'));
                exit;
                
            }else{
                echo  json_encode(array('error'=>'1', 'msg'=>'Not Suffecient ARB in your Vault.'));
                exit;
            }
            
        }else{
            echo  json_encode(array('error'=>'1', 'msg'=>'User Session Expired.'));
            exit;
        }
        
    }
    
    public function external_wallet_to_vault(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($this->db->query("SELECT count(*) as tot FROM vault_req WHERE user_id = ?", array($u_id))->row()->tot > 0){
                echo  json_encode(array('error'=>'1', 'msg'=>'You have already request in queue'));
                exit;
            }
            // get ARB to transfer from wallet to Vault min amount 5 ARB
            $amount = $this->getTruncatedValue(abs($this->input->post('amount')), 4);
            if($amount < 5){
                echo  json_encode(array('error'=>'1', 'msg'=>'Minimum 5 ARB transfer in Vault.'));
                exit;
            }
            
            // check user system wallet
            $user_wallet = $this->db->query("SELECT * FROM external_wallet WHERE user_id = ?", array($u_id))->row();
            if($user_wallet->activeArb >= $amount){
                // update user system wallet
                $user_updated_arb = $user_wallet->activeArb - $amount;
                if($user_updated_arb < 0.0001){$user_updated_arb = 0;}
                $user_updated_arb = $this->getTruncatedValue($user_updated_arb, 4);
                $this->db->query("UPDATE external_wallet SET activeArb = ? WHERE user_id = ?", array($user_updated_arb, $u_id));
                
                // insert logs
                $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'externalWallet_ARB','-'.$amount, $user_wallet->activeArb, "Transfer ARB from external wallet to vault (Processing...)"));
                $log_id = $this->db->insert_id();
                // generate request
                $this->db->query("INSERT INTO vault_req (user_id, amount, type, wallet, log_id) VALUES (?,?,?,?,?)", array($u_id, $amount, 'in','external', $log_id));
                
                echo  json_encode(array('success'=>'1', 'msg'=>'ARB Transfered to Vault. It may take some time to credit in vault please be patient.'));
                exit;
                
            }else{
                echo  json_encode(array('error'=>'1', 'msg'=>'Not Suffecient ARB in your Wallet.'));
                exit;
            }
            
        }else{
            echo  json_encode(array('error'=>'1', 'msg'=>'User Session Expired.'));
            exit;
        }
        
    }
    
    public function vault_to_external_wallet(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($this->db->query("SELECT count(*) as tot FROM vault_req WHERE user_id = ?", array($u_id))->row()->tot > 0){
                echo  json_encode(array('error'=>'1', 'msg'=>'You have already request in queue'));
                exit;
            }
            // get ARB to transfer from vault to wallet min amount 5 ARB
            $amount = $this->getTruncatedValue(abs($this->input->post('amount')), 4);
            if($amount < 5){
                echo  json_encode(array('error'=>'1', 'msg'=>'Minimum 5 ARB transfer in Wallet.'));
                exit;
            }
            
            // check user vault wallet
            $vault_wallet = $this->db->query("SELECT * FROM vault_wallet WHERE user_id = ?", array($u_id))->row();
            if($vault_wallet->external_count >= $amount){
                //update user vault wallet
                $vault_updated_arb = $vault_wallet->activeArb - $amount;
                if($vault_updated_arb < 0.0001){$vault_updated_arb = 0;}
                $vault_updated_arb = $this->getTruncatedValue($vault_updated_arb, 4);
                $new_external_count = $vault_wallet->external_count - $amount;
                if($new_external_count < 0.0001){$new_external_count = 0;}
                $new_external_count = $this->getTruncatedValue($new_external_count, 4);
                
                $this->db->query("UPDATE vault_wallet SET activeArb = ?, external_count = ? WHERE user_id = ?", array($vault_updated_arb, $new_external_count, $u_id));
                
                // insert logs
                $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", array($u_id,'vaultWallet_ARB','-'.$amount, $vault_wallet->activeArb, "Transfer ARB from vault to external wallet (Processing...)"));
                $log_id = $this->db->insert_id();
                // generate request
                $this->db->query("INSERT INTO vault_req (user_id, amount, type, wallet, log_id) VALUES (?,?,?,?,?)", array($u_id, $amount, 'out','external', $log_id));
                
                
                echo  json_encode(array('success'=>'1', 'msg'=>'ARB Transfered to Wallet. It may take some time to credit in Wallet please be patient.'));
                exit;
                
            }else{
                echo  json_encode(array('error'=>'1', 'msg'=>'Not Suffecient ARB in your Vault.'));
                exit;
            }
            
        }else{
            echo  json_encode(array('error'=>'1', 'msg'=>'User Session Expired.'));
            exit;
        }
        
    }
    
    
    
    
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
    
    
    public function vault_data(){
        $u_id = $this->session->userdata('u_id');
        $sql = "SELECT activeArb,external_count FROM vault_wallet WHERE user_id = $u_id";
        if($vault = $this->db->query($sql)->row()){
            $user_activeArb = $vault->activeArb;
            $from_internal = $vault->activeArb - $vault->external_count;
            $from_external = $vault->external_count;
        }
        else{
            $user_activeArb = 0;
            $from_internal = 0;
            $from_external = 0;
        }
        
        $sql = "SELECT SUM(activeArb) as total_activeArb FROM vault_wallet";
        if($vdata = $this->db->query($sql)->row()){
            $total_activeArb = $vdata->total_activeArb;
        }
        else{
            $total_activeArb = 0;
        }
        
        if($total_activeArb > 300000){
            $above_3 = $total_activeArb - 300000;
            $per_80 = ($above_3 /100) * 28;
            $total_activeArb = 300000 + $per_80;
        }

        $sql = "SELECT activeArb FROM system_wallet WHERE user_id = $u_id";
        $sw_activeArb = $this->db->query($sql)->row()->activeArb;
        
        echo  json_encode(array('user_activeArb'=>$user_activeArb, 'internal' => $this->getTruncatedValue($from_internal, 4).'', 'external' => $this->getTruncatedValue($from_external, 4).'', 'total_activeArb'=>$total_activeArb, 'sw_activeArb'=>$sw_activeArb));
        exit;
    }
    
    public function in_progress(){
        $u_id = $this->session->userdata('u_id');
        if($this->db->query("SELECT count(*) as tot FROM vault_req WHERE user_id = ?", array($u_id))->row()->tot > 0){
            echo  json_encode(array('in_progress'=>'1'));
            exit;
        }else{
            echo  json_encode(array('in_progress'=>'0'));
            exit; 
        }
    }
    
    public function get_vault_log(){
       $u_id = $this->session->userdata('u_id');
       if($u_id != ''){
           $table_name = 'vault_logs';
           $qry = 'SELECT comment as type, value, created_at FROM '.$table_name.' WHERE user_id = ? AND comment NOT LIKE "Fee" ORDER BY created_at DESC';
           $res = $this->db->query($qry, array($u_id))->result();
           
           $qry2 = 'SELECT comment as type, value, created_at FROM wallet_logs WHERE user_id = ? AND comment LIKE "Vault Share Distribution" ORDER BY created_at DESC';
           $res2 = $this->db->query($qry2, array($u_id))->result();
           
           echo json_encode(array_merge($res, $res2));
       }else{
           echo json_encode(array('error'=>'1', 'msg'=>'User session Expired.')); exit;
       }
    }
    
    
}









                