<?php

class Helper
{

    public static function hashPass($pass)
    {
        return password_hash($pass, PASSWORD_BCRYPT);
    }

    public static function escape($string)
    {
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }

    public static function escapeUrl($url)
    {
        $ret = '';
        for ($i = 0; $i < strlen($url); $i += 2) {
            $ret .= '&' . $url[$i] . $url[$i + 1];
        }
        return $ret;
    }

    public static function htmlsafechars($txt = '')
    {
        $txt = preg_replace("/&(?!#[0-9]+;)(?:amp;)?/s", '&amp;', $txt);
        $txt = str_replace(["<", ">", '"', "'"], ["&lt;", "&gt;", "&quot;", '&#039;'], $txt);
        return $txt;
    }

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
    
    public static function priv($name, $descr)
    {
        if (Users::get("privacy") == $name) {
            return "<input type=\"radio\" name=\"privacy\" value=\"$name\" checked=\"checked\" /> $descr";
        }
        return "<input type=\"radio\" name=\"privacy\" value=\"$name\" /> $descr";
    }

    //DISPLAY NFO BLOCK
    public static function my_nfo_translate($nfo)
    {
        $trans = array(
            "\x80" => "&#199;", "\x81" => "&#252;", "\x82" => "&#233;", "\x83" => "&#226;", "\x84" => "&#228;", "\x85" => "&#224;", "\x86" => "&#229;", "\x87" => "&#231;", "\x88" => "&#234;", "\x89" => "&#235;", "\x8a" => "&#232;", "\x8b" => "&#239;", "\x8c" => "&#238;", "\x8d" => "&#236;", "\x8e" => "&#196;", "\x8f" => "&#197;", "\x90" => "&#201;",
            "\x91" => "&#230;", "\x92" => "&#198;", "\x93" => "&#244;", "\x94" => "&#246;", "\x95" => "&#242;", "\x96" => "&#251;", "\x97" => "&#249;", "\x98" => "&#255;", "\x99" => "&#214;", "\x9a" => "&#220;", "\x9b" => "&#162;", "\x9c" => "&#163;", "\x9d" => "&#165;", "\x9e" => "&#8359;", "\x9f" => "&#402;", "\xa0" => "&#225;", "\xa1" => "&#237;",
            "\xa2" => "&#243;", "\xa3" => "&#250;", "\xa4" => "&#241;", "\xa5" => "&#209;", "\xa6" => "&#170;", "\xa7" => "&#186;", "\xa8" => "&#191;", "\xa9" => "&#8976;", "\xaa" => "&#172;", "\xab" => "&#189;", "\xac" => "&#188;", "\xad" => "&#161;", "\xae" => "&#171;", "\xaf" => "&#187;", "\xb0" => "&#9617;", "\xb1" => "&#9618;", "\xb2" => "&#9619;",
            "\xb3" => "&#9474;", "\xb4" => "&#9508;", "\xb5" => "&#9569;", "\xb6" => "&#9570;", "\xb7" => "&#9558;", "\xb8" => "&#9557;", "\xb9" => "&#9571;", "\xba" => "&#9553;", "\xbb" => "&#9559;", "\xbc" => "&#9565;", "\xbd" => "&#9564;", "\xbe" => "&#9563;", "\xbf" => "&#9488;", "\xc0" => "&#9492;", "\xc1" => "&#9524;", "\xc2" => "&#9516;", "\xc3" => "&#9500;",
            "\xc4" => "&#9472;", "\xc5" => "&#9532;", "\xc6" => "&#9566;", "\xc7" => "&#9567;", "\xc8" => "&#9562;", "\xc9" => "&#9556;", "\xca" => "&#9577;", "\xcb" => "&#9574;", "\xcc" => "&#9568;", "\xcd" => "&#9552;", "\xce" => "&#9580;", "\xcf" => "&#9575;", "\xd0" => "&#9576;", "\xd1" => "&#9572;", "\xd2" => "&#9573;", "\xd3" => "&#9561;", "\xd4" => "&#9560;",
            "\xd5" => "&#9554;", "\xd6" => "&#9555;", "\xd7" => "&#9579;", "\xd8" => "&#9578;", "\xd9" => "&#9496;", "\xda" => "&#9484;", "\xdb" => "&#9608;", "\xdc" => "&#9604;", "\xdd" => "&#9612;", "\xde" => "&#9616;", "\xdf" => "&#9600;", "\xe0" => "&#945;", "\xe1" => "&#223;", "\xe2" => "&#915;", "\xe3" => "&#960;", "\xe4" => "&#931;", "\xe5" => "&#963;",
            "\xe6" => "&#181;", "\xe7" => "&#964;", "\xe8" => "&#934;", "\xe9" => "&#920;", "\xea" => "&#937;", "\xeb" => "&#948;", "\xec" => "&#8734;", "\xed" => "&#966;", "\xee" => "&#949;", "\xef" => "&#8745;", "\xf0" => "&#8801;", "\xf1" => "&#177;", "\xf2" => "&#8805;", "\xf3" => "&#8804;", "\xf4" => "&#8992;", "\xf5" => "&#8993;", "\xf6" => "&#247;",
            "\xf7" => "&#8776;", "\xf8" => "&#176;", "\xf9" => "&#8729;", "\xfa" => "&#183;", "\xfb" => "&#8730;", "\xfc" => "&#8319;", "\xfd" => "&#178;", "\xfe" => "&#9632;", "\xff" => "&#160;",
        );
        $trans2 = array("\xe4" => "&auml;", "\xF6" => "&ouml;", "\xFC" => "&uuml;", "\xC4" => "&Auml;", "\xD6" => "&Ouml;", "\xDC" => "&Uuml;", "\xDF" => "&szlig;");
        $all_chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $last_was_ascii = false;
        $tmp = "";
        $nfo = $nfo . "\00";
        for ($i = 0; $i < (strlen($nfo) - 1); $i++) {
            $char = $nfo[$i];
            if (isset($trans2[$char]) and ($last_was_ascii or strpos($all_chars, ($nfo[$i + 1])))) {
                $tmp = $tmp . $trans2[$char];
                $last_was_ascii = true;
            } else {
                if (isset($trans[$char])) {
                    $tmp = $tmp . $trans[$char];
                } else {
                    $tmp = $tmp . $char;
                }
                $last_was_ascii = strpos($all_chars, $char);
            }
        }
        return $tmp;
    }
	
