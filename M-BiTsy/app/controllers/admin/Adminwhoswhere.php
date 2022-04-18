<?php

class Adminwhoswhere
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Whos Where Default Page
    public function index()
    {
        // Get Data
        $res = DB::run("SELECT `id`, `username`, `page`, `last_access`
                        FROM `users`
                        WHERE `enabled` = 'yes' AND `status` = 'confirmed' AND `page` != ''
                        ORDER BY `last_access`
                        DESC LIMIT 100");
        
        // Init Data
        $data = [
            'title' => 'Where are members',
            'res' => $res,
        ];

        // Load View
        View::render('whoswhere/index', $data, 'admin');
    }

}