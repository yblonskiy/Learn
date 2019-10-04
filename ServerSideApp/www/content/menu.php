<?php

require_once(realpath(dirname(__FILE__) . "/../../lib/helper.php"));

use Library\Helper;
$helper = new Helper();

$str = '<div id="nav">
 <ul class="nav navHorz">';

$home_current = '';
$task_current = '';
$defect_current = '';
$user_current = '';

if (strpos($_SERVER['REQUEST_URI'], "/home.php") > 0 )
{
    $home_current = 'class="current"';
}

if (strpos($_SERVER['REQUEST_URI'], "/task/list.php") > 0
    || strpos($_SERVER['REQUEST_URI'], "/task/new.php") > 0
    || strpos($_SERVER['REQUEST_URI'], "/task/show.php") > 0
    || strpos($_SERVER['REQUEST_URI'], "/task/delete.php") > 0 )
{
    $task_current = 'current ';
}

if (strpos($_SERVER['REQUEST_URI'], "/defect/list.php") > 0
    || strpos($_SERVER['REQUEST_URI'], "/defect/new.php") > 0
    || strpos($_SERVER['REQUEST_URI'], "/defect/edit.php") > 0
    || strpos($_SERVER['REQUEST_URI'], "/defect/delete.php") > 0)
{
    $defect_current = 'current ';
}

if (strpos($_SERVER['REQUEST_URI'], "/user/list.php") > 0
    || strpos($_SERVER['REQUEST_URI'], "/user/new.php") > 0
    || strpos($_SERVER['REQUEST_URI'], "/user/edit.php") > 0
    || strpos($_SERVER['REQUEST_URI'], "/user/delete.php") > 0
    || strpos($_SERVER['REQUEST_URI'], "/user/newpass.php") > 0)
{
    $user_current = 'current ';
}

$str .= '<li '.$home_current.'><a href="'.$GLOBALS["BASE_URL"].'www/home.php">Головна</a></li>';

$str .= '<li class="'.$task_current.'dropdown">';
$str .= '<a class="dropbtn" href="'.$GLOBALS["BASE_URL"].'www/task/list.php">Огляд</a>';
$str .= '<div class="dropdown-content">';
$str .= '<a href="'.$GLOBALS["BASE_URL"].'www/task/new.php">Створити огляд</a>';
$str .= '<a href="'.$GLOBALS["BASE_URL"].'www/task/list.php">Список оглядів</a>';
$str .= '</div>';
$str .= '</li>';

if ($helper->IsManager()) {

    $str .= '<li class="' . $defect_current . 'dropdown">';
    $str .= '<a class="dropbtn" href="' . $GLOBALS["BASE_URL"] . 'www/defect/list.php">Дефекти</a>';
    $str .= '<div class="dropdown-content">';
    $str .= '<a href="' . $GLOBALS["BASE_URL"] . 'www/defect/new.php">Створити дефект</a>';
    $str .= '<a href="' . $GLOBALS["BASE_URL"] . 'www/defect/list.php">Список дефектів</a>';
    $str .= '</div>';
    $str .= '</li>';

    $str .= '<li class="' . $user_current . 'dropdown">';
    $str .= '<a class="dropbtn" href="' . $GLOBALS["BASE_URL"] . 'www/user/list.php">Користувачі</a>';
    $str .= '<div class="dropdown-content">';
    $str .= '<a href="' . $GLOBALS["BASE_URL"] . 'www/user/new.php">Створити користувача</a>';
    $str .= '<a href="' . $GLOBALS["BASE_URL"] . 'www/user/list.php">Список користувачів</a>';
    $str .= '</div>';
    $str .= '</li>';
}

$str .= '</ul>';
$str .= '<div style="float: right;">
            <ul class="nav navHorz">
                <li> <a href="'.$GLOBALS["BASE_URL"].'www/logout.php">Вихід</a></li>

            </ul>
        </div>

        <div class="clear"></div>
    </div>';

echo $str;

?>