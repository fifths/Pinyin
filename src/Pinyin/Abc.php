<?php
/**
 * Created by PhpStorm.
 * User: lee
 * Date: 15-10-30
 * Time: 下午4:35
 */

namespace Pinyin;


class Abc
{
    public function GetPinyin()
    {
        $file = dirname(__FILE__) . '/data/cedict_ts.u8';
        $fp = fopen($file, 'r');
        $line = trim(fgets($fp));
        $a=explode('/',$line);
        $b=$a[0];
        $c=explode(' ',$b);
        $d=str_replace($c[0],'',$b);
        var_dump(trim($d));
    }

}