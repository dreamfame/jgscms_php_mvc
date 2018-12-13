<?php
	Class QueryConditionManage
	{
		public static function IntTranslateToString($table,$obj)
		{
			$arr = explode(',', $obj);
			if($table=="user"){
				for($i=0;$i<count($arr);$i++){
					switch($arr[$i])
					{
						case "1":$str = "id";break;
						case "2":$str = "username";break;
						case "3":$str = "email";break;
						case "4":$str = "phone";break;
						case "5":$str = "name";break;
						case "6":$str = "age";break;
						case "7":$str = "birth";break;
						case "8":$str = "sex";break;
						case "9":$str = "qq";break;
					}
					if($i==count($arr)-1)
					{
						$strs.=$str;
					}
					else{
						$strs.= $str.",";
					}
				}
			}
			else if($table=="vip")
			{
				for($i=0;$i<count($arr);$i++){
					switch($arr[$i])
					{
						case "1":$str = "userId";break;
						case "2":$str = "name";break;
						case "3":$str = "email";break;
						case "4":$str = "phone";break;
						case "5":$str = "level";break;
						case "6":$str = "sex";break;
						case "7":$str = "place";break;
					}
					if($i==count($arr)-1)
					{
						$strs.=$str;
					}
					else{
						$strs.= $str.",";
					}
				}
			}
			return $strs;
		}
		
		public static function StringTranslateToInt($table,$obj)
		{
			
		}
	}
?>