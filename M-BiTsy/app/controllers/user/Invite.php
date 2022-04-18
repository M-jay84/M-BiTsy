<?php

class Invite
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Invite Default Page
    public function index()
    {
        // Check Invites On
        if (!Config::get('INVITEONLY') && !Config::get('ENABLEINVITES')) {
            Redirect::autolink(URLROOT, Lang::T("INVITES_DISABLED_MSG"));
        }

        // Check User Has Invite
        if (Users::get("invites") == 0) {
            Redirect::autolink(URLROOT, Lang::T("YOU_HAVE_NO_INVITES_MSG"));
        }

        // Check Max Users
        $users = get_row_count("users", "WHERE enabled = 'yes'");
        if ($users >= Config::get('MAXUSERSINVITE')) {
            Redirect::autolink(URLROOT, "Sorry, The current user account limit (" . number_format(Config::get('MAXUSERSINVITE')) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...");
        }

        // Init Data
        $data = [
            'title' => 'Invite User',
        ];
        
        // Load View
        View::render('invite/index', $data, 'user');
    }

    // Invite Form Submit
    public function submit()
    {
        // Check User Input
        $email = Input::get("email") ?? '';
        
        // Check Correct Input
        if (!Validate::Email($email)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_EMAIL_ADDRESS"));
        }
        
        // Check Email Not Banned
        $message = Bans::checkemail($email);
        if ($message) {
            Redirect::autolink(URLROOT, $message);
        }

        // Set Invite
        $secret = Security::mksecret();
        $username = "invite_" . Security::mksecret(20);
        DB::run("INSERT INTO users (username, secret, email, status, invited_by, added, stylesheet, language) VALUES (?,?,?,?,?,?,?,?)",
                [$username, $secret, $email, 'pending', Users::get("id"), TimeDate::get_date_time(), Config::get('DEFAULTTHEME'), Config::get('DEFAULTLANG')]);
        $id = DB::lastInsertId();
        $invitees = "$id ".Users::get('invitees')."";
        DB::run("UPDATE users SET invites = invites - 1, invitees='$invitees' WHERE id = ".Users::get('id')."");
        
        // Send Email
        $mess = strip_tags($_POST["mess"]);
        $names = Config::get('SITENAME');
        $links = URLROOT;
        $emailmain = Config::get('SITEEMAIL');
        
        $body = file_get_contents(APPROOT . "/views/user/email/inviteuser.php");
        $body = str_replace("sitename%", $names, $body);
        $body = str_replace("%username%", Users::get('username'), $body);
        $body = str_replace("%email%", $email, $body);
        $body = str_replace("%mess%", $mess, $body);
        $body = str_replace("%links%", $links, $body);
        $body = str_replace("%id%", $id, $body);
        $body = str_replace("%secret%", $secret, $body);

        $TTMail = new TTMail();
        $TTMail->Send($email, "$names user registration confirmation", $body, "", "-f$emailmain");
        Redirect::autolink(URLROOT, Lang::T("A_CONFIRMATION_EMAIL_HAS_BEEN_SENT") . " (" . htmlspecialchars($email) . "). " . Lang::T("ACCOUNT_CONFIRM_SENT_TO_ADDY_REST") . " <br/ >");
    }

    // User Invitee's Default Page
    public function invitetree()
    {
        // Check User Input
        $id = Input::get("id");
        
        // Check User
        if (!Validate::Id($id)) {
            $id = Users::get('id');
        }

        // Get Invitees Data
        $res = DB::raw('users', '*', ['status'=>'confirmed','invited_by'=>$id], 'ORDER BY username');
        $num = $res->rowCount();
        $invitees = DB::column('users', '*', ['status'=>'confirmed','invited_by'=>$id], 'ORDER BY username');
        if ($invitees == 0) {
            Redirect::autolink(URLROOT . "/profile?id=$id", "This member has no invitees");
        }

        // Set Title
        if ($id != Users::get("id")) {
            $title = "Invite Tree for [<a href=" . URLROOT . "/profile?id=$id>" . $id . "</a>]";
        } else {
            $title = "You have $invitees invitees " . Users::coloredname(Users::get('id')) . "";
        }

        // Init Data
        $data = [
            'title' => $title,
            'id' => $id,
            'invitees' => $invitees,
            'res' => $res,
            'num' => $num,
        ];
        
        // Load View
        View::render('invite/tree', $data, 'user');
    }
    
}