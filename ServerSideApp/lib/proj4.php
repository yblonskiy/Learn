<?php

namespace Library;

include(__DIR__ . "/../proj4/vendor/autoload.php");

use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

error_reporting(0);

class Proj4
{
    public function __construct()
    {
    }

    // Get rows by TP and Linename
    public function ConvertToWGS84($x, $y)
    {
        // Initialise Proj4
        $proj4 = new Proj4php();

        $projWGS84 = new Proj('EPSG:4326', $proj4);
        $proj63 = new Proj('+proj=tmerc +lat_0=0 +lon_0=29.5 +k=1 +x_0=3300000 +y_0=-9214.688 +ellps=krass +towgs84=21,-124,-82 +units=m +nadgrids=@null +wktext +over +no_defs', $proj4);

        $pointSrc = new Point($x, $y, $proj63);
        $pointDest = $proj4->transform($projWGS84, $pointSrc);

        $array = array();

        $a =  preg_split("/[\s]/", $pointDest->toShortString());

        $array['x'] = $a[0];
        $array['y'] = $a[1];

        return $array;
    }
}

?>