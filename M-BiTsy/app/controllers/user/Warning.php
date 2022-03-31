<?php
class Warning
{
    public function __construct()
    {
        Auth::user(0, 2);
    }

    public function index()
    {
        $id = (int) Input::get("id");

        if (Users::get("view_users") == "no" && Users::get("id") != $id) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }
        if ((Users::get("enabled") == "no" || (Users::get("status") == "pending")) && Users::get("edit_users") == "no") {
            Redirect::autolink(URLROOT . '/group/members', Lang::T("NO_ACCESS_ACCOUNT_DISABLED"));
        }

        $user = DB::raw('users', '*', ['id'=>$id])->fetch();
        if (!$user) {
            Redirect::autolink(URLROOT . '/group/members', Lang::T("NO_USER_WITH_ID") . " $id.");
        }
        
        // Get Warnings
        $warning = DB::raw('warnings', '*', ['userid'=>$id], 'ORDER BY id DESC');
        $title = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($id));
        
        $data = [
            'title' => $title,
            'res' => $warning,
            'id' => $user['id'],
            'username' => $user['username'],
        ];
        View::render('warning/index', $data, 'user');
    }

    public function submit()
    {
        $userid = (int) Input::get("userid");
        $reason = Input::get("reason");
        $expiry = (int) Input::get("expiry");
        $type = Input::get("type");
        
        if (Users::get("edit_users") != "yes") {
            Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("TASK_ADMIN"));
        }
        if (!Validate::Id($userid)) {
            Redirect::autolink(URLROOT . '/group/members', Lang::T("INVALID_USERID"));
        }
        if (!$reason || !$expiry || !$type) {
            Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("MISSING_FORM_DATA"));
        }
        
        $expiretime = TimeDate::get_date_time(TimeDate::gmtime() + (86400 * $expiry));
        DB::insert('warnings', ['userid'=>$userid, 'reason'=>$reason, 'added'=>TimeDate::get_date_time(), 'expiry'=>$expiretime,'warnedby'=>Users::get('id'),'type'=>$type]);
        DB::update('users', ['warned'=>'yes'], ['id'=>$userid]);
        
        $msg = "You have been warned by " . Users::get("username") . " - Reason: " . $reason . " - Expiry: " . $expiretime . "";
        Messages::insert(['sender'=>0, 'receiver'=>$userid, 'added'=>TimeDate::get_date_time(), 'subject'=>'New Warning', 'msg'=>$msg, 'unread'=>'yes', 'location'=>'in']);
        Logs::write(Users::get('username') . " has added a warning for user: <a href='" . URLROOT . "/profile?id=$userid'>$userid</a>");
        Redirect::autolink(URLROOT . "/profile?id=$userid", "Warning given");
    }

}