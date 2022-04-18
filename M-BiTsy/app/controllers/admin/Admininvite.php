<?php

class Admininvite
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Invited Default Page
    public function index()
    {
        // Pagination
        $count = DB::run("SELECT COUNT(*) FROM users WHERE status = 'confirmed' AND invited_by != '0'")->fetchColumn();
        list($pagerbuttons, $limit) = Pagination::pager(25, $count, 'Admininvite?');
        $res = DB::run("SELECT u.id, u.username, u.email, u.added, u.last_access, u.class, u.invited_by, i.username as inviter FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'confirmed' AND u.invited_by != '0' ORDER BY u.added DESC $limit");
        
        // Init Data
        $data = [
            'title' => Lang::T("Invited Users"),
            'count' => $count,
            'pagerbuttons' => $pagerbuttons,
            'res' => $res,
        ];

        // Load View
        View::render('invite/index', $data, 'admin');
    }

    // Invite Form Submit
    public function submit()
    {
        // Check User Input
        $url = $_POST['pending'] == 0 ? '/admininvite' : '/admininvite/pending';
        if (!@count($_POST["users"])) {
            Redirect::autolink(URLROOT . $url, "Nothing Selected.");
        }

        // Delete Invite
        $ids = array_map("intval", $_POST["users"]);
        $ids = implode(", ", $ids);
        $res = DB::run("SELECT u.id, u.invited_by, i.invitees FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' AND u.id IN ($ids)");
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            # We remove the invitee from the inviter and give them back there invite.
            $invitees = str_replace("$row[id] ", "", $row["invitees"]);
            $views = $row["invites"] + 1;
            DB::update('users', ['invites'=>$views,'invitees'=>$invitees], ['id'=>$row['invited_by']]);
            Users::deleteuser($row['id']);
        }
        Redirect::autolink(URLROOT . $url, "Entries Deleted");
    }

    // Pendings Invites Default Page
    public function pending()
    {
        // Pagination
        $count = DB::run("SELECT COUNT(*) FROM users WHERE status = 'pending' AND invited_by != '0'")->fetchColumn();
        list($pagerbuttons, $limit) = Pagination::pager(25, $count, '/Admininvite/pending?');
        $res = DB::run("SELECT u.id, u.username, u.email, u.added, u.invited_by, i.username as inviter FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' ORDER BY u.added DESC $limit");
        
        // Init Data
        $data = [
            'title' => Lang::T("Invited Pending Users"),
            'count' => $count,
            'pagerbuttons' => $pagerbuttons,
            'res' => $res,
        ];

        // Load View
        View::render('invite/pending', $data, 'admin');
    }

}
