<?php

error_reporting(0);

include('../extra.php');
include('../checks.php');

require_once(realpath(dirname(__FILE__) . "/../../bll/defect.php"));
require_once(realpath(dirname(__FILE__) . "/../../lib/helper.php"));

use BLL\bDefect;
use Library\Helper;

$obj_defect = new bDefect();
$helper = new Helper();

$message = "";

$defect_id = isset($_GET['id']) ? $_GET['id'] : 0;
$defect = null;

if ($helper->IsManager()) {
    $defect = $obj_defect->GetByDefectId($defect_id);

    if (isset($_POST['defectid'])) {

        $defect_id = $_POST['defectid'];

        $res = $obj_defect->UpdateActive($defect_id, false);

        if ($res == true) {
            $_SESSION['defect_deleted'] = 1;
            header('Location: list.php');
            exit;
        }
        else
        {
            $message = $helper->GetErrorMessage('Помилка при видаленні дефекта.');
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

        <h3 class="main_title">Видалення дефекта</h3>

        <?php if (!$helper->IsManager()): ?>
            <?php echo $helper->GetErrorMessage('Доступ тільки для адміністратора.'); ?>
        <?php else: ?>

            <?php if ($defect === null): ?>
                <?php echo $helper->GetErrorMessage('Такого дефекта не існує.'); ?>
            <?php else: ?>

                <?php echo $message ?>

                <form method="post" action="delete.php">

                    <input type="hidden" value="<?php echo $defect_id; ?>" name="defectid"/>

                    <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">
                        <tbody>
                        <tr>
                            <td style="width: 150px;vertical-align: top;">Назва дефекта:</td>
                            <td class="lbl_text">
                                <?php echo $defect['title']; ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <br>

                    <input type="submit" name="btnuser" id="btnuser" value="Видалити">

                </form>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

<?php include('../content/footer.php'); ?>

</body>
</html>
