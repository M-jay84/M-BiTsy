<?php

class View
{
    // Return View Template
    public static function render($file, $data = [], $page = false)
    {
        // Start With No Error
        $error = 0;

        // Debug
        $GLOBALS['debugviewdata'] = debugviewdata($data);

        if ($page == 'user') {

            // Does User View Exist
            if (file_exists('../app/views/user/' . $file . '.php')) {
                Style::header($data['title']);
                Style::begin($data['title']);
                require_once "../app/views/user/" . $file . ".php";
                Style::end();
                Style::footer();
                return;
            } else {
                $error = 1;
            }

        } elseif ($page == 'admin') {

            // Does Admin View Exist
            if (file_exists('../app/views/admin/' . $file . '.php')) {
                Style::adminheader('Staff Panel');
                Style::adminnavmenu();
                Style::begin($data['title']);
                require_once "../app/views/admin/" . $file . ".php";
                Style::end();
                Style::adminfooter();
                return;
            } else {
                $error = 1;
            }

        } else {

            // Does Requalar View Exist
            if (file_exists('../app/views/user/' . $file . '.php')) {
                require_once "../app/views/user/" . $file . ".php";
                return;
            } else {
                $error = 1;
            }
            
        }
        // The View Isnt There !
        if ($error === 1) {
            Redirect::autolink(URLROOT, "View $file does not exist");
        }
    }

}