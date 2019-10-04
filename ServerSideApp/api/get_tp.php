<?php

/*
 * params:
 * 'session' as string,
 * 'userid' as int,
 * 'taskid' as int (if 0 then load all for current user)
*/

error_reporting(0);

require_once(realpath(dirname(__FILE__) . "/../bll/session.php"));
require_once(realpath(dirname(__FILE__) . "/../bll/technical_place.php"));

use BLL\bSession;
use BLL\bTechnicalPlace;

header('Content-type: text/json; charset=UTF-8');

$list = array();

if (!isset($_POST['session']) || !isset($_POST['user_id'])) {
    echo json_encode($list, JSON_UNESCAPED_UNICODE);
    return;
}

$session = $_POST['session'];
$user_id = $_POST['user_id'];
$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : 0;

/*
// datas to test
$session = "RwSU7vMT0Y1d2Mf";
$user_id = 0;
$task_id = 18;
*/

$ssn = new bSession();

if ($ssn->LoadBySession($session)) {

    $tp = new bTechnicalPlace();

    if ($user_id > 0) {
        $list = $tp->GetByUserID($user_id);
    } else if ($task_id > 0) {
        $list = $tp->GetByTaskID($task_id);
    }
}

echo json_encode($list, JSON_UNESCAPED_UNICODE);

?>