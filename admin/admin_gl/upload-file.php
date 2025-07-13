<?php

include '../inc/configs.php';
include 'inc/classes.php';

if ($_FILES['BannerLoad']['name']) {
    $uploaddir = '../images/banners/';
    $file = $uploaddir . 'pre' . basename($_FILES['BannerLoad']['name']);
    $ext = end(explode('.', $file));
    if (move_uploaded_file($_FILES['BannerLoad']['tmp_name'], $file)) {
        $file2 = $uploaddir . md5(time() . $file) . '.' . $ext;
        if (rename($file, $file2)) {
            if ($ext == 'gif' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {
                echo '<img src="' . $file2 . '" title="" alt="">';
            } elseif ($ext == 'swf') {
                echo '<embed src="' . $file2 . '" quality="high" width="" height="" wmode="opaque"></embed>';
            } elseif (TRUE) {
                echo str_replace($uploaddir, '', $file2);
            }
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}

if ($_FILES['TextRedaktor']['name']) {
    $uploaddir = '../images/blog/';
    $file = $uploaddir .'pre' . basename($_FILES['TextRedaktor']['name']);
    if (move_uploaded_file($_FILES['TextRedaktor']['tmp_name'], $file)) {
        if (exif_imagetype($file) == 1) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['TextRedaktor']['name'])) . '.gif';
        }
        if (exif_imagetype($file) == 2) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['TextRedaktor']['name'])) . '.jpg';
        }
        if (exif_imagetype($file) == 3) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['TextRedaktor']['name'])) . '.png';
        }
        if (rename($file, $file2)) {
            list($width, $height, $type, $attr) = getimagesize($file2);
            $newwidth = 800;
            if($width < $newwidth) {
                $newwidth = $width;
            }
            $newheight = $height / ($width / $newwidth);
            switch ($type) {
                case 1: $source = imagecreatefromgif($file2) or die('Cannot load original GIF');
                    break;
                case 2: $source = imagecreatefromjpeg($file2) or die('Cannot load original JPEG');
                    break;
                case 3: $source = imagecreatefrompng($file2) or die('Cannot load original PNG');
                    break;
            }
            $target = imagecreatetruecolor($newwidth, $newheight);
            if ($type == 3) {
                imageAlphaBlending($target, FALSE);
                imagesavealpha($target, TRUE);
            }
            imagecopyresampled($target, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
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
            echo $file2;
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}
?>