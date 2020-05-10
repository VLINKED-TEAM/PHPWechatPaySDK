<?php


namespace VlinkedUtils;


/**
 * THINKPHP 的配置加载方法提取出来了
 * Class Env
 * @package VlinkedUtils
 */
class Env
{


    /**
     * @var Env
     */
    private static $instance;

    /**
     * @param  $filePath
     * @return Env
     */
    public static function getInstance($filePath)
    {
        if (is_null(self::$instance)) {
            self::$instance = new Env($filePath);
        }
        return self::$instance;
    }


    public function __construct($filePath = "./.env")
    {
        if (is_file($filePath)) {
            $env = parse_ini_file('.env', true);

            foreach ($env as $key => $val) {
                $name = "PHP_" . strtoupper($key);
                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        $item = $name . '_' . strtoupper($k);
                        putenv("$item=$v");
                    }
                } else {
                    putenv("$name=$val");
                }
            }
        } else {
            var_dump("加载失败");
        }
    }

    /**
     * 获取环境变量值
     * @access public
     * @param string $filePath 环境变量名（支持二级 . 号分割）
     * @param string $name 环境变量名（支持二级 . 号分割）
     * @param string $default 默认值
     * @return mixed
     */
    public static function get($name, $filePath = ".env", $default = null)
    {
        self::getInstance($filePath);


        $result = getenv('PHP_' . strtoupper(str_replace('.', '_', $name)));

        if (false !== $result) {
            if ('false' === $result) {
                $result = false;
            } elseif ('true' === $result) {
                $result = true;
            }

            return $result;
        }

        return $default;
    }
}