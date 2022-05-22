<?php
class Snatched
{
    public static function join($tid, $limit)
    {
        $res = DB::run("SELECT
        users.id,
        users.username,
        users.class,
        snatched.uid as uid,
        snatched.tid as tid,
        snatched.upload,
        snatched.download,
        snatched.start_time,
        snatched.upload_time,
        snatched.last_time,
        snatched.completed,
        snatched.hnr,
        (
            SELECT seeder
            FROM peers
            WHERE torrent = tid AND userid = uid LIMIT 1
        ) AS seeding
        FROM
        snatched
        INNER JOIN users ON snatched.uid = users.id
        INNER JOIN torrents ON snatched.tid = torrents.id
        WHERE
        users.status = 'confirmed' AND
        torrents.banned = 'no' AND snatched.tid = '$tid'
        ORDER BY start_time DESC $limit");

        return $res;
    }
	
	
    public static function join1($uid)
    {
        $res = DB::run("SELECT 
                    snatched.tid as tid, 
                    torrents.name, 
                    torrents.size, 
	                snatched.upload, 
                    snatched.download, 
		            snatched.last_time, 
		            snatched.hnr, 
		            users.uploaded, 
		            users.seedbonus, 
		            users.modcomment 
		            FROM snatched 
		            INNER JOIN users ON snatched.uid = users.id 
		            INNER JOIN torrents ON snatched.tid = torrents.id
		            WHERE users.status = 'confirmed'
		            AND snatched.uid = '$uid'
		            AND snatched.hnr = 'yes'
		            AND snatched.done = 'no'
		            ORDER BY start_time DESC");

        return $res;
    }
	
	public static function join2($uid, $LIMIT = 25)
    {
        $res = DB::run("SELECT
				snatched.tid as tid,
				torrents.name,                                      
				snatched.upload,
				snatched.download,
				snatched.start_time,
				snatched.upload_time,
				snatched.last_time,
				snatched.completed,
				snatched.hnr,
				(
					SELECT seeder
					FROM peers
					WHERE torrent = tid AND userid = $uid LIMIT 1
				) AS seeding
				FROM
				snatched
				INNER JOIN users ON snatched.uid = users.id
				INNER JOIN torrents ON snatched.tid = torrents.id
				WHERE
				users.status = 'confirmed' AND
				torrents.banned = 'no' AND snatched.uid = '$uid'
				ORDER BY start_time DESC $limit");

        return $res;
    }
	
}