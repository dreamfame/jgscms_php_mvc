<?php
	Class LoginStatus{
		private $id;
		private $username;
		private $is_login;
		private $client_ip;
        private $session_id;
		
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
