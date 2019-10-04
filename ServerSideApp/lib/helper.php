<?php

namespace Library;

require_once(realpath(dirname(__FILE__) . "/geoserver.php"));
require_once(realpath(dirname(__FILE__) . "/proj4.php"));

use Library\GeoServer;
use Library\Proj4;

class Helper
{
    public $arrayTypePlace = array(
        "опора" => array("name" => "Опора", "id" => "1"),
        "леп" => array("name" => "ЛЕП", "id" => "2"),
        "пс/тп" => array("name" => "ПС/ТП", "id" => "3"),
        "відгалуження" => array("name" => "Відгалуження", "id" => "4"),
        "ответвление" => array("name" => "Відгалуження", "id" => "4"),
        "проліт" => array("name" => "Проліт", "id" => "5"),
        "пролёт" => array("name" => "Проліт", "id" => "5"),
        "секція" => array("name" => "Секція", "id" => "6"),
        "секция" => array("name" => "Секція", "id" => "6"),
        "комірка" => array("name" => "Комірка", "id" => "7"),
        "ячейка" => array("name" => "Комірка", "id" => "7"),
        "трансформатор" => array("name" => "Трансформатор", "id" => "8"),
    );

    public function GetCoordinatFromLine($str)
    {
        $str = str_replace("LINESTRING(", "", $str);
        $str = str_replace(")", "", $str);

        return $str;
    }

    public function GetRemName($str)
    {
        $str = mb_strtolower($str, 'UTF-8');

        $array = array(
            "sever" => "Північний РЕМ",
            "centr" => "Центральний РЕМ",
            "yug" => "Південний РЕМ",
            "anan" => "Ананьївський РЕМ",
            "artsiz" => "Арцизький РЕМ",
            "balta" => "Балтський РЕМ",
            "b-dnestr" => "Б-Днестровський РЕМ",
            "berezov" => "Березівський РЕМ",
            "bilyaiv" => "Біляївський РЕМ",
            "bolgrad" => "Болградський РЕМ",
            "velikomih" => "Великомихайлівський РЕМ",
            "ivan" => "Іванівський РЕМ",
            "izmail" => "Ізмаїльський РЕМ",
            "illichevsk" => "Іллічівський РЕМ",
            "kiliya" => "Кілійський РЕМ",
            "kodyma" => "Кодимський РЕМ",
            "komint" => "Комінтернівський РЕМ",
            "kotov" => "Котовський РЕМ",
            "krasnookn" => "Красноокнянський РЕМ",
            "luba" => "Любашевський РЕМ",
            "nikola" => "Миколаївський РЕМ",
            "ovidiop" => "Овідіопільський РЕМ",
            "razdeln" => "Раздільнянський РЕМ",
            "reni" => "Ренійський РЕМ",
            "savran" => "Савранський РЕМ",
            "sarata" => "Саратський РЕМ",
            "tarutin" => "Тарутінський РЕМ",
            "tatarbunar" => "Татарбунарський РЕМ",
            "frunz" => "Фрунзівський РЕМ",
            "shyr" => "Ширяївський РЕМ"
        );

        return $array[$str];
    }

