<?php

include '../inc/configs.php';
include 'inc/classes.php';

if ($_FILES['PhotoForAddress']['name']) {
    $uploaddir = '../images/addresses/';
    $file = $uploaddir . 'pre' . basename($_FILES['PhotoForAddress']['name']);
    if (move_uploaded_file($_FILES['PhotoForAddress']['tmp_name'], $file)) {
        if (exif_imagetype($file) == IMAGETYPE_JPEG) {
            $file2 = $uploaddir . md5(time() . $file) . '.jpg';
        }
        if (exif_imagetype($file) == IMAGETYPE_PNG) {
            $file2 = $uploaddir . md5(time() . $file) . '.png';
        }
        if (exif_imagetype($file) == IMAGETYPE_GIF) {
            $file2 = $uploaddir . md5(time() . $file) . '.gif';
        }
        if (rename($file, $file2)) {
            list($width, $height, $type, $attr) = getimagesize($file2);
            $newheight = 768;
            $newwidth = 1024;
            if ($width / $height > 4 / 3) {
                $src_height = $height;
                $src_width = $src_height * $newwidth / $newheight;
                $src_y = 0;
                $src_x = ($width / 2) - ($src_width / 2);
            } else {
                $src_width = $width;
                $src_height = $src_width * $newheight / $newwidth;
                $src_x = 0;
                $src_y = ($height / 2) - ($src_height / 2);
            }
            switch ($type) {
                case 1: $source = imagecreatefromgif($file2) or die('Cannot load original GIF');
                    break;
                case 2: $source = imagecreatefromjpeg($file2) or die('Cannot load original JPEG');
                    break;
                case 3: $source = imagecreatefrompng($file2) or die('Cannot load original PNG');
                    break;
            }
            $target = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($target, $source, 0, 0, $src_x, $src_y, $newwidth, $newheight, $src_width, $src_height);
            switch ($type) {
                case 1: imagegif($target, $file2, 80);
                    break;
                case 2: imagejpeg($target, $file2, 80);
                    break;
                case 3: imagepng($target, $file2);
                    break;
            }
            imagedestroy($target);
            imagedestroy($source);
            $file3 = str_replace('../images/addresses/', '../images/addresses/1_', $file2);
            copy($file2, $file3);
            list($width2, $height2, $type2, $attr2) = getimagesize($file3);
            $newheight2 = 165;
            $newwidth2 = 220;
            if ($width2 / $height2 > 4 / 3) {
                $src_height = $height2;
                $src_width = $src_height * 220 / 165;
                $src_y = 0;
                $src_x = ($width2 / 2) - ($src_width / 2);
            } else {
                $src_width = $width2;
                $src_height = $src_width * 165 / 220;
                $src_x = 0;
                $src_y = ($height2 / 2) - ($src_height / 2);
            }
            switch ($type) {
                case 1: $source2 = imagecreatefromgif($file3) or die('Cannot load original GIF');
                    break;
                case 2: $source2 = imagecreatefromjpeg($file3) or die('Cannot load original JPEG');
                    break;
                case 3: $source2 = imagecreatefrompng($file3) or die('Cannot load original PNG');
                    break;
            }
            $target2 = imagecreatetruecolor($newwidth2, $newheight2);
            imagecopyresampled($target2, $source2, 0, 0, $src_x, $src_y, $newwidth2, $newheight2, $src_width, $src_height);
            switch ($type) {
                case 1: imagegif($target2, $file3, 80);
                    break;
                case 2: imagejpeg($target2, $file3, 80);
                    break;
                case 3: imagepng($target2, $file3);
                    break;
            }
            imagedestroy($target2);
            imagedestroy($source2);
            echo $file3;
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}
?>