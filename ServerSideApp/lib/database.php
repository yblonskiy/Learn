<?php

namespace Library;

//error_reporting(0);

class Database
{
    public $isConnect = false;
    public $dbConnect;

    private $host = "127.0.0.1";
    private $dbname = "mobile_review";
    private $user = "mobileuser";
    private $password = "123qaz";

    public function __construct()
    {
    }

    public function SetConnectionString($phost, $pdbname, $puser, $ppassword)
    {
         $this->host = $phost;
         $this->dbname = $pdbname;
         $this->user  = $puser;
         $this->password  = $ppassword;
    }

    function Open()
    {
        $this->dbConnect = pg_connect("host=" . $this->host . " dbname=" . $this->dbname . " user=" . $this->user . " password=" . $this->password . "")
        or die('Could not connect: ' . pg_last_error());

        $this->isConnect = true;

        pg_set_client_encoding($this->dbConnect, "UNICODE");
    }

    public function QueryParams($sql, $params)
    {
        return pg_query_params($this->dbConnect, $sql, $params);
    }

    public function Query($sql, $params)
    {
        foreach ($params as $key => $color) {
            $sql = str_ireplace ($key, pg_escape_string($params[$key]), $sql);
        }

        try {
            $result = pg_query($this->dbConnect, $sql);
        } catch (Exception $e) {
            $result = "";
        }

        return $result;
    }

    public function beginTransaction()
    {
        pg_query($this->dbConnect,"BEGIN") or die("Could not start transaction\n");
    }

    public function commitTransaction()
    {
        pg_query($this->dbConnect,"COMMIT") or die("Transaction commit failed\n");
    }

    public function rollbackTransaction()
    {
        pg_query($this->dbConnect,"ROLLBACK") or die("Transaction rollback failed\n");
    }

    function Close()
    {
        if ($this->isConnect) {
            pg_close($this->dbConnect);
        }
    }

}

?>