<?php

error_reporting(0);

// params: 'login' as string, 'password' as string, 'device' as string

require_once(realpath(dirname(__FILE__) . "/../bll/user.php"));
require_once(realpath(dirname(__FILE__) . "/../bll/session.php"));
require_once(realpath(dirname(__FILE__) . "/../lib/helper.php"));

use Library\Helper;
use BLL\bUser;
use BLL\bSession;

header('Content-type: text/json; charset=UTF-8');

$responseDict = array();

 if (!isset($_POST['login']) || !isset($_POST['password']) || !isset($_POST['device'])) {
    $responseDict['login'] = '-1';
    echo json_encode($responseDict, JSON_UNESCAPED_UNICODE);
    return;
}

$login = $_POST['login'];
$password = $_POST['password']; // as md5
$device = $_POST['device'];

/*
// datas to test
$login = "yara";
$password = md5("123"); // as md5
$device = "12345";
*/


$user = new bUser();
$helper = new Helper();

$user_id = $user->LoadUserID($login, $password);

if ($user_id > 0) {

    $ssn = new bSession();

    // delete old sessions
    $ssn->DeleteSession($device);

    // session id
    $ssnid = $helper->rand_string(1, 15);

    // create session
    if ($ssn->AddSession($ssnid, $user_id, $device)) {
        $responseDict['session'] = $ssnid;
        $responseDict['login'] = '0';
    }
    else
    {
        $responseDict['login'] = '2'; // session is not created
    }

} else {
    $responseDict['login'] = '1'; // user is not exist
}

$responseDict['user_id'] = $user_id; // user id

echo json_encode($responseDict, JSON_UNESCAPED_UNICODE);

?>