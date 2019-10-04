<?php

error_reporting(0);

include('../extra.php');
include('../checks.php');

require_once(realpath(dirname(__FILE__) . "/../../lib/database.php"));
require_once(realpath(dirname(__FILE__) . "/../../lib/helper.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/defect.php"));
require_once(realpath(dirname(__FILE__) . "/../../excel/simplexlsx.class.php"));

use Library\Database;
use BLL\bDefect;
use Library\Helper;


$obj_defect = new bDefect();
$obj_helper = new Helper();

if (!isset($_GET['nottest']))
{
    return;
}

//return;


$message = "";

$file_path = 'defects.xlsx';

/*

row[0] - code_full
row[1] - title
row[2] - probability_shutdown
row[3] - type_place
row[4] - voltage_level
row[5] - in_journal
row[6] - lep
row[7] - title_short
row[8] - multiple
row[9] - code
row[10] - *ignore this field
row[12] - *ignore this field


 */

if ($xlsx = SimpleXLSX::parse($file_path)) {

    $rows = $xlsx->rows();

    if (count($rows) > 2) {

        $db = new Database();

        try {
            $db->Open();
            $db->beginTransaction();

            $count = 0;

            for ($i = 1; $i < count($rows); $i++) {

                // title
                $title = str_replace("'", '"', $rows[$i][1]);

                // title_short
                $title_short = str_replace("'", '"', $rows[$i][7]);

                // code
                $code = $rows[$i][9];

                // multiple
                $multiple_str = $rows[$i][8];
                $multiple_str = mb_strtolower($multiple_str, 'UTF-8');
                $multiple = (mb_strpos($multiple_str, 'да') > -1 || mb_strpos($multiple_str, 'так') > -1) ? true : false;

                // probability_shutdown
                $probabilityshutdown = $rows[$i][2];
                $probabilityshutdown = str_replace(",", '.', $probabilityshutdown);
                $probabilityshutdown = number_format($probabilityshutdown, 2);

                // lep
                $lep = $rows[$i][6];
                $lep = mb_strtolower($lep, 'UTF-8');

                if (mb_strpos($lep, 'лэп') > -1 || mb_strpos($lep, 'леп') > -1) {
                    $lep = 'ЛЕП';
                } else {
                    $lep = 'ПС/ТП';
                }

                // type_place
                $typeplace = $obj_helper->ParseTypePlace($rows[$i][3]);

                // type_place_short
                if (strpos($typeplace, 'ПС/') > -1) {
                    $type_place_short = 'ТП';
                } else {
                    $type_place_short = mb_substr($typeplace, 0, 1, 'UTF-8');
                }

                // in_journal
                $in_journal_str = $rows[$i][5];
                $in_journal_str = mb_strtolower($in_journal_str, 'UTF-8');
                $in_journal = (mb_strpos($in_journal_str, 'да') > -1 || mb_strpos($in_journal_str, 'так') > -1) ? true : false;

                // code_full
                $codefull = $rows[$i][0];

                // code_voltage_range
                $code_voltage_range = $code . '_' . $type_place_short . 'A';

                // voltage_range
                $voltagerange = 'A';

                // voltage_level
                $voltagelevel = $rows[$i][4];
                $voltagelevel = str_replace(",", '.', $voltagelevel);

                // voltage_level_full
                $voltage_level_full = $obj_helper->GetVoltageLevel($voltagelevel);

                // type_place_id
                $type_place_id = $obj_helper->GetTypePlaceID($typeplace);

                if ($obj_defect->IsCodeFullExistsTransaction($db, $codefull))
                {
                    continue;
                }

                $defect_id = $obj_defect->AddDefectTransaction($db, $title, $title_short, $code, $multiple,
                    $probabilityshutdown, $lep, $typeplace, $type_place_short, $in_journal, $codefull,
                    $code_voltage_range, $voltagerange, $voltagelevel, $voltage_level_full, $type_place_id, true);

                $count = $count + 1;
            }

            $db->commitTransaction();

            echo 'imported';

        } catch (\Exception $e) {
            echo 'ERROR imported' . $e;
            $db->rollbackTransaction();
            $res = false;
        } finally {
            $db->Close();
        }
    }

    echo '<br><br>Count=' . $count;

} else {
    echo SimpleXLSX::parse_error();
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

        <h3 class="main_title">Імпорт дефектів</h3>

    </div>

</div>

<?php include('../content/footer.php'); ?>

</body>
</html>
