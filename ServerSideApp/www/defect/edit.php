<?php

error_reporting(0);

include('../extra.php');
include('../checks.php');

require_once(realpath(dirname(__FILE__) . "/../../lib/helper.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/defect.php"));

use BLL\bDefect;
use Library\Helper;

$obj_defect = new bDefect();
$helper = new Helper();

$message = "";

$defect_id = isset($_GET['id']) ? $_GET['id'] : 0;
$defect = null;

if ($helper->IsManager()) {
    $defect = $obj_defect->GetByDefectId($defect_id);

    if (isset($_POST['defectcode']) && isset($_POST['defecttitle']) && isset($_POST['defecttitleshort'])
        && isset($_POST['defectmultiple']) && isset($_POST['defectprobabilityshutdown']) && isset($_POST['defectlep'])
        && isset($_POST['defecttypeplace']) && isset($_POST['defectvoltagelevel']) && isset($_POST['defectvoltagerange'])
    ) {
        $defectcode = $_POST['defectcode'];
        $defecttitle = $_POST['defecttitle'];
        $defecttitleshort = $_POST['defecttitleshort'];
        $defectmultiple = $_POST['defectmultiple'];
        $defectprobabilityshutdown = $_POST['defectprobabilityshutdown'];
        $defectlep = $_POST['defectlep'];
        $defecttypeplace = $_POST['defecttypeplace'];
        $defectvoltagelevel = $_POST['defectvoltagelevel'];
        $defectvoltagerange = $_POST['defectvoltagerange'];

        if (empty($defectcode)) {
            $message = $helper->GetErrorMessage('Введіть "Код".');
        } elseif (empty($defecttitle)) {
            $message = $helper->GetErrorMessage('Введіть "Назва дефекта".');
        } elseif (empty($defecttitleshort)) {
            $message = $helper->GetErrorMessage('Введіть "Коротка назва дефекта".');
        } else {

            if (strpos($defecttypeplace, 'ПС') > -1) {
                $type_place_short = 'ТП';
            } else {
                $type_place_short = mb_substr($defecttypeplace, 0, 1, 'UTF-8');
            }

            $voltage_level_full = $helper->GetVoltageLevel($defectvoltagelevel);
            $codefull = $defectcode . '_' . $type_place_short . '_' . $voltage_level_full;
            $code_voltage_range = $defectcode . '_' . $type_place_short . '_' . $defectvoltagerange;
            $type_place_id = $helper->GetTypePlaceID($defecttypeplace);

            $res = $obj_defect->UpdateDefect($defect_id, $defecttitle, $defecttitleshort, $defectcode,
                $defectprobabilityshutdown, $defectlep, $defecttypeplace, $type_place_short, $codefull,
                $code_voltage_range, $defectvoltagerange, $defectvoltagelevel, $voltage_level_full, $type_place_id);

            if ($res === true) {
                $message = $helper->GetInfoMessage('Дані успішно збережені.');

                $defect = $obj_defect->GetByDefectId($defect_id);
            }
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

    <div class="colMain" id="mainContent">

        <h3 class="main_title">Редагування дефекта</h3>

        <?php if (!$helper->IsManager()): ?>
            <?php echo $helper->GetErrorMessage('Доступ тільки для адміністратора.'); ?>
        <?php else: ?>

            <?php if ($defect === null): ?>
                <?php echo $helper->GetErrorMessage('Такого дефекта не існує.'); ?>
            <?php else: ?>

                <script type="text/javascript">

                    $(document)
                        .ready(function () {

                            $("#defectmultiple").val('<?php echo $defect["multiple"] == 'true' ? '1' : '0'; ?>');
                            $("#defectprobabilityshutdown").val('<?php echo $defect["probability_shutdown"]; ?>');
                            $("#defectlep").val('<?php echo $defect["lep"]; ?>');
                            $("#defecttypeplace").val('<?php echo $defect["type_place"]; ?>');
                            $("#defectvoltagelevel").val('<?php echo $defect["voltage_level"]; ?>');
                            $("#defectvoltagerange").val('<?php echo $defect["voltage_range"]; ?>');

                            CreateFullCode();

                            $("#defectcode").keyup(function (e) {
                                CreateFullCode();
                            });

                            $("#defecttypeplace").change(function (e) {
                                CreateFullCode();
                            });

                            $("#defectvoltagelevel").change(function (e) {
                                CreateFullCode();
                            });

                            $("#btndefect").click(function (e) {

                                var err = [];

                                if ($('#defectcode').val().trim().length == 0)
                                    err.push(" - Код");

                                if ($('#defecttitle').val().trim().length == 0) {
                                    err.push(" - Назва дефекта");
                                }

                                if ($('#defecttitleshort').val().trim().length == 0) {
                                    err.push(" - Коротка назва дефекта");
                                }

                                if (err.length != 0) {
                                    alert("Будь-ласка, заповніть наступні дані: \r\n" + err.join("\r\n"));
                                    return false;
                                }

                                return true;

                            });

                            function CreateFullCode() {

                                $("#fullcode").html('');

                                if ($("#defectcode").val() != '') {

                                    var typePlace = 'ТП';

                                    if ($("#defecttypeplace").val().indexOf('ПС') == -1) {
                                        typePlace = $("#defecttypeplace").val().charAt(0);
                                    }

                                    var str = $("#defectcode").val() + '_' + typePlace + '_' + getVoltageLevel($("#defectvoltagelevel").val());
                                    $("#fullcode").html(str);
                                }
                            }

                        });

                </script>

            <?php echo $message ?>

                <form method="post" action="edit.php?id=<?php echo $defect_id; ?>">

                    <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">
                        <tbody>
                        <tr>
                            <td style="width: 150px;">Код:</td>
                            <td>
                                <input type="text" maxlength="10" style="width: 200px;" id="defectcode"
                                       name="defectcode" value="<?php echo $defect["code"]; ?>">&nbsp;
                                <label id="fullcode" name="fullcode"></label>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;vertical-align: top;">Назва дефекта:</td>
                            <td>
                                    <textarea style="width: 500px;" maxlength="200" id="defecttitle" cols="20"
                                              rows="3" name="defecttitle"><?php echo $defect["title"]; ?></textarea>

                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;vertical-align: top;">Коротка назва дефекта:</td>
                            <td>
                                    <textarea style="width: 500px;" maxlength="200" id="defecttitleshort"
                                              cols="20"
                                              rows="3" name="defecttitleshort"><?php echo $defect["title_short"]; ?></textarea>

                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">Множинний дефект:</td>
                            <td>
                                <select id="defectmultiple" name="defectmultiple" class="select2" style="width:100px;">
                                    <option value="1">Так</option>
                                    <option value="0">Ні</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">Ймовірність відключення:</td>
                            <td>
                                <select id="defectprobabilityshutdown" name="defectprobabilityshutdown" class="select2"
                                        style="width:100px;">
                                    <option value="0.10">0.10</option>
                                    <option value="0.20">0.20</option>
                                    <option value="0.30">0.30</option>
                                    <option value="0.40">0.40</option>
                                    <option value="0.50">0.50</option>
                                    <option value="0.60">0.60</option>
                                    <option value="0.70">0.70</option>
                                    <option value="0.80">0.80</option>
                                    <option value="0.90">0.90</option>
                                    <option value="1.00">1.00</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">ЛЕП/ТП:</td>
                            <td>
                                <select id="defectlep" name="defectlep" class="select2" style="width:100px;">
                                    <option value="ЛЕП">ЛЕП</option>
                                    <option value="ПС/ТП">ПС/ТП</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">Тип тех. місця:</td>
                            <td>
                                <select id="defecttypeplace" name="defecttypeplace" class="select2"
                                        style="width:150px;">
                                    <option value="Опора">Опора</option>
                                    <option value="ЛЕП">ЛЕП</option>
                                    <option value="ПС/ТП">ПС/ТП</option>
                                    <option value="Відгалуження">Відгалуження</option>
                                    <option value="Проліт">Проліт</option>
                                    <option value="Секція">Секція</option>
                                    <option value="Комірка">Комірка</option>
                                    <option value="Трансформатор">Трансформатор</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">Рівень напруги, кВ:</td>
                            <td>
                                <select id="defectvoltagelevel" name="defectvoltagelevel" class="select2"
                                        style="width:100px;">
                                    <option value="0.22">0.22</option>
                                    <option value="0.4">0.4</option>
                                    <option value="6">6</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">Діапазон напруги:</td>
                            <td>
                                <select id="defectvoltagerange" name="defectvoltagerange" class="select2"
                                        style="width:100px;">
                                    <option value="A">A</option>
                                    <option value="N">N</option>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <br>

                    <input type="submit" name="btndefect" id="btndefect" value="Зберегти">

                </form>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

<?php include('../content/footer.php'); ?>

</body>
</html>
