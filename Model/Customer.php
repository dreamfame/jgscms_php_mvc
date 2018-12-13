<?php
	Class Customer{
	    private $id;
		private $name;
		private $sex;
		private $phone;
		private $telephone;
		private $age;
		
		function __set($name,$value)
		{
			$this->$name = $value ;
		}

		function __get($name)
		{
			return $this->$name;
		}
	}
?>
