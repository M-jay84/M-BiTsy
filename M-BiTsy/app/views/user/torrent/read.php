<?php
foreach ($data['selecttor'] as $torr) :
    torrentmenu($torr['id'], $torr['external']); ?>
        <p class="text-end"> <?php
        if (Users::get("edit_torrents") == "yes") { ?>
            <a href='<?php echo  URLROOT ?>/torrent/edit?id=<?php echo $torr['id'] ?>'><button type='button' class='btn btn-sm ttbtn'><b><?php echo Lang::T("EDIT_TORRENT") ?></b></button></a>&nbsp;
            <a href="<?php echo URLROOT; ?>/torrent?id=<?php echo $torr['id'] ?>&bump=1"><button type='button' class='btn btn-sm ttbtn'><?php echo Lang::T("Bump") ?></button></a>
            <a href="<?php echo URLROOT; ?>/snatch?tid=<?php echo $torr['id']; ?>"><button type='button' class='btn btn-sm ttbtn'><?php echo Lang::T("SNATCHLIST") ?></button></a><?php
        }
        if (Users::get("delete_torrents") == "yes") { ?>
            <a href="<?php echo URLROOT; ?>/torrent/delete?id=<?php echo $torr['id']; ?>"><button type='button' class='btn btn-sm ttbtn'><?php echo Lang::T("Delete") ?></button></a>&nbsp;<?php
        } ?>
        </p>

    <div class="row">
       <p class='text-center'><legend><b><?php echo $torr["name"]; ?></b></legend></p>
    <div class="col-12 col-md-2"><?php
        if (!Config::get('FORCETHANKS')) {
            print("<a href=\"" . URLROOT . "/download?id=$torr[id]&amp;name=" . rawurlencode($torr["filename"]) . "\"><button type='button' class='btn btn-success btn-sm'>" . Lang::T("DOWNLOAD") . "</button></a>");
            print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . $torr["announce"] . "\"><button type='button' class='btn btn-danger btn-sm'>Magnet</button></a>");
        } else {
            print("<a href=\"" . URLROOT . "/download?id=$torr[id]&amp;name=" . rawurlencode($torr["filename"]) . "\"><button type='button' class='btn btn-sm ttbtn'>" . Lang::T("DOWNLOAD_TORRENT") . "</button></a>");
            $like = DB::select('thanks', 'user', ['thanked' => $torr['id'], 'type' => 'torrent', 'user' => Users::get('id')]);
            if ($like) {
                // magnet
                if ($torr["external"] == 'yes') {
                    if (Users::get("can_download") == "yes") {
                        // magnet
                        print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . URLROOT . "/announce.php?passkey=" . Users::get("passkey") . "\"><button type='button' class='btn btn-sm ttbtn'>Magnet Download</button></a>");
                    } else {
                        echo '<br>';
                    }
                } else {
                    if (Users::get("can_download") == "yes") {
                        // magnet button
                        print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm ttbtn'>Magnet Download</button></a>");
                    } else {
                        echo '<br>';
                    }
                }
            } else {
                if (Users::get("id") != $torr["owner"]) {
                    print("<a href='" . URLROOT . "/like/thanks?id=$torr[id]&type=torrent'><button  class='btn btn-sm ttbtn'>Thanks</button></a>");
                } else {
                    if ($torr["external"] == 'yes') {
                        // magnet
                        print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=" . URLROOT . "/announce.php?passkey=" . Users::get("passkey") . "\"><button type='button' class='btn btn-sm ttbtn'>Magnet Download</button></a>");
                    } else {
                        print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $torr["info_hash"] . "&dn=" . $torr["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><button type='button' class='btn btn-sm ttbtn'>Magnet Download</button></a>");
                    }
                }
            }
        } ?><br>

        <i class="fa fa-arrow-circle-up" style="color:green" aria-hidden="true">&nbsp;<?php echo Lang::T("SEEDS"); ?>: <?php echo number_format($torr["seeders"]); ?></i>&nbsp;
        <i class="fa fa-arrow-circle-down" style="color:red" aria-hidden="true">&nbsp;<?php echo Lang::T("LEECHERS"); ?>: <?php echo number_format($torr["leechers"]); ?></i>&nbsp;
        <img src='<?php echo URLROOT; ?>/assets/images/health/health_<?php echo health($torr['leechers'], $torr['seeders']); ?>.gif' alt='' /><br>
        <?php echo Lang::T("VIEWS"); ?>:&nbsp;<?php echo number_format($torr["views"]); ?>&nbsp;
        <?php echo Lang::T("HITS"); ?>:&nbsp;<?php echo number_format($torr["hits"]); ?>&nbsp; 
        <?php echo Lang::T("COMPLETED"); ?>: <?php echo number_format($torr["times_completed"]); ?>&nbsp; <?php
        if ($torr["external"] != "yes" && $torr["times_completed"] > 0) { ?>
            <a href='<?php echo URLROOT; ?>/complete?id=<?php echo $torr['id']; ?>'><?php echo Lang::T("WHOS_COMPLETED"); ?></a>]<br><?php  
        } ?><br><br>
        <b><?php echo Lang::T("DATE_ADDED"); ?>:</b>&nbsp;<?php echo date("d-m-Y H:i:s", TimeDate::utc_to_tz_time($torr["added"])); ?><br><?php
        if ($torr["anon"] == "yes" && !$torr['owned']) { ?>
            <b><?php echo Lang::T("ADDED_BY"); ?>:</b>&nbsp; Anonymous<br> <?php
        } elseif ($torr["username"]) { ?>
            <b><?php echo Lang::T("ADDED_BY"); ?>:</b>&nbsp;<a href='profile?id=<?php echo $torr["owner"]; ?>'><?php echo Users::coloredname($torr["owner"]); ?></a><br><?php
        } else { ?>
            <b><?php echo Lang::T("ADDED_BY"); ?>:</b>&nbsp; Unknown<br><?php
        }  ?>
        <b><?php echo Lang::T("LAST_CHECKED"); ?>: </b><?php echo date("d-m-Y H:i:s", TimeDate::utc_to_tz_time($torr["last_action"])); ?><br><br>
        <b><?php echo Lang::T("CATEGORY"); ?>:</b>&nbsp;<?php echo $torr["cat_parent"]; ?> -> <?php echo $torr["cat_name"]; ?><br>
        
		<b><?php echo Lang::T("Tags"); ?>: </b><?php echo $data["tags"]; ?><br> <?php
		
		if (empty($torr["lang_name"])) {
             $torr["lang_name"] = "Unknown/NA";
        } ?>
        <b><?php echo Lang::T("LANG"); ?>:</b>&nbsp;<?php echo $torr["lang_name"]; ?><br> <?php
        if (isset($torr["lang_image"]) && $torr["lang_image"] != "") {
           print("&nbsp;<img border=\"0\" src=\"" . URLROOT . "/assets/images/languages/" . $torr["lang_image"] . "\" alt=\"" . $torr["lang_name"] . "\" /><br>");
        } ?>
        <b><?php echo Lang::T("TOTAL_SIZE"); ?>:</b>&nbsp;<?php echo mksize($torr["size"]); ?><br>
        <b><?php echo Lang::T("Hash"); ?>:</b><br><?php echo $torr["info_hash"]; ?><br><br> <?php
        
        Bookmarks::select($torr['id']); ?>
        <a href="<?php echo URLROOT; ?>/report/torrent?torrent=<?php echo $torr['id']; ?>"><button type='button' class='btn btn-sm ttbtn'><?php echo Lang::T("REPORT") ?></button></a>&nbsp;<?php

        if ($torr["seeders"] <= 1) { ?>
            <a href='<?php echo URLROOT ?>/search/reseed?id=<?php echo $torr['id']; ?>'><button type='button' class='btn btn-sm ttbtn'><?php echo Lang::T("Reseed"); ?></button></a><br><?php
        }
		
		// LIKE MOD
        if (Config::get('ALLOWLIKES')) {
            $data1 = DB::raw('likes', 'user', ['liked' => $torr['id'], 'type' => 'torrent', 'user' => Users::get('id'), 'reaction' => 'like']);
            $likes = $data1->fetch(PDO::FETCH_ASSOC);
            if ($likes) { ?>
                <b>Reaction:</b>&nbsp;<a href='<?php echo URLROOT; ?>/like?id=<?php echo $torr['id']; ?>&type=unliketorrent'><i class='fa fa-thumbs-up' title='Like'></i></a><br><?php
            } else { ?>
                <b>Reaction:</b>&nbsp;<a href='<?php echo URLROOT; ?>/like?id=<?php echo $torr['id']; ?>&type=liketorrent'><i class='fa fa-thumbs-up' title='Like'></i></a><br><?php
            }
        }
        if (Config::get('ALLOWLIKES')) {
            $data3 = DB::run("SELECT * FROM `users` AS u LEFT JOIN `likes` AS l ON(u.id = l.user) WHERE liked=? AND type=?", [$torr['id'], 'torrent']);
            print('<b>Liked by</b>&nbsp;');
            foreach ($data3 as $stmt) :
                print("<a href='" . URLROOT . "/profile?id=$stmt[id]'>" . Users::coloredname($stmt['id']) . "</a>&nbsp;");
            endforeach;
        }
        echo "<br />";
        if (Config::get('FORCETHANKS')) {
            $data3 = DB::run("SELECT * FROM `users` AS u LEFT JOIN `thanks` AS l ON(u.id = l.user) WHERE thanked=? AND type=?", [$torr['id'], 'torrent']);
            print('<b>Thanked by</b>&nbsp;');
            foreach ($data3 as $stmt) :
                print("<a href='" . URLROOT . "/profile?id=$stmt[id]'>" . Users::coloredname($stmt['id']) . "</a>&nbsp;");
            endforeach;
        }
        echo "<br />";
        if ($torr["external"] != 'yes' && $torr["freeleech"] == '1') {
            print("<b>" . Lang::T("FREE_LEECH") . ": </b><font color='#ff0000'>" . Lang::T("FREE_LEECH_MSG") . "</font><br />");
        }
        if ($torr["external"] != 'yes' && $torr["vip"] == 'yes') {
            print("<b>Torrent VIP: </b><font color='orange'>Torrent reserved for VIP</font><br>");
        }
        if (Users::get('id')) {
            echo Ratings::ratingtor($torr['id']);
        }
        // Scrape External Torrents
        if ($torr["external"] == 'yes') {
            echo $data['scraper'];
        } ?>

    </div>

    <div class="col-12 col-md-7"> <?php
        if (!empty($torr["tmdb"]) && in_array($torr["cat_parent"], SerieCats)) {
            $id_tmdb = TMDBS::getId($torr["tmdb"]);
            $total = DB::column('tmdb', ' count(*)', ['id_tmdb' => $id_tmdb, 'type' => 'show']);
            if ($total > 0) {
                $_data = TMDBS::getSerie($id_tmdb); ?>
                
                <b> Plot : </b><?php echo $_data["plot"] ?><br><br>
                <b> Actors : </b><br>
                <div class='row'>  <?php
                   $casting = explode('&', $_data["actor"]);
                   for ($i = 0; $i <= 3; $i++) {
                     list($pseudo, $role, $image) = explode("*", $casting[$i]);;
                     print("<div class='col-3'>".$pseudo . "<br>");
                     print(" <img class='avatar3' src='" . $image . "' /><br>");
                     print("".$role . "<br></div>");
                   } ?>
                </div>
                <b> Seasons : </b><?php echo $_data["season"] ?> (<?php echo $_data["episode"] ?> épisodes) <br>
                <b> Status : </b><?php echo $_data["status"] ?> <br>
                <b> Date : </b><?php echo $_data["date"] ?> <br>
                <b> Creator : </b><?php echo $_data["creator"] ?><br>
                <b> Genre : </b><?php echo $_data["genre"] ?><br>
                <br> <?php
            }
        } elseif (!empty($torr["tmdb"]) && in_array($torr["cat_parent"], MovieCats)) {
            $id_tmdb = TMDBS::getId($torr["tmdb"]);
            $total = DB::column('tmdb', ' count(*)', ['id_tmdb' => $id_tmdb, 'type' => 'movie']);
            if ($total > 0) {
                $_data = TMDBS::getFilm($id_tmdb); ?>
                
                <b> Plot : </b><?php echo $_data["plot"] ?> <br><br>
                <b> Actors : </b><br>
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
                <b> Genre : </b><?php echo $_data["genre"] ?> <br>
                <br> <?php
            }
        } else { ?>
            <b><?php echo Lang::T("DESCRIPTION"); ?>:</b><br><?php echo format_comment($torr['descr']); ?><br> <?php
        }
        if (!empty($torr["tube"])) {
            print("<embed src='" . str_replace("watch?v=", "v/", htmlspecialchars($torr["tube"])) . "' type=\"application/x-shockwave-flash\"  height=\"410\" width='100%'></embed>");
        } ?>
    </div>

    <div class="col-12 col-md-3"> <?php
        if ($torr["tmdb"] != "") {
            $url = UPLOADDIR.'/tmdb/' . $_data["type"].'/' . $_data["poster"]; ?>
            <img src='<?php echo data_uri($url, $_data["poster"]) ?>' height='450' width='100%' border='0' alt='' /><br><br><?php
        }
        if ($torr["image1"] != "") {
            $img1 = "<img src='" . data_uri(UPLOADDIR . "/images/" . $torr["image1"], $torr['image1']) . "' height='250' width='100%' border='0' alt='' />";
        }
        print("" . $img1 . ""); ?> <br><br> <?php
        if ($torr["image2"] != "") {
            $img2 = "<img src='" . data_uri(UPLOADDIR . "/images/" . $torr["image2"], $torr['image2']) . "' height='250' width='100%' border='0' alt='' />";
        }
        print("" . $img2 . ""); ?> <br><br> <?php ?>
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
$query = "SELECT torrents.id, torrents.anon, torrents.descr, torrents.announce, torrents.category, torrents.sticky,  torrents.vip,  torrents.tube,  torrents.tmdb, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, torrents.image1, torrents.image2,
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
    print(Lang::T("NO_SIMILAR_TORRENT_FOUND"));
}