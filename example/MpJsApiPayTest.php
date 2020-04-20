<?php


require_once "vendor/autoload.php";

use VlinkedWechatPay\payload\WxPayUnifiedOrder;
use VlinkedWechatPay\WxJsApiPay;
use VlinkedWechatPay\WxPayApi;
use VlinkedWechatPay\WxPayException;

$myConfig = new VLinkedPayAccount();


try {
    // 构建下单对象
    $orderInfo = new WxPayUnifiedOrder();
    // 统一下单获取预下单信息
    $unifiedInfo = WxPayApi::unifiedOrder($myConfig, $orderInfo);
    // 构建js支付需要的参数
    $wxJsApiPay = new WxJsApiPay($myConfig);
    // 得到js支付的参数
    $jsPayJsonConfigStr = $wxJsApiPay->GetJsApiParameters($unifiedInfo['appid'], $unifiedInfo['prepa_id']);
    echo $jsPayJsonConfigStr;
} catch (WxPayException $e) {
}







