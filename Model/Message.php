<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class Message{
		private $id;
		private $uid;
		private $aid;
		private $msg;
		private $reply;
		private $msg_time;
		private $reply_time;
		private $status;
		
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
