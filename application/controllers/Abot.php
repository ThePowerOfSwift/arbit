<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Abot extends MY_Controller {
    public function distribute_bonus_eth(){
        exit;
        echo 'runing<br>';
        if($this->db->query("SELECT * FROM cron_status WHERE name = 'eth_bonus_cron'")->row()->value == 0){
            $this->db->query("UPDATE cron_status SET value = 1 WHERE name = 'eth_bonus_cron'");
            $eth_bonus_pool = $this->db->query("SELECT * FROM eth_bonus_pool WHERE id = 1")->row();
            
            $this->db->query('INSERT INTO eth_bonus_pool_history (data) values (?)', array(json_encode($eth_bonus_pool)));
            $this->db->query("UPDATE eth_bonus_pool SET activeEth = 0 WHERE id = 1");
            
            if($eth_bonus_pool->activeEth > 0.01){
                $total_abot_invest_lock_active = $this->db->query("SELECT sum(active) as total_sum FROM abot_wallet WHERE active >= 250 AND auto_reinvest = 1 AND reinvest_lock = 1")->row()->total_sum;
                $abot_users = $this->db->query("SELECT * FROM abot_wallet WHERE active >= 250 AND auto_reinvest = 1 AND reinvest_lock = 1")->result();
                foreach($abot_users as $auser){
                    $per_investment = ($auser->active * 100) / $total_abot_invest_lock_active;
                    $eth_part = ($eth_bonus_pool->activeEth/100)*$per_investment;
                    //if user reinvest 50% for 30, 60 or 90 days
                    if($auser->auto_reinvest_per == 50 && $auser->reinvest_lock_days == 30){
                        $deduct_part = ($eth_part / 100) * 30; $eth_part = $eth_part - $deduct_part; $add_to_admin = $deduct_part;
                    }
                    else if($auser->auto_reinvest_per == 50 && $auser->reinvest_lock_days == 60){
                        $deduct_part = ($eth_part / 100) * 25; $eth_part = $eth_part - $deduct_part; $add_to_admin = $deduct_part;
                    }
                    else if($auser->auto_reinvest_per == 50 && $auser->reinvest_lock_days == 90){
                        $deduct_part = ($eth_part / 100) * 20; $eth_part = $eth_part - $deduct_part; $add_to_admin = $deduct_part;
                    }
                    //if user reinvest 75% for 30, 60 or 90 days
                    else if($auser->auto_reinvest_per == 75 && $auser->reinvest_lock_days == 30){
                        $deduct_part = ($eth_part / 100) * 25; $eth_part = $eth_part - $deduct_part; $add_to_admin = $deduct_part;
                    }
                    else if($auser->auto_reinvest_per == 75 && $auser->reinvest_lock_days == 60){
                        $deduct_part = ($eth_part / 100) * 20; $eth_part = $eth_part - $deduct_part; $add_to_admin = $deduct_part;
                    }
                    else if($auser->auto_reinvest_per == 75 && $auser->reinvest_lock_days == 90){
                        $deduct_part = ($eth_part / 100) * 10; $eth_part = $eth_part - $deduct_part; $add_to_admin = $deduct_part;
                    }
                    //if user reinvest 100% for 30, 60 or 90 days
                    else if($auser->auto_reinvest_per == 100 && $auser->reinvest_lock_days == 30){
                        $deduct_part = ($eth_part / 100) * 20; $eth_part = $eth_part - $deduct_part; $add_to_admin = $deduct_part;
                    }
                    else if($auser->auto_reinvest_per == 100 && $auser->reinvest_lock_days == 60){
                        $deduct_part = ($eth_part / 100) * 10; $eth_part = $eth_part - $deduct_part; $add_to_admin = $deduct_part;
                    }
                    else if($auser->auto_reinvest_per == 100 && $auser->reinvest_lock_days == 90){
                        $eth_part = $eth_part; $add_to_admin = 0;
                    }else{
                        continue;
                    }
                    
                    if($add_to_admin >= 0.00000001){
                        //update admin wallet
                        $get_admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 4 LIMIT 1")->row();
                        $admin_new_eth = $get_admin_wallet->eth + $add_to_admin;
                        //$add_to_admin = number_format($add_to_admin, 10, '.', '');
                        $this->db->query("UPDATE admin_wallet SET eth = ? WHERE id = ?", array($admin_new_eth, 4));
                        $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", 
                            array($auser->user_id,'adminEth_BonusDeduct_'.$auser->auto_reinvest_per.'%_'.$auser->reinvest_lock_days.'days', 
                            $add_to_admin, $get_admin_wallet->eth, "Fee"));
                    }
                    
                    if($eth_part >= 0.00000001){
                        //update bonus 
                        $eth_bonus = $this->db->query("SELECT * FROM abot_eth_bonus WHERE user_id = ?", array($auser->user_id))->row();
                        $new_bonus = $eth_bonus->activeEth + $eth_part;
                        
                        if($new_bonus >= 0.00000001){
                            //$new_bonus =  $this->getTruncatedValue($new_bonus, 6);
                            //$new_bonus = number_format($new_bonus, 12, '.', '');
                            $this->db->query("UPDATE abot_eth_bonus SET activeEth = ? WHERE user_id = ?", array($new_bonus, $auser->user_id));
                            $eth_part = number_format($eth_part, 10, '.', '');
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, last_blnc, comment) values (?,?,?,?,?)", 
                                array($auser->user_id,'abot_eth_bonus', $eth_part, $eth_bonus->activeEth, "ETH bonus on reinvest lock"));
                        }
                        echo 'done';
                    }
                    
                }
                
            }
        }else{
            echo  'cron locked';
        }
    }
    
    public function open_eth_bonus_cron(){
        $this->db->query("UPDATE cron_status SET value = 0 WHERE name = 'eth_bonus_cron'");
        echo 'done';
    }
    
    
}