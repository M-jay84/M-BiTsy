<?php

class Import
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(_UPLOADER, 2);
    }

    // Get Torrent Files
    private function gettorrentfiles()
    {
        $files = array();
        $dh = opendir(UPLOADDIR."/import/");
        while (false !== ($file = readdir($dh))) {
            if (preg_match("/\.torrent$/i", $file)) {
                $files[] = $file;
            }
        }
        closedir($dh);
        return $files;
    }

    // Import Default Page
    public function index()
    {
        //ini_set("upload_max_filesize",$max_torrent_size);
        $files = self::gettorrentfiles();
        
        // Check User
        if (Users::get("edit_torrents") != "yes") {
            Redirect::autolink(URLROOT, Lang::T("ACCESS_DENIED"));
        }

        // Init Data
        $data = [
            'title' => Lang::T("UPLOAD"),
            'files' => $files,
        ];

        // Load View
        View::render('import/index', $data, 'user');
    }

    // Import Form Submit
    public function submit()
    {
        $files = self::gettorrentfiles();
        //generate announce_urls[] from config.php
        $announce_urls = explode(",", strtolower(ANNOUNCELIST));
        set_time_limit(0);
        
        // Check User
        if (Users::get("edit_torrents") != "yes") {
            Redirect::autolink(URLROOT, Lang::T("ACCESS_DENIED"));
        }

        // Check Correct Input
        $catid = (int) Input::get("type");
        if (!Validate::Id($catid)) {
            $message = Lang::T("UPLOAD_NO_CAT");
        }

        if (empty($message)) {
            $r = DB::raw('categories', 'name, parent_cat', ['id'=>$catid])->fetch();

            Style::header(Lang::T("UPLOAD_COMPLETE"));
            Style::begin(Lang::T("UPLOAD_COMPLETE"));
            echo "<center>";
            echo "<b>Category:</b> " . htmlspecialchars($r['parent_cat']) . " -> " . htmlspecialchars($r['name']) . "<br />";
            for ($i = 0; $i < count($files); $i++) {
                $fname = $files[$i];
                $descr = Lang::T("UPLOAD_NO_DESC");
                $langid = (int) $_POST["lang"];
                preg_match('/^(.+)\.torrent$/si', $fname, $matches);
                $shortfname = $torrent = $matches[1];

                //parse torrent file
                $torrent_dir = UPLOADDIR."/torrents";
                $torInfo = new Parse();
                $tor = $torInfo->torr(UPLOADDIR."/import/$fname");

                $announce = $tor['announce'];
                $infohash = $tor['hash'];
                $creationdate = $tor["creation date"];
                $internalname = $tor['name'];
                $torrentsize = $tor['length'];
                $filecount = $tor['ttfilecount'];
                $annlist = $tor["announce-list"];
                $comment = $tor['comment'];
                $filelist = $tor['files'];

                $message = "<br /><br /><hr /><br /><b>$internalname</b><br /><br />Name: " . htmlspecialchars($fname) . "<br />message: ";
                //check announce url is local or external
                $announce_urls = explode(",", ANNOUNCELIST);
                if (!in_array($announce, $announce_urls)) {
                    $external = 'yes';
                } else {
                    $external = 'no';
                }

                if ($external === 'yes') {
                    $multi = array_flatten($annlist);
                    $announcelist = serialize($multi);
                }

                if (!Config::get('ALLOWEXTERNAL') && $external == 'yes') {
                    $message .= Lang::T("UPLOAD_NO_TRACKER_ANNOUNCE");
                    echo $message;
                    continue;
                }

                $name = $internalname;
                $name = str_replace(".torrent", "", $name);
                $name = str_replace("_", " ", $name);

                //anonymous upload
                $anonyupload = $_POST["anonycheck"];
                if ($anonyupload == "yes") {
                    $anon = "yes";
                } else {
                    $anon = "no";
                }

                $ret = DB::run("INSERT INTO torrents (filename, owner, name, descr, category, added, info_hash, size, numfiles, save_as, announce, external, torrentlang, anon, last_action, announcelist)
                          VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                    [$fname, Users::get('id'), $name, $descr, $catid, TimeDate::get_date_time(), $infohash, $torrentsize, $filecount, $fname, $announce, $external, $langid, $anon, TimeDate::get_date_time(), $announcelist]);
                $id = DB::lastInsertId();

                if ($ret->errorCode() == 1062) {
                    $message .= Lang::T("UPLOAD_ALREADY_UPLOADED");
                    echo $message;
                    continue;
                }

                if ($id == 0) {
                    $message .= Lang::T("UPLOAD_NO_ID");
                    echo $message;
                    continue;
                }

                copy(UPLOADDIR."/import/$files[$i]", "$torrent_dir/$id.torrent");

                //EXTERNAL SCRAPE
                if ($external == 'yes' && Config::get('UPLOADSCRAPE')) {
                    Tscraper::ScrapeId($id, $annlist, $infohash);
                }

                Logs::write("Torrent $id ($name) was Uploaded by Users::get(username]");
                $message .= "<br /><b>" . Lang::T("UPLOAD_OK") . "</b><br /><a href='" . URLROOT . "/torrent?id=" . $id . "'>" . Lang::T("UPLOAD_VIEW_DL") . "</a><br /><br />";
                echo $message;
                @unlink(UPLOADDIR."/import/$fname");
            }
            echo "</center>";
            Style::end();
            Style::footer();
            die;

        } else {
            Redirect::autolink(URLROOT, $message);
        }

    }

}
