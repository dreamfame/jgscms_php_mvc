<?php
	Class User{//user info
		private $userId;//for login
		private $userName;//nickname
		private $password;
		private $name;
		private $birth;
		private $sex;
		private $phone;
		private $headImg;
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
