<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class Sysmsg{
		private $id;
		private $openid;
		private $title;
		private $content;
		private $created_at;
		private $see;
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
