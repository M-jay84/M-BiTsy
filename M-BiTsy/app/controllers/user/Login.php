<?php

class Login 
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 0);
        
        // Verify User Already Logged In
        if (isset($_SESSION['loggedin'])) {
            Redirect::autolink(URLROOT, Lang::T("Already Logged In"));
        }
    }

    // Login Default Page
    public function index()
    {
        // Init Data
        $data = [
            'title' => Lang::T("LOGIN"),
        ];

        // Load View
        View::render('login/index', $data, 'user');
    }

    // Login Form Submit
    public function submit()
    {
        // Check Input Else Return To Form
        if (Input::exist() && Cookie::csrf_check()) {

            // Check Google Captcha
            (new Captcha)->response(Input::get('g-recaptcha-response'));
                    
            // Check User Input
            $username = Input::get("username");
            $password = Input::get("password");
        
            // Check If User
            Users::login($username, $password);
        
        } else {
            Redirect::to(URLROOT . "/login");
        }
    }
    
}