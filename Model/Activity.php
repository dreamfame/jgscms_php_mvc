<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class Activity{
		private $id;
		private $name;
		private $date;
		private $join;
        private $intro;
        private $prize_way;
        private $prize;
        private $phone;
        private $pic;
        private $enable;
        private $num;
		
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
