<?php

session_start();

/**
 * Stop sending output untill the script is finished
 * Mkes redirection header() on the middle of a page possible
 */
ob_start();

function sx_generateCaptchaCode($characters = 4)
{
    $possible = "abcdefghjkmnopqrstuvwxyz23456789";
    $code = '';
    for ($i = 0; $i < $characters; $i++) {
        $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
    }
    return $code;
}

$characters = 6;
$code = sx_generateCaptchaCode($characters);
$_SESSION['captcha_code'] = $code;

//$font = realpath(dirname(__DIR__)).'/captcha/fonts/Alam_font.ttf';
$font = 'fonts/Alam_font.ttf';
$width = 200;
$height = 60;

/* font size will be 75% of the image height */
$font_size = $height * 0.75;
$image = imagecreate($width, $height) or die('Cannot initialize new GD image stream');

/* set the colours */
imagecolorallocate($image, 220, 220, 220);
$text_color = imagecolorallocate($image, 100, 120, 220);
$noise_color = imagecolorallocate($image, 140, 160, 220);

/* generate random dots in background */
for ($i = 0; $i < ($width * $height) / 3; $i++) {
    imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noise_color);
}

/* generate random lines in background */
for ($i = 0; $i < ($width * $height) / 150; $i++) {
    imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noise_color);
}

/* create textbox and add text ImageString */
//$textbox = ImageString();
$textbox = imagettfbbox($font_size, 0, $font, $_SESSION['captcha_code'] ) or die('Error in imagettfbbox function');
$x = (int) (($width - $textbox[4]) / 2);
$y = (int) (($height - $textbox[5]) / 2);
$y -= 5;
imagettftext($image, $font_size, 0, $x, $y, $text_color, $font, $_SESSION['captcha_code'] ) or die('Error in imagettftext function');

/* output captcha image to browser */
//header('Content-type: image/png');
header('Content-Type: image/jpeg');
imagejpeg($image);
imagedestroy($image);
