<?php
/**
 * User: lee
 * Date: 15-10-27
 */

require __DIR__."/../vendor/autoload.php";

/*$pinyin=new Pinyin\Pinyin();

$str1=$pinyin->GetPinyin("宁波",0);
echo $str1,'<br />';
$str2=$pinyin->GetPinyin("宁波",1);
echo $str2,'<br />';
$str3=$pinyin->GetPinyin("囧字测试",0);
echo $str3,'<br />';*/


$pinyin=new Pinyin\PinyinMini();
var_dump($pinyin->utf8_to('囧字测试'));
var_dump($pinyin->utf8_to('囧字测试',1));
var_dump($pinyin->to_first('字测试'));