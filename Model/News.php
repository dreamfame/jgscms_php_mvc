<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class News{
		private $id;
		private $title;
		private $type;
		private $content;
        private $show;
        private $top;
        private $see;
		private $operator;
        private $created_at;
        private $updated_at;
        private $keyword;
        private $abstract;
        private $pic;
		
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
