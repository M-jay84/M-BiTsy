<?php

class Rating
{
    
    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Torrent Rating Submit
    public function index()
    {
        // Check User Input
        $id = (int) Input::get("id");
        $rating = (int) Input::get('rating');

        // Check Correct Input
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("THATS_NOT_A_VALID_ID"));
        }

        if ($rating <= 0 || $rating > 5) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("INVAILD_RATING"));
        }

        // Add Rating
        Ratings::addRating($id, $rating);
        Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("RATING_THANK") . "<br /><br /><a href='" . URLROOT . "/torrent?id=$id'>" . Lang::T("BACK_TO_TORRENT") . "</a>");
    }

}