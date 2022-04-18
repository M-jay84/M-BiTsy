<?php

class Validate
{
    // Check If Empty
    public static function isEmpty($data)
    {
        if (is_array($data)) {
            return empty($data);
        } elseif ($data == "") {
            return true;
        } else {
            return false;
        }
    }

    // Check Valid Email
    public static function Email($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        return true;
    }

    // Check File Name
    public static function Filename($name)
    {
        return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
    }

    // Check ID 
    public static function Id($id)
    {
        return is_numeric($id) && ($id > 0) && (floor($id) == $id);
    }

    // Check Valid User Name
    public static function username($username) {
		$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-";
		for ($i = 0; $i < strlen($username); ++$i)
			if (strpos($allowedchars, $username[$i]) === false)
			return false;
		return true;
    }

    // Check Valid Number
    public static function Int($id)
    {
        return is_numeric($id) && (floor($id) == $id);
    }

    // Sanitize String
    public static function cleanstr($s)
    {
        return filter_var($s, FILTER_FLAG_STRIP_LOW);
    }

}