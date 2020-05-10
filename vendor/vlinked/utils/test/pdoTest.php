<?php


require_once __DIR__ . "/../vendor/autoload.php";

use VlinkedUtils\PayOrder;
use VlinkedUtils\Env;

$host = Env::get("database.host");
$db = 'e_pay_coupons';
$port = Env::get("database.port");
$user = Env::get("database.username");
$pass = Env::get("database.password");
$charset = 'utf8mb4';
$dsn = "mysql:host=$host:$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $list = $pdo->query('SELECT * FROM e_pay_order')->fetchAll(PDO::FETCH_ASSOC);
    var_dump($list);


} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
