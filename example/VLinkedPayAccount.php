<?php


use VlinkedWechatPay\base\WxPayConfigInterface;

class VLinkedPayAccount extends WxPayConfigInterface
{


    /**
     * @inheritDoc
     */
    public function GetAppId()
    {
        return "sasasasasa";
    }

    public function GetMerchantId()
    {
        // TODO: Implement GetMerchantId() method.
    }

    /**
     * @inheritDoc
     */
    public function GetNotifyUrl()
    {
        // TODO: Implement GetNotifyUrl() method.
    }

    public function GetSignType()
    {
        // TODO: Implement GetSignType() method.
    }

    /**
     * @inheritDoc
     */
    public function GetProxy(&$proxyHost, &$proxyPort)
    {
        // TODO: Implement GetProxy() method.
    }

    /**
     * @inheritDoc
     */
    public function GetReportLevenl()
    {
        // TODO: Implement GetReportLevenl() method.
    }

    public function GetKey()
    {
        // TODO: Implement GetKey() method.
    }

    public function GetAppSecret()
    {
        // TODO: Implement GetAppSecret() method.
    }

    /**
     * @inheritDoc
     */
    public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath)
    {
        // TODO: Implement GetSSLCertPath() method.
    }
}