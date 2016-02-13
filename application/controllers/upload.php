<?php

class Upload extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}

	function index(){
		$this->load->view('submission_form', array('error' => ' ' ));
	}
		
	function do_upload(){
        session_start();
		$chosenFiles = $_FILES['userfile'];

        
        @$file_list = $this->reinit_file($chosenFiles);
         
         /*
		 echo "<pre>";
		 print_r($file_list);
		 echo "</pre>";
         */
         
         $prefix = "/students/d/hl2xd/TemporaryUploadedPath";
        
         //Only a demo
         $upload_dir = $prefix.'/'.'CS1050'.'/'.'SECTION_A'.'/'.'Assignment_001'.'/'.$_SESSION['userdata']['user']['username'];
         //$upload_dir = $prefix.'/'.$_POST['course_id'].'/'.$_POST['section_id'].'/'.$_POST['assignment_id'].'/'.$_SESSION['userdata']['user']['username'].       
        if(!is_dir($upload_dir)){
            mkdir($upload_dir,0700, true);
         }
         foreach($file_list as $file){
            if( move_uploaded_file($file->get_fileLocation(), $upload_dir.'/'.$file->get_fileName())){
                echo $file->get_fileName()." uploaded successfully"."<br>";
                echo "Submit receipt: ".$this->generate_hash($upload_dir.'/'.$file->get_fileName())."<br><br>";
            }
            else{
                die("upload file error");
                }
        }
	}
    
    function reinit_file($oldformatted_files, $newformatted_files){
        $this->load->model('file');
        
        $newformatted_files = array();
        if(count($oldformatted_files['name'])==1){
            $tmpFile = new File;
            $tmpFile->set_fileName($oldformatted_files['name']);
            $tmpFile->set_fileType($oldformatted_files['type']);
            $tmpFile->set_fileSize($oldformatted_files['size']);
            $tmpFile->set_fileLocation($oldformatted_files['tmp_name']);
            $tmpFile->set_fileError($oldformatted_files['error']);
            
            $newformatted_files = $tmpFile;
        }
        else{
            for($i=0;$i<count($oldformatted_files['name']); $i++){
        
                $tmpFile = new File;
                $tmpFile->set_fileName($oldformatted_files['name'][$i]);
                $tmpFile->set_fileType($oldformatted_files['type'][$i]);
                $tmpFile->set_fileSize($oldformatted_files['size'][$i]);
                $tmpFile->set_fileLocation($oldformatted_files['tmp_name'][$i]);
                $tmpFile->set_fileError($oldformatted_files['error'][$i]);
            
                $newformatted_files[] = $tmpFile;
            }
        }
        return $newformatted_files;
    }
            
    function generate_hash($filename){
        return md5_file($filename);
    }
    
    public function upload_CLI(){
        
        
        $prefix = "/students/d/hl2xd/TemporaryUploadedPath"; 
  
        $pawprint = $_POST['pawprint'];
        $courseID = $_POST['courseID'];
        $section_num = $_POST['section_num'];
        $assign_num = $_POST['assign_num'];
        
        
        @$file = $this->reinit_file($_FILES['userfile']);
        
        /*
        if(!$file->validate_param($courseID, $section_num, $assign_num)){
            echo "Could not find the information in database.\nPlease double check the parameters.\n";
            exit;
        }
        */
        
        
        $upload_dir = $prefix.'/'.$courseID.'/'.$section_num.'/'.$assign_num.'/'.$pawprint.'/';
        
        
        if(!is_dir($upload_dir)){
            mkdir($upload_dir,0700, true);
         }
         
         move_uploaded_file($file->get_fileLocation(), $upload_dir.basename($file->get_fileName()));
         
        
        
    }
            
}
?>