<?php

class Admintorrentlang
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Torrent Language Default Page
    public function index()
    {
        // Get Torrent Lang Data
        $sql = DB::raw('torrentlang', '*', '', 'ORDER BY sort_index ASC');
        
        // Init Data
        $data = [
            'title' => Lang::T("TORRENT_LANGUAGES"),
            'sql' => $sql,
        ];

        // Load View
        View::render('torrentlang/index', $data, 'admin');
    }

    // Edit Torrent Language Default Page
    public function edit()
    {
        // Check User Input
        $id = (int) Input::get("id");

        // Check Correct Input
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "/admintorrentlang", Lang::T("INVALID_ID"));
        }

        // Get Torrent Lang Data
        $res = DB::raw('torrentlang', '*', ['id'=>$id]);
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT . "/admintorrentlang", "No Language with ID $id.");
        }

        if ($_GET["save"] == '1') {

            // Check User Input
            $name = $_POST['name'];
            $sort_index = $_POST['sort_index'];
            $image = $_POST['image'];

            // Check Correct Input
            if ($name == "") {
                Redirect::autolink(URLROOT."/admintorrentlang/edit", "Language cat cannot be empty!");
            }

            // Update Torrent Lang
            DB::update('torrentlang', ['name' =>$name, 'sort_index' =>$sort_index,'image' =>$image], ['id' => $id]);
            Redirect::autolink(URLROOT . "/admintorrentlang/torrentlang", Lang::T("Language was edited successfully."));

        } else {

            // Init Data
            $data = [
                'title' => Lang::T("TORRENT_LANGUAGES"),
                'id' => $id,
                'res' => $res,
            ];

            // Load View
            View::render('torrentlang/edit', $data, 'admin');

        }
    }

    // Delete Torrent Language Default Page
    public function delete()
    {
        // Check User Input
        $id = (int) $_GET["id"];

        if ($_GET["sure"] == '1') {

            // Check User Input
            $newlangid = (int) $_POST["newlangid"];
            if (!Validate::Id($id)) {
                Redirect::autolink(URLROOT."/admintorrentlang/delete", "Invalid Language item ID");
            }
            // Delete Torrent Lang
            DB::run("UPDATE torrents SET torrentlang=$newlangid WHERE torrentlang=$id");
            DB::delete('torrentlang', ['id'=>$id]);
            Redirect::autolink(URLROOT . "/admintorrentlang", Lang::T("Language Deleted OK."));

        } else {

            // Init Data
            $data = [
                'title' => Lang::T("TORRENT_LANGUAGES"),
                'id' => $id,
            ];

            // Load View
            View::render('torrentlang/delete', $data, 'admin');

        }
    }

    // Add Torrent Language Default Page
    public function add()
    {
        // Init Data
        $data = [
            'title' => Lang::T("TORRENT_LANGUAGES"),
        ];

        // Load View
        View::render('torrentlang/add', $data, 'admin');
    }

    // Add Torrent Language Form Submit
    public function takeadd()
    {
        // Check User Input
        $name = $_POST['name'];
        $sort_index = $_POST['sort_index'];
        $image = $_POST['image'];

        // Check Correct Input
        if ($name == "") {
            Redirect::autolink(URLROOT . "/admintorrentlang/add", "Name cannot be empty!");
        }

        // Insert Torrent Lang
        $ins = DB::insert('torrentlang', ['name'=>$name, 'sort_index'=>$sort_index, 'image'=>$image]);
        if ($ins) {
            Redirect::autolink(URLROOT . "/admintorrentlang", Lang::T("Language was added successfully."));
        } else {
            Redirect::autolink(URLROOT . "/admintorrentlang/add", "Unable to add Language");
        }
    }

}