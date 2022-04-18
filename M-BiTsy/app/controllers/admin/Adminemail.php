<?php

class Adminemail
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Massemail Default Page
    public function index()
    {
        // Get Group Data
        $res = DB::raw('groups', 'group_id, level', '', 'ORDER BY `group_id` ASC');
        
        // Init Data
        $data = [
            'title' => Lang::T("Mass Email"),
            'res' => $res,
        ];

        // Load View
        View::render('message/massemail', $data, 'admin');
    }

    // Mass Email Form Submit
    public function sendemail()
    {
        // Check User Input
        $msg_log = "Sent to classes: ";
        @set_time_limit(0);
        $subject = $_POST["subject"];
        $body = format_comment($_POST["body"]);

        // Check Correct Input
        if (!$subject || !$body) {
            Redirect::autolink(URLROOT . "/adminemail", "No subject or body specified.");
        }

        if (!@count($_POST["groups"])) {
            Redirect::autolink(URLROOT . "/adminemail", "No groups Selected.");
        }

        // Get Group Data
        $ids = array_map("intval", $_POST["groups"]);
        $ids = implode(", ", $ids);
        $res_log = DB::run("SELECT DISTINCT level FROM `groups` WHERE group_id IN ($ids)");
        while ($row_log = $res_log->fetch(PDO::FETCH_ASSOC)) {
            $msg_log .= $row_log["level"] . ", ";
        }
        
        // Send Email
        $res = DB::run("SELECT u.email FROM users u LEFT JOIN `groups` g ON u.class = g.group_id WHERE u.enabled = 'yes' AND u.status = 'confirmed' AND u.class IN ($ids)");
        $siteemail = Config::get('SITEEMAIL');
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $TTMail = new TTMail();
            $TTMail->Send($row["email"], $subject, $body, "Content-type: text/html; charset=utf-8", "-f$siteemail");
        }

        Logs::write("<b><font color='Magenta'>A Mass E-Mail</font> was sent by (<font color='Navy'>".Users::get('username')."</font>) $msg_log<b>");
        Redirect::autolink(URLROOT . "/adminemail", "<b><font color='#ff0000'>Mass mail sent....</font></b>");
    }

}