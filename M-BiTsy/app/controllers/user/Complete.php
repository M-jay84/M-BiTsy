<?php

class Complete
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }
    
    // Who Completed Torrent Default Page
    public function index()
    {
        // Check User Input
        $id = (int) Input::get("id");

        // Check Permission
        if (Users::get("view_torrents") == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }

        // Check Torrent
        $row = DB::select('torrents', 'name, external, banned', ['id'=>$id]);
        if ((!$row) || ($row["banned"] == "yes" && Users::get("edit_torrents") == "no")) {
            Redirect::autolink(URLROOT, Lang::T("TORRENT_NOT_FOUND"));
        }
        if ($row["external"] == "yes") {
            Redirect::autolink(URLROOT, Lang::T("THIS_TORRENT_IS_EXTERNALLY_TRACKED"));
        }

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
        torrents.banned = 'no' AND snatched.tid = '$id' AND snatched.completed != 0
        ORDER BY start_time DESC");
        
        if ($res->rowCount() == 0) {
            Redirect::autolink(URLROOT, Lang::T("NO_DOWNLOADS_YET"));
        }

        // Set Title
        $title = sprintf(Lang::T("COMPLETED_DOWNLOADS"), CutName($row["name"], 40));

        // Init Data
        $data = [
            'title' => $title,
            'res' => $res,
            'id' => $id,
        ];

        // Load View
        View::render('complete/index', $data, 'user');
    }

}