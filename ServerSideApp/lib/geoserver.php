<?php

namespace Library;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

error_reporting(0);

class GeoServer
{
    private $host = "127.0.0.1";
    private $dbname = "odessadb";
    private $user = "postgres";
    private $password = "pwdtest";

    public function __construct()
    {
    }

    // Get rows by TP and Linename
    public function GetSpansByTPandLineName($tp, $linename, $rem, $voltage)
    {
        $array = array();

        $db = new Database();

        try {
            $db->SetConnectionString($this->host, $this->dbname, $this->user, $this->password);
            $db->Open();

            $query = "select *, ST_AsEWKT(the_geom) as geom "
                . "from \":rem-prlt:voltage\" "
                . "where \"TP\" like ':tp' and \"LINENAME\" like ':linename%' ";

            $result = $db->Query($query,
                [
                    ":rem" => $rem,
                    ":tp" => $tp,
                    ":linename" => $linename,
                    ":voltage" => $voltage
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $a = array();

                    $a['tp'] = $line['TP'];
                    $a['linename'] = $line['LINENAME'];
                    $a['place_id'] = $line['NOMOPR'];
                    $a['place_parent_id'] = $line['NOMOPRZAD'];
                    $a['number_span'] = $line['NOMPRLT'];
                    $a['geom'] = $line['geom'];

                    $array[] = $a;
                }
            }

        } catch (\Exception $e) {
            $array = array();
        } finally {
            $db->Close();
        }

        return $array;
    }

    // Get rows by TP and Linename
    public function GetСonsumersByTPandLineName($tp, $linename, $rem, $voltage)
    {
        $array = array();

        $db = new Database();

        try {
            $db->SetConnectionString($this->host, $this->dbname, $this->user, $this->password);
            $db->Open();

            $query = "select *, ST_AsEWKT(the_geom) as geom "
                . "from \":rem-spzh:voltage\" "
                . "where \"TP\" like ':tp' and \"LINENAME\" like ':linename%' ";

            $result = $db->Query($query,
                [
                    ":rem" => $rem,
                    ":tp" => $tp,
                    ":linename" => $linename,
                    ":voltage" => $voltage
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $a = array();

                    $a['tp'] = $line['TP'];
                    $a['linename'] = $line['LINENAME'];
                    $a['place_id'] = $line['NOMOPR'];
                    $a['geom'] = $line['geom'];

                    $array[] = $a;
                }
            }

        } catch (\Exception $e) {
            $array = array();
        } finally {
            $db->Close();
        }

        return $array;
    }

    // Get TP by rem and voltage
    public function GetTPsByRemAndVoltage($rem, $voltage)
    {
        $array = array();

        $db = new Database();

        try {

            $db->SetConnectionString($this->host, $this->dbname, $this->user, $this->password);
            $db->Open();

            $query = "SELECT \"TP\" "
                . "from \":rem-prlt:voltage\" "
                . "group by \"TP\" "
                . "order by \"TP\" ";

            $result = $db->Query($query,
                [
                    ":rem" => $rem,
                    ":voltage" => $voltage
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $a = array();

                    $a['tp'] = $line['TP'];

                    $array[] = $a;
                }
            }

        } catch (\Exception $e) {
            $array = array();
        } finally {
            $db->Close();
        }

        return $array;
    }

    // Get Lines by rem and tp and voltage
    public function GetLinesByTPAndVoltage($tp, $rem, $voltage)
    {
        $array = array();

        $db = new Database();

        try {
            $db->SetConnectionString($this->host, $this->dbname, $this->user, $this->password);
            $db->Open();

            $query = "SELECT \"LINENAME\" "
                . "from \":rem-prlt:voltage\" "
                . "where \"TP\" like ':tp' "
                . "group by \"LINENAME\" "
                . "order by \"LINENAME\" ";

            $result = $db->Query($query,
                [
                    ":rem" => $rem,
                    ":tp" => $tp,
                    ":voltage" => $voltage
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $linename = $line['LINENAME'];

                    if (strpos($line['LINENAME'], '(') > -1) {
                        $linename = substr_replace($line['LINENAME'], '', strpos($line['LINENAME'], '('));
                    }

                    if (!in_array($linename, $array)) {
                        array_push($array, $linename);
                    }
                }
            }

        } catch (\Exception $e) {
            $array = array();
        } finally {
            $db->Close();
        }

        return $array;
    }
}

?>