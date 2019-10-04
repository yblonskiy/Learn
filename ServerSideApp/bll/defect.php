<?php

namespace BLL;

require_once(realpath(dirname(__FILE__) . "/../lib/database.php"));

use Library\Database;

error_reporting(0);

class bDefect
{
    private $sql_insert = "INSERT INTO public.\"defect\" (title, title_short, code, multiple, probability_shutdown, lep, type_place, "
    . " type_place_short, in_journal, code_full, code_voltage_range, voltage_range, voltage_level, voltage_level_full, type_place_id, active) "
    . " VALUES (':1title', ':2title_short', ':3code', :4multiple, ':5probability_shutdown', ':6lep', ':7type_place', "
    . " ':8type_place_short', :9in_journal, ':10code_full', ':11code_voltage_range', ':12voltage_range', ':13voltage_level', ':14voltage_level_full', :15type_place_id, :16active) RETURNING id";


    public function __construct()
    {
    }

    public function AddDefect($title, $title_short, $code, $multiple, $probability_shutdown, $lep, $type_place,
                              $type_place_short, $in_journal, $code_full, $code_voltage_range, $voltage_range, $voltage_level,
                              $voltage_level_full, $type_place_id, $active)
    {
        $res = false;

        $db = new Database();

        try {
            $db->Open();

            $result = $db->Query($this->sql_insert,
                [
                    ":1title" => $title,
                    ":2title_short" => $title_short,
                    ":3code" => $code,
                    ":4multiple" => boolval($multiple) ? "true" : "false",
                    ":5probability_shutdown" => $probability_shutdown,
                    ":6lep" => $lep,
                    ":7type_place" => $type_place,
                    ":8type_place_short" => $type_place_short,
                    ":9in_journal" => boolval($in_journal) ? "true" : "false",
                    ":10code_full" => $code_full,
                    ":11code_voltage_range" => $code_voltage_range,
                    ":12voltage_range" => $voltage_range,
                    ":13voltage_level" => $voltage_level,
                    ":14voltage_level_full" => $voltage_level_full,
                    ":15type_place_id" => $type_place_id,
                    ":16active" => boolval($active) ? "true" : "false"
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

    public function AddDefectTransaction($db, $title, $title_short, $code, $multiple, $probability_shutdown, $lep, $type_place,
                                         $type_place_short, $in_journal, $code_full, $code_voltage_range, $voltage_range, $voltage_level,
                                         $voltage_level_full, $type_place_id, $active)
    {
        $res = 0;

        $result = $db->Query($this->sql_insert,
            [
                ":1title" => $title,
                ":2title_short" => $title_short,
                ":3code" => $code,
                ":4multiple" => boolval($multiple) ? "true" : "false",
                ":5probability_shutdown" => $probability_shutdown,
                ":6lep" => $lep,
                ":7type_place" => $type_place,
                ":8type_place_short" => $type_place_short,
                ":9in_journal" => boolval($in_journal) ? "true" : "false",
                ":10code_full" => $code_full,
                ":11code_voltage_range" => $code_voltage_range,
                ":12voltage_range" => $voltage_range,
                ":13voltage_level" => $voltage_level,
                ":14voltage_level_full" => $voltage_level_full,
                ":15type_place_id" => $type_place_id,
                ":16active" => boolval($active) ? "true" : "false"
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

    public function IsCodeFullExistsTransaction($db, $codefull)
    {
        $res = false;

        try {
            $query = "SELECT count(id) as total FROM public.\"defect\" WHERE \"code_full\" like ':codefull'";

            $result = $db->Query($query,
                [
                    ":codefull" => $codefull
                ]);

            if ($result) {
                $row_sql = pg_fetch_row($result);
                $res = $row_sql[0] > 0;
            }

        } catch (\Exception $e) {
            // if error then do not add new row into 'defect'
            $res = true;
        }

        return $res;
    }


    public function UpdateDefect($defect_id, $title, $title_short, $code, $probability_shutdown, $lep, $type_place,
                                 $type_place_short, $code_full, $code_voltage_range, $voltage_range, $voltage_level,
                                 $voltage_level_full, $type_place_id)
    {
        $res = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "UPDATE public.\"defect\" SET title = ':1title', title_short = ':2title_short', code = ':3code', "
                . " probability_shutdown =':5probability_shutdown', lep =':6lep', type_place =':7type_place', type_place_short =':8type_place_short', "
                . " code_full =':10code_full', code_voltage_range =':11code_voltage_range', voltage_range =':12voltage_range', voltage_level =':13voltage_level', "
                . " voltage_level_full =':14voltage_level_full', type_place_id = :15type_place_id "
                . " where id = :defect_id ";

            $result = $db->Query($query,
                [
                    ":defect_id" => $defect_id,
                    ":1title" => $title,
                    ":2title_short" => $title_short,
                    ":3code" => $code,
                    ":5probability_shutdown" => $probability_shutdown,
                    ":6lep" => $lep,
                    ":7type_place" => $type_place,
                    ":8type_place_short" => $type_place_short,
                    ":10code_full" => $code_full,
                    ":11code_voltage_range" => $code_voltage_range,
                    ":12voltage_range" => $voltage_range,
                    ":13voltage_level" => $voltage_level,
                    ":14voltage_level_full" => $voltage_level_full,
                    ":15type_place_id" => $type_place_id
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

    // Get all defects
    public function GetDefects()//: array
    {
        $array = array();

        $db = new Database();

        try {
            $db->Open();

            $result = $db->Query("SELECT * FROM public.\"defect\" where active = true ", []);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $a = array();

                    $a['id'] = $line['id'];
                    $a['title'] = $line['title'];
                    $a['title_short'] = $line['title_short'];
                    $a['code'] = $line['code'];
                    $a['multiple'] = ($line['multiple'] === 't') ? 'true' : 'false';
                    $a['probability_shutdown'] = $line['probability_shutdown'];
                    $a['lep'] = $line['lep'];
                    $a['type_place'] = $line['type_place'];
                    $a['type_place_short'] = $line['type_place_short'];
                    $a['type_place_id'] = $line['type_place_id'];
                    $a['in_journal'] = ($line['in_journal'] === 't') ? 'true' : 'false';
                    $a['code_full'] = $line['code_full'];
                    $a['code_voltage_range'] = $line['code_voltage_range'];
                    $a['voltage_range'] = $line['voltage_range'];
                    $a['voltage_level'] = $line['voltage_level'];
                    $a['voltage_level_full'] = $line['voltage_level_full'];
                    $a['active'] = $line['active'];

                    $array[] = $a;
                }
            }

        } catch (\Exception $e) {
            $array = array();
        } finally {
            $db->Close();
        }

        return $array;
    }

    public function GetByPaging($page_size, $page_number)//: array
    {
        $array = array();
        $total = 0;

        $db = new Database();

        try {
            $db->Open();

            $start = ($page_number - 1) * $page_size;

            $query = "SELECT * FROM public.\"defect\" where active = true order by code_full limit :page_size offset :start ";

            $result = $db->Query($query, [
                ":start" => $start,
                ":page_size" => $page_size,
            ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {
                    $a = array();

                    $a['id'] = $line['id'];
                    $a['title'] = $line['title'];
                    $a['title_short'] = $line['title_short'];
                    $a['code'] = $line['code'];
                    $a['multiple'] = ($line['multiple'] === 't') ? 'true' : 'false';
                    $a['probability_shutdown'] = $line['probability_shutdown'];
                    $a['lep'] = $line['lep'];
                    $a['type_place'] = $line['type_place'];
                    $a['type_place_short'] = $line['type_place_short'];
                    $a['type_place_id'] = $line['type_place_id'];
                    $a['in_journal'] = ($line['in_journal'] === 't') ? 'true' : 'false';
                    $a['code_full'] = $line['code_full'];
                    $a['code_voltage_range'] = $line['code_voltage_range'];
                    $a['voltage_range'] = $line['voltage_range'];
                    $a['voltage_level'] = $line['voltage_level'];
                    $a['voltage_level_full'] = $line['voltage_level_full'];
                    $a['active'] = $line['active'];

                    $array[] = $a;
                }
            }

            $query_count = "SELECT count(*) as total FROM public.\"defect\"  ";
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

    // Get defect by id
    public function GetByDefectId($defect_id)//: array
    {
        $res = array();

        $db = new Database();

        try {
            $db->Open();

            $result = $db->Query("SELECT * FROM public.\"defect\" where id = :defect_id and active = true ", [
                ":defect_id" => $defect_id
            ]);

            if ($result) {
                while ($line = pg_fetch_array($result)) {

                    $res['id'] = $line['id'];
                    $res['title'] = $line['title'];
                    $res['title_short'] = $line['title_short'];
                    $res['code'] = $line['code'];
                    $res['multiple'] = ($line['multiple'] === 't') ? 'true' : 'false';
                    $res['probability_shutdown'] = $line['probability_shutdown'];
                    $res['lep'] = $line['lep'];
                    $res['type_place'] = $line['type_place'];
                    $res['type_place_short'] = $line['type_place_short'];
                    $res['type_place_id'] = $line['type_place_id'];
                    $res['in_journal'] = ($line['in_journal'] === 't') ? 'true' : 'false';
                    $res['code_full'] = $line['code_full'];
                    $res['code_voltage_range'] = $line['code_voltage_range'];
                    $res['voltage_range'] = $line['voltage_range'];
                    $res['voltage_level'] = $line['voltage_level'];
                    $res['voltage_level_full'] = $line['voltage_level_full'];
                    $res['active'] = $line['active'];

                    break;
                }
            }

            if (count($res) == 0) {
                $res = null;
            }

        } catch (\Exception $e) {
        } finally {
            $db->Close();
        }

        return $res;
    }

    public function UpdateActive($defect_id, $active)
    {
        $res = false;

        $db = new Database();

        try {
            $db->Open();

            $query = "UPDATE public.\"defect\" SET active = :active where id = :defect_id ";

            $result = $db->Query($query,
                [
                    ":defect_id" => $defect_id,
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