<?php

class Admincomment
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Comments Default Page
    public function index()
    {
        // Pagination
        $count = get_row_count("comments");
        list($pagerbuttons, $limit) = Pagination::pager(10, $count, URLROOT."/admincomment?");
        $res = Comments::graball($limit);
        
        // Init Data
        $data = [
            'title' => Lang::T("TORRENT_COMMENTS"),
            'res' => $res,
            'pagerbuttons' => $pagerbuttons,
            'count' => $count,
        ];

        // Load View
        View::render('comment/index', $data, 'admin');
    }

}