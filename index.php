<?php

include_once('core.php');


if( !isLogin() ){
    header("HTTP/1.1 401 Unauthorized");
    header('location: ../login.php?rederict=new/');
    echo "ERROR";
    exit;
}



$tpl = file_get_contents("index.html");
$tpl = str_replace("{{user}}",$user['name'],$tpl);
echo $tpl;
