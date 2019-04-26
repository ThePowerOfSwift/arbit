<?php
class Md_users extends CI_Model {

	private $_tname = 'users';

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	/*ajouter*/
	public function add(){
		$pwd = $this->input->post('u_pwd');
		$wallet = str_replace(' ', '', $this->input->post('u_wallet'));
		$qry = $this->db->query("SELECT address FROM blacklist_addresses")->result();
		$black_list = array();
		foreach($qry as $q){
		    $black_list[] = $q->address;
		}
		//'0x59a5208b32e627891c389ebafc644145224006e8'
		if(in_array($wallet, $black_list)){
		    return false;
		}
		
		$wallet_exist = $this->db->query("select count(*) as wallet_exist from users where u_wallet = ?", array($wallet))->row()->wallet_exist;
		if($wallet_exist > 0 && $wallet != ''){
		    return false;
		}
		
		$data = array(
			'u_id' => '',
			'u_nom' => $this->input->post('u_nom'),
			'u_prenom' => $this->input->post('u_prenomnom'),
			'u_email' => htmlspecialchars($this->input->post('u_email')),
			'u_wallet' => htmlspecialchars($wallet),
			'u_pwd' => md5($pwd),
			'u_username' => htmlspecialchars($this->input->post('u_username')),
			'active' => 0, // Eric
			'u_pin' => $this->input->post('u_pin'),
			'date_created' => date('Y-m-d h:m:s')
		);
		
		return $this->db->insert($this->_tname, $data);
	}
	
	public function update_user_pass($id){
	    $pwd = $this->input->post('u_pwd');
	  $data = array(
		'u_pwd' => $pwd
	  );
	  $this->db->where('u_id',$id);
	 
	  $this->db->update($this->_tname,$data);
	}

	public function add_affiliation($userid){
		$codeAff = $this->input->post('a_code');
		$data = array(
			'a_id' => '',
			'u_id' => $userid,
			'a_code' => $codeAff
		);
		return $this->db->insert('affiliate', $data);
	}

	//all users
	public function all_users(){
		$this->db->select('*');
		$this->db->from($this->_tname);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function add_child_affiliation($userid, $codeAff){
		$data = array(
			'a_id' => '',
			'u_id' => $userid,
			'a_code' => $codeAff
		);
		return $this->db->insert('affiliate', $data);
	}

	public function child_affiliation($userid, $u_id_parent){
		$data = array(
			'child_u_id' => $userid,
			'parent_u_id' => $u_id_parent
		);
		return $this->db->insert('child_affiliate', $data);
	}

	/*liste affiliate par compte*/
	public function list_affiliate($u_id){
		$this->db->where('parent_u_id ='.$u_id);
		$query = $this->db->get_where('child_affiliate as c');
		return $query->result_array();
	}

	/*code affiliate une compte*/
	public function code_affiliate($u_id){
		$this->db->join('affiliate as a', 'a.u_id ='.$u_id);
		$this->db->where('u.u_id = a.u_id');
		$query = $this->db->get_where('users as u');
		//print_r($query->result_array());exit;
		return $query->result_array();
	}

	public function recuperer_id($email) {
      $query = $this->db->get_where($this->_tname, array('u_email' => $email), 1, 0);
      $compte = $query->result_array();
      foreach ($compte as $user) {
          return $user['u_id'];
      }
      return null;
  }

	public function recuperer_u_id_parent($codeAff) {
		$query = $this->db->get_where('affiliate', array('a_code' => $codeAff), 1, 0);
		$compte = $query->result_array();
		foreach ($compte as $user) {
			return $user['u_id'];
		}
		return null;
  }
  
    public function get_pin($email, $pwd){
        $qry = $this->db->query("SELECT * FROM users WHERE u_email = ? AND u_pwd = ?", array($email, $pwd))->row();
    	return $qry;
    }

	//resolve login
	public function get_user($email, $pwd) {
	    $qry = $this->db->query("SELECT * FROM users WHERE u_email = ? AND u_pwd = ?", array($email, md5($pwd)))->row();
	    return $qry;
	}

	public function get_user_id($user_id) {
		$this->db->from($this->_tname);
		$this->db->where('u_id', $user_id);
		return $this->db->get()->row();
	}

	public function get_user_id_from_email($email) {
		$this->db->select('u_id');
		$this->db->from($this->_tname);
		$this->db->where('u_email', $email);
		//$this->db->or_where('u_username', $email);
		return $this->db->get()->row('u_id');
	}
	public function update_user($email,$mdp){
	  $data = array(
		'u_email' =>$email,
		'u_pwd' => md5($mdp),
		'active'=>'1'
	  );
	  $this->db->where('u_email',$email);
	 
	  $this->db->update($this->_tname,$data);
	}
	
	public function update_user_wallet($id,$wallet){
	  $user = $this->db->query("SELECT u_email, u_wallet FROM users where u_id = ?", array($id))->row();  
	  if($wallet != $user->u_wallet){
	        $this->db->query("INSERT INTO user_activity_log (user_id, activity, comment) VALUES (?,?,?)", array($id, "wallet_change", "User change ETH wallet (".$user->u_wallet.")"));
	  }
// 	  if($email != $user->u_email){
// 	        $this->db->query("INSERT INTO user_activity_log (user_id, activity, comment) VALUES (?,?,?)", array($id, "email_change", "User change email (".$user->u_email.")"));
// 	  }
	  $current_time = date("Y-m-d H:i:s");
	  $data = array(
		'u_wallet' => $wallet,
		'ew_status' => $current_time
	  );
	  $this->db->where('u_id',$id);
	 
	  $this->db->update($this->_tname,$data);
	}
	
	public function update_user_pin($id,$pin){
	  $data = array(
		'u_pin' => $pin
	  );
	  $this->db->where('u_id',$id);
	 
	  $this->db->update($this->_tname,$data);
	}
	
	public function update2fa($id,$pin){
	  $data = array(
		'allow_pin' => $pin
	  );
	  $this->db->where('u_id',$id);
	 
	  $this->db->update($this->_tname,$data);
	}
	
}
