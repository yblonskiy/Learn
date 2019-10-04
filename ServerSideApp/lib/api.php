<?php

namespace Library;

//error_reporting(0);

class API
{
    public function POST($url, $array) //: string
    {
        $options = array(
          'http' => array(
            'header'  => array("Content-type: application/x-www-form-urlencoded; charset=utf-8",
                    "User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0",
                    "Accept-Encoding:	gzip, deflate"),
          'method'  => 'POST',
          'request_fulluri' => true,
          'content' => http_build_query($array)
          )
        );

        $context  = stream_context_create($options);

        $result = file_get_contents($url, false, $context);

    /*    $fp = fopen($url, 'r', true, $context);
        $result = stream_get_contents($fp);
        fclose($fp);*/
        
        return $result;
    }
}

?>