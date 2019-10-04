<?php

namespace BLL;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

error_reporting(0);

class bTaskUserHistory
{
    public function AddTaskTransaction($db, $task_id, $user_id)
    {
        $res = 0;

        $query = "INSERT INTO task_user_history (task_id, user_id, date_updated) "
            . " VALUES (:task_id, :user_id, NOW()) RETURNING id";

        $result = $db->Query($query,
            [
                ":task_id" => $task_id,
                ":user_id" => $user_id
            ]);

        if ($result) {
            $insert_row = pg_fetch_row($result);
            $res = $insert_row[0];
        }

        if ($res === 0)
        {
            throw new \ErrorException('error');
        }

        return $res;
    }
}

?>