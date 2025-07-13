<?php

include '../inc/configs.php';
include 'inc/classes.php';

$uploaddir = '../images/banners/';

if ($_FILES['BannerLoad']['name']) {
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
?>