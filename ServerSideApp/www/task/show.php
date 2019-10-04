<?php

error_reporting(0);

include('../extra.php');
include('../checks.php');

require_once(realpath(dirname(__FILE__) . "/../../bll/vw_tasks.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/technical_span.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/technical_span_defect.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/technical_place.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/technical_place_defect.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/technical_consumer.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/technical_consumer_defect.php"));
require_once(realpath(dirname(__FILE__) . "/../../lib/helper.php"));

use BLL\vwTasks;
use BLL\bTechnicalSpan;
use BLL\bTechnicalSpanDefect;
use BLL\bTechnicalPlace;
use BLL\bTechnicalPlaceDefect;
use BLL\bTechnicalConsumer;
use BLL\bTechnicalConsumerDefect;
use Library\Helper;

$obj_vw_task = new vwTasks();
$obj_span = new bTechnicalSpan();
$obj_span_defect = new bTechnicalSpanDefect();
$obj_place = new bTechnicalPlace();
$obj_place_defect = new bTechnicalPlaceDefect();
$obj_consumer = new bTechnicalConsumer();
$obj_consumer_defect = new bTechnicalConsumerDefect();
$helper = new Helper();

$task_id = isset($_GET['id']) ? $_GET['id'] : 0;
$task = null;

if ($helper->IsManager()) {
    $task = $obj_vw_task->GetByTaskId($task_id);
} else {
    $task = $obj_vw_task->GetByTaskIdAndUserId($task_id, $_SESSION['user']['id']);
}

$list_consumers = $obj_consumer->GetByTaskID($task_id);
$json_consumers = "";

if (count($list_consumers) > 0) {

    $arr_all = array();

    for ($i = 0; $i < count($list_consumers); $i++) {
        $a = array();

        $a['latitude_start'] = $list_consumers[$i]['latitude_start'];
        $a['longitude_start'] = $list_consumers[$i]['longitude_start'];
        $a['latitude_end'] = $list_consumers[$i]['latitude_end'];
        $a['longitude_end'] = $list_consumers[$i]['longitude_end'];

        $arr_all[] = $a;
    }

    $json_consumers = json_encode($arr_all, JSON_UNESCAPED_UNICODE);
} else {
    $json_consumers = '[]';
}

$list_spans = $obj_span->GetByTaskID($task_id);
$json_spans = "";

if (count($list_spans) > 0) {

    $arr_all = array();

    for ($i = 0; $i < count($list_spans); $i++) {
        $a = array();

        $a['latitude_start'] = $list_spans[$i]['latitude_start'];
        $a['longitude_start'] = $list_spans[$i]['longitude_start'];
        $a['latitude_end'] = $list_spans[$i]['latitude_end'];
        $a['longitude_end'] = $list_spans[$i]['longitude_end'];

        $arr_all[] = $a;
    }

    $json_spans = json_encode($arr_all, JSON_UNESCAPED_UNICODE);
} else {
    $json_spans = '[]';
}

$list_tps = $obj_place->GetByTaskID($task_id);
$json_tps = "";

if (count($list_tps) > 0) {

    $arr_all = array();

    for ($i = 0; $i < count($list_tps); $i++) {
        $a = array();

        $a['latitude'] = $list_tps[$i]['latitude'];
        $a['longitude'] = $list_tps[$i]['longitude'];

        $arr_all[] = $a;
    }

    $json_tps = json_encode($arr_all, JSON_UNESCAPED_UNICODE);
} else {
    $json_tps = '[]';
}

$tp_defects = $obj_place_defect->GetByTaskID($task_id);
$span_defects = $obj_span_defect->GetByTaskID($task_id);
$consumer_defects = $obj_consumer_defect->GetByTaskID($task_id);

?>

<!DOCTYPE html>
<html lang="en">

<?php include('../content/head.php'); ?>

<body>

