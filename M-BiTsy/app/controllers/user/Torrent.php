<?php

class Torrent
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 1);
    }

    // Validate User
    private function validuser()
    {
        if (Users::get("view_torrents") != "yes" && Config::get('MEMBERSONLY')) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }
    }

    // Torrent Default Page
    public function index()
    {
        // Validate User
        $this->validuser();

        // Check User Input
        $id = (int) $_GET["id"];
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("THATS_NOT_A_VALID_ID"));
        }

        // Get Torrent Data
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.tube, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.tmdb, torrents.imdb, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, torrents.vip, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        
        // Check Torrent
        if (!$row || ($row["banned"] == "yes" && Users::get("edit_torrents") == "no")) {
            Redirect::autolink(URLROOT, Lang::T("TORRENT_NOT_FOUND"));
        }

        $vip = $row["vip"] == "yes" ?? 'no';
        $freeleech = $row["freeleech"] == 1 ? "<font color=green><b>Yes</b></font>" : "<font color=red><b>No</b></font>";

        // TMDB
        if(!empty($row["imdb"])) {
            $total = DB::run("SELECT COUNT(*) FROM tmdb WHERE torrentid = ?", [$id])->fetchColumn();
            $id_tmdb = MDBS::getId($row["imdb"]);
            if($total == 0) {
                MDBS::createImdb($id_tmdb, $id, $row["imdb"]);
            }
        } elseif(!empty($row["tmdb"]) && in_array($row["cat_parent"], SerieCats)) {
            $id_tmdb = MDBS::getId($row["tmdb"]);
            $total = DB::column('tmdb', 'COUNT(*)', ['id_tmdb'=>$id_tmdb,'type'=>'show']);
            if($total == 0) {
                MDBS::createSerie($id_tmdb, $id, $row["tmdb"]);
            }
        } elseif(!empty($row["tmdb"]) && in_array($row["cat_parent"], MovieCats)) {
            $id_tmdb = MDBS::getId($row["tmdb"]);
            $total = DB::column('tmdb', 'COUNT(*)', ['id_tmdb'=>$id_tmdb,'type'=>'movie']);
            if($total == 0) {
            MDBS::createFilm($id_tmdb, $id, $row["tmdb"]);
            }
        }

        // Add Hit
        if ($_GET["hit"]) {
            $pvkey_var = "t_".$id;
            $views = $_SESSION[$pvkey_var] == 1 ? $row["views"] + 0 : $row["views"] + 1;
            $_SESSION[$pvkey_var] = 1;
            DB::update('torrents', ['views'=>$views], ['id'=>$id]);
            Redirect::to(URLROOT . "/torrent?id=$id");
        }

        // Add Bump
        if ($_GET["bump"]) {
            DB::update('torrents', ['added' =>TimeDate::get_date_time()], ['id' => $id]);
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("Bumped Torrent"));
        }

		// Get Tag Data
        $tag = DB::run("SELECT type1, type2, type3 FROM `tagtorrent` wHERE torrentid = ?", [$id])->fetch();
        //var_dump($tag);
		if ($tag) {
            $tags = get_tags($id);
        } else {
            $tags = '---';
        }

        // Display Scraper Button
        $ts = TimeDate::modify('date', $row['last_action'], '+2 day');
        if ($ts > TT_DATE) {
            $scraper = "<br>
            <br><b>" . Lang::T("EXTERNAL_TORRENT") . "</b>
            <font  size='4' color=#ff9900><b>Stats Recently Updated</b></font>";
        } else {
            $scraper = "
            <br><b>" . Lang::T("EXTERNAL_TORRENT") . "</b>
            <form action='" . URLROOT . "/scrape/external?id=" . $id . "' method='post'>
            <button type='submit' class='btn ttbtn center-block' value=''>" . Lang::T("Update Stats") . "</button>
            </form>";
        }

        if (Users::get("id") == $row["owner"] || Users::get("edit_torrents") == "yes") {
            $owned = 1;
        } else {
            $owned = 0;
        }

        $shortname = CutName(htmlspecialchars($row["name"]), 40);
        
        // Calculate local torrent speed test
        if ($row["leechers"] >= 1 && $row["seeders"] >= 1 && $row["external"] != 'yes') {
            $speedQ = DB::run("SELECT (SUM(p.downloaded)) / (UNIX_TIMESTAMP('" . TimeDate::get_date_time() . "') - UNIX_TIMESTAMP(added)) AS totalspeed FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND p.torrent = '$id' GROUP BY t.id ORDER BY added ASC LIMIT 15");
            $a = $speedQ->fetch(PDO::FETCH_ASSOC);
            $totalspeed = mksize($a["totalspeed"]) . "/s";
        } else {
            $totalspeed = Lang::T("NO_ACTIVITY");
        }

        // Get Torrent Data
        $torrent1 = Torrents::getAll($id);

        // Init Data
        $data = [
            'title' => Lang::T("DETAILS_FOR_TORRENT") . " \"" . $row["name"] . "\"",
            'row' => $row,
            'owned' => $owned,
            'shortname' => $shortname,
            'speed' => $totalspeed,
            'shortname' => $shortname,
            'vip' => $vip,
            'freeleech' => $freeleech,
            'selecttor' => $torrent1,
            'tags' => $tags,
            'scraper' => $scraper,
        ];

        // Load View
        View::render('torrent/read', $data, 'user');
    }

    // Edit Torrent Default Page
    public function edit()
    {
        // Check User Input
        $id = (int) $_GET["id"];

        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
        }

        // Check User
        $row = DB::run("SELECT `owner` FROM `torrents` WHERE id=?", [$id])->fetch();
        if (Users::get("edit_torrents") == "no" && Users::get('id') != $row['owner']) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("NO_TORRENT_EDIT_PERMISSION"));
        }

        // Get Torrent Data
        $row = DB::raw('torrents', '*', ['id'=>$id])->fetch();
        if (!$row) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("TORRENT_ID_GONE"));
        }

        // CATEGORY DROPDOWN
        $catdropdown = "<select name=\"type\">\n";
        $cats = Catagories::genrelist();
        foreach ($cats as $catdropdownubrow) {
            $catdropdown .= "<option value=\"" . $catdropdownubrow["id"] . "\"";
            if ($catdropdownubrow["id"] == $row["category"]) {
                $catdropdown .= " selected=\"selected\"";
            }
            $catdropdown .= ">" . htmlspecialchars($catdropdownubrow["parent_cat"]) . ": " . htmlspecialchars($catdropdownubrow["name"]) . "</option>\n";
        }
        $catdropdown .= "</select>\n";

        // TORRENTLANG DROPDOWN
        $langdropdown = "<select name=\"language\"><option value='0'>Unknown</option>\n";
        $lang = Torrentlang::langlist();
        foreach ($lang as $lang) {
            $langdropdown .= "<option value=\"" . $lang["id"] . "\"";
            if ($lang["id"] == $row["torrentlang"]) {
                $langdropdown .= " selected=\"selected\"";
            }
            $langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
        }
        $langdropdown .= "</select>\n";

        $shortname = CutName(htmlspecialchars($row["name"]), 40);

		// Get Tag Data
		$tag = DB::run("SELECT type1, type2, type3 FROM `tagtorrent` wHERE torrentid = ?", [$id])->fetch();
        //var_dump($tag);
		if ($tag) {
            $tags = get_tags($id);
        } else {
            $tags = 'No Tags Added';
        }
        
        // Get Torrent Data
        $torrent1 = Torrents::getAll($id);
        
        // Init Data
        $data = [
            'title' => Lang::T("EDIT_TORRENT") . " \"$shortname\"",
            'row' => $row,
            'catdrop' => $catdropdown,
            'shortname' => $shortname,
            'langdrop' => $langdropdown,
            'id' => $id,
            'tags' => $tags,
            'selecttor' => $torrent1,
        ];

        // Load View
        View::render('torrent/edit', $data, 'user');
    }

    // Edit Torrent Form Submit
    public function submit()
    {
        // Check User Input
        $id = (int) $_GET["id"];
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
        }

        // Check User
        $row = DB::raw('torrents', 'owner', ['id'=>$id])->fetch();
        if (Users::get("edit_torrents") == "no" && Users::get('id') != $row['owner']) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_EDIT_PERMISSION"));
        }

        // Get Torrent Data
        $row = DB::raw('torrents', '*', ['id'=>$id])->fetch();
        if (!$row) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("TORRENT_ID_GONE"));
        }

        $nfo_dir = UPLOADDIR."/nfos";
        $updateset = array();

        // If Input Update
        if (Input::exist()) {
            // NFO
            $nfoaction = $_POST['nfoaction'];
            if ($nfoaction == "update") {
                $nfofile = $_FILES['nfofile'];
                if (!$nfofile) {
                    die("No data " . var_dump($_FILES));
                }
                if ($nfofile['size'] > 65535) {
                    Redirect::autolink(URLROOT . "/torrent?id=$id", "NFO is too big! Max 65,535 bytes.");
                }
                $nfofilename = $nfofile['tmp_name'];
                if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0) {
                    @move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo");
                    $updateset['nfo'] = 'yes';
                } //success
            } elseif ($nfoaction == "delete") {
                unlink(UPLOADDIR."/nfos/$id");
                $updateset['nfo'] = 'no';
            }

            if (!empty($_POST["name"])) {
                $updateset['name'] = $_POST["name"];
            }

            $updateset['tmdb'] = $_POST["tmdb"];
            $updateset['imdb'] = $_POST["imdb"];
            $updateset['descr'] = $_POST["descr"];
            $updateset['category'] = (int) $_POST["type"];
            $updateset['tube'] = $_POST['tube'] ?? '';
            $updateset['vip'] = $_POST["vip"] ? "yes" : "no";
            $updateset['anon'] = $_POST["anon"] ? "yes" : "no";

            if (Users::get("class") >= _MODERATOR) {
                $updateset['sticky'] = $_POST["sticky"] == 'yes' ? 'yes' : 'no';
            }

            if (Users::get("edit_torrents") == "yes") {
                $updateset['freeleech'] = $_POST["freeleech"] ? 1 : 0;
            }

            if (Users::get("edit_torrents") == "yes") {
                if ($_POST["banned"]) {
                    $updateset['banned'] = 'yes';
                    $_POST["visible"] = 'no';
                } else {
                    $updateset['banned'] = 'no';
                }
            }

            $updateset['visible'] = $_POST["visible"] ? "yes" : "no";
            
            $tags = $_POST['tags'];
            if ($tags) {
                DB::insert('tagtorrent', ['torrentid'=> $id, 'type1' => $tags[0], 'type2' => $tags[1], 'type3' => $tags[2]]);
            }

            // Images
            $img1action = $_POST['img1action'];
            if ($img1action == "update") {
                $updateset['image1'] = uploadimage(0, $row["image1"], $id);
            }

            if ($img1action == "delete") {
                if ($row['image1']) {
                    unlink(UPLOADDIR . "/images/$row[image1]");
                    $updateset['image1'] = '';
                }
            }

            $img2action = $_POST['img2action'];
            if ($img2action == "update") {
                $updateset['image2'] = uploadimage(1, $row["image2"], $id);
            }

            if ($img2action == "delete") {
                if ($row['image2']) {
                    unlink(UPLOADDIR . "/images/$row[image2]");
                    $updateset['image2'] = '';
                }
            }

            // Update
            DB::update("torrents", $updateset, ['id' => $id]);
            Logs::write("Torrent $id (" . htmlspecialchars($_POST["name"]) . ") was edited by ".Users::get('username')."");
            Redirect::to(URLROOT . "/torrent?id=$id");

        } else {
            Redirect::autolink(URLROOT . "/torrent?id=$id", 'Error');
        }
    }

    // Delete Torrent Default Page
    public function delete()
    {
        // Check User Input
        $id = (int) $_GET["id"];
        
        // Check Corrert Input
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("INVALID_ID"));
        }

        // Check User
        $row = DB::raw('torrents', 'owner', ['id'=>$id])->fetch();
        if (Users::get("delete_torrents") == "no" && Users::get('id') != $row['owner']) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("NO_TORRENT_DELETE_PERMISSION"));
        }
        $owner = $row['owner'];
        
        // Get Torrent Data
        $row = DB::raw('torrents', '*', ['id'=>$id])->fetch();
        if (!$row) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("TORRENT_ID_GONE"));
        }

        $torrname = $row['name'];
        $shortname = CutName(htmlspecialchars($row["name"]), 45);
        
        // Init Data
        $data = [
            'title' => Lang::T("DELETE_TORRENT") . " \"$shortname\"",
            'owner' => $owner,
            'id' => $id,
            'name' => $torrname,
        ];

        // Load View
        View::render('torrent/delete', $data, 'user');
    }

    // Delete Torrent Form Submit
    public function deleteok()
    {
        // Check User Input
        $torrentid = (int) $_GET["id"];
        $delreason = $_POST["delreason"];
        $torrentname = $_POST["torrentname"];
        
        // Check Corrert Input
        if (Users::get("delete_torrents") == "no") {
            Redirect::autolink(URLROOT . "/torrent?id=$torrentid", Lang::T("NO_TORRENT_DELETE_PERMISSION"));
        }
        if (!Validate::Id($torrentid)) {
            Redirect::autolink(URLROOT . "/torrent/delete?id=$torrentid", Lang::T("INVALID_TORRENT_ID"));
        }
        if (!$delreason) {
            Redirect::autolink(URLROOT . "/torrent/delete?id=$torrentid", Lang::T("MISSING_FORM_DATA"));
        }

        // Delete Torrent
        $owner = DB::raw('torrents', 'name, owner', ['id'=>$torrentid])->fetch();
        Torrents::deletetorrent($torrentid);
        Logs::write(Users::get('username') . " has deleted torrent: ID:$torrentid - " . htmlspecialchars($torrentname) . " - Reason: " . htmlspecialchars($delreason));
        unlink(UPLOADDIR."/torrents/$torrentid.torrent");

        if (Users::get('id') === $owner['owner']) {
            $msg_shout = "Your torrent " . $torrentname . " has been deleted by " . Users::get('username') . "/nReason: ".$_POST["delreason"]."";
            DB::insert('messages', ['sender'=>0,'receiver'=>$owner['owner'],'added'=>TimeDate::get_date_time(), 'subject'=>'Torrent Deleted', 'msg'=>$msg_shout, 'unread'=>'yes', 'location'=>'in']);
        }

        Redirect::autolink(URLROOT . "/peer/uploaded?id=".Users::get('id')."", htmlspecialchars($torrentname) . " " . Lang::T("HAS_BEEN_DEL_DB"));
    }

    // Torrent Files Default Page
    public function torrentfilelist()
    {
        // Check User
        $id = (int) $_GET["id"];

        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("THATS_NOT_A_VALID_ID"));
        }

        // Validate User
        $this->validuser();

        // Get Torrent Data
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, torrents.announcelist, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $fres = DB::raw('files', '*', ['torrent'=>$id], 'ORDER BY `path` ASC');
        
        $shortname = CutName(htmlspecialchars($row["name"]), 45);

        // Init Data
        $data = [
            'title' =>  Lang::T("TORRENT_DETAILS_FOR") . " \"" . $shortname . "\"",
            'row' => $row,
            'shortname' => $shortname,
            'id' => $id,
            'name' => $row["name"],
            'size' => $row["size"],
            'fres' => $fres,
        ];

        // Load View
        View::render('torrent/filelist', $data, 'user');
    }

    // Torrent Files Default Page
    public function torrenttrackerlist()
    {
        // Check User
        $id = (int) $_GET["id"];

        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("THATS_NOT_A_VALID_ID"));
        }

        // Validate User
        $this->validuser();

        // Get Torrent Data
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, torrents.announcelist, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        
        // Init Data
        $data = [
            'title' => Lang::T("Tracker List"),
            'id' => $id,
            'res' => $res,
        ];

        // Load View
        View::render('torrent/trackerlist', $data, 'user');
    }

}