<?php

class Home
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 1, true);
    }

    // Home Default Page
    public function index()
    {
        Style::header(Lang::T("HOME"));
        
        // Check
        if (file_exists("check.php") && Users::get("class") == 7) {
            Style::begin("<font class='error'>" . htmlspecialchars('WARNING') . "</font>");
            echo '<div class="alert ttalert">check still exists, please delete or rename the file as it could pose a security risk<br /><br /><a href="check.php">View /check</a> - Use to check your config!<br /></div>';
            Style::end();
        }

        // Start Hit And Run Warning
        if (Config::get('HNR_ON')) {
            $count = DB::column('snatched', 'count(hnr)', ['uid'=>Users::get("id"),'hnr'=>'yes']);
            if ($count > 0) {
                $data = [
                    'count' => $count,
                ];
                View::render('home/hitnrun', $data);
            }
        }

        // Site Notice
        if (Config::get('SITENOTICEON')) {
            $data = [];
            View::render('home/notice', $data);
        }

        // Site News
        if (Config::get('NEWSON') && Users::get('view_news') == "yes") {
            $data = [];
            View::render('home/news', $data);
        }

        // Shoutbox
        if (Config::get('SHOUTBOX') && !(Users::get('hideshoutbox') == 'yes')) {
            $data = [];
            View::render('home/shoutbox', $data);
        }

        // Last Forum Post On Index
        if (Config::get('LATESTFORUMPOSTONINDEX')) {
            $data = [];
            View::render('home/lastforumpost', $data);
        }

        // Last Forum Post On Index
        if (Config::get('FORUMONINDEX')) {
            $forums_res = Forums::getIndex();
            if ($forums_res->rowCount() == 0) {
                Style::begin(Lang::T("Forums"));
                echo Lang::T("NO fORUMS fOUND");
                Style::end();
            } else {
                $subforums_res = Forums::getsub();
                $data = [
                    'mainquery' => $forums_res,
                    'mainsub' => $subforums_res,
                ];
                View::render('home/forum', $data);
            }
        }

        // Carousel
        if ($_SESSION['loggedin'] && Users::get("view_torrents") == "yes") {
            $stmt = DB::run("SELECT torrents.id, torrents.category, torrents.vip, torrents.image1, torrents.image2, torrents.tmdb, torrents.leechers, 
            torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.freeleech,
            categories.name AS cat_name, categories.image AS cat_pic, categories.parent_cat AS cat_parent
            FROM torrents
            LEFT JOIN categories ON category = categories.id
            LEFT JOIN users ON torrents.owner = users.id
            WHERE visible = 'yes' AND banned = 'no' 
            ORDER BY added DESC limit 25");

            $data = [
                'sql' => $stmt
            ];
            View::render('home/carousel', $data);
        }

        // Grid
        if ($_SESSION['loggedin'] && Users::get("view_torrents") == "yes") {
            $stmt = DB::run("SELECT torrents.id, torrents.category, torrents.vip, torrents.image1, torrents.image2, torrents.tmdb, torrents.leechers, 
                    torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.freeleech,
                    categories.name AS cat_name, categories.image AS cat_pic, categories.parent_cat AS cat_parent
                    FROM torrents
                    LEFT JOIN categories ON category = categories.id
                    LEFT JOIN users ON torrents.owner = users.id
                    WHERE visible = 'yes' AND banned = 'no' 
                    ORDER BY added DESC limit 8");

            $data = [
                'sql' => $stmt
            ];
            View::render('home/grid', $data);
        }

        // Latest Torrents
        if (Config::get('MEMBERSONLY') && !$_SESSION['loggedin']) {
            $data = [
                'message' => Lang::T("BROWSE_MEMBERS_ONLY")
            ];
            View::render('home/notorrents', $data);
        } else {
            $query = "SELECT torrents.id, torrents.anon, torrents.descr, torrents.announce, torrents.category, torrents.sticky,  torrents.vip,  torrents.tube,  torrents.tmdb, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, torrents.image1, torrents.image2,
            categories.name AS cat_name, categories.image AS cat_pic, categories.parent_cat AS cat_parent,
            users.username, users.privacy,
            IF(torrents.numratings < 1, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating
            FROM torrents
            LEFT JOIN categories ON category = categories.id
            LEFT JOIN users ON torrents.owner = users.id
            WHERE visible = 'yes' AND banned = 'no'
            ORDER BY sticky, added DESC, id DESC LIMIT 25";
            $res = DB::run($query);
            if ($res->rowCount() > 0) {
                $data = [
                    'torrtable' => $res,
                ];
                View::render('home/torrent', $data);
            } else {
                $data = [];
                View::render('home/nothingfound', $data);
            }
            if ($_SESSION['loggedin'] == true) {
                DB::update('users', ['last_browse' =>TimeDate::gmtime()], ['id' => Users::get('id')]);
            }
        }

        // Visited Users
        $stmt = DB::run("SELECT id, username, class, donated, warned, avatar FROM users WHERE enabled = 'yes' AND status = 'confirmed' AND privacy !='strong' AND UNIX_TIMESTAMP('".timedate::get_date_time()."') - UNIX_TIMESTAMP(users.last_access) <= 86400");
        $data = [
            'stmt' => $stmt,
        ];
        View::render('home/visitedusers', $data);

        // Disclaimer
        if (Config::get('DISCLAIMERON')) {
            $data = [];
            View::render('home/disclaimer', $data);
        }

        Style::footer();
    }

}