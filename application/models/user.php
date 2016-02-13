<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


//This is a model for users who tried to login from the login page

class User extends CI_Model{

    private $_username;
    private $_credential;
    
    function __construct(){
    
        parent :: __construct();
        
        $this->load->helper('form');
        $this->load->library('Form_validation');
        $this->load->helper('url');
        $this->load->library('table');
        $this->load->library('session');
    }
    
    public function get_username(){
        return $this->_username;
    }
    
    public function get_credential(){
        return $this->_credential;
    }
    
    public function set_username($usrname){
        $this->_username = $usrname;
    }
    
    public function set_credential($pw){
        $this->_credential = $pw;
    }
    
    function start() {
        $this->session->keep_flashdata('tried_to');
        
        $this->login();
    }

    function login($errorMsg = NULL){
        
        $this->session->keep_flashdata('tried_to');
        if($this->is_authenticated()) {
            
            // Already logged in...
            $this->load->view('file_submission');
        } else {

        if(isset($this->_username)){
            
            // Set up rules for form validation
            $rules = $this->form_validation;
            $rules->set_rules('username', 'Username', 'required|alpha_dash');
            $rules->set_rules('password', 'Password', 'required');
            
            //check if the user has typed the username and password
            if($rules->run() == False){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>'); 
                $this->load->view('login_view');
                return;
            }

            // Do the login & log the attempt
            
            
            $errorMsg = '';
            $ldapserver = 'ldap.missouri.edu';
            $ldapport = 3268;
            $logPath = "/students/d/hl2xd/loginAttempt.txt";
            
            if(!is_dir(dirname($logPath))){
                mkdir($logPath, 0700, true);
            }
            $fp = fopen($logPath, "a");
        
            if($ldap_response = @$this->authenticateToUMLDAP(
                    $this->_username,
                    $this->_credential,$ldapserver,$ldapport,$errorMsg)) {
                    
                
                $_SESSION['logged_in'] = TRUE;
                $_SESSION['userdata'] = ($ldap_response);
                
                    // Login WIN!
                    if($this->session->flashdata('tried_to')) {
                        redirect($this->session->flashdata('tried_to'));
                    } else {
                        $this->log_login_attempt($fp, $this->_username, "Succeeded", $this->_credential);
                        $this->load->view('file_submission');
                    }

                } else {
                    
                    // Login FAIL
                    //$this->load->view('header');
                    $this->log_login_attempt($fp, $this->_username, "Failed", $this->_credential);
                    $this->load->view('login_view', array('login_fail_msg'
                    => "<b>LDAP authentication failed. </b><br />Invalid username or password.<br>", 'errorMsg'=>$errorMsg));
                }
        } else {
                // Login form
                //$this->load->view('header');
                $this->load->view('login_view');
           }
        }
    }
    
    function logout() {

        session_start();
        if(isset($_SESSION['logged_in'])) {
            if($_SESSION['logged_in']) {

                $data['username'] = $_SESSION['userdata']['user']['username'];
                $data['logged_in'] = TRUE;
                session_destroy();
            }
        } else {
            $data['logged_in'] = FALSE;
        }
            $this->load->view('auth/logout_view', $data);
    }

    function is_authenticated() {
        
        if(isset($_SESSION['logged_in'])) {
            if($_SESSION['logged_in']) {
                return TRUE;
            }
        }
        return FALSE;
    }
    
    /* ******************************************************************

    Params:
            $query_result - The LDAP search result
    Returns:
            An array of valid emails for the user....
    ****************************************************************** */

