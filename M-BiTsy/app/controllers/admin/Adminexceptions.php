<?php

class Adminexceptions
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_ADMINISTRATOR, 2);
    }

    // Exception Default Page
    public function index() {
        // Get File
        $exceptionfilelocation = LOGGER."/exception_log.txt";
        $filegetcontents = file_get_contents($exceptionfilelocation);
        $errorlog = htmlspecialchars($filegetcontents);

        // Create File
        function make_content_file($exceptionfilelocation, $content, $opentype = "w")
        {
            $fp_file = fopen($exceptionfilelocation, $opentype);
            fputs($fp_file, $content);
            fclose($fp_file);
        }

        if (Input::exist()) {
            $newcontents = $_POST['newcontents'];
            make_content_file($exceptionfilelocation, $newcontents);
        }

        // Init Data
        $data = [
            'title' => 'Exception Log',
            'filecontents' => $filegetcontents,
            'errorlog' => $errorlog,
        ];

        // Load View
        View::render('exception/index', $data, 'admin');
    }

}