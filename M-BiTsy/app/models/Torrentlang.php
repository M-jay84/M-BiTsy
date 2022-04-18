<?php

class Torrentlang
{

    // Get Torrent Lang Array
    public static function langlist()
    {
        $ret = array();
        $stmt = DB::raw('torrentlang', 'id, name, image', '', 'ORDER BY sort_index, id');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ret[] = $row;
        }
        return $ret;
    }

    // Torrent Lang Drop Down
    public static function select()
    {
        $language = "<select name=\"lang\">\n<option value=\"0\">" . Lang::T("UNKNOWN_NA") . "</option>\n";
        $langs = self::langlist();
        foreach ($langs as $row) {
            $language .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
        }
        $language .= "</select>\n";
        return $language;
    }
    
}