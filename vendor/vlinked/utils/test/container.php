<?php


namespace VlinkedUtils;

require_once __DIR__ . "/../vendor/autoload.php";

header("Content-Type: text/plain");

var_dump(Container::isWeChat());// 单个判断
var_dump(Container::isREL()); // 单个判断
var_dump(Container::detection()); // 检测用法

var_dump(Container::detection() === Container::WeChat); // 检测用法二

