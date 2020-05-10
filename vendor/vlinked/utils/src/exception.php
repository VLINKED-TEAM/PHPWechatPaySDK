<?php

/**
 * 自定义异常处理类
 */

namespace VlinkedUtils;


/**
 * 参数范围输入异常
 * Class ArgumentOutOfRangeException
 * @package VlinkedUtils
 */
class ArgumentOutOfRangeException extends \InvalidArgumentException
{

}

/**
 * 参数类型异常对象
 * Class InvalidArgumentTypeException
 * @package VlinkedUtils
 */
class InvalidArgumentTypeException extends \InvalidArgumentException
{
}

/**
 * 运行时报错
 * Class InvalidStateException
 * @package VlinkedUtils
 */
class InvalidStateException extends \RuntimeException
{
}


class IOException extends \RuntimeException
{
}

class StaticFunctionNotFind extends \RuntimeException
{
}

/**
 * json异常
 * Class JsonException
 * @package VlinkedUtils
 */
class JsonException extends \RuntimeException
{
}

class FileNotFoundException extends IOException
{
}

class RegexpException extends \Exception
{
}


class AssertionException extends \Exception
{

}

namespace VlinkedUtils\Http;

class HttpCurlException extends \Exception
{

    /**
     * @var int curl 的错误码
     */
    public $error_code;
    /**
     * @var string curl的错误信息
     */
    public $error_msg;

    /**
     * HttpException constructor.
     * @param resource $curl_context
     * @param int $code
     */
    public function __construct($curl_context)
    {
        $this->error_msg = curl_error($curl_context);
        $this->error_code = curl_errno($curl_context);
        curl_close($curl_context);
        parent::__construct($this->error_msg, $this->error_code);


    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "errcode" => $this->error_code,
            "errmsg" => $this->error_msg,
        ];
    }

}