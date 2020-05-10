<?php



require_once __DIR__ . "/../vendor/autoload.php";


try {
    echo VlinkedUtils\Date::formatDate(VlinkedUtils\Date::DATE_FMT_MYSQL_DATETIME, "2sds2");
} catch (VlinkedUtils\InvalidArgumentTypeException $e) {
    var_dump($e->getMessage());

}
