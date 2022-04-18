<?php
class Blocks
{
    public static function left()
    {
        $TTCache = new Cache();
        if (($blocks = $TTCache->get("block/blocks_left", 900)) === false) {
            $res = DB::run("SELECT * FROM blocks WHERE position='left' AND enabled=1 ORDER BY sort");
            $blocks = array();
            while ($result = $res->fetch(PDO::FETCH_LAZY)) {
                $blocks[] = $result["name"];
            }
            $TTCache->Set("block/blocks_left", $blocks, 900);
        }
        foreach ($blocks as $blockfilename) {
            $url = $_GET['url'] ?? '';
            if (!in_array($url, ISURL)) {
                include "../app/views/user/block/" . $blockfilename . "_block.php";
            }
        }
    }

    public static function right()
    {
        $TTCache = new Cache();
        if (($blocks = $TTCache->get("block/blocks_right", 900)) === false) {
            $res = DB::run("SELECT * FROM blocks WHERE position='right' AND enabled=1 ORDER BY sort");
            $blocks = array();
            while ($result = $res->fetch(PDO::FETCH_LAZY)) {
                $blocks[] = $result["name"];
            }
            $TTCache->Set("block/blocks_right", $blocks, 900);
        }
        foreach ($blocks as $blockfilename) {
            $url = $_GET['url'] ?? '';
            if (!in_array($url, ISURL)) {
                include "../app/views/user/block/" . $blockfilename . "_block.php";
            }
        }
    }

    public static function middle()
    {
        $TTCache = new Cache();
        if (($blocks = $TTCache->get("block/blocks_middle", 900)) === false) {
            $res = DB::run("SELECT * FROM blocks WHERE position='middle' AND enabled=1 ORDER BY sort");
            $blocks = array();
            while ($result = $res->fetch(PDO::FETCH_LAZY)) {
                $blocks[] = $result["name"];
            }
            $TTCache->Set("block/blocks_middle", $blocks, 900);
        }
        foreach ($blocks as $blockfilename) {
            $url = $_GET['url'] ?? '';
            if (!in_array($url, ISURL)) {
                include "../app/views/user/block/" . $blockfilename . "_block.php";
            }
        }
    }

    public static function resort($place)
    {
        $sortleft = DB::run("SELECT sort, id FROM blocks WHERE position=? AND enabled=? ORDER BY sort ASC",[$place,1]);
        $i = 1;
        while ($sort = $sortleft->fetch(PDO::FETCH_ASSOC)) {
            DB::run("UPDATE blocks SET sort = $i WHERE id=" . $sort["id"]);
            $i++;
        }
    }

    public static function getposition($position)
    {
        $getposition = DB::run("SELECT position FROM blocks
                               WHERE position=? AND enabled=1", [$position])->rowCount() + 1;
        return $getposition;
    }

}