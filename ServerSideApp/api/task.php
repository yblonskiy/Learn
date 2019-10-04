<?php

error_reporting(0);

require_once(realpath(dirname(__FILE__) . "/../bll/session.php"));
require_once(realpath(dirname(__FILE__) . "/../bll/task_status_history.php"));
require_once(realpath(dirname(__FILE__) . "/../helper/task_helper.php"));

use BLL\bSession;
use BLL\bTaskStatusHistory;
use Helper\hTask;

header('Content-type: text/json; charset=UTF-8');

$res = array();

$res['result'] = '0';

/*
$session = "RwSU7vMT0Y1d2Mf";
$action = "upload_tasks";
$tasks_json = '[{"task_id":1,"tps":[{"tp_id":52,"defects":[{"defect_id":1}]},{"tp_id":53,"defects":[{"defect_id":1}]},{"tp_id":54,"defects":[{"defect_id":1}]},{"tp_id":55,"defects":[{"defect_id":1}]},{"tp_id":56,"defects":[{"defect_id":1}]},{"tp_id":57,"defects":[{"defect_id":1}]},{"tp_id":58,"defects":[{"defect_id":1}]},{"tp_id":59,"defects":[{"defect_id":1}]},{"tp_id":60,"defects":[{"defect_id":1}]},{"tp_id":61,"defects":[{"defect_id":1}]},{"tp_id":62,"defects":[{"defect_id":1}]},{"tp_id":63,"defects":[{"defect_id":1}]},{"tp_id":64,"defects":[{"defect_id":1}]},{"tp_id":65,"defects":[{"defect_id":1}]},{"tp_id":66,"defects":[{"defect_id":2}]},{"tp_id":67,"defects":[{"defect_id":1}]},{"tp_id":68,"defects":[{"defect_id":1}]},{"tp_id":69,"defects":[{"defect_id":1}]},{"tp_id":70,"defects":[{"defect_id":1}]},{"tp_id":71,"defects":[{"defect_id":1}]},{"tp_id":72,"defects":[{"defect_id":1}]},{"tp_id":73,"defects":[{"defect_id":1}]},{"tp_id":74,"defects":[{"defect_id":1}]},{"tp_id":75,"defects":[{"defect_id":1}]},{"tp_id":76,"defects":[{"defect_id":1}]},{"tp_id":77,"defects":[{"defect_id":5}]},{"tp_id":78,"defects":[{"defect_id":1}]}],"spans":[{"span_id":374,"defects":[{"defect_id":1}]},{"span_id":375,"defects":[{"defect_id":1}]},{"span_id":376,"defects":[{"defect_id":1}]},{"span_id":377,"defects":[{"defect_id":1}]},{"span_id":378,"defects":[{"defect_id":1}]},{"span_id":379,"defects":[{"defect_id":1}]},{"span_id":380,"defects":[{"defect_id":1}]},{"span_id":381,"defects":[{"defect_id":1}]},{"span_id":382,"defects":[{"defect_id":1}]},{"span_id":383,"defects":[{"defect_id":1}]},{"span_id":384,"defects":[{"defect_id":1}]},{"span_id":385,"defects":[{"defect_id":1}]},{"span_id":386,"defects":[{"defect_id":4}]},{"span_id":387,"defects":[{"defect_id":1}]},{"span_id":388,"defects":[{"defect_id":1}]},{"span_id":389,"defects":[{"defect_id":1}]},{"span_id":390,"defects":[{"defect_id":1}]},{"span_id":391,"defects":[{"defect_id":1}]},{"span_id":392,"defects":[{"defect_id":1}]},{"span_id":393,"defects":[{"defect_id":1}]},{"span_id":394,"defects":[{"defect_id":1}]},{"span_id":395,"defects":[{"defect_id":1}]},{"span_id":396,"defects":[{"defect_id":1}]},{"span_id":397,"defects":[{"defect_id":1}]},{"span_id":398,"defects":[{"defect_id":1}]},{"span_id":399,"defects":[{"defect_id":1}]},{"span_id":400,"defects":[{"defect_id":2}]}]}]';
*/

if (!isset($_POST['session']) || !isset($_POST['action'])) {
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
    return;
}

$session = $_POST['session'];
$action = $_POST['action'];

$ssn = new bSession();

if ($ssn->LoadBySession($session)) {

    if ($_POST['action'] == 'update_status') {

        if (isset($_POST['task_id']) && isset($_POST['task_status_id'])) {
            $task_id = $_POST['task_id'];
            $task_status_id = $_POST['task_status_id'];

            $obj_task_status_history = new bTaskStatusHistory();

            if ($obj_task_status_history->AddTask($task_id, $task_status_id) > 0) {
                $res['result'] = '1';
            }
        }
    } else if ($_POST['action'] == 'upload_tasks') {

        if (isset($_POST['tasks'])) {

            $tasks_json = $_POST['tasks'];
            $tasks_array = json_decode($tasks_json, JSON_UNESCAPED_UNICODE);

            $obj_task_helper = new hTask();

            if ($obj_task_helper->UploadTask($tasks_array)) {
                $res['result'] = '1';
            }
        }
    }
}

echo json_encode($res, JSON_UNESCAPED_UNICODE);

?>