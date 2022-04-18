<?php

class Adminbonus
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Bonus Default Page
    public function index()
    {
        // Delete Option
        if ($_POST['do'] == "del") {
            if (!@count($_POST["ids"])) {
                Redirect::autolink(URLROOT . '/adminbonus', "select nothing.");
            }
            $ids = array_map("intval", $_POST["ids"]);
            $ids = implode(", ", $ids);
            DB::deleteByIds('bonus', 'id', $ids);
            Redirect::autolink(URLROOT."/adminbonus", "deleted entries");
        }

        // Pagination
        $count = get_row_count("bonus");
        list($pagerbuttons, $limit) = Pagination::pager(10, $count, 'adminbonus&amp;');
        $res = DB::raw('bonus', 'id, title, cost, value, descr, type', '', "ORDER BY `type` $limit");

        // Init Data
        $data = [
            'title' => Lang::T("Seedbonus Manager"),
            'count' => $count,
            'pagerbuttons' => $pagerbuttons,
            'limit' => $limit,
			'res' => $res,
        ];

        // Load View
        View::render('bonus/index', $data, 'admin');
    }

    // Bonus Edit/Add Default Page
    public function edit()
    {
        // Check User Input
        $id = Input::get("id");

        // Get Bonus Data
        $row = null;
        if (Validate::Id($id)) {
            $res = DB::raw('bonus', 'id, title, cost, value, descr, type', ['id'=>$id]);
            $row = $res->fetch(PDO::FETCH_LAZY);
        }

        // Form Submit
        if (Input::exist()) {
            if (empty($_POST['title']) or empty($_POST['descr']) or empty($_POST['type']) or !is_numeric($_POST['cost'])) {
                Redirect::autolink($_SERVER['HTTP_REFERER'], "missing information.");
            }

            // Check User Input
            $title = Input::get("title");
            $descr = Input::get("descr");
            $cost = Input::get("cost");
            $value = $_POST["type"] == "traffic" ? strtobytes($_POST["value"]) : (int) $_POST["value"];
            $type = Input::get("type");

            // Insert/Update Bonus
            if ($row == null) {
                DB::insert('bonus', ['title'=>$title, 'descr'=>$descr, 'cost'=>$cost, 'value'=>$value, 'type'=>$type]);
            } else {
                DB::update('bonus', ['title' => $title, 'descr' => $descr, 'cost' => $cost, 'value' => $value, 'type' => $type], ['id' => $id]);
            }
            Redirect::autolink(URLROOT . "/adminbonus", "Updating the bonus seed.");
        }

        // Init Data
        $data = [
            'title' => Lang::T("Seedbonus Manager"),
            'row' => $row,
        ];
        
        // Load View
        View::render('bonus/edit', $data, 'admin');
    }

}