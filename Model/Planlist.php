<?php
	Class Planlist{

		private $id;
        private $planlistid;
        private $videoid;
        private $times;

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
