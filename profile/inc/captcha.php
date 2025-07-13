<?php
// start a session
$currentCookieParams = session_get_cookie_params();

$rootDomain = '.tomsk-line.ru';

session_set_cookie_params(
    $currentCookieParams["lifetime"],
    $currentCookieParams["path"],
    $rootDomain,
    $currentCookieParams["secure"],
    $currentCookieParams["httponly"]
);

session_start();

// settings variables
$width = 120;
$height = 50;
$blob_width = 20;
$blob_height = 20;

// image container
$image = imagecreatetruecolor($width, $height);

// background color of the image container
/*$white = imagecolorallocate($image, 230, 230, 230);
$red = imagecolorallocate($image, 255, 0, 0);
$blue = imagecolorallocate($image, 0, 0, 230);*/
$white = imagecolorallocate($image, 230, 230, 230);
//$red = imagecolorallocate($image, 2, 134, 0);
$red = imagecolorallocate($image, 229, 48, 57);
$blue = imagecolorallocate($image, 0, 50, 0);

// fill the image container
imagefilledrectangle($image, 0, 0, $width, $height, $white);


// create the spiral pattern on the image container

$theta = 1;
$thetac = 7;
$radius = 16;
$circles = 20;
$points = 32;

for ($i = 0; $i < ($circles * $points) - 1; $i++) {
    $theta = $theta + $thetac;
    $rad = $radius * ($i / $points );
    $x = ($rad * cos($theta)) + $x_axis;
    $y = ($rad * sin($theta)) + $y_axis;
    $theta = $theta + $thetac;
    $rad1 = $radius * (($i + 1) / $points);
    $x1 = ($rad1 * cos($theta)) + $x_axis;
    $y1 = ($rad1 * sin($theta)) + $y_axis;
    imageline($image, $x, $y, $x1, $y1, $blue);
    $theta = $theta - $thetac;
}

// blob location
$x_axis = rand($blob_width, $width) - ($blob_width / 2);
$y_axis = rand($blob_height, $height) - ($blob_height / 2);

// render the blob on the image container 
imagefilledellipse($image, $x_axis, $y_axis, $blob_width, $blob_height, $red);

// create the mask image
$mask_image = imagecreatetruecolor($width, $height);

// background color of the mask image
$mask_white = imagecolorallocate($mask_image, 255, 255, 255);
$mask_red = imagecolorallocate($mask_image, 255, 0, 0);

// fill the image container
imagefilledrectangle($mask_image, 0, 0, $width, $height, $mask_white);

// render the blob on the mask image
imagefilledellipse($mask_image, $x_axis, $y_axis, $blob_width, $blob_height, $mask_red);

// write the mask image to the buffer
ob_start();
imagepng($mask_image);
$mask_image_data = ob_get_contents();  // we retrive the data that was written in the buffer
ob_end_clean();

// convert the image data to Base64
$mask_image_data_b64 = base64_encode($mask_image_data);

// save the base64 mask image data to the session
$_SESSION['captcha_image_code'] = $mask_image_data_b64;
$_COOKIE['captcha_image_code'] = $mask_image_data_b64;
/*
$fp = fopen('log.txt', 'a+');
fwrite($fp, "\n\n-----------------\n\n");
fwrite($fp, "0.\n" . $mask_image_data_b64."\n");
fwrite($fp, "1.\n" . var_export(session_get_cookie_params(),true));

fwrite($fp, "\n-----------------\n");
fclose($fp);
*/

// Set the content type
header('Content-type: image/jpeg');
header('Cache-control: no-cache');

// render it
imagejpeg($image);

imagedestroy($image);
?>