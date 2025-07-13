<?php
/*
$uploaddir = '/admin/images/';
$upl = $_SERVER['DOCUMENT_ROOT'].$uploaddir;
echo $upl.'<br>';
var_dump(dirname($upl));
*/
if ($_FILES['uploadfile']['name']) {
    $uploaddir = '../admin/images/';
    $file = $uploaddir . 'pre_' . basename($_FILES['uploadfile']['name']);
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
            list($width, $height, $type, $attr) = getimagesize($file2);
            $newheight = 768;
            $newwidth = 1024;
            if ($width / $height > 4 / 3) {
                $src_height = $height;
                $src_width = $src_height * 1024 / 768;
                $src_y = 0;
                $src_x = ($width / 2) - ($src_width / 2);
            } else {
                $src_width = $width;
                $src_height = $src_width * 768 / 1024;
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
            $file3 = str_replace('../admin/images/', '../admin/images/1_', $file2);
            copy($file2, $file3);
            list($width, $height, $type, $attr) = getimagesize($file3);
            if ($height > $width) {
                $newheight = 320;
                $newwidth = $width / ($height / $newheight);
            } else {
                $newwidth = 320;
                $newheight = $height / ($width / $newwidth);
            }
            switch ($type) {
                case 1: $source = imagecreatefromgif($file3) or die('Cannot load original GIF');
                    break;
                case 2: $source = imagecreatefromjpeg($file3) or die('Cannot load original JPEG');
                    break;
                case 3: $source = imagecreatefrompng($file3) or die('Cannot load original PNG');
                    break;
            }
            $target = imagecreatetruecolor($newwidth, $newheight);
            if ($type == 3) {
                imageAlphaBlending($target, FALSE);
                imagesavealpha($target, TRUE);
            }
            imagecopyresampled($target, $source, 0, 0, $src_x, $src_y, $newwidth, $newheight, $src_width, $src_height);
            switch ($type) {
                case 1: imagegif($target, $file3, 80);
                    break;
                case 2: imagejpeg($target, $file3, 80);
                    break;
                case 3: imagepng($target, $file3);
                    break;
            }
            imagedestroy($target);
            imagedestroy($source);
            //echo $file3;
            echo '/admin/images/'.basename($file3);
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}

if ($_FILES['photo_load']['name']) {

    $uploaddir = '../admin/images/photo/';
    //$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/admin/images/photo/';
    $file = $uploaddir . 'pre' . basename($_FILES['photo_load']['name']);

    if (move_uploaded_file($_FILES['photo_load']['tmp_name'], $file)) {
        if (exif_imagetype($file) == 1) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['photo_load']['name'])) . '.gif';
        }
        if (exif_imagetype($file) == 2) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['photo_load']['name'])) . '.jpg';
        }
        if (exif_imagetype($file) == 3) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['photo_load']['name'])) . '.png';
        }
        if (rename($file, $file2)) {
            list($width, $height, $type, $attr) = getimagesize($file2);
            if ($width / $height > 4 / 3) {
                $newwidth = 1024;
                $newheight = $height * $newwidth / $width;
            } else {
                $newheight = 1024;
                $newwidth = $width * $newheight / $height;
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
            //$file3 = str_replace($_SERVER['DOCUMENT_ROOT'].'/admin/images/photo/', $_SERVER['DOCUMENT_ROOT'].'/admin/images/photo/1_', $file2);
            $file3 = str_replace('../admin/images/photo/', '../admin/images/photo/1_', $file2);
            copy($file2, $file3);
            list($width, $height, $type, $attr) = getimagesize($file3);
            $newheight = 240;
            $newwidth = 320;
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
                case 1: $source = imagecreatefromgif($file3) or die('Cannot load original GIF');
                    break;
                case 2: $source = imagecreatefromjpeg($file3) or die('Cannot load original JPEG');
                    break;
                case 3: $source = imagecreatefrompng($file3) or die('Cannot load original PNG');
                    break;
            }
            $target = imagecreatetruecolor($newwidth, $newheight);
            if ($type == 3) {
                imageAlphaBlending($target, FALSE);
                imagesavealpha($target, TRUE);
            }
            imagecopyresampled($target, $source, 0, 0, $src_x, $src_y, $newwidth, $newheight, $src_width, $src_height);
            switch ($type) {
                case 1: imagegif($target, $file3, 80);
                    break;
                case 2: imagejpeg($target, $file3, 80);
                    break;
                case 3: imagepng($target, $file3);
                    break;
            }
            imagedestroy($target);
            imagedestroy($source);
            echo '/admin/images/photo/'.basename($file3);
            //echo ' - file moved. ';
        } else {
            echo "error moving file";
        }
    } else {
        echo "error upload file";
    }
    die();
}

