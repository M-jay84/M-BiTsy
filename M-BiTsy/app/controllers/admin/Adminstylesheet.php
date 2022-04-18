<?php

class Adminstylesheet
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_ADMINISTRATOR, 2);
    }

    // Themes Default Page
    public function index()
    {
        // Get Themes Data
        $res = DB::raw('stylesheets', '*', '');

        // Init Data
        $data = [
            'title' => Lang::T("THEME_MANAGEMENT"),
            'sql' => $res,
        ];

        // Load View
        View::render('stylesheet/index', $data, 'admin');
    }

    // Add Theme Default Page
    public function add()
    {
        if (Input::exist()) {
            // Check Correct Input
            if (empty($_POST['name'])) {
                Redirect::autolink(URLROOT . "/adminstylesheet/add", Lang::T("THEME_NAME_WAS_EMPTY"));
            }
            if (empty($_POST['uri'])) {
                Redirect::autolink(URLROOT . "/adminstylesheet/add", Lang::T("THEME_FOLDER_NAME_WAS_EMPTY"));
            }

            // Add Theme
            $qry = DB::insert('stylesheets', ['name'=>$_POST["name"], 'uri'=>$_POST["uri"]]);
            if ($qry) {
                Redirect::autolink(URLROOT . "/adminstylesheet/add", "Theme '" . htmlspecialchars($_POST["name"]) . "' added.");
            } elseif ($qry->errorCode() == 1062) {
                Redirect::autolink(URLROOT . "/adminstylesheet/add", Lang::T("THEME_ALREADY_EXISTS"));
            } else {
                 Redirect::autolink(URLROOT . "/adminstylesheet/add", Lang::T("THEME_NOT_ADDED_DB_ERROR") . " " . $qry->errorInfo());
            }
        }

        // Init Data
        $data = [
            'title' => Lang::T("Theme"),
        ];

        // Load View
        View::render('stylesheet/add', $data, 'admin');
    }

    // Delete Theme Form Submit
    public function delete()
    {
        if (!@count($_POST["ids"])) {
            Redirect::autolink(URLROOT . "/adminstylesheet", Lang::T("NOTHING_SELECTED"));
        }

        $ids = array_map("intval", $_POST["ids"]);
        $ids = implode(', ', $ids);
        DB::deleteByIds('stylesheets', 'id', $ids);
        DB::run("UPDATE `users` SET `stylesheet` = " . Config::get('DEFAULTTHEME') . " WHERE stylesheet NOT IN (SELECT id FROM stylesheets)");
        Redirect::autolink(URLROOT . "/adminstylesheet", Lang::T("THEME_SUCCESS_THEME_DELETED"));
    }

}