<?php
class Torrent
{
    public function __construct()
    {
        Auth::user(0, 1);
    }

    public function index()
    {
        //check permissions
        if (Users::get("view_torrents") != "yes" && Config::get('MEMBERSONLY')) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }

        $id = (int) $_GET["id"];
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("THATS_NOT_A_VALID_ID"));
        }

        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.tube, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.tmdb, torrents.tmdb, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, torrents.vip, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        if (!$row || ($row["banned"] == "yes" && Users::get("edit_torrents") == "no")) {
            Redirect::autolink(URLROOT, Lang::T("TORRENT_NOT_FOUND"));
        }

        // TMDB
        if(!empty($row["tmdb"]) && in_array($row["cat_parent"], SerieCats)) {
            $id_tmdb = TMDBS::getId($row["tmdb"]);
            $total = DB::column('tmdb', 'COUNT(*)', ['id_tmdb'=>$id_tmdb,'type'=>'show']);
            if($total == 0) {
                TMDBS::createSerie($id_tmdb, $id, $row["tmdb"]);
            }
        } elseif(!empty($row["tmdb"]) && in_array($row["cat_parent"], MovieCats)) {
            $id_tmdb = TMDBS::getId($row["tmdb"]);
            $total = DB::column('tmdb', 'COUNT(*)', ['id_tmdb'=>$id_tmdb,'type'=>'movie']);
            if($total == 0) {
                TMDBS::createFilm($id_tmdb, $id, $row["tmdb"]);
            }
        }

        // vip / freeleech
        $vip = $row["vip"] == "yes" ?? 'no';
        $freeleech = $row["freeleech"] == 1 ? "<font color=green><b>Yes</b></font>" : "<font color=red><b>No</b></font>";

        //torrent is availiable so do some stuff
        if ($_GET["hit"]) {
            $pvkey_var = "t_".$id;
            $views = $_SESSION[$pvkey_var] == 1 ? $row["views"] + 0 : $row["views"] + 1;
            $_SESSION[$pvkey_var] = 1;
            DB::update('torrents', ['views'=>$views], ['id'=>$id]);
            Redirect::to(URLROOT . "/torrent?id=$id");
        }

        if ($_GET["bump"]) {
            DB::update('torrents', ['added' =>TimeDate::get_date_time()], ['id' => $id]);
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("Bumped Torrent"));
        }

		$stmt = Tags::getByTorrentId($id);
		if ($stmt) {
            $tags = '';
            foreach ($stmt as $tag) {
                $tags .= "<a href=".URLROOT."/search/tags?name=$tag[name]>$tag[name]</a>&nbsp;";
            }
        } else {
            $tags = '---';
        }

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

        $torrent1 = Torrents::getAll($id);

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
        View::render('torrent/read', $data, 'user');
    }

    public function edit()
    {
        $id = (int) $_GET["id"];

        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
        }

        $row = DB::run("SELECT `owner` FROM `torrents` WHERE id=?", [$id])->fetch();
        if (Users::get("edit_torrents") == "no" && Users::get('id') != $row['owner']) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("NO_TORRENT_EDIT_PERMISSION"));
        }

        //GET DATA FROM DB
        $row = DB::raw('torrents', '*', ['id'=>$id])->fetch();
        if (!$row) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("TORRENT_ID_GONE"));
        }

        //UPDATE CATEGORY DROPDOWN
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

        //UPDATE TORRENTLANG DROPDOWN
        $langdropdown = "<select name=\"language\"><option value='0'>Unknown</option>\n";
        $lang = Lang::langlist();
        foreach ($lang as $lang) {
            $langdropdown .= "<option value=\"" . $lang["id"] . "\"";
            if ($lang["id"] == $row["torrentlang"]) {
                $langdropdown .= " selected=\"selected\"";
            }
            $langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
        }
        $langdropdown .= "</select>\n";

        $shortname = CutName(htmlspecialchars($row["name"]), 40);

		$stmt = Tags::getByTorrentId($id);
		if ($stmt) {
            $tags = '';
            foreach ($stmt as $tag) {
                $tags .= "<a href=".URLROOT."/search/tags?name=$tag[name]>$tag[name]</a>&nbsp;";
            }
        } else {
            $tags = 'No Tags Added';
        }

        if ($_GET["edited"]) { // todo
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("TORRENT_EDITED_OK"));
        }

        $torrent1 = Torrents::getAll($id);
        
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
        View::render('torrent/edit', $data, 'user');
    }

    public function submit()
    {
        $id = (int) $_GET["id"];
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
        }

        $row = DB::raw('torrents', 'owner', ['id'=>$id])->fetch();
        if (Users::get("edit_torrents") == "no" && Users::get('id') != $row['owner']) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_EDIT_PERMISSION"));
        }

        //GET DATA FROM DB
        $row = DB::raw('torrents', '*', ['id'=>$id])->fetch();
        if (!$row) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("TORRENT_ID_GONE"));
        }

        $nfo_dir = UPLOADDIR."/nfos";

        //DO THE SAVE TO DB HERE
        if (Input::exist()) {
            $updateset = array();
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
            // TMDB
            $updateset['tmdb'] = $_POST["tmdb"];

            $updateset['descr'] = $_POST["descr"];
            $updateset['category'] = (int) $_POST["type"];
            if (Users::get("class") >= 5) { // lowest class to make torrent sticky.
                if ($_POST["sticky"] == "yes") {
                    $updateset['sticky'] = 'yes';
                } else {
                    $updateset['sticky'] = 'no';
                }
            }
            $updateset['torrentlang'] = (int) $_POST["language"];
            if (Users::get("edit_torrents") == "yes") {
                if ($_POST["banned"]) {
                    $updateset['banned'] = 'yes';
                    $_POST["visible"] = 'no';
                } else {
                    $updateset['banned'] = 'no';
                }
            }

            $tags = $_POST['tags'];
            if ($tags) {
                foreach ($tags as $tag) {
                    DB::insert('tags', ['name' => $tag, 'type' => 'torrent', 'torrentid'=> $id]);
                }
            }

            $updateset['visible'] = $_POST["visible"] ? "yes" : "no";
            
            // youtube
            $updateset['tube'] = $_POST['tube'] ?? '';

            if (Users::get("edit_torrents") == "yes") {
                $updateset['freeleech'] = $_POST["freeleech"] ? 1 : 0;
            }

            $updateset['vip'] = $_POST["vip"] ? "yes" : "no";
            $updateset['anon'] = $_POST["anon"] ? "yes" : "no";

            //update images
            $img1action = $_POST['img1action'];
            if ($img1action == "update") {
                $updateset['image1'] = uploadimage(0, $row["image1"], $id);
            }

            if ($img1action == "delete") {
                if ($row['image1']) {
                    $del = unlink(UPLOADDIR . "/images/$row[image1]");
                    $updateset['image1'] = '';
                }
            }

            $img2action = $_POST['img2action'];
            if ($img2action == "update") {
                $updateset['image2'] = uploadimage(1, $row["image2"], $id);
            }

            if ($img2action == "delete") {
                if ($row['image2']) {
                    $del = unlink(UPLOADDIR . "/images/$row[image2]");
                    $updateset['image2'] = '';
                }
            }
            DB::update("torrents", $updateset, ['id' => $id]);
            Logs::write("Torrent $id (" . htmlspecialchars($_POST["name"]) . ") was edited by ".Users::get('username')."");
            Redirect::to(URLROOT . "/torrent?id=$id");
        } else {
            Redirect::autolink(URLROOT . "/torrent?id=$id", 'Error');
        }
    }

    public function delete()
    {
        $id = (int) $_GET["id"];
        
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("INVALID_ID"));
        }
        $row = DB::raw('torrents', 'owner', ['id'=>$id])->fetch();
        if (Users::get("delete_torrents") == "no" && Users::get('id') != $row['owner']) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("NO_TORRENT_DELETE_PERMISSION"));
        }
        $owner = $row['owner'];
        
        $row = DB::raw('torrents', '*', ['id'=>$id])->fetch();
        if (!$row) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("TORRENT_ID_GONE"));
        }

        $torrname = $row['name'];
        $shortname = CutName(htmlspecialchars($row["name"]), 45);
        
        $data = [
            'title' => Lang::T("DELETE_TORRENT") . " \"$shortname\"",
            'owner' => $owner,
            'id' => $id,
            'name' => $torrname,
        ];
        View::render('torrent/delete', $data, 'user');
    }

    public function deleteok()
    {
        $torrentid = (int) $_GET["id"];
        $delreason = $_POST["delreason"];
        $torrentname = $_POST["torrentname"];
        
        if (Users::get("delete_torrents") == "no") {
            Redirect::autolink(URLROOT . "/torrent?id=$torrentid", Lang::T("NO_TORRENT_DELETE_PERMISSION"));
        }
        if (!Validate::Id($torrentid)) {
            Redirect::autolink(URLROOT . "/torrent/delete?id=$torrentid", Lang::T("INVALID_TORRENT_ID"));
        }
        if (!$delreason) {
            Redirect::autolink(URLROOT . "/torrent/delete?id=$torrentid", Lang::T("MISSING_FORM_DATA"));
        }

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

    public function torrentfilelist()
    {
        $id = (int) $_GET["id"];

        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("THATS_NOT_A_VALID_ID"));
        }

        //check permissions
        if (Users::get("view_torrents") == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }

        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, torrents.announcelist, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $fres = DB::raw('files', '*', ['torrent'=>$id], 'ORDER BY `path` ASC');
        
        $shortname = CutName(htmlspecialchars($row["name"]), 45);

        $data = [
            'title' =>  Lang::T("TORRENT_DETAILS_FOR") . " \"" . $shortname . "\"",
            'row' => $row,
            'shortname' => $shortname,
            'id' => $id,
            'name' => $row["name"],
            'size' => $row["size"],
            'fres' => $fres,
        ];
        View::render('torrent/filelist', $data, 'user');
    }

    public function torrenttrackerlist()
    {
        $id = (int) $_GET["id"];

        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("THATS_NOT_A_VALID_ID"));
        }

        if (Users::get("view_torrents") == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }

        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, torrents.announcelist, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        
        $data = [
            'title' => Lang::T("Tracker List"),
            'id' => $id,
            'res' => $res,
        ];
        View::render('torrent/trackerlist', $data, 'user');
    }

    public function reseed()
    {
        if (Users::get("view_torrents") == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }

        $id = (int) $_GET["id"];

        if (isset($_COOKIE["reseed$id"])) {
            Redirect::autolink(URLROOT, Lang::T("RESEED_ALREADY_ASK"));
        }

        $row = DB::select('torrents', 'owner,banned,external', ['id'=>$id]);
        if (!$row || $row["banned"] == "yes" || $row["external"] == "yes") {
            Redirect::autolink(URLROOT, Lang::T("TORRENT_NOT_FOUND"));
        }

        $res2 = DB::run("SELECT users.id FROM completed LEFT JOIN users ON completed.userid = users.id WHERE users.enabled = 'yes' AND users.status = 'confirmed' AND completed.torrentid = $id");
        $message = sprintf(Lang::T('RESEED_MESSAGE'), Users::get('username'), URLROOT, $id);
        while ($row2 = $res2->fetch(PDO::FETCH_ASSOC)) {
            DB::insert('messages', [ 'subject'=>Lang::T("RESEED_MES_SUBJECT"),'sender'=>Users::get('id'),'receiver'=>$row2['id'],'added'=>TimeDate::get_date_time(), 'msg'=>$message]);
        }
        if ($row["owner"] && $row["owner"] != Users::get("id")) {
            DB::insert('messages', [ 'subject'=>'Torrent Reseed Request','sender'=>Users::get('id'),'receiver'=>$row['owner'],'added'=>TimeDate::get_date_time(), 'msg'=>$message]);
        }
        
        setcookie("reseed$id", $id, time() + 86400, '/');
        Redirect::autolink(URLROOT, Lang::T("RESEED_SENT"));
    }

}