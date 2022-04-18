<?php

class Adminmessage
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Message Default Page
    public function index()
    {
        // Check User
        if(Users::get('class') > _ADMINISTRATOR) {
            Redirect::to(URLROOT.'/admincp');
        }
        
        // Pagination
        $count = DB::run("SELECT COUNT(*) FROM messages WHERE location in ('in', 'both')")->fetchColumn();
        list($pagerbuttons, $limit) = Pagination::pager(40, $count, "/adminmessage?;");
        $res = DB::run("SELECT * FROM messages WHERE location in ('in', 'both') ORDER BY id DESC $limit");

        // Init Data
        $data = [
            'title' => Lang::T("Message Spy"),
            'res' => $res,
            'pagerbuttons' => $pagerbuttons,
        ];

        // Load View
        View::render('message/spypm', $data, 'admin');
    }

    // Message Form Submit
    public function delete()
    {
        if ($_POST["delall"]) {
            DB::delete('messages', ['active'=>'yes']);
        } else {
            if (!@count($_POST["del"])) {
                Redirect::autolink(URLROOT . '/adminmessage', Lang::T("NOTHING_SELECTED"));
            }
            $ids = array_map("intval", $_POST["del"]);
            $ids = implode(", ", $ids);
            DB::deleteByIds('messages', 'id', $ids);
        }
        Redirect::autolink(URLROOT . '/adminmessage', Lang::T("CP_DELETED_ENTRIES"));
    }

    // Mass Message Default Page
    public function masspm()
    {
        // Get Group Data
        $res = DB::run("SELECT group_id, level FROM `groups`");

        // Init Data
        $data = [
            'title' => Lang::T("Mass Private Message"),
            'res' => $res,
        ];

        // Load View
        View::render('message/masspm', $data, 'admin');
    }
    
    // Mass Message Form Submit
    public function send()
    {
        // Check User Input
        $sender_id = ($_POST['sender'] == 'system' ? 0 : Users::get('id'));
        $msg = $_POST['msg'];
        $subject = $_POST["subject"];

        // Check Correct Input
        if (!$msg) {
            Redirect::autolink(URLROOT . '/adminmessage/masspm', "Please Enter Something!");
        }

        $updateset = array_map("intval", $_POST['clases']);
        $query = DB::run("SELECT id FROM users WHERE class IN (" . implode(",", $updateset) . ") AND enabled = 'yes' AND status = 'confirmed'");
        while ($dat = $query->fetch(PDO::FETCH_ASSOC)) {
            DB::insert('messages', ['sender'=>$sender_id, 'receiver'=>$dat['id'], 'added'=>TimeDate::get_date_time(), 'msg'=>$msg, 'subject'=> $subject]);
        }

        Logs::write("A Mass PM was sent by ".Users::get('id')."");
        Redirect::autolink(URLROOT . "/adminmessage/masspm", Lang::T("SUCCESS"), "Mass PM Sent!");
    }

}