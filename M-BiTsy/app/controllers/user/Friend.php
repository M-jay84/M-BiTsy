<?php

class Friend
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Friends Default Page
    public function index()
    {
        // Check User Input
        $userid = (int) Input::get('id');

        // Check User
        if (!Validate::Id($userid)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_USERID"));
        }

        if (Users::get("view_users") == "no" && Users::get("id") != $userid) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }

        // Get Friends Data
        $friend = Friends::join($userid, 'friend');
        $enemy = Friends::join($userid, 'enemy');

        // Init Data
        $data = [
            'title' => "Friend Lists For ".Users::coloredname($userid)."",
            'userid' => $userid,
            'friend' => $friend,
            'enemy' => $enemy,
        ];
        
        // Load View
        View::render('friend/index', $data, 'user');
    }

    // Add Friend Submit
    public function add()
    {
        // Check User Input
        $targetid = (int) Input::get('targetid');
        $type = Input::get('type');

        // Check User
        if (!Validate::Id($targetid)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_USERID"));
        }

        // Add Friend
        Friends::add($targetid, $type);
    }

    // Delete Friend Submit
    public function delete()
    {
        // Check User Input
        $targetid = (int) Input::get('targetid');
        $type = htmlentities(Input::get('type'));

        // Check User
        if (!Validate::Id($targetid)) {
            Redirect::autolink(URLROOT . "/friend?id=".Users::get('id')."]", "Invalid ID ".Users::get('id')."");
        }

        // Delete Friend
        Friends::delete($targetid, $type);
    }

}