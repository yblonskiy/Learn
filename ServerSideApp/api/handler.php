<?php

error_reporting(0);

require_once(realpath(dirname(__FILE__) . "/../lib/geoserver.php"));
require_once(realpath(dirname(__FILE__) . "/../lib/helper.php"));

use Library\GeoServer;
use Library\Helper;

header('Content-type: text/json; charset=UTF-8');

$geo = new GeoServer();
$helper = new Helper();

$list = array();

if (isset($_GET['action'])) {

    if ($_GET['action'] == 'loadtps') {
        if (isset($_GET['rem']) && isset($_GET['voltage'])) {
            $list = $geo->GetTPsByRemAndVoltage($_GET['rem'], $_GET['voltage']);
        }

    } elseif ($_GET['action'] == 'loadlines') {
        if (isset($_GET['tp']) && isset($_GET['rem']) && isset($_GET['voltage'])) {
            $list = $geo->GetLinesByTPAndVoltage($_GET['tp'], $_GET['rem'], $_GET['voltage']);
        }
    } elseif ($_GET['action'] == 'loadmap') {
        if (isset($_GET['tp']) && isset($_GET['line']) && isset($_GET['rem']) && isset($_GET['voltage'])) {
            $list = $helper->GetLine($_GET['tp'], $_GET['line'], $_GET['rem'], $_GET['voltage']);
        }
    }
}

echo json_encode($list, JSON_UNESCAPED_UNICODE);

?>