<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Backup extends MY_Controller {
    public function get_logs_backup(){
        header('Access-Control-Allow-Origin: *');
        $u_id = $this->session->userdata('u_id');
        if($u_id != ''){
           //before 1 dec 2018
           $qry = 'SELECT comment as type, value, created_at FROM wallet_logs_dec_2018 WHERE user_id = ? AND comment NOT LIKE "Fee" ORDER BY created_at DESC';
           $res = $this->DB4->query($qry, array($u_id))->result();
           
           //between 1 dec 2018 to 1 feb 2018
           $qry2 = 'SELECT comment as type, value, created_at FROM wallet_logs_feb_2019 WHERE user_id = ? AND comment NOT LIKE "Fee" ORDER BY created_at DESC';
           $res2 = $this->DB4->query($qry2, array($u_id))->result();
           
           $final = array_merge($res2, $res);
           echo json_encode($final);
        }else{
           echo json_encode(array('error'=>'1', 'msg'=>'User session Expired.')); exit;
        }
    }
    
    public function get_orders_backup(){
        header('Access-Control-Allow-Origin: *');
        $u_id = $this->session->userdata('u_id');
       if($u_id != ''){
           $table_name = 'orders_dec_2018';
           $qry = 'SELECT price, amount, order_type, created_at, status, remark FROM '.$table_name.' WHERE user_id = ?  ORDER BY created_at DESC';
           $res = $this->DB4->query($qry, array($u_id))->result();
           echo json_encode($res);
       }else{
           echo json_encode(array('error'=>'1', 'msg'=>'User session Expired.')); exit;
       }
    }
    
    public function get_orders_block_exchange(){
        header('Access-Control-Allow-Origin: *');
        $u_id = $this->session->userdata('u_id');
       if($u_id != ''){
           $qry = 'SELECT price, amount, order_type, created_at, status, remark FROM orders_beta WHERE user_id = ? and status = 1 ORDER BY created_at DESC';
           $res = $this->DB2->query($qry, array($u_id))->result();
           echo json_encode($res);
       }else{
           echo json_encode(array('error'=>'1', 'msg'=>'User session Expired.')); exit;
       }
    }
    
    
    public function get_current_logs(){
        header('Access-Control-Allow-Origin: *');
       $u_id = $this->session->userdata('u_id');
       if($u_id != ''){
           $qry = 'SELECT comment as type, value, created_at FROM wallet_logs WHERE user_id = ? AND comment NOT LIKE "Fee" AND comment != "Vault Share Distribution" ORDER BY created_at DESC';
           $res = $this->db->query($qry, array($u_id))->result();
           echo json_encode($res);
       }else{
           echo json_encode(array('error'=>'1', 'msg'=>'User session Expired.')); exit;
       }
    }
}