<?php

class Warning
{
    
    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // User Warn Default Page
    public function index()
    {
        // Check User Input
        $id = (int) Input::get("id");

        // Check User
        if (Users::get("view_users") == "no" && Users::get("id") != $id) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }
        if ((Users::get("enabled") == "no" || (Users::get("status") == "pending")) && Users::get("edit_users") == "no") {
            Redirect::autolink(URLROOT . '/members', Lang::T("NO_ACCESS_ACCOUNT_DISABLED"));
        }

        // Get Warnings
        $warning = DB::raw('warnings', '*', ['userid'=>$id], 'ORDER BY id DESC');
        $title = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($id));
        
        // Init Data
        $data = [
            'title' => $title,
            'res' => $warning,
            'id' => $id
        ];
        
        // Load View
        View::render('warning/index', $data, 'user');
    }

    // User Warn Form Submit
    public function submit()
    {
        // Check User Input
        $userid = (int) Input::get("userid");
        $reason = Input::get("reason");
        $expiry = (int) Input::get("expiry");
        $type = Input::get("type");
        
        // Check Input
        if (!$reason || !$expiry || !$type) {
            Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("MISSING_FORM_DATA"));
        }

        // Check User
        if (Users::get("edit_users") != "yes") {
            Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("TASK_ADMIN"));
        }
        if (!Validate::Id($userid)) {
            Redirect::autolink(URLROOT . '/members', Lang::T("INVALID_USERID"));
        }

        // Insert Warning
        $expiretime = TimeDate::get_date_time(TimeDate::gmtime() + (86400 * $expiry));
        DB::insert('warnings', ['userid'=>$userid, 'reason'=>$reason, 'added'=>TimeDate::get_date_time(), 'expiry'=>$expiretime,'warnedby'=>Users::get('id'),'type'=>$type]);
        // Warn User
        DB::update('users', ['warned'=>'yes'], ['id'=>$userid]);
        // Send PM
        $msg = "You have been warned by " . Users::get("username") . " - Reason: " . $reason . " - Expiry: " . $expiretime . "";
        Messages::insert(['sender'=>0, 'receiver'=>$userid, 'added'=>TimeDate::get_date_time(), 'subject'=>'New Warning', 'msg'=>$msg, 'unread'=>'yes', 'location'=>'in']);
        // Log Warning
        Logs::write(Users::get('username') . " has added a warning for user: <a href='" . URLROOT . "/profile?id=$userid'>$userid</a>");
        Redirect::autolink(URLROOT . "/profile?id=$userid", "Warning given");
    }

}