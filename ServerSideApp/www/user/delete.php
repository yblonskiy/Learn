<?php

error_reporting(0);

include('../extra.php');
include('../checks.php');

require_once(realpath(dirname(__FILE__) . "/../../bll/user.php"));
require_once(realpath(dirname(__FILE__) . "/../../lib/helper.php"));

use Library\Helper;
use BLL\bUser;

$helper = new Helper();
$user = new bUser();

$message = "";

$user_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($helper->IsManager()) {
    $list = $user->GetUserByID($user_id);

    if (isset($_POST['userid'])) {

        $user_id = $_POST['userid'];

        $res = $user->UpdateActive($user_id, false);

        if ($res == true) {
            $_SESSION['user_deleted'] = 1;
            header('Location: list.php');
            exit;
        }
        else
        {
            $message = $helper->GetErrorMessage('Помилка при видаленні користувача.');
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

        <h3 class="main_title">Видалення користувача</h3>

        <?php if (!$helper->IsManager()): ?>
            <?php echo $helper->GetErrorMessage('Доступ тільки для адміністратора.'); ?>
        <?php else: ?>

            <?php if (empty($list["id"])): ?>
                <?php echo $helper->GetErrorMessage('Такого користувача не існує.'); ?>
            <?php else: ?>

                <?php echo $message ?>

                <form method="post" action="delete.php">

                    <input type="hidden" value="<?php echo $user_id; ?>" name="userid"/>

                    <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">
                        <tbody>
                        <tr>
                            <td style="width: 150px;vertical-align: top;">Прізвище та Ім'я:</td>
                            <td class="lbl_text">
                                <?php echo $list['last_name'] . ' ' . $list['first_name']; ?>
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
