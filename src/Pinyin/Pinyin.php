<?php

namespace Fifths\Pinyin;
/**
 * User: lee
 */


class Pinyin
{


    /**
     * 获取拼音信息
     *
     * @param $str
     * @param int $ishead 是否为首字母
     * @return string
     */
    public function GetPinyin($str, $ishead = 0)
    {
        $str = iconv('utf-8', 'gbk//ignore', $str);
        $restr = '';
        $str = trim($str);
        $slen = strlen($str);
        $file = dirname(__FILE__) . '/data/pinyin.dat';
        $fp = fopen($file, 'r');
        while (!feof($fp)) {
            $line = trim(fgets($fp));
            $pinyins[$line[0] . $line[1]] = substr($line, 3, strlen($line) - 3);
        }
        fclose($fp);
        for ($i = 0; $i < $slen; $i++) {
            if (ord($str[$i]) > 0x80) {
                $c = $str[$i] . $str[$i + 1];
                $i++;
                if (isset($pinyins[$c])) {
                    if ($ishead == 0) {
                        $restr .= $pinyins[$c];
                    } else {
                        $restr .= $pinyins[$c][0];
                    }
                } else {
                    $restr .= "_";
                }
            } else if (preg_match("/[a-z0-9]/i", $str[$i])) {
                $restr .= $str[$i];
            } else {
                $restr .= "_";
            }
        }
        unset($pinyins);
        return $restr;
    }

}