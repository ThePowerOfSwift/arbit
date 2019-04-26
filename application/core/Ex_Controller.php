<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ex_Controller extends CI_Controller {

    //set the class variable.
    public $template = array();
    public $data = array();

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
    }

	public function back($view) {
        $this->template['content'] = $this->load->view('pages/backend/' . $view, $this->data, true);
        $this->load->view('pages/backend/template', $this->template);
	}

}
