<?php

class Mailbox
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Mailbox Overview Default Page
    public function overview()
    {
        // Count All Messages
        $arr = Messages::countmsg();

        // Init Dta
        $data = [
            'title' => Lang::T('MY_MESSAGES'),
            'inbox' => $arr['inbox'],
            'unread' => $arr['unread'],
            'outbox' => $arr['outbox'],
            'draft' => $arr['draft'],
            'template' => $arr['template'],
        ];

        // Load View
        View::render('mailbox/overview', $data, 'user');
    }


	// Sort Message Array By Type
    public static function msgdetails($type, $arr = [])
    {
        // Whos Sender
        if ($arr["sender"] == Users::get('id')) {
            $sender = "Yourself";
        } elseif (Validate::Id($arr["sender"])) {
            $sender = "<a href=\"/profile?id=$arr[sender]\">" . ($arr["sender"] ? Users::coloredname($arr["sender"]) : "[Deleted]") . "</a>";
        } else {
            $sender = Lang::T("SYSTEM");
        }

        // Whos Reciever
        if ($arr["receiver"] == Users::get('id')) {
            $receiver = "Yourself";
        } elseif (Validate::Id($arr["receiver"])) {
            $receiver = "<a href=\"" . URLROOT . "/profile?id=$arr[receiver]\">" . ($arr["receiver"] ? Users::coloredname($arr["receiver"]) : "[Deleted]") . "</a>";
        } else {
            $receiver = Lang::T("SYSTEM");
        }

        // Subject
        $subject = "<a href='" . URLROOT . "/message?id=" . $arr["id"] . "&type=$type'><b>" . format_comment($arr["subject"]) . "</b></a>";
        $added = TimeDate::utc_to_tz($arr["added"]);

        // Unread
        if ($arr["unread"] == "yes") {
            $unread = "<i class='fa fa-file-text tticon-red' title='UnRead'></i>";
        } else {
            $unread = "<i class='fa fa-file-text' title='Read'></i>";
        }

        // Return In Array
        $arr = array($sender, $receiver, $subject, $unread, $added);
        return $arr;
    }


    // Mailbox Default Page
    public function index()
    {
        // Check User Input
        $type = isset($_GET['type']) ? $_GET['type'] : 'inbox';

        // Pagination
        $arr = Messages::msgPagination($type);
        $res = DB::run("SELECT * FROM messages WHERE $arr[where] $arr[limit]");
        
        // Init Dta
        $data = [
            'title' => $type,
            'pagename' => $arr['pagename'] ?? 'inbox',
            'pagerbuttons' => $arr['pagerbuttons'],
            'mainsql' => $res,
        ];

        // Load View
        View::render('mailbox/index', $data, 'user');
    }

}