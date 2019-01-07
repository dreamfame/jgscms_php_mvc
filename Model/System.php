<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class System{
		private $webName;
		private $webTitle;
		private $version;
		private $defaultHeadPic;
		private $defaultPic;
        private $server;
        private $dataBase;
        private $powerby;
        private $description;
        private $record;
		
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
