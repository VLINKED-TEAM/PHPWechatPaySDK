<?php


namespace VlinkedUtils;


/**
 * 容器判断 判断当前运行的web 所在的容器环境
 * @method static isWeChat
 * @method static isREL
 * @method static isREG
 * @method static isZhenuanSchool
 * @method static isAlipay
 * Class Container
 * @package VlinkedUtils
 */
class Container
{
    const WeChat = "WeChat";
    const REL = "Rel";
    const REG = "Reg";
    const ZhenyuanSchool = "ZhenyuanSchool";
    const Alipay = "Alipay";

    public static $rules =
        [
            self::WeChat => ["MicroMessenger", "Windows Phone"],
            self::REL => ["ICBCiPhoneBSNew", "ICBCAndroidBS"],
            self::REG => ["newEmallVersion"],
            self::ZhenyuanSchool => ["yunmaapp.NET"],
            self::Alipay=>['Alipay'],
        ];

    /**
     * 调用静态方法时
     * @param string $name 方法名
     * @param string $arguments 参数
     * @return bool|mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $methods = get_class_methods(__CLASS__);
        $vars = array_keys(self::$rules);
        if (!in_array($name, $methods) && Strings::startsWith($name, "is")) {
            $ruleKey = Strings::substring($name, 2);
            if (in_array($ruleKey, $vars)) {
                return self::_is(self::$rules[$ruleKey]);
            } else {
                throw new StaticFunctionNotFind("name: $name StaticFunctionNotFind in" . __CLASS__);
            }
        } else {
            throw new StaticFunctionNotFind("name: $name StaticFunctionNotFind in" . __CLASS__);
        }

    }

    /**
     * 处理静态调用的魔法函数
     * @param $rule
     * @return bool|mixed
     */
    public static function _is($rule)
    {
        $userAgent = Servers::getUsersAgent();
        foreach ($rule as $item) {
            if (Strings::contains($userAgent, $item)) {
                return $item;
            }
        }
        return false;
    }

    /**
     * 检测当前的容器环境 匹配已知的规则
     * @return bool|string 找到已知就返回字符串 没有就返回 false
     */
    public static function detection()
    {
        foreach (self::$rules as $name => $rule) {
            if ($is_rule = self::_is($rule)) {
                return $name;
            }
        }
        return false;
    }

    public static function isMobile()
    {
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        if (isset($_SERVER['HTTP_VIA'])) {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
}