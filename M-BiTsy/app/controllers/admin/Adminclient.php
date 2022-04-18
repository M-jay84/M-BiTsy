<?php

class Adminclient
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Client Default Page
    public function index()
    {
        // Ban Client
        if (isset($_POST['ban'])) {
            DB::insert('clients', ['agent_name'=>$_POST['ban'], 'hits'=>1, 'ins_date'=>TimeDate::get_date_time()]);
            Redirect::autolink(URLROOT . "/adminclient/banned", Lang::T("SUCCESS"));
        }

        // Get Client Data
        $res11 = DB::select('peers', 'client, peer_id', '', 'ORDER BY client');

        // Init Data
        $data = [
            'title' => Lang::T("Clients"),
            'res11' => $res11,
        ];
        
        // Load View
        View::render('client/index', $data, 'admin');
    }

    // Banned Client Default Page
    public function banned()
    {
        // Un-Ban Client
        if (isset($_POST['unban'])) {
            foreach ($_POST['unban'] as $deleteid) {
                DB::delete('clients', ['agent_id'=>$deleteid]);
            }
            Redirect::autolink(URLROOT . "/adminclient/banned", Lang::T("SUCCESS"));
        }

        // Get Client Data
        $sql = DB::all('clients', '*', '');

        // Init Data
        $data = [
            'title' => Lang::T("Clients"),
            'sql' => $sql,
        ];
        
        // Load View
        View::render('client/banned', $data, 'admin');
    }

}