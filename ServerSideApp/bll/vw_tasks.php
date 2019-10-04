<?php

namespace BLL;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

error_reporting(0);

class vwTasks
{
    private function GetRow($line)
    {
        $a = array();

        $a['task_id'] = $line['task_id'];
        $a['task_name'] = $line['task_name'];
        $a['date_created'] = $line['date_created'];
        $a['tp'] = $line['tp'];
        $a['linename'] = $line['linename'];
        $a['rem'] = $line['rem'];
        $a['task_active'] = ($line['task_active'] === 't') ? true : false;
        $a['voltage_level_full'] = $line['voltage_level_full'];
        $a['user_id'] = $line['user_id'];
        $a['email'] = $line['email'];
        $a['first_name'] = $line['first_name'];
        $a['last_name'] = $line['last_name'];
        $a['user_types_id'] = $line['user_types_id'];
        $a['user_active'] = ($line['user_active'] === 't') ? true : false;
        $a['password'] = $line['password'];
        $a['login'] = $line['login'];
        $a['patronymic'] = $line['patronymic'];
        $a['status_name'] = $line['status_name'];
        $a['task_status_id'] = $line['task_status_id'];
        $a['date_status_updated'] = $line['date_status_updated'];
        $a['created_user_id'] = $line['created_user_id'];
        $a['type'] = $line['type'];

        return $a;
    }

    private function GetRowOfWeb($line)
    {
        $a = array();

        $a['id'] = $line['task_id'];
        $a['name'] = $line['task_name'];
        $a['date_created'] = $line['date_created'];
        $a['tp'] = $line['tp'];
        $a['linename'] = $line['linename'];
        $a['rem'] = $line['rem'];
        $a['voltage_level_full'] = $line['voltage_level_full'];
        $a['status_id'] = $line['task_status_id'];
        $a['active'] = ($line['task_active'] === 't') ? true : false;
        $a['user_id'] = $line['user_id'];
        //$a['date_status_updated'] = $line['date_status_updated'];
        $a['created_user_id'] = $line['created_user_id'];
        $a['type'] = $line['type'];

        return $a;
    }

    // Get rows by user id
    public function GetWithPagingByUserId($user_id, $page_size, $page_number)//: array
    {
        $array = array();
        $total = 0;

        $db = new Database();

        try {
            $db->Open();

            $start = ($page_number - 1) * $page_size;

            $query = "select * from vw_get_tasks "
                . "where user_id = :user_id and task_active = true "
                . "order by date_created desc limit :page_size offset :start ";

            $result = $db->Query($query,
                [
                    ":user_id" => $user_id,
                    ":start" => $start,
                    ":page_size" => $page_size
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $array[] = $this->GetRow($line);
                }
            }

            $query_count = "SELECT count(*) as total FROM vw_get_tasks "
                . "where user_id = :user_id and task_active = true  ";

            $result = $db->Query($query_count, [":user_id" => $user_id]);

            if ($result) {
                $row_sql = pg_fetch_row($result);
                $total = $row_sql[0];
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        $array['total'] = $total;

        return $array;
    }

    // Get all rows
    public function GetWithPaging($page_size, $page_number)//: array
    {
        $array = array();
        $total = 0;

        $db = new Database();

        try {
            $db->Open();

            $start = ($page_number - 1) * $page_size;

            $query = "select * from vw_get_tasks "
                . "where task_active = true "
                . "order by date_created desc limit :page_size offset :start ";

            $result = $db->Query($query, [
                ":start" => $start,
                ":page_size" => $page_size
            ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $array[] = $this->GetRow($line);
                }
            }

            $query_count = "SELECT count(*) as total FROM vw_get_tasks where task_active = true ";
            $result = $db->Query($query_count, []);

            if ($result) {
                $row_sql = pg_fetch_row($result);
                $total = $row_sql[0];
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        $array['total'] = $total;

        return $array;
    }

    // Get rows by user id
    public function webGetByUserId($user_id)//: array
    {
        $array = array();

        $db = new Database();

        try {
            $db->Open();

            $query = "select *, DATE(date_created) as date_created from vw_get_tasks where user_id = :user_id ";

            $result = $db->Query($query,
                [
                    ":user_id" => $user_id
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $array[] = $this->GetRowOfWeb($line);
                }
            }
        } catch (\Exception $e) {
            $array = array();
        } finally {
            $db->Close();
        }

        return $array;
    }

    public function webGetByTaskId($task_id)
    {
        $a = array();

        $db = new Database();

        try {
            $db->Open();

            $query = "select *, DATE(date_created) as date_created from vw_get_tasks where task_id = :task_id ";

            $result = $db->Query($query,
                [
                    ":task_id" => $task_id
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $a = $this->GetRowOfWeb($line);
                    break;
                }
            }

        } catch (\Exception $e) {
            $a = array();
        } finally {
            $db->Close();
        }

        return $a;
    }

    public function GetByTaskId($task_id)
    {
        $a = array();

        $db = new Database();

        try {
            $db->Open();

            $query = "select * from vw_get_tasks "
                . "where task_id = :task_id and task_active = true ";

            $result = $db->Query($query,
                [
                    ":task_id" => $task_id
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $a = $this->GetRow($line);
                    break;
                }
            }

            if (count($a) == 0) {
                $res = null;
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $a;
    }

    public function GetByTaskIdAndUserId($task_id, $user_id)
    {
        $a = array();

        $db = new Database();

        try {
            $db->Open();

            $query = "select * from vw_get_tasks "
                . "where task_id = :task_id and user_id = :user_id and task_active = true ";

            $result = $db->Query($query,
                [
                    ":task_id" => $task_id,
                    ":user_id" => $user_id
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $a = $this->GetRow($line);
                    break;
                }
            }

            if (count($a) == 0) {
                $a = null;
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $a;
    }
}

?>