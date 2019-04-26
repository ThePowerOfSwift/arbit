<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Admin extends MY_Controller {
    private $allowed = [43, 60221, 14259, 49, 52, 54, 55, 60, 62, 65, 74, 80, 89, 90, 147, 170, 54112, 109, 115, 127, 176, 445, 567, 593, 634, 656, 699, 780, 800, 846, 900, 902, 932, 942, 1042, 1043, 1213, 4231, 9804, 9819, 9829, 9845, 9850, 9892, 10008, 13587, 13617, 13637, 13706, 13720, 13791, 13852, 13870, 13968, 14241, 15371, 15461, 15651, 15701, 15728, 15834, 15879, 15923, 15925, 16056, 16406, 16580, 16985, 17370, 17506, 18916, 19360, 19724, 20081, 20664, 26337, 26461, 27669, 28719, 29681, 32959, 36498, 37004, 31487, 33464, 36168, 37082, 37129, 45215, 55002, 51876, 51951, 52397, 52537, 52547, 52561, 52825, 52861, 52954, 53012, 53054, 53077, 53104, 53179, 53248, 53310, 53468, 53510, 53589, 53714, 53706, 53794, 53812, 53830, 53963, 54030, 54110, 54115, 54118, 54125, 54141, 54991, 54998, 55180, 55191, 55193, 55317, 55322, 55327, 55351, 55353, 55356, 55407, 55418, 55471, 55446, 55481, 55482, 55487, 55516, 55566, 55624, 55645, 55657, 55663, 55699, 55729, 55745, 55768, 55808, 55811, 55872, 55874, 55879, 55883, 55883, 55902, 55913, 55924, 55925, 55985, 56933, 55948, 55972, 56004, 56025, 56502, 56518, 56544, 56772, 56746, 56753, 56768, 56826, 56827, 56836, 56837, 56839, 56872, 56873, 56875, 56867, 56878, 57309, 57712, 57553, 57592, 57596, 57577, 57588, 57597, 57605, 57668, 57824, 57880, 57882, 57957, 57991, 58007, 58132, 58170, 58179, 58662, 59570];
    public function __construct() {
        parent::__construct();
        $this->load->model('md_users');
    }
    
    public function user_ann($user_id){
        $curr = date('Y-m-d H:i:s');
        if($ann = $this->db->query("SELECT * FROM user_announcement WHERE user_id = ? AND status = 1 AND expire_time > ?", array($user_id, $curr))->row()){
            return $ann;
        }else{
            return false;
        }
    }

    public function uncon(){
        return redirect('https://www.arbitraging.co/platform/underconstruction.html');
    } 
    public function index_old(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');

        $sql = "SELECT * FROM system_wallet WHERE user_id = $u_id";
        $s_data = $this->db->query($sql)->result();

        $sql = "SELECT * FROM abot_wallet WHERE user_id = $u_id";
        $a_data = $this->db->query($sql)->result();

        $sql = "SELECT * FROM exchange_wallet WHERE user_id = $u_id";
        $e_data = $this->db->query($sql)->result();
        
        $sql = "SELECT * FROM exchange_earned_wallet WHERE user_id = $u_id";
        $ee_data = $this->db->query($sql)->result();

        if(empty($s_data)){
            $sql = "INSERT INTO system_wallet (user_id, activeArb, activeEth) VALUES ($u_id,0,0)";
            $this->db->query($sql);

            $sql = "INSERT INTO abot_wallet (user_id, pending, active, profit, earned) VALUES ($u_id,0,0,0,0)";
            $this->db->query($sql);

            $sql = "INSERT INTO exchange_wallet (user_id, activeArb, activeEth) VALUES ($u_id,0,0)";
            $this->db->query($sql);
            
            $sql = "INSERT INTO exchange_earned_wallet (user_id, activeArb) VALUES ($u_id,0)";
            $this->db->query($sql);

            $sql = "SELECT * FROM system_wallet WHERE user_id = $u_id";
            $s_data = $this->db->query($sql)->result();
            $this->data['s_data'] = $s_data;

            $sql = "SELECT * FROM abot_wallet WHERE user_id = $u_id";
            $a_data = $this->db->query($sql)->result();
            $this->data['a_data'] = $a_data;

            $sql = "SELECT * FROM exchange_wallet WHERE user_id = $u_id";
            $e_data = $this->db->query($sql)->result();
            $this->data['e_data'] = $e_data;
            
            $sql = "SELECT * FROM exchange_earned_wallet WHERE user_id = $u_id";
            $ee_data = $this->db->query($sql)->result();
            $this->data['ee_data'] = $ee_data;
        }
        else {
            $this->data['s_data'] = $s_data;
            $this->data['a_data'] = $a_data;
            $this->data['e_data'] = $e_data;
            $this->data['ee_data'] = $ee_data;
        }
        
        if($a = $this->user_ann($u_id)){
            $this->data['ann'] = $a->msg.''; 
        }else{
            $this->data['ann'] = 'false';
        }
        
        $this->data['code'] = $this->md_users->code_affiliate($u_id);

        $codeAff = $this->codeAff();
        $affiliate_data = $this->db->query("SELECT a_code FROM affiliate WHERE u_id = $u_id")->row();
        if($affiliate_data->a_code == "" || $affiliate_data->a_code == NULL){
            $affiliate_sql = "UPDATE affiliate SET a_code = '$codeAff' WHERE u_id = $u_id";
		    $this->db->query($affiliate_sql);
        }
        if(!isset($affiliate_data)){
            $affiliate_sql = "INSERT INTO affiliate (a_code, u_id) VALUES ('$codeAff',$u_id)";
		    $this->db->query($affiliate_sql);
        }
    	        
        $this->back('dashboard');
    }
    
        public function trading(){
            if($this->session->userdata('u_id') == FALSE) {
                redirect('login');
            }
            $u_id = $this->session->userdata('u_id');
            $this->data['exchanges_data'] = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ?", array($u_id))->result();
            
            $this->back('trading');
    
        }
    
       public function index(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');

        $sql = "SELECT * FROM system_wallet WHERE user_id = $u_id";
        $s_data = $this->db->query($sql)->result();

        $sql = "SELECT * FROM abot_wallet WHERE user_id = $u_id";
        $a_data = $this->db->query($sql)->result();

        $sql = "SELECT * FROM exchange_wallet WHERE user_id = $u_id";
        $e_data = $this->db->query($sql)->result();

        $sql = "SELECT * FROM stop_abot_wallet WHERE user_id = $u_id";
        $stop_data = $this->db->query($sql)->result();
        
        $sql = "SELECT * FROM exchange_earned_wallet WHERE user_id = $u_id";
        $ee_data = $this->db->query($sql)->result();

        if(empty($s_data)){
            $sql = "INSERT INTO system_wallet (user_id, activeArb, activeEth) VALUES ($u_id,0,0)";
            $this->db->query($sql);

            $sql = "INSERT INTO abot_wallet (user_id, pending, active, profit, earned) VALUES ($u_id,0,0,0,0)";
            $this->db->query($sql);

            $sql = "INSERT INTO exchange_wallet (user_id, activeArb, activeEth) VALUES ($u_id,0,0)";
            $this->db->query($sql);
            
            $sql = "INSERT INTO stop_abot_wallet (user_id, activeArb) VALUES ($u_id,0)";
            $this->db->query($sql);
            
            $sql = "INSERT INTO exchange_earned_wallet (user_id, activeArb) VALUES ($u_id,0)";
            $this->db->query($sql);

            $sql = "SELECT * FROM system_wallet WHERE user_id = $u_id";
            $s_data = $this->db->query($sql)->result();
            $this->data['s_data'] = $s_data;

            $sql = "SELECT * FROM abot_wallet WHERE user_id = $u_id";
            $a_data = $this->db->query($sql)->result();
            $this->data['a_data'] = $a_data;

            $sql = "SELECT * FROM exchange_wallet WHERE user_id = $u_id";
            $e_data = $this->db->query($sql)->result();
            $this->data['e_data'] = $e_data;
            
            $sql = "SELECT * FROM stop_abot_wallet WHERE user_id = $u_id";
            $stop_data = $this->db->query($sql)->result();
            $this->data['stop_data'] = $stop_data;
            
            $sql = "SELECT * FROM exchange_earned_wallet WHERE user_id = $u_id";
            $ee_data = $this->db->query($sql)->result();
            $this->data['ee_data'] = $ee_data;
            
        }
        else {
            $this->data['s_data'] = $s_data;
            $this->data['a_data'] = $a_data;
            $this->data['e_data'] = $e_data;
            $this->data['stop_data'] = $stop_data;
            $this->data['ee_data'] = $ee_data;
        }
        
        if($a = $this->user_ann($u_id)){
            $this->data['ann'] = $a->msg.''; 
        }else{
            $this->data['ann'] = 'false';
        }
        $this->data['code'] = $this->md_users->code_affiliate($u_id);
        
        $sql = "SELECT activeArb FROM pro_plus_wallet WHERE user_id = $u_id";
        $pp_data = $this->db->query($sql)->row()->activeArb;
        
        if(isset($pp_data)){
            $this->data['pp_status'] = 1; 
            if($pp_data > 0.1){
                $this->data['pp_data'] = $pp_data;
            }
            else{
                $this->data['pp_data'] = 0;
            }
        }
        else{
            $this->data['pp_status'] = 0; 
        }

        $this->back('dashboard_beta');
    }
    
    // public function mbotv2_bilal(){
    //     if($this->session->userdata('u_id') == FALSE) {
    //         redirect('login');
    //     }
    //     $u_id = $this->session->userdata('u_id');
    //     if($u_id == 13870) {
    //         $this->back('mbotv2_bilal');
    //     }
    // }
    
    
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
    
    // public function abot2_beta2(){
    //     if($this->session->userdata('u_id') == FALSE) {
    //         redirect('login');
    //     }
    //     $u_id = $this->session->userdata('u_id');

    //     $sql = "SELECT * FROM abot_wallet WHERE user_id = $u_id";
    //     $abot_data = $this->db->query($sql)->result();
    //     $this->data['abotData'] = $abot_data;

    //     $sql = "SELECT * FROM commission";
    //     $commissions = $this->db->query($sql)->result();
    //     $day = Date('d', strtotime('-4 hour'));
    //     $new = array_slice($commissions, $day-1);

    //     for($i=0; $i<=$day-1; $i++){
    //         array_push($new , $commissions[$i]);
    //     }
    //     $this->data['commission'] = $new;
        
    //   /*$day = date('d');
    //     $sql = "SELECT * FROM commission WHERE date=$day";
    //     $commissions = $this->db->query($sql)->row()->value;
        
    //     $this->data['commission'] = $commissions;*/
        
    //   $arb_value = doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
    //   //$arb_db_value = $this->db->query("SELECT * FROM abot_price WHERE name = ?", array('abot_price'))->row()->value;
    //   //$avg_arb = ($arb_db_value + $arb_value)/2;
       
    // //   if($arb_value > $arb_db_value){
    // //         $this->data['arb_db_value'] = $arb_value;
    // //   }else{
    // //       $this->data['arb_db_value'] = $arb_db_value;
    // //   }
       
    //   $this->data['arb_db_value'] = $arb_value;
       
    //     // $sql = "SELECT * FROM user_transactions WHERE user_id = $u_id";
    //     // $this->data['transactions'] = $this->db->query($sql)->result();
        
    //     $sql = "SELECT * FROM abot_wallet WHERE user_id = $u_id ";
    //     $auto_reinvest = $this->db->query($sql)->row();
        
    //     $this->data['auto_reinvest'] = $auto_reinvest->auto_reinvest;
    //     $this->data['eth_payout'] = $auto_reinvest->eth_payout;
    //     $this->data['eth_earned'] = $auto_reinvest->eth_earned;
    //     if($auto_reinvest->audit_per != null){
    //         $this->data['audit_per'] = $auto_reinvest->audit_per;
    //     }else{
    //         $this->data['audit_per'] = -1;
    //     }
        
    //     $this->data['old_user'] = $auto_reinvest->old_user;
        

    //     $this->data['code'] = $this->md_users->code_affiliate($u_id);
        
    //     $this->data['arb_abot_price'] = ARB_VALUE_IN_USD;
        
    //     $sql = "SELECT activeEth FROM abot_eth_bonus WHERE user_id = $u_id";
    //     $activeEth_abot = $this->db->query($sql)->row();
    //     if($activeEth_abot){
    //         $this->data['activeEth_abot'] = $activeEth_abot->activeEth;
    //     }else{
    //         $this->data['activeEth_abot'] = 0;
    //     }

    //     $this->back('abot2');

    // }
    
    public function abot2(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');

        $sql = "SELECT * FROM abot_wallet WHERE user_id = $u_id";
        $abot_data = $this->db->query($sql)->result();
        $this->data['abotData'] = $abot_data;

        $sql = "SELECT * FROM commission";
        $commissions = $this->db->query($sql)->result();
        $day = Date('d', strtotime('-4 hour'));
        $new = array_slice($commissions, $day-1);

        for($i=0; $i<=$day-1; $i++){
            array_push($new , $commissions[$i]);
        }
        $this->data['commission'] = $new;
        
       /*$day = date('d');
        $sql = "SELECT * FROM commission WHERE date=$day";
        $commissions = $this->db->query($sql)->row()->value;
        
        $this->data['commission'] = $commissions;*/
        
       $arb_value = doubleval(file_get_contents("https://www.arbitraging.co/platform/abot_arb"));
       //$arb_db_value = $this->db->query("SELECT * FROM abot_price WHERE name = ?", array('abot_price'))->row()->value;
       //$avg_arb = ($arb_db_value + $arb_value)/2;
       
    //   if($arb_value > $arb_db_value){
    //         $this->data['arb_db_value'] = $arb_value;
    //   }else{
    //       $this->data['arb_db_value'] = $arb_db_value;
    //   }
       
       $this->data['arb_db_value'] = $arb_value;
       
        // $sql = "SELECT * FROM user_transactions WHERE user_id = $u_id";
        // $this->data['transactions'] = $this->db->query($sql)->result();
        
        $sql = "SELECT * FROM abot_wallet WHERE user_id = $u_id ";
        $auto_reinvest = $this->db->query($sql)->row();
        
        $this->data['auto_reinvest'] = $auto_reinvest->auto_reinvest;
        $this->data['eth_payout'] = $auto_reinvest->eth_payout;
        $this->data['eth_earned'] = $auto_reinvest->eth_earned;
        if($auto_reinvest->audit_per != null){
            $this->data['audit_per'] = $auto_reinvest->audit_per;
        }else{
            $this->data['audit_per'] = -1;
        }
        
        $this->data['old_user'] = $auto_reinvest->old_user;
        

        $this->data['code'] = $this->md_users->code_affiliate($u_id);
        
        $this->data['arb_abot_price'] = ARB_VALUE_IN_USD;
        
        $sql = "SELECT activeEth FROM abot_eth_bonus WHERE user_id = $u_id";
        $activeEth_abot = $this->db->query($sql)->row();
        if($activeEth_abot){
            $this->data['activeEth_abot'] = $activeEth_abot->activeEth;
        }else{
            $this->data['activeEth_abot'] = 0;
        }
        
        $this->back('abot4');

    }

    public function deposit(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');
        $this->data['code'] = $this->md_users->code_affiliate($u_id);
        $this->back('deposit');
    }

    public function lending(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $this->back('lending');
    }

    // public function exchange_beta_a(){
    //     if($this->session->userdata('u_id') == FALSE) {
    //         redirect('login');
    //     }
    //     $sql = "SELECT fee FROM order_fee WHERE status = 1 AND currency_id = 1";
    //     $ArbFee = $this->DB2->query($sql)->row()->fee;

    //     $sql = "SELECT fee FROM order_fee WHERE status = 1 AND currency_id = 2";
    //     $EthFee = $this->DB2->query($sql)->row()->fee;

    //     $u_id = $this->session->userdata('u_id');

    //     $sql = "SELECT * FROM exchange_wallet WHERE user_id = $u_id";
    //     $ex_data = $this->db->query($sql)->result();
    //     $this->data['exData'] = $ex_data;

    //     $this->data['arbfee'] = $ArbFee;
    //     $this->data['ethfee'] = $EthFee;

    //     $this->data['user_id'] = $u_id;
        
        
    //     $ex_data = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = $u_id")->row();
    //     $this->data['auto_buy_status'] = $ex_data->auto_buy_status;
    //     $this->data['auto_buy_amount'] = $ex_data->auto_buy_amount;
    //     $this->data['auto_buy_min'] = $ex_data->auto_buy_min;
        
    //     $this->data['auto_sell_status'] = $ex_data->auto_sell_status;
    //     $this->data['auto_sell_amount'] = $ex_data->auto_sell_amount;
    //     $this->data['auto_sell_min'] = $ex_data->auto_sell_min;
        
    //     $this->data['code'] = $this->md_users->code_affiliate($u_id);

    //     $this->back('exchange_beta');
    // }
    
    public function exchange(){
        
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $sql = "SELECT fee FROM order_fee WHERE status = 1 AND currency_id = 1";
        $ArbFee = $this->DB2->query($sql)->row()->fee;

        $sql = "SELECT fee FROM order_fee WHERE status = 1 AND currency_id = 2";
        $EthFee = $this->DB2->query($sql)->row()->fee;

        $u_id = $this->session->userdata('u_id');

        $sql = "SELECT * FROM exchange_wallet WHERE user_id = $u_id";
        $ex_data = $this->db->query($sql)->result();
        $this->data['exData'] = $ex_data;

        $this->data['arbfee'] = $ArbFee;
        $this->data['ethfee'] = $EthFee;

        $this->data['user_id'] = $u_id;
        
        
        $ex_data = $this->db->query("SELECT * FROM exchange_wallet WHERE user_id = $u_id")->row();
        $this->data['auto_buy_status'] = $ex_data->auto_buy_status;
        $this->data['auto_buy_amount'] = $ex_data->auto_buy_amount;
        $this->data['auto_buy_min'] = $ex_data->auto_buy_min;
        
        $this->data['auto_sell_status'] = $ex_data->auto_sell_status;
        $this->data['auto_sell_amount'] = $ex_data->auto_sell_amount;
        $this->data['auto_sell_min'] = $ex_data->auto_sell_min;
        
        $this->data['code'] = $this->md_users->code_affiliate($u_id);

        $this->back('exchange_beta');
    }
    
    
    // public function exchange1(){
    //     if($this->session->userdata('u_id') == FALSE) {
    //         redirect('login');
    //     }
    //     $sql = "SELECT fee FROM order_fee WHERE status = 1 AND currency_id = 1";
    //     $ArbFee = $this->DB2->query($sql)->row()->fee;

    //     $sql = "SELECT fee FROM order_fee WHERE status = 1 AND currency_id = 2";
    //     $EthFee = $this->DB2->query($sql)->row()->fee;

    //     $u_id = $this->session->userdata('u_id');

    //     $sql = "SELECT * FROM exchange_wallet WHERE user_id = $u_id";
    //     $ex_data = $this->db->query($sql)->result();
    //     $this->data['exData'] = $ex_data;

    //     $this->data['arbfee'] = $ArbFee;
    //     $this->data['ethfee'] = $EthFee;

    //     $this->data['user_id'] = $u_id;

    //     $this->back('exchange1');
    // }

    public function wallet_old(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');

        $sql = "SELECT * FROM system_wallet WHERE user_id = $u_id";
        $data = $this->db->query($sql)->result();

        if(empty($data)){
            $sql = "INSERT INTO system_wallet (user_id, activeArb, activeEth) VALUES ($u_id,0,0)";
            $this->db->query($sql);

            $sql = "SELECT * FROM system_wallet WHERE user_id = $u_id";
            $data = $this->db->query($sql)->result();
            $this->data['tokens'] = $data;
        }
        else {
            $this->data['tokens'] = $data;
        }
        
        // USer Pending balance
        $this->data['pending_arb'] = $this->db->query("SELECT sum(value) as arbsum FROM arb_deposit_hold WHERE user_id = ? AND status = ?", array($u_id, 0))->row()->arbsum;
        //----------------------
        
        if($a = $this->user_ann($u_id)){
            $this->data['ann'] = $a->msg.''; 
        }else{
            $this->data['ann'] = 'false';
        }

        $this->data['code'] = $this->md_users->code_affiliate($u_id);

        $sql = "SELECT withdraw_status FROM users WHERE u_id = $u_id";
        $wdStatus = $this->db->query($sql)->row()->withdraw_status;
        $this->data['withdraw_status'] = $wdStatus;
        
        
        $sql = "SELECT * FROM mbot_members WHERE user_id = $u_id";
        if($mobtStatus = $this->db->query($sql)->row()){
            $this->data['mbot_status'] = $mobtStatus->status;
        }
        else{
            $this->data['mbot_status'] = 0;
        }
        
        // external wallet balance 
        if($ex_wallet = $this->db->query("SELECT * FROM external_wallet WHERE user_id = ?", array($u_id))->row()){
            $this->data['external_wallet_arb'] = $ex_wallet->activeArb;
        }else{
            $this->db->query("INSERT INTO external_wallet (user_id, activeArb) values (?,?)", array($u_id, 0));
            $this->data['external_wallet_arb'] = 0;
        }
        
        // pro_plus wallet balance 
        if($pp_wallet = $this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = ?", array($u_id))->row()){
            $this->data['pp_wallet_activeArb'] = $pp_wallet->activeArb;
            $this->data['pp_wallet_freeArb'] = $pp_wallet->freeArb;
        }else{
            $this->data['pp_wallet_activeArb'] = 0;
            $this->data['pp_wallet_freeArb'] = 0;
        }
        
        if($current_add_on = $this->db->query("SELECT * FROM user_add_ons WHERE user_id = $u_id AND add_on_name = 'Pro+'")->row()){
            $this->data['user_current_add_on'] = 1;
        }
        else{
            $this->data['user_current_add_on'] = 0;
        }
        
        $this->data['user_id'] = $u_id;

        $this->back('wallet_beta');
    }
    
    public function wallet(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');

        $sql = "SELECT * FROM system_wallet WHERE user_id = $u_id";
        $data = $this->db->query($sql)->result();

        if(empty($data)){
            $sql = "INSERT INTO system_wallet (user_id, activeArb, activeEth) VALUES ($u_id,0,0)";
            $this->db->query($sql);

            $sql = "SELECT * FROM system_wallet WHERE user_id = $u_id";
            $data = $this->db->query($sql)->result();
            $this->data['tokens'] = $data;
        }
        else {
            $this->data['tokens'] = $data;
        }
        
        // USer Pending balance
        $this->data['pending_arb'] = $this->db->query("SELECT sum(value) as arbsum FROM arb_deposit_hold WHERE user_id = ? AND status = ?", array($u_id, 0))->row()->arbsum;
        //----------------------
        
        if($a = $this->user_ann($u_id)){
            $this->data['ann'] = $a->msg.''; 
        }else{
            $this->data['ann'] = 'false';
        }

        $this->data['code'] = $this->md_users->code_affiliate($u_id);

        $sql = "SELECT withdraw_status FROM users WHERE u_id = $u_id";
        $wdStatus = $this->db->query($sql)->row()->withdraw_status;
        $this->data['withdraw_status'] = $wdStatus;
        
        
        $sql = "SELECT * FROM mbot_members WHERE user_id = $u_id";
        if($mobtStatus = $this->db->query($sql)->row()){
            $this->data['mbot_status'] = $mobtStatus->status;
        }
        else{
            $this->data['mbot_status'] = 0;
        }
        
        // external wallet balance 
        if($ex_wallet = $this->db->query("SELECT * FROM external_wallet WHERE user_id = ?", array($u_id))->row()){
            if($ex_wallet->activeArb < 0.0001){
                $ex_wallet->activeArb = 0;
            }
            $this->data['external_wallet_arb'] = $ex_wallet->activeArb;
        }else{
            $this->db->query("INSERT INTO external_wallet (user_id, activeArb) values (?,?)", array($u_id, 0));
            $this->data['external_wallet_arb'] = 0;
        }
        // pro_plus wallet balance 
        if($pp_wallet = $this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = ?", array($u_id))->row()){
            $this->data['pp_wallet_activeArb'] = $pp_wallet->activeArb;
            $this->data['pp_wallet_freeArb'] = $pp_wallet->freeArb;
        }else{
            $this->data['pp_wallet_activeArb'] = 0;
            $this->data['pp_wallet_freeArb'] = 0;
        }
        
        if($current_add_on = $this->db->query("SELECT * FROM user_add_ons WHERE user_id = $u_id AND add_on_name = 'Pro+'")->row()){
            $this->data['user_current_add_on'] = 1;
        }
        else{
            $this->data['user_current_add_on'] = 0;
        }
        
        $this->data['user_id'] = $u_id;
        
        if($eth_fee = $this->DB2->query("SELECT fee FROM order_fee WHERE type = 'withdraw' AND currency_id = 2")->row()){$eth_withdraw_fee = $eth_fee->fee;}else{$eth_withdraw_fee = 0.0015;}
        if($arb_fee = $this->DB2->query("SELECT fee FROM order_fee WHERE type = 'withdraw' AND currency_id = 1")->row()){$arb_withdraw_fee = $arb_fee->fee;}else{$arb_withdraw_fee = 0.5;}
        $this->data['eth_withdraw_fee'] = $eth_withdraw_fee;
        $this->data['arb_withdraw_fee'] = $arb_withdraw_fee;

        $this->back('wallet');
    }

    // public function support(){
    //     if($this->session->userdata('u_id') == FALSE) {
    //         redirect('login');
    //     }
    //     $this->data['s_id'] = 0;
    //     $this->back('support');
    // }
    
    public function support_new(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');
        $this->data['code'] = $this->md_users->code_affiliate($u_id);
        
        $check_pin = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
        if($check_pin->support_pin == 0){
            $this->data['support_pin_status'] = 0;
        }else{
            $this->data['support_pin_status'] = 1;
        }
        
        $this->back('support_new');
    }
    
    public function support_beta_a(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');
        $this->data['code'] = $this->md_users->code_affiliate($u_id);
        
        $check_pin = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
        if($check_pin->support_pin == 0){
            $this->data['support_pin_status'] = 0;
        }else{
            $this->data['support_pin_status'] = 1;
        }
        
        $this->data['tab_data'] = $this->db->query('SELECT * FROM tab_status')->result();
        
        $this->back('support');

    }
    
    
    public function vault(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $this->back('vault_beta');
    }
    
    // public function vault_beta(){
    //     if($this->session->userdata('u_id') == FALSE) {
    //         redirect('login');
    //     }
    //     $this->back('vault_beta');
    // }
    // public function vault_mobile_beta(){
    //     if($this->session->userdata('u_id') == FALSE) {
    //         redirect('login');
    //     }
    //     $this->back('vault_mobile_beta');
    // }
    
    public function vault_mobile(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $this->back('vault_mobile_beta');
    }

    // public function user_tx(){
    //     if($this->session->userdata('u_id') == FALSE) {
    //         redirect('login');
    //     }
    //     $u_id = $this->session->userdata('u_id');
    //     $sql = "SELECT * FROM wallet_logs WHERE user_id = $u_id AND comment != 'Fee' ORDER BY created_at DESC";
    //     $data = $this->db->query($sql)->result();
    //     $this->data['code'] = $this->md_users->code_affiliate($u_id);

    //     $this->data['user_tx'] = $data;
    //     $this->back('user_tx');

    // }
    
    
    public function history(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');
        // $sql = "SELECT * FROM wallet_logs WHERE user_id = $u_id AND comment != 'Fee' ORDER BY created_at DESC";
        // $data = $this->db->query($sql)->result();
        // $this->data['code'] = $this->md_users->code_affiliate($u_id);

        // $this->data['user_tx'] = $data;
        $this->back('user_history');

    }
    
    public function announcement(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');
        $sql = "SELECT * FROM `announcement` ORDER BY `created_at` DESC LIMIT 20";
        $data = $this->db->query($sql)->result();

        $this->data['announcements'] = $data;
        $this->data['code'] = $this->md_users->code_affiliate($u_id);
        $this->back('announcement');

    }

    public function staking(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $this->back('staking');
    }

    // public function affiliate(){
    //     if($this->session->userdata('u_id') == FALSE) {
    //         redirect('login');
    //     }
    //     $u_id = $this->session->userdata('u_id');
    //     $this->data['affiliate'] = $this->md_users->list_affiliate($u_id);
    //     $this->data['all_user'] = $this->md_users->all_users();
    //     $this->data['code'] = $this->md_users->code_affiliate($u_id);
        
    //     $user_aff_earn = $this->db->query("SELECT aff_earned FROM abot_wallet WHERE user_id = ?", array($u_id))->row()->aff_earned;
    //     if($user_aff_earn == ''){
    //         $user_aff_earn = 0;
    //     }
    //     $this->data['aff_earn'] = $user_aff_earn;
    //     $this->back('affiliate');
    // }
    
    
    public function affiliate(){
        if($this->session->userdata('original_session') != 0) {
            redirect('admin');
        }
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');
        $user_affiliates = $this->md_users->list_affiliate($u_id);
        $affiliate_array = array();
        foreach($user_affiliates as $child){
            $email = $this->db->query("SELECT u_email FROM users WHERE u_id = ?", array($child['child_u_id']))->row()->u_email;
            if($email != ''){
                $invest_status = 0;
                if($abot_active = $this->db->query("SELECT active FROM abot_wallet WHERE user_id = ?", array($child['child_u_id']))->row()->active){
                    if($abot_active >= 250){$invest_status = 1;}else{$invest_status = 0;}
                }
                $affiliate_array[$child['child_u_id']] = array('email'=>$email, 'investment'=>$invest_status);
            }
        }
        
        $this->data['affiliate'] = $affiliate_array;
        //$this->data['all_user'] = array();
        $this->data['code'] = $this->md_users->code_affiliate($u_id);
         $user_aff_earn = $this->db->query("SELECT aff_earned FROM abot_wallet WHERE user_id = ?", array($u_id))->row()->aff_earned;
         if($user_aff_earn == ''){
             $user_aff_earn = 0;
         }
         $this->data['aff_earn'] = $user_aff_earn;
         $this->back('affiliate');
    }
    
    
     public function mbot(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');
        if($mbot_user_data = $this->db->query("SELECT * FROM mbot_members WHERE user_id = ?", array($u_id))->row()){
            $this->data['gas'] = ($mbot_user_data->max_trade_limit * 100)/500000;
        }else{
            $this->data['gas'] = 0;
        }
        $count = $this->db->query("Select * from counter_table WHERE name = 'mbot_page_counter'")->row()->value;
        $newcount = $count + 1;
        $this->db->query("UPDATE counter_table SET value = ? WHERE name = 'mbot_page_counter'", array($newcount));
        $this->data['count'] = $newcount;
        
        $this->back('mbot_angular');

    }

    // public function account_beta_a(){
    //     if($this->session->userdata('original_session') != 0) {
    //         redirect('admin');
    //     }
        
    //     if($this->session->userdata('u_id') == FALSE) {
    //         redirect('login');
    //     }
    //     $u_id = $this->session->userdata('u_id');
    //     $sql = "SELECT auto_reinvest FROM abot_wallet WHERE user_id = $u_id ";
    //     $auto_reinvest = $this->db->query($sql)->row()->auto_reinvest;
        
    //     $this->data['auto_reinvest'] = $auto_reinvest;
        
    //     $sql = "SELECT * FROM exchange_wallet WHERE user_id = $u_id";
    //     $ex_data = $this->db->query($sql)->row();
    //     $this->data['auto_buy'] = $ex_data->auto_buy_status;
        
    //     $this->data['code'] = $this->md_users->code_affiliate($u_id);
        
        
    //     $res = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ?", array($u_id))->result();
    //     $main_arr = array();
    //     foreach($res as $key => $one){
    //         $dp_addresses = $this->db->query("SELECT * FROM mbot_ex_no_dp_address_api WHERE user_id = ? AND exchange = ?", array($u_id, $one->exchange))->result();
    //         $coin_arr = array();
    //         foreach($dp_addresses as $a){
    //             $coin_arr[$a->coin]['address'] = $a->deposit_address;
    //             $coin_arr[$a->coin]['tag'] = $a->coin_tag;
    //         }
    //         $main_arr[$one->exchange]['apikey'] = $one->api_key;
    //         $main_arr[$one->exchange]['seckey'] = $this->encrypt->decode($one->sec_key);
    //         $main_arr[$one->exchange]['coins'] = $coin_arr;
    //     }
    //     $this->data['mbot_data'] = $main_arr;
        
    //     // get checked exchanges in account->mbot tab
    //     $exchanges_check = $this->DB3->query("SELECT * FROM mbot_user_exchanges WHERE user_id = ?", array($u_id))->row();
    //     if($exchanges_check->exchanges == ''){
    //         $a = '{"Kraken":{"checked":"0"},"Bithumb":{"checked":"0"},"BtcMarket":{"checked":"0"},"Poloniex":{"checked":"0"},"Binance":{"checked":"0"},"Bittrex":{"checked":"0"},"HitBtc":{"checked":"0"},"Huobi":{"checked":"0"},"Livecoin":{"checked":"0"},"Exmo":{"checked":"0"}}';
    //         $this->data['ex_checkbox_data'] = json_decode($a);
    //     }else{
    //         $this->data['ex_checkbox_data'] = json_decode($exchanges_check->exchanges);
    //     }
        
    //     $check_pin = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
    //     if($check_pin->support_pin == '0'){
    //         $this->data['support_pin_status'] = 0;
    //     }else{
    //         $this->data['support_pin_status'] = 1;
    //     }
        
    //     //voting status 
    //     $user_detail = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
    //     $this->data['voting_status'] = $user_detail->allow_voting;
        
    //     //voting data
    //     $now = date('Y-m-d H:i:s');
    //     if($aaa = $this->db->query("SELECT * FROM voting_topics WHERE status = ? AND expire_date < ?", array(1, $now))->result()){
    //         foreach($aaa as $aaaa){
    //             $this->db->query("UPDATE voting_topics SET status = ? WHERE id = ?", array(0, $aaaa->id));
    //         }
    //     }
        
    //     $open_votings = $this->db->query("SELECT * FROM voting_topics WHERE status = ? AND expire_date > ? order by created_at DESC LIMIT 3", array(1, $now))->result();
    //     $open_array = array();
    //     foreach($open_votings as $open){
    //         $selected_opt = $this->db->query("SELECT * FROM voting_cast WHERE topic_id= ? AND user_id = ?", array($open->id, $u_id))->row();
    //         if(isset($selected_opt->selected_option)){
    //             $sel_op = $selected_opt->selected_option;
    //         }else{
    //             $sel_op = null;
    //         }
    //         $arr1 = array();
    //         $arr1['topic_id'] = $open->id;
    //         $arr1['subject'] = $open->subject;
    //         $arr1['discription'] = $open->body;
    //         $arr1['created'] = $open->created_at;
    //         $arr1['expire'] = $open->expire_date;
    //         $opt = json_decode($open->options);
    //         $arr2 = array();
    //         $total = 0;
    //         foreach($opt as $o){
    //             $total = $total + $o->count;
    //         }
    //         $_david30 =  ceil(($total / 100) * 30);
    //         if($_david30 < 30){ $_david30 = 30; }
            
    //         foreach($opt as $o){
    //             if($o->option == $sel_op){
    //                 if($o->davidchoice == 1){$addd = $_david30; }else{$addd = 0;}
    //                 if($sel_op == 'other'){
    //                     $arr2[] = array('option'=>$o->option, 'count'=>$o->count+$addd, 'checked'=>1, 'other_text' => $selected_opt->other_option, 'davidchoice'=>$o->davidchoice);
    //                 }else{
    //                     $arr2[] = array('option'=>$o->option, 'count'=>$o->count+$addd, 'checked'=>1, 'davidchoice'=>$o->davidchoice);
    //                 }
    //             }else{
    //                 if($o->davidchoice == 1){$addd = $_david30; }else{$addd = 0;}
    //                 $arr2[] = array('option'=>$o->option, 'count'=>$o->count+$addd, 'davidchoice'=>$o->davidchoice);
    //             }
    //         }
            
            
    //         $arr1['options'] = $arr2;
    //         $arr1['total'] = $total+$_david30;
            
    //         $open_array[] = $arr1;
    //     }
        
    //     $this->data['open_votings'] = $open_array;
        
        
    //     $close_votings = $this->db->query("SELECT * FROM voting_topics WHERE status = ? order by created_at DESC LIMIT 20", array(0))->result();
    //     $close_array = array();
    //     foreach($close_votings as $close){
    //         $selected_opt = $this->db->query("SELECT * FROM voting_cast WHERE topic_id= ? AND user_id = ?", array($close->id, $u_id))->row();
    //         if(isset($selected_opt->selected_option)){
    //             $sel_op = $selected_opt->selected_option;
    //         }else{
    //             $sel_op = null;
    //         }
    //         $arr1 = array();
    //         $arr1['topic_id'] = $close->id;
    //         $arr1['subject'] = $close->subject;
    //         $arr1['discription'] = $close->body;
    //         $arr1['created'] = $close->created_at;
    //         $arr1['expire'] = $close->expire_date;
    //         $opt = json_decode($close->options);
    //         $arr2 = array();
    //         $total = 0;
    //         foreach($opt as $o){
    //             $total = $total + $o->count;
    //         }
    //         $_david30 =  ceil(($total / 100) * 30);
    //         if($_david30 < 30){ $_david30 = 30; }
            
    //         foreach($opt as $o){
    //             if($o->option == $sel_op){
    //                 if($o->davidchoice == 1){$addd = $_david30; }else{$addd = 0;}
    //                 if($sel_op == 'other'){
    //                     $arr2[] = array('option'=>$o->option, 'count'=>$o->count+$addd, 'checked'=>1, 'other_text' => $selected_opt->other_option, 'davidchoice'=>$o->davidchoice);
    //                 }else{
    //                     $arr2[] = array('option'=>$o->option, 'count'=>$o->count+$addd, 'checked'=>1, 'davidchoice'=>$o->davidchoice);
    //                 }
    //             }else{
    //                 if($o->davidchoice == 1){$addd = $_david30; }else{$addd = 0;}
    //                 $arr2[] = array('option'=>$o->option, 'count'=>$o->count+$addd, 'davidchoice'=>$o->davidchoice);
    //             }
    //         }
            
            
    //         $arr1['options'] = $arr2;
    //         $arr1['total'] = $total+$_david30;
            
    //         $close_array[] = $arr1;
    //     }
        
    //     $this->data['close_votings'] = $close_array;
        
    //      // user package and all packages detail
    //      $user_detail = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
         
    //     $this->data['user_current_package'] = $user_detail->package;
    //     $this->data['packages'] = $this->db->query("SELECT * FROM packages")->result();
        
    //     $this->data['user_id'] = $u_id = $this->session->userdata('u_id');
        
        
    //     //-----------------Access-------------------//
        
    //     $request = $this->db->query("SELECT * FROM access_req WHERE main_user_id = ?", array($u_id))->row();
    //     if(isset($request)){
    //         $access_given_to = $this->db->query("SELECT u_email FROM users WHERE u_id = ?", array($request->user_id))->row()->u_email;
    //         if(isset($access_given_to)){
    //             $this->data['access_given_to'] = $access_given_to;
    //         }
    //         else{
    //             $this->data['access_given_to'] = 0;
    //         }
    //     }
    //     else{
    //         $this->data['access_given_to'] = 0;
    //     }
    
    //     $requests = $this->db->query("SELECT * FROM access_req WHERE user_id = ? AND status = ?", array($u_id, 0))->result();
    //     if(sizeof($requests) > 0){
    //         foreach($requests as $req){
    //             $access_given_by_users[] = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($req->main_user_id))->row();
    //         }
    //         $access_given_by_users = json_decode(json_encode($access_given_by_users), true);
    //         $this->data['access_given_by_users'] = json_encode($access_given_by_users);
    //     }
    //     else{
    //         $this->data['access_given_by_users'] = 0;
    //     }
        
    //     // pro+
        
    //     if($add_ons = $this->db->query("SELECT * FROM add_ons")->result()){
    //         $this->data['add_ons'] = $add_ons;
    //     }
    //     else{
    //         $this->data['add_ons'] = 0;
    //     }
        
    //     if($current_add_ons = $this->db->query("SELECT * FROM user_add_ons WHERE user_id = $u_id")->result()){
    //         $this->data['user_current_add_ons'] = $current_add_ons;
    //     }
    //     else{
    //         $this->data['user_current_add_ons'] = 0;
    //     }
        
        
    //     $this->data['pp_auto_abot_active'] = $this->db->query("SELECT * FROM pp_auto_abot_active WHERE u_id = $u_id")->row();
    //     $this->data['pp_auto_stop_abot'] = $this->db->query("SELECT * FROM pp_auto_stop_abot WHERE u_id = $u_id")->row();
        
    //     if($pro_plus_wallet_data = $this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = $u_id")->row()){
    //         $this->data['pp_auto_selling_or_buying'] = $pro_plus_wallet_data;
    //     }
        
    //     $votingNoti = $this->db->query("SELECT voting FROM users Where u_id = $u_id")->row()->voting;
    //     if($votingNoti == 0) { $votingNoti = ""; }
        
    //     $this->data['votingNoti'] = $votingNoti;
        
    //     $this->back('account_test');
    // }

    public function auction(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');
        if($this->db->query("SELECT * FROM auction_req WHERE user_id = ?", array($u_id))->row()){
            $this->data['status'] = 1;
        }else{
             $this->data['status'] = 0; 
        }
        $this->data['total_auctions'] = $this->db->query("SELECT count(*) as tot FROM auction_req WHERE status = 1")->row()->tot;
        
        $bids = $this->db->query("SELECT * FROM bids WHERE user_id = ?", array($u_id))->result();
        if($bids){
            foreach($bids as $bid){
                $total_worth = $this->db->query("SELECT total_worth FROM auction_req WHERE account_id = ?", array($bid->account_id))->row()->total_worth;
                $remaining_time = strtotime('+48 hour', strtotime($bid->created_at)) - strtotime('-5 hour', strtotime(date('Y-m-d H:i:s')));
                $remaining_time =  gmdate('H:i:s', $remaining_time);
                $all_bids[] = array('total_worth'=>$total_worth, 'account_id'=>$bid->account_id, 'bid_value'=>$bid->bid_amount, 'remaining_time'=>$remaining_time);
            }
            $this->data['all_bids'] = $all_bids; 
        }
        else{
            $this->data['all_bids'] = 0; 
        }
        $this->back('auction');
    }

    public function account(){
        if($this->session->userdata('original_session') != 0) {
            redirect('admin');
        }
        
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');
        $sql = "SELECT auto_reinvest FROM abot_wallet WHERE user_id = $u_id ";
        $auto_reinvest = $this->db->query($sql)->row()->auto_reinvest;
        
        $this->data['auto_reinvest'] = $auto_reinvest;
        
        $sql = "SELECT * FROM exchange_wallet WHERE user_id = $u_id";
        $ex_data = $this->db->query($sql)->row();
        $this->data['auto_buy'] = $ex_data->auto_buy_status;
        
        $this->data['code'] = $this->md_users->code_affiliate($u_id);
        
        
        $res = $this->db->query("SELECT * FROM mbot_cred WHERE user_id = ?", array($u_id))->result();
        $main_arr = array();
        foreach($res as $key => $one){
            $dp_addresses = $this->db->query("SELECT * FROM mbot_ex_no_dp_address_api WHERE user_id = ? AND exchange = ?", array($u_id, $one->exchange))->result();
            $coin_arr = array();
            foreach($dp_addresses as $a){
                $coin_arr[$a->coin]['address'] = $a->deposit_address;
                $coin_arr[$a->coin]['tag'] = $a->coin_tag;
            }
            $main_arr[$one->exchange]['apikey'] = $one->api_key;
            $main_arr[$one->exchange]['seckey'] = $this->encrypt->decode($one->sec_key);
            $main_arr[$one->exchange]['coins'] = $coin_arr;
        }
        $this->data['mbot_data'] = $main_arr;
        
        // get checked exchanges in account->mbot tab
        $exchanges_check = $this->DB3->query("SELECT * FROM mbot_user_exchanges WHERE user_id = ?", array($u_id))->row();
        if($exchanges_check->exchanges == ''){
            $a = '{"Kraken":{"checked":"0"},"Bithumb":{"checked":"0"},"BtcMarket":{"checked":"0"},"Poloniex":{"checked":"0"},"Binance":{"checked":"0"},"Bittrex":{"checked":"0"},"HitBtc":{"checked":"0"},"Huobi":{"checked":"0"},"Livecoin":{"checked":"0"},"Exmo":{"checked":"0"}}';
            $this->data['ex_checkbox_data'] = json_decode($a);
        }else{
            $this->data['ex_checkbox_data'] = json_decode($exchanges_check->exchanges);
        }
        
        $check_pin = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
        if($check_pin->support_pin == '0'){
            $this->data['support_pin_status'] = 0;
        }else{
            $this->data['support_pin_status'] = 1;
        }
        
        //voting status 
        $user_detail = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
        $this->data['voting_status'] = $user_detail->allow_voting;
        
        //voting data
        $now = date('Y-m-d H:i:s');
        if($aaa = $this->db->query("SELECT * FROM voting_topics WHERE status = ? AND expire_date < ?", array(1, $now))->result()){
            foreach($aaa as $aaaa){
                $this->db->query("UPDATE voting_topics SET status = ? WHERE id = ?", array(0, $aaaa->id));
            }
        }
        
        $open_votings = $this->db->query("SELECT * FROM voting_topics WHERE status = ? AND expire_date > ? order by created_at DESC LIMIT 3", array(1, $now))->result();
        $open_array = array();
        foreach($open_votings as $open){
            $selected_opt = $this->db->query("SELECT * FROM voting_cast WHERE topic_id= ? AND user_id = ?", array($open->id, $u_id))->row();
            if(isset($selected_opt->selected_option)){
                $sel_op = $selected_opt->selected_option;
            }else{
                $sel_op = null;
            }
            $arr1 = array();
            $arr1['topic_id'] = $open->id;
            $arr1['subject'] = $open->subject;
            $arr1['discription'] = $open->body;
            $arr1['created'] = $open->created_at;
            $arr1['expire'] = $open->expire_date;
            $opt = json_decode($open->options);
            $arr2 = array();
            $total = 0;
            foreach($opt as $o){
                $total = $total + $o->count;
            }
            $_david30 =  ceil(($total / 100) * 30);
            if($_david30 < 30){ $_david30 = 30; }
            
            foreach($opt as $o){
                if($o->option == $sel_op){
                    if($o->davidchoice == 1){$addd = $_david30; }else{$addd = 0;}
                    if($sel_op == 'other'){
                        $arr2[] = array('option'=>$o->option, 'count'=>$o->count+$addd, 'checked'=>1, 'other_text' => $selected_opt->other_option, 'davidchoice'=>$o->davidchoice);
                    }else{
                        $arr2[] = array('option'=>$o->option, 'count'=>$o->count+$addd, 'checked'=>1, 'davidchoice'=>$o->davidchoice);
                    }
                }else{
                    if($o->davidchoice == 1){$addd = $_david30; }else{$addd = 0;}
                    $arr2[] = array('option'=>$o->option, 'count'=>$o->count+$addd, 'davidchoice'=>$o->davidchoice);
                }
            }
            
            
            $arr1['options'] = $arr2;
            $arr1['total'] = $total+$_david30;
            
            $open_array[] = $arr1;
        }
        
        $this->data['open_votings'] = $open_array;
        
        
        $close_votings = $this->db->query("SELECT * FROM voting_topics WHERE status = ? order by created_at DESC LIMIT 20", array(0))->result();
        $close_array = array();
        foreach($close_votings as $close){
            $selected_opt = $this->db->query("SELECT * FROM voting_cast WHERE topic_id= ? AND user_id = ?", array($close->id, $u_id))->row();
            if(isset($selected_opt->selected_option)){
                $sel_op = $selected_opt->selected_option;
            }else{
                $sel_op = null;
            }
            $arr1 = array();
            $arr1['topic_id'] = $close->id;
            $arr1['subject'] = $close->subject;
            $arr1['discription'] = $close->body;
            $arr1['created'] = $close->created_at;
            $arr1['expire'] = $close->expire_date;
            $opt = json_decode($close->options);
            $arr2 = array();
            $total = 0;
            foreach($opt as $o){
                $total = $total + $o->count;
            }
            $_david30 =  ceil(($total / 100) * 30);
            if($_david30 < 30){ $_david30 = 30; }
            
            foreach($opt as $o){
                if($o->option == $sel_op){
                    if($o->davidchoice == 1){$addd = $_david30; }else{$addd = 0;}
                    if($sel_op == 'other'){
                        $arr2[] = array('option'=>$o->option, 'count'=>$o->count+$addd, 'checked'=>1, 'other_text' => $selected_opt->other_option, 'davidchoice'=>$o->davidchoice);
                    }else{
                        $arr2[] = array('option'=>$o->option, 'count'=>$o->count+$addd, 'checked'=>1, 'davidchoice'=>$o->davidchoice);
                    }
                }else{
                    if($o->davidchoice == 1){$addd = $_david30; }else{$addd = 0;}
                    $arr2[] = array('option'=>$o->option, 'count'=>$o->count+$addd, 'davidchoice'=>$o->davidchoice);
                }
            }
            
            
            $arr1['options'] = $arr2;
            $arr1['total'] = $total+$_david30;
            
            $close_array[] = $arr1;
        }
        
        $this->data['close_votings'] = $close_array;
        
         // user package and all packages detail
         $user_detail = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
         
        $this->data['user_current_package'] = $user_detail->package;
        $this->data['packages'] = $this->db->query("SELECT * FROM packages")->result();
        
        $this->data['user_id'] = $u_id = $this->session->userdata('u_id');
        
        
        //-----------------Access-------------------//
        
        $request = $this->db->query("SELECT * FROM access_req WHERE main_user_id = ?", array($u_id))->row();
        if(isset($request)){
            $access_given_to = $this->db->query("SELECT u_email FROM users WHERE u_id = ?", array($request->user_id))->row()->u_email;
            if(isset($access_given_to)){
                $this->data['access_given_to'] = $access_given_to;
            }
            else{
                $this->data['access_given_to'] = 0;
            }
        }
        else{
            $this->data['access_given_to'] = 0;
        }
    
        $requests = $this->db->query("SELECT * FROM access_req WHERE user_id = ? AND status = ?", array($u_id, 0))->result();
        if(sizeof($requests) > 0){
            foreach($requests as $req){
                $access_given_by_users[] = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($req->main_user_id))->row();
            }
            $access_given_by_users = json_decode(json_encode($access_given_by_users), true);
            $this->data['access_given_by_users'] = json_encode($access_given_by_users);
        }
        else{
            $this->data['access_given_by_users'] = 0;
        }
        
        // pro+
        
        if($add_ons = $this->db->query("SELECT * FROM add_ons")->result()){
            $this->data['add_ons'] = $add_ons;
        }
        else{
            $this->data['add_ons'] = 0;
        }
        
        if($current_add_ons = $this->db->query("SELECT * FROM user_add_ons WHERE user_id = $u_id")->result()){
            $this->data['user_current_add_ons'] = $current_add_ons;
        }
        else{
            $this->data['user_current_add_ons'] = 0;
        }
        
        
        $this->data['pp_auto_abot_active'] = $this->db->query("SELECT * FROM pp_auto_abot_active WHERE u_id = $u_id")->row();
        $this->data['pp_auto_stop_abot'] = $this->db->query("SELECT * FROM pp_auto_stop_abot WHERE u_id = $u_id")->row();
        
        if($pro_plus_wallet_data = $this->db->query("SELECT * FROM pro_plus_wallet WHERE user_id = $u_id")->row()){
            $this->data['pp_auto_selling_or_buying'] = $pro_plus_wallet_data;
        }
        
        $votingNoti = $this->db->query("SELECT voting FROM users Where u_id = $u_id")->row()->voting;
        if($votingNoti == 0) { $votingNoti = ""; }
        
        $this->data['votingNoti'] = $votingNoti;
        
        $this->back('account');
    }

    public function update_user_wallet(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $wallet = str_replace(' ', '', $this->input->post("wallet"));
        //$email = $this->input->post("email");
        $u_id = $this->session->userdata('u_id');
        
        
        // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //     echo 'false';
        //     exit;
        // }
        
        $user_wallet = $this->db->query("select u_wallet from users where u_id = ?", array($u_id))->row()->u_wallet;
        
        if($wallet != $user_wallet){
            $wallet_exist = $this->db->query("select count(*) as wallet_exist from users where u_wallet = ?", array($wallet))->row()->wallet_exist;
            if($wallet_exist > 0 ){
                echo "false";
                exit;
            }else{
                $this->data['code'] = $this->md_users->update_user_wallet($u_id, $wallet);
                echo "true";
            }
        }
        else{
            $this->data['code'] = $this->md_users->update_user_wallet($u_id, $wallet);
            echo "true";
        }

        //	$this->back('account');
    }

    public function call_support(){
        if($this->session->userdata('u_id') == FALSE) {
            redirect('login');
        }
        $u_id = $this->session->userdata('u_id');
        $u_email = $this->input->post("email");
        $subject = $this->input->post("subject");
        $msg = $this->input->post("msg");

        if(!empty($u_email)){

            //  $config['upload_path']   = '/home/arbitraging/public_html/platform/upload';
            //  $config['allowed_types'] = 'gif|jpg|png';

            //  $this->load->library('upload', $config);

            //  if ( !$this->upload->do_upload('userfile')) {

            //     $this->data['s_id'] = $this->upload->display_errors();
            //     $this->back('support');

            //  }else{
            //  $upload_data = $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
            $file_name = $upload_data['file_name'];
            $support_id =  "#".(mt_rand(20,5000000));
            $this->send_mail_to_admin($u_id, $u_email, $subject, $msg, $support_id);
            $this->send_mail_to_user($u_id, $u_email, $subject, $msg, $support_id);
            $this->data['s_id'] = $support_id;
           
            $mew = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row()->u_wallet;
            $this->db->query("INSERT INTO support_queries_old (support_id, user_id, mew, email, subject, msg) VALUES (?,?,?,?,?,?)", array($support_id, $u_id, $mew, $u_email, $subject, $msg));
            
            $this->back('support');
            //  }
        }

    }

    public function userCount(){
        $count = $this->db->query("SELECT COUNT(*) as count FROM users")->row()->count;
        print_r($count);
    }





    public function send_mail_to_admin($u_id, $u_email, $subject, $msg, $support_id) {
        $subject = 'Support Request';
        $message = '<html>
<head>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
</style>
</head>
<body>

<h2>Withdrawal Details: </h2>

<table>
  <tr>    
    <th>Support ID</th>
    <th>User ID</th>
    <th>Email</th>
    <th>Subject</th>
    <th>Message</th>
  </tr>
  <tr>
    <td>' . $support_id .'</td>
    <td>' . $u_id .'</td>
    <td>' . $u_email .'</td>
    <td>' . $subject .'</td>
    <td>' . $msg .'</td>
  </tr>
</table>

</body>
</html>

	  ';
        $config['charset'] = 'UTF-8';
        $this->email->from('no-reply@arbitraging.co', 'Support Request');
	    $this->email->to('arbwithdrawal@gmail.com');
        // $this->email->to('majazelahi@gmail.com');
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->set_mailtype('html');
        if ($this->email->send()) {
            return true;

        } else {
            return false;
        }
    }

    public function send_mail_to_user($u_email, $subject, $support_id) {
        $subject = 'Support Request Token';
        $message = '<html>
<head>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
</style>
</head>
<body>

<h2>Withdrawal Details: </h2>

<table>
  <tr>  
    <th>Subject</th>
    <th>Support Token</th>
  </tr>
  <tr>
    <td>' . $subject .'</td>
    <td>' . $support_id .'</td>
  </tr>
</table>

</body>
</html>

	  ';
        $config['charset'] = 'UTF-8';
        $this->email->from('no-reply@arbitraging.co', 'Support Request Token');
// 	  $this->email->to('arbwithdrawal@gmail.com');
        $this->email->to($u_email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->set_mailtype('html');
        if ($this->email->send()) {
            return true;

        } else {
            return false;
        }
    }
    
    

}
