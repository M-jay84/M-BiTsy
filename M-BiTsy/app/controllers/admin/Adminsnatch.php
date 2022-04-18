<?php

class Adminsnatch
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Hit & Runs Default Page
    public function index()
    {
        // Delete
        if ($_POST['do'] == 'delete') {
            if (!@count($_POST['ids'])) {
                Redirect::autolink(URLROOT . "/adminsnatch", "Nothing Selected.");
            }
            $ids = array_map('intval', $_POST['ids']);
            $ids = implode(',', $ids);
            DB::run("UPDATE snatched SET ltime = '86400', hnr = 'no', done = 'yes' WHERE `sid` IN ($ids)");
            Redirect::autolink(URLROOT . "/Adminsnatch", "Entries deleted.");
        }

        if (Config::get('HNR_ON')) {

            // Pagination
            $count = DB::column('snatched', 'count(*)', ['hnr'=>'yes']);
            list($pagerbuttons, $limit) = Pagination::pager(30, $count, "Adminsnatch?");
            $res = DB::run("SELECT *,s.tid FROM users u left join snatched s on s.uid=u.id  where hnr='yes' ORDER BY s.uid DESC $limit");

            // Init Data
            $data = [
                'title' => "List of Hit and Run",
                'count' => $count,
                'pagerbuttons' => $pagerbuttons,
                'res' => $res,
            ];

            // Load View
            View::render('snatch/hitnrun', $data, 'admin');

        } else {
            Redirect::autolink(URLROOT, "Hit & Run Disabled in Config.php (mod in progress)");
        }
    }

}