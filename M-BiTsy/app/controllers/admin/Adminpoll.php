<?php

class Adminpoll
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Polls Default Page
    public function index()
    {
        // Get Poll Data
        $query = DB::raw('polls', 'id,question,added', '', 'ORDER BY added DESC');

        // Init Data
        $data = [
            'title' => Lang::T("POLLS_MANAGEMENT"),
            'query' => $query,
        ];

        // Load View
        View::render('poll/index', $data, 'admin');
    }

    // Poll Results Default Page
    public function results()
    {
        // Get Poll Data
        $poll = DB::raw('pollanswers', '*', '', 'ORDER BY pollid DESC');

        // Init Data
        $data = [
            'title' => Lang::T("POLLS_MANAGEMENT"),
            'poll' => $poll,
        ];

        // Load View
        View::render('poll/results', $data, 'admin');
    }

    // Delete Poll Form Submit
    public function delete()
    {
        // Check User Input
        $id = (int) $_GET["id"];

        // Check Correct Input
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT."/adminpoll", sprintf(Lang::T("CP_NEWS_INVAILD_ITEM_ID").$id));
        }

        // Delete Poll
        DB::delete('polls', ['id'=>$id]);
        DB::delete('pollanswers', ['pollid'=>$id]);
        Redirect::autolink(URLROOT . "/adminpoll", Lang::T("Poll and answers deleted"));
    }

    // Add/Edit Poll Default Page
    public function add()
    {
        // Check User Input
        $pollid = (int) $_GET["pollid"];

        // Get Poll Data
        $res = DB::raw('polls', '*', ['id'=>$pollid]);

        // Init Data
        $data = [
            'title' => Lang::T("POLLS_MANAGEMENT"),
            'res' => $res,
            'id' => $pollid
        ];

        // Load View
        View::render('poll/add', $data, 'admin');
    }

    // Add/Edit Poll Form Submit
    public function save()
    {
        // Check User Input
        $subact = $_POST["subact"];
        $pollid = (int) $_POST["pollid"];

        $data = [
            'question' => $_POST["question"],
            'option0' => $_POST["option0"],
            'option1' => $_POST["option1"],
            'option2' => $_POST["option2"],
            'option3' => $_POST["option3"],
            'option4' => $_POST["option4"],
            'option5' => $_POST["option5"],
            'option6' => $_POST["option6"],
            'option7' => $_POST["option7"],
            'option8' => $_POST["option8"],
            'option9' => $_POST["option9"],
            'option10' => $_POST["option10"],
            'sort' => (int) $_POST["sort"],
            'added' => TimeDate::get_date_time()
        ];

        // Check Correct Input
        if (!$data['question'] || !$data['option0'] || !$data['option1']) {
            Redirect::autolink(URLROOT."/adminpoll", Lang::T("MISSING_FORM_DATA") . "!");
        }

        // Add Insert / Edit Update
        if ($subact == "edit") {
            if (!Validate::Id($pollid)) {
                Redirect::autolink(URLROOT."/adminpoll", Lang::T("INVALID_ID"));
            }

            DB::update("polls", $data, ['id'=>$pollid]);
        } else {
            DB::insert("polls", $data);
        }
        Redirect::autolink(URLROOT . "/adminpoll", Lang::T("COMPLETE"));
    }

}