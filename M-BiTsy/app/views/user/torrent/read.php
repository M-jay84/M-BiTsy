<?php
foreach ($data['selecttor'] as $torr) :
    torrentmenu($torr['id'], $torr['external']); ?>

    <div class="row">
       
    <div class="col-12 col-md-3"><br>
        <i class="fa fa-arrow-circle-up" style="color:green" aria-hidden="true">&nbsp;<?php echo Lang::T("SEEDS"); ?>: <?php echo number_format($torr["seeders"]); ?></i>&nbsp;
        <i class="fa fa-arrow-circle-down" style="color:red" aria-hidden="true">&nbsp;<?php echo Lang::T("LEECHERS"); ?>: <?php echo number_format($torr["leechers"]); ?></i>&nbsp;
        <img src='<?php echo URLROOT; ?>/assets/images/health/health_<?php echo health($torr['leechers'], $torr['seeders']); ?>.gif' alt='' /><br>
        <?php echo Lang::T("Added"); ?>:&nbsp;<?php echo date("d-m-Y", TimeDate::utc_to_tz_time($torr["added"])); ?>&nbsp;<?php
        if ($torr["anon"] == "yes" && !$torr['owned']) { ?>
            <?php echo Lang::T("by"); ?>:&nbsp; Anonymous<br><br><?php
        } elseif ($torr["username"]) { ?>
            <?php echo Lang::T("by"); ?>:&nbsp;<a href='profile?id=<?php echo $torr["owner"]; ?>'><?php echo Users::coloredname($torr["owner"]); ?></a><br><br><?php
        } else { ?>
            <?php echo Lang::T("by"); ?>:&nbsp; Unknown<br><br><?php
        }
        if (!Config::get('FORCETHANKS')) {
            print("<a href=\"" . URLROOT . "/download?id=$torr[id]&amp;name=" . rawurlencode($torr["filename"]) . "\"><i class='fa fa-download fa-2x' style='color:green' title='Download'></i></a>&nbsp;");
            print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . $torr["announce"] . "\"><i class='fa fa-magnet fa-2x' style='color:green' title='Download'></i></a>&nbsp;<br>");
        } else {
            print("<a href=\"" . URLROOT . "/download?id=$torr[id]&amp;name=" . rawurlencode($torr["filename"]) . "\"><i class='fa fa-download fa-2x' style='color:green' title='Download'></i></a>&nbsp;");
            $like = DB::select('thanks', 'user', ['thanked' => $torr['id'], 'type' => 'torrent', 'user' => Users::get('id')]);
            if ($like) {
                // magnet
                if ($torr["external"] == 'yes') {
                    if (Users::get("can_download") == "yes") {
                        // magnet
                        print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . URLROOT . "/announce.php?passkey=" . Users::get("passkey") . "\"><i class='fa fa-magnet fa-2x' style='color:green' title='Download'></i></a>&nbsp;<br>");
                    } else {
                        echo '<br>';
                    }
                } else {
                    if (Users::get("can_download") == "yes") {
                        // magnet button
                        print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><i class='fa fa-magnet fa-2x' style='color:green' title='Download'></i></a>&nbsp;<br>");
                    } else {
                        echo '<br>';
                    }
                }
            } else {
                if (Users::get("id") != $torr["owner"]) {
                    print("<a href='" . URLROOT . "/like/thanks?id=$torr[id]&type=torrent'><button  class='btn btn-sm ttbtn'>Thanks</button></a><br>");
                } else {
                    if ($torr["external"] == 'yes') {
                        // magnet
                        print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . URLROOT . "/announce.php?passkey=" . Users::get("passkey") . "\"><i class='fa fa-magnet fa-2x' style='color:green' title='Download'></i></a>&nbsp;<br>");
                    } else {
                        print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><i class='fa fa-magnet fa-2x' style='color:green' title='Download'></i></a>&nbsp;<br>");
                    }
                }
            }
        } ?>

        <?php echo Lang::T("VIEWS"); ?>:&nbsp;<?php echo number_format($torr["views"]); ?>&nbsp;
        <?php echo Lang::T("HITS"); ?>:&nbsp;<?php echo number_format($torr["hits"]); ?>&nbsp; 
        <?php echo Lang::T("COMPLETED"); ?>: <?php echo number_format($torr["times_completed"]); ?>&nbsp; 
        <?php
        if ($torr["external"] != "yes" && $torr["times_completed"] > 0) { ?>
            <a href='<?php echo URLROOT; ?>/complete?id=<?php echo $torr['id']; ?>'><?php echo Lang::T("WHOS_COMPLETED"); ?></a>]<?php  
        } ?><br><br>

        <b><?php echo Lang::T("CATEGORY"); ?>:</b>&nbsp;<?php echo $torr["cat_parent"]; ?> / <?php echo $torr["cat_name"]; ?><br>
		<b><?php echo Lang::T("Genre"); ?>: </b><?php echo $data["tags"]; ?><br> <?php
		if (empty($torr["lang_name"])) {
             $torr["lang_name"] = "Unknown/NA";
        } ?>
        <b><?php echo Lang::T("LANG"); ?>:</b>&nbsp;<?php echo $torr["lang_name"]; ?><br> <?php
        if (isset($torr["lang_image"]) && $torr["lang_image"] != "") {
           print("&nbsp;<img border=\"0\" src=\"" . URLROOT . "/assets/images/languages/" . $torr["lang_image"] . "\" alt=\"" . $torr["lang_name"] . "\" /><br>");
        } ?>
        <b><?php echo Lang::T("TOTAL_SIZE"); ?>:</b>&nbsp;<?php echo mksize($torr["size"]); ?><br><br>
        <?php
		// LIKE MOD
        if (Config::get('ALLOWLIKES')) {
            $data1 = DB::raw('likes', 'user', ['liked' => $torr['id'], 'type' => 'torrent', 'user' => Users::get('id'), 'reaction' => 'like']);
            $likes = $data1->fetch(PDO::FETCH_ASSOC);
            if ($likes) { ?>
                <b>Likes:</b>&nbsp;<a href='<?php echo URLROOT; ?>/like?id=<?php echo $torr['id']; ?>&type=unliketorrent'><i class='fa fa-thumbs-up' title='Like'></i></a><br><?php
            } else { ?>
                <b>Likes:</b>&nbsp;<a href='<?php echo URLROOT; ?>/like?id=<?php echo $torr['id']; ?>&type=liketorrent'><i class='fa fa-thumbs-up' title='Like'></i></a><br><?php
            }
        }
        if (Config::get('ALLOWLIKES')) {
            $data3 = DB::run("SELECT user FROM `likes` WHERE liked=? AND type=?", [$torr['id'], 'torrent'])->fetchAll();
            foreach ($data3 as $stmt) :
                print("<a href='" . URLROOT . "/profile?id=$stmt[user]'>" . Users::coloredname($stmt['user']) . "</a>&nbsp;");
            endforeach;
            echo "<br />";
        }
        if (Config::get('FORCETHANKS')) {
            $data3 = DB::run("SELECT * FROM `users` AS u LEFT JOIN `thanks` AS l ON(u.id = l.user) WHERE thanked=? AND type=?", [$torr['id'], 'torrent']);
            print('<b>Thanked by</b>&nbsp;');
            foreach ($data3 as $stmt) :
                print("<a href='" . URLROOT . "/profile?id=$stmt[id]'>" . Users::coloredname($stmt['id']) . "</a>&nbsp;");
            endforeach;
            echo "<br />";
        }
        if ($torr["external"] != 'yes' && $torr["freeleech"] == '1') {
            print("<b>" . Lang::T("FREE_LEECH") . ": </b><font color='#ff0000'>" . Lang::T("FREE_LEECH_MSG") . "</font><br />");
        }
        if ($torr["external"] != 'yes' && $torr["vip"] == 'yes') {
            print("<b>Torrent VIP: </b><font color='orange'>Torrent reserved for VIP</font><br>");
        }
        
        echo Ratings::ratingtor($torr['id'], $torr['rating']);
		
        // Scrape External Torrents
        if ($torr["external"] == 'yes') {
            echo $data['scraper'];
        }
        
        echo "<br><br>".Lang::T("Last Checked"); ?>: <?php echo date("d-m-Y", TimeDate::utc_to_tz_time($torr["last_action"])); ?><br><br>
        
    </div>

    <div class="col-12 col-md-6">
        <p class='text-center'><legend><b><?php echo $torr["name"]; ?></b></legend></p><?php
        if ($torr["imdb"]) {
            $total = DB::run("SELECT COUNT(*) FROM tmdb WHERE torrentid = ?", [$torr['id']])->fetchColumn();
            $id_tmdb = MDBS::getId($row["imdb"]);
            if($total > 0) {
                $_data = MDBS::getImdb($id_tmdb, $torr['id']); ?>
                
                <?php echo $_data["plot"] ?><br><br>
                <b> Actors : </b><?php echo $_data["actors"] ?> <br>
                <b> Release : </b><?php echo date("d-m-Y", TimeDate::utc_to_tz_time($_data["release"])); ?> <br>
                <b> Duration : </b><?php echo $_data["duration"] ?> <br>
                <b> Genre : </b><?php echo $_data["genre"] ?><br>
                <br> <?php
            }
        } elseif (!empty($torr["tmdb"]) && in_array($torr["cat_parent"], SerieCats)) {
            $id_tmdb = MDBS::getId($torr["tmdb"]);
            $total = DB::column('tmdb', ' count(*)', ['id_tmdb' => $id_tmdb, 'type' => 'show']);
            if ($total > 0) {
                $_data = MDBS::getSerie($id_tmdb); ?>
                <?php echo $_data["plot"] ?><br><br>
                <div class='row'>  <?php
                   $casting = explode('&', $_data["actor"]);
                   for ($i = 0; $i <= 3; $i++) {
                     list($pseudo, $role, $image) = explode("*", $casting[$i]);;
                     print("<div class='col-3'>".$pseudo . "<br>");
                     print(" <img class='avatar3' src='" . $image . "' /><br>");
                     print("".$role . "<br></div>");
                   } ?>
                </div><br>
                <b> Seasons : </b><?php echo $_data["season"] ?> <br>
                <b> Episodes : </b><?php echo $_data["episode"] ?> <br>
                <b> Date : </b><?php echo $_data["date"] ?> <br>
                <b> Creator : </b><?php echo $_data["creator"] ?><br>
                <b> Genre : </b><?php echo $_data["genre"] ?><br>
                <br> <?php
            }
        } elseif (!empty($torr["tmdb"]) && in_array($torr["cat_parent"], MovieCats)) {
            $id_tmdb = MDBS::getId($torr["tmdb"]);
            $total = DB::column('tmdb', ' count(*)', ['id_tmdb' => $id_tmdb, 'type' => 'movie']);
            if ($total > 0) {
                $_data = MDBS::getFilm($id_tmdb); ?>
                <?php echo $_data["plot"] ?> <br><br>
               <div class='row'>  <?php
                   $casting = explode('&', $_data["actors"]);
                   for ($i = 0; $i <= 3; $i++) {
                     list($pseudo, $role, $image) = explode("*", $casting[$i]);;
                     print("<div class='col-3'>".$pseudo . "<br>");
                     print(" <img class='avatar3' src='" . $image . "' /><br>");
                     print("".$role . "<br></div>");
                   } ?>
                </div><br>
                <b> Duration : </b><?php echo $_data["duration"] ?> <br>
                <b> Genre : </b><?php echo $_data["genre"] ?> <br><br> <?php
                print("<embed src='https://www.youtube.com/v/" . str_replace("watch?v=", "v/", htmlspecialchars($_data["trailer"])) . "' type=\"application/x-shockwave-flash\"  height=\"410\" width='100%'></embed>");
            }
        } else { ?>
            <b><?php echo Lang::T("DESCRIPTION"); ?>:</b><br><?php echo format_comment($torr['descr']); ?><br> <?php
        }
        if (!empty($torr["tube"])) {
            print("<embed src='" . str_replace("watch?v=", "v/", htmlspecialchars($torr["tube"])) . "' type=\"application/x-shockwave-flash\"  height=\"410\" width='100%'></embed>");
        } ?>
    </div>

    <div class="col-12 col-md-3"> <?php
        if ($torr["imdb"] != "") {
            //$url = UPLOADDIR.'/tmdb/' . $_data["type"].'/' . $_data["poster"];
            echo '<img src="'.$_data["poster"].'" class="scaleA">';
        } elseif ($torr["tmdb"] != "") {
            $url = UPLOADDIR.'/tmdb/' . $_data["type"].'/' . $_data["poster"]; ?>
            <img src='<?php echo data_uri($url, $_data["poster"]) ?>' height='450' width='100%' border='0' alt='' /><?php
        } else {
            if ($torr["image1"] != "") {
                $img1 = "<img src='" . data_uri(UPLOADDIR . "/images/" . $torr["image1"], $torr['image1']) . "' height='250' width='100%' border='0' alt='' />";
            }
            print("" . $img1 . ""); ?>  <?php
            if ($torr["image2"] != "") {
                $img2 = "<img src='" . data_uri(UPLOADDIR . "/images/" . $torr["image2"], $torr['image2']) . "' height='250' width='100%' border='0' alt='' />";
            }
            print("" . $img2 . "");
        }
        
        Bookmarks::select($torr['id']);  // <i class="fa fa-bookmark" aria-hidden="true"></i>
        ?>
        <a href="<?php echo URLROOT; ?>/report/torrent?torrent=<?php echo $torr['id']; ?>"><i class="fa fa-flag fa-2x" title="<?php echo Lang::T("REPORT") ?>"></i></a>&nbsp;<?php

        if ($torr["seeders"] <= 1) { ?>
            <a href='<?php echo URLROOT ?>/search/reseed?id=<?php echo $torr['id']; ?>'><i class="fa fa-refresh fa-2x" title="<?php echo Lang::T("Reseed"); ?>"></i></a>&nbsp;<?php
        }
        if (Users::get("edit_torrents") == "yes") { ?>
            <a href='<?php echo  URLROOT ?>/torrent/edit?id=<?php echo $torr['id'] ?>'><i class="fa fa-pencil fa-2x" title="<?php echo Lang::T("EDIT_TORRENT") ?>"></i></a>&nbsp;
            <a href="<?php echo URLROOT; ?>/torrent?id=<?php echo $torr['id'] ?>&bump=1"><i class="fa fa-retweet fa-2x" title="<?php echo Lang::T("Bump") ?>"></i></a>&nbsp;
            <a href="<?php echo URLROOT; ?>/snatch?tid=<?php echo $torr['id']; ?>"><i class="fa fa-history fa-2x" title='<?php echo Lang::T("SNATCHLIST") ?>'></i></a>&nbsp;<?php
        }
        if (Users::get("delete_torrents") == "yes") { ?>
            <a href="<?php echo URLROOT; ?>/torrent/delete?id=<?php echo $torr['id']; ?>"><i class="fa fa-trash fa-2x" title="<?php echo Lang::T("Delete") ?>"></i></a>&nbsp;<?php
        } ?>
    </div>
    </div> <?php


