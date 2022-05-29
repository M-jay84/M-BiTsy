<?php
class Torrents
{

    // Get All User Array
    public static function getAll($id)
    {
        $row = DB::run(" SELECT torrents.anon, torrents.seeders, torrents.tube, torrents.banned,
            torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.torrentlang, torrents.category,
            torrents.last_action, torrents.numratings, torrents.name, torrents.tmdb, torrents.imdb,
            torrents.owner, torrents.save_as, torrents.descr, torrents.visible,
            torrents.size, torrents.added, torrents.views, torrents.hits,
            torrents.times_completed, torrents.id, torrents.type, torrents.external,
            torrents.image1, torrents.image2, torrents.announce, torrents.numfiles,
            torrents.freeleech, torrents.vip, torrents.sticky,
            IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1))
            AS rating, torrents.numratings, categories.name
            AS cat_name, torrentlang.name
            AS lang_name, torrentlang.image
            AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy
            FROM torrents
            LEFT JOIN categories
            ON torrents.category = categories.id
            LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id
            LEFT JOIN users ON torrents.owner = users.id
            WHERE torrents.id = $id");
        $user1 = $row->fetchAll(PDO::FETCH_ASSOC);
        return $user1;
    }

    public static function getTorrentByCat($where, $parent_check, $orderby, $limit)
    {
        $row = DB::run("SELECT torrents.id, torrents.anon, torrents.announce, torrents.tube,  torrents.tmdb, torrents.category, torrents.sticky, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed,
	                           torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name
	                    AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy,
	                    IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1))
	                    AS rating FROM torrents
	                    LEFT JOIN categories
	                    ON category = categories.id
	                    LEFT JOIN users
	                    ON torrents.owner = users.id
	                    $where $parent_check $orderby $limit");
        return $row;
    }

    public static function getCatByParent()
    {
        $row = DB::run("SELECT distinct parent_cat  FROM categories ORDER BY parent_cat");
        return $row;
    }

    public static function getCatSortAll($where, $date_time, $orderby, $limit)
    {
        $row = DB::run("SELECT torrents.id, torrents.anon, torrents.category, torrents.sticky, torrents.tmdb, torrents.tube, torrents.tube, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size,
                               torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name
                        AS cat_name, categories.parent_cat
                        AS cat_parent, categories.image
                        AS cat_pic, users.username, users.privacy
                        FROM torrents
                        LEFT JOIN categories
                        ON category = categories.id
                        LEFT JOIN users
                        ON torrents.owner = users.id $where AND torrents.added>='$date_time' $orderby $limit");
        return $row;
    }

    public static function getCatwhere($where)
    {
        $row = DB::run("SELECT COUNT(*) FROM torrents LEFT JOIN categories ON category = categories.id $where")->fetchColumn();
        return $row;
    }

    public static function updateComments($id, $var)
    {
        if ($var == 'sub') {
            $row = DB::run("SELECT comments FROM torrents WHERE id=$id")->fetch();
            DB::run("UPDATE torrents SET comments = $row[comments] - 1 WHERE id = $id");
        } elseif ($var == 'add') {
            DB::run("UPDATE torrents SET comments = comments + 1 WHERE id = $id");
        }
    }

    public static function search($where, $orderby, $limit, $params)
    {
        $row = DB::run("SELECT torrents.id, torrents.anon, torrents.announce, torrents.tube,  torrents.tmdb, torrents.category, torrents.sticky, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed,
        torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name
        AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy,
        IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1))
        AS rating FROM torrents
        LEFT JOIN categories
        ON category = categories.id
        LEFT JOIN users
        ON torrents.owner = users.id
        $where $orderby $limit", $params);
        return $row;
    }

    public static function catalog($query, $limit)
    {
    $stmt = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, 
    IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings,
     categories.name AS cat_name, 
     torrentlang.name AS lang_name, 
     torrentlang.image AS lang_image, 
     categories.parent_cat as cat_parent, 
     users.username, users.donated, users.warned, users.privacy, snatched.tid, snatched.uid, snatched.upload, snatched.download, snatched.start_time, snatched.upload_time, snatched.last_time
     FROM torrents 
     LEFT JOIN categories ON torrents.category = categories.id 
     LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id 
     LEFT JOIN users ON torrents.owner = users.id 
     LEFT JOIN snatched ON users.id = snatched.uid
     WHERE $query AND visible != ? 
     ORDER BY name ASC $limit", ['no']);
     return $stmt;
    }

    // Function To Delete A Torrent
    public static function deletetorrent($id)
    {
        $row = DB::select('torrents', 'image1,image2', ['id'=>$id]);
        foreach (explode(".", "peers.comments.ratings.files") as $x) {
            DB::delete($x, ['torrent' =>$id]);
        }
        DB::delete('completed', ['torrentid' =>$id]);
        if (file_exists(UPLOADDIR . "/torrents/$id.torrent")) {
            unlink(UPLOADDIR . "/torrents$id.torrent");
        }
        if ($row["image1"]) {
            unlink(UPLOADDIR . "/images/" . $row["image1"]);
        }
        if ($row["image2"]) {
            unlink(UPLOADDIR . "/images/" . $row["image2"]);
        }
        @unlink(UPLOADDIR . "/nfos/$id.nfo");
        DB::delete('torrents', ['id' =>$id]);
        DB::delete('reports', ['votedfor' =>$id, 'type' => 'torrent']);
        DB::delete('snatched', ['tid' =>$id]);
		DB::delete('bookmarks', ['targetid' =>$id, 'type'=>'torrent']);
        DB::delete('thanks', ['thanked' =>$id, 'type'=>'torrent']);
        DB::delete('likes', ['liked' =>$id, 'type'=>'torrent']);
    }

}