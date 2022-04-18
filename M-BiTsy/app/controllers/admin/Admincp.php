<?php

class Admincp
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Admincp Default Page
    public function index()
    {
        // Init Data
        $data = [
            'title1' => 'Staff Panel',
            'title' => 'Staff Chat',
        ];

        // Load View
        View::render('admincp/index', $data, 'admin');
    }
    
}