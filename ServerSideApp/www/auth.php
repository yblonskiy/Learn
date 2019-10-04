<?php

error_reporting(0);

//session_save_path("tmp");
session_start();

require_once(realpath(dirname(__FILE__) . "/../bll/user.php"));

use BLL\bUser;

$user = new bUser();

//Если пользователь уже аутентифицирован, то перебросить его на страницу home.php
if (isset($_SESSION['user'])) {
    header('Location: home.php');
    exit;
}

//Если пользователь не аутентифицирован, то проверить его
if (isset($_POST['login']) && isset($_POST['password']))
{
    $obj_user = $user->GetUser($_POST['login'], md5($_POST['password']));

    if (!is_null($obj_user)) {
        $_SESSION['user'] = $obj_user;
        header('Location: home.php');
        exit;

    } else {
        die('<b>На жаль, Вам доступ заборонено.</b></br>Для переходу на сторінку авторизації натисніть <a href="login.php">сюди</a>');
    }
}

?>