<?php
/**
 * Created by PhpStorm.
 * User: Liu Liu
 * Date: 2018/12/12
 * Time: 15:19
 */
    //定义application路径
    define('APPPATH', trim(trim(preg_replace('/(.*)\/{1}([^\/]*)/i', '$1', $_SERVER['DOCUMENT_ROOT']))));

    //获得请求地址
    $root = $_SERVER['SCRIPT_NAME'];
    $request = $_SERVER['REQUEST_URI'];

    $URI = array();

    //获得index.php 后面的地址
    $url = trim(str_replace($root, '', $request), '/');


    //如果为空，则是访问根地址
    if (empty($url)) {
        //默认控制器和默认方法
        $class = 'Hello';
        $func = 'index';
    } else {
        $URI = explode('/', $url);

        //如果function为空 则默认访问index
        if (count($URI) < 2) {
            $class = $URI[0];
            echo  $URI[0];
            $func = 'index';
        } else {
            $class = $URI[0];
            $func = $URI[1];
        }
    }

    //把class加载进来
    include(APPPATH . '/' . 'Control/' . $class . 'Control.php');

    //实例化->将控制器首字母大写
    $obj = ucfirst($class.'Control');

    call_user_func_array(
    //调用内部function
        array($obj, $func),
        //传递参数
        array_slice($URI, 2)
    );
    ?>
