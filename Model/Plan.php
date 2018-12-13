<?php
	Class Plan{
		private $id;
		private $userid;
		private $time;
		private $sbsz;
		private $lbyd;
		private $sjhw;
		private $zbny;
		private $kxyd;
		private $k1;
		private $k2;
		private $k3;
		private $k4;
		private $k5;
		private $rt1;
		private $rt2;
		private $rt3;
		private $rt4;
		private $rt5;
		
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
