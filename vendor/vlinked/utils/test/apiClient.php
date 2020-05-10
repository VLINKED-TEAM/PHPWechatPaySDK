<?php


namespace VlinkedUtils;

use VlinkedUtils\Http\VlinkedApiClient;

require_once __DIR__ . "/../vendor/autoload.php";


$apiPath1 = Env::get("app.host") . "/public/service/token/getWxJsTicket";
$appid = Env::get("app.appid");
$appkey = Env::get("app.appkey");


$apiClient = new VlinkedApiClient($appid, $appkey, false);


//$param['openid'] = "sdaasdasdasd";
$param['platid'] = "1231231";

try {
    $respo = $apiClient->doGet($apiPath1, $param);
    print_r($respo);
} catch (\RuntimeException $e) {
} catch (Http\HttpCurlException $e) {
    print_r($e->getMessage());
}