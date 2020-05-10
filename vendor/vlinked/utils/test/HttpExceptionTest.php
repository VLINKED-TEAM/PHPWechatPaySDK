<?php


namespace VlinkedUtils;


require_once __DIR__ . "/../vendor/autoload.php";


//try {
//    echo Http\Client::curlGet("https://cn.bing.com/search", ['q'=>"vlinked"], 1);
//} catch (Http\HttpCurlException $e) {
//    echo $e->error_msg . "\n"; // 错误信息
//    echo $e->error_code . "\n"; // 错误码
//}

var_export(Servers::getUsersAgent());
var_export(Servers::getClientIP());
var_export(Servers::getClientIP());
