<?php

class TimeDate
{
    // Function that calculates the Hours Minutes Seconds of a Timestamp 
    public static function mkprettytime($s)
    {
        if ($s < 0) {
            $s = 0;
        }

        $t = array();
        $t["day"] = floor($s / 86400);
        $s -= $t["day"] * 86400;
        $t["hour"] = floor($s / 3600);
        $s -= $t["hour"] * 3600;
        $t["min"] = floor($s / 60);
        $s -= $t["min"] * 60;
        $t["sec"] = $s;

        if ($t["day"]) {
            return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
        }
        if ($t["hour"]) {
            return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
        }
        return sprintf("%d:%02d", $t["min"], $t["sec"]);
    }

    // Get Time As INT According To TimeZone
    public static function gmtime()
    {
        return self::sql_timestamp_to_unix_timestamp(self::get_date_time());
    }

    // Convert Timestamp To INT
    public static function sql_timestamp_to_unix_timestamp($s)
    {
        return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
    }

    // Obtain Week Day Hour Minute Second From Int
    public static function get_elapsed_time($ts)
    {
        $mins = floor((self::gmtime() - $ts) / 60);
        $hours = floor($mins / 60);
        $mins -= $hours * 60;
        $days = floor($hours / 24);
        $hours -= $days * 24;
        $weeks = floor($days / 7);
        $days -= $weeks * 7;
        $t = "";
        if ($weeks > 0) {
            return "$weeks wk" . ($weeks > 1 ? "s" : "");
        }
        if ($days > 0) {
            return "$days day" . ($days > 1 ? "s" : "");
        }
        if ($hours > 0) {
            return "$hours hr" . ($hours > 1 ? "s" : "");
        }
        if ($mins > 0) {
            return "$mins min" . ($mins > 1 ? "s" : "");
        }
        return "< 1 min";
    }

    // Get Timestamp - Option Pass Format To Data
    public static function get_date_time($timestamp = 0)
    {
        if ($timestamp) {
            return date("Y-m-d H:i:s", $timestamp);
        } else {
            return gmdate("Y-m-d H:i:s");
        }
    }

    // Get Timestamp From User Time Zone
    public static function utc_to_tz($timestamp = 0)
    {
        global $tzs;
        if (method_exists("DateTime", "setTimezone")) {
            if (!$timestamp) {
                $timestamp = self::get_date_time();
            }
            $date = new DateTime($timestamp, new DateTimeZone("UTC"));
            $ZONE = $tzs[Users::get("tzoffset")][1] ?? "Europe/London";
            $date->setTimezone(new DateTimeZone($ZONE));
            return $date->format('Y-m-d H:i:s');
        }
        if (!is_numeric($timestamp)) {
            $timestamp = self::sql_timestamp_to_unix_timestamp($timestamp);
        }
        if ($timestamp == 0) {
            $timestamp = self::gmtime();
        }
        $timestamp = $timestamp + (Users::get('tzoffset') * 60);
        if (date("I")) {
            $timestamp += 3600;
        }
        // DST Fix
        return date("Y-m-d H:i:s", $timestamp);
    }

    // Get Int From User Time Zone
    public static function utc_to_tz_time($timestamp = 0)
    {
        global $tzs;
        if (method_exists("DateTime", "setTimezone")) {
            if (!$timestamp) {
                $timestamp = TimeDate::get_date_time();
            }
            $date = new DateTime($timestamp, new DateTimeZone("UTC"));
            $ZONE = $tzs[Users::get("tzoffset")][1] ?? "Europe/London";
            $date->setTimezone(new DateTimeZone($ZONE));
            return self::sql_timestamp_to_unix_timestamp($date->format('Y-m-d H:i:s'));
        }
        if (!is_numeric($timestamp)) {
            $timestamp = self::sql_timestamp_to_unix_timestamp($timestamp);
        }
        if ($timestamp == 0) {
            $timestamp = self::gmtime();
        }
        $timestamp = $timestamp + (Users::get('tzoffset') * 60);
        if (date("I")) {
            $timestamp += 3600;
        }
        // DST Fix
        return $timestamp;
    }

    // Get Interval Between 2 Dates As Int
    public static function DateDiff($start, $end)
    {
        if (!is_numeric($start)) {
            $start = self::sql_timestamp_to_unix_timestamp($start);
        }
        if (!is_numeric($end)) {
            $end = self::sql_timestamp_to_unix_timestamp($end);
        }
        return ($end - $start);
    }

    // Obtain Week Day Hour Minute Second From Timestamp
    public static function get_time_elapsed($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
        $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
        if (!$full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    // Drop Down for Time Zones
    public static function timeZoneDropDown($tzoffset)
    {
        global $tzs;
        $tz = '';
        ksort($tzs);
        reset($tzs);
        foreach($tzs as $key => $val) {
            if ($tzoffset == $key) {
                $tz .= "<option value=\"$key\" selected='selected'>$val[0]</option>\n";
            } else {
                $tz .= "<option value=\"$key\">$val[0]</option>\n";
            }
        }
        return $tz;
    }
	
	// $type=date/string || $target=sqlresult || $amount=add/sub date
	public static function modify($type, $target, $amount)
    {
        if ($type == 'date') {
            $date = date_create($target);
            date_modify($date, $amount);
        } elseif ($type == 'time') {
            $date = date_create();
            date_timestamp_set($date, $target);
            date_modify($date, $amount);
        }
            
        return date_format($date, 'Y-m-d H:i:s');
 
    }
}