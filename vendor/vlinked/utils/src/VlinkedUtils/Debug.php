<?php


namespace VlinkedUtils;


class Debug
{
    public static function log($var)
    {
        if (is_array($var) || is_object($var) || is_bool($var)) {
            var_export($var);
        } else {
            echo $var;
        }
        echo PHP_EOL;
    }
}