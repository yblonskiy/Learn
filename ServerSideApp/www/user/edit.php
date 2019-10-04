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

if (isset($_SESSION['user_passsword'])) {
    unset($_SESSION['user_passsword']);

    $message = $helper->GetInfoMessage('Пароль успішно змінено.');
}

$user_id = isset($_GET['id']) ? $_GET['id'] : 0;

$list = $user->GetUserByID($user_id);

if (isset($_POST['userlastname']) && isset($_POST['userfirstname']) && isset($_POST['useremail'])
    && isset($_POST['usertype']) && isset($_POST['userpatronymic']) && isset($_POST['userpostname'])
) {
    $userlastname = $_POST['userlastname'];
    $userfirstname = $_POST['userfirstname'];
    $useremail = $_POST['useremail'];
    $usertype = $_POST['usertype'];
    $userpatronymic = $_POST['userpatronymic'];
    $userpostname = $_POST['userpostname'];

    if (empty($userlastname)) {
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
    } else {

        $res = $user->UpdateUser($user_id, $list["login"], $userfirstname, $userlastname, $useremail, $usertype, true, $userpatronymic, $userpostname);

        if ($res === true) {
            $message = $helper->GetInfoMessage('Дані успішно збережені.');

            $list = $user->GetUserByID($user_id);
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

        <h3 class="main_title">Редагування користувача</h3>

        <?php if (!$helper->IsManager()): ?>
            <?php echo $helper->GetErrorMessage('Доступ тільки для адміністратора.'); ?>
        <?php else: ?>

            <?php if (empty($list["id"])): ?>
                <?php echo $helper->GetErrorMessage('Такого користувача не існує.'); ?>
            <?php else: ?>

                <script type="text/javascript">

                    $(document)
                        .ready(function () {

                            $("#btnuser").click(function (e) {

                                var err = [];

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

                                if ($('#useremail').val().trim().length != 0 && !CheckupEmail($('#useremail').val())) {
                                    alert("Будь-ласка, введіть коректно 'Email'.");
                                    return false;
                                }

                                return true;

                            });

                        });

                </script>

            <?php echo $message ?>


                <table width="100%" cellspacing="1" cellpadding="4" border="0" style="border: 0px;">
                    <tbody>
                    <tr>
                        <td style="text-align:right;"><a href="newpass.php?id=<?php echo $user_id; ?>">Змінити пароль</a></td>
                    </tr>
                    </tbody>
                </table>

                <form method="post" action="edit.php?id=<?php echo $user_id; ?>">

                    <table width="100%" cellspacing="1" cellpadding="4" border="0" class="forumline">
                        <tbody>
                        <tr>
                            <td style="width: 150px;">Логін:</td>
                            <td>
                                <?php echo $list["login"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">Прізвище:</td>
                            <td>
                                <input type="text" maxlength="100" style="width: 400px;" id="userlastname"
                                       name="userlastname"
                                       value="<?php echo $list["last_name"]; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">Ім'я:</td>
                            <td>
                                <input type="text" maxlength="100" style="width: 400px;" id="userfirstname"
                                       name="userfirstname" value="<?php echo $list["first_name"]; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">По-батькові:</td>
                            <td>
                                <input type="text" maxlength="100" style="width: 400px;" id="userpatronymic"
                                       name="userpatronymic" value="<?php echo $list['patronymic']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">Посада:</td>
                            <td>
                                <input type="text" maxlength="150" style="width: 400px;" id="userpostname"
                                       name="userpostname" value="<?php echo $list['post_name']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px;">Email:</td>
                            <td>
                                <input type="text" maxlength="150" style="width: 400px;" id="useremail" name="useremail"
                                       value="<?php echo $list["email"]; ?>">
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
                                        echo '<option ' . ($list["user_types_id"] === $types[$i]['id'] ? ' selected="selected" ' : '') . ' value="' . $types[$i]['id'] . '">' . $types[$i]['name'] . '</option>';
                                    }

                                    ?>

                                </select>
                            </td>
                        </tr>

                        </tbody>
                    </table>

                    <br>

                    <input type="submit" name="btnuser" id="btnuser" value="Зберегти">

                </form>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

<?php include('../content/footer.php'); ?>

</body>
</html>
