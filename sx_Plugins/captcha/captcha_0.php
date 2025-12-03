<?php session_start();
if (isset($_SESSION['captcha_code'])) {
    unset($_SESSION['captcha_code']); // destroy the session if already there
}
$string1 = "abcdefghijklmnopqrstuvwxyz";
$string2 = "1234567890";
$string = $string1 . $string2;
$string = str_shuffle($string);
$random_text = substr($string, 0, 6); // change the number to change number of chars
$_SESSION['captcha_code'] = $random_text;

$image = imagecreatetruecolor(200, 60) or die("Cannot Initialize new GD image stream");

$width = imagesx($image);
$height = imagesy($image);

$black = imagecolorallocate($image, 0, 0, 0);
$white = imagecolorallocate($image, 255, 255, 255);
$gray = imagecolorallocate($image, 153, 153, 153);
$red = imagecolorallocatealpha($image, 255, 0, 0, 75);
$green = imagecolorallocatealpha($image, 0, 255, 0, 75);
$blue = imagecolorallocatealpha($image, 0, 0, 255, 75);

imagefilledrectangle($image, 0, 0, $width, $height, $white);
imagefilledrectangle($image, 0, 0, $width, 0, $black);
imagefilledrectangle($image, $width - 1, 0, $width - 1, $height - 1, $black);
imagefilledrectangle($image, 0, 0, 0, $height - 1, $black);
imagefilledrectangle($image, 0, $height - 1, $width, $height - 1, $black);

imagefilledellipse($image, ceil(rand(15, 195)), ceil(rand(10, 50)), 60, 40, $red);
imagefilledellipse($image, ceil(rand(15, 195)), ceil(rand(10, 50)), 60, 40, $green);
imagefilledellipse($image, ceil(rand(15, 195)), ceil(rand(10, 50)), 60, 40, $blue);
imagefilledellipse($image, ceil(rand(15, 195)), ceil(rand(10, 50)), 60, 40, $gray);

/*
$font = realpath(dirname(__DIR__)).'/captcha/fonts/Alam_font.ttf';
        $font = realpath(dirname(__DIR__)).'/captcha/fonts/DejaVuSans-Bold.ttf';
    $font = realpath(dirname(__DIR__)).'/captcha/fonts/DejaVuSans-ExtraLight.ttf';
        $font = realpath(dirname(__DIR__)).'/captcha/fonts/DejaVuSerif-Bold.ttf';
    $font = realpath(dirname(__DIR__)).'/captcha/fonts/DejaVuSerif-Italic.ttf';
        $font = realpath(dirname(__DIR__)).'/captcha/fonts/monofont.ttf';
        $font = realpath(dirname(__DIR__)).'/captcha/fonts/TheanoDidot-Regular.ttf';
$font = realpath(dirname(__DIR__)).'/captcha/fonts/TheanoModern-Regular.ttf';
$font = realpath(dirname(__DIR__)).'/captcha/fonts/Tinos-Italic.ttf';
*/
$font = realpath(dirname(__DIR__)).'/captcha/fonts/TheanoModern-Regular.ttf';

imagettftext($image, 38, 0, 20, 38, $black, $font, $_SESSION['captcha_code']);

//imagestring($image, 5, intval(($width - (strlen($_SESSION['captcha_code']) * 9)) / 2), intval(($height - 15) / 2), $_SESSION['captcha_code'], $black);
//imagestring($image, $font, 100, 20, $_SESSION['captcha_code'], $black);


//header('Content-type: image/jpeg');
//imagejpeg($image);

header('Content-type: image/png');
imagepng($image);
imagedestroy($image);

