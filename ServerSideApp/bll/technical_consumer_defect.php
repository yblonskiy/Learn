<?php

namespace BLL;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

//error_reporting(0);

class bTechnicalConsumerDefect
{
    public function __construct()
    {
    }

    public function AddDefect($consumer_id, $defect_id)
    {
        $res = false;

        try {
            $db = new Database();
            $db->Open();

            $query = "INSERT INTO technical_consumer_defect (technical_consumer_id, defect_id) VALUES (:consumer_id, :defect_id)";

            $result = $db->Query($query,
                [
                    ":consumer_id" => $consumer_id,
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

    public function AddDefectTransaction($db, $consumer_id, $defect_id, $notes)
    {
        $res = false;

        $result = $db->Query("INSERT INTO technical_consumer_defect (technical_consumer_id, defect_id, notes) VALUES (:consumer_id, :defect_id, ':notes')",
            [
                ":consumer_id" => $consumer_id,
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

            $query = "SELECT con.*, def.*, con_def.notes FROM public.technical_consumer_defect con_def "
                . "INNER JOIN technical_consumer con on con_def.technical_consumer_id =  con.id  "
                . "INNER JOIN defect def on con_def.defect_id =  def.id  "
                . "where con.task_id = :task_id "
                . "order by con.id ";

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