//DISPLAY NFO BLOCK
if ($torr["nfo"] == "yes") {
    $nfofilelocation = UPLOADDIR . "/nfos/$torr[id].nfo";
    $filegetcontents = file_get_contents($nfofilelocation);
    $nfo = $filegetcontents;
    if ($nfo) {
        $nfo = my_nfo_translate($nfo);
        echo "<br /><br /><b>NFO:</b><br />";
        print("<div><textarea class='nfo' style='width:98%;height:100%;' rows='20' cols='20' readonly='readonly'>" . stripslashes($nfo) . "</textarea></div>");
    } else {
        print(Lang::T("ERROR") . " reading .nfo file!");
    }
}

endforeach;

// Similar Torrents mod
$query = "SELECT torrents.id, torrents.anon, torrents.descr, torrents.announce, torrents.category, torrents.sticky,  torrents.vip,  torrents.tube,  torrents.tmdb, torrents.imdb, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, torrents.image1, torrents.image2,
categories.name AS cat_name, categories.image AS cat_pic, categories.parent_cat AS cat_parent,
users.username, users.privacy,
IF(torrents.numratings < 1, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating
FROM torrents
LEFT JOIN categories ON category = categories.id
LEFT JOIN users ON torrents.owner = users.id
WHERE visible = 'yes' AND banned = 'no' AND torrents.category = $torr[category] 
ORDER BY sticky, added DESC, id DESC LIMIT 15";
$res = DB::run($query);

if ($res->rowCount() > 0) { 
    echo '<br><center><b>Similar Torrents</b></center>';
    torrenttable($res);
} else {
    echo '<br><center><b>No Similar Torrents Found</b></center>';
}