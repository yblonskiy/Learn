<?php

error_reporting(0);

include('../extra.php');
include('../checks.php');

require_once(realpath(dirname(__FILE__) . "/../../bll/task.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/vw_tasks.php"));
require_once(realpath(dirname(__FILE__) . "/../../lib/helper.php"));

use BLL\bTasks;
use BLL\vwTasks;
use Library\Helper;

$obj_task = new bTasks();
$obj_vw_task = new vwTasks();
$helper = new Helper();

$message = "";

$task_id = isset($_GET['id']) ? $_GET['id'] : 0;
$task = null;

if ($helper->IsManager()) {
    $task = $obj_vw_task->GetByTaskId($task_id);
} else {
    $task = $obj_vw_task->GetByTaskIdAndUserId($task_id, $_SESSION['user']['id']);
}

if (isset($_POST['taskid'])) {
    $task_id = $_POST['taskid'];

    $res = $obj_task->UpdateActive($_POST['taskid'], false);

    if ($res == true) {
        $_SESSION['task_deleted'] = 1;
        header('Location: list.php');
        exit;
    } else {
        $message = $helper->GetErrorMessage('Помилка при видаленні завдання.');
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

        <h3 class="main_title">Видалення огляду</h3>

        <?php echo $message ?>

        <?php if ($task === null): ?>
            <?php echo $helper->GetErrorMessage('Такого огляду не існує або у Вас відсутній доступ.'); ?>
        <?php else: ?>

            <?php if (!$helper->IsManager() && $task['task_status_id'] != 3): ?>
                <?php echo $helper->GetErrorMessage('Неможливо видалити огляд зі статусом "' . $task['status_name'] . '".'); ?>
            <?php endif; ?>

            <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">
                <tbody>
                <tr>
                    <td style="width: 120px;vertical-align: top;">Назва огляду:</td>
                    <td class="lbl_text">
                        <?php echo $task["task_name"]; ?>
                    </td>
                </tr>
                </tbody>
            </table>

            <br>

            <?php if ($helper->IsManager() || $task['task_status_id'] == 3): ?>

                <form method="post" action="delete.php">

                    <input type="hidden" value="<?php echo $task_id; ?>" name="taskid"/>
                    <input type="submit" name="btntask" id="btntask" value="Видалити">

                </form>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

<?php include('../content/footer.php'); ?>

</body>
</html>