    public function GetPaging($items_count, $page_number, $page_size, $url)
    {
        //string templates for links
        $link = '<li class="##active##"><a href="##url####id##">##text##</a></li>';
        $link_pre = '<li class="previous##prev_enabled##"><a href="##url####id##">Попередня</a></li>';
        $link_next = '<li class="next##next_enabled##"><a href="##url####id##">Наступна</a></li>';

        $paging = '<ul class="pagination">';

        $count_pages = ceil($items_count / $page_size);

        //setting page numbers with links
        if ($page_number == 1) {
            $str = str_replace("##prev_enabled##", " disabled", $link_pre);
            $str = str_replace("##url##", "#", $str);
            $str = str_replace("##id##", "", $str);

            $paging .= $str;
        } else {
            $str = str_replace("##prev_enabled##", "", $link_pre);
            $str = str_replace("##url##", $url, $str);
            $str = str_replace("##id##", "?id=" . ($page_number - 1), $str);

            $paging .= $str;
        }

        //generate dynamic paging
        $start = 1;

        //set up the ist page and the last page
        if ($count_pages > 7) {
            if (($page_number - 3) > 1) {
                $str = str_replace("##url##", $url, $link);
                $str = str_replace("##id##", "?id=1", $str);
                $str = str_replace("##text##", "1", $str);
                $str = str_replace("##active##", "", $str);

                $paging .= $str;
                $paging .= '<li class="paginate_button disabled"><a class="paginate fdot" href="#">...</a></li>';

                $start = $page_number - 3;
                if (($count_pages - $start) < 7) {
                    $start = $count_pages - 6;
                }
            } else {
                $start = 1;
            }
        }

        for ($i = $start; $i < $start + 7; $i++) {
            if ($i > $count_pages) continue;

            //create dynamic HyperLinks
            if ($i == $page_number)//current page
            {
                $str = str_replace("##url##", "#", $link);
                $str = str_replace("##id##", "?id=" . $i, $str);
                $str = str_replace("##text##", $i, $str);
                $str = str_replace("##active##", "active", $str);

                $paging .= $str;
            } else {
                $str = str_replace("##url##", $url, $link);
                $str = str_replace("##id##", "?id=" . $i, $str);
                $str = str_replace("##text##", $i, $str);
                $str = str_replace("##active##", "", $str);

                $paging .= $str;
            }
        }

        //set up the ist page and the last page
        if ($count_pages > 7) {
            if (($page_number + 3) < $count_pages) {
                $paging .= '<li class="paginate_button disabled"><a class="paginate fdot" href="#">...</a></li>';

                $str = str_replace("##url##", $url, $link);
                $str = str_replace("##id##", "?id=" . $count_pages, $str);
                $str = str_replace("##text##", $count_pages, $str);
                $str = str_replace("##active##", "", $str);

                $paging .= $str;
            }
        }

        if ($page_number == $count_pages) {
            $str = str_replace("##next_enabled##", " disabled", $link_next);
            $str = str_replace("##url##", "#", $str);

            $paging .= $str;
        } else {
            $str = str_replace("##next_enabled##", "", $link_next);
            $str = str_replace("##url##", $url, $str);
            $str = str_replace("##id##", "?id=" . ($page_number + 1), $str);

            $paging .= $str;
        }

        $paging .= '</ul>';

        return $paging;
    }

    public function GetInfoMessage($str)
    {
        return '<div class="info_message">' . $str . '</div>';
    }

    public function GetErrorMessage($str)
    {
        return '<div class="error_message">' . $str . '</div>';
    }

    public function IsManager()
    {
        return $_SESSION['user']['user_types_id'] == 1;
    }

    public function GetVoltageLevel($str, $revert = false)
    {
        $str = mb_strtolower($str, 'UTF-8');

        if ($revert == true) {
            $array = array(
                "0002" => "0.22",
                "0004" => "0.4",
                "0060" => "6",
                "0100" => "10",
                "0200" => "20"
            );
        } else {
            $array = array(
                "0.22" => "0002",
                "0.4" => "0004",
                "6" => "0060",
                "10" => "0100",
                "20" => "0200"
            );
        }

        return $array[$str];
    }

    public function GetTypePlaceID($str)
    {
        $str = mb_strtolower($str, 'UTF-8');

        return $this->arrayTypePlace[$str]['id'];
    }

    public function ParseTypePlace($str)
    {
        $str = mb_strtolower($str, 'UTF-8');

        foreach ($this->arrayTypePlace as $i => $value) {
            if (mb_strpos($str, $i) > -1) {
                return $value['name'];
            }
        }

        return "";
    }

