<?php
session_start();
//You can customize your captcha settings here
 
$captcha_code = '';
$captcha_image_height = 60;
$captcha_image_width = 200;
$total_characters_on_image = 5;
 
//The characters that can be used in the CAPTCHA code.
//avoid all confusing characters and numbers (For example: l, 1 and i)
//$possible_captcha_letters = 'bcdfghjkmnpqrstvwxyz23456789';
//$possible_captcha_letters = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
//$possible_captcha_letters = "abcdefghjkmnopqrstuvwxyz23456789ABDEFGHLMNQRT";
$possible_captcha_letters = "abcdefghjkmnopqrstuvwxyz23456789";

/*
$captcha_font = realpath(dirname(__DIR__)).'/captcha/fonts/Alam_font.ttf';
        $captcha_font = realpath(dirname(__DIR__)).'/captcha/fonts/DejaVuSans-Bold.ttf';
    $captcha_font = realpath(dirname(__DIR__)).'/captcha/fonts/DejaVuSans-ExtraLight.ttf';
        $captcha_font = realpath(dirname(__DIR__)).'/captcha/fonts/DejaVuSerif-Bold.ttf';
    $captcha_font = realpath(dirname(__DIR__)).'/captcha/fonts/DejaVuSerif-Italic.ttf';
        $captcha_font = realpath(dirname(__DIR__)).'/captcha/fonts/monofont.ttf';
        $captcha_font = realpath(dirname(__DIR__)).'/captcha/fonts/TheanoDidot-Regular.ttf';
$captcha_font = realpath(dirname(__DIR__)).'/captcha/fonts/TheanoModern-Regular.ttf';
$captcha_font = realpath(dirname(__DIR__)).'/captcha/fonts/Tinos-Italic.ttf';

*/

//$captcha_font = realpath(dirname(__DIR__)).'/captcha/fonts/Alam_font.ttf';
$captcha_font = realpath(dirname(__DIR__)).'/captcha/fonts/DejaVuSans-ExtraLight.ttf';
//$captcha_font = realpath(dirname(__DIR__)).'/captcha/fonts/TheanoModern-Regular.ttf';


$random_captcha_dots = 60;
$random_captcha_lines = 25;
$captcha_text_color = "0x142864";
$captcha_noise_color = "0x142864";
 
 
$count = 0;
while ($count < $total_characters_on_image) { 
$captcha_code .= substr(
	$possible_captcha_letters,
	mt_rand(0, strlen($possible_captcha_letters)-1),
	1);
$count++;
}
 
$captcha_font_size = (int) ($captcha_image_height * 0.6);
$captcha_image = @imagecreate(
	$captcha_image_width,
	$captcha_image_height
	);
 
/* setting the background, text and noise colours here */
$background_color = imagecolorallocate(
	$captcha_image,
	225,
	225,
	225
	);
 
$array_text_color = hextorgb($captcha_text_color);
$captcha_text_color = imagecolorallocate(
	$captcha_image,
	$array_text_color['red'],
	$array_text_color['green'],
	$array_text_color['blue']
	);
 
$array_noise_color = hextorgb($captcha_noise_color);
$image_noise_color = imagecolorallocate(
	$captcha_image,
	$array_noise_color['red'],
	$array_noise_color['green'],
	$array_noise_color['blue']
	);
 
/* Generate random dots in background of the captcha image */
for( $count=0; $count<$random_captcha_dots; $count++ ) {
imagefilledellipse(
	$captcha_image,
	mt_rand(0,$captcha_image_width),
	mt_rand(0,$captcha_image_height),
	2,
	3,
	$image_noise_color
	);
}
 
/* Generate random lines in background of the captcha image */
for( $count=0; $count<$random_captcha_lines; $count++ ) {
imageline(
	$captcha_image,
	mt_rand(0,$captcha_image_width),
	mt_rand(0,$captcha_image_height),
	mt_rand(0,$captcha_image_width),
	mt_rand(0,$captcha_image_height),
	$image_noise_color
	);
}
 
/* Create a text box and add 6 captcha letters code in it */
$text_box = imagettfbbox(
	$captcha_font_size,
	0,
	$captcha_font,
	$captcha_code
	); 
$x = (int) (($captcha_image_width - $text_box[4])/2);
$y = (int) (($captcha_image_height - $text_box[5])/2);
imagettftext(
	$captcha_image,
	$captcha_font_size,
	1,
	$x,
	$y,
	$captcha_text_color,
	$captcha_font,
	$captcha_code
	);
 
/* Show captcha image in the html page */
// defining the image type to be shown in browser widow
header('Content-Type: image/jpeg'); 
imagejpeg($captcha_image); //showing the image
imagedestroy($captcha_image); //destroying the image instance
$_SESSION['captcha_code'] = $captcha_code;
 
function hextorgb ($hexstring){
  $integar = hexdec($hexstring);
  return array("red" => 0xFF & ($integar >> 0x10),
               "green" => 0xFF & ($integar >> 0x8),
               "blue" => 0xFF & $integar);
			   }
?>