<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MY_Controller {

    public function find_user(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($this->db->query("SELECT package FROM users WHERE u_id = ?", array($u_id))->row()->package == "Advance" || "Pro"){
                $email = htmlspecialchars($this->input->post('email'));
                if(!empty($email) || $email != null){
                    if($user = $this->db->query("SELECT * FROM users WHERE u_email = ? AND package = ? OR u_email = ? AND package = ?", array($email, "Pro", $email, "Pro+"))->row()){
                        if($user->u_id == $u_id){
                            echo json_encode(array('error'=>'1', 'msg' => "Your are searching youself.")); exit;
                        }
                        else{
                            echo json_encode(array('success'=>'1', 'msg' => "User Found.", 'u_id' => $user->u_id)); exit;
                        }
                    }
                    else{
                        echo json_encode(array('error'=>'1', 'msg' => "User Not Found OR User package is not Pro.")); exit;
                    }
                }
            }else{
                echo json_encode(array('error'=>'1', 'msg' => "User package is not Pro.")); exit;
            }
        }else{
            echo "Session Expired"; exit;
        }
    }

    public function generate_request(){
        $main_user_id = $this->session->userdata('u_id');
        if($main_user_id != ''){
            if($this->db->query("SELECT package FROM users WHERE u_id = ?", array($main_user_id))->row()->package == "Pro+" || "Pro"){
                $give_access_to_user_id = $this->input->post('user_id');
                $pin = $this->input->post('pin');
                if(!empty($give_access_to_user_id) || $give_access_to_user_id != null){
                    if((!empty($pin) || $pin != null) && (strlen($pin) == 6)){
                        if($main_user_id == $give_access_to_user_id){
                            echo json_encode(array('error'=>'1', 'msg' => "Invalid Request.")); exit;
                        }
                        else{
                            $pin = md5($pin);
                            $user = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($give_access_to_user_id))->row();
                            if($user->package == "Pro" && $user->access_limit < 5){
                                if($this->db->query("SELECT * FROM access_req WHERE main_user_id = ?", array($main_user_id))->row()){
                                    echo json_encode(array('error'=>'1', 'msg' => "Request already Submitted.")); exit;
                                }
                                else{
                                    //generate_request
                                    $this->db->query("INSERT into access_req (main_user_id, user_id, pin) values (?,?,?)", array($main_user_id, $give_access_to_user_id, $pin));
                                    //update_limit
                                    $updated_limit = $user->access_limit + 1;
                                    $this->db->query("UPDATE users SET access_limit = ? WHERE u_id = ?", array($updated_limit , $give_access_to_user_id));
                                    echo json_encode(array('success'=>'1', 'msg' => "Your request is submitted successfully."));exit;
                                }
                            }
                            // else if($user->package == "Pro+" && $user->access_limit < 20){
                            //     if($this->db->query("SELECT * FROM access_req WHERE main_user_id = ?", array($main_user_id))->row()){
                            //         echo json_encode(array('error'=>'1', 'msg' => "Request already Submitted.")); exit;
                            //     }
                            //     else{
                            //         //generate_request
                            //         $this->db->query("INSERT into access_req (main_user_id, user_id, pin) values (?,?,?)", array($main_user_id, $give_access_to_user_id, $pin));
                            //         //update_limit
                            //         $updated_limit = $user->access_limit + 1;
                            //         $this->db->query("UPDATE users SET access_limit = ? WHERE u_id = ?", array($updated_limit , $give_access_to_user_id));
                            //         echo json_encode(array('success'=>'1', 'msg' => "Your request is submitted successfully."));exit;
                            //     }
                            // }
                            else{
                                echo json_encode(array('error'=>'1', 'msg' => "Users Access Limit Reached to Full.")); exit;
                            }
                        }
                    }
                    else{
                        echo json_encode(array('error'=>'1', 'msg' => "Pin is Invalid.")); exit;
                    }
                }
                else{
                    echo json_encode(array('error'=>'1', 'msg' => "User id is Empty.")); exit;
                }
            }
            else{
                echo json_encode(array('error'=>'1', 'msg' => "User package is not Pro.")); exit;
            }
        }
        else{
            echo "Session Expired"; exit;
        }
    }
    
    public function deny_request(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($this->db->query("SELECT package FROM users WHERE u_id = ?", array($u_id))->row()->package == "Pro+" || "Pro"){
                $get_access_req = $this->db->query("SELECT * FROM access_req WHERE main_user_id = ?", array($u_id))->row();
                if(isset($get_access_req)){
                     // log -- send it to history.
                    $this->db->query("INSERT into access_req_history (main_user_id, user_id, pin, status) values (?,?,?,?)", array($get_access_req->main_user_id, $get_access_req->user_id, $get_access_req->pin, $get_access_req->status));
                     // delete requests.
                    $this->db->query("DELETE FROM access_req WHERE main_user_id = ?", array($u_id));
                    
                    $get_access_verified = $this->db->query("SELECT * FROM access_verified WHERE accessed_user_id = ?", array($u_id))->row();
                    if(isset($get_access_verified)){
                        // log -- send it to history.
                        $this->db->query("INSERT into access_verified_history (user_id, accessed_user_id) values (?,?)", array($get_access_verified->user_id, $get_access_verified->accessed_user_id));
                        // delete requests.
                        $this->db->query("DELETE FROM access_verified WHERE accessed_user_id = ?", array($get_access_verified->accessed_user_id));
                        
                    }
                    
                    //update access_limit
                    $user = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($get_access_req->user_id))->row();
                    $updated_limit = $user->access_limit - 1;
                    $this->db->query("UPDATE users SET access_limit = ? WHERE u_id = ?", array($updated_limit , $get_access_req->user_id));
                    
                     echo json_encode(array('success'=>'1', 'msg' => "Access Successfully Denied.")); exit;
                }
                else{
                    echo json_encode(array('error'=>'1', 'msg' => "No Record Found.")); exit;
                }
            }
            else{
                echo json_encode(array('error'=>'1', 'msg' => "User package is not Pro.")); exit;
            }
        }else{
            echo "Session Expired"; exit;
        }
    }
    
    public function verify_access(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $user_pkg = $this->db->query("SELECT package FROM users WHERE u_id = ?", array($u_id))->row()->package;
            if($user_pkg == "Pro+" || $user_pkg == "Pro"){
                $pin = $this->input->post('pin');
                $accessed_user_id = $this->input->post('accessed_user_id');
                if($accessed_user_id != null){
                    if(($pin != null) && (strlen($pin) == 6)){
                        if($u_id == $accessed_user_id){
                            echo json_encode(array('error'=>'1', 'msg' => "Invalid Request.")); exit;
                        }
                        else{
                            $pin = md5($pin);
                            if($req = $this->db->query("SELECT * FROM access_req WHERE user_id = ? AND main_user_id = ? AND pin = ? AND status = ?", array($u_id, $accessed_user_id, $pin, 0))->row()){
                                $this->db->query("INSERT into access_verified (user_id, accessed_user_id) values (?,?)", array($u_id, $accessed_user_id));
                                $this->db->query("UPDATE access_req SET status = ? WHERE id = ?", array(1 , $req->id));
                                echo json_encode(array('success'=>'1', 'msg' => "Access Granted.")); exit;
                            }
                            else{
                                echo json_encode(array('error'=>'1', 'msg' => "Access Denied, Invalid Pin.")); exit;
                            }
                        }
                    } 
                    else{
                        echo json_encode(array('error'=>'1', 'msg' => "Access Denied, Invalid Pin.")); exit;
                    }
                }else{
                    echo json_encode(array('error'=>'1', 'msg' => "Access Denied, Invalid Request.")); exit;
                }
            }else{
                echo json_encode(array('error'=>'1', 'msg' => "User package is not Pro.")); exit;
            }
        }else{
            echo "Session Expired"; exit;
        }
    }
    
    public function switch_session(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            if($this->db->query("SELECT package FROM users WHERE u_id = ?", array($u_id))->row()->package == "Pro+" || "Pro"){
                
                $original_user_id = $this->input->post('original_user_id');
                
                if($original_user_id != null){
                    if($this->db->query("SELECT * FROM access_verified WHERE user_id = ? AND accessed_user_id = ?", array($original_user_id, $u_id))->row()){
                        // switch session ----- 
                        $user = $this->db->query("SELECT * FROM users WHERE u_id = ? AND allow_login = ? AND active = ?", array($original_user_id, 1, 1))->row();
                        if(!isset($user->u_id)){
                            echo json_encode(array('error'=>'1', 'msg' => "User Blocked by ADMIN.")); exit;                                     
                        }
                        $sessiondata = array(
            				'u_id'      => $user->u_id,
            				'u_nom'     => $user->u_nom,
            				'u_prenom'  => $user->u_prenom,
            				'u_email'    => $user->u_email,
            				'u_username'    => $user->u_username,
            				'logged_in' => TRUE,
            				'original_session' => 0
            			);
                        $this->session->set_userdata($sessiondata);
        	    	   echo json_encode(array('success'=>'1', 'msg' => "Successfully Switched.")); exit;
                    }
                    else{
                        echo json_encode(array('error'=>'1', 'msg' => "Invalid Request.")); exit;
                    }
                
                }else{
                    
                    $accessed_user_id = $this->input->post('accessed_user_id');
                
                    if(!empty($accessed_user_id) || $accessed_user_id != null){
                        if($u_id == $accessed_user_id){
                            echo json_encode(array('error'=>'1', 'msg' => "Invalid Request.")); exit;
                        }
                        else{
                            if($this->db->query("SELECT * FROM access_verified WHERE user_id = ? AND accessed_user_id = ?", array($u_id, $accessed_user_id))->row()){
                                
                                // switch session ----- 
                                $user = $this->db->query("SELECT * FROM users WHERE u_id = ? AND allow_login = ? AND active = ?", array($accessed_user_id, 1, 1))->row();
                                if(!isset($user->u_id)){
                                    echo json_encode(array('error'=>'1', 'msg' => "User Blocked by ADMIN.")); exit;                                     
                                }
                                //session delete of accessing user
                                $this->db->query("DELETE FROM ci_sessions WHERE data LIKE '%".$accessed_user_id."%'");
                                
                                $sessiondata = array(
                    				'u_id'      => $user->u_id,
                    				'u_nom'     => $user->u_nom,
                    				'u_prenom'  => $user->u_prenom,
                    				'u_email'    => $user->u_email,
                    				'u_username'    => $user->u_username,
                    				'logged_in' => TRUE,
                    				'original_session' => $u_id,
                    			);

                	    	    $this->session->set_userdata($sessiondata);
                	    	    echo json_encode(array('success'=>'1', 'msg' => "Successfully Switched.")); exit;
                            }
                            else{
                                echo json_encode(array('error'=>'1', 'msg' => "Invalid Request.")); exit;
                            }
                        }
                    }else{
                        echo json_encode(array('error'=>'1', 'msg' => "Invalid Request.")); exit;
                    }
                }
            }else{
                echo json_encode(array('error'=>'1', 'msg' => "User package is not Pro.")); exit;
            }
        }else{
            echo "Session Expired"; exit;
        }
    }
}
	