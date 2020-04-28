<?php


namespace VlinkedWechatPay\serivce;


use VlinkedWechatPay\base\WxPayConfigInterface;
use VlinkedWechatPay\base\WxPayOrderQuery;
use VlinkedWechatPay\base\WxPayRefund;
use VlinkedWechatPay\base\WxPayUnifiedOrder;
use VlinkedWechatPay\base\WxPayRefundQuery;
use VlinkedWechatPay\exception\WxPayException;
use VlinkedWechatPay\base\WxPayJsApiPay;
use VlinkedWechatPay\payload\WxPayReport;
use VlinkedWechatPay\payload\WxPayResults;
use VlinkedWechatPay\utils\WxSdkUtils;

class WxPayService
{
    /**
     *
     * 网页授权接口微信服务器返回的数据，返回样例如下
     * {
     *  "access_token":"ACCESS_TOKEN",
     *  "expires_in":7200,
     *  "refresh_token":"REFRESH_TOKEN",
     *  "openid":"OPENID",
     *  "scope":"SCOPE",
     *  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
     * }
     * 其中access_token可用于获取共享收货地址
     * openid是微信支付jsapi支付接口必须的参数
     * @var array
     */
    public $data = null;
    /**
     * @var WxPayConfigInterface
     */
    private $wxPayConfig;


    /**
     * @var int 业务超时时间
     */
    private $timeout;

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }


    /**
     * WxPayServic constructor.
     * @param WxPayConfigInterface $wxPayConfig
     */
    public function __construct(WxPayConfigInterface $wxPayConfig, $timeout = 6)
    {
        $this->wxPayConfig = $wxPayConfig;
        $this->timeout = $timeout;
    }

    /**
     * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
     * AppId、mchid、spbill_create_ip、nonce_str不需要填入
     * @param  WxPayUnifiedOrder $inputObj
     * @return array|bool 成功时返回，其他抛异常
     * @throws WxPayException
     */
    public function unifiedOrder($inputObj)
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $config = $this->wxPayConfig;
        //检测必填参数
        if (!$inputObj->IsOut_trade_noSet()) {
            throw new WxPayException("缺少统一支付接口必填参数out_trade_no！");
        } else if (!$inputObj->IsBodySet()) {
            throw new WxPayException("缺少统一支付接口必填参数body！");
        } else if (!$inputObj->IsTotal_feeSet()) {
            throw new WxPayException("缺少统一支付接口必填参数total_fee！");
        } else if (!$inputObj->IsTrade_typeSet()) {
            throw new WxPayException("缺少统一支付接口必填参数trade_type！");
        }

        //关联参数
        if ($inputObj->GetTrade_type() == "JSAPI" && !$inputObj->IsOpenidSet()) {
            throw new WxPayException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
        }
        if ($inputObj->GetTrade_type() == "NATIVE" && !$inputObj->IsProduct_idSet()) {
            throw new WxPayException("统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
        }

        //异步通知url未设置，则使用配置文件中的url
        if (!$inputObj->IsNotify_urlSet() && $config->GetNotifyUrl() != "") {
            $inputObj->SetNotify_url($config->GetNotifyUrl());//异步通知url
        }

        $inputObj->SetAppid($config->GetAppId());//公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId());//商户号
        $inputObj->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip
        $inputObj->SetNonce_str(WxSdkUtils::getNonceStr());//随机字符串

        //签名
        $inputObj->SetSign($config);
        $xml = $inputObj->ToXml();

        $startTimeStamp = WxSdkUtils::getMillisecond();//请求开始时间
        $response = WxSdkUtils::postXmlCurl($config, $xml, $url, false, $this->timeout);
        $result = WxPayResults::Init($config, $response);
        $this->reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * @param $wxPayUnifiedOrder WxPayUnifiedOrder
     * @return array
     * @throws WxPayException
     */
    public function jsApiPayParameters($wxPayUnifiedOrder)
    {
        $UnifiedOrderResult = $this->unifiedOrder($wxPayUnifiedOrder);
        $config = $this->wxPayConfig;
        if(!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "")
        {
            throw new WxPayException("参数错误");
        }

        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(WxSdkUtils::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);

        $jsapi->SetPaySign($jsapi->MakeSign($config));
        return $jsapi->GetValues();
    }

    /**
     *
     * 获取地址js参数
     * @param  string access_token
     * @return string 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
     */
    public function GetEditAddressParameters($access_token)
    {
        $data = array();
        $data["appid"] = $this->wxPayConfig->GetAppId();
        $data["url"] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $time = time();
        $data["timestamp"] = "$time";
        $data["noncestr"] = WxSdkUtils::getNonceStr();
        $data["accesstoken"] = $access_token;
        ksort($data);
        $params = WxSdkUtils::ToUrlParams($data);
        $addrSign = sha1($params);

        $afterData = array(
            'url'=>$data['url'],
            "addrSign" => $addrSign,
            "signType" => "sha1",
            "scope" => "jsapi_address",
            "appId" => $this->wxPayConfig->GetAppId(),
            "timeStamp" => $data["timestamp"],
            "nonceStr" => $data["noncestr"]
        );
        return json_encode($afterData);
    }
    /**
     *
     * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayOrderQuery $inputObj
     * @return string WxPayResults 成功时返回，其他抛异常
     * @throws WxPayException
     */
    public function orderQuery($inputObj)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        $config = $this->wxPayConfig;
        //检测必填参数
        if (!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
            throw new WxPayException("订单查询接口中，out_trade_no、transaction_id至少填一个！");
        }
        $inputObj->SetAppid($config->GetAppId());//公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId());//商户号
        $inputObj->SetNonce_str(WxSdkUtils::getNonceStr());//随机字符串

        $inputObj->SetSign($config);//签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = WxSdkUtils::getMillisecond();//请求开始时间
        $response = WxSdkUtils::postXmlCurl($config, $xml, $url, false, $this->timeout);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }


    /**
     *
     * 申请退款，WxPayRefund中out_trade_no、transaction_id至少填一个且
     * out_refund_no、total_fee、refund_fee、op_user_id为必填参数
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayRefund $inputObj
     * @return string WxPayResults 成功时返回，其他抛异常
     * @throws WxPayException
     */
    public function refund($inputObj)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        $config = $this->wxPayConfig;
        //检测必填参数
        if (!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
            throw new WxPayException("退款申请接口中，out_trade_no、transaction_id至少填一个！");
        } else if (!$inputObj->IsOut_refund_noSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数out_refund_no！");
        } else if (!$inputObj->IsTotal_feeSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数total_fee！");
        } else if (!$inputObj->IsRefund_feeSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数refund_fee！");
        } else if (!$inputObj->IsOp_user_idSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数op_user_id！");
        }
        $inputObj->SetAppid($config->GetAppId());//公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId());//商户号
        $inputObj->SetNonce_str(WxSdkUtils::getNonceStr());//随机字符串

        $inputObj->SetSign($config);//签名
        $xml = $inputObj->ToXml();
        $startTimeStamp = WxSdkUtils::getMillisecond();//请求开始时间
        $response = WxSdkUtils::postXmlCurl($config, $xml, $url, true, $this->timeout);
        $result = WxPayResults::Init($config, $response);
        $this->reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 查询退款
     * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
     * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
     * WxPayRefundQuery中out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayRefundQuery $inputObj
     * @return string 成功时返回，其他抛异常
     * @throws WxPayException
     */
    public function refundQuery($inputObj)
    {
        $url = "https://api.mch.weixin.qq.com/pay/refundquery";
        $config = $this->wxPayConfig;
        //检测必填参数
        if (!$inputObj->IsOut_refund_noSet() &&
            !$inputObj->IsOut_trade_noSet() &&
            !$inputObj->IsTransaction_idSet() &&
            !$inputObj->IsRefund_idSet()) {
            throw new WxPayException("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！");
        }
        $inputObj->SetAppid($config->GetAppId());//公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId());//商户号
        $inputObj->SetNonce_str(WxSdkUtils::getNonceStr());//随机字符串

        $inputObj->SetSign($config);//签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = WxSdkUtils::getMillisecond();//请求开始时间
        $response = WxSdkUtils::postXmlCurl($config, $xml, $url, false, $this->timeOut);
        $result = WxPayResults::Init($config, $response);
        $this->reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }
    /**
     *
     * 上报数据， 上报的时候将屏蔽所有异常流程
     * @param WxPayConfigInterface $config 配置对象
     * @param string $url
     * @param int $startTimeStamp
     * @param array $data
     */
    private function reportCostTime($config, $url, $startTimeStamp, $data)
    {
        //如果不需要上报数据
        $reportLevenl = $config->GetReportLevenl();
        if ($reportLevenl == 0) {
            return;
        }
        //如果仅失败上报
        if ($reportLevenl == 1 &&
            array_key_exists("return_code", $data) &&
            $data["return_code"] == "SUCCESS" &&
            array_key_exists("result_code", $data) &&
            $data["result_code"] == "SUCCESS") {
            return;
        }

        //上报逻辑
        $endTimeStamp = WxSdkUtils::getMillisecond();
        $objInput = new WxPayReport();
        $objInput->SetInterface_url($url);
        $objInput->SetExecute_time_($endTimeStamp - $startTimeStamp);
        //返回状态码
        if (array_key_exists("return_code", $data)) {
            $objInput->SetReturn_code($data["return_code"]);
        }
        //返回信息
        if (array_key_exists("return_msg", $data)) {
            $objInput->SetReturn_msg($data["return_msg"]);
        }
        //业务结果
        if (array_key_exists("result_code", $data)) {
            $objInput->SetResult_code($data["result_code"]);
        }
        //错误代码
        if (array_key_exists("err_code", $data)) {
            $objInput->SetErr_code($data["err_code"]);
        }
        //错误代码描述
        if (array_key_exists("err_code_des", $data)) {
            $objInput->SetErr_code_des($data["err_code_des"]);
        }
        //商户订单号
        if (array_key_exists("out_trade_no", $data)) {
            $objInput->SetOut_trade_no($data["out_trade_no"]);
        }
        //设备号
        if (array_key_exists("device_info", $data)) {
            $objInput->SetDevice_info($data["device_info"]);
        }
        //机器IP
        if (!array_key_exists("user_ip", $objInput)) {
            $objInput->SetUser_ip($_SERVER['REMOTE_ADDR']);
        }
        try {
            $this->report($config, $objInput);
        } catch (WxPayException $e) {
            //不做任何处理
        }
    }

    /**
     *
     * 测速上报，该方法内部封装在report中，使用时请注意异常流程
     * WxPayReport中interface_url、return_code、result_code、user_ip、execute_time_必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config 配置对象
     * @param WxPayReport $inputObj
     * @param int $timeOut
     * @return bool|string
     * @throws WxPayException
     */
    public static function report($config, $inputObj, $timeOut = 1)
    {
        $url = "https://api.mch.weixin.qq.com/payitil/report";
        //检测必填参数
        if (!$inputObj->IsInterface_urlSet()) {
            throw new WxPayException("接口URL，缺少必填参数interface_url！");
        }
        if (!$inputObj->IsReturn_codeSet()) {
            throw new WxPayException("返回状态码，缺少必填参数return_code！");
        }
        if (!$inputObj->IsResult_codeSet()) {
            throw new WxPayException("业务结果，缺少必填参数result_code！");
        }
        if (!$inputObj->IsUser_ipSet()) {
            throw new WxPayException("访问接口IP，缺少必填参数user_ip！");
        }
        if (!$inputObj->IsExecute_time_Set()) {
         throw new WxPayException("接口耗时，缺少必填参数execute_time_！");
        }
        $inputObj->SetAppid($config->GetAppId());//公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId());//商户号
        $inputObj->SetUser_ip($_SERVER['REMOTE_ADDR']);//终端ip
        $inputObj->SetTime(date("YmdHis"));//商户上报时间
        $inputObj->SetNonce_str(WxSdkUtils::getNonceStr());//随机字符串

        $inputObj->SetSign($config);//签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = WxSdkUtils::getMillisecond();//请求开始时间
        @$response = WxSdkUtils::postXmlCurl($config, $xml, $url, false, $timeOut);
        return $response;
    }

}