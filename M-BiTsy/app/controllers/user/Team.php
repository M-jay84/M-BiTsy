<?php

class Team
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }
    
    // Teams Default Page
    public function index()
    {
        // Get Teams Data
        $res = Teams::getTeams();

        // Check If Team
        if ($res->rowCount() == 0) {
            Redirect::autolink(URLROOT, Lang::T("NO_TEAM"));
        }
        
        // Init Data
        $data = [
            'title' => Lang::T("TEAM[1]"),
            'res' => $res
        ];
        
        // Load View
        View::render('team/index', $data, 'user');
    }

}