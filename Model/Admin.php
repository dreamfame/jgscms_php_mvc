<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class Admin{
		private $id;
		private $username;
		private $password;
		private $nickname;
		private $head_pic;
        private $auth_key;
        private $password_reset_token;
        private $email;
        private $phone;
        private $age;
        private $status;
        private $role;
        private $created_at;
        private $updated_at;
		
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
