<?php

class Adminlog
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_SUPERMODERATOR, 2);
    }

    // Log Default Page
    public function index()
    {
        // Check User Input
        $search = trim($_GET['search']);
        if ($search != '') {
            $where = "WHERE txt LIKE " . sqlesc("%$search%") . "";
        }

        // Pagination
        $count = Logs::countWhere($where);
        list($pagerbuttons, $limit) = Pagination::pager(25, $count, URLROOT."/adminlog?");
        $res = Logs::getAll($where, $limit);

        // Init Data
        $data = [
            'title' => Lang::T("Site Log"),
            'pagerbuttons' => $pagerbuttons,
            'res' => $res,
        ];

        // Load View
        View::render('log/index', $data, 'admin');
    }

    // Log Form Submit
    public function delete() {
        if ($_POST["delall"]) {
            DB::delete('log', '');
        } else {
            if (!@count($_POST["del"])) {
                Redirect::autolink(URLROOT."/adminlog", Lang::T("NOTHING_SELECTED"));
            }
            $ids = array_map("intval", $_POST["del"]);
            $ids = implode(", ", $ids);
            DB::deleteByIds('log', 'id', $ids);
        }
        Redirect::autolink(URLROOT . "/adminlog", Lang::T("CP_DELETED_ENTRIES"));
    }

}