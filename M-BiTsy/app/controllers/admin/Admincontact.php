<?php
class Admincontact
{
    public function __construct()
    {
        Auth::user(_MODERATOR, 2);
    }
    
    public function index()
    {
        $res = DB::run("SELECT * FROM staffmessages ORDER BY id desc");

        $data = [
            'title' => 'Staff PMs',
            'res' => $res,
        ];
        View::render('contact/index', $data, 'admin');
    }

    public function viewpm()
    {
        $pmid = (int) $_GET["pmid"];

        $arr4 = DB::select('staffmessages', ' id, subject, sender, added, msg, answeredby, answered', ['id'=>$pmid]);
        $answeredby = $arr4["answeredby"];

        $arr5 = DB::select('users', 'username', ['id'=>$answeredby]);
        $senderr = "" . $arr4["sender"] . "";

        if (Validate::Id($arr4["sender"])) {
            $arr2 = DB::select('users', 'username', ['id'=>$arr4["sender"]]);
            $sender = "<a href='" . URLROOT . "/profile/read?id=$senderr'>" . (Users::coloredname($senderr) ? Users::coloredname($senderr) : "[Deleted]") . "</a>";
        } else {
            $sender = "System";
        }

        $subject = $arr4["subject"];
        if ($arr4["answered"] == '0') {
            $answered = "<font color=red><b>No</b></font>";
        } else {
            $answered = "<font color=blue><b>Yes</b></font> by <a href='" . URLROOT . "/profile/read?id=$answeredby>" . Users::coloredname($answeredby) . "</a> (<a href=" . URLROOT . "/admincontact/viewanswer?pmid=$pmid>Show Answer</a>)";
        }

        if ($arr4["answered"] == '0') {
            $setanswered = "[<a href=" . URLROOT . "/admincontact/setanswered?id=$arr4[id]>Mark Answered</a>]";
        } else {
            $setanswered = "";
        }

        $iidee = $arr4["id"];

        $elapsed = TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr4["added"]));

        $data = [
            'title' => 'Staff PMs',
            'elapsed' => $elapsed,
            'sender' => $sender,
            'added' => $arr4["added"],
            'subject' => $subject,
            'answeredby' => $answeredby,
            'answered' => $answered,
            'setanswered' => $setanswered,
            'msg' => $arr4["msg"],
            'sender1' => $arr4["sender"],
            'iidee' => $iidee,
            'id' => $arr4["id"],
        ];
        View::render('contact/viewpm', $data, 'admin');
    }

    public function reply()
    {
        $answeringto = $_GET["answeringto"];
        $receiver = (int) $_GET["receiver"];

        if (!Validate::Id($receiver)) {
            Redirect::autolink(URLROOT . '/admincontact', "Invalid id.");
        }

        $res = DB::raw('users', '*', ['id'=>$receiver]);
        $res2 = DB::raw('staffmessages', '*', ['id'=>$answeringto]);

        $data = [
            'title' => 'Staff PMs',
            'res' => $res,
            'res2' => $res2,
            'answeringto' => $answeringto,
            'receiver' => $receiver,
        ];
        View::render('contact/reply', $data, 'admin');
    }

    public function takeanswer()
    {
        $receiver = (int) $_POST["receiver"];
        $answeringto = $_POST["answeringto"];

        if (!Validate::Id($receiver)) {
            Redirect::autolink(URLROOT . '/admincontact', "Invalid ID");
        }

        $userid = Users::get("id");
        $msg = trim($_POST["msg"]);
        $message = $msg;

        if (!$msg) {
            Redirect::autolink(URLROOT . '/admincontact', "Please enter something!");
        }

        DB::update('staffmessages', ['answered' => 1, 'answeredby' => $userid,'answer'=>$msg], ['id' => $answeringto]);
        $smsg = "Staff Message $answeringto has been answered.";
        Redirect::autolink(URLROOT . '/admincontact', $smsg);
    }

    public function setanswered()
    {
        $id = (int) $_GET["id"];

        DB::update('staffmessages', ['answered' => 1, 'answeredby' => Users::get('id'),'answer'=>"Marked as answer by ".Users::get('id').""], ['id' => $id]);
        $smsg = "Staff Message $id has been set as answered.";
        Redirect::autolink(URLROOT . "/admincontact/viewpm?pmid=$id", $smsg);
    }

    public function viewanswer()
    {
        $pmid = (int) $_GET["pmid"];

        $arr4 =  DB::select('staffmessages', 'id, subject, sender, added, msg, answeredby, answered, answer', ['id'=>$pmid]);
        $answeredby = $arr4["answeredby"];

        if (Validate::Id($arr4["sender"])) {
            $arr2 = DB::select('users', 'username', ['id'=>$arr4["sender"]]);
            $sender = "<a href=" . URLROOT . "/profile?id=" . $arr4["sender"] . ">" . ($arr2["username"] ? $arr2["username"] : "[Deleted]") . "</a>";
        } else {
            $sender = "System";
        }

        if ($arr4['subject'] == "") {
            $subject = "No subject";
        } else {
            $subject = "<a style='color: darkred' href=".URLROOT."/admincontact/viewpm&pmid=$pmid>$arr4[subject]</a>";
        }

        $iidee = $arr4["id"];

        if ($arr4['answer'] == "") {
            $answer = "This message has not been answered yet!";
        } else {
            $answer = $arr4["answer"];
        }

        $data = [
            'title' => 'Staff PMs',
            'answer' => $answer,
            'added' =>  $arr4["added"],
            'subject' => $subject,
            'iidee' => $iidee,
            'sender' => $sender,
            'answeredby' => $answeredby,
        ];
        View::render('contact/viewanswer', $data, 'admin');
    }

    public function deletestaffmessage()
    {
        $id = (int) $_GET["id"];

        if (!is_numeric($id) || $id < 1 || floor($id) != $id) {
            die;
        }

        DB::delete('staffmessages', ['id'=>$id]);
        $smsg = "Staff Message $id has been deleted.";
        Redirect::autolink(URLROOT . "/admincontact", $smsg);
    }

    public function takecontactanswered() // index checkbox
    {
        $res = DB::run("SELECT id FROM staffmessages WHERE answered=0 AND id IN (" . implode(", ", $_POST['setanswered']) . ")");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            DB::update('staffmessages', ['answered' => 1, 'answeredby' => Users::get('id'),'answer'=>"Marked as answer by ".Users::get('id').""], ['id' => $arr['id']]);
        }

        $smsg = "Staff Messages have been marked as answered.";
        Redirect::autolink(URLROOT."/admincontact", $smsg);
    }

}