<?php

class Thank
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Thank Submit
    public function index()
    {
        // Check User Input
        $id = (int) Input::get('id');
        $type = Input::get('type');

        // Check Correct Input
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
        }

        if (!$type) {
            Redirect::autolink(URLROOT, "No ID");
        }

        // Insert/Delete Thank
        Thanks::switch($id, $type);
    }

}