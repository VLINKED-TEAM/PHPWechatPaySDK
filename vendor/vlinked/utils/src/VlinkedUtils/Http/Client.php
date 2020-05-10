<?php


namespace VlinkedUtils\Http;


use VlinkedUtils\Strings;

class Client
{

    /**
     * 发起一个curl 的get 请求
     * @param string $url 接口地址 不带 ？
     * @param string $getParam 数据参数
     * @param int $timeout 请求超时时间
     * @return bool|string
     * @throws HttpCurlException
     */
    public static function curlGet($url = '', $getParam = '', $timeout = 10)
    {
        if (is_array($getParam)) {
            $getParam = http_build_query($getParam);
            $url .= "?" . $getParam;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 返回文件流而不是输出
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); //

        //https请求 不验证证书和host
        if (Strings::contains($url, "https://")) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new HttpCurlException($ch);
        }
        curl_close($ch);
        return $data;
    }

    /**
     * POST 请求不验证 SSL 针数
     * @param string $url 地址
     * @param string $postData 请求的数据
     * @param array $options 设置
     * @param int $timeout 设置cURL允许执行的最长秒数
     * @return bool|string
     * @throws HttpCurlException
     */
    public static function curlPost($url = '', $postData = '', $options = array(), $timeout = 20)
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); //
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Expect:"]); // 解决数据过大导致状态码100问题
        if (!empty($options)) {
            if (isset($options[CURLOPT_HTTPHEADER])) {
                $options[CURLOPT_HTTPHEADER] = array_merge(["Expect:"], $options[CURLOPT_HTTPHEADER]);
            }
            curl_setopt_array($ch, $options);
        }

        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new HttpCurlException($ch);
        }
        curl_close($ch);
        return $data;
    }

    /**
     * 使用双向证书POST请求HTTPS接口
     * @param string $url 接口地址
     * @param string|array $param 参数
     * @param string $cert_path 证书绝对路径
     * @param string $key_path 密钥绝对路径
     * @param int $second 超时时间
     * @return bool|string
     * @throws HttpCurlException
     */
    public static function curlPostWithCertAndKey($url, $param, $cert_path, $key_path, $second = 30)
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);
        //默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        //返回结果
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new HttpCurlException($ch);
        }
        curl_close($ch);
        return $data;
    }
    // 下载图片


}