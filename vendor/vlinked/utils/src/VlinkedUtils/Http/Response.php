<?php


namespace VlinkedUtils\Http;


use VlinkedUtils\InvalidArgumentTypeException;

class Response
{
    /**
     * 服务器端响应 json 返回
     * @param array $arr
     * @param bool $exit 是否在输出后结束响应
     * @return string
     * @throws InvalidArgumentTypeException
     */
    public static function json(array $arr, $exit = true)
    {

        if (!is_array($arr)) {
            throw new InvalidArgumentTypeException("arr 必须传入 数组类型");
        }
        $json_str = json_encode($arr, JSON_UNESCAPED_UNICODE);
        if ($exit) {
            ob_clean();
            header("Content-Type: application/json");
            echo $json_str;
            exit();
        } else {
            return $json_str;
        }
    }

}
