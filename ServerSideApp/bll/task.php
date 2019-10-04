<?php

namespace BLL;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

error_reporting(0);

class bTasks
{
    private $sql_insert = "INSERT INTO task (name, date_created, rem, tp, linename, active, voltage_level_full, created_user_id, type) "
    . " VALUES (':name', NOW(), ':rem', ':tp', ':linename', :active, ':voltage_level_full', :created_user_id, ':type') RETURNING id";

    public function AddTask($name, $rem, $tp, $linename, $active, $voltage_level_full, $created_user_id, $type)
    {
        $res = 0;

        $db = new Database();

        try {
            $db->Open();

            $result = $db->Query($this->sql_insert,
                [
                    ":name" => $name,
                    ":rem" => $rem,
                    ":tp" => $tp,
                    ":linename" => $linename,
                    ":active" => boolval($active) ? "true" : "false",
                    ":voltage_level_full" => $voltage_level_full,
                    ":created_user_id" => $created_user_id,
                    ":type" => $type
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

    public function AddTaskTransaction($db, $name, $rem, $tp, $linename, $active, $voltage_level_full, $created_user_id, $type)
    {
        $res = 0;

        $result = $db->Query($this->sql_insert,
            [
                ":name" => $name,
                ":rem" => $rem,
                ":tp" => $tp,
                ":linename" => $linename,
                ":active" => boolval($active) ? "true" : "false",
                ":voltage_level_full" => $voltage_level_full,
                ":created_user_id" => $created_user_id,
                ":type" => $type
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

    public function IsExistsTask($task_id)
    {
        $count = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "SELECT COUNT(*) as count_tasks FROM task where id = :task_id ";

            $result = $db->Query($query,
                [
                    ":task_id" => $task_id
                ]);

            if ($result) {
                $count = pg_fetch_array($result)['count_tasks'] > 0;
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $count;
    }

    public function IsExistsTaskTransaction($db, $task_id)
    {
        $count = false;

        $result = $db->Query("SELECT COUNT(*) as count_tasks FROM task where id = :task_id ",
            [
                ":task_id" => $task_id
            ]);

        if ($result) {
            $count = pg_fetch_array($result)['count_tasks'] > 0;
        }

        return $count;
    }

    public function UpdateActive($task_id, $active)
    {
        $res = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "UPDATE task SET active = :active where id = :task_id ";

            $result = $db->Query($query,
                [
                    ":task_id" => $task_id,
                    ":active" => boolval($active) ? "true" : "false"
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

}

?>