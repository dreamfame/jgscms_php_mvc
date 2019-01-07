<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class Praise{
		private $id;
		private $openid;
		private $photo_id;
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