if ($_FILES['JobUpload']['name']) {
    $uploaddir = '../admin/images/job/';
    $file = $uploaddir . 'pre' . basename($_FILES['JobUpload']['name']);
    if (move_uploaded_file($_FILES['JobUpload']['tmp_name'], $file)) {
        if (exif_imagetype($file) == 1) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['JobUpload']['name'])) . '.gif';
        }
        if (exif_imagetype($file) == 2) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['JobUpload']['name'])) . '.jpg';
        }
        if (exif_imagetype($file) == 3) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['JobUpload']['name'])) . '.png';
        }
        if (rename($file, $file2)) {
            list($width, $height, $type, $attr) = getimagesize($file2);
            if ($width / $height > 1) {
                $newwidth = 1024;
                $newheight = $height * $newwidth / $width;
            } else {
                $newheight = 1024;
                $newwidth = $width * $newheight / $height;
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
            $file3 = str_replace('../admin/images/job/', '../admin/images/job/1_', $file2);
            copy($file2, $file3);
            list($width, $height, $type, $attr) = getimagesize($file3);
            $newheight = 320;
            $newwidth = 240;
            if ($width / $height > 4 / 3) {
                $src_height = $height;
                $src_width = $src_height * $newwidth / $newheight;
                $src_y = 0;
                $src_x = ($width / 2) - ($src_width / 2);
            } elseif ($height / $width > 4 / 3) {
                $src_width = $width;
                $src_height = $src_width * $newheight / $newwidth;
                $src_x = 0;
                $src_y = ($height / 2) - ($src_height / 2);
            } elseif ($height >= $width) {
                $src_width = $width * 0.7;
                $src_height = $src_width * $newheight / $newwidth;
                $src_x = 0;
                $src_y = ($height / 2) - ($src_height / 2);
            } elseif ($width > $height) {
                $src_height = $height * 0.7;
                $src_width = $src_height * $newwidth / $newheight;
                $src_y = 0;
                $src_x = ($width / 2) - ($src_width / 2);
            }
            switch ($type) {
                case 1: $source = imagecreatefromgif($file3) or die('Cannot load original GIF');
                    break;
                case 2: $source = imagecreatefromjpeg($file3) or die('Cannot load original JPEG');
                    break;
                case 3: $source = imagecreatefrompng($file3) or die('Cannot load original PNG');
                    break;
            }
            $target = imagecreatetruecolor($newwidth, $newheight);
            if ($type == 3) {
                imageAlphaBlending($target, FALSE);
                imagesavealpha($target, TRUE);
            }
            imagecopyresampled($target, $source, 0, 0, $src_x, $src_y, $newwidth, $newheight, $src_width, $src_height);
            switch ($type) {
                case 1: imagegif($target, $file3, 80);
                    break;
                case 2: imagejpeg($target, $file3, 80);
                    break;
                case 3: imagepng($target, $file3);
                    break;
            }
            imagedestroy($target);
            imagedestroy($source);
            //echo $file3;
            echo '/admin/images/job/'.basename($file3);
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}

if ($_FILES['AgentUpload']['name']) {
    $uploaddir = '../admin/images/agents/';
    $file = $uploaddir . 'pre' . basename($_FILES['AgentUpload']['name']);
    if (move_uploaded_file($_FILES['AgentUpload']['tmp_name'], $file)) {
        if (exif_imagetype($file) == 1) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['AgentUpload']['name'])) . '.gif';
        }
        if (exif_imagetype($file) == 2) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['AgentUpload']['name'])) . '.jpg';
        }
        if (exif_imagetype($file) == 3) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['AgentUpload']['name'])) . '.png';
        }
        if (rename($file, $file2)) {
            list($width, $height, $type, $attr) = getimagesize($file2);
            $newheight = 165;
            $newwidth = 220;
            if ($width / $height > 4 / 3) {
                $src_height = $height;
                $src_width = $src_height * 220 / 165;
                $src_y = 0;
                $src_x = ($width / 2) - ($src_width / 2);
            } else {
                $src_width = $width;
                $src_height = $src_width * 165 / 220;
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
            //echo $file2;
            echo '/admin/images/agents/'.basename($file2);
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}

if ($_FILES['ExpertUpload']['name']) {
    $uploaddir = '../admin/images/experts/';
    $file = $uploaddir . 'pre' . basename($_FILES['ExpertUpload']['name']);
    if (move_uploaded_file($_FILES['ExpertUpload']['tmp_name'], $file)) {
        if (exif_imagetype($file) == 1) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['ExpertUpload']['name'])) . '.gif';
        }
        if (exif_imagetype($file) == 2) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['ExpertUpload']['name'])) . '.jpg';
        }
        if (exif_imagetype($file) == 3) {
            $file2 = $uploaddir . time() . md5(basename($_FILES['ExpertUpload']['name'])) . '.png';
        }
        if (rename($file, $file2)) {
            list($width, $height, $type, $attr) = getimagesize($file2);
            $newheight = 165;
            $newwidth = 220;
            if ($width / $height > 4 / 3) {
                $src_height = $height;
                $src_width = $src_height * 220 / 165;
                $src_y = 0;
                $src_x = ($width / 2) - ($src_width / 2);
            } else {
                $src_width = $width;
                $src_height = $src_width * 165 / 220;
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
            //echo $file2;
            echo '/admin/images/experts/'.basename($file2);
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}
?>