<?php

class Adminavatar
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Avatar Default Page
    public function index()
    {
        // Pagination
        $count = DB::run("SELECT count(*) FROM users WHERE enabled=? AND avatar !=?", ['yes', ''])->fetchColumn();
        list($pagerbuttons, $limit) = Pagination::pager(50, $count, URLROOT.'/adminavatar?');
        $res = DB::run("SELECT username, id, avatar FROM users WHERE enabled='yes' AND avatar !='' $limit");

        // Init Data
        $data = [
            'title' => "Avatar Log",
            'res' => $res,
            'pagerbuttons' => $pagerbuttons,
        ];

        // Load View
        View::render('avatar/index', $data, 'admin');
    }

}