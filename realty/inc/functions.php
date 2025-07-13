<?php
function dropBackWords($word) {
    $reg = "/(ый|ой|ая|ия|ий|ое|ые|ому|а|о|у|е|ого|ему|и|ство|ых|ох|я|ют|ат|ок)$/i";
    if (!preg_match($reg, $word)) {
        $word .= 'ного';
    } else {
        if (preg_match("/(о)$/i", $word)) {
            $word = preg_replace("/(о)$/i", 'яного', $word);
        }
        if (preg_match("/(во)$/i", $word)) {
            $word = preg_replace("/(во)$/i", 'вянного', $word);
        }
        if (preg_match("/(к)$/i", $word)) {
            $word = preg_replace("/(к)$/i", 'чного', $word);
        }
        if (preg_match("/(ый|ой|ий)$/i", $word)) {
            $word = preg_replace("/(ый|ой|ий)$/i", 'ого', $word);
        }
    }
    return $word;
}

?>