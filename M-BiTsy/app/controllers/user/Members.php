<?php

class Members
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Members Default Page
    public function index()
    {
        // Check User
        if (Users::get("view_users") == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }

        // Check User Input
        $search = Input::get('search');
        $class = (int) Input::get('class');
        $letter = Input::get('letter');

        // Search Users
        $data = Users::search($search, $class, $letter);

        // Pagination
        $count = DB::run("SELECT COUNT(*) FROM users WHERE " . $data['query'])->fetchcolumn();
        list($pagerbuttons, $limit) = Pagination::pager(25, $count, URLROOT . "/members?$data[q]&");
        $results = Groups::getGroupsearch($data['query'], $limit);

        // Get Group Data
        $res = DB::run("SELECT group_id, level FROM `groups` ORDER BY group_id ASC");

        // Init Data
        $data = [
            'title' => 'Members',
            'getgroups' => $res,
            'results' => $results,
            'pagerbuttons' => $pagerbuttons,
        ];

        // Load View
        View::render('members/index', $data, 'user');
    }

}