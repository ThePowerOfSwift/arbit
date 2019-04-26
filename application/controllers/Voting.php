<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voting extends MY_Controller {
    
    public $voting_fee = 1;
    
    public function cast_vote_with_option(){
        $u_id = $this->session->userdata('u_id'); 
        if($u_id != ''){
            $get_user_data = $this->db->query("SELECT * FROM users WHERE u_id = ? ", array($u_id))->row();
            if($get_user_data->allow_voting == 0){
                echo json_encode(array('error'=>'1', 'msg'=>'Your account is not registerd for voting.'));
                exit; 
            }
            $topic_id = $this->input->post('topic_id');
            $option = $this->input->post('option');
            if(strlen($option) > 16){
                echo json_encode(array('error'=>'1', 'msg'=>'String length not be less or equal to 16 characters.'));
                exit;  
            }
            else if($this->db->query("select * from voting_cast WHERE user_id = ? AND topic_id = ?", array($u_id, $topic_id))->row()){
                echo json_encode(array('error'=>'1', 'msg'=>'You have already casted vote for this topic.'));
                exit;
            }else{
                // update user data  
                $this->db->query("INSERT INTO voting_cast (topic_id, user_id, selected_option) VALUES (?,?,?)", array($topic_id, $u_id, strtolower($option)));
                //$this->db->query("UPDATE voting_topics SET options = ? WHERE id = ?", array(json_encode($arr), $topic_id));
               
                echo json_encode(array('success'=>'1', 'msg'=>'Your vote has been successfully cast.'));
                exit;
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'User session expired'));
            exit;
        }
    }
    
    public function register_for_vote(){
        $u_id = $this->session->userdata('u_id'); 
        if($u_id != ''){
            $get_user_data = $this->db->query("SELECT * FROM users WHERE u_id = ? ", array($u_id))->row();
            if($get_user_data->allow_voting == 1){
                echo json_encode(array('error'=>'1', 'msg'=>'Your account already registerd for voting.'));
                exit; 
            }else{
                $get_user_wallet = $this->db->query("SELECT * FROM system_wallet WHERE user_id = ?", array($u_id))->row();
                $get_user_earned = $this->db->query("SELECT * FROM exchange_earned_wallet WHERE user_id = ?", array($u_id))->row();
                if($get_user_wallet->activeArb >= $this->voting_fee){
                    //update user wallet 
                    $new_user_arb = $get_user_wallet->activeArb - $this->voting_fee;
                    $this->db->query("UPDATE system_wallet SET activeArb = ? WHERE user_id = ?", array($new_user_arb, $u_id));
                    
                    // update admin wallet 
                    $admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1")->row();
                    $new_admin_arb = $admin_wallet->arb + $this->voting_fee;
                    $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = 1", array($new_admin_arb));
                    
                    // add logs 
                    $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'systemWallet_ARB', '-'.$this->voting_fee, 'Voting subscription fee (System wallet)', $get_user_wallet->activeArb));
                    $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'adminWallet_ARB', $this->voting_fee, 'fee', $admin_wallet->arb));
                    
                    // update user data
                    $this->db->query("UPDATE users SET allow_voting = ? WHERE u_id = ?", array(1, $u_id));
                    
                    echo json_encode(array('success'=>'1', 'msg'=>'You are successfuly subscribed for voting.'));
                    exit;
                    
                }else if($get_user_earned->activeArb >= $this->voting_fee){
                    //update user ex earned wallet 
                    $new_user_arb = $get_user_earned->activeArb - $this->voting_fee;
                    $this->db->query("UPDATE exchange_earned_wallet SET activeArb = ? WHERE user_id = ?", array($new_user_arb, $u_id));
                    
                    // update admin wallet 
                    $admin_wallet = $this->db->query("SELECT * FROM admin_wallet WHERE id = 1")->row();
                    $new_admin_arb = $admin_wallet->arb + $this->voting_fee;
                    $this->db->query("UPDATE admin_wallet SET arb = ? WHERE id = 1", array($new_admin_arb));
                    
                    // add logs 
                    $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'exchangeEarnedWallet_ARB', '-'.$this->voting_fee, 'Voting subscription fee (Exchange earned)', $get_user_earned->activeArb));
                    $this->db->query("INSERT INTO wallet_logs (user_id, type, value, comment, last_blnc) VALUES (?,?,?,?,?)", array($u_id, 'adminWallet_ARB', $this->voting_fee, 'fee', $admin_wallet->arb));
                    
                    // update user data
                    $this->db->query("UPDATE users SET allow_voting = ? WHERE u_id = ?", array(1, $u_id));
                    
                    echo json_encode(array('success'=>'1', 'msg'=>'You are successfuly subscribed for voting.'));
                    exit;
                }else{
                    echo json_encode(array('error'=>'1', 'msg'=>"You don't have Sufficient ARB in your wallets."));
                    exit; 
                }
            }
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'User session expired'));
            exit;
        }
    }
    
    public function cast_vote(){
        $u_id = $this->session->userdata('u_id'); 
        if($u_id != ''){
            $get_user_data = $this->db->query("SELECT * FROM users WHERE u_id = ? ", array($u_id))->row();
            if($get_user_data->allow_voting == 0){
                echo json_encode(array('error'=>'1', 'msg'=>'Your account is not registerd for voting.'));
                exit; 
            }
            $topic_id = $this->input->post('topic_id');
            $option = $this->input->post('option');
            if($this->db->query("select * from voting_cast WHERE user_id = ? AND topic_id = ?", array($u_id, $topic_id))->row()){
                echo json_encode(array('error'=>'1', 'msg'=>'You have already casted vote for this topic.'));
                exit;
            }else{
                $topic = $this->db->query("select * from voting_topics WHERE id = ?", array($topic_id))->row();
                $options = json_decode($topic->options);
                $arr = array();
                $allow_other = false;
                $opt_exist = false;
                foreach($options as $opt){
                    if($opt->option == $option){
                        $opt_exist = true;
                        $new_count = $opt->count + 1;
                        if($option == 'other'){ $allow_other= true;}
                    }
                    else{
                        $new_count = $opt->count;
                    }
                    
                    $arr[] = array('option' => $opt->option, 'count'=>$new_count, 'davidchoice'=>$opt->davidchoice);
                }
                if(isset($opt->allow_user) && $opt->allow_user == 1){
                    $allow_user = true;
                }
                
                if(!$opt_exist){
                    echo json_encode(array('error'=>'1', 'msg'=>'No such option exist for vote in this topic.'));
                    exit;
                }
                else if($opt_exist && $allow_other){
                    $user_option = $this->input->post('user_option');
                    if(strlen($user_option) > 16){
                        echo json_encode(array('error'=>'1', 'msg'=>'String length not greater 16 characters.'));
                        exit;  
                    }else{
                        $this->db->query("INSERT INTO voting_cast (topic_id, user_id, selected_option, other_option) VALUES (?,?,?,?)", array($topic_id, $u_id, $option, strtolower($user_option)));
                        $this->db->query("UPDATE voting_topics SET options = ? WHERE id = ?", array(json_encode($arr), $topic_id));
                       
                        echo json_encode(array('success'=>'1', 'msg'=>'Your vote has been successfully cast.'));
                    }
                    
                }
                else{
                   // update user data  
                   $this->db->query("INSERT INTO voting_cast (topic_id, user_id, selected_option) VALUES (?,?,?)", array($topic_id, $u_id, $option));
                   $this->db->query("UPDATE voting_topics SET options = ? WHERE id = ?", array(json_encode($arr), $topic_id));
                   
                   echo json_encode(array('success'=>'1', 'msg'=>'Your vote has been successfully cast.'));
                   exit;
                }
                
            }
            
        }else{
            echo json_encode(array('error'=>'1', 'msg'=>'User session expired'));
            exit;
        }
    }
    
    
    public function suggestion_list(){
        $ticket_id = $this->input->post('ticket_id');
        $result = $this->db->query('SELECT * FROM voting_cast WHERE topic_id = ? AND selected_option = ? GROUP BY other_option', array($ticket_id, 'other'))->result();
        $arr = array();
        foreach($result as $re){
            $arr[] = $re->other_option;
        }
        
        $a = implode('","',$arr);
        
        print_r('["'.$a.'"]');
    }
    
    
    public function voting_seen(){
        $u_id = $this->session->userdata('u_id');
        if($u_id){
            $this->db->query("Update users Set voting = ? WHERE u_id = ?", array(0 , $u_id));
            echo "Success";exit; 
        }
    }
    
    public function announcement_seen(){
        $u_id = $this->session->userdata('u_id');
        if($u_id){
            $this->db->query("Update users Set announcement = ? WHERE u_id = ?", array(0 , $u_id));
            echo "Success";exit; 
        }
    }
    
}