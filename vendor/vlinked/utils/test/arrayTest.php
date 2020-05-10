<?php

namespace VlinkedUtils;

require_once __DIR__ . "/../vendor/autoload.php";

$arr = [
    'test' =>
        [
            'ddd' => [
                "ssa" => 12121
            ],
            "de" => "adasd1s"
        ]
];


try {
    echo Arrays::get($_GET, 'test.ddd.22', "f");

    echo "\t\n";
    echo $json = Json::encode($arr) . PHP_EOL;
    var_export(Json::decode($json, 1));
} catch (InvalidArgumentTypeException $e) {
    var_dump($e->getMessage());
}
