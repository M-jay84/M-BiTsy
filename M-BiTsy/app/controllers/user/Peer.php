<?php

class Peer
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // No Default Page
    public function index()
    {
        Redirect::to(URLROOT);
    }

    // User Seeding (Profile) Default Page
    public function seeding()
    {
        // Check User Input
        $id = (int) Input::get("id");

        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, "Bad ID.");
        }

        if (Users::get("view_users") == "no" && Users::get("id") != $id) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }

        // Check User Input
        $user =  DB::raw('users', '*', ['id'=>$id])->fetch();
        if (!$user) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_WITH_ID") . " $id.");
        }

        if (($user["enabled"] == "no" || ($user["status"] == "pending"))) {
            Redirect::autolink(URLROOT, Lang::T("NO_ACCESS_ACCOUNT_DISABLED"));
        }

        // Get Peer Data
        $res = DB::raw('peers', 'torrent, uploaded, downloaded', ['userid'=>$id,'seeder'=>'yes']);
        if ($res->rowCount() > 0) {
            $seeding = peerstable($res) ?? '';
        }

        $res = DB::raw('peers', 'torrent, uploaded, downloaded', ['userid'=>$id,'seeder'=>'no']);
        if ($res->rowCount() > 0) {
            $leeching = peerstable($res);
        }

        $title = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($id));
        
        // Init Data
        $data = [
            'id' => $id,
            'title' => $title,
            'leeching' => $leeching ?? '',
            'seeding' => $seeding ?? '',
            'uid' => $user["id"],
            'username' => $user["username"],
            'privacy' => $user["privacy"],
        ];

        // Load View
        View::render('peer/seeding', $data, 'user');
    }

    // User Uploading (Profile) Default Page
    public function uploaded()
    {
        // Check User Input
        $id = (int) Input::get("id");

        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, "Bad ID.");
        }

        if (Users::get("view_users") == "no" && Users::get("id") != $id) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }

        // Check User
        $user = DB::raw('users', '*', ['id'=>$id])->fetch();
        if (!$user) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_WITH_ID") . " $id.");
        }
        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && Users::get("edit_users") == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_ACCESS_ACCOUNT_DISABLED"));
        }
        
        $where = "";
        if (Users::get('control_panel') != "yes") {
            $where = "AND anon='no'";
        }

        // Pagination
        $count = DB::run("SELECT COUNT(*) FROM torrents WHERE owner='$id' $where")->fetchColumn();
        unset($where);
        $orderby = "ORDER BY id DESC";
        // Get sql info
        if ($count) {
            list($pager, $limit) = Pagination::pager(25, $count, URLROOT . "/peer/uploaded?id=$id&amp;");
            $res = DB::run("SELECT torrents.id, torrents.category, torrents.leechers, torrents.tmdb, torrents.imdb, torrents.tube, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, torrents.anon, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.announce FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE owner = $id $orderby $limit");
        } else {
            unset($res);
        }

        // Set Title
        $title = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($id));

        // Init Data
        $data = [
            'id' => $id,
            'title' => $title,
            'count' => $count,
            'res' => $res,
            'pager' => $pager,
        ];

        // Load View
        View::render('peer/uploaded', $data, 'user');
    }

    // Torrent Peers (Torrent) Default Page
    public function peerlist()
    {
        // Check User Input
        $id = (int) Input::get("id");

        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("THATS_NOT_A_VALID_ID"));
        }

        // Check User Input
        if (Users::get("view_torrents") == "no" && Config::get('MEMBERSONLY')) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }

        // Get Torrent Data
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.tmdb, torrents.imdb, torrents.tube, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $size = $row['size'];
        $shortname = CutName(htmlspecialchars($row["name"]), 40);
        $title = Lang::T("TORRENT_DETAILS_FOR") . " \"" . $shortname . "\"";

        if ($row["external"] != 'yes') {
            // Get Peer Data
            $query = DB::raw('peers', '*', ['torrent'=>$id], 'ORDER BY seeder DESC');
            $result = $query->rowCount();
            
            if ($result == 0) {
                Redirect::autolink(URLROOT, Lang::T("NO_ACTIVE_PEERS") . " $id.");
            } else {
                // Init Data
                $data = [
                    'id' => $id,
                    'title' => $title,
                    'query' => $query,
                    'size' => $size,
                ];

                // Load View
                View::render('peer/peerlist', $data, 'user');
            }
        } else {
            Redirect::autolink(URLROOT, 'Sorry External Torrent');
        }
    }

    // User Seeding (navbar) Popout Window
    public function popoutseed()
    {
        // Check User Input
        $id = (int) Input::get("id");

        if ($id != Users::get("id")) {
            echo Lang::T("FORUMS_NOT_ALLOWED");
        }

        // Get Peer Data
        $res = Peers::seedingTorrent($id, 'yes');
        if ($res->rowCount() > 0) {
            $seeding = peerstable($res);
        } else {
            $seeding = false;
        }
        if ($seeding) {
            print("$seeding");
        }
        if (!$seeding) {
            print("<B>Currently not seeding<BR><BR><a href=\"javascript:self.close()\">close window</a><BR>");
        }
    }

    // User Leeching (navbar) Popout Window
    public function popoutleech()
    {
        // Check User Input
        $id = (int) Input::get("id");

        if ($id != Users::get("id")) {
            echo Lang::T("FORUMS_NOT_ALLOWED");
        }
        
        // Get Peer Data
        $res = Peers::seedingTorrent($id, 'no');
        if ($res->rowCount() > 0) {
            $leeching = peerstable($res);
        } else {
            $leeching = false;
        }
        if ($leeching) {
            print("$leeching");
        }
        if (!$leeching) {
            print("<B>Not currently leeching!<BR><br><a href=\"javascript:self.close()\">close window</a><BR>\n");
        }
    }

}