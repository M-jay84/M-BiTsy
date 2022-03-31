<?php
class Invite
{

    public function __construct()
    {
        Auth::user(0, 2);
    }

    public function index()
    {
        if (!Config::get('INVITEONLY') && !Config::get('ENABLEINVITES')) {
            Redirect::autolink(URLROOT, Lang::T("INVITES_DISABLED_MSG"));
        }

        $users = get_row_count("users", "WHERE enabled = 'yes'");
        if ($users >= Config::get('MAXUSERSINVITE')) {
            Redirect::autolink(URLROOT, "Sorry, The current user account limit (" . number_format(Config::get('MAXUSERSINVITE')) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...");
        }
        if (Users::get("invites") == 0) {
            Redirect::autolink(URLROOT, Lang::T("YOU_HAVE_NO_INVITES_MSG"));
        }

        $data = [
            'title' => 'Invite User',
        ];
        View::render('invite/invite', $data, 'user');
    }

    public function submit()
    {
        $email = Input::get("email") ?? '';
        
        if (!Validate::Email($email)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_EMAIL_ADDRESS"));
        }
        
        // Check email isnt banned/used
        $maildomain = (substr($email, strpos($email, "@") + 1));
        $a = DB::column('email_bans', 'count(*)', ['mail_domain'=>$email]);
        if ($a != 0) {
            $message = sprintf(Lang::T("EMAIL_ADDRESS_BANNED"), $email);
        }
        $a = DB::column('email_bans', 'count(*)', ['mail_domain'=>$maildomain]);
        if ($a != 0) {
            $message = sprintf(Lang::T("EMAIL_ADDRESS_BANNED"), $email);
        }
        if (get_row_count("users", "WHERE email='$email'")) {
            $message = sprintf(Lang::T("EMAIL_ADDRESS_INUSE"), $email);
        }
        if ($message) {
            Redirect::autolink(URLROOT, $message);
        }

        $secret = Helper::mksecret();
        $username = "invite_" . Helper::mksecret(20);
        $ret = DB::run("INSERT INTO users (username, secret, email, status, invited_by, added, stylesheet, language) VALUES (?,?,?,?,?,?,?,?)",
                [$username, $secret, $email, 'pending', Users::get("id"), TimeDate::get_date_time(), Config::get('DEFAULTTHEME'), Config::get('DEFAULTLANG')]);
        $id = DB::lastInsertId();

        $invitees = "$id ".Users::get('invitees')."";
        DB::run("UPDATE users SET invites = invites - 1, invitees='$invitees' WHERE id = ".Users::get('id')."");
        
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

    public function invitetree()
    {
        $id = Input::get("id");
        
        if (!Validate::Id($id)) {
            $id = Users::get('id');
        }

        $res = DB::raw('users', '*', ['status'=>'confirmed','invited_by'=>$id], 'ORDER BY username');
        $num = $res->rowCount();
        $invitees = DB::column('users', '*', ['status'=>'confirmed','invited_by'=>$id], 'ORDER BY username');
        if ($invitees == 0) {
            Redirect::autolink(URLROOT . "/profile?id=$id", "This member has no invitees");
        }

        if ($id != Users::get("id")) {
            $title = "Invite Tree for [<a href=" . URLROOT . "/profile?id=$id>" . $id . "</a>]";
        } else {
            $title = "You have $invitees invitees " . Users::coloredname(Users::get('id')) . "";
        }

        $data = [
            'title' => $title,
            'id' => $id,
            'invitees' => $invitees,
            'res' => $res,
            'num' => $num,
        ];
        View::render('invite/invitetree', $data, 'user');
    }
    
}