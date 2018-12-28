<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 * Date: 2018/12/28
 * Time: 14:28
 */
if (!session_id()) session_start();
$_SESSION['openid'] = "1";
echo $_SESSION['openid'];