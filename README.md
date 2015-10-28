# Pinyin

> composer require fifths/pinyin

or

>composer.json

    {
        "require": {
            "fifths/pinyin": "*@dev"
        }
    }


>composer udpate


    $pinyin=new \Pinyin\Pinyin();
    echo $pinyin->GetPinyin("中国",0);
