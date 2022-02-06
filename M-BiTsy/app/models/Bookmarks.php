<?php
class Bookmarks
{
    public static function select($target, $type = 'torrent')
    {
        $bookt = DB::column('bookmarks', 'COUNT(*)', ['targetid' =>$target, 'type' =>$type, 'userid'=>Users::get('id')]);
        if ($bookt > 0) {
            print("<a href=".URLROOT."/bookmark/delete?target=$target><button type='button' class='btn btn-sm ttbtn'>Delete Bookmark</button></a>");
        } else {
            print("<a href=".URLROOT."/bookmark/add?target=$target><button type='button' class='btn btn-sm ttbtn'>Add Bookmark</button></a>");
        }
    }

    public static function join($limit, $id)
    {
        $stmt = DB::run("SELECT bookmarks.id as bookmarkid,
        torrents.size,
        torrents.freeleech,
        torrents.external,
        torrents.id,
        torrents.category,
        torrents.name,
        torrents.filename,
        torrents.added,
        torrents.banned,
        torrents.comments,
        torrents.seeders,
        torrents.leechers,
        torrents.times_completed,
        categories.name AS cat_name,
        categories.parent_cat AS cat_parent,
        categories.image AS cat_pic
        FROM bookmarks
        LEFT JOIN torrents ON bookmarks.targetid = torrents.id
        LEFT JOIN categories ON category = categories.id
        WHERE bookmarks.userid = ?
        ORDER BY added DESC 
        $limit", [$id]);

        return $stmt;
    }
}