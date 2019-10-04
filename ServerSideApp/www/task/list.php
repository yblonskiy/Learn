<?php

error_reporting(0);

include('../extra.php');
include('../checks.php');

require_once(realpath(dirname(__FILE__) . "/../../bll/vw_tasks.php"));
require_once(realpath(dirname(__FILE__) . "/../../lib/helper.php"));

use BLL\vwTasks;
use Library\Helper;

$obj_vw_task = new vwTasks();
$helper = new Helper();

$message = "";

if (isset($_SESSION['task_created'])) {
    unset($_SESSION['task_created']);

    $message = $helper->GetInfoMessage('Огляд успішно створенно.');
}

if (isset($_SESSION['task_deleted'])) {
    unset($_SESSION['task_deleted']);

    $message = $helper->GetInfoMessage('Огляд успішно видаленно.');
}

$page_number = isset($_GET['id']) ? $_GET['id'] : 1;
$list = null;

if ($helper->IsManager()) {
    $list = $obj_vw_task->GetWithPaging($_page_size, $page_number);
} else {
    $list = $obj_vw_task->GetWithPagingByUserId($_SESSION['user']['id'], $_page_size, $page_number);
}

$is_show_remove = false;

if ($list['total'] > 0) {
    for ($i = 0; $i < count($list); $i++) {
        if (is_null($list[$i]) === false) {
            if ($list[$i]['task_status_id'] == 3) {
                $is_show_remove = true;
                break;
            }
        }
    }
}

?>

<!DOCTYPE html>

<?php include('../content/head.php'); ?>

<body>

<div id="wrapper">

    <?php include('../content/header.php'); ?>

    <?php include('../content/menu.php'); ?>

    <div class="colMain" id="mainContent">

        <h3 class="main_title">Список оглядів</h3>

        <?php if ($list == null || $list['total'] == 0): ?>
            <?php echo $helper->GetInfoMessage('Список пустий.'); ?>
        <?php else: ?>

            <?php echo $message ?>

            <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">
                <tbody>
                <tr>
                    <th height="25" nowrap="nowrap" align="left" class="thCornerL">&nbsp;Назва огляду&nbsp;</th>
                    <th width="110" nowrap="nowrap" align="left" class="thTop">&nbsp;Експорт в Excel&nbsp;</th>
                    <th width="200" nowrap="nowrap" align="center" class="thTop">&nbsp;Назва об'єкта&nbsp;</th>
                    <th width="200" nowrap="nowrap" align="center" class="thTop">&nbsp;РЕМ&nbsp;</th>
                    <th width="200" nowrap="nowrap" align="center" class="thTop">&nbsp;Виконавець&nbsp;</th>
                    <th width="90" nowrap="nowrap" align="center" class="thTop">&nbsp;Дата створення&nbsp;</th>
                    <th width="90" nowrap="nowrap" align="center" class="thTop">&nbsp;Стан&nbsp;</th>

                    <?php if ($is_show_remove): ?>
                        <th width="90" nowrap="nowrap" align="center" class="thTop">&nbsp;Дата виконання&nbsp;</th>
                        <th width="80" nowrap="nowrap" align="center" class="thCornerR"></th>
                    <?php else: ?>
                        <th width="90" nowrap="nowrap" align="center" class="thCornerR">&nbsp;Дата виконання&nbsp;</th>
                    <?php endif; ?>

                </tr>

                <?php

                for ($i = 0; $i < count($list); $i++) {
                    if (is_null($list[$i]) === false) {
                        $user_name = $list[$i]['last_name'] . ' ' . mb_substr($list[$i]['first_name'], 0, 1) . '.' . mb_substr($list[$i]['patronymic'], 0, 1) . '.';

                        echo '<tr class="' . ($i % 2 ? 'col2' : 'col1') . '">';
                        echo '<td><span class="topictitle"><a href="show.php?id=' . $list[$i]["task_id"] . '">' . $list[$i]["task_name"] . '</a></span></td>';

                        if ($list[$i]['task_status_id'] == 2) {
                            echo '<td style="width:110px; text-align:center;"><span class="topictitle">';
                            echo '<a onclick="self.location.href = \'export04.php?id=' . $list[$i]["task_id"] . '\'; return false;" href="#">Експортувати</a>';
                            echo '</span></td>';
                        }
                        else
                        {
                            echo '<td style="width:110px;"></td>';
                        }

                        echo '<td><span class="topictitle"><a href="show.php?id=' . $list[$i]["task_id"] . '">' . $list[$i]["tp"] . " " . $list[$i]["linename"] . '</a></span></td>';

                        echo '<td><span class="postdetails">' . str_replace(" РЕМ", "", $helper->GetRemName($list[$i]["rem"])) . '</span></td>';
                        echo '<td><span class="postdetails">' . $user_name . '</span></td>';
                        echo '<td style="width:90px; text-align:center;"><span class="postdetails">' . (new DateTime($list[$i]["date_created"]))->format('d-m-Y H:i:s') . '</span></td>';
                        echo '<td style="width:90px; text-align:center;"><span class="postdetails">' . $list[$i]['status_name'] . '</span></td>';

                        echo '<td style="width:90px; text-align:center;">';
                        if ($list[$i]['task_status_id'] == 2) {
                            echo '<span class="postdetails">' . (new DateTime($list[$i]["date_status_updated"]))->format('d-m-Y H:i:s') . '</span>';
                        }
                        echo '</td>';

                        if ($is_show_remove) {

                            if ($helper->IsManager()) {
                                echo '<td style="width:80px; text-align:center;"><span class="topictitle">';
                                echo '<a href="delete.php?id=' . $list[$i]["task_id"] . '">Видалити</a>';
                                echo '</span></td>';
                            } elseif ($list[$i]['task_status_id'] == 3) {
                                echo '<td style="width:80px; text-align:center;"><span class="topictitle">';
                                echo '<a href="delete.php?id=' . $list[$i]["task_id"] . '">Видалити</a>';
                                echo '</span></td>';
                            } else {
                                echo '<td style="width:80px;"></td>';
                            }
                        }

                        echo '</tr>';
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
                       <?php echo $helper->GetPaging($list['total'], $page_number, $_page_size, $GLOBALS["BASE_URL"] . 'www/task/list.php'); ?>
                   </span>
                        </td>
                    </tr>
                    </tbody>
                </table>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

<?php include('../content/footer.php'); ?>

</body>
</html>
