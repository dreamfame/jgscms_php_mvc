<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class Picture{
		private $id;
		private $title;
		private $content;
		private $sender;
		private $senddate;
		private $picsrc;
		
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
