<?php

class Changepass
{
    
    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Change Password Default Page
    public function index()
    {
        // Check User Input
        $id = (int) Input::get("id");

        // Check User
        if (Users::get('class') < _MODERATOR && $id != Users::get('id')) {
            Redirect::autolink(URLROOT . "/index", Lang::T("NO_PERMISSION"));
        }

        // Init Dta
        $data = [
            'title' => Lang::T('CHANGE_PASS'),
            'id' => $id,
        ];

        // Load View
        View::render('changepass/index', $data, 'user');
    }

    // Change Password Form Submit
    public function submit()
    {
        // Check User Input
        $id = (int) Input::get("id");
        $chpassword = Input::get('chpassword');
        $passagain = Input::get('passagain');

        // Check User
        if (Users::get('class') < _MODERATOR && $id != Users::get('id')) {
            Redirect::autolink(URLROOT . "/index", Lang::T("NO_PERMISSION"));
        }

        // Check Correct Input
        if ((!$chpassword) || (!$passagain)) {
            Redirect::autolink(URLROOT . "/changepass?id=$id", Lang::T("YOU_DID_NOT_ENTER_ANYTHING"));
        }

        if (strlen($chpassword) < 6) {
            Redirect::autolink(URLROOT . "/changepass?id=$id", Lang::T("PASS_TOO_SHORT"));
        }

        if ($chpassword != $passagain) {
            Redirect::autolink(URLROOT . "/changepass?id=$id", Lang::T("PASSWORDS_NOT_MATCH"));
        }
        
        // Set User Secret
        $chpassword = password_hash($chpassword, PASSWORD_BCRYPT);
        $secret = Security::mksecret();
        $in = DB::update('users', ['password'=>$chpassword, 'secret'=>$secret], ['id'=>$id]);
        if ($in > 0) {
            Redirect::autolink(URLROOT . "/logout", Lang::T("PASSWORD_CHANGED_OK"));
        } else {
            Redirect::autolink(URLROOT . "/changepass?id=$id", Lang::T("ERROR"));
        }
    }

}