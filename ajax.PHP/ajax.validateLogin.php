<?php
    session_start();
	$username = $_REQUEST['username'];
	$password = $_REQUEST['password'];
	$sql = "select userId,password from user where phone = '$username'";
	$conn = mysqli_connect("192.168.2.113", "root", "111111", "dumbbell");
	mysqli_query($conn, "set Names UTF8");
	$result = mysqli_query($conn,$sql);
	mysqli_close($conn);
	$r = false;
	$u = mysqli_fetch_row($result);
	if($u[0]!=""&&$u[1]==$password){
	    $r = true;
	    $_SESSION["sid"] = $u[0]; 
	}
	echo $r;
?>