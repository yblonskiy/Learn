<?php

$GLOBALS["BASE_URL"] = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/'.'Odessa/';

date_default_timezone_set('Europe/Kiev');
setlocale(LC_ALL, 'uk_UK.UTF-8');

$_page_size = 50;

?>
