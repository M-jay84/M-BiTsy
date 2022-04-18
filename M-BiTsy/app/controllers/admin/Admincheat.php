<?php

class Admincheat
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Cheats Default Page
    public function index()
    {
        // Init Data
        $data = [
            'title' => Lang::T("Possible Cheater Detection"),
        ];

        // Load View
        View::render('cheat/index', $data, 'admin');
    }

    // Cheats Form Submit
    public function result()
    {
        // Check User Input
        $megabts = (int) $_POST['megabts'];
        $daysago = (int) $_POST['daysago'];

        if ($daysago && $megabts) {
            $timeago = 84600 * $daysago; //last 7 days
            $bytesover = 1048576 * $megabts; //over 500MB Upped
            $result = DB::run("SELECT * FROM users WHERE UNIX_TIMESTAMP('?') - UNIX_TIMESTAMP(added) < ? AND status=? AND uploaded > ? ORDER BY uploaded DESC ", [TimeDate::get_date_time(), $timeago, 'confirmed', $bytesover]);
            $num = $result->rowCount(); // how many uploaders
            $message = "<p>" . $num . " Users with found over last " . $daysago . " days with more than " . $megabts . " MB (" . $bytesover . ") Bytes Uploaded.</p>";
            $zerofix = $num - 1; // remove one row because mysql starts at zero
            
            if ($num > 0) {
                // Init Data
                $data = [
                    'title' => Lang::T("Possible Cheater Detection"),
                    'result' => $result,
                    'zerofix' => $zerofix,
                    'message' => $message
                    ];
                    
                    // Load View
                    View::render('user/cheatresult', $data, 'admin');
            } else {
                Redirect::autolink(URLROOT . '/admincheat', $message);
            } 

        } else {
            // Init Data
            $data = [
                'title' => Lang::T("Possible Cheater Detection"),
            ];

            // Load View
            View::render('cheat/cheatform', $data, 'admin');
        }
    }

}