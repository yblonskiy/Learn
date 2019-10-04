<?php

namespace BLL;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

error_reporting(0);

class bUser
{
    public function __construct()
    {
    }

    private function GetRow($line)
    {
        $a = array();

        $a['id'] = $line['id'];
        $a['email'] = $line['email'];
        $a['first_name'] = $line['first_name'];
        $a['last_name'] = $line['last_name'];
        $a['user_types_id'] = $line['user_types_id'];
        $a['active'] = ($line['active'] === 't') ? true : false;
        $a['password'] = $line['password'];
        $a['login'] = $line['login'];
        $a['patronymic'] = $line['patronymic'];
        $a['post_name'] = $line['post_name'];

        return $a;
    }

    public function AddUser($login, $password, $firstname, $lastname, $email, $type_id, $active, $patronymic, $postname)
    {
        $res = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "INSERT INTO public.\"user\" (email, first_name, last_name, user_types_id, active, password, login, patronymic, post_name) "
                . " VALUES (':email', ':first_name', ':last_name', :user_types_id, :active, ':password', ':login', ':patronymic', ':post_name') RETURNING id";

            $result = $db->Query($query,
                [
                    ":email" => $email,
                    ":first_name" => $firstname,
                    ":last_name" => $lastname,
                    ":user_types_id" => $type_id,
                    ":active" => boolval($active) ? "true" : "false",
                    ":password" => $password,
                    ":login" => $login,
                    ":patronymic" => $patronymic,
                    ":post_name" => $postname
                ]);

            if ($result) {
                $insert_row = pg_fetch_row($result);
                $res = $insert_row[0];
            }

        } catch (\Exception $e) {
            $res = false;
        } finally {
            $db->Close();
        }

        return $res;
    }

    public function UpdateUser($user_id, $login, $firstname, $lastname, $email, $type_id, $active, $patronymic, $postname)
    {
        $res = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "UPDATE public.\"user\" SET email = ':email', first_name = ':first_name', last_name = ':last_name', "
                . " user_types_id = :user_types_id, active = :active, login =':login', patronymic = ':patronymic', post_name = ':post_name'  "
                . " where id = :user_id ";

            $result = $db->Query($query,
                [
                    ":user_id" => $user_id,
                    ":email" => $email,
                    ":first_name" => $firstname,
                    ":last_name" => $lastname,
                    ":user_types_id" => $type_id,
                    ":active" => boolval($active) ? "true" : "false",
                    ":login" => $login,
                    ":patronymic" => $patronymic,
                    ":post_name" => $postname
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

    public function UpdateActive($user_id, $active)
    {
        $res = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "UPDATE public.\"user\" SET active = :active where id = :user_id ";

            $result = $db->Query($query,
                [
                    ":user_id" => $user_id,
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

    public function EditPassword($user_id, $password)
    {
        $res = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "UPDATE public.\"user\" SET password = ':password'  "
                . " where id = :user_id ";

            $result = $db->Query($query,
                [
                    ":user_id" => $user_id,
                    ":password" => $password
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

    public function IsUserExists($login, $password)//: bool
    {
        return $this->LoadUserID($login, $password) > 0;
    }

    public function LoadUserID($login, $password)//: int
    {
        $res = 0;

        $db = new Database();

        try {
            $db->Open();

            $query = "SELECT id FROM public.\"user\" WHERE \"password\" like ':password' and login like ':login' and active = true ";

            $result = $db->Query($query,
                [
                    ":login" => $login,
                    ":password" => $password
                ]);

            if ($result) {
                if ($line = pg_fetch_assoc($result)) {
                    $res = $line['id'];
                }
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $res;
    }

    public function GetUser($login, $password)//: int
    {
        $a = array();

        $db = new Database();

        try {
            $db->Open();

            $query = "SELECT * FROM public.\"user\" WHERE \"password\" like ':password' and login like ':login' and active = true ";

            $result = $db->Query($query,
                [
                    ":login" => $login,
                    ":password" => $password
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
            $a = null;
        } finally {
            $db->Close();
        }

        return $a;
    }

    public function IsLoginExists($login)//: int
    {
        $res = 0;

        $db = new Database();

        try {
            $db->Open();

            $query = "SELECT id FROM public.\"user\" WHERE login like ':login' ";

            $result = $db->Query($query,
                [
                    ":login" => $login
                ]);

            if ($result) {
                if ($line = pg_fetch_assoc($result)) {
                    $res = $line['id'];
                }
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $res > 0;
    }

    // Get all active users
    public function GetUsers()//: array
    {
        $array = array();

        $db = new Database();

        try {
            $db->Open();

            $result = $db->Query("SELECT * FROM public.\"user\" where active = true ", []);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $array[] =  $this->GetRow($line);
                }
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $array;
    }

    // Get user by id
    public function GetUserByID($user_id)//: array
    {
        $a = array();

        $db = new Database();

        try {
            $db->Open();

            $result = $db->Query("SELECT * FROM public.\"user\" where id = :user_id and active = true ", [
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
            $a = null;
        } finally {
            $db->Close();
        }

        return $a;
    }

    // Get user by task id
    public function GetUserByTaskID($task_id)
    {
        $a = array();

        $db = new Database();

        try {
            $db->Open();

            $query = "SELECT distinct on (usr.id) usr.* FROM public.\"user\" usr "
                . "where usr.id in ( select user_id from vw_get_user_tasks where task_id = :task_id)";

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
                $a = null;
            }

        } catch (\Exception $e) {
            $a = null;
        } finally {
            $db->Close();
        }

        return $a;
    }

    // Get all rows
    public function GetByPaging($page_size, $page_number)//: array
    {
        $array = array();
        $total = 0;

        $db = new Database();

        try {
            $db->Open();

            $start = ($page_number - 1) * $page_size;

            $query = "SELECT * FROM public.\"user\" where active = true order by last_name desc limit :page_size offset :start ";

            $result = $db->Query($query, [
                ":start" => $start,
                ":page_size" => $page_size,
            ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $array[] =  $this->GetRow($line);
                }
            }

            $query_count = "SELECT count(*) as total FROM public.\"user\"  ";
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

    // Get all rows
    public function GetUserTypes()//: array
    {
        $array = array();

        $db = new Database();

        try {
            $db->Open();

            $query = "SELECT * FROM user_types ";

            $result = $db->Query($query, []);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $a = array();

                    $a['id'] = $line['id'];
                    $a['name'] = $line['name'];

                    $array[] = $a;
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