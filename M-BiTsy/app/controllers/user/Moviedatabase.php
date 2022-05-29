<?php
// Get TMDB Classes
require_once APPROOT . "/libraries/TMDB.php";

class Moviedatabase
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);

        // Check TMDB Set
        if (_TMDBswitch === false) {
            Redirect::autolink(URLROOT, Lang::T("TMDB turned off"));
        }
    }

    // TMDB Search Default Page
    public function index()
    {
        // Init Data
        $data = [
            'title' => 'Search TMDB Movie',
        ];

        // Load View
        View::render('moviedatabase/index', $data, 'user');
    }

    // TMDB Search Form Submit
    public function submit()
    {
        // Check User Input
        $name = Input::get('inputsearch');
        $type = Input::get('input');

        // Check Correct Input
        if (!$name || !$type) {
            Redirect::to(URLROOT . "/moviedatabase");
        }

        // Get TMDB Data
        $tmdb = new TMDB();
            
        // Init Data
        $data = [
            'title' => $type,
            'name' => $name,
            'tmdb' => $tmdb,
        ];

        // Load View
        if ($type == 'person') {
            View::render('moviedatabase/person', $data, 'user');
        } elseif ($type == 'show') {
            View::render('moviedatabase/show', $data, 'user');
        } else {
            View::render('moviedatabase/movie', $data, 'user');
        }
    }

    // Person Default Page
    public function person()
    {
        // Check User Input
        $id = (int) Input::get('id');

        // Get TMDB Data
        $tmdb = new TMDB();
            
        // Init Data
        $data = [
            'id' => $id,
            'title' => 'Person',
            'tmdb' => $tmdb,
        ];

        // Load View
        View::render('moviedatabase/persondetails', $data, 'user');
    }

    // Show Default Page
    public function shows()
    {
        // Check User Input
        $id = (int) Input::get('id');

        // Get TMDB Data
        $tmdb = new TMDB();
            
        // Init Data
        $data = [
            'id' => $id,
            'title' => 'Show',
            'tmdb' => $tmdb,
        ];

        // Load View
        View::render('moviedatabase/showdetails', $data, 'user');
    }

    // Movies Default Page
    public function movies()
    {
        // Check User Input
        $id = (int) Input::get('id');

        // Get TMDB Data
        $tmdb = new TMDB();
            
        // Init Data
        $data = [
            'id' => $id,
            'title' => 'Movie',
            'tmdb' => $tmdb,
        ];

        // Load View
        View::render('moviedatabase/moviedetails', $data, 'user');
    }
    
    // Movies Default Page
    public function season()
    {
        // Check User Input
        $id = (int) Input::get('id');
        $seasonnumber = (int) Input::get('sid');

        // Get TMDB Data
        $tmdb = new TMDB();
            
        // Init Data
        $data = [
            'id' => $id,
            'seasonnumber' => $seasonnumber,
            'title' => 'Season',
            'tmdb' => $tmdb,
        ];

        // Load View
        View::render('moviedatabase/seasondetails', $data, 'user');
    }
}