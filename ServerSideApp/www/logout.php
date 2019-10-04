<?php

session_start();

// Logout
if (isset($_SESSION['user']))
{
    $_SESSION['user']=null;
}

header('Location: login.php');
exit;

?>