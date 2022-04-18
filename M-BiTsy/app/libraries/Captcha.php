<?php

class Captcha
{
    private $key;
    private $secret;

    public function __construct()
    {
        $this->key = Config::get('CAPTCHA_KEY');
        $this->secret = Config::get('CAPTCHA_SECRET');
    }

    // Captcha Response
    public function response($captcha)
    {
        if (Config::get('CAPTCHA_ON')) {
            if (!$captcha) {
                    Redirect::autolink(URLROOT . '/login', Lang::T('<b>Please check the the captcha form.</b>'));
            }
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $this->secret . '&response=' . $captcha);
            $responseData = json_decode($verifyResponse);
            if ($responseData->success) {
                // its successfull so let it go
            }
        }
    }

    // Display Captcha
    public function html()
    {
        if (Config::get('CAPTCHA_ON')) {
            return print("<center><div class='g-recaptcha' data-theme='light' data-sitekey='" . $this->key . "'></div><center>");
        }
    }

}