<?php
	Class Route{
		private $id;
		private $scenic_id;
		private $name;
		private $type;
		private $route;
        private $time;
        private $created_at;
		
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
