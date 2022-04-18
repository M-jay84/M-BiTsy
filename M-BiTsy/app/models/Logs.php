<?php

class Logs
{

    public static function write($text)
    {
        DB::insert('log', ['added'=>TimeDate::get_date_time(), 'txt'=>$text]);
    }

    public static function countWhere($where)
    {
        $count = DB::run("SELECT COUNT(*) FROM log $where")->fetchColumn();
        return $count;
    }

    public static function getAll($where, $limit)
    {
        $stmt = DB::run("SELECT id, added, txt FROM log $where ORDER BY id DESC $limit");
        return $stmt;
    }

}