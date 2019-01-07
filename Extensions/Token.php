<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	require_once 'Security.php';
	Class Token
	{
		public static  $salt= "lllxk";
		
		static public function createToken()
		{
			$args = func_get_args();
			if(func_num_args() == 1)
			{
				return self::createToken1($args[0]);
			}
			else if(func_num_args() == 2)
			{
				return self::createToken2($args[0], $args[1]);
			}
			else if(func_num_args() == 3)
			{
				return self::createToken3($args[0], $args[1], $args[2]);
			}
			else return "";
		}
		
		static private function createToken1($str1)
		{
			return Security::encrypt($str1).Security::encrypt(self::$salt);
		}
		
		static private function createToken2($str1,$str2)
		{
			$data = $str1."+".$str2;
			return Security::encrypt($data).Security::encrypt(self::$salt);
		}
		
		static private function createToken3($str1,$str2,$str3)
		{
			$data = $str1."+".$str2."+".$str3;
			return Security::encrypt($data).Security::encrypt(self::$salt);
		}
	}
?>