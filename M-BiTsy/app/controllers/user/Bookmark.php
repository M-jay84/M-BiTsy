<?php

class Bookmark
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2, true);
    }

    // Book Mark Default Page
    public function index()
    {
        // Pagination
        $count = DB::column('bookmarks', 'COUNT(*)', ['userid'=>Users::get("id"),'type'=>'torrent']);
        list($pager, $limit) = Pagination::pager(25, $count, URLROOT."/bookmark?");
        $query = Bookmarks::join($limit, Users::get("id"));

        if ($count == 0) {
            Redirect::autolink(URLROOT, "Your Bookmarks list is empty !");
        } else {
            
            // Init Data
            $data = [
                'title' => 'My Bookmarks',
                'count' => $count,
                'pager' => $pager,
                'res' => $query,
            ];

            // Load View
            View::render('bookmark/index', $data, 'user');
        }

    }

    // Add Bookmark Submit
    public function add()
    {
        // Check User Input
        $target = (int) Input::get("target");
        $type = 'torrent';

        // Check Input
        if (!isset($target)) {
            Redirect::autolink(URLROOT, "No target selected...");
        }

        // Check Bookmarked
        $arr = DB::column('bookmarks', 'COUNT(*)', ['targetid'=>$target,'type'=>$type,'userid'=>Users::get('id')]);
        if ($arr > 0) {
            Redirect::autolink(URLROOT, "Already bookmarked...");
        }

        // Add Bookmark
        Bookmarks::switch($type, $target);
        Redirect::autolink(URLROOT, "ID not found");
    }

    // Delete Bookmark Submit
    public function delete()
    {
        // Check User Input
        $target = (int) Input::get("target");
        $type = 'torrent';

        // Check Input
        if (!isset($target)) {
            Redirect::autolink(URLROOT, "No target selected...");
        }

        // Check Bookmarked
        $arr = DB::column('bookmarks', 'COUNT(*)', ['targetid'=>$target,'type'=>$type,'userid'=>Users::get('id')]);
        if (!$arr) {
            Redirect::autolink(URLROOT, "ID not found in your bookmarks list...");
        }

        // Delete Bookmark
        DB::delete('bookmarks', ['targetid' =>$target, 'type' => $type, 'userid'=>Users::get('id')]);
        if ($type === 'torrent') {
            Redirect::autolink(URLROOT."/profile?id=".Users::get('id')."", "Book Mark Deleted...");
        } else {
            
        }
    }

}