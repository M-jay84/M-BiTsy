<?php

class Logout
{

    // Logout Default Page
    public function index()
    {
        // Destroy $_COOKIE/$_SESSION
        Cookie::destroyAll();

        // gO To Login Page
        Redirect::to(URLROOT . "/login");
    }

}