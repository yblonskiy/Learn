<?php

namespace Helper;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));
require_once(realpath(dirname(__FILE__) . "/../lib/geoserver.php"));
require_once(realpath(dirname(__FILE__) . "/../lib/helper.php"));
require_once(realpath(dirname(__FILE__) . "/../lib/proj4.php"));

require_once(realpath(dirname(__FILE__) . "/../bll/task.php"));
require_once(realpath(dirname(__FILE__) . "/../bll/task_user_history.php"));
require_once(realpath(dirname(__FILE__) . "/../bll/task_status_history.php"));
require_once(realpath(dirname(__FILE__) . "/../bll/technical_span.php"));
require_once(realpath(dirname(__FILE__) . "/../bll/technical_span_defect.php"));
require_once(realpath(dirname(__FILE__) . "/../bll/technical_place.php"));
require_once(realpath(dirname(__FILE__) . "/../bll/technical_place_defect.php"));
require_once(realpath(dirname(__FILE__) . "/../bll/technical_consumer.php"));
require_once(realpath(dirname(__FILE__) . "/../bll/technical_consumer_defect.php"));

use Library\Database;
use Library\GeoServer;
use Library\Helper;
use Library\Proj4;

use BLL\bTasks;
use BLL\bTaskUserHistory;
use BLL\bTaskStatusHistory;
use BLL\bTechnicalPlace;
use BLL\bTechnicalPlaceDefect;
use BLL\bTechnicalSpan;
use BLL\bTechnicalSpanDefect;
use BLL\bTechnicalConsumer;
use BLL\bTechnicalConsumerDefect;

error_reporting(0);

class hTask
{
    public function AddTask($user_id, $name, $rem, $tp, $linename, $active, $voltage_level_full, $db_voltage, $type)
    {
        $res = false;

        $obj_task = new bTasks();
        $obj_task_user_history = new bTaskUserHistory();
        $obj_task_status_history = new bTaskStatusHistory();
        $obj_geo = new GeoServer();
        $obj_helper = new Helper();
        $obj_proj4 = new Proj4();
        $obj_tp_span = new bTechnicalSpan();
        $obj_tp_place = new bTechnicalPlace();
        $obj_tp_consumer = new bTechnicalConsumer();

        $db = new Database();

        try {

            $list_spans = $obj_geo->GetSpansByTPandLineName($tp, $linename, $rem, $db_voltage);
            $list_consumers = $obj_geo->GetСonsumersByTPandLineName($tp, $linename, $rem, $db_voltage);

            if (count($list_spans) == 0 || count($list_consumers) == 0)
            {
                return false;
            }

            $db->Open();

            $db->beginTransaction();

            $task_id = $obj_task->AddTaskTransaction($db, $name, $rem, $tp, $linename, $active, $voltage_level_full, $_SESSION['user']['id'], $type);

            if ($task_id > 0) {

                $obj_task_user_history->AddTaskTransaction($db, $task_id, $user_id);
                $obj_task_status_history->AddTaskTransaction($db, $task_id, 3);

                for ($i = 0; $i < count($list_spans); $i++) {

                    $start_end = preg_split("/[,]/", $obj_helper->GetCoordinatFromLine($list_spans[$i]["geom"]));

                    $start = preg_split("/[\s]/", $start_end[0]);
                    $end = preg_split("/[\s]/", $start_end[1]);

                    $startXY = $obj_proj4->ConvertToWGS84($start[0], $start[1]);
                    $endXY = $obj_proj4->ConvertToWGS84($end[0], $end[1]);

                    // Spans
                    $span = array();

                    $span['place_id'] = $list_spans[$i]["place_id"];
                    $span['place_parent_id'] = $list_spans[$i]["place_parent_id"];
                    $span['tp'] = $list_spans[$i]["tp"];
                    $span['linename'] = $list_spans[$i]["linename"];
                    $span['task_id'] = $task_id;
                    $span['latitude_start'] = $startXY['y'];
                    $span['longitude_start'] = $startXY['x'];
                    $span['latitude_end'] = $endXY['y'];
                    $span['longitude_end'] = $endXY['x'];
                    $span['reviewed'] = "false";
                    $span['number_span'] = $list_spans[$i]["number_span"];

                    $obj_tp_span->AddSpanTransaction($db, $span);

                    // TPs
                    $tp = array();

                    $tp['task_id'] = $task_id;
                    $tp['reviewed'] = "false";
                    $tp['parent_technical_place_id'] = $list_spans[$i]["place_parent_id"];
                    $tp['latitude'] = $startXY['y'];
                    $tp['longitude'] = $startXY['x'];
                    $tp['place_id'] = $list_spans[$i]["place_id"];

                    $obj_tp_place->AddTPTransaction($db, $tp);
                }

                for ($i = 0; $i < count($list_consumers); $i++) {

                    $start_end = preg_split("/[,]/", $obj_helper->GetCoordinatFromLine($list_consumers[$i]["geom"]));

                    $start = preg_split("/[\s]/", $start_end[0]);
                    $end = preg_split("/[\s]/", $start_end[1]);

                    $startXY = $obj_proj4->ConvertToWGS84($start[0], $start[1]);
                    $endXY = $obj_proj4->ConvertToWGS84($end[0], $end[1]);

                    // Consumers
                    $consumer = array();

                    $consumer['place_id'] = $list_consumers[$i]["place_id"];
                    $consumer['tp'] = $list_consumers[$i]["tp"];
                    $consumer['linename'] = $list_consumers[$i]["linename"];
                    $consumer['task_id'] = $task_id;
                    $consumer['latitude_start'] = $startXY['y'];
                    $consumer['longitude_start'] = $startXY['x'];
                    $consumer['latitude_end'] = $endXY['y'];
                    $consumer['longitude_end'] = $endXY['x'];
                    $consumer['reviewed'] = "false";

                    $obj_tp_consumer->AddConsumerTransaction($db, $consumer);
                }

                $db->commitTransaction();

                $res = true;
            } else {
                $db->rollbackTransaction();
            }

        } catch (\Exception $e) {
            $db->rollbackTransaction();
            $res = false;
        } finally {
            $db->Close();
        }

        return $res;
    }

