<?php

class Admincensor
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Censor Default Page
    public function index()
    {
        // Check Old/New Config
        if (Config::get('OLD_CENSOR')) {
            // Add Word
            if ($_POST['submit'] == 'Add Censor') {
                DB::insert('censor', ['word'=>$_POST['word'], 'censor'=>$_POST['censor']]);
            }

            // Delete Word
            if ($_POST['submit'] == 'Delete Censor') {
                DB::delete('censor', ['word'=>$_POST['censor']], 1);
            }

            // Get Censor Data
            $sres = DB::select('censor', 'word', '', 'ORDER BY word');

            // Init Data
            $data = [
              'title' => Lang::T("Censor"),
              'sres' => $sres,
            ];

            // Load View
            View::render('censor/oldcensor', $data, 'admin');

        } else {
            // Check User Input
            $to = isset($_GET["to"]) ? htmlentities($_GET["to"]) : $to = '';

            // Action Switch
            switch ($to) {
                case 'write':
                    if (isset($_POST["badwords"])) {
                        $f = fopen(LOGGER . "/censor.txt", "w+");
                        @fwrite($f, $_POST["badwords"]);
                        fclose($f);
                    }
                    Redirect::autolink(URLROOT . "/admincensor", Lang::T("SUCCESS"), "Censor Updated!");
                    break;
                    
                case '':
                case 'read':
                default:
                    $f = @fopen(LOGGER . "/censor.txt", "r");
                    $badwords = @fread($f, filesize(LOGGER . "/censor.txt"));
                    @fclose($f);
                    $data = [
                      'title' => Lang::T("Censor"),
                      'badwords' => $badwords,
                    ];
                    View::render('censor/newcensor', $data, 'admin');
                    break;
            }
        }
    }
    
}