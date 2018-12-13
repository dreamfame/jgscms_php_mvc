<?php
	interface ILogin
	{
		function validateLogin($type);

		function successLogin($user,$type);

		function failLogin($result,$type);
		
		function recordToken($uesr);
		
		function judgeRemember($user);
	}
?>