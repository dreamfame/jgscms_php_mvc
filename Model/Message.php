<?php
	Class Message{
		private $id;
		private $customerid;
		private $specialid;
		private $message;
		private $reply;
		private $sendtime;
		private $replytime;
		
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
