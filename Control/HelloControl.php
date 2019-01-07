<?php
/**
 * Created by PhpStorm.
 * User: liu liu
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
