<?php

namespace BLL;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

//error_reporting(0);

class bTechnicalSpan
{
    private $sql_insert = "INSERT INTO technical_span (place_id, tp, linename, task_id, latitude_start, longitude_start, "
    . " latitude_end, longitude_end, reviewed, number_span, place_parent_id) "
    . " VALUES (':place_id', ':tp', ':linename', :task_id, :latitude_start, :longitude_start, :latitude_end, :longitude_end, "
    . " :reviewed, ':number_span', ':place_parent_id') RETURNING id";

    public function __construct()
    {
    }

    private function GetRow($line)
    {
        $a = array();

        $a['id'] = $line['id'];
        $a['place_id'] = $line['place_id'];
        $a['tp'] = $line['tp'];
        $a['linename'] = $line['linename'];
        $a['task_id'] = $line['task_id'];
        $a['latitude_start'] = $line['latitude_start'];
        $a['longitude_start'] = $line['longitude_start'];
        $a['latitude_end'] = $line['latitude_end'];
        $a['longitude_end'] = $line['longitude_end'];
        $a['reviewed'] = ($line['reviewed'] === 't') ? true : false;
        $a['date_reviewed'] = $line['date_reviewed'];
        $a['number_span'] = $line['number_span'];
        $a['place_parent_id'] = $line['place_parent_id'];

        return $a;
    }

    public function AddSpan($array)
    {
        $res = 0;

        $db = new Database();

        try {
            $db->Open();

            $result = $db->Query($this->sql_insert,
                [
                    ":tp" => $array['tp'],
                    ":linename" => $array['linename'],
                    ":task_id" => $array['task_id'],
                    ":latitude_start" => $array['latitude_start'],
                    ":longitude_start" => $array['longitude_start'],
                    ":latitude_end" => $array['latitude_end'],
                    ":longitude_end" => $array['longitude_end'],
                    ":reviewed" => $array['reviewed'],
                    ":number_span" => $array['number_span'],
                    ":place_parent_id" => $array['place_parent_id'],
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

    public function AddSpanTransaction($db, $array)
    {
        $res = 0;

        $result = $db->Query($this->sql_insert,
            [
                ":tp" => $array['tp'],
                ":linename" => $array['linename'],
                ":task_id" => $array['task_id'],
                ":latitude_start" => $array['latitude_start'],
                ":longitude_start" => $array['longitude_start'],
                ":latitude_end" => $array['latitude_end'],
                ":longitude_end" => $array['longitude_end'],
                ":reviewed" => $array['reviewed'],
                ":number_span" => $array['number_span'],
                ":place_parent_id" => $array['place_parent_id'],
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

    // Get rows by task id
    public function GetByTaskID($task_id)//: array
    {
        $array = array();

        $db = new Database();

        try {
            $db->Open();

            $query = "select *, DATE(date_reviewed) as date_reviewed, DATE(task_date_created) as task_date_created from vw_get_spans where task_id = :task_id order by place_parent_id ";

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

            $query = "select *, DATE(date_reviewed) as date_reviewed, DATE(task_date_created) as task_date_created from vw_get_spans where user_id = :user_id and task_active = true order by task_id, place_parent_id ";

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

    public function IsExistsSpan($span_id)
    {
        $count = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "SELECT COUNT(*) as count_spans FROM technical_span where id = :span_id ";

            $result = $db->Query($query,
                [
                    ":span_id" => $span_id
                ]);

            if ($result) {
                $count = pg_fetch_array($result)['count_spans'] > 0;
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $count;
    }

    public function IsExistsSpanTransaction($db, $span_id)
    {
        $count = false;

        $result = $db->Query("SELECT COUNT(*) as count_spans FROM technical_span where id = :span_id ",
            [
                ":span_id" => $span_id
            ]);

        if ($result) {
            $count = pg_fetch_array($result)['count_spans'] > 0;
        }

        return $count;
    }

    public function UpdateStatus($span_id, $reviewed)
    {
        $res = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "UPDATE technical_span SET reviewed = :reviewed, date_reviewed = now() where id = :span_id ";

            $result = $db->Query($query,
                [
                    ":span_id" => $span_id,
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

    public function UpdateStatusTransaction($db, $span_id, $reviewed)
    {
        $res = false;

        $result = $db->Query("UPDATE technical_span SET reviewed = :reviewed, date_reviewed = now() where id = :span_id ",
            [
                ":span_id" => $span_id,
                ":reviewed" => boolval($reviewed) ? "true" : "false"
            ]);

        if ($result) {
            $res = pg_affected_rows($result) > 0;
        }

        return $res;
    }
}

?>