<?php
	Class Postcard{
		private $id;
		private $name;
		private $wx;
		private $pic;
        private $date;
        private $wishes;
		
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
