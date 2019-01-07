<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class Route{
		private $id;
		private $area_id;
		private $name;
		private $type;
		private $route;
        private $time;
        private $created_at;
        private $pic;
		
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
