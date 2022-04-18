<?php

class Stylesheet
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Style/Language Block Form Submit
    public function index()
    {
        // Check Input Else Return To Form
        if (Input::exist() && Cookie::csrf_check()) {
            
            // Check User Input
            $stylesheet = Input::get('stylesheet');
            $language = Input::get('language');

            // Set As Array
            $updateset = array();
            $updateset["stylesheet"] = $stylesheet;
            $updateset["language"] = $language;

            // Update User Preference
            if (count($updateset)) {
                DB::update("users", $updateset, ['id' => Users::get('id')]);
            }
        }

        // Back To Page
        if (empty($_SERVER["HTTP_REFERER"])) {
            Redirect::to(URLROOT);
        }
        Redirect::to($_SERVER["HTTP_REFERER"]);
    }
}
