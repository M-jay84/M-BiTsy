<?php

class Signup
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 0);
    }

    // Signup Default Page
    public function index()
    {
        // Check User IP
        if (Config::get('IPCHECK')) {
            Ip::checkIP();
        }

        // Check If Invite
        $invite = Input::get("invite");
        $secret = Input::get("secret");

        // Check Invite
        $invite_row = 0;
        if (!Validate::Id($invite) || strlen($secret) != 20) {
            if (Config::get('INVITEONLY')) {
                Redirect::autolink(URLROOT, "<center>" . Lang::T("INVITE_ONLY_MSG") . "<br></center>");
            }
        } else {
            $invite_row = DB::select('users', 'id', ['id'=>$invite, 'secret'=>$secret]);
            if (!$invite_row) {
                Redirect::autolink(URLROOT, Lang::T("INVITE_ONLY_NOT_FOUND") . "" . (Config::get('SIGNUPTIMEOUT') / 86400) . "days.");
            }
        }
        
        // Init Data
        $data = [
            'title' => Lang::T("SIGNUP"),
            'invite' => $invite_row,
        ];

        // Load View
        View::render('signup/index', $data, 'user');
    }

    // Signup Form Submit
    public function submit()
    {
        // Check Input Else Return To Form
        if (Input::exist() && Cookie::csrf_check()) {
            
            // Check Google Captcha
            (new Captcha)->response($_POST['g-recaptcha-response']);
            
            // Check Input
            $passagain = Input::get("passagain");
            $invite = Input::get("invite");

            // Init Data
            $data = [
                'username' => Input::get("wantusername"),
                'email' => Input::get("email"),
                'password' => Input::get("wantpassword"),
                'country' => Input::get("country"),
                'gender' => Input::get("gender"),
                'client' => Input::get("client"),
                'age' => Input::get("age"),
                'secret' => Input::get("secret")
            ];

            // Check User Invite
            if (strlen($data['secret']) == 20 || !is_numeric($invite)) {
                $invite_row1 = DB::select('users', 'id', ['id'=>$invite, 'secret'=>$data['secret']]);
                $invite_row = (int) $invite_row1['id'] ?? 0;
            }
            
            // Check User Input
            $message = $this->validSign($passagain, $data, $invite_row);
            
            if ($message == "") {
                // If NOT Invite Check
                if (!$invite_row) {
                    // Check Max Users
                    $numsitemembers = get_row_count("users");
                    if ($numsitemembers >= Config::get('MAXUSERS')) {
                        $msg = Lang::T("SITE_FULL_LIMIT_MSG") . number_format(Config::get('MAXUSERS')) . " " . Lang::T("SITE_FULL_LIMIT_REACHED_MSG") . " " . number_format($numsitemembers) . " members";
                        Redirect::autolink(URLROOT, $msg);
                    }

                    // Check Email Not Banned
                    $message = Bans::checkemail($data['email']);
                }

                // Check Username NOT In Use
                $count = DB::column('users', 'COUNT(*)', ['username'=>$data['username']]);
                if ($count != 0) {
                    $message = sprintf(Lang::T("USERNAME_INUSE_S"), $data['username']);
                }

                // Generate Secret
                $data['secret'] = Security::mksecret();
                
                // Hash Password
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
                
                // Make Passkey
                $rand = array_sum(explode(" ", microtime()));
                $passkey = md5($data['username'] . $rand . $data['secret'] . ($rand * mt_rand()));
                $data['passkey'] = $passkey;
            }

            // Check Message Error
            if ($message != "") {
                Redirect::autolink(URLROOT . '/login', $message);
            }

            // No Message So Proceed
            if ($message == "") {
                // Invited User
                if ($invite_row) {
                    DB::run("UPDATE users SET username=?, password=?, class=?, secret=?, status=?, added=?, passkey=? WHERE id=?",
                                                  [$data['username'], $data['password'], 1, $data['secret'], 'confirmed', TimeDate::get_date_time(), $data['passkey'], $invite_row]);
                    //var_dump($test); die();
                    //DB::update('users', ['username'=>$data['username'], 'password'=>$data['password'], 'class'=>1, 'secret'=>$data['secret'], 'status'=>'confirmed', 'added'=>TimeDate::get_date_time()], ['id'=>1]);
                    // Welcome PM
                    if (Config::get('WELCOMEPM_ON')) {
                        Messages::insert(['sender'=>0, 'receiver'=>$invite_row, 'added'=>TimeDate::get_date_time(), 'subject'=>'Welcome', 'msg'=>Config::get('WELCOMEPM_MSG'), 'unread'=>'yes', 'location'=>'in']);
                    }
                    // Post Shout
                    DB::insert('shoutbox', ['userid'=>0, 'date'=>TimeDate::get_date_time(), 'user'=>'System', 'message'=>"New User: " . $data['username'] . " has joined."]);
                    // Invite Activated
                    Redirect::autolink(URLROOT . '/login', Lang::T("ACCOUNT_ACTIVATED"));
                }

                // Check To Confirm
                if (Config::get('CONFIRMEMAIL') || Config::get('ACONFIRM')) {
                    $data['status'] = "pending";
                } else {
                    $data['status'] = "confirmed";
                }
                
                // Set Class
                if ($numsitemembers == '0') {
                    $data['class'] = '7';
                } else {
                    $data['class'] = '1';
                }

                // Standard Inputs
                $data['added'] = TimeDate::get_date_time();
                $data['last_login'] = TimeDate::get_date_time();
                $data['last_access'] = TimeDate::get_date_time();
                $data['stylesheet'] = Config::get('DEFAULTTHEME');
                $data['language'] = Config::get('DEFAULTLANG');
                $data['ip'] = Ip::getIP();

                // Insert User 
                DB::insert('users', $data);
                $id = DB::lastInsertId();
    
                // Post Shout
                DB::insert('shoutbox', ['userid'=>0, 'date'=>TimeDate::get_date_time(), 'user'=>'System', 'message'=>"New User: " . $data['username'] . " has joined."]);

                // Welcome PM
                if (Config::get('WELCOMEPM_ON')) {
                    Messages::insert(['sender'=>0, 'receiver'=>$id, 'added'=>TimeDate::get_date_time(), 'subject'=>'Welcome', 'msg'=>Config::get('WELCOMEPM_MSG'), 'unread'=>'yes', 'location'=>'in']);
                }

                // Admin Confirm
                if (Config::get('ACONFIRM')) {
                    $body = Lang::T("YOUR_ACCOUNT_AT") . " " . Config::get('SITENAME') . " " . Lang::T("HAS_BEEN_CREATED_YOU_WILL_HAVE_TO_WAIT") . "\n\n" . Config::get('SITENAME') . " " . Lang::T("ADMIN");
                } else {
                    $body = Lang::T("YOUR_ACCOUNT_AT") . " " . Config::get('SITENAME') . " " . Lang::T("HAS_BEEN_APPROVED_EMAIL") . "\n\n	" . URLROOT . "/confirmemail/signup?id=$id&secret=$data[secret]\n\n" . Lang::T("HAS_BEEN_APPROVED_EMAIL_AFTER") . "\n\n	" . Lang::T("HAS_BEEN_APPROVED_EMAIL_DELETED") . "\n\n" . URLROOT . " " . Lang::T("ADMIN");
                }

                // Confirm Account
                if (Config::get('CONFIRMEMAIL') || Config::get('ACONFIRM')) {
                    $TTMail = new TTMail();
                    $TTMail->Send($data['email'], "Your " . Config::get('SITENAME') . " User Account", $body, "", "-f" . Config::get('SITEEMAIL') . "");
                    Redirect::autolink(URLROOT . '/login', Lang::T("A_CONFIRMATION_EMAIL_HAS_BEEN_SENT") . " (" . htmlspecialchars($data['email']) . "). " . Lang::T("ACCOUNT_CONFIRM_SENT_TO_ADDY_REST") . " <br/ >");
                } else {
                    Redirect::autolink(URLROOT . '/login', Lang::T("ACCOUNT_ACTIVATED"));
                }
            }

        } else {
            Redirect::to(URLROOT . "/signup");
        }
    }

    // Check User Input
    public function validSign($passagain, $data, $invite_row = false)
    {
        $message = "";

        if (Validate::isEmpty($data['password']) || (Validate::isEmpty($data['email']) && !$invite_row) || Validate::isEmpty($data['username'])) {
            $message = Lang::T("DONT_LEAVE_ANY_FIELD_BLANK");

        } elseif (strlen($data['username']) > 50) {
            $message = sprintf(Lang::T("USERNAME_TOO_LONG"), 16);

        } elseif ($data['password'] != $passagain) {
            $message = Lang::T("PASSWORDS_NOT_MATCH");

        } elseif (strlen($data['password']) < 6) {
            $message = sprintf(Lang::T("PASS_TOO_SHORT_2"), 6);

        } elseif (strlen($data['password']) > 16) {
            $message = sprintf(Lang::T("PASS_TOO_LONG_2"), 16);

        } elseif ($data['password'] == $data['username']) {
            $message = Lang::T("PASS_CANT_MATCH_USERNAME");

        } elseif (!Validate::username($data['username'])) {
            $message = "Invalid username.";

        } elseif ($data['invite_row'] = false && !Validate::Email($data['email'])) {
            $message = "That doesn't look like a valid email address.";
        }
        
        return $message;
    }

}