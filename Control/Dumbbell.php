<?php
    header('Access-Control-Allow-Origin：*');
	header("Content-Type: text/html;charset=utf-8");
	header("Access-Control-Allow-Methods","POST,OPTIONS,GET");
    $action = $_REQUEST["action"];
    include ucwords($action)."Control.php";