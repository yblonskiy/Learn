<?php

namespace BLL;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

//error_reporting(0);

class bTechnicalSpanDefect
{
    public function __construct()
    {
    }

    public function AddDefect($span_id, $defect_id)
    {
        $res = false;

        try {
            $db = new Database();
            $db->Open();

            $query = "INSERT INTO technical_span_defect (technical_span_id, defect_id) VALUES (:span_id, :defect_id)";

            $result = $db->Query($query,
                [
                    ":span_id" => $span_id,
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

    public function AddDefectTransaction($db, $span_id, $defect_id, $notes)
    {
        $res = false;

        $result = $db->Query("INSERT INTO technical_span_defect (technical_span_id, defect_id, notes) VALUES (:span_id, :defect_id, ':notes')",
            [
                ":span_id" => $span_id,
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

            $query = "SELECT span.*, def.*, span_def.notes FROM public.technical_span_defect span_def "
                . "INNER JOIN technical_span span on span_def.technical_span_id =  span.id "
                . "INNER JOIN defect def on span_def.defect_id = def.id "
                . "where span.task_id = :task_id "
                . "order by span.id ";

            $result = $db->Query($query,
                [
                    ":task_id" => $task_id
                ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $a = array();

                    $a['place_id'] = $line['place_id'];
                    $a['number_span'] = $line['number_span'];
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