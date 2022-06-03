<?php
class Bookmarks
{
    public static function select($target, $type = 'torrent')
    {
        $bookt = DB::column('bookmarks', 'COUNT(*)', ['targetid' => $target, 'type' => $type, 'userid' => Users::get('id')]);
        if ($bookt > 0) {
            print("<a href=" . URLROOT . "/bookmark/delete?target=$target><i class='fa fa-bookmark-o fa-2x' title='". Lang::T("Delete") ."'></i></a>");
        } else {
            print("<a href=" . URLROOT . "/bookmark/add?target=$target><i class='fa fa-bookmark fa-2x' title='Add Bookmark'></i></a>");
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

    public static function switch($type, $target)
    {
        if ($type === 'torrent') {
            if ((get_row_count("torrents", "WHERE id=$target")) > 0) {
                DB::insert('bookmarks', ['userid' => Users::get('id'), 'targetid' => $target, 'type' => 'torrent']);
                Redirect::autolink(URLROOT . "/torrent?id=$target", "Torrent was successfully bookmarked.");
            }
        } else {
            // if type forum ???
        }
    }
}
