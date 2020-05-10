<?php

namespace VlinkedUtils;
require_once __DIR__ . "/../vendor/autoload.php";


$sas = Strings::startsWith("DX12121212", "DX");

var_export($sas);


var_dump(Date::genOrderId("REL", "X"));

$obj = ['code' => 1, 'msg' => "ok"];

echo Json::encode($obj);