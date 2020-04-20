<?php


namespace VlinkedWechatPay;


use Exception;

class WxPayException extends Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}