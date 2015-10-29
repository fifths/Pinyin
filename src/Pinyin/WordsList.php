<?php

namespace Pinyin;

class WordsList
{
    public $dicloadtime; // 词典载入时间
    public $splitwordstime; // 分词时间
    private $starttime; // 分词开始时间
    private $splitchar = ' '; // 切割符，默认为空格
    private $wordsdic = array(); // 词库（为键值为词，值为词频的数组） 本分次尚未用到词频
    private $cnnumber = '０|１|２|３|４|５|６|７|８|９|＋|－|％|．|ａ|ｂ|ｃ|ｄ|ｅ|ｆ|ｇ|ｈ|ｉ|ｊ|ｋ|ｌ|ｍ|ｎ|ｏ|ｐ|ｑ|ｒ|ｓ|ｔ|ｕ|ｖ|ｗ|ｘ|ｙ|ｚ|Ａ|Ｂ|Ｃ|Ｄ|Ｅ|Ｆ|Ｇ|Ｈ|Ｉ|Ｊ|Ｋ|Ｌ|Ｍ|Ｎ|Ｏ|Ｐ|Ｑ|Ｒ|Ｓ|Ｔ|Ｕ|Ｖ|Ｗ|Ｘ|Ｙ|Ｚ'; // 中文全角字母，数字
    private $punctuation = array('/r', '/n', '/t', '`', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '+', '=', '|', '//', '/', '"', ';', ':', '/', '?', '.', '>', ',', '<', '[', '{', ']', '}', '·', '～', '！', '＠', '＃', '￥', '％', '……', '＆', '×', '（', '）', '－', '——', '＝', '＋', '＼', '｜', '【', '｛', '】', '｝', '‘', '“', '”', '；', '：', '、', '？', '。', '》', '，', '《', ' ', '　'); // 标点符号
    private $trimchars = array("▲", "△", "▼", "▽", "★", "☆", "◆", "◇", "■", "□", "●", "○", "⊙", "㊣", "◎", "▂", "▁", "▃", "▄", "▅", "▆", "▇", "█", "▏", "▎", "▍", "▌", "▋", "▊", "◢", "◣", "◥", "◤", "▲", "▼", "♀", "♂", "卍", "※");
    private $resultstr = array(); //分词结果字串

    function __construct($dic_file = '')
    {
        $this->starttime = $this->getmicrotime(); // 构造函数给成员变量$starttime 赋值

        if($dic_file==''){
            $dic_file=dirname(__FILE__) . '/data/pinyin.dat';
        }
        $tmpcontents = $this->getFileContents($dic_file);  // 直接使用file_get_contents()
        if ($tmpcontents) {
            $wordsdic = explode("/r/n", $tmpcontents);
            $i = 0;
            $ttwords = array();
            foreach ($wordsdic as $value) {
                list($twords[], $tfreq[]) = explode("/t", $value);
                $ttwords[$twords[$i]] = $tfreq[$i];
                $i++;
            }
            $this->wordsdic = $ttwords;
        } else {
            exit('无法载入词典'); // 载入词典失败
        }
        unset($ttwords, $twords, $tfreq, $wordsdic); // 释放无用的临时变量
        $this->dicloadtime = (float)$this->getMicrotime() - $this->starttime; // 载入词典时间
    }

    function wordsplit($dic_file = 'DirCMSChineseDictionary.txt')
    {
        $this->__construct($dic_file);
    }

    function splitWords($str) // 分词函数 beta 1.0 2010-5-11
    {
        $result = array(); // 存放临时结果
        $spiltchar = $this->splitchar;
        $str = $this->toHalfWidth($str); // 半角转换
        $strlen = strlen($str); // 需分词的字串长度
        $tmpstr = ''; // 最终结果
        $prechar = 1; // 上一个字符 1：中文 2：英文
        for ($i = 0; $i < $strlen;) {
            if (ord($str[$i]) >= 0x81)  // 汉字
            {
                $tchar = $str[$i] . $str[$i + 1] . $str[$i + 2];
                if (in_array($tchar, $this->trimchars) || in_array($tchar, $this->punctuation)) {
                    $tmpstr .= $spiltchar;
                    $i = $i + 3;
                    continue;
                } else {
                    if ($prechar == 1) {
                        $tmpstr .= $tchar;
                    } else {
                        $tmpstr .= $spiltchar . $tchar;
                    }
                }
                $prechar = 1;
                $i = $i + 3;
            } else // 英文
            {
                if (ord($str[$i]) < 32 || in_array($str[$i], $this->punctuation)) // 将不常见字符替换成分隔符
                {
                    $tmpstr .= $spiltchar;
                    $i++;
                    continue;
                }
                if ($prechar == 1) {
                    $tmpstr .= $spiltchar . $str[$i];
                } else {
                    $tmpstr .= $str[$i];
                }
                $prechar = 2;
                $i++;
            }
        }

        $tmpstr = array_unique(explode(' ', $tmpstr));
        foreach ($tmpstr as $value) {
            $value = trim($value);
            if ($value) {
                if (preg_match('/^[0-9a-z]+$/i', $value) || $this->isWords($value)) {
                    $result[] = $value;
                } else {
                    $vstrlen = strlen($value);
                    $vtstr = '';
                    for ($j = $vstrlen; $j >= 2;) {
                        for ($m = 0; $m < $j; $m += 3) {
                            $tlen = $j - $m;
                            $vtstr = substr($value, $m, $tlen);
                            if ($this->isWords($vtstr)) {
                                $result[] = $vtstr;
                                break;
                            }
                        }
                        $j -= strlen($vtstr);
                    }
                }
            }
        }
        $this->resultstr = array_unique(array_reverse($result)); // 重新排序 ；去除重复
        $this->splitwordstime = (float)$this->getMicrotime() - $this->starttime; // 分词时间
        return $this->resultstr;
    }

    function isWords($words)  //判断是不是一个词（根据词库判断）
    {
        if (!trim($words)) return false;
        return array_key_exists($words, $this->wordsdic);
    }

    function getMicrotime() // 返回浮点数的 microtime()
    {
        if (version_compare(PHP_VERSION, '5.0.0', 'ge')) return microtime(true);
        else {
            list($msec, $sec) = explode(' ', microtime());
            return ((float)$sec + (float)$msec);
        }
    }

    function getFileContents($filename) // 兼容低版本的修正后的 get_file_contents();
    {
        if (!file_exists($filename)) return false; // 不存在文件则返回false
        if (version_compare(PHP_VERSION, '4.3.0', 'ge')) return file_get_contents($filename);
        else {
            $fp = fopen($filename, 'r');
            $contents = fread($fp, filesize($filename));
            fclose($fp);
            return $contents;
        }
    }

    function toHalfWidth($str) //全角字符切换成半角字符
    {
        $cnnumber = explode('|', $this->cnnumber);
        $ennumber = explode('|', '0|1|2|3|4|5|6|7|8|9|+|-|%|.|a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z');
        return str_replace($cnnumber, $ennumber, $str);
    }

    function __destruct() // 清掉字典载入的数组变量
    {
        unset($this->wordsdic);
    }
}

