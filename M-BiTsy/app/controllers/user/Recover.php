<?php

class Recover
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 0);
    }

    // Recover Password Default Page
    public function index()
    {
        // Init Data
        $data = [
            'title' => 'Recover Account',
        ];

        // Load View
        View::render('recover/index', $data, 'user');
    }

    // Recover Password Form Submit
    public function submit()
    {
        // Check User Input
        $email = Input::get("email");

        // Check Valid Email
        if (!Validate::Email($email)) {
            Redirect::autolink(URLROOT, Lang::T("EMAIL_ADDRESS_NOT_VAILD"));
        }

        // Check Input Else Return To Form
        if (Input::exist() && Cookie::csrf_check()) {

            // Check Google Captcha
            (new Captcha)->response($_POST['g-recaptcha-response']);

            // Get User By Email
            $arr = DB::raw('users', 'id, username, email', ['email' => $email])->fetch();
            if (!$arr) {
                Redirect::autolink(URLROOT, Lang::T("EMAIL_ADDRESS_NOT_FOUND"));
            }

            // Send Email
            if ($arr) {
                $sec = Security::mksecret();
                $id = $arr['id'];
                $username = $arr['username']; // 06/01
                $emailmain = Config::get('SITEEMAIL');
                $url = URLROOT;
                $body = Lang::T("SOMEONE_FROM") . " " . $_SERVER["REMOTE_ADDR"] . " " . Lang::T("MAILED_BACK") . " ($email) " . Lang::T("BE_MAILED_BACK") . " \r\n\r\n " . Lang::T("ACCOUNT_INFO") . " \r\n\r\n " . Lang::T("USERNAME") . ": " . $username . " \r\n " . Lang::T("CHANGE_PSW") . "\n\n$url/recover/confirm?id=$id&secret=$sec\n\n\n" . $url . "\r\n";
                $TTMail = new TTMail();
                $TTMail->Send($email, Lang::T("ACCOUNT_DETAILS"), $body, "", "-f$emailmain");
                DB::update('users', ['secret' => $sec], ['email' => $email], 1);
                Redirect::autolink(URLROOT, sprintf(Lang::T('MAIL_RECOVER'), htmlspecialchars($email)));
            }
        } else {
            Redirect::to(URLROOT . "/recover");
        }
    }

    // Recover Confirm Default Page
    public function confirm()
    {
        // Init Data
        $data = [
            'title' => 'Recover Account'
        ];

        // Load View
        View::render('recover/confirm', $data, 'user');
    }

    // Recover Confirm Form Submit
    public function ok()
    {
        // Check User Input
        $id = Input::get("id");
        $secret = Input::get("secret");

        // Check ID & Secret
        if (Validate::Id(Input::get("id")) && strlen(Input::get("secret")) == 20) {
            Redirect::autolink(URLROOT, Lang::T("Wrong Imput"));
        }

        // Check Input Else Return To Form
        if (Input::exist() && Cookie::csrf_check()) {

            // Check Google Captcha
            (new Captcha)->response($_POST['g-recaptcha-response']);
            
            // Check User Input
            $password = Input::get("password");
            $password1 = Input::get("password1");

            // Check Password
            if (empty($password) || empty($password1)) {
                Redirect::autolink(URLROOT, Lang::T("NO_EMPTY_FIELDS"));
            } elseif ($password != $password1) {
                Redirect::autolink(URLROOT, Lang::T("PASSWORD_NO_MATCH"));
            }
            
            // Check User
            $count = DB::column('users', 'COUNT(*)', ['id' => $id, 'secret' => $secret]);
            if ($count != 1) {
                Redirect::autolink(URLROOT, Lang::T("NO_SUCH_USER"));
            }

            // Update User
            $newsec = Security::mksecret();
            $wantpassword = password_hash($password, PASSWORD_BCRYPT);
            Users::recoverUpdate($wantpassword, $newsec, $id, $secret);
            Redirect::autolink(URLROOT, Lang::T("PASSWORD_CHANGED_OK"));
            
        } else {
            Redirect::to(URLROOT . "/recover/confirm");
        }
    }
}