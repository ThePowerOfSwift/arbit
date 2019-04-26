<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    //set the class variable.
    public $template = array();
    public $data = array();
    public $DB2 = null;
    public $DB3 = null;

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load1();
    }

	public function back($view) {
        $this->template['content'] = $this->load->view('pages/backend/' . $view, $this->data, true);
        $this->load->view('pages/backend/template', $this->template);
	}
	
	
	public function load1() {
                 $CI =& get_instance();

                 $CI->DB2 = $CI->load->database('exchange', TRUE);
                 $CI->DB3 = $CI->load->database('mBOT', TRUE);
                 $CI->DB4 = $CI->load->database('backup', TRUE);
         }

}