	// try move message detail array
    public static function msgdetails($type, $arr = [])
    {
        // Whos Sender
        if ($arr["sender"] == Users::get('id')) {
            $sender = "Yourself";
        } elseif (Validate::Id($arr["sender"])) {
            $arr2 = DB::select('users', 'username', ['id'=>$arr['sender']]);
            $sender = "<a href=\"/profile?id=$arr[sender]\">" . ($arr2["username"] ? Users::coloredname($arr["sender"]) : "[Deleted]") . "</a>";
        } else {
            $sender = Lang::T("SYSTEM");
        }
        // Whos Reciever
        if ($arr["receiver"] == Users::get('id')) {
            $receiver = "Yourself";
        } elseif (Validate::Id($arr["receiver"])) {
            $arr2 = DB::select('users', 'username', ['id'=>$arr['receiver']]);
            $receiver = "<a href=\"" . URLROOT . "/profile?id=$arr[receiver]\">" . ($arr2["username"] ? Users::coloredname($arr2["receiver"]) : "[Deleted]") . "</a>";
        } else {
            $receiver = Lang::T("SYSTEM");
        }
        // Subject
        $subject = "<a href='" . URLROOT . "/message/read?&type=$type&id=" . $arr["id"] . "'><b>" . format_comment($arr["subject"]) . "</b></a>";
        $added = TimeDate::utc_to_tz($arr["added"]);
        // Unread
        if ($arr["unread"] == "yes") {
            $unread = "<i class='fa fa-file-text tticon-red' title='UnRead'></i>";
        } else {
            $unread = "<i class='fa fa-file-text' title='Read'></i>";
        }
        // Return In Array
        $arr = array($sender, $receiver, $subject, $unread, $added);
        return $arr;
    }

    public static function echotemplates()
    {
        $templates = "<option value=\"0\">---- " . Lang::T("NONE_SELECTED") . " ----</option>\n";
        $stmt = DB::raw('messages', '*', ['sender' =>Users::get('id'), 'location'=>'template'], 'ORDER BY `subject`');
        foreach ($stmt as $arr) {
            $templates .= "<option value=\"$arr[id]\">$arr[subject]</option>\n";
        }
        echo $templates;
    }

}