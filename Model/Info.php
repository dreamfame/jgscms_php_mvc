<?php
	Class Info{
		private $id;
		private $nickname;
		private $height;
		private $weight;
		private $location;
		private $vocation;
		private $company;
		private $headimg;
		private $createAt;
		
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
