<?php


namespace VlinkedWechatPay\payload;


use VlinkedWechatPay\base\WxPayConfigInterface;

class WxPayResults extends WxPayDataBase
{
    /**
     * 生成签名 - 重写该方法
     * @param WxPayConfigInterface $config 配置对象
     * @param bool $needSignType 是否需要补signtype
     * @return string 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function MakeSign($config, $needSignType = false)
    {
        //签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->ToUrlParams();
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $config->GetKey();
        //签名步骤三：MD5加密或者HMAC-SHA256
        if (strlen($this->GetSign()) <= 32) {
            //如果签名小于等于32个,则使用md5验证
            $string = md5($string);
        } else {
            //是用sha256校验
            $string = hash_hmac("sha256", $string, $config->GetKey());
        }
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
}