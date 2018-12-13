<?php
	Class Video{
		private $id;
		private $name;
		private $pngsrc;
		private $gifsrc;
		private $type;
		private $intro;

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
