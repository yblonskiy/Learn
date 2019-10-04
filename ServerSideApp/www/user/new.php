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

if (isset($_POST['userlogin']) && isset($_POST['userpassword']) && isset($_POST['userpasswordconfirm'])
    && isset($_POST['userlastname']) && isset($_POST['userfirstname']) && isset($_POST['useremail']) && isset($_POST['usertype'])
    && isset($_POST['userpatronymic']) && isset($_POST['userpostname'])
) {
    $userlogin = $_POST['userlogin'];
    $userpassword = $_POST['userpassword'];
    $userpasswordconfirm = $_POST['userpasswordconfirm'];
    $userlastname = $_POST['userlastname'];
    $userfirstname = $_POST['userfirstname'];
    $useremail = $_POST['useremail'];
    $usertype = $_POST['usertype'];
    $userpatronymic = $_POST['userpatronymic'];
    $userpostname = $_POST['userpostname'];

    if (empty($userlogin)) {
        $message = $helper->GetErrorMessage('Введіть "Логін".');
    } elseif (empty($userpassword)) {
        $message = $helper->GetErrorMessage('Введіть "Пароль".');
    } elseif (empty($userlastname)) {
        $message = $helper->GetErrorMessage('Введіть "Прізвище".');
    } elseif (empty($userfirstname)) {
        $message = $helper->GetErrorMessage('Введіть "Ім\'я".');
    } elseif (empty($userpatronymic)) {
        $message = $helper->GetErrorMessage('Введіть "По-батькові".');
    } elseif (empty($userpostname)) {
        $message = $helper->GetErrorMessage('Введіть "Посада".');
    } elseif (empty($useremail)) {
        $message = $helper->GetErrorMessage('Введіть "Email".');
    } elseif (empty($usertype)) {
        $message = $helper->GetErrorMessage('Оберіть "Тип користувача".');
    } elseif (strtolower($userpassword) !== strtolower($userpasswordconfirm)) {
        $message = $helper->GetErrorMessage('Пароль не співпадає. Будь-ласка, введіть ще раз');
    } elseif ($user->IsLoginExists($userlogin)) {
        $message = $helper->GetErrorMessage('Користувач з таким логіном вже існує.');
    } else {

        $userpassword = md5($userpassword);

        $user_id = $user->AddUser($userlogin, $userpassword, $userfirstname, $userlastname, $useremail, $usertype, true, $userpatronymic, $userpostname);

        if ($user_id > 0) {

            $_SESSION['user_created'] = 1;
            header('Location: list.php');
            exit;
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

        <h3 class="main_title">Новий користувач</h3>

        <?php if (!$helper->IsManager()): ?>
            <?php echo $helper->GetErrorMessage('Доступ тільки для адміністратора.'); ?>
        <?php else: ?>

            <script type="text/javascript">

                $(document)
                    .ready(function () {

                        <?php if (isset($_POST['usertype'])) : ?>

                        $('#usertype').val('<?php echo $_POST['usertype']; ?>');

                        <?php endif; ?>

                        $("#btnuser").click(function (e) {

                            var err = [];

                            if ($('#userlogin').val().trim().length == 0)
                                err.push(" - Логін");

                            if ($('#userpassword').val().trim().length == 0) {
                                err.push(" - Пароль");
                            }

                            if ($('#userlastname').val().trim().length == 0) {
                                err.push(" - Прізвище");
                            }

                            if ($('#userfirstname').val().trim().length == 0) {
                                err.push(" - Ім'я");
                            }

                            if ($('#userpatronymic').val().trim().length == 0) {
                                err.push(" - По-батькові");
                            }

                            if ($('#userpostname').val().trim().length == 0) {
                                err.push(" - Посада");
                            }

                            if ($('#useremail').val().trim().length == 0) {
                                err.push(" - Email");
                            }

                            if ($('#usertype').val() == "0") {
                                err.push(" - Тип користувача");
                            }

                            if (err.length != 0) {
                                alert("Будь-ласка, заповніть наступні дані: \r\n" + err.join("\r\n"));
                                return false;
                            }

                            if (!IsValidLogin($('#userlogin').val())) {
                                alert("Будь-ласка, введіть коректно 'Логін' \r\n (доступні латинські літери, цифри та знаки '-', '_').");
                                return false;
                            }

                            if (!CheckPassword('userpassword', 'userpasswordconfirm')) {
                                return false;
                            }

                            if ($('#useremail').val().trim().length != 0 && !CheckupEmail($('#useremail').val())) {
                                alert("Будь-ласка, введіть коректно 'Email'.");
                                return false;
                            }

                            return true;

                        });

                    });

            </script>

        <?php echo $message ?>

            <form method="post" action="new.php">

                <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">
                    <tbody>
                    <tr>
                        <td style="width: 150px;">Логін:</td>
                        <td>
                            <input type="text" maxlength="30" style="width: 200px;" id="userlogin" name="userlogin"
                                   value="<?php if (isset($_POST['userlogin'])) {
                                       echo $_POST['userlogin'];
                                   } ?>">
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
                    <tr>
                        <td style="width: 150px;">Прізвище:</td>
                        <td>
                            <input type="text" maxlength="100" style="width: 400px;" id="userlastname"
                                   name="userlastname"
                                   value="<?php if (isset($_POST['userlastname'])) {
                                       echo $_POST['userlastname'];
                                   } ?>">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">Ім'я:</td>
                        <td>
                            <input type="text" maxlength="100" style="width: 400px;" id="userfirstname"
                                   name="userfirstname" value="<?php if (isset($_POST['userfirstname'])) {
                                echo $_POST['userfirstname'];
                            } ?>">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">По-батькові:</td>
                        <td>
                            <input type="text" maxlength="100" style="width: 400px;" id="userpatronymic"
                                   name="userpatronymic" value="<?php if (isset($_POST['userpatronymic'])) {
                                echo $_POST['userpatronymic'];
                            } ?>">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">Посада:</td>
                        <td>
                            <input type="text" maxlength="150" style="width: 400px;" id="userpostname"
                                   name="userpostname" value="<?php if (isset($_POST['userpostname'])) {
                                echo $_POST['userpostname'];
                            } ?>">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">Email:</td>
                        <td>
                            <input type="text" maxlength="150" style="width: 400px;" id="useremail" name="useremail"
                                   value="<?php if (isset($_POST['useremail'])) {
                                       echo $_POST['useremail'];
                                   } ?>">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 150px;vertical-align: top;">Тип користувача:</td>
                        <td>
                            <select id="usertype" name="usertype" class="select2" style="width:300px;">
                                <option value="0">Не обрано...</option>

                                <?php

                                $types = $user->GetUserTypes();

                                for ($i = 0; $i < count($types); $i++) {
                                    echo '<option value="' . $types[$i]['id'] . '">' . $types[$i]['name'] . '</option>';
                                }

                                ?>

                            </select>
                        </td>
                    </tr>

                    </tbody>
                </table>

                <br>

                <input type="submit" name="btnuser" id="btnuser" value="Створити">

            </form>

        <?php endif; ?>

    </div>

</div>

<?php include('../content/footer.php'); ?>

</body>
</html>
