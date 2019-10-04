<?php

//session_save_path("tmp");
session_start();

if (!isset($_SESSION['user'])) {
    die('<b>Ви повинні пройти авторизацію!</b><br/>Для переходу на сторінку авторизації натисніть <a href="'.$GLOBALS["BASE_URL"].'www/login.php">сюди</a>');
}

?>
