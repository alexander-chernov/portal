<?php
$uploaddir = '../images/news/';
$file = $uploaddir . basename($_FILES['uploadfile']['name']);

if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
    if (exif_imagetype($file) == 1) {
        $file2 = $uploaddir . time() . md5(basename($_FILES['uploadfile']['name'])) . '.gif';
    }
    if (exif_imagetype($file) == 2) {
        $file2 = $uploaddir . time() . md5(basename($_FILES['uploadfile']['name'])) . '.jpg';
    }
    if (exif_imagetype($file) == 3) {
        $file2 = $uploaddir . time() . md5(basename($_FILES['uploadfile']['name'])) . '.png';
    }
    if (rename($file, $file2)) {
        echo $file2;
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>