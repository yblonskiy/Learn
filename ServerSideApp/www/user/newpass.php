<?php

error_reporting(0);

include('../extra.php');
include('../checks.php');

require_once(realpath(dirname(__FILE__) . "/../../lib/helper.php"));
require_once(realpath(dirname(__FILE__) . "/../../bll/user.php"));

use Library\Helper;
use BLL\bUser;

$helper = new Helper();
$user = new bUser();

$message = "";

$user_id = isset($_GET['id']) ? $_GET['id'] : 0;

if (isset($_POST['userid']) && isset($_POST['userpassword']) && isset($_POST['userpasswordconfirm'])) {

    $user_id = $_POST['userid'];
    $userpassword = $_POST['userpassword'];
    $userpasswordconfirm = $_POST['userpasswordconfirm'];

    if (empty($userpassword)) {
        $message = $helper->GetErrorMessage('Введіть "Пароль".');
    } elseif (strtolower($userpassword) !== strtolower($userpasswordconfirm)) {
        $message = $helper->GetErrorMessage('Пароль не співпадає. Будь-ласка, введіть ще раз');
    } else {

        $userpassword = md5($userpassword);

        $res = $user->EditPassword($user_id, $userpassword);

        if ($res === true) {
            $_SESSION['user_passsword'] = 1;
            header('Location: edit.php?id='.$user_id);
            exit;
        }
    }
}

$list = $user->GetUserByID($user_id);

?>

<!DOCTYPE html>
<html lang="en">

<?php include('../content/head.php'); ?>

<body>

<div id="wrapper">

    <?php include('../content/header.php'); ?>

    <?php include('../content/menu.php'); ?>

    <div class="colMain" id="mainContent">

        <h3 class="main_title">Зміна пароля</h3>

        <?php if (!$helper->IsManager()): ?>
            <?php echo $helper->GetErrorMessage('Доступ тільки для адміністратора.'); ?>
        <?php else: ?>

            <?php if ($user_id == 0): ?>
                <?php echo $helper->GetErrorMessage('Такого користувача не існує.'); ?>
            <?php else: ?>

                <script type="text/javascript">

                    $(document)
                        .ready(function () {

                            $("#btnuser").click(function (e) {

                                var err = [];

                                if ($('#userpassword').val().trim().length == 0) {
                                    err.push(" - Пароль");
                                }

                                if (err.length != 0) {
                                    alert("Будь-ласка, заповніть наступні дані: \r\n" + err.join("\r\n"));
                                    return false;
                                }

                                if (!CheckPassword('userpassword', 'userpasswordconfirm')) {
                                    return false;
                                }

                                return true;
                            });

                        });

                </script>

            <?php echo $message ?>

                <form method="post" action="newpass.php">

                    <input type="hidden" value="<?php echo $user_id; ?>" name="userid"/>

                    <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">
                        <tbody>
                        <tr>
                            <td style="width: 150px;">Логін:</td>
                            <td>
                                <?php echo $list["login"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">Пароль:</td>
                            <td>
                                <input type="password" maxlength="100" style="width: 200px;" id="userpassword"
                                       name="userpassword">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">Ще раз пароль:</td>
                            <td>
                                <input type="password" maxlength="100" style="width: 200px;" id="userpasswordconfirm"
                                       name="userpasswordconfirm">
                            </td>
                        </tr>

                        </tbody>
                    </table>

                    <br>

                    <input type="submit" name="btnuser" id="btnuser" value="Змінити">

                </form>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

<?php include('../content/footer.php'); ?>

</body>
</html>
