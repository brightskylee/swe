<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


// -----------------------------------------------------------------------------
// LDAP Auth
// -----------------------------------------------------------------------------

// TODO: Refactor this into a model which includes the LDAP code.
// TODO: Handle errors better and log all sign in attempts.


class Authentication extends CI_Controller {
    function __construct() {
        parent::__construct();
        
        $this->load->helper('form');
        $this->load->library('Form_validation');
        $this->load->helper('url');
        $this->load->library('table');
        $this->load->library('session');
    }

    function index() {
    session_start();
    session_destroy();
        $this->session->keep_flashdata('tried_to');
        
        $this->load->view('login_view');
    }

    function do_login(){
    
        session_start();
        
        if($_POST['usertype'] == 'student'){
            $this->load->model('user');
        
        
            $usr = new User;
     
            $usr->set_username($_POST['username']);
            $usr->set_credential($_POST['password']);
        
            $usr->start();
        }
        else{
            echo "Instructor or TA web service coming soon...";
        }
    }
    
    function submitAssist(){
        $this->load->view('file_submission');
    }
}

?>