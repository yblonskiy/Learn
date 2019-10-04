<?php

namespace BLL;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

error_reporting(0);

class bTechnicalPlaceDefect
{
    public function __construct()
    {
    }

    public function AddDefect($tp_id, $defect_id)
    {
        $res = false;

        try {
            $db = new Database();
            $db->Open();

            $query = "INSERT INTO technical_place_defect (technical_place_id, defect_id) VALUES (:tp_id, :defect_id)";

            $result = $db->Query($query,
                [
                    ":tp_id" => $tp_id,
                    ":defect_id" => $defect_id
                ]);

            if ($result) {
                $res = pg_affected_rows($result) > 0;
            }

            $db->Close();
        } catch (Exception $e) {
            $res = false;
        }

        return $res;
    }

    public function AddDefectTransaction($db, $tp_id, $defect_id, $notes)
    {
        $res = false;

        $result = $db->Query("INSERT INTO technical_place_defect (technical_place_id, defect_id, notes) VALUES (:tp_id, :defect_id, ':notes')",
            [
                ":tp_id" => $tp_id,
                ":defect_id" => $defect_id,
                ":notes" => $notes
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

        try {
            $db = new Database();
            $db->Open();

            $query = "SELECT tp.place_id, def.*, tp_def.notes FROM public.technical_place_defect tp_def "
                . "INNER JOIN technical_place tp on tp_def.technical_place_id =  tp.id "
                . "INNER JOIN defect def on tp_def.defect_id =  def.id  "
                . "where tp.task_id = :task_id "
                . "order by tp.place_id ";

            $result = $db->Query($query,
                [
                    ":task_id" => $task_id
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $a = array();

                    $a['place_id'] = $line['place_id'];
                    $a['title'] = $line['title'];
                    $a['code_full'] = $line['code_full'];
                    $a['notes'] = $line['notes'];

                    $array[] = $a;
                }
            }

            $db->Close();
        } catch (Exception $e) {
        }

        return $array;
    }
}

?>