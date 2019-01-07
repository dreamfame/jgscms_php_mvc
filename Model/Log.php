<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class Log{
		private $id;
		private $username;
		private $content;
		private $time;
		
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
