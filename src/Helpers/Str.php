<?php

namespace App\Helpers;

class Str
{
    /**
     * https://github.com/illuminate/support/blob/master/Str.php#L991
     */
    public static function random(int $length): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytesSize = (int) ceil(($size) / 3) * 3;

            $bytes = random_bytes($bytesSize);

            $string .= substr(
                str_replace(['/', '+', '='], '', base64_encode($bytes)),
                0,
                $size,
            );
        }

        return $string;
    }
}