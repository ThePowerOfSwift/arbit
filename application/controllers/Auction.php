<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auction extends MY_Controller {
    public function verify_auction(){
        if($_GET['id'] != ''){
            $vid = urldecode($_GET['id']);
            $now = date('Y-m-d H:i:s');
            $this->db->query("UPDATE auction_req SET status = ?, confirm_key = ?, created_at = ? WHERE confirm_key = ?", array(1, '', $now, $vid));
        }
        redirect('admin/auction');
    }
    
    public function reject_auction(){
        if($_GET['id'] != ''){
            $vid = urldecode($_GET['id']);
           // print_r($vid); exit;
            if($auction = $this->db->query("SELECT * FROM auction_req WHERE confirm_key = ?", array($vid))->row()){
                $this->db->query("DELETE FROM auction_req WHERE confirm_key = ?", array($vid));
                //return 80% of fee back
                if($auction->fee_deducted > 0.01){
                    $abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($auction->user_id))->row();
                    $per_80 = ($auction->fee_deducted/100)*80;
                    $now_active = $abot_wallet->active + $per_80;
                    $update_abot_active = $abot_wallet->pending + $now_active;
                    $cur_time = date('Y-m-d H:i:s');
                    $this->db->query("UPDATE abot_wallet SET pending = ?, active = 0, auction_count = 0, auction_reject_time = ? WHERE user_id = ?", array($update_abot_active, $cur_time, $auction->user_id));
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($auction->user_id, 'abot_active', $per_80, '4% fee revert on auction request rejection', $abot_wallet->active));
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($auction->user_id, 'abot_pending', $now_active, 'aBOT active transfer to pending due to auction rejection', $abot_wallet->pending));
                    
                    $this->db->query("INSERT into auction_logs (user_id, type, comment, value) values (?,?,?,?)", array($auction->user_id, 'account_auction_request', 'User reject his auction. 4% fee reverted', $per_80));
                }else{
                    $abot_wallet = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($auction->user_id))->row();
                    $update_abot_active = $abot_wallet->pending + $abot_wallet->active;
                    $cur_time = date('Y-m-d H:i:s');
                    $this->db->query("UPDATE abot_wallet SET pending = ?, active = 0, auction_count = 0, auction_reject_time = ? WHERE user_id = ?", array($update_abot_active, $cur_time, $auction->user_id));
                    $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", 
                        array($auction->user_id, 'abot_pending', $abot_wallet->active, 'aBOT active transfer to pending due to auction rejection', $abot_wallet->pending));
                    
                    $this->db->query("INSERT into auction_logs (user_id, type, comment, value) values (?,?,?,?)", array($auction->user_id, 'account_auction_request', 'User reject his auction. without fee revert', 0));
                }
                $auction->reject = 1;
                $this->db->query("INSERT into auction_history (no_bid_austions, reject) values (?,?)", array(json_encode($auction), 1));
            }
        }
        redirect('admin/auction');
    }
    
    
    public function request_for_auction(){
        exit;
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            
            if($this->db->query("SELECT * FROM bids WHERE user_id = ?", array($u_id))->result()){
                echo json_encode(array('error'=>'1', 'msg' => "You cannot request for auction.")); exit;
            }
            else if($this->db->query("SELECT * FROM auction_req WHERE user_id = ?", array($u_id))->row()){
                echo json_encode(array('error'=>'1', 'msg' => "Request already Submitted.")); exit;
            }
            else{
                if($user = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row()){
                    
                    $account_id = $user->u_username."-".$user->u_id;
                    
                    $user_abot = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($user->u_id))->row();
                    $user_system = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($user->u_id))->row();
                    $user_exchange = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = ?", array($user->u_id))->row();
                    $user_ex_earned = $this->db->query("SELECT * FROM exchange_earned_wallet WHERE user_id = ?", array($user->u_id))->row();
                    $user_pp = $this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = ?", array($user->u_id))->row();
                    $user_stop = $this->db->query("SELECT * FROM stop_abot_wallet WHERE user_id = ?", array($user->u_id))->row();
                    $user_vault = $this->db->query("SELECT * FROM vault_wallet WHERE user_id = ?", array($user->u_id))->row();
                    
                    if($user_abot){$abot_worth = $user_abot->pending + $user_abot->active;}
                    else{$abot_worth = 0;}
                    if($user_system){$system_arb = $user_system->activeArb;}
                    else{$system_arb = 0;}
                    if($user_exchange){$exchange_arb = $user_exchange->activeArb;}
                    else{$exchange_arb = 0;}
                    if($user_ex_earned){$ex_earned_arb = $user_ex_earned->activeArb;}
                    else{$ex_earned_arb = 0;}
                    if($user_pp){$pp_arb = $user_pp->freeArb;}
                    else{$pp_arb = 0;}
                    if($user_stop){$stop_abot_arb = $user_stop->activeArb;}
                    else{$stop_abot_arb = 0;}
                    if($user_vault){$vault_arb = $user_vault->activeArb;}
                    else{$vault_arb = 0;}
                    
                    $this->db->query("INSERT into auction_req (user_id, account_id, abot_worth, system_arb, exchange_arb, ex_earned_arb, pp_arb, stop_abot_arb, vault_arb) values (?,?,?,?,?,?,?,?,?)", array($user->u_id, $account_id, $this->getTruncatedValue($abot_worth, 4), $this->getTruncatedValue($system_arb, 4), $this->getTruncatedValue($exchange_arb, 4), $this->getTruncatedValue($ex_earned_arb, 4), $this->getTruncatedValue($pp_arb, 4), $this->getTruncatedValue($stop_abot_arb, 4), $this->getTruncatedValue($vault_arb, 4)));
                    $this->db->query("INSERT into auction_logs (user_id, type, comment) values (?,?,?)", array($user->u_id, 'account_auction_request', 'User requested for auction his/her account.'));

                    echo json_encode(array('success'=>'1', 'msg' => "Your request is submitted successfully."));exit;
                }
            }
        }
        else{
            echo json_encode(array('error'=>'1', 'msg' => "Session Expired.")); exit;
        }
    }
    
    public function place_bid(){
        
        if($this->db->query("SELECT * FROM admin_locks WHERE name = 'auction_lock'")->row()->lock_status == 1 && $u_id != 13870){
            echo json_encode(array('error'=>'1', 'msg'=>'This feature is locked')); exit;
        }
        
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($this->db->query("SELECT * FROM auction_req WHERE user_id = ?", array($u_id))->row()){
                echo json_encode(array('error'=>'1', 'msg' => "You cannot bid.")); exit;
            }
            $bid_amount = $this->input->post('bid_amount');
            $account_id = $this->input->post('account_id');
            
            
            $get_auction_with_acid = $this->db->query("SELECT * FROM auction_req WHERE account_id = ?", array($account_id))->row();
            $after_48hour = date('Y-m-d H:i:s', strtotime('+48 hour', strtotime($get_auction_with_acid->created_at)));
            $before_10min_expiration = date('Y-m-d H:i:s', strtotime('-10 min', strtotime($after_48hour)));
            $bid_current_time = date('Y-m-d H:i:s');
            if($bid_current_time >= $before_10min_expiration){
                echo json_encode(array('error'=>'1', 'msg' => "Biding on this account is closed now.")); exit;
            }
            
            
            $user_activeEth = $this->db->query("SELECT activeEth FROM system_wallet WHERE user_id = ?", array($u_id))->row()->activeEth;
            $threashold = 0.1;
            $bid = $this->db->query("SELECT * FROM bids WHERE user_id = ? AND account_id = ?", array($u_id, $account_id))->row();
            if($bid){
                
                $user_activeEth = $user_activeEth + $bid->bid_amount;
                if(($user_activeEth - $threashold) > $bid_amount){
                    $check_bid_amount = $bid_amount - $threashold;
                    if($check_bid_amount >= $bid->bid_amount){
                        $updated_activeEth = $user_activeEth - $bid_amount;
                        $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($updated_activeEth , $u_id));
                        $this->db->query("UPDATE bids SET bid_amount = ? WHERE user_id = ? AND account_id = ?", array($bid_amount , $u_id, $account_id));
                        
                        //$this->db->query("INSERT into auction_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id, 'update_bid', $bid_amount, 'User updated his/her bid amount on account: ('.$account_id.')', $bid->bid_amount));
                        $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id, 'systemWallet_ETH', '-'.($bid_amount - $bid->bid_amount), 'User bid updated on account: ('.$account_id.')', ($user_activeEth - $bid->bid_amount)));
                        
                        //update read flag
                        $this->db->query("UPDATE auction_req SET read_flag = 1 WHERE account_id = ?", array($account_id));

                        echo json_encode(array('success'=>'1', 'msg' => "Your bid is updated successfully."));exit;
                    }
                    else{echo json_encode(array('error'=>'1', 'msg' => "Cannot bid less then highest bid placed.")); exit;}
                }
                else{echo json_encode(array('error'=>'1', 'msg' => "Not enough balance in System Wallet.")); exit;}
            }else{
                if(($user_activeEth - $threashold) > $bid_amount){
                    $max_bid_amount = $this->db->query("SELECT MAX(bid_amount) as max_bid_amount FROM bids WHERE account_id = ?", array($account_id))->row()->max_bid_amount;
                    if(!$max_bid_amount){
                        $max_bid_amount = $this->db->query("SELECT total_worth FROM auction_req WHERE account_id = ?", array($account_id))->row()->total_worth;
                    }
                    $check_bid_amount = $bid_amount - $threashold;
                    if($check_bid_amount >= $max_bid_amount){
                        
                        $all_bids = $this->db->query("SELECT * FROM bids WHERE account_id = ?", array($account_id))->result();
                        foreach($all_bids as $bid){
                            $old_bid_sys_eth= $this->db->query("SELECT activeEth FROM system_wallet WHERE user_id = ?", array($bid->user_id))->row()->activeEth;
                            $updated_sys_activeEth = $old_bid_sys_eth + $bid->bid_amount;
                            $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($updated_sys_activeEth , $bid->user_id));
                            $this->db->query("DELETE FROM bids WHERE id = ?", array($bid->id));
                            //logs
                            $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", 
                                array($bid->user_id, 'system_wallet', $bid->bid_amount, 'ETH reverted because higher bid placed on account: ('.$account_id.')', $old_bid_sys_eth));
                        }
                        
                        $updated_activeEth = $user_activeEth - $bid_amount;
                        
                        $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($updated_activeEth, $u_id));
                        $this->db->query("INSERT into bids (user_id, account_id, bid_amount, bid_by) values (?,?,?,?)", array($u_id, $account_id, $bid_amount, 'User'));
                        
                        //$this->db->query("INSERT into auction_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id, 'bid', $bid_amount, 'User bid on account: ('.$account_id.')', $bid_amount));
                        $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($u_id, 'systemWallet_ETH', '-'.$bid_amount, 'User bid on account: ('.$account_id.')', $user_activeEth));

                        //update read flag
                        $this->db->query("UPDATE auction_req SET read_flag = 1 WHERE account_id = ?", array($account_id));

                        echo json_encode(array('success'=>'1', 'msg' => "Your bid is submitted successfully."));exit;
                    }
                    else{echo json_encode(array('error'=>'1', 'msg' => "Cannot bid less then highest bid placed.")); exit;}
                }
                else{echo json_encode(array('error'=>'1', 'msg' => "Not enough balance in System Wallet.")); exit;}
            }
        }
        else{echo json_encode(array('error'=>'1', 'msg' => "Session Expired")); exit;}
    }

    public function secondsToTime($seconds) {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        if($dtF->diff($dtT)->format('%a') > 0){
            return $dtF->diff($dtT)->format('%a day, %hH:%im');
        }else{
            return $dtF->diff($dtT)->format('%hH:%im');
        }
    }
    
    public function get_auctions(){
        $u_id = $this->session->userdata('u_id');
        if($this->input->post('sort')){
            $sort = strtoupper($this->input->post('sort'));
        }else{
            $sort = 'ASC';
        }
        
        if($this->input->post('field')){
            $field = $this->input->post('field');
        }else{
            $field = 'created_at';
        }
        
        
        if($u_id != ''){
            $page_no = $this->input->post('page_no');
            if($page_no){
                $no_of_records = 6;
                $offset = ($page_no * $no_of_records) - $no_of_records;
                $auction_reqs = $this->db->query("SELECT * FROM auction_req WHERE status = 1 ORDER BY $field $sort Limit $offset, $no_of_records")->result();
                
                foreach($auction_reqs as $one){
                    $bid_data = $this->db->query("SELECT MAX(bid_amount) as max_bid_amount , MIN(bid_amount) as min_bid_amount FROM bids WHERE account_id = ?", array($one->account_id))->row();
                    $remaining_time = strtotime('+48 hour', strtotime($one->created_at)) - strtotime(date('Y-m-d H:i:s'));
                    if($remaining_time <= 0){$remaining_time=0;}
                    $remaining_time =  $this->secondsToTime($remaining_time);
                    if($bid_data){
                        $auctions[] = array('auction' => $one, 'max_bid_amount' => $bid_data->max_bid_amount."", 'min_bid_amount' => $one->total_worth."", "remain_time" => $remaining_time);
                    }
                }
                echo json_encode($auctions);
            }
            else{echo json_encode(array('error'=>'1', 'msg' => "Somthing Went Wrong")); exit;}
        }
        else{echo json_encode(array('error'=>'1', 'msg' => "Session Expired")); exit;}
    }
    
    public function get_user_auctions(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($auction_req = $this->db->query("SELECT * FROM auction_req WHERE user_id = ? limit 1", array($u_id))->row()){
                if($auction_req->read_flag > 0){
                    //update read flag
                    $this->db->query("UPDATE auction_req SET read_flag = 0 WHERE user_id = ?", array($u_id));
                }
                if($auction_req->status == 1){
                    $auction_req->confirm_key = 0;
                    $auction_req->state = 'Open for biding';
                }else{
                    if($auction_req->confirm_key != ''){
                        $auction_req->confirm_key = 1;
                        $auction_req->state = 'Verify your Email';
                    }else{
                        $auction_req->confirm_key = 0;
                        $auction_req->state = 'Under Admin Approval';
                    }
                }
                
                $bid_data = $this->db->query("SELECT MAX(bid_amount) as max_bid_amount FROM bids WHERE account_id = ?", array($auction_req->account_id))->row();
                if($bid_data){
                    $auction_req->max_bid =  $bid_data->max_bid_amount.'';
                }
                echo json_encode(array('success'=>'1', 'result' => $auction_req));
            }else{echo json_encode(array('error'=>'1', 'msg' => "Nothing Found")); exit;}
        }else{echo json_encode(array('error'=>'1', 'msg' => "Session Expired")); exit;}
    }
    
    
    public function auction_win_cron(){
        echo "running ... ";
        $db_current = date('Y-m-d H:i:s'); //date("Y-m-d H:i:s", strtotime('-5 hour'));
        $before_24 = date("Y-m-d H:i:s", strtotime('-48 hour', strtotime($db_current)));
        $auctions = $this->db->query("SELECT * FROM auction_req WHERE status = 1 AND created_at < '$before_24'")->result();
        
        foreach($auctions as $auction){
            $max_bid_amount = $this->db->query("SELECT user_id, bid_amount as max_bid_amount FROM bids WHERE account_id = '$auction->account_id' order by bid_amount desc limit 1")->row();
            if($max_bid_amount->max_bid_amount){
                //fees
                $fee = 0; //($max_bid_amount->max_bid_amount * 5) / 100;
                $updated_amount = $max_bid_amount->max_bid_amount - $fee;
                
                // $admin_eth = $this->db->query("SELECT eth FROM admin_wallet WHERE id = 1")->row()->eth;
                // $admin_new_eth = $admin_eth + $fee;
                // $this->db->query("UPDATE admin_wallet SET eth = ? WHERE id = ?", array($admin_new_eth, 1));
                // $this->db->query("INSERT into auction_logs (user_id, type, value, comment) values (?,?,?,?)", array($auction->user_id,'auction_Fee_Eth','-'.$fee, "Fee"));

                //deduct active $ anf add to buyer abot pending
                $seller_abot = $this->db->query("SELECT * FROM abot_wallet where user_id = ?", array($auction->user_id))->row();
                $this->db->query("UPDATE abot_wallet SET active = 0, auction_count = 0 WHERE user_id = ?", array($auction->user_id));
                //log
                $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($auction->user_id, 'abot_active', '-'.$seller_abot->active, 'Sell aBOT active $ in auction', $seller_abot->active));
                
                $buyer_abot = $this->db->query("SELECT * FROM abot_wallet WHERE user_id = ?", array($max_bid_amount->user_id))->row();
                $update_buyer_abot = $buyer_abot->pending + $seller_abot->active;
                $this->db->query("UPDATE abot_wallet SET pending = ? WHERE user_id = ?", array($update_buyer_abot, $max_bid_amount->user_id));
                //log
                $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($max_bid_amount->user_id, 'abot_pending', $seller_abot->active, 'Buy $ from aBOT auction', $buyer_abot->pending));
                
                //transfer eth to seller's system wallet
                $seller_sys_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = $auction->user_id")->row();
                $updated_seller_sys_wallet = $seller_sys_wallet->activeEth + $updated_amount;
                $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($updated_seller_sys_wallet, $auction->user_id));
                //log
                $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($auction->user_id, 'systemWallet_ETH', $max_bid_amount->max_bid_amount, 'Credit ETH from aBOT auction.', $seller_sys_wallet->activeEth));
                
                //revert all other bids 
                $all_bids = $this->db->query("SELECT * FROM bids WHERE account_id = '$auction->account_id'")->result();
                foreach($all_bids as $bid){
                    if($bid->user_id != $max_bid_amount->user_id){
                        $user_activeEth = $this->db->query("SELECT activeEth FROM system_wallet WHERE user_id = ?", array($bid->user_id))->row()->activeEth;
                        $updated_activeEth = $user_activeEth + $bid->bid_amount;
                        $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($updated_activeEth , $bid->user_id));
                        //logs
                        $this->db->query("INSERT into wallet_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($bid->user_id, 'system_wallet', $bid->bid_amount, 'ETH reverted of bid on account: ('.$auction->account_id.')', $user_activeEth));
                    }
                }
                
                $bids_of_auctions = array();
                $bids_of_auctions[] = array('auction'=>$auction, 'bids'=>$all_bids);
                $this->db->query("DELETE FROM bids WHERE account_id = '$auction->account_id'");
                $this->db->query("DELETE FROM auction_req WHERE user_id = $auction->user_id AND account_id = '$auction->account_id'");
                 $this->db->query("INSERT into auction_history (bids_of_austions) values (?)", array(json_encode($bids_of_auctions)));
                
                
            }else{
                $no_bid_austions = array();
                $no_bid_austions[] = $auction;
                $this->db->query("DELETE FROM auction_req WHERE user_id = $auction->user_id AND account_id = '$auction->account_id'");
                //logs
                $this->db->query("INSERT into auction_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($auction->user_id, 'Auction_request_reverted', 0, 'Auction request reverted due to no bid placed.', 0));
                
                 $this->db->query("INSERT into auction_history (no_bid_austions) values (?)", array(json_encode($no_bid_austions)));
                
            }
            
            // if($no_bid_austions || $bids_of_auctions){
               
            // }
        
        }
        echo "done ... ";
    }
    
    
    
    public function auction_win_cron_old(){
        echo "running ... ";
        $db_current = date("Y-m-d H:i:s", strtotime('-5 hour'));
        $before_24 = date("Y-m-d H:i:s", strtotime('-24 hour', strtotime($db_current)));
        $auctions = $this->db->query("SELECT * FROM auction_req WHERE status = 1 AND updated_at < '$before_24'")->result();
        
        foreach($auctions as $auction){
            $max_bid_amount = $this->db->query("SELECT user_id, bid_amount as max_bid_amount FROM bids WHERE account_id = '$auction->account_id' order by bid_amount desc limit 1")->row();
            if($max_bid_amount->max_bid_amount){
                //fees
                $fee = ($max_bid_amount->max_bid_amount * 5) / 100;
                $updated_amount = $max_bid_amount->max_bid_amount - $fee;
                
                $admin_eth = $this->db->query("SELECT eth FROM admin_wallet WHERE id = 1")->row()->eth;
                $admin_new_eth = $admin_eth + $fee;
                $this->db->query("UPDATE admin_wallet SET eth = ? WHERE id = ?", array($admin_new_eth, 1));

                $this->db->query("INSERT into auction_logs (user_id, type, value, comment) values (?,?,?,?)", array($auction->user_id,'auction_Fee_Eth','-'.$fee, "Fee"));

                //transfer eth to seller's external wallet
                if($user_external_wallet = $this->db->query("SELECT * FROM external_wallet WHERE user_id = $auction->user_id")->row()){
                    
                    $updated_external_wallet = $user_external_wallet->activeEth + $updated_amount;
                    
                    $this->db->query("UPDATE external_wallet SET activeEth = ? WHERE user_id = ?", array($updated_external_wallet, $auction->user_id));
                    //logs
                    $this->db->query("INSERT into auction_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($auction->user_id, 'external_wallet', $max_bid_amount->max_bid_amount, 'User Credited with ETH.', $user_external_wallet->activeEth));
                }
                else{
                    $this->db->query("INSERT into external_wallet (user_id, activeEth) values (?,?)", array($auction->user_id, $updated_amount));
                    //logs
                    $this->db->query("INSERT into auction_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($auction->user_id, 'external_wallet', $max_bid_amount->max_bid_amount, 'User Credited with ETH.', 0));
                }

                $all_bids = $this->db->query("SELECT * FROM bids WHERE account_id = '$auction->account_id'")->result();
                foreach($all_bids as $bid){
                    if($bid->user_id != $max_bid_amount->user_id){
                        $user_activeEth = $this->db->query("SELECT activeEth FROM system_wallet WHERE user_id = ?", array($bid->user_id))->row()->activeEth;
                        $updated_activeEth = $user_activeEth + $bid->bid_amount;
                        $this->db->query("UPDATE system_wallet SET activeEth = ? WHERE user_id = ?", array($updated_activeEth , $bid->user_id));
                        //logs
                        $this->db->query("INSERT into auction_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($bid->user_id, 'system_wallet', $bid->bid_amount, 'ETH reverted of bid on account: ('.$auction->account_id.')', $user_activeEth));
                    }
                }
                $bids_of_auctions[] = array('auction'=>$auction, 'bids'=>$all_bids);
                $this->db->query("DELETE FROM bids WHERE account_id = '$auction->account_id'");
                $this->db->query("DELETE FROM auction_req WHERE user_id = $auction->user_id AND account_id = '$auction->account_id'");

                
                // delete access requests (having) ... 
                $get_access_req = $this->db->query("SELECT * FROM access_req WHERE main_user_id = ?", array($auction->user_id))->row();
                if(isset($get_access_req)){
                     // log -- send it to history.
                    $this->db->query("INSERT into access_req_history (main_user_id, user_id, pin, status, reason) values (?,?,?,?,?)", array($get_access_req->main_user_id, $get_access_req->user_id, $get_access_req->pin, $get_access_req->status, "Auction"));
                     // delete requests.
                    $this->db->query("DELETE FROM access_req WHERE main_user_id = ?", array($auction->user_id));
                    
                    $get_access_verified = $this->db->query("SELECT * FROM access_verified WHERE accessed_user_id = ?", array($auction->user_id))->row();
                    if(isset($get_access_verified)){
                        // log -- send it to history.
                        $this->db->query("INSERT into access_verified_history (user_id, accessed_user_id, reason) values (?,?,?)", array($get_access_verified->user_id, $get_access_verified->accessed_user_id, "Auction"));
                        // delete requests.
                        $this->db->query("DELETE FROM access_verified WHERE accessed_user_id = ?", array($get_access_verified->accessed_user_id));
                    }
                }
                
                // max_bidder will get access to auction_user_id and staus will be 0 for admin verification;
                $seller_user_id = $auction->user_id;
                $buyer_user_id = $max_bid_amount->user_id;
                
                $this->db->query("INSERT into access_req (main_user_id, user_id, comment) values (?,?,?)", array($seller_user_id, $buyer_user_id, "Auction Account Access Request."));
                
                $this->db->query("INSERT into auction_logs (user_id, type, comment) values (?,?,?)", array($buyer_user_id, 'Account_Access', 'Account Access Pending of account: ('.$auction->account_id.')'));
            }
            else{
                $user_abot_active = $this->db->query("SELECT active FROM abot_wallet WHERE user_id = ?", array($auction->user_id))->row()->active;
                $fee = ($user_abot_active * 5) / 100;
                $updated_abot_active = $user_abot_active - $fee;
                
                $this->db->query("UPDATE abot_wallet SET active = ? WHERE user_id = ?", array($updated_abot_active , $auction->user_id));

                $feeArbs = $fee / doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
                
                $admin_arb = $this->db->query("SELECT arb FROM admin_wallet WHERE id = 1")->row()->arb;
                $admin_new_arb = $admin_arb + $feeArbs;
                $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = ?", array($admin_new_arb, 1));
                
                $no_bid_austions[] = $auction;
                
                $this->db->query("DELETE FROM auction_req WHERE user_id = $auction->user_id AND account_id = '$auction->account_id'");
                
                //logs
                $this->db->query("INSERT into auction_logs (user_id, type, value, comment, last_blnc) values (?,?,?,?,?)", array($auction->user_id, 'Auction_request_deleted', '-'.$fee, 'Auction request deleted after fee deduction.', $user_abot_active));
                $this->db->query("INSERT into auction_logs (user_id, type, value, comment) values (?,?,?,?)", array($auction->user_id, 'admin_wallet', $feeArbs, "Fee"));

            }
        }
        if($no_bid_austions || $bids_of_auctions){
            $this->db->query("INSERT into auction_history (no_bid_austions, bids_of_austions) values (?,?)", array(json_encode($no_bid_austions), json_encode($bids_of_auctions)));
        }
        echo "done ... ";
    }
    
    //truncate extra decimal values
    public function getTruncatedValue ( $value, $precision ){
        //Casts provided value
        $value = ( string )$value;

        //Gets pattern matches
        preg_match( "/(-+)?\d+(\.\d{1,".$precision."})?/" , $value, $matches );

        //Returns the full pattern match
        return $matches[0];            
    }
    
}
	