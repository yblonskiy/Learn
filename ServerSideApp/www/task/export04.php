<?php

error_reporting(0);

include('../extra.php');
include('../checks.php');

require_once(realpath(dirname(__FILE__) . "/../../lib/helper.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/defect.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/vw_tasks.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/technical_place.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/technical_place_defect.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/technical_span_defect.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/technical_consumer_defect.php"));
require_once(realpath(dirname(__FILE__) . "/../../excel/PHPExcel/PHPExcel.php"));
require_once(realpath(dirname(__FILE__) . "/../../excel/PHPExcel/PHPExcel/Writer/Excel5.php"));
require_once(realpath(dirname(__FILE__) . "/../../excel/PHPExcel/PHPExcel/IOFactory.php"));

use BLL\bDefect;
use BLL\vwTasks;
use Library\Helper;
use BLL\bTechnicalPlace;
use BLL\bTechnicalPlaceDefect;
use BLL\bTechnicalSpanDefect;
use BLL\bTechnicalConsumerDefect;

$obj_defect = new bDefect();
$obj_helper = new Helper();
$obj_vw_task = new vwTasks();
$obj_place = new bTechnicalPlace();
$obj_place_defect = new bTechnicalPlaceDefect();
$obj_span_defect = new bTechnicalSpanDefect();
$obj_consumer_defect = new bTechnicalConsumerDefect();

$task_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($task_id <= 0) {
    echo "Потрібно ввести номер огляду.";
    exit;
}

$task = null;

if ($obj_helper->IsManager()) {
    $task = $obj_vw_task->GetByTaskId($task_id);
} else {
    $task = $obj_vw_task->GetByTaskIdAndUserId($task_id, $_SESSION['user']['id']);
}

if ($task === null)
{
    echo "Такого огляду не існує.";
    exit;
}

$xls = PHPExcel_IOFactory::load("./export04.xlsx");

// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);

// Получаем активный лист
$sheet = $xls->getActiveSheet();

$sheet->setCellValue("A4", $obj_helper->GetRemName($task["rem"]));

// Выравнивание текста
$sheet->getStyle('A4')->getAlignment()->setHorizontal(
    PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue("C6", $task_id);

$sheet->setCellValue("A8", $task["type"]);

$sheet->setCellValue("A10", "ПЛ 0,4 кВ від ".$task["tp"]." ".$task["linename"]);

$sheet->setCellValue("A12", (new DateTime($task["date_created"]))->format('d-m-Y H:i:s'));

$user_name = "";
$user = $user->GetUserByID($task["created_user_id"]);

if ($user != null)
{
    $user_name = $user['last_name'] . ' ' . mb_substr($user['first_name'], 0, 1) . '.' . mb_substr($user['patronymic'], 0, 1) . '.';
}

$user_name = $user['post_name']."/".$user_name;

$sheet->setCellValue("B12", $user_name);

$tp_first = 0;
$tp_last = 0;
$list_tps = $obj_place->GetByTaskID($task_id);

if (count($list_tps) > 0) {
    $tp_first = $list_tps[0]["place_id"];
    $tp_last = $list_tps[count($list_tps) - 1]["place_id"];
}

$sheet->setCellValue("B10", $tp_first);
$sheet->setCellValue("C10", $tp_last);

$start = 16;
$add_value_row = 0;
$insert_row = 1;

$tp_defects = $obj_place_defect->GetByTaskID($task_id);

if(count($tp_defects) > 0)
{
    for ($i = 0; $i < count($tp_defects); $i++) {

        if ($tp_defects[$i]["code_full"] != "0") {

            $sheet->insertNewRowBefore($start + $insert_row, 1);

            $sheet->setCellValue("A".($start + $add_value_row), $tp_defects[$i]["code_full"]);
            $sheet->setCellValue("B".($start + $add_value_row), $tp_defects[$i]["title"]);
            $sheet->setCellValue("C".($start + $add_value_row), "Опора № ".$tp_defects[$i]["place_id"]);
            $sheet->setCellValue("D".($start + $add_value_row), "1");
            $sheet->setCellValue("E".($start + $add_value_row), $tp_defects[$i]["notes"]);

            $insert_row += 1;
            $add_value_row += 1;
        }
    }
}

$span_defects = $obj_span_defect->GetByTaskID($task_id);

if(count($span_defects) > 0)
{
    $sheet->insertNewRowBefore($start + $insert_row, 1);
    $insert_row += 1;
    $add_value_row += 1;

    for ($i = 0; $i < count($span_defects); $i++) {

        if ($span_defects[$i]["code_full"] != "0") {

            $sheet->insertNewRowBefore($start + $insert_row, 1);

            $sheet->setCellValue("A".($start + $add_value_row), $span_defects[$i]["code_full"]);
            $sheet->setCellValue("B".($start + $add_value_row), $span_defects[$i]["title"]);
            $sheet->setCellValue("C".($start + $add_value_row), "Прольот № ".$span_defects[$i]["number_span"]);
            $sheet->setCellValue("D".($start + $add_value_row), "1");
            $sheet->setCellValue("E".($start + $add_value_row), $span_defects[$i]["notes"]);

            $insert_row += 1;
            $add_value_row += 1;
        }
    }
}

$consumer_defects = $obj_consumer_defect->GetByTaskID($task_id);

if(count($consumer_defects) > 0)
{
    $sheet->insertNewRowBefore($start + $insert_row, 1);
    $insert_row += 1;
    $add_value_row += 1;

    for ($i = 0; $i < count($consumer_defects); $i++) {

        if ($consumer_defects[$i]["code_full"] != "0") {

            $sheet->insertNewRowBefore($start + $insert_row, 1);

            $sheet->setCellValue("A".($start + $add_value_row), $consumer_defects[$i]["code_full"]);
            $sheet->setCellValue("B".($start + $add_value_row), $consumer_defects[$i]["title"]);
            $sheet->setCellValue("C".($start + $add_value_row), "Споживач № ".$consumer_defects[$i]["place_id"]);
            $sheet->setCellValue("D".($start + $add_value_row), "1");
            $sheet->setCellValue("E".($start + $add_value_row), $consumer_defects[$i]["notes"]);

            $insert_row += 1;
            $add_value_row += 1;
        }
    }
}

$xls->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.'export04'.'.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
$objWriter->save('php://output');

exit;


?>