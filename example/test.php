<?php
/**
 * User: lee
 * Date: 15-10-27
 */

require __DIR__."/../vendor/autoload.php";

$pinyin=new \Fifths\Pinyin\Pinyin();

$str1=$pinyin->GetPinyin("宁波",0);
echo $str1,'<br />';
$str2=$pinyin->GetPinyin("宁波",1);
echo $str2,'<br />';
$str3=$pinyin->GetPinyin("囧字测试",0);
echo $str3,'<br />';