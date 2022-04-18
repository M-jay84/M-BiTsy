<?php

class Upload
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Check User
    public static function checks()
    {
        if (Users::get("can_upload") == "no") {
            Redirect::autolink(URLROOT, Lang::T("UPLOAD_NO_PERMISSION"));
        }
        if (Config::get('UPLOADERSONLY') && Users::get("class") < _VIP) {
            Redirect::autolink(URLROOT, Lang::T("UPLOAD_ONLY_FOR_UPLOADERS"));
        }
    }

    // Upload Default Page
    public function index()
    {
        // Check User
        self::checks();

        // Get Announce List
        $announce_urls = explode(",", ANNOUNCELIST);
        
        // Init Data
        $data = [
            'title' => Lang::T("UPLOAD"),
            'announce_urls' => $announce_urls,
        ];
        
        // Load View
        View::render('upload/index', $data, 'user');
    }

    // Upload Form Submit
    public function submit()
    {
        // Check User
        self::checks();

        if (Input::exist()) {

            // Check Form Data.
            if (!isset($_POST['type'], $_POST['name'], $_FILES['torrent'])) {
                $message = Lang::T('MISSING_FORM_DATA');
            }

            // Upload File
            $tupload = new Tupload('torrent');
            if (($num = $tupload->getError())) {
                Redirect::autolink(URLROOT . '/upload', Lang::T("UPLOAD_ERR[$num]"));
            }

            // Get Name
            if (!($fname = $tupload->getName())) {
                $message = Lang::T("EMPTY_FILENAME");
            }

            // Check File Name
            if (!Validate::Filename($fname)) {
                $message = Lang::T("UPLOAD_INVALID_FILENAME");
            }

            // Check File Torrent
            if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches)) {
                $message = Lang::T("UPLOAD_INVALID_FILENAME_NOT_TORRENT");
            }

            // Check NFO
            $nfo = 'no';
            if ($_FILES['nfo']['size'] != 0) {
                $nfofile = $_FILES['nfo'];
                if ($nfofile['name'] == '') {
                    $message = Lang::T("NO_NFO_UPLOADED");
                }
                if (!preg_match('/^(.+)\.nfo$/si', $nfofile['name'], $fmatches)) {
                    $message = Lang::T("UPLOAD_NOT_NFO");
                }
                if ($nfofile['size'] == 0) {
                    $message = Lang::T("NO_NFO_SIZE");
                }
                if ($nfofile['size'] > 65535) {
                    $message = Lang::T("NFO_UPLOAD_SIZE");
                }
                $nfofilename = $nfofile['tmp_name'];
                if (($num = $_FILES['nfo']['error'])) {
                    $message = Lang::T("UPLOAD_ERR[$num]");
                }
                $nfo = 'yes';
            }

            // Check User Inputs
            $descr = Input::get("descr") == "" ? Lang::T("UPLOAD_NO_DESC") : Input::get("descr");
            $vip = Input::get("vip") == "" ? 0 : Input::get("vip");
            $free = Input::get("free") == "" ? 0 : Input::get("free");
            $tube = Input::get('tube');
            $tmdb = Input::get('tmdb');
            $anon = Input::get("anonycheck") == "yes" ? "yes" : "no";
            $langid = (int) Input::get("lang");
            $catid = (int) Input::get("type");

            // Check Cat Selected
            if (!Validate::Id($catid)) {
                $message = Lang::T("UPLOAD_NO_CAT");
            }

            // Shorten Name
            if (!empty(Input::get("name"))) {
                $name = Input::get("name");
            }

            // If Message Show
            if ($message) {
                Redirect::autolink(URLROOT . '/upload', $message);
            }

            // No Message So Continue
            if (!$message) {

                // Torrent / NFO Dir
                $torrent_dir = UPLOADDIR."/torrents";
                $nfo_dir = UPLOADDIR."/nfos";

                // Move Torrent File
                if (!($tupload->move("$torrent_dir/$fname"))) {
                    Redirect::autolink(URLROOT . '/upload', Lang::T("ERROR") . ": " . Lang::T("UPLOAD_COULD_NOT_BE_COPIED") . " $torrent_dir - $fname");
                }

                // Parse Torrent File
                $torInfo = new Parse();
                $tor = $torInfo->torr("$torrent_dir/$fname");

                // File Data
                $announce = $tor['announce'];
                $infohash = $tor['hash'];
                $creationdate = $tor["creation date"];
                $internalname = $tor['name'];
                $torrentsize = $tor['length'];
                $filecount = $tor['ttfilecount'];
                $annlist = $tor["announce-list"];
                $comment = $tor['comment'];
                $filelist = $tor['files'];

                // Check External
                $announce_urls = explode(",", ANNOUNCELIST);
                if (!in_array($announce, $announce_urls)){
                    $external='yes';
                }else{
                    $external='no';
                }

                if (!Config::get('ALLOWEXTERNAL') && $external == 'yes') {
                    $message = Lang::T("UPLOAD_NO_TRACKER_ANNOUNCE");
                }

                // Save announcelist Array
                if ($external === 'yes') {
                    $multi = array_flatten($annlist);
                    $announcelist = serialize($multi);
                }

            }

            // Error So Delete File
            if ($message) {
                @$tupload->remove();
                @unlink("$torrent_dir/$fname");
                Redirect::autolink(URLROOT . '/upload', $message);
            }

            // Check Name
            if ($name == "") {
                $name = $internalname;
            }
            $name = str_replace(".torrent", "", $name);
            $name = str_replace("_", " ", $name);

            // Upload Images
            $allowed_types = ALLOWEDIMAGETYPES;
            $inames = array();
            for ($x = 0; $x < 2; $x++) {
                if (!($_FILES['image' . $x]['name'] == "")) {
                    $y = $x + 1;
                    if ($_FILES['image$x']['size'] > IMAGEMAXFILESIZE) {
                        Redirect::autolink(URLROOT . '/upload', Lang::T("INVAILD_FILE_SIZE_IMAGE"));
                    }
                    $uploaddir = UPLOADDIR . '/images/';
                    $ifile = $_FILES['image' . $x]['tmp_name'];
                    $im = getimagesize($ifile);
                    if (!$im[2]) {
                        Redirect::autolink(URLROOT . '/upload', sprintf(Lang::T("INVALID_IMAGE")));
                    }
                    if (!array_key_exists($im['mime'], $allowed_types)) {
                        Redirect::autolink(URLROOT . '/upload', Lang::T("INVALID_FILETYPE_IMAGE"));
                    }
                    $row = DB::run("SHOW TABLE STATUS LIKE 'torrents'")->fetch();
                    $next_id = $row['Auto_increment'];
                    $ifilename = $next_id . $x . $allowed_types[$im['mime']];
                    $copy = copy($ifile, $uploaddir . $ifilename);
                    if (!$copy) {
                        Redirect::autolink(URLROOT . '/upload', sprintf(Lang::T("IMAGE_UPLOAD_FAILED")));
                    }
                    $inames[] = $ifilename;
                }
            }

            // Check File Count Is INT
            $filecounts = (int) $filecount;

            // Insert Torrent
            try {
                DB::run("INSERT INTO torrents (filename, owner, name, vip, descr, image1, image2, category, tube, added, info_hash, size, numfiles, save_as, announce, external, nfo, torrentlang, anon, last_action, freeleech, tmdb, announcelist)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [$fname, Users::get('id'), $name, $vip, $descr, $inames[0], $inames[1], $catid, $tube, TimeDate::get_date_time(), $infohash, $torrentsize, $filecounts, $fname, $announce, $external, $nfo, $langid, $anon, TimeDate::get_date_time(), $free, $tmdb, $announcelist]);
            } catch (PDOException $e) {
                Torrents::deletetorrent(DB::lastInsertId());
                rename("$torrent_dir/$fname", "$torrent_dir/duplicate.torrent"); // todo
                Redirect::autolink(URLROOT . '/upload', 'Error On Insert, check if added');
            }

            $id = DB::lastInsertId();

            // Rename To INT
            rename("$torrent_dir/$fname", "$torrent_dir/$id.torrent");

            // Add File List
            if (is_array($filelist)) {
                foreach ($filelist as $file) {
                    $dir = '';
                    $size = $file["length"];
                    $count = count($file["path"]);
                    for ($i = 0; $i < $count; $i++) {
                        if (($i + 1) == $count) {
                            $fname = $file["path"][$i] . $dir ;
                        } else {
                            $dir .= "/" . $file["path"][$i];
                        }

                    }
                    DB::insert('files', ['torrent'=>$id, 'path'=>$fname, 'filesize'=>$size]);
                }
            } else {
                DB::insert('files', ['torrent'=>$id, 'path'=>$internalname, 'filesize'=>$torrentsize]);
            }

            // Tags Mod
            $tags = $_POST['tags'];
            if ($tags) {
                foreach ($tags as $tag) {
                    DB::insert('tags', ['name' => $tag, 'type' => 'torrent', 'torrentid'=> $id]);
                }
            }

            // Move NFO
            if ($nfo == 'yes') {
                move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo");
            }

            // Log Upload
            Logs::write(sprintf(Lang::T("TORRENT_UPLOADED"), htmlspecialchars($name), Users::get("username")));
            
            // Shout Torrent
            $msg_shout = "New Torrent: [url=" . URLROOT . "/torrent?id=" . $id . "]" . $name . "[/url] has been uploaded " . ($anon == 'no' ? "by [url=" . URLROOT . "/profile?id=" . Users::get('id') . "]" . Users::get('username') . "[/url]" : "") . "";
            DB::insert('shoutbox', ['userid'=>0, 'date'=>TimeDate::get_date_time(), 'user'=>'System', 'message'=>$msg_shout]);
            
            //Uploaded ok message
            if ($external == 'no') {
                $message = sprintf(Lang::T("TORRENT_UPLOAD_LOCAL"), $name, $id, $id);
            } else {
                $message = sprintf(Lang::T("TORRENT_UPLOAD_EXTERNAL"), $name, $id);
                // Scrape External
                Tscraper::ScrapeId($id, $annlist, $infohash);
            }

            Redirect::autolink(URLROOT . "/torrent?id=$id", $message);

        } else {

            Redirect::autolink(URLROOT, Lang::T("ERROR"));

        }
    }

}