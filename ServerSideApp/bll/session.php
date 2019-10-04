<?php

namespace BLL;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

//error_reporting(0);

class bSession
{
    public function __construct()
    {
    }

    public function AddSession($session_id, $user_id, $device)//: int
    {
        $res = 0;

        $db = new Database();

        try {
            $db->Open();

            $query = "INSERT INTO session(session_id, user_id, date_created, device) VALUES(':session_id', :user_id, NOW(), ':device') RETURNING id";

            $result = $db->Query($query,
                [
                    ":session_id" => $session_id,
                    ":user_id" => $user_id,
                    ":device" => $device
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

    public function DeleteSession($device)//: bool
    {
        $res = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "delete from session where device = ':device'";

            $result = $db->Query($query,
                [
                    ":device" => $device
                ]);

            if ($result) {
                $res = true;
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $res;
    }

    public function LoadBySession($session_id)//: bool
    {
        $res = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "SELECT id FROM session where session_id = ':session_id'";

            $result = $db->Query($query,
                [
                    ":session_id" => $session_id
                ]);

            if ($result) {
                $res = true;
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $res;
    }
}

?>