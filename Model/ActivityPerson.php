<?php
	Class ActivityPerson{
		private $id;
		private $activity_id;
		private $nickname;
		private $phone;
		private $time;
        private $prize;

		
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
