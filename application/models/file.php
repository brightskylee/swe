<?php

/* Class of students' submitted files */
class File extends CI_Model{
	
	private $_name;
	private $_type;
	private $_size;
	private $_location;
	private $_error;
	
	function __construct(){
	
		parent :: __construct();
	}
	
	public function get_fileName(){
	
		return $this->_name;
	}
	
	public function get_fileType(){
	
		return $this->_type;
	}

	public function get_fileSize(){
	
		return $this->_size;
	}
	
	public function get_fileLocation(){
	
		return $this->_location;
	}
	
	public function get_fileError(){
	
		return $this->_error;
	}
	
	public function set_fileName($name){
	
		$this->_name = $name;
		
	}

	public function set_fileType($type){
	
		$this->_type = $type;
		
	}

	public function set_fileSize($size){
	
		$this->_size = $size;
		
	}
	
	public function set_fileLocation($location){
	
		$this->_location = $location;
		
	}

	public function set_fileError($error){
	
		$this->_error = $error;
		
	}
	
    /*Function takes a parameter as maximum of file size and returns 1 if file is within the size or 0 if not */
	public function checkSize($MAX_SIZE){
	
		return $this->_size > $MAX_SIZE ? 0 : 1;
		
	}
	
    /*Function which takes the command line dir as input, parse it and decide if the parameters
      (course_id, section_number, assignment_num..) are legal */
	public function validate_param($file_dir){
	
		$param_list = explode("/", $file_dir);
		
        $sql = "SELECT * FROM submis_collection.enrollment E
                INNER JOIN submit_collection.assignment A USING (course_ID)
                WHERE pawprint = ? AND course_ID  = ? Section_Number = ? AND Assignment_ID = ?;";
        
        /* The following statements only serve demo, 
           will be adjusted according to the structure of directory tree of server filesystem */
		$query = $this->db->query($sql, array($param_list[1],$param_list[2], $param_list[3], $param_list[4]));

        return $query->num_rows(); 		
	}
}	
	