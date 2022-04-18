<?php

class Contact
{
    
    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 0);
    }

    // Contact Default Page
    public function index()
    {
        // Init Data
        $data = [
            'title' => 'Contact Staff',
        ];

        // Load View
        View::render('contact/index', $data, 'user');
    }

    // Contact Form Submit
    public function submit()
    {
        // Check Input Else Return To Form
        if (Input::exist() && Cookie::csrf_check()) {

            // Check Google Captcha
            (new Captcha)->response($_POST['g-recaptcha-response']);

            // Check User Input
            $msg = Input::get("msg");
            $subject = Input::get("subject");

            // Check Correct Input
            if (!$msg || !$subject) {
                Redirect::autolink(URLROOT, Lang::T("NO_EMPTY_FIELDS"));
            }
            if (strlen($msg) < 10 || strlen($subject) < 5) {
                Redirect::autolink(URLROOT, Lang::T("Message or subject too short"));
            }

            // Insert Message
            $userid = Users::get('id') > 0 ? Users::get('id') : 0;
            DB::insert('staffmessages', ['sender' => $userid, 'added' => TimeDate::get_date_time(), 'msg' => $msg, 'subject' => $subject]);
            Redirect::autolink(URLROOT, Lang::T("CONTACT_SENT"));

        } else {
            Redirect::to(URLROOT . "/contact");
        }
    }
}
