<?php
	Class Admin{
		private $username;
		private $password;
		private $nickname;
		private $head_pic;
        private $auth_key;
        private $password_reset_token;
        private $sex;
        private $email;
        private $phone;
        private $birth;
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
