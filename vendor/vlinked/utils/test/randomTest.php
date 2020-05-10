<?php
/**
 * 生成0~1随机小数
 * @param Int $min
 * @param Int $max
 * @return Float
 */

namespace VlinkedUtils;

require_once __DIR__ . "/../vendor/autoload.php";

$type = Arrays::get($_GET, "type", "normal");

$img_size = 1024;

header('content-type: image/png');
function randFloat($min = 0, $max = 1)
{
    return $min + mt_rand() / mt_getrandmax() * ($max - $min);
}

if ($type === "normal") {
    $im = imagecreatetruecolor($img_size, $img_size);
    $color1 = imagecolorallocate($im, 255, 255, 255);
    $color2 = imagecolorallocate($im, 0, 0, 0);
    for ($y = 0; $y < $img_size; $y++) {
        for ($x = 0; $x < $img_size; $x++) {
            $rand = randFloat();
            if (round($rand, 2) >= 0.5) {
                imagesetpixel($im, $x, $y, $color1);
            } else {
                imagesetpixel($im, $x, $y, $color2);
            }
        }
    }
    imagepng($im);

} else {
    $im = imagecreatetruecolor($img_size, $img_size);
    $color1 = imagecolorallocate($im, 255, 255, 255);
    $color2 = imagecolorallocate($im, 0, 0, 0);
    for ($y = 0; $y < $img_size; $y++) {
        for ($x = 0; $x < $img_size; $x++) {
            $rand = lcg_value();
            if (round($rand, 2) >= 0.5) {
                imagesetpixel($im, $x, $y, $color1);
            } else {
                imagesetpixel($im, $x, $y, $color2);
            }
        }
    }
    imagepng($im);

}
imagedestroy($im);

Log\FileLog::Info($type);










