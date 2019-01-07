<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class Recommend{

		private $id;
        private $planlistid;
        private $partment;
        private $professional;

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
