<?php

class Cookie
{

    // Destroy All Cookies
    public static function destroyAll()
    {
        setcookie("id", "", time() - 7000000, "/");
        setcookie("password", "", time() - 7000000, "/");
        setcookie("PHPSESSID", "", time() - 7000000, "/");
        $_SESSION = array();
        unset($_SESSION);
        @session_destroy();
    }

    // Set Cookie Values
    public static function set($name, $value, $expiry)
    {
        if (setcookie($name, $value, time() + $expiry, "/")) {
            return true;
        }
        return false;
    }

    // Set All Cookie Values
    public static function setAll($id, $pass)
    {
        self::set('id', $id, 5485858, "/");
        self::set('password', $pass, 5485858, "/");
    }

    // Create CSRF Cookie
    public static function csrf_token()
	{
		// Check if a token is present for the current session
		if (! isset($_COOKIE['csrf_token'])) {
			$token = base64_encode(openssl_random_pseudo_bytes(32));
			self::set("csrf_token", $token, 5485858, "/");
        } else {
            $token = $_COOKIE['csrf_token'];
        }
        return $token;
    }

    // Check CSRF Cookie
    public static function csrf_check()
	{
		// return true;
        
        if (!$_POST["csrf_token"] == $_COOKIE["csrf_token"]) {
            // Reset token
            setcookie("csrf_token", "", time() - 7000000, "/");
            Redirect::autolink(URLROOT . "/logout", Lang::T("CSRF token validation failed"));
            return false;
        } else {
            setcookie("csrf_token", "", time() - 7000000, "/");
            return true;
        }
        
    }

	// Get Cookie
    public static function get($name) {
		if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        } else {
            return false;
        }
	}

}