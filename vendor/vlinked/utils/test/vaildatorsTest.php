<?php


namespace VlinkedUtils;


require_once __DIR__ . "/../vendor/autoload.php";

$arr = [1, 2, 3, 4, 5];
$isOk = false;
/**
 * 判断当前数值数组
 */
$isOk = Validators::is($arr, 'array');
Debug::log($isOk);

/**
 * 判断当前的array全部为数字
 */
$arr = [1, 2, 3, 4, 5];
$isOk2 = Validators::is($arr, 'number[]');
Debug::log($isOk2);

Debug::log(Validators::is("12131", 'pattern:\d{5}'));
