<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class Photo{
		private $id;
		private $uid;
		private $des;
		private $praise;
        private $comment;
        private $img1;
        private $img2;
        private $img3;
        private $img4;
        private $img5;
        private $img6;
        private $img7;
        private $img8;
        private $img9;
        private $created_at;
        private $verify;
        private $operator;
        private $top;
        private $private;
		
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
