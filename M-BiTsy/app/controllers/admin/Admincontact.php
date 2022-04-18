<?php

class Admincontact
{
    
    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }
    
    // Contact Staff Default Page
    public function index()
    {
        // Get Contact Staff Data
        $res = DB::run("SELECT * FROM staffmessages ORDER BY id desc");

        // Init Data
        $data = [
            'title' => 'Staff PMs',
            'res' => $res,
        ];

        // Load View
        View::render('contact/index', $data, 'admin');
    }

    // View Message Default Page
    public function viewpm()
    {
        // Check User Input
        $pmid = (int) $_GET["id"];

        // Get Contact Staff Data
        $arr4 = DB::select('staffmessages', ' id, subject, sender, added, msg, answeredby, answered', ['id'=>$pmid]);
        $subject = $arr4["subject"];   
        $elapsed = TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr4["added"]));

        if ($arr4["answered"] == '0') {
            $answered = "<font color=red><b>No</b></font>";
        } else {
            $answered = "<font color=blue><b>Yes</b></font> by <a href='" . URLROOT . "/profile/read?id=$arr4[answeredby]>" . Users::coloredname($arr4["answeredby"]) . "</a> (<a href=" . URLROOT . "/admincontact/viewanswer?id=$pmid>Show Answer</a>)";
        }

        if ($arr4["answered"] == '0') {
            $setanswered = "[<a href=" . URLROOT . "/admincontact/setanswered?id=$arr4[id]>Mark Answered</a>]";
        } else {
            $setanswered = "";
        }

        // Init Data
        $data = [
            'title' => 'Staff PMs',
            'elapsed' => $elapsed,
            'added' => $arr4["added"],
            'subject' => $subject,
            'answeredby' => $arr4["answeredby"],
            'answered' => $answered,
            'setanswered' => $setanswered,
            'msg' => $arr4["msg"],
            'sender' => $arr4["sender"],
            'pmid' => $arr4["id"],
        ];

        // Load View
        View::render('contact/viewpm', $data, 'admin');
    }

    // Message Reply Default Page
    public function reply()
    {
        // Check User Input
        $answeringto = $_GET["answeringto"];
        $receiver = (int) $_GET["receiver"];

        // Check Correct Input
        if (!Validate::Id($receiver)) {
            Redirect::autolink(URLROOT . '/admincontact', "Invalid id.");
        }

        // Get Data
        $res2 = DB::raw('staffmessages', '*', ['id'=>$answeringto]);

        // Init Data
        $data = [
            'title' => 'Staff PMs',
            'res2' => $res2,
            'answeringto' => $answeringto,
            'receiver' => $receiver,
        ];

        // Load View
        View::render('contact/reply', $data, 'admin');
    }

    // Message Reply Form Submit
    public function takeanswer()
    {
        // Check User Input
        $receiver = (int) $_POST["receiver"];
        $answeringto = $_POST["answeringto"];
        $msg = trim($_POST["msg"]);

        // Check Correct Input
        if (!Validate::Id($receiver)) {
            Redirect::autolink(URLROOT . '/admincontact', "Invalid ID");
        }

        if (!$msg) {
            Redirect::autolink(URLROOT . '/admincontact', "Please enter something!");
        }

        // Take Answer
        DB::update('staffmessages', ['answered' => 1, 'answeredby' => Users::get("id"),'answer'=>$msg], ['id' => $answeringto]);
        Redirect::autolink(URLROOT . '/admincontact', "Staff Message $answeringto has been answered.");
    }

    // Message Set Answered Submit
    public function setanswered()
    {
        // Check User Input
        $id = (int) $_GET["id"];

        // Message Set Answered Submit
        DB::update('staffmessages', ['answered' => 1, 'answeredby' => Users::get('id'),'answer'=>"Marked as answer by ".Users::get('id').""], ['id' => $id]);
        Redirect::autolink(URLROOT . "/admincontact/viewpm?id=$id", "Staff Message $id has been set as answered.");
    }

    // View Answer Default Page
    public function viewanswer()
    {
        // Check User Input
        $pmid = (int) $_GET["id"];

        // Get Data
        $arr4 =  DB::select('staffmessages', 'id, subject, sender, added, msg, answeredby, answered, answer', ['id'=>$pmid]);

        if ($arr4['subject'] == "") {
            $subject = "No subject";
        } else {
            $subject = "<a style='color: darkred' href=".URLROOT."/admincontact/viewpm&id=$pmid>$arr4[subject]</a>";
        }

        if ($arr4['answer'] == "") {
            $answer = "This message has not been answered yet!";
        } else {
            $answer = $arr4["answer"];
        }

        // Init Data
        $data = [
            'title' => 'Staff PMs',
            'answer' => $answer,
            'added' =>  $arr4["added"],
            'subject' => $subject,
            'iidee' => $pmid,
            'sender' => $arr4["sender"],
            'answeredby' => $arr4["answeredby"],
        ];

        // Load View
        View::render('contact/viewanswer', $data, 'admin');
    }

    // Delete Message Default Page
    public function deletestaffmessage()
    {
        // Check User Input
        $id = (int) $_GET["id"];

        // Delete Message
        DB::delete('staffmessages', ['id'=>$id]);
        Redirect::autolink(URLROOT . "/admincontact", "Staff Message $id has been deleted.");
    }

    // Mark Answers Default Page (index)
    public function takecontactanswered()
    {
        // Get Data
        $res = DB::run("SELECT id FROM staffmessages WHERE answered=0 AND id IN (" . implode(", ", $_POST['setanswered']) . ")");
        
        // Update Answered
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            DB::update('staffmessages', ['answered' => 1, 'answeredby' => Users::get('id'),'answer'=>"Marked as answer by ".Users::get('id').""], ['id' => $arr['id']]);
        }

        Redirect::autolink(URLROOT."/admincontact", "Staff Messages have been marked as answered.");
    }

}