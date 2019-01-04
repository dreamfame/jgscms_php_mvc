<?php
	Class User{//user info
		private $id;//for login
		private $openid;//nickname
		private $wx;
		private $nickname;
		private $avatar;
		private $city;
		private $country;
		private $gender;
		private $auth;
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
