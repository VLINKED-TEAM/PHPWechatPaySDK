<?php


namespace VlinkedWechatPay\exception;


use Exception;

/**
 * 微信支付异常
 * Class WxPayException
 * @package VlinkedWechatPay
 */
class WxPayException extends Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}