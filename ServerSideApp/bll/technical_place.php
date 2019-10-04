<?php

namespace BLL;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

error_reporting(0);

class bTechnicalPlace
{
    private $sql_insert = "INSERT INTO technical_place (task_id, reviewed, parent_technical_place_id, latitude, longitude, place_id) "
    . " VALUES (:task_id, :reviewed, :parent_technical_place_id, :latitude, :longitude, :place_id) RETURNING id";

    public function __construct()
    {
    }

    private function GetRow($line)
    {
        $a = array();

        $a['id'] = $line['id'];
        $a['task_id'] = $line['task_id'];
        $a['reviewed'] = ($line['reviewed'] === 't') ? true : false;
        $a['parent_technical_place_id'] = $line['parent_technical_place_id'];
        $a['latitude'] = $line['latitude'];
        $a['longitude'] = $line['longitude'];
        $a['date_reviewed'] = $line['date_reviewed'];
        $a['place_id'] = $line['place_id'];

        return $a;
    }

    public function AddTP($array)
    {
        $res = 0;

        $db = new Database();

        try {
            $db->Open();

            $result = $db->Query($this->sql_insert,
                [
                    ":task_id" => $array['task_id'],
                    ":reviewed" => $array['reviewed'],
                    ":parent_technical_place_id" => $array['parent_technical_place_id'],
                    ":latitude" => $array['latitude'],
                    ":longitude" => $array['longitude'],
                    ":place_id" => $array['place_id']
                ]);

            if ($result) {
                $insert_row = pg_fetch_row($result);
                $res = $insert_row[0];
            }

        } catch (\Exception $e) {
            $res = 0;
        } finally {
            $db->Close();
        }

        return $res;
    }

    public function AddTPTransaction($db, $array)
    {
        $res = 0;

        $result = $db->Query($this->sql_insert,
            [
                ":task_id" => $array['task_id'],
                ":reviewed" => $array['reviewed'],
                ":parent_technical_place_id" => $array['parent_technical_place_id'],
                ":latitude" => $array['latitude'],
                ":longitude" => $array['longitude'],
                ":place_id" => $array['place_id']
            ]);

        if ($result) {
            $insert_row = pg_fetch_row($result);
            $res = $insert_row[0];
        }

        if ($res === 0) {
            throw new \ErrorException('error');
        }

        return $res;
    }

    public function IsExistsTP($tp_id)
    {
        $count = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "SELECT COUNT(*) as count_tps FROM technical_place where id = :tp_id ";

            $result = $db->Query($query,
                [
                    ":tp_id" => $tp_id
                ]);

            if ($result) {
                $count = pg_fetch_array($result)['count_tps'] > 0;
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $count;
    }

    public function IsExistsTPTransaction($db, $tp_id)
    {
        $count = false;

        $result = $db->Query("SELECT COUNT(*) as count_tps FROM technical_place where id = :tp_id ",
            [
                ":tp_id" => $tp_id
            ]);

        if ($result) {
            $count = pg_fetch_array($result)['count_tps'] > 0;
        }

        return $count;
    }

    public function UpdateStatus($tp_id, $reviewed)
    {
        $res = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "UPDATE technical_place SET reviewed = :reviewed, date_reviewed = now() where id = :tp_id ";

            $result = $db->Query($query,
                [
                    ":tp_id" => $tp_id,
                    ":reviewed" => boolval($reviewed) ? "true" : "false"
                ]);

            if ($result) {
                $res = pg_affected_rows($result) > 0;
            }

        } catch (\Exception $e) {
            $res = false;
        } finally {
            $db->Close();
        }

        return $res;
    }

    public function UpdateStatusTransaction($db, $tp_id, $reviewed)
    {
        $res = false;

        $result = $db->Query("UPDATE technical_place SET reviewed = :reviewed, date_reviewed = now() where id = :tp_id ",
            [
                ":tp_id" => $tp_id,
                ":reviewed" => boolval($reviewed) ? "true" : "false"
            ]);

        if ($result) {
            $res = pg_affected_rows($result) > 0;
        }

        return $res;
    }

    // Get rows by task id
    public function GetByTaskID($task_id)//: array
    {
        $array = array();

        $db = new Database();

        try {
            $db->Open();

            $query = "select *, DATE(date_reviewed) as date_reviewed, DATE(task_date_created) as task_date_created from vw_get_tps where task_id = :task_id order by parent_technical_place_id ";

            $result = $db->Query($query,
                [
                    ":task_id" => $task_id
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $array[] = $this->GetRow($line);
                }
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $array;
    }

    // Get rows by user id
    public function GetByUserID($user_id)//: array
    {
        $array = array();

        $db = new Database();

        try {
            $db->Open();

            $query = "select *, DATE(date_reviewed) as date_reviewed, DATE(task_date_created) as task_date_created from vw_get_tps where user_id = :user_id and task_active = true order by task_id, parent_technical_place_id ";

            $result = $db->Query($query,
                [
                    ":user_id" => $user_id
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $array[] = $this->GetRow($line);
                }
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $array;
    }
}

?>