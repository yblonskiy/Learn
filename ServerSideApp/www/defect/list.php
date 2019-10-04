<?php

error_reporting(0);

include('../extra.php');
include('../checks.php');

require_once(realpath(dirname(__FILE__) . "/../../bll/defect.php"));
require_once(realpath(dirname(__FILE__) . "/../../lib/helper.php"));

use BLL\bDefect;
use Library\Helper;

$defect = new bDefect();
$helper = new Helper();

$message = "";

if (isset($_SESSION['defect_created'])) {
    unset($_SESSION['defect_created']);

    $message = $helper->GetInfoMessage('Дефект успішно створенно.');
}

if (isset($_SESSION['defect_deleted'])) {
    unset($_SESSION['defect_deleted']);

    $message = $helper->GetInfoMessage('Дефект успішно видаленно.');
}

$page_number = isset($_GET['id']) ? $_GET['id'] : 1;
$list = null;

if ($helper->IsManager()) {
    $list = $defect->GetByPaging($_page_size, $page_number);
}

?>

<!DOCTYPE html>

<?php include('../content/head.php'); ?>

<body>

<div id="wrapper">

    <?php include('../content/header.php'); ?>

    <?php include('../content/menu.php'); ?>

    <div class="colMain" id="mainContent">

        <h3 class="main_title">Список дефектів</h3>

        <?php if (!$helper->IsManager()): ?>
            <?php echo $helper->GetErrorMessage('Доступ тільки для адміністратора.'); ?>
        <?php else: ?>

            <?php if ($list == null || $list['total'] == 0): ?>
                <?php echo $helper->GetInfoMessage('Список пустий.'); ?>
            <?php else: ?>

                <?php echo $message ?>

                <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">
                    <tbody>
                    <tr>
                        <th width="130" height="25" nowrap="nowrap" align="left" class="thCornerL">&nbsp;Код дефекту&nbsp;</th>
                        <th nowrap="nowrap" align="center" class="thTop">&nbsp;Назва дефекту&nbsp;</th>
                        <th width="80" nowrap="nowrap" align="center" class="thTop">&nbsp;Тип тех. місця&nbsp;</th>
                        <th width="80" nowrap="nowrap" align="center" class="thTop">&nbsp;Рівень напруги, кВ&nbsp;
                        </th>
                        <th width="80" nowrap="nowrap" align="center" class="thCornerR"></th>
                    </tr>

                    <?php

                    for ($i = 0; $i < count($list); $i++) {
                        if (is_null($list[$i]) === false) {
                            if ($list[$i]["code_full"] != "0") {
                                echo '<tr class="' . ($i % 2 ? 'col2' : 'col1') . '">';
                                echo '<td style="width:130px;"><span class="topictitle"><a href="edit.php?id=' . $list[$i]["id"] . '">' . $list[$i]["code_full"] . '</a></span></td>';
                                echo '<td><span class="postdetails">' . $list[$i]["title"] . '</span></td>';
                                echo '<td style="width:80px; text-align:center;"><span class="postdetails">' . $list[$i]['type_place'] . '</span></td>';
                                echo '<td style="width:80px; text-align:center;"><span class="postdetails">' . $list[$i]['voltage_level'] . '</span></td>';

                                echo '<td style="width:80px; text-align:center;"><span class="topictitle">';
                                echo '<a href="delete.php?id=' . $list[$i]["id"] . '">Видалити</a>';
                                echo '</span></td>';
                                echo '</tr>';
                            }
                        }
                    }

                    ?>

                    </tbody>
                </table>

                <?php if ($list['total'] > $_page_size): ?>

                    <table width="100%" cellspacing="2" cellpadding="2" border="0" align="center">
                        <tbody>
                        <tr>
                            <td align="right"><span
                                        class="nav">Сторінка <b><?php echo $page_number ?></b> з <b><?php echo ceil($list['total'] / $_page_size); ?></b></span>
                            </td>
                        </tr>
                        <tr>
                            <td valign="middle" nowrap="nowrap" align="right">
                   <span class="nav">
                       <?php echo $helper->GetPaging($list['total'], $page_number, $_page_size, $GLOBALS["BASE_URL"] . 'www/defect/list.php'); ?>
                   </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                <?php endif; ?>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

<?php include('../content/footer.php'); ?>

</body>
</html>
