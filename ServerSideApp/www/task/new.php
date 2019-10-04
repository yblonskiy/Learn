<?php

error_reporting(0);

include('../extra.php');
include('../checks.php');

require_once(realpath(dirname(__FILE__) . "/../../lib/helper.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/user.php"));
require_once(realpath(dirname(__FILE__) . "/../../helper/task_helper.php"));

use Helper\hTask;
use Library\Helper;
use BLL\bUser;

$obj_task_helper = new hTask();
$obj_helper = new Helper();
$obj_user = new bUser();

$message = "";

if (isset($_POST['taskname']) && isset($_POST['taskuserid']) && isset($_POST['taskrem'])
    && isset($_POST['tasktp']) && isset($_POST['taskline']) && isset($_POST['taskvoltagelevel']) && isset($_POST['tasktype'])
) {
    $taskname = $_POST['taskname'];
    $taskrem = $_POST['taskrem'];
    $tasktp = $_POST['tasktp'];
    $taskline = $_POST['taskline'];
    $taskuserid = $_POST['taskuserid'];
    $taskvoltagelevel = $_POST['taskvoltagelevel'];
    $tasktype = $_POST['tasktype'];

    /*
        $taskname = 'Завдання 31';
         $taskrem = 'Ivan';
         $tasktp = 'ТП-304';
         $taskline = 'Л-1';
         $taskuserid = 6;
         $taskvoltagelevel = '0.4';
        $tasktype = 'Плановий';
    */

    if (empty($taskname)) {
        $message = $obj_helper->GetErrorMessage('Введіть "Назва огляду".');
    } else {
        $voltage_level_full = $obj_helper->GetVoltageLevel($taskvoltagelevel);
        $db_voltage = str_replace(".", "", $taskvoltagelevel);

        if ($obj_task_helper->AddTask($taskuserid, $taskname, $taskrem, $tasktp, $taskline, true, $voltage_level_full, $db_voltage, $tasktype)) {
            $_SESSION['task_created'] = 1;
            header('Location: list.php');
            exit;
        } else {
            $message = $obj_helper->GetErrorMessage('Неможливо створити огляд.');
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<?php include('../content/head.php'); ?>

<body>

<div id="wrapper">

    <?php include('../content/header.php'); ?>

    <?php include('../content/menu.php'); ?>

    <script type="text/javascript">

        $(document)
            .ready(function () {

                $("#taskvoltagelevel").change(function () {
                    $('#tasktp > option').remove();
                    $('#taskline > option').remove();

                    $('#map').hide();

                    if ($('#taskrem').val() != "0" && $(this).val() != "0") {
                        LoadTPs($('#taskrem').val(), $(this).val().replace('.', ''), '<?php echo $GLOBALS["BASE_URL"]; ?>', 'tasktp');
                    }
                });

                $("#taskrem").change(function () {
                    $('#tasktp > option').remove();
                    $('#taskline > option').remove();

                    $('#map').hide();

                    if ($('#taskvoltagelevel').val() != "0" && $(this).val() != "0") {
                        LoadTPs($(this).val(), $('#taskvoltagelevel').val().replace('.', ''), '<?php echo $GLOBALS["BASE_URL"]; ?>', 'tasktp');
                    }
                });

                $("#tasktp").change(function () {
                    $('#taskline > option').remove();

                    $('#map').hide();

                    LoadLines($(this).val(), $("#taskrem").val(), $('#taskvoltagelevel').val().replace('.', ''), '<?php echo $GLOBALS["BASE_URL"]; ?>', 'taskline');
                });

                $("#taskline").change(function () {

                    if ($(this).val() == null || $(this).val() == "0") {
                        $('#map').hide();
                    }
                    else {

                        $.ajax({
                            url: '<?php echo $GLOBALS["BASE_URL"]; ?>api/handler.php',
                            data: {
                                action: 'loadmap',
                                tp: $('#tasktp').val(),
                                line: $('#taskline').val(),
                                rem: $('#taskrem').val(),
                                voltage: $('#taskvoltagelevel').val().replace('.', '')
                            },
                            dataType: 'json',
                            type: 'GET',
                            timeout: 15000,
                            cache: false,
                            beforeSend: function (xhr) {
                                $('#map').hide();
                            },
                            success: function (json) {

                                $('#map').show();

                                var json_consumers = json['consumers'];
                                var json_spans = json['spans'];
                                var json_tps = json['tps'];

                                initMap(json_consumers, json_spans, json_tps);
                            },
                            error: function () {
                                $('#map').hide();
                            }
                        });
                    }

                });

                $("#btntask").click(function (e) {

                    var err = [];

                    if ($('#taskname').val().trim().length == 0)
                        err.push(" - Назва огляду");

                    if ($('#tasktype').val() == "0") {
                        err.push(" - Вид огляду");
                    }

                    if ($('#taskrem').val() == "0") {
                        err.push(" - РЕМ");
                    }

                    if ($('#taskuserid').val() == "0") {
                        err.push(" - Виконавця");
                    }

                    if ($('#taskvoltagelevel').val() == "0") {
                        err.push(" - Рівень напруги");
                    }

                    if ($('#tasktp').val() == null || $('#tasktp').val() == "0") {
                        err.push(" - ТП");
                    }

                    if ($('#taskline').val() == null || $('#taskline').val() == "0") {
                        err.push(" - Лінія");
                    }

                    if (err.length != 0) {
                        alert("Будь-ласка, заповніть наступні дані: \r\n" + err.join("\r\n"));
                        return false;
                    }

                    return true;

                });

            });

    </script>

    <div class="colMain" id="mainContent">

        <h3 class="main_title">Новий огляд</h3>

        <?php echo $message ?>

        <form method="post" action="new.php">

            <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">
                <tbody>
                <tr>
                    <td style="width: 150px;vertical-align: top;">Назва огляду:</td>
                    <td>
                                    <textarea value="" style="width: 500px;" maxlength="200" id="taskname" cols="20"
                                              rows="3" name="taskname"></textarea>

                    </td>
                </tr>
                <tr>
                    <td style="width: 150px;">Вид огляду:</td>
                    <td>
                        <select id="tasktype" name="tasktype" class="select2"
                                style="width:200px;">
                            <option value="0">Не обрано...</option>
                            <option value="Плановий">Плановий</option>
                            <option value="Позаплановий">Позаплановий</option>
                            <option value="Аварійний">Аварійний</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width: 150px;vertical-align: top;">Оберіть виконавця:</td>
                    <td>
                        <select id="taskuserid" name="taskuserid" class="select2" style="width:500px;">
                            <option value="0">Не обрано...</option>

                            <?php

                            $list_users = $obj_user->GetUsers();

                            for ($i = 0; $i < count($list_users); $i++) {
                                echo '<option value="' . $list_users[$i]['id'] . '">' . $list_users[$i]['last_name'] . ' ' . $list_users[$i]['first_name'] . ' ' . $list_users[$i]['patronymic'] . '</option>';
                            }

                            ?>

                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width: 150px;">Рівень напруги, кВ:</td>
                    <td>
                        <select id="taskvoltagelevel" name="taskvoltagelevel" class="select2"
                                style="width:100px;">
                            <option value="0">Не обрано...</option>
                            <option value="0.4">0.4</option>
                            <option value="10">10</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width: 150px;vertical-align: top;">Оберіть РЕМ:</td>
                    <td>
                        <select id="taskrem" name="taskrem" class="select2" style="width:400px;">
                            <option value="0">Не обрано...</option>
                            <option value="Sever">Північний РЕМ</option>
                            <option value="Centr">Центральний РЕМ</option>
                            <option value="Yug">Південний РЕМ</option>
                            <option value="Anan">Ананьївський РЕМ</option>
                            <option value="Artsiz">Арцизький РЕМ</option>
                            <option value="Balta">Балтський РЕМ</option>
                            <option value="B-Dnestr">Б-Днестровський РЕМ</option>
                            <option value="Berezov">Березівський РЕМ</option>
                            <option value="Bilyaiv">Біляївський РЕМ</option>
                            <option value="Bolgrad">Болградський РЕМ</option>
                            <option value="Velikomih">Великомихайлівський РЕМ</option>
                            <option value="Ivan">Іванівський РЕМ</option>
                            <option value="Izmail">Ізмаїльський РЕМ</option>
                            <option value="Illichevsk">Іллічівський РЕМ</option>
                            <option value="Kiliya">Кілійський РЕМ</option>
                            <option value="Kodyma">Кодимський РЕМ</option>
                            <option value="Komint">Комінтернівський РЕМ</option>
                            <option value="Kotov">Котовський РЕМ</option>
                            <option value="Krasnookn">Красноокнянський РЕМ</option>
                            <option value="Luba">Любашевський РЕМ</option>
                            <option value="Nikola">Миколаївський РЕМ</option>
                            <option value="Ovidiop">Овідіопільський РЕМ</option>
                            <option value="Razdeln">Раздільнянський РЕМ</option>
                            <option value="Reni">Ренійський РЕМ</option>
                            <option value="Savran">Савранський РЕМ</option>
                            <option value="Sarata">Саратський РЕМ</option>
                            <option value="Tarutin">Тарутінський РЕМ</option>
                            <option value="Tatarbunar">Татарбунарський РЕМ</option>
                            <option value="Frunz">Фрунзівський РЕМ</option>
                            <option value="Shyr">Ширяївський РЕМ</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width: 150px;vertical-align: top;">Оберіть ТП:</td>
                    <td>
                        <select id="tasktp" name="tasktp" class="select2" style="width:200px;">
                            <option value="0">Не обрано...</option>

                        </select>
                    </td>
                </tr>

                <tr>
                    <td style="width: 150px;vertical-align: top;">Оберіть Лінію:</td>
                    <td>
                        <select id="taskline" name="taskline" class="select2" style="width:200px;">
                            <option value="0">Не обрано...</option>
                        </select>
                    </td>
                </tr>

                </tbody>
            </table>

            <br>

            <input type="submit" name="btntask" id="btntask" value="Створити">

        </form>

        <br>

        <div id="map" style="display: none;"></div>

        <script type="text/javascript"
                src="<?php echo $GLOBALS["BASE_URL"]; ?>www/js/map.js"></script>

        <script type="text/javascript">

            function initMap(json_consumers, json_spans, json_tps) {

                var coors = {};

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
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAjpuE9bhyGO08suiv0toDpS4GWTMeznjg">
        </script>

    </div>

</div>

<?php include('../content/footer.php'); ?>

</body>
</html>
