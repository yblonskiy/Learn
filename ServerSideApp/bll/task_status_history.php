<?php

namespace BLL;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

error_reporting(0);

class bTaskStatusHistory
{
    private $sql_insert = "INSERT INTO task_status_history (task_id, task_status_id, date_updated) "
    . " VALUES (:task_id, :task_status_id, NOW()) RETURNING id";

    public function AddTask($task_id, $task_status_id)
    {
        $res = 0;

        $db = new Database();

        try {
            $db->Open();

            $result = $db->Query($this->sql_insert,
                [
                    ":task_id" => $task_id,
                    ":task_status_id" => $task_status_id
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

    public function AddTaskTransaction($db, $task_id, $task_status_id)
    {
        $res = 0;

        $result = $db->Query($this->sql_insert,
            [
                ":task_id" => $task_id,
                ":task_status_id" => $task_status_id
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
}

?>