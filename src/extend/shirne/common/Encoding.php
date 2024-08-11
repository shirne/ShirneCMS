<?php

namespace shirne\common;

use think\Exception;

define('UTF32_BIG_ENDIAN_BOM', chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
define('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));
define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));

class Encoding
{


    public static function detect($string)
    {
        $encoding = mb_detect_encoding($string);
        if ($encoding === false) {
            $encoding = self::_detect_utf_encoding($string);
        }
        return $encoding;
    }
    private static function _detect_utf_encoding($text)
    {
        $first2 = substr($text, 0, 2);
        $first3 = substr($text, 0, 3);
        $first4 = substr($text, 0, 3);

        if ($first3 == UTF8_BOM)
            return 'UTF-8';
        elseif ($first4 == UTF32_BIG_ENDIAN_BOM)
            return 'UTF-32BE';
        elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM)
            return 'UTF-32LE';
        elseif ($first2 == UTF16_BIG_ENDIAN_BOM)
            return 'UTF-16BE';
        elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM)
            return 'UTF-16LE';

        return false;
    }

    public static function convert2utf8($string)
    {
        //检测并转换编码
        $encode = self::detect($string);
        if ($encode === false) {
            throw new Exception('detect encoding failed');
        }

        //转换编码
        if ($encode != 'UTF-8') {
            $string = iconv($encode, 'UTF-8//IGNORE', $string);
        }

        //移除utf8-bom
        $string = trim($string);
        if (strpos($string, UTF8_BOM) === 0) {
            $string = substr($string, strlen(UTF8_BOM));
        }

        return $string;
    }
}
