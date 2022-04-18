<?php

class Input
{

    // Check Input Exist
    public static function exist($type = "POST")
    {
        switch ($type) {
            case "POST":
                return (!empty($_POST)) ? true : false;
                break;

            case "GET":
                return (!empty($_GET)) ? true : false;
                break;

            default:
                return false;
                break;
        }
    }

    // Get Input Value
    public static function get($value)
    {
        if (isset($_POST[$value])) {
            return trim(strip_tags(filter_input(INPUT_POST, $value)));
        } elseif (isset($_GET[$value])) {
            return trim(strip_tags(filter_input(INPUT_GET, $value)));
        }
        return "";
    }

}