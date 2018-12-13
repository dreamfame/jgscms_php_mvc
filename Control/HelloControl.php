<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/12
 * Time: 15:44
 */

class HelloControl{

	public function index(){
        $pwd = "liuliu";
        $hash = password_hash($pwd, PASSWORD_DEFAULT);
        echo $hash;
	}

	public function name($name){
	    $a = $_REQUEST['username'];
		echo "hello ".$a;
	}
}
