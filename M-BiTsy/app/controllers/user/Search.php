<?php

class Search
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 1);
    }

    // Validate User
    public function check()
    {
        if (Users::get("view_torrents") == "no" && Config::get('MEMBERSONLY')) {
                Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }
    }

    // Search Default Page
    public function index()
    {
        // Validate User
        $this->check();

        // Search Torrent ($_GET)
        $search = search();

        $count = DB::run("SELECT COUNT(*) FROM torrents " . $search['where'], $search['params'])->fetchcolumn();
        if ($count) {
            // Pagination
            list($pagerbuttons, $limit) = Pagination::pager(25, $count, URLROOT . "/search?$search[url]$search[pagerlink]");
            $res = Torrents::search($search['where'], $search['orderby'], $limit, $search['params']);

            // sET tITLE
            if (!$search['keyword'] == '') {
                $title = Lang::T("SEARCH_RESULTS_FOR") . " \"" . htmlspecialchars($search['keyword']) . "\"";
            } else {
                $title = Lang::T("SEARCH");
            }

            // Init Data
            $data = [
                'title' => $title,
                'res' => $res,
                'pagerbuttons' => $pagerbuttons,
                'keyword' => $search['keyword'],
                'url' => $search['url'],
            ];

            // Load View
            View::render('search/search', $data, 'user');

        } else {
            Redirect::autolink(URLROOT . "/search", "Nothing Found Try Again");
        }
    }

    // Search Need Seed Default Page
    public function needseed()
    {
        // Validate User
        $this->check();

        // Get Torrent Data
        $res = DB::run("SELECT torrents.id, torrents.name, torrents.owner, torrents.external, torrents.size, torrents.seeders, torrents.leechers, torrents.times_completed, torrents.added, users.username FROM torrents LEFT JOIN users ON torrents.owner = users.id WHERE torrents.banned = 'no' AND torrents.leechers > 0 AND torrents.seeders <= 1 ORDER BY torrents.seeders");
        if ($res->rowCount() == 0) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_NEED_SEED"));
        }

        // Init Data
        $data = [
            'title' => Lang::T("TORRENT_NEED_SEED"),
            'res' => $res,
        ];

        // Load View
        View::render('search/needseed', $data, 'user');
    }

    // Request Reseed Form Submit
    public function reseed()
    {
        // Check User
        $id = (int) $_GET["id"];

        // Validate User
        $this->check();

        if (isset($_COOKIE["reseed$id"])) {
            Redirect::autolink(URLROOT, Lang::T("RESEED_ALREADY_ASK"));
        }

        // Check Torrent
        $row = DB::select('torrents', 'owner,banned,external', ['id' => $id]);
        if (!$row || $row["banned"] == "yes" || $row["external"] == "yes") {
            Redirect::autolink(URLROOT, Lang::T("TORRENT_NOT_FOUND"));
        }

        // Send Message
        $res2 = DB::run("SELECT users.id FROM completed LEFT JOIN users ON completed.userid = users.id WHERE users.enabled = 'yes' AND users.status = 'confirmed' AND completed.torrentid = $id");
        $message = sprintf(Lang::T('RESEED_MESSAGE'), Users::get('username'), URLROOT, $id);
        while ($row2 = $res2->fetch(PDO::FETCH_ASSOC)) {
            DB::insert('messages', ['subject' => Lang::T("RESEED_MES_SUBJECT"), 'sender' => Users::get('id'), 'receiver' => $row2['id'], 'added' => TimeDate::get_date_time(), 'msg' => $message]);
        }
        if ($row["owner"] && $row["owner"] != Users::get("id")) {
            DB::insert('messages', ['subject' => 'Torrent Reseed Request', 'sender' => Users::get('id'), 'receiver' => $row['owner'], 'added' => TimeDate::get_date_time(), 'msg' => $message]);
        }

        setcookie("reseed$id", $id, time() + 86400, '/');
        Redirect::autolink(URLROOT, Lang::T("RESEED_SENT"));
    }

    // Search Today Default Page
    public function today()
    {
        // Validate User
        $this->check();

        $date_time = TimeDate::get_date_time(TimeDate::gmtime() - (3600 * 24)); // the 24 is the hours you want listed
        
        // Get Cat Data
        $catresult = DB::raw('categories', 'id, name', '', 'ORDER BY sort_index');

        // Init Data
        $data = [
            'title' => Lang::T("TODAYS_TORRENTS"),
            'date_time' => $date_time,
            'catresult' => $catresult,
        ];

        // Load View
        View::render('search/today', $data, 'user');
    }

    // Search Browse Default Page
    public function browse()
    {
        // Validate User
        $this->check();

        // Search Torrent ($_GET)
        $search = search();

        $count = DB::run("SELECT COUNT(*) FROM torrents LEFT JOIN categories ON category = categories.id $search[where]", $search['params'])->fetchColumn();
        $catsquery = Torrents::getCatByParent();

        if ($count) {
            // Pagination
            list($pagerbuttons, $limit) = Pagination::pager(25, $count, URLROOT . "/search/browse" . $search['url'] . $search['pagerlink']);
            $res = DB::run("SELECT torrents.id, torrents.anon, torrents.announce, torrents.category, torrents.sticky, 
                                    torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, 
                                    torrents.tube, torrents.tmdb, torrents.imdb, torrents.size, torrents.added, torrents.comments, torrents.numfiles, 
                                    torrents.filename, torrents.owner, torrents.external, torrents.freeleech, 
                                    categories.name AS cat_name, categories.parent_cat AS cat_parent, 
                                    categories.image AS cat_pic, users.username, users.privacy, 
                                    IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating 
                                    FROM torrents 
                                    LEFT JOIN categories 
                                    ON category = categories.id 
                                    LEFT JOIN users 
                                    ON torrents.owner = users.id $search[where] $search[orderby] $limit", $search['params']);
        } else {
            unset($res);
        }

        // Get Cat Data
        $cats = DB::raw('categories', '*', '', 'ORDER BY parent_cat, name');

        // Init Data
        $data = [
            'title' => Lang::T("BROWSE_TORRENTS"),
            'res' => $res,
            'pagerbuttons' => $pagerbuttons,
            'catsquery' => $catsquery,
            'url' => $search['url'],
            'parent_cat' => $search['parent_cat'],
            'count' => $count,
            'wherecatina' => $search['wherecatina'],
            'cats' => $cats
        ];

        // Load View
        View::render('search/browse', $data, 'user');
    }

    // Search By Tag Default Page
    public function tags()
    {
        // Validate User
        $this->check();
        
        // Check User Input
        $name = $_GET['name'] ?? '';

        if ($name == '') {
            Redirect::autolink(URLROOT, Lang::T("NO_Tag"));
        }

        // Get Tag Data
        $stmt = DB::run("SELECT torrentid FROM tagtorrent WHERE type1 = ? OR type2 = ? OR type3 = ?", [$name, $name, $name])->fetchAll();
        if (count($stmt) == 0) {
            Redirect::autolink(URLROOT, Lang::T("NO_Tag"));
        }

        // String Of IDS
        $array = array();
        foreach ($stmt as $item) {
            array_push($array, $item['torrentid']);
        }
        $ids = implode(",", $array);
        $prefix = ',';
        if (substr($ids, 0, strlen($prefix)) == $prefix) {
            $ids = substr($ids, strlen($prefix));
        }

        // Get Torrent Data
        $stmt = DB::run("SELECT torrents.id, torrents.name, torrents.owner, torrents.external, torrents.size,
                         torrents.seeders, torrents.leechers, torrents.times_completed, torrents.added, 
                         users.username
                         FROM torrents 
                         LEFT JOIN users 
                         ON torrents.owner = users.id
                         WHERE torrents.id 
                         IN ($ids) ");

        if ($stmt->rowCount() == 0) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_FOUND"));
        }

        // Init Data
        $data = [
            'title' => Lang::T("Search Tags"),
            'stmt' => $stmt,
        ];

        // Load View
        View::render('search/tag', $data, 'user');
    }

}