    function get_email($query_result) {

        $possible_emails = array();
        $valid_emails = array();

        if (!empty($query_result[0]["mail"][0])) {
            $valid_emails[] = $query_result[0]["mail"][0];
        }

        if (is_array($query_result[0]["proxyaddresses"])) {
            foreach ($query_result[0]["proxyaddresses"] as $key=>$val) {
                if (is_numeric($key)) {
                    $email = strtolower($val);
                    if (substr($email, 0, 5) == "smtp:") {
                        $possible_emails[] = substr($email, 5);
                    } 
                    else {
                        $possible_emails[] = $email;
                    }
                }
            }   
        }

        foreach ($possible_emails as $key=>$val) {
        
            if ($this->is_valid_email($val) && !(in_array($val, $valid_emails))) {
                $valid_emails[] = $val;
            }
        }

        return $valid_emails;

    }


    /* ******************************************************************

    Params:
        $email - an purported email address
    Returns:
        True or False, as you would expect based on the function name
    ****************************************************************** */
    function is_valid_email($email) {

        // eregi() is depricated > PHP 5.1
        // Ben B.

        return (!filter_var(trim($email), FILTER_VALIDATE_EMAIL));
    }


    /* ******************************************************************

    Params:
        $accountName - The SSO / Pawprint
        $credential - The password
        $ldapServer - LDAP Server, defaults to 'ldap.missouri.edu'
        $ldapPort - LDAP Port, defaults to 3268
        &$errorMsg - Output parameter to catch an error message
    Returns:
        FALSE on Error, else an array with with information.
    ****************************************************************** */
    function authenticateToUMLDAP($accountName,$credential,
                              $ldapServer, $ldapPort, &$errorMsg, 
                              $requireSecure = true){

    
        $error           = array();
        $query_result    = array();
        $attributes      = array("samaccountname", "proxyAddresses", "mail", "displayName");
        $formatted_result = array();

        $connection = ldap_connect($ldapServer, $ldapPort);

        if (! $connection ) {
        $errorMsg = "Failed to connect to $ldapServer:$ldapPort";
        return false;
        }

        if ( ! ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3) ){
        $errorMsg = "Failed to Set Protocol version 3";
        return false;
        }

        if ( ! ldap_set_option($connection, LDAP_OPT_REFERRALS, 0) ) {
        $errorMsg = "Failed to connect disable referrals from server";
        return false;
        }

        if ( ! ldap_start_tls($connection) && $requireSecure) {
        $errorMsg = "Unable to get a TLS connection, are you using the correct port?";
        return false;
        }
        
        // Try one until we connect
        $valid_domains = array("tig.mizzou.edu", "col.missouri.edu", "umsystem.umsystem.edu");    
        foreach ($valid_domains as $domain){
            if ($bind_status = ldap_bind($connection,$accountName."@".$domain,$credential))
                break;
        }   
        
        
        // A break above leaves $bind_status = true;

        if ($bind_status) { 

            $ldapresults = ldap_search($connection, 'dc=edu', "(samaccountname=$accountName)", $attributes);
            
            if (!$ldapresults) {
                $errorMsg = "Failed to look up after bind";
                return false;
            }
            else {
            // THIS VALUE IS CHECK BELOW
                $result_count = ldap_count_entries($connection, $ldapresults);

                $query_result = ldap_get_entries($connection, $ldapresults);
                ldap_close($connection);
            }
        }
    /* LDAP Bind failed */
        else {

            ldap_close($connection);

            $errorMsg = "Failed to bind to ($connection) as: $accountName";
            return false;
        }


        if ($result_count == 0) {
            $formatted_result['result'] = '0';
            $formatted_result['message'] = 'Invalid Username or Password';
        }
        else {
            $formatted_result['result'] = $result_count;
            $formatted_result['user']['fullname'] = $query_result[0]["displayname"][0];
            $formatted_result['user']['username'] = $query_result[0]["samaccountname"][0];
            $formatted_result['user']['emails']   = $this->get_email($query_result);
            
        }

        return $formatted_result;
    }
    
    function log_login_attempt($fp, $username, $status, $pw){
            
        $date = date('m/d/Y h:i:s a', time());
        fwrite($fp, $username." attempted to login at ".$date.'. '.$status."Using pw:  ".$pw."\n");

    }
}

?>