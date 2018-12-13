<?php
	Class Integral{
		private $id;
		private $userid;
		private $integral;
		
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
