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

if ($_FILES['WebcamImage']['name']) {
    $uploaddir = '../images/cameras/';
    $file = $uploaddir . 'pre' . basename($_FILES['WebcamImage']['name']);
    if (move_uploaded_file($_FILES['WebcamImage']['tmp_name'], $file)) {
        if (exif_imagetype($file) == 1) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['WebcamImage']['name'])) . '.gif';
        }
        if (exif_imagetype($file) == 2) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['WebcamImage']['name'])) . '.jpg';
        }
        if (exif_imagetype($file) == 3) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['WebcamImage']['name'])) . '.png';
        }
        if (rename($file, $file2)) {
            list($width, $height, $type, $attr) = getimagesize($file2);
            $newheight = 195;
            $newwidth = 260;
            if ($width / $height > 4 / 3) {
                $src_height = $height;
                $src_width = $src_height * 260 / 195;
                $src_y = 0;
                $src_x = ($width / 2) - ($src_width / 2);
            } else {
                $src_width = $width;
                $src_height = $src_width * 195 / 260;
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
            if ($type == 3) {
                imageAlphaBlending($target, FALSE);
                imagesavealpha($target, TRUE);
            }
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
            echo $file2;
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}
?>