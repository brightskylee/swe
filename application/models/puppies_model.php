<?php
	class Puppies_Model extends CI_Model
	{
		private $_id;
		private $_name;
		private $_breed;
		private $_age;
		private $_color;
		private $_sex;

		function __construct()
		{
			parent::__construct();
		}

		public function getId()
		{
			return $this->_id;
		}

		public function setId($id)
		{
			$this->_id = $id;
		}

		public function setName($name)
		{
			$this->_name = $name;
		}

		public function getName()
		{
			return $this->_name;
		}

		public function setBreed($breed)
		{
			$this->_breed = $breed;
		}

		public function getBreed()
		{
			return $this->_breed;
		}		

		public function setAge($age)
		{
			$this->_age = $age;
		}

		public function getAge()
		{
			return $this->_age;
		}
	
		public function setColor($color)
		{
			$this->_color = $color;
		}

		public function getColor()
		{
			return $this->_color;
		}

		public function setSex($sex)
		{
			$this->_sex = $sex;
		}

		public function getSex()
		{
			return $this->_sex;
		}

		public function commit()
		{
			$data = array(
				'name' => $this->_name,
				'breed' => $this->_breed,
				'age' => $this->_age,
				'color' => $this->_color,
				'sex' => $this->_sex
			);

			if ($this->_id > 0) {
				if ($this->db->update('puppy', $data, array('id' => $this->_id))) {
					return true;
				}
			} else {		
				if ($this->db->insert('puppy', $data)) {
					$this->_id = $this->db->insert_id();
					return true;
				}
			}
			return false;

		}
	}
?>