<div id="wrapper">

    <?php include('../content/header.php'); ?>

    <?php include('../content/menu.php'); ?>

    <div class="colMain" id="mainContent">

        <h3 class="main_title">Перегляд огляду</h3>

        <?php if ($task === null): ?>
            <?php echo $helper->GetErrorMessage('Такого огляду не існує або у Вас відсутній доступ.'); ?>
        <?php else: ?>

            <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">
                <tbody>
                <tr>
                    <td style="width: 130px;vertical-align: top;">Стан огляду:</td>
                    <td class="lbl_text">
                        <?php echo mb_strtoupper($task["status_name"], 'UTF-8'); ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Назва огляду:</td>
                    <td class="lbl_text">
                        <?php echo $task["task_name"]; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Виконавець:</td>
                    <td class="lbl_text">
                        <?php echo $task['last_name'] . ' ' . mb_substr($task['first_name'], 0, 1) . '.' . mb_substr($task['patronymic'], 0, 1) . '.'; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Рівень напруги, кВ:</td>
                    <td class="lbl_text">
                        <?php echo $helper->GetVoltageLevel($task["voltage_level_full"], true); ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">РЕМ:</td>
                    <td class="lbl_text">
                        <?php echo $helper->GetRemName($task["rem"]); ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">ТП:</td>
                    <td class="lbl_text">
                        <?php echo $task["tp"]; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Лінія:</td>
                    <td class="lbl_text">
                        <?php echo $task["linename"]; ?>
                    </td>
                </tr>

                </tbody>
            </table>

            <br>

            <div id="map"></div>

            <script type="text/javascript"
                    src="<?php echo $GLOBALS["BASE_URL"]; ?>www/js/map.js"></script>

            <script type="text/javascript">

                function initMap() {

                    var coors = {};
                    var json_consumers = <?php echo $json_consumers; ?>;
                    var json_spans = <?php echo $json_spans; ?>;
                    var json_tps = <?php echo $json_tps; ?>;

                    for (var key in json_tps) {
                        coors = {
                            lat: parseFloat(json_tps[key].latitude),
                            lng: parseFloat(json_tps[key].longitude)
                        };
                        break;
                    }

                    var map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 16,
                        center: coors
                    });

                    showConsumers(map, json_consumers);
                    showSpans(map, json_spans);
                    showTPs(map, json_tps);
                }

            </script>
            <script async defer
                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAjpuE9bhyGO08suiv0toDpS4GWTMeznjg&callback=initMap">
            </script>

            <br>

        <?php if ($task['task_status_id'] == 2 && count($tp_defects) > 0): ?>

            <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">

                <tbody>
                <tr>
                    <th width="150" height="25" nowrap="nowrap" align="left" class="thCornerL">&nbsp;Номер опори&nbsp;
                    </th>
                    <th width="130" nowrap="nowrap" align="center" class="thTop">&nbsp;Код дефекту&nbsp;</th>
                    <th nowrap="nowrap" align="center" class="thTop">&nbsp;Назва дефекту&nbsp;</th>
                    <th width="250" nowrap="nowrap" align="center" class="thCornerR">&nbsp;Примітка&nbsp;</th>
                </tr>

                <?php

                for ($i = 0; $i < count($tp_defects); $i++) {

                    if ($tp_defects[$i]["code_full"] != "0") {
                        echo '<tr class="' . ($i % 2 ? 'col2' : 'col1') . '">';
                        echo '<td style="width:150px;">Опора № ' . $tp_defects[$i]["place_id"] . '</td>';
                        echo '<td style="width:130px;">' . $tp_defects[$i]["code_full"] . '</td>';
                        echo '<td>' . $tp_defects[$i]["title"] . '</td>';
                        echo '<td style="width:250px;">' . $tp_defects[$i]["notes"] . '</td>';
                        echo ' </tr>';
                    }
                }

                ?>

                </tbody>

            </table>

        <br>

        <?php endif; ?>

        <?php if ($task['task_status_id'] == 2 && count($span_defects) > 0): ?>

            <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">

                <tbody>
                <tr>
                    <th width="150" height="25" nowrap="nowrap" align="left" class="thCornerL">&nbsp;Номер прольоту&nbsp;</th>
                    <th width="130" nowrap="nowrap" align="center" class="thTop">&nbsp;Код дефекту&nbsp;</th>
                    <th nowrap="nowrap" align="center" class="thTop">&nbsp;Назва дефекту&nbsp;</th>
                    <th width="250" nowrap="nowrap" align="center" class="thCornerR">&nbsp;Примітка&nbsp;</th>
                </tr>

                <?php

                for ($i = 0; $i < count($span_defects); $i++) {

                    if ($span_defects[$i]["code_full"] != "0") {
                        echo '<tr class="' . ($i % 2 ? 'col2' : 'col1') . '">';
                        echo '<td style="width:150px;">Прольот № ' . $span_defects[$i]["number_span"] . '</td>';
                        echo '<td style="width:130px;">' . $span_defects[$i]["code_full"] . '</td>';
                        echo '<td>' . $span_defects[$i]["title"] . '</td>';
                        echo '<td style="width:250px;">' . $span_defects[$i]["notes"] . '</td>';
                        echo ' </tr>';
                    }
                }

                ?>

                </tbody>

            </table>

        <br>

        <?php endif; ?>

        <?php if ($task['task_status_id'] == 2 && count($consumer_defects) > 0): ?>

            <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">

                <tbody>
                <tr>
                    <th width="150" height="25" nowrap="nowrap" align="left" class="thCornerL">&nbsp;Номер споживача&nbsp;</th>
                    <th width="130" nowrap="nowrap" align="center" class="thTop">&nbsp;Код дефекту&nbsp;</th>
                    <th nowrap="nowrap" align="center" class="thTop">&nbsp;Назва дефекту&nbsp;</th>
                    <th width="250" nowrap="nowrap" align="center" class="thCornerR">&nbsp;Примітка&nbsp;</th>
                </tr>

                <?php

                for ($i = 0; $i < count($consumer_defects); $i++) {
                    if ($consumer_defects[$i]["code_full"] != "0") {
                        echo '<tr class="' . ($i % 2 ? 'col2' : 'col1') . '">';
                        echo '<td style="width:150px;">Споживач № ' . $consumer_defects[$i]["place_id"] . '</td>';
                        echo '<td style="width:130px;">' . $consumer_defects[$i]["code_full"] . '</td>';
                        echo '<td>' . $consumer_defects[$i]["title"] . '</td>';
                        echo '<td style="width:250px;">' . $consumer_defects[$i]["notes"] . '</td>';
                        echo ' </tr>';
                    }
                }

                ?>

                </tbody>

            </table>

        <br>

        <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

<?php include('../content/footer.php'); ?>

</body>
</html>
