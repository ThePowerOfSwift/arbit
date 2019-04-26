<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Support extends MY_Controller {
    
    public function generate_ticket()
    {
        $u_id = $this->session->userdata('u_id');
        if($u_id != '')
        {
            $subject = htmlspecialchars($this->input->post('subject'));
            $message = htmlspecialchars($this->input->post('message'));
            $category = $this->input->post('category');
            
            // $support_pin = $this->input->post('support_pin');
            
            // $check_pin = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($u_id))->row();
            
            // if($check_pin->support_pin == null){
            //     echo "Your Support pin does not exist";
            //     exit;
            // }
            
            // else if($check_pin->support_pin == 0){
            //     echo "Your Support pin does not exist";
            //     exit;
            // }
            // else if($check_pin->support_pin != $support_pin){
            //     echo "Your Support pin does not match";
            //     exit;
            // }
            
            $sql = $this->db->query("INSERT INTO support_queries(user_id, message, subject, category, t_status, has_new_msg) VALUES(?,?,?,?,?,?)", array($u_id, $message, $subject, $category, 1, 1));
            $sql1 = $this->db->query("SELECT * FROM support_queries WHERE user_id = $u_id AND t_status = 1")->row();
            $t_id = $sql1->id;
            $sql2 =  $this->db->query("INSERT INTO support_responses(t_id,user_id,responder,response, new_msg) VALUES(?,?,?,?,?)",array($t_id, $u_id, 'user', $message, 1));
        }
        
    }
    
    public function support_response()
    {
        $u_id = $this->session->userdata('u_id');
        $response= htmlspecialchars($this->input->post('response'));
        $sql = $this->db->query("SELECT * FROM support_queries WHERE user_id = $u_id AND t_status = 1")->row();
        
        if($sql != null)
        {
            $t_id = $sql->id;
            $this->db->query("INSERT INTO support_responses(user_id, t_id, responder, response, new_msg) VALUES(?,?,?,?,?)", array($u_id, $t_id, 'user', $response,1));
            $this->db->query("UPDATE support_queries SET has_new_msg = 1 WHERE id = $t_id");
        }
        else
        {
            echo "Your Ticket is closed.. Generate New Ticket";
        }
    }
    
    public function query_data()
    {
        $u_id = $this->session->userdata('u_id');
        $sql = $this->db->query("SELECT * FROM support_queries WHERE user_id = $u_id AND t_status = 1")->row();
        if($sql != null)
        {
            echo "Record Exist";
            
        }
 
        else
        {
            echo "No Record Found";
        }
            
    }
    
    public function complete_chat()
    {
        $u_id =$this->session->userdata('u_id');
        $sql = $this->db->query("SELECT * FROM support_queries WHERE user_id = $u_id AND t_status = 1")->row();
        if($sql != null)
        {
            $subj = $sql->subject;
            $sql = $this->db->query("SELECT * FROM support_responses WHERE t_id = $sql->id")->result();
            
            foreach($sql as $one){
                $this->db->query("UPDATE support_responses SET new_msg = 0 WHERE t_id = $one->t_id AND responder = 'Admin'");
            }
            
            $data = [
                'sub'=>$subj,
                'data'=>$sql
                ];
            $data = json_encode($data);
            print_r($data);
        }
        else
        {
            echo "No Record Found";
        }
       
    }
    
    public function all_tickets()
    {
        $u_id = $this->session->userdata('u_id');
        $sql = $this->db->query("SELECT * FROM support_queries WHERE user_id = $u_id AND t_status = 0 ORDER BY created_at DESC LIMIT 5")->result();
        if($sql != null)
        {
            foreach ($sql as $one){
                $ticket = $this->db->query("SELECT * FROM support_responses WHERE t_id = $one->id")->result();
                $tickets_array[] = array("index" => $one->id, "query" => $one, "tickets" => $ticket);
            }
            $tickets_array = json_encode($tickets_array);
            print_r($tickets_array);
        }
        else
        {
            echo "No Record Found";
        }
       
    }

    public function msg_count(){
        $u_id = $this->session->userdata('u_id');
        $count = 0;
        $sql = $this->db->query("SELECT * FROM support_queries WHERE user_id = $u_id AND t_status = 1")->row();
        if($sql != null)
        {
          $result = $this->db->query("SELECT COUNT(*) as new_msgs FROM support_responses WHERE t_id = $sql->id AND new_msg = 1 AND responder = 'admin'")->row();
          $count = $result->new_msgs;
        }  
        echo $count;
    }
    
    public function support_cat()
    {
        $support_categories = $this->db->query("SELECT cat_name FROM support_cat")->result();
        $result = json_encode($support_categories,true);
        print_r($result);
       
    }
}

?>