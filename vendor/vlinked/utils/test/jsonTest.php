<?php

namespace VlinkedUtils;

use VlinkedUtils\Lottery\Item;

require_once __DIR__ . "/../vendor/autoload.php";
define("LOG_DIR", "/Users/jrexe/work/VLINKED/VlinkedUtils/runtime/log/");

$arr = [
    'test' =>
        [
            'ddd' => [
                "ssa" => 12121
            ],
            "de" => "adasd1s",
            "hint" => "去你妹",
        ]
];


try {
    echo Json::encode($arr);
} catch (JsonException $e) {
    var_dump($e->getMessage());
}

echo Random::generate(10, '0-9');


$item[] = Item::load("11", "231", 12);
$item[] = Item::load("11", "231", 12);
$item[] = Item::load("11", "231", 12);
$item[] = Item::load("11", "231", 12);

var_export(Json::encode($item));