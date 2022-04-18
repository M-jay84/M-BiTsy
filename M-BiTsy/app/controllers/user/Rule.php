<?php

class Rule
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 1);
    }

    // Rule Default Page
    public function index()
    {
        // Get Rules
        $res = DB::run("SELECT * FROM `rules` ORDER BY `id`");
        
        // Init Data
        $data = [
            'title' => 'Rules',
            'res' => $res
        ];
        
        // Load View
        View::render('rule/index', $data, 'user');
    }

}