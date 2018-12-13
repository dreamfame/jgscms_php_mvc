<?php
	Class Sports{
		private $id;
		private $userId;
		private $time;
		private $sbsz;
		private $lbyd;
		private $sjhw;
		private $zbny;
		private $kxyd;
		private $finishtime;

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
