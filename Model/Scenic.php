<?php
	Class Scenic{
		private $id;
		private $area_id;
		private $name;
		private $intro;
		private $brief;
        private $recommend;
        private $isshow;
        private $top;
        private $see;
        private $created_at;
        private $updated_at;
		
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
