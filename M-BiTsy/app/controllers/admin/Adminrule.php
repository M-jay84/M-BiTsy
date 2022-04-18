<?php

class Adminrule
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Rules Default Page
    public function index()
    {
        // Get Rules Data
        $res = DB::raw('rules', '*', '', 'ORDER BY id');

        // Init Data
        $data = [
            'title' => Lang::T("SITE_RULES_EDITOR"),
            'res' => $res,
        ];

        // Load View
        View::render('rule/index', $data, 'admin');
    }

    // Edit Rules Default Page
    public function edit()
    {
        // Edit Rules Form Submit
        if ($_GET["save"] == "1") {
            // Check User Input
            $id = (int) $_POST["id"];
            $title = $_POST["title"];
            $text = $_POST["text"];
            $public = $_POST["public"];
            $class = $_POST["class"];

            // Update Rule
            DB::update('rules', ['title' =>$title, 'text' =>$text,'public' =>$public, 'class' =>$class], ['id' => $id]);
            Logs::write("Rules have been changed by ".Users::get('username')."");
            Redirect::autolink(URLROOT."/adminrule", "Rules edited ok<br /><br /><a href=" . URLROOT . "/adminrule>Back To Rules</a>");
        }

        $id = (int) $_POST["id"];

        // Get Rules Data
        $res = DB::raw('rules', '*', ['id'=>$id]);
        
        // Init Data
        $data = [
            'title' => Lang::T("SITE_RULES_EDITOR"),
            'id' => $id,
            'res' => $res,
        ];

        // Load View
        View::render('rule/edit', $data, 'admin');
    }

    // Add Rules Default Page
    public function addsect()
    {
        // Add Rules Form Submit
        if ($_GET["save"] == "1") {
            $title = $_POST["title"];
            $text = $_POST["text"];
            $public = $_POST["public"];
            $class = $_POST["class"];
            DB::insert('rules', ['title'=>$title, 'text'=>$text, 'public'=>$public, 'class'=>$class]);
            Redirect::autolink(URLROOT."/adminrule", "New Section Added<br /><br /><a href=" . URLROOT . "/adminrule>Back To Rules</a>");
        }

        // Init Data
        $data = [
            'title' => Lang::T("SITE_RULES_EDITOR"),
        ];

        // Load View
        View::render('rule/addsect', $data, 'admin');
    }

}