    public function UploadTask($tasks_array)
    {
        $res = false;

        $obj_task = new bTasks();
        $obj_task_status_history = new bTaskStatusHistory();
        $obj_span = new bTechnicalSpan();
        $obj_place = new bTechnicalPlace();
        $obj_consumer = new bTechnicalConsumer();

        $obj_tp_defect = new bTechnicalPlaceDefect();
        $obj_span_defect = new bTechnicalSpanDefect();
        $obj_consumer_defect = new bTechnicalConsumerDefect();

        $db = new Database();

        try {

            $db->Open();

            $db->beginTransaction();

            foreach ($tasks_array as $data => $task) {

                if (!empty($task['task_id'])) {

                    if ($obj_task->IsExistsTaskTransaction($db, $task['task_id'])) {

                        foreach ($task['tps'] as $data1 => $tp) {
                            if ($obj_place->IsExistsTPTransaction($db, $tp['tp_id'])) {

                                $obj_place->UpdateStatusTransaction($db, $tp['tp_id'], true);

                                foreach ($tp['defects'] as $data2 => $defect) {
                                    $obj_tp_defect->AddDefectTransaction($db, $tp['tp_id'], $defect['defect_id'], $defect['notes']);
                                }
                            }
                        }

                        foreach ($task['spans'] as $data1 => $span) {
                            if ($obj_span->IsExistsSpanTransaction($db, $span['span_id'])) {

                                $obj_span->UpdateStatusTransaction($db, $span['span_id'], true);

                                foreach ($span['defects'] as $data2 => $defect) {
                                    $obj_span_defect->AddDefectTransaction($db, $span['span_id'], $defect['defect_id'], $defect['notes']);
                                }
                            }
                        }

                        foreach ($task['consumers'] as $data1 => $consumer) {
                            if ($obj_consumer->IsExistsConsumerTransaction($db, $consumer['consumer_id'])) {

                                $obj_consumer->UpdateStatusTransaction($db, $consumer['consumer_id'], true);

                                foreach ($consumer['defects'] as $data2 => $defect) {
                                    $obj_consumer_defect->AddDefectTransaction($db, $consumer['consumer_id'], $defect['defect_id'], $defect['notes']);
                                }
                            }
                        }

                        $obj_task_status_history->AddTaskTransaction($db, $task['task_id'], 2);
                    }
                }
            }

            $db->commitTransaction();

            $res = true;
        } catch (\Exception $e) {
            $db->rollbackTransaction();
            $res = false;
        } finally {
            $db->Close();
        }

        return $res;
    }
}

?>