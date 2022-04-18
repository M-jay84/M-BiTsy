<?php

class Adminban
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // No Default Page
    public function index()
    {
        Redirect::to(URLROOT . "/admincp");
    }

    // Ip Ban Default Page
    public function ip()
    {
        // Get Input
        $do = $_GET['do'];
        
        // Delete Ban
        if ($do == "del") {
            if (!@count($_POST["delids"])) {
                Redirect::autolink(URLROOT . '/adminban/ip', Lang::T("NONE_SELECTED"));
            }

            $delids = array_map('intval', $_POST["delids"]);
            $delids = implode(', ', $delids);
            $res = Bans::whereIn($delids);

            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                Bans::delete(['id'=>$row['id']]);
                Logs::write("IP Ban ($row[first] - $row[last]) was removed by ".Users::get('id')." (".Users::get('username').")");
            }
            Redirect::autolink(URLROOT . '/adminban/ip', "Ban(s) deleted.");
        }

        // Add Ban
        if ($do == "add") {
            // Check User Input
            $first = trim($_POST["first"]);
            $last = trim($_POST["last"]);
            $comment = trim($_POST["comment"]);

            if ($first == "" || $last == "" || $comment == "") {
                Redirect::autolink(URLROOT . '/adminban/ip', Lang::T("MISSING_FORM_DATA"));
            }

            Bans::insert(TimeDate::get_date_time(), Users::get('id'), $first, $last, $comment);
        }

        // Pagination
        $count = get_row_count("bans");
        list($pagerbuttons, $limit) = Pagination::pager(50, $count, URLROOT . "/adminban/ip?"); // 50 per page
        $res = DB::run("SELECT bans.*, users.username FROM bans LEFT JOIN users ON bans.addedby=users.id ORDER BY added $limit");

        // Init Data
        $data = [
            'title' => Lang::T("BANNED_IPS"),
            'count' => $count,
            'pagerbuttons' => $pagerbuttons,
            'res' => $res,
        ];

        // Load View
        View::render('ban/ip', $data, 'admin');
    }

    // Email Ban Default Page
    public function email()
    {
        // Check User Input
        $remove = (int) $_GET['remove'];

        // Delete Ban
        if (Validate::Id($remove)) {
            DB::delete('email_bans', ['id'=>$remove], false);
            Logs::write(sprintf(Lang::T("EMAIL_BANS_REM"), $remove, Users::get("username")));
            Redirect::autolink(URLROOT . '/adminban/email', Lang::T("EMAIL_BAN_DELETED"));
        }

        // Add Ban
        if ($_GET["add"] == '1') {
            // Check User Input
            $mail_domain = trim($_POST["mail_domain"]);
            $comment = trim($_POST["comment"]);
            if (!$mail_domain || !$comment) {
                Redirect::autolink(URLROOT . '/adminban/email', Lang::T("MISSING_FORM_DATA") . ".");
            }
            DB::insert('email_bans', ['added'=>TimeDate::get_date_time(), 'addedby'=>Users::get('id'), 'mail_domain'=>$mail_domain, 'comment'=>$comment]);
            Logs::write(sprintf(Lang::T("EMAIL_BANS_ADD"), $mail_domain, Users::get("username")));
            Redirect::autolink(URLROOT . '/adminban/email', Lang::T("EMAIL_BAN_ADDED"));
        }

        // Pagination
        $count = DB::column('email_bans', 'count(id)', '');
        list($pagerbuttons, $limit) = Pagination::pager(40, $count, URLROOT . "/adminban/email?");
        $res = DB::raw('email_bans', '*', '', "ORDER BY added DESC $limit");

        // Set Title
        $title = Lang::T("EMAIL_BANS");

        // Init Data
        $data = [
            'title' => $title,
            'count' => $count,
            'res' => $res,
            'pagerbuttons' => $pagerbuttons,
            'limit' => $limit,
        ];

        // Load View
        View::render('ban/email', $data, 'admin');
    }

    // Torrent Ban Default Page
    public function torrent()
    {
        // Pagination
        $count = DB::column('torrents', 'COUNT(*)', ['banned'=>'yes']);
        list($pagerbuttons, $limit) = Pagination::pager(50, $count, URLROOT."/adminban/torrent?");
        $resqq = DB::raw('torrents', 'id, name, seeders, leechers, visible, banned, external', ['banned'=>'yes'], 'ORDER BY name');
        
        // Set Title
        $title = "Banned " . Lang::T("TORRENT_MANAGEMENT");

        // Init Data
        $data = [
            'title' => $title,
            'count' => $count,
            'pagerbuttons' => $pagerbuttons,
            'resqq' => $resqq,
        ];

        // Load View
        View::render('ban/torrents', $data, 'admin');
    }

}