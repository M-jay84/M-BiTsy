<?php

class Like
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Like Submit
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
            Redirect::autolink(URLROOT, "No Type");
        }

        // Insert/Delete Like
        Likes::switch($id, $type);
    }

}