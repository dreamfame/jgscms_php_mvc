<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
	Class IPControl
	{
		static public $ip = "";
		
		static public function getClientIp()
		{
			if (getenv("HTTP_CLIENT_IP"))
				self::$ip = getenv("HTTP_CLIENT_IP");
			else if(getenv("HTTP_X_FORWARDED_FOR"))
				self::$ip = getenv("HTTP_X_FORWARDED_FOR");
			else if(getenv("REMOTE_ADDR"))
				self::$ip = getenv("REMOTE_ADDR");
			else self::$ip = "Unknow";
			return self::$ip;
		}
		
		static public function getServerIp()
		{
			return gethostbyname($_SERVER["SERVER_NAME"]);
		} 
		
		static public function getLocationByIpSina($data = "")
		{
			if($data=="")
			{
				$data = self::getClientIp();
			}
			$res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $data);
			if(empty($res)){ return false; }
			$jsonMatches = array();
			preg_match('#\{.+?\}#', $res, $jsonMatches);
			if(!isset($jsonMatches[0])){ return false; }
			$json = json_decode($jsonMatches[0], true);
			if(isset($json['ret']) && $json['ret'] == 1){
				$json['ip'] = $data;
				unset($json['ret']);
			}else{
				return false;
			}
			return $json;
		}
		
		static public function getLocationByIpTaobao($data)
		{
			if($data=="")
			{
				$data = self::getClientIp();
			}
			$res = @file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$data);
			if(empty($res)){ return false; }
			return $res;
			/*
			$json = json_decode($res, true);
			return $json;*/
		}
		
		static public function getLocationByIpSohu($data)
		{
			/*if($data=="")
			{
				$data = $this->getClientIp();
			}*/
		}
	}
?>