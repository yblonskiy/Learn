<?php

/*
 * params:
 * 'session' as string,
*/

error_reporting(0);

require_once(realpath(dirname(__FILE__) . "/../bll/user.php"));
require_once(realpath(dirname(__FILE__) . "/../bll/session.php"));

use BLL\bUser;
use BLL\bSession;

header('Content-type: text/json; charset=UTF-8');

$list = array();

if (!isset($_POST['session'])) {
    echo json_encode($list, JSON_UNESCAPED_UNICODE);
    return;
}

$session = $_POST['session'];

/*
// datas to test
$session = "RwSU7vMT0Y1d2Mf";
*/

$ssn = new bSession();

if ($ssn->LoadBySession($session)) {

    $user = new bUser();

    $list = $user->GetUsers();
}

echo json_encode($list, JSON_UNESCAPED_UNICODE);

?>