<?php

class Adminpeer
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Peers Default Page
    public function index()
    {
        // Pagination
        $count = number_format(get_row_count("peers"));
        list($pagerbuttons, $limit) = Pagination::pager(50, $count, "/adminpeer?");
        $result = DB::raw('peers', '*', '', 'ORDER BY started DESC', "$limit");

        // Init Data
        $data = [
            'title' => Lang::T("Peers List"),
            'count1' => $count,
            'pagerbuttons' => $pagerbuttons,
            'result' => $result
        ];

        // Load View
        View::render('peer/index', $data, 'admin');
   }

}