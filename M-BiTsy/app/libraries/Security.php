<?php

class Security
{

    // Hash Password
    public static function hashPass($pass)
    {
        return password_hash($pass, PASSWORD_BCRYPT);
    }

    // Sanitize String
    public static function escape($string)
    {
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }

    // Escape URL
    public static function escapeUrl($url)
    {
        $ret = '';
        for ($i = 0; $i < strlen($url); $i += 2) {
            $ret .= '&' . $url[$i] . $url[$i + 1];
        }
        return $ret;
    }

    // Sanitize String
    public static function htmlsafechars($txt = '')
    {
        $txt = preg_replace("/&(?!#[0-9]+;)(?:amp;)?/s", '&amp;', $txt);
        $txt = str_replace(["<", ">", '"', "'"], ["&lt;", "&gt;", "&quot;", '&#039;'], $txt);
        return $txt;
    }

    // Create Random String
    public static function mksecret($len = 20)
    {
        $chars = array_merge(range(0, 9), range("A", "Z"), range("a", "z"));
        shuffle($chars);
        $x = count($chars) - 1;
        for ($i = 1; $i <= $len; $i++) {
            $str .= $chars[mt_rand(0, $x)];
        }
        return $str;
    }
    
    // Privacy Level
    public static function priv($name, $descr)
    {
        if (Users::get("privacy") == $name) {
            return "<input type=\"radio\" name=\"privacy\" value=\"$name\" checked=\"checked\" /> $descr";
        }
        return "<input type=\"radio\" name=\"privacy\" value=\"$name\" /> $descr";
    }

}