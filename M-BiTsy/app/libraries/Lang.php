<?php

class Lang
{

    // Translate String To Language
    public static function T($s)
    {
        global $LANG;
        if ($ret = (isset($LANG[$s]) ? $LANG[$s] : null)) {
            return $ret;
        }
        if ($ret = (isset($LANG["{$s}[0]"]) ? $LANG["{$s}[0]"] : null)) {
            return $ret;
        }
        return $s;
    }

    // Translate INT To Language - pureil
    public static function N($s, $num)
    {
        global $LANG;
        $num = (int) $num;
        $plural = str_replace("n", $num, $LANG["PLURAL_FORMS"]);
        $i = eval("return intval($plural);");
        if ($ret = (isset($LANG["{$s}[$i]"]) ? $LANG["{$s}[$i]"] : null)) {
            return $ret;
        }
        return $s;
    }

    // Add Letter Search
    public static function letters()
    {
        print("<p class=text-center>");
        // Print Each Letter
        for ($i = 97; $i < 123; ++$i) {
            $l = chr($i);
            $L = chr($i - 32);
            if ($l == $_GET['letter']) {
                print("<b><font color=#ff0000>$L</font></b>\n");
            } else {
                print("<a href=?letter=$l><b>$L</b></a>\n");
            }
        }
        print("</p>");
    }
}
