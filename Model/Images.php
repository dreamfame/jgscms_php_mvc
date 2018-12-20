<?php
	Class Images{
		private $id;
		private $scenic_id;
		private $name;
		private $src;
		
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
