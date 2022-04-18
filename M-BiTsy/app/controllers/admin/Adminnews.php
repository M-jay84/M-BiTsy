<?php

class Adminnews
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // News Default Page
    public function index()
    {
        // Get News Data
        $res = DB::raw('news', '*', '', 'ORDER BY added DESC');

        // Init Data
        $data = [
            'title' => Lang::T("NEWS"),
            'sql' => $res
        ];

        // Load View
        View::render('news/index', $data, 'admin');
    }

    // Add News Default Page
    public function add()
    {
        // Init Data
        $data = [
            'title' => Lang::T("CP_NEWS_ADD"),
        ];

        // Load View
        View::render('news/add', $data, 'admin');
    }

    // Add News Form Submit
    public function submit()
    {
        // Check User Input
        $body = $_POST["body"];
        $title = $_POST['title'];
        $added = $_POST["added"];
        
        // Check Correct Input
        if (!$body) {
            Redirect::autolink(URLROOT."/adminnews/add", Lang::T("ERR_NEWS_ITEM_CAN_NOT_BE_EMPTY"));
        }

        if (!$title) {
            Redirect::autolink(URLROOT."/adminnews/add", Lang::T("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"));
        }

        if (!$added) {
            $added = TimeDate::get_date_time();
        }

        // Insert News
        $afr = DB::insert('news', ['userid'=>Users::get('id'), 'added'=>$added, 'body'=>$body, 'title'=>$title]);
        if ($afr) {
            Redirect::autolink(URLROOT."/adminnews", Lang::T("CP_NEWS_ITEM_ADDED_SUCCESS"));
        } else {
            Redirect::autolink(URLROOT."/adminnews/add", Lang::T("CP_NEWS_UNABLE_TO_ADD"));
        }
    }

    // Edit News Default Page
    public function edit()
    {
        // Check User Input
        $newsid = (int) $_GET["newsid"];

        // Check Correct Input
        if (!Validate::Id($newsid)) {
            Redirect::autolink(URLROOT."/adminnews", sprintf(Lang::T("CP_NEWS_INVAILD_ITEM_ID").$newsid));
        }

        // Get News Data
        $res = DB::raw('news', '*', ['id'=>$newsid]);
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT."/adminnews", sprintf(Lang::T("CP_NEWS_NO_ITEM_WITH_ID").$newsid));
        }
        
        // Init Data
        $data = [
            'newsid' => $newsid,
            'res' => $res,
            'title' => Lang::T("CP_NEWS_EDIT")
        ];

        // Load View
        View::render('news/edit', $data, 'admin');
    }

    // Edit News Form Submit
    public function updated()
    {
        // Check User Input
        $newsid = (int) $_GET["id"];
        $body = $_POST['body'];
        $title = $_POST['title'];
        
        // Check Correct Input
        if ($body == "") {
            Redirect::autolink(URLROOT."/adminnews/edit", Lang::T("FORUMS_BODY_CANNOT_BE_EMPTY"));
        }

        if ($title == "") {
            Redirect::autolink(URLROOT."/adminnews/edit", Lang::T("ERR_NEWS_TITLE_CAN_NOT_BE_EMPTY"));
        }

        // Update News
        DB::update('news', ['body' =>$body, 'title' =>$title], ['id' => $newsid]);
        Redirect::autolink(URLROOT . "/adminnews", Lang::T("CP_NEWS_ITEM_WAS_EDITED_SUCCESS"));
    }

    // Delete News Form Submit
    public function newsdelete()
    {
        // Check User Input
        $newsid = (int) $_GET["newsid"];

        // Check Correct Input
        if (!Validate::Id($newsid)) {
            Redirect::autolink(URLROOT."/adminnews", sprintf(Lang::T("CP_NEWS_INVAILD_ITEM_ID").$newsid));
        }
        
        // Delete News
        DB::delete('news', ['id'=>$newsid]);
        DB::delete('comments', ['news'=>$newsid]);
        Redirect::autolink(URLROOT . "/adminnews", Lang::T("CP_NEWS_ITEM_DEL_SUCCESS"));
    }

}