    public function GetLine($tp, $line, $rem, $voltage)
    {
        $arr_all = array();

        $arr_spans = array();
        $arr_tps = array();
        $arr_consumers = array();

        $proj4 = new Proj4();
        $geo = new GeoServer();

        $list_spans = $geo->GetSpansByTPandLineName($tp, $line, $rem, $voltage);
        $list_consumers = $geo->GetСonsumersByTPandLineName($tp, $line, $rem, $voltage);

        if (count($list_spans) == 0 || count($list_consumers) == 0) {
            return $arr_all;
        }

        for ($i = 0; $i < count($list_spans); $i++) {

            $start_end = preg_split("/[,]/", $this->GetCoordinatFromLine($list_spans[$i]["geom"]));

            $start = preg_split("/[\s]/", $start_end[0]);
            $end = preg_split("/[\s]/", $start_end[1]);

            $startXY = $proj4->ConvertToWGS84($start[0], $start[1]);
            $endXY = $proj4->ConvertToWGS84($end[0], $end[1]);

            // Spans

            $spans = array();

            $spans['latitude_start'] = $startXY['y'];
            $spans['longitude_start'] = $startXY['x'];
            $spans['latitude_end'] = $endXY['y'];
            $spans['longitude_end'] = $endXY['x'];

            $arr_spans[] = $spans;

            // TPs

            $tps = array();

            $tps['latitude'] = $startXY['y'];
            $tps['longitude'] = $startXY['x'];

            $arr_tps[] = $tps;
        }

        for ($i = 0; $i < count($list_consumers); $i++) {

            $start_end = preg_split("/[,]/", $this->GetCoordinatFromLine($list_consumers[$i]["geom"]));

            $start = preg_split("/[\s]/", $start_end[0]);
            $end = preg_split("/[\s]/", $start_end[1]);

            $startXY = $proj4->ConvertToWGS84($start[0], $start[1]);
            $endXY = $proj4->ConvertToWGS84($end[0], $end[1]);

            // Consumers

            $consumers = array();

            $consumers['latitude_start'] = $startXY['y'];
            $consumers['longitude_start'] = $startXY['x'];
            $consumers['latitude_end'] = $endXY['y'];
            $consumers['longitude_end'] = $endXY['x'];

            $arr_consumers[] = $consumers;
        }

        $arr_all['spans'] = $arr_spans;
        $arr_all['tps'] = $arr_tps;
        $arr_all['consumers'] = $arr_consumers;

        return $arr_all;
    }

    public function encrypt($str)
    {
        //Key for encrypt text (it must be equal to the key in your ios project)
        $key = 'a16byteslongkey!a16byteslongkey!';

        $block = mcrypt_encrypt('rijndael_128', 'cbc');
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);

        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_CBC)));
    }

    public function decrypt($str)
    {
        //Key for encrypt text (it must be equal to the key in your ios project)
        $key = 'a16byteslongkey!a16byteslongkey!';

        $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($str), MCRYPT_MODE_CBC);
        $block = mcrypt_get_block_size('rijndael_128', 'cbc');
        $pad = ord($str[($len = strlen($str)) - 1]);

        return substr($str, 0, strlen($str) - $pad);

    }

    public function encode($string)
    {
        return htmlspecialchars(addslashes($string));
    }

    public function decode($string)
    {
        return html_entity_decode(stripslashes($string));
    }

    public function rand_string($typeString, $intLength = 6)
    {
        if ($typeString == 1) {
            $validCharacters = "abcdefghijklmnopqrstuxyvwz0123456789ABCDEFGHIJKLMNOPQRSTUXYVWZ";
        }
        if ($typeString == 2) {
            $validCharacters = "1234567890";
        }
        if ($typeString == 3) {
            $validCharacters = "abcdefghijklmnopqrstuxyvwz";
        }
        if ($typeString == 4) {
            $validCharacters = "ABCDEFGHIJKLMNOPQRSTUXYVWZ";
        }

        $validCharNumber = strlen($validCharacters);
        $result = "";
        for ($i = 0; $i < $intLength; $i++) {
            $index = mt_rand(0, $validCharNumber - 1);
            $result .= $validCharacters[$index];
        }
        return $result;
    }

}


?>