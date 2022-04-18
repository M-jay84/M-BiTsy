<?php

class Confirmemail
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 0);
    }


    // Email Reset Default Page
    public function index()
    {
        // Check User Input
        $id = (int) Input::get("id");

        // Check User
        if ($id != Users::get('id')) {
            Redirect::autolink(URLROOT . "/index", Lang::T("NO_PERMISSION"));
        }

        if (Input::exist()) {
            
            // Check User Input
            $email = $_POST["email"];

            // Check Email
            if (!Validate::Email($email)) {
                Redirect::autolink(URLROOT, Lang::T("INVALID_EMAIL_ADDRESS"));
            }

            // Set Secret
            $sec = Security::mksecret();
            $obemail = rawurlencode($email);
            $sitename = URLROOT;

            // Send Email
            $body = file_get_contents(APPROOT . "/views/user/email/changeemail.php");
            $body = str_replace("%usersname%", Users::get("username"), $body);
            $body = str_replace("%sitename%", $sitename, $body);
            $body = str_replace("%usersip%", $_SERVER["REMOTE_ADDR"], $body);
            $body = str_replace("%usersid%", Users::get("id"), $body);
            $body = str_replace("%userssecret%", $sec, $body);
            $body = str_replace("%obemail%", $obemail, $body);
            $body = str_replace("%newemail%", $email, $body);
            $TTMail = new TTMail();
            $TTMail->Send($email, "$sitename profile update confirmation", $body, "From: " . Config::get('SITEEMAIL') . "", "-f" . Config::get('SITEEMAIL') . "");
            
            // Update User Secret
            DB::update('users', ['editsecret' => $sec], ['id' => Users::get('id')]);
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("EMAIL_CHANGE_SEND"));
        }

        // Init Dta
        $data = [
            'title' => Lang::T("LOGIN"),
            'id' => $id,
        ];

        // Load Data
        View::render('confirmemail/index', $data, 'user');
    }

    // Email Reset Confirm
    public function confirm()
    {
        // Check User Input
        $id = (int) Input::get("id");
        $md5 = Input::get("secret");
        $email = Input::get("email");

        // Check Correct Input
        if (!$id || !$md5 || !$email) {
            Redirect::autolink(URLROOT . "/home",  Lang::T("MISSING_FORM_DATA"));
        }

        // Check User
        $row = Users::getEditsecret($id);
        if (!$row) {
            Redirect::autolink(URLROOT . "/home",  Lang::T("NOTHING_FOUND"));
        }

        if ($md5 != $row['editsecret']) {
            Redirect::autolink(URLROOT . "/home",  Lang::T("NOTHING_FOUND"));
        }

        // Update User Editsecret
        Users::updateUserEmailResetEditsecret($email, $id, $row['editsecret']);
        Redirect::autolink(URLROOT . "/home", Lang::T("SUCCESS"));
    }

    // Signup Email Confirm
    public function signup()
    {
        // Check User Input
        $id = (int) Input::get("id");
        $md5 = Input::get("secret");

        // Check Correct Input
        if (!$id || !$md5) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
        }

        // Check User
        $row = DB::raw('users', 'password,secret,status', ['id' => $id])->fetch();
        if (!$row) {
            $mgs = sprintf(Lang::T("CONFIRM_EXPIRE"), Config::get('SIGNUPTIMEOUT') / 86400);
            Redirect::autolink(URLROOT, $mgs);
        }
        if ($row['status'] != "pending") {
            Redirect::autolink(URLROOT, Lang::T("ACCOUNT_ACTIVATED"));
            die;
        }
        if ($md5 != $row['secret']) {
            Redirect::autolink(URLROOT, Lang::T("SIGNUP_ACTIVATE_LINK"));
        }

        // Set Secret
        $secret = Security::mksecret();
        $upd = Users::updatesecret($secret, $id, $row['secret']);
        if ($upd == 0) {
            Redirect::autolink(URLROOT, Lang::T("SIGNUP_UNABLE"));
        }
        Redirect::autolink(URLROOT . '/login', Lang::T("ACCOUNT_ACTIVATED"));
    }

}