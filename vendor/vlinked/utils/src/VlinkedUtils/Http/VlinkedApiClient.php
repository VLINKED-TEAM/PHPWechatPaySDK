<?php


namespace VlinkedUtils\Http;


use http\Exception\RuntimeException;
use VlinkedUtils\AssertionException;
use VlinkedUtils\Http\callback\OnResponse;
use VlinkedUtils\Json;
use VlinkedUtils\JsonException;

class VlinkedApiClient
{


    private $appid;

    private $appkey;


    private $debug = false;


    /**
     * RequestApiHandler constructor.
     * @param $appid string appid
     * @param $appkey string appkey
     */
    public function __construct($appid, $appkey, $debug = false)
    {
        $this->appid = $appid;
        $this->appkey = $appkey;
        $this->debug = $debug;
    }


    /**
     * 一个get请求
     * @param $apiPath
     * @param $param
     * @param bool $needSign
     * @return mixed
     * @throws \RuntimeException
     * @throws HttpCurlException
     */
    public function doGet($apiPath, $param, $needSign = true)
    {

        return $responseArr = $this->doRequest($apiPath, $param, $needSign, 'get');

    }

    /**
     * 一个post请求
     * @param $apiPath
     * @param $param
     * @param bool $needSign
     * @return mixed
     * @throws \RuntimeException
     * @throws HttpCurlException
     */
    public function doPost($apiPath, $param, $needSign = true)
    {

        return $responseArr = $this->doRequest($apiPath, $param, $needSign, 'post');

    }

    private function debugPrint($tag, $printData)
    {

        if ($this->debug) {
            print_r($tag . ": ");
            print_r($printData);
            echo PHP_EOL;
        }

    }


    /**
     * @param $apiPath string
     * @param $param array
     * @param bool $needSign
     * @param string $type
     * @return mixed
     * @throws HttpCurlException
     * @throws \RuntimeException
     */
    private function doRequest($apiPath, $param, $needSign = true, $type = 'get')
    {
        $t = time();
        $sign = "";
        if ($needSign) {
            $sign = $this->calcRequestSign($param, $t);
        }
        /**
         * 请求url以及三个参数
         */
        $finalPath = $apiPath . "?appid=" . $this->appid . "&sign=" . $sign . "&t=" . $t;
        $this->debugPrint("finalPath", $finalPath);
        $this->debugPrint("param", $param);
        /**
         * 发起请求
         */
        $response = "";

        if ($type === 'get') {
            $finalPath .= "&" . http_build_query($param);
            $response = Client::curlGet($finalPath, null);

        } else {
            $response = Client::curlPost($finalPath, $param);
        }
        $this->debugPrint("response", $response);
        if (is_null($response)) {
            throw new \RuntimeException("数据返回为空");
        }
        $arrResponse = json_decode($response, true);
        if ($error = json_last_error()) {
            throw new \RuntimeException("服务器返回json出错");
        }

        if (!$this->verifyResponseSign($arrResponse)) {
            throw new \RuntimeException("响应数据签名校验失败");
        }
        return $arrResponse;

    }

    /**
     * 响应结果校验
     * @param array $responseData
     * @return bool
     */
    public function verifyResponseSign(array $responseData)
    {
        $code = $responseData['code'];
        $time = $responseData['time'];
        $sign = $responseData['sign'];
        if (empty($sign)) {
            return true;
        }
        $calc = md5($code . $this->appid . $this->appkey . $time);
        return $sign == $calc;
    }

    /**
     * @param $paramsgit
     * @param $t string
     * @return string
     */
    private function calcRequestSign($params, $t)
    {
        ksort($params);
        $fullStr = "t=" . $t . "&";
        foreach ($params as $key => $val) {
            $fullStr .= $key . "=" . $val . "&";
        }
        $fullStr .= "secret_key=" . $this->appkey;
        $fullStr = strtolower($fullStr);
        return md5($fullStr);
    }


}