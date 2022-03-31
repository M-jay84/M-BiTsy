<?php

// get image embeded image
function data_uri($file, $mime)
{
    $contents = file_get_contents($file);
    $base64 = base64_encode($contents);
    return ('data:' . $mime . ';base64,' . $base64);
}

// Get Image Or Poster
function getimage($row) {
    if ($row["tmdb"] != '') {
        $id_tmdb = TMDBS::getId($row["tmdb"]);
        if (in_array($row["cat_parent"], SerieCats)) {
            $_data = TMDBS::getSerie($id_tmdb);
        } elseif (in_array($row["cat_parent"], MovieCats)) {
            $_data = TMDBS::getFilm($id_tmdb);
        }
        $url = UPLOADDIR.'/tmdb/' . $_data["type"].'/' . $_data["poster"];
        $img = data_uri($url, $_data["poster"]);

    } elseif ($row["image1"] != '') {
        $img = data_uri(UPLOADDIR . "/images/" . $row["image1"], $row['image1']);

    } elseif ($row["image2"] != '') {
        $img = data_uri(UPLOADDIR . "/images/" . $row["image2"], $row['image2']);

    } else {
        $img = "" . URLROOT . "/assets/images/misc/default_avatar.png";
    }
    return $img;
}

// Function To Count Database Table
function get_row_count($table, $suffix = "")
{
    global $pdo;
    $suffix = !empty($suffix) ? ' ' . $suffix : '';
    $row = DB::run("SELECT COUNT(*) FROM $table $suffix")->fetchColumn();
    return $row;
}

// Returns The Size
function mksize($s, $precision = 2)
{
    $suf = array("B", "kB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");

    for ($i = 1, $x = 0; $i <= count($suf); $i++, $x++) {
        if ($s < pow(1024, $i) || $i == count($suf)) // Change 1024 to 1000 if you want 0.98GB instead of 1,0000MB
        {
            return number_format($s / pow(1024, $x), $precision) . " " . $suf[$x];
        }
    }
}

// Shorten Name
function CutName($vTxt, $Car)
{
    if (strlen($vTxt) > $Car) {
        return substr($vTxt, 0, $Car) . "...";
    }
    return $vTxt;
}

// Returns a numeric conversion according to a string
function strtobytes($str)
{
    $str = trim($str);
    if (!preg_match('!^([\d\.]+)\s*(\w\w)?$!', $str, $matches)) {
        return 0;
    }

    $num = $matches[1];
    $suffix = strtolower($matches[2]);
    switch ($suffix) {
        case "tb": // TeraByte
            return $num * 1099511627776;
        case "gb": // GigaByte
            return $num * 1073741824;
        case "mb": // MegaByte
            return $num * 1048576;
        case "kb": // KiloByte
            return $num * 1024;
        case "b": // Byte
            default:
            return $num;
    }
}

// Active Link
function activelink($page, $define = false)
{
    if ($define == true && $_GET['type'] == $page) {
        $active = 'btn btn-sm ttbtnactive';
        return $active;
    }
    $active = $_GET['url'] == $page ? 'btn btn-sm ttbtnactive' : 'btn btn-sm ttbtn';
    return $active;
}

// Profile Navbar
function usermenu($id, $page = false)
{
    $messageactive = $page == 'messages' ? 'btn btn-sm ttbtnactive' : 'btn btn-sm ttbtn';
    ?>
    <a href='<?php echo URLROOT; ?>/profile?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('profile'); ?>">Profile</button></a>
    <?php if (Users::get("id") == $id or Users::get("class") > _UPLOADER) {?>
    <a href='<?php echo URLROOT; ?>/profile/edit?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('profile/edit'); ?>">Edit</button></a>&nbsp;
    <?php }?>
    <?php if (Users::get("id") == $id) {?>
    <a href='<?php echo URLROOT; ?>/account/password?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('account/password'); ?>">Password</button></a>
    <a href='<?php echo URLROOT; ?>/account/email?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('account/email'); ?>">Email</button></a>
    <a href='<?php echo URLROOT; ?>/message/overview'><button type="button" class="<?php echo $messageactive; ?>">Messages</button></a>
    <a href='<?php echo URLROOT; ?>/bonus'><button type="button" class="<?php echo activelink('bonus'); ?>">Seed Bonus</button></a>
    <a href='<?php echo URLROOT; ?>/bookmark'><button type="button" class="<?php echo activelink('bookmarks'); ?>">Bookmarks</button></a>
    <?php }?>
    <?php if (Users::get("view_users")) {?>
    <a href='<?php echo URLROOT; ?>/friend?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('friends'); ?>">Friends</button></a>
    <?php }?>
    <?php if (Users::get("view_torrents")) {?>
    <a href='<?php echo URLROOT; ?>/peer/seeding?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('peer/seeding'); ?>"><?php echo Lang::T("SEEDING"); ?></button></a>
    <a href='<?php echo URLROOT; ?>/peer/uploaded?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('peer/uploaded'); ?>"><?php echo Lang::T("UPLOADED"); ?></button></a>
    <?php }?>
    <?php if (Users::get("class") > _UPLOADER) {?>
    <a href='<?php echo URLROOT; ?>/warning?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('warning'); ?>">Warn</button></a>
    <a href='<?php echo URLROOT; ?>/profile/admin?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('profile/admin'); ?>">Admin</button></a>
	<?php }?>
    <br><br><?php
}

// Torrent Navbar
function torrentmenu($id, $external = 'no')
{
    ?>
    <a href='<?php echo URLROOT; ?>/torrent?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('torrent'); ?>">Back</button></a>
    <?php if (Users::get("id") == $id or Users::get("edit_torrents") == 'yes') {?>
    <a href='<?php echo URLROOT; ?>/torrent/edit?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('torrent/edit'); ?>">Edit</button></a>
    <?php }?>
    <a href='<?php echo URLROOT; ?>/comment?type=torrent&amp;id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('comments'); ?>">Comments</button></a>
    <a href='<?php echo URLROOT; ?>/torrent/torrentfilelist?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('torrent/torrentfilelist'); ?>">Files</button></a>
    <?php if ($external != 'yes') {?>
    <a href='<?php echo URLROOT; ?>/peer/peerlist?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('peer/peerlist'); ?>">Peers</button></a>
    <?php }
    if ($external == 'yes') {?>
     <a href='<?php echo URLROOT; ?>/torrent/torrenttrackerlist?id=<?php echo $id; ?>'><button type="button" class="<?php echo activelink('torrent/torrenttrackerlist'); ?>">Trackers</button></a>
    <?php }
    ?>
    <br><br>
    <?php
}

// Upload Image - torrent/edit
function uploadimage($x, $imgname, $tid)
{
    $imagesdir = UPLOADDIR."/images";
    $allowed_types = ALLOWEDIMAGETYPES;
    if (!($_FILES["image$x"]["name"] == "")) {
        if ($imgname != "") {
            $img = "$imagesdir/$imgname";
            $del = unlink($img);
        }
        $y = $x + 1;
        $im = getimagesize($_FILES["image$x"]["tmp_name"]);
        if (!$im[2]) {
            Redirect::autolink(URLROOT . $_SERVER["HTTP_REFERER"], "Invalid Image $y.");
        }
        if (!array_key_exists($im['mime'], $allowed_types)) {
            Redirect::autolink(URLROOT . $_SERVER["HTTP_REFERER"], Lang::T("INVALID_FILETYPE_IMAGE"));
        }
        if ($_FILES["image$x"]["size"] > IMAGEMAXFILESIZE) {
            Redirect::autolink(URLROOT . $_SERVER["HTTP_REFERER"], sprintf(Lang::T("INVAILD_FILE_SIZE_IMAGE"), $y));
        }
        $uploaddir = "$imagesdir/";
        $ifilename = $tid . $x . $allowed_types[$im['mime']];
        $copy = copy($_FILES["image$x"]["tmp_name"], $uploaddir . $ifilename);
        if (!$copy) {
            Redirect::autolink(URLROOT . $_SERVER["HTTP_REFERER"], sprintf(Lang::T("ERROR_UPLOADING_IMAGE"), $y));
        }
        return $ifilename;
    }
}

// Escape (Not Needed in Prepared Statements)
function sqlesc($x)
{
    if (!is_numeric($x)) {
        $x = "'" . $x . "'";
    }
    return $x;
}

function getexttype($ext = '')
{
    $ext = strtolower($ext);
    $music = array('mp3', 'wav', 'flac', 'm3u');
    $video = array('mp4', 'avi', 'mkv', 'flv', 'wmv');
    $file = array('txt', 'pdf', 'doc', 'zip', 'nfo', 'srt', 'exe');
    $image = array('jpeg', 'gif', 'png');
    if ($ext == false || $ext == '') {
        $filetype_icon = "&nbsp;<i class='fa fa-question'></i>";
    } else if (in_array($ext, $music)) {
        $filetype_icon = "&nbsp;<i class='fa fa-music'></i>";
    } else if (in_array($ext, $video)) {
        $filetype_icon = "&nbsp;<i class='fa fa-film'></i>";
    } else if (in_array($ext, $file)) {
        $filetype_icon = "&nbsp;<i class='fa fa-file'></i>";
    } else if (in_array($ext, $image)) {
        $filetype_icon = "&nbsp;<i class='fa fa-picture-o'></i>";
    }
    return $filetype_icon;
}

function get_attachment($id)
{
    $sql = DB::raw('attachments', '*', ['content_id' =>$id]);
    if ($sql->rowCount() != 0) {
        foreach ($sql as $row7) {
            print("<br><br>&nbsp;<b>$row7[filename]</b><br>");
            $extension = substr($row7['filename'], -3);
            if ($extension == 'zip') {
                $daimage = UPLOADDIR . "/attachment/$row7[file_hash].data";
                if (file_exists($daimage)) {
                    print(" <a class='btn btn-sm ttbtn' href=\"" . URLROOT . "/download/attachment?id=$row7[id]&amp;hash=" . rawurlencode($row7["file_hash"]) . "\"><i class='fa fa-file-archive-o tticon' ></i>Download</a><br>");
                } else {
                    print("no zip<br>");
                }
            } else {
                $daimage = "uploads/thumbnail/$row7[file_hash].jpg";
                if (file_exists($daimage)) {
					$switchimage = UPLOADDIR . "/attachment/$row7[file_hash].data"; ?> 
					<a href='<?php echo URLROOT ?>/download/images?hash=<?php echo $row7['file_hash'] ?>'><img alt="test image" src="<?php echo data_uri($switchimage, $row7['filename']); ?>" height="100px" width="100px" ></a> <?php
                } else {
                    print("<a href=\"" . URLROOT . "/download/attachment?id=$row7[id]&amp;hash=" . rawurlencode($row7["file_hash"]) . "\"><img src='" . URLROOT . "/thumb/$row7[file_hash].jpg' height='110px' width='110px' border='0' alt='' /></a><br>");
                }
            }
        }
    }
}

function any_uploaded($name) {
  foreach ($_FILES[$name]['error'] as $ferror) {
    if ($ferror != UPLOAD_ERR_NO_FILE) {
      return true;
    }
  }
  return false;
}

function set_attachmnent($topicid, $postid)
{
    if (any_uploaded('upfile')):
        $array = array($_FILES['upfile']['name']);
        $result = array_filter($array);
        foreach ($result as $k => $ar):
            // check if file has one of the following extensions

            $sourcePath = $_FILES['upfile']['tmp_name'][$k]; // Storing source path of the file in a variable
            $fileSize = $_FILES['upfile']['size'][$k];
            $fileName = $_FILES['upfile']['name'][$k];
            $extension = substr($fileName, -3);
            $hash = sha1($sourcePath);

            $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip');
            if (!in_array($extension, $allowedfileExtensions)) {
                Redirect::autolink(URLROOT . '/forum', "Sorry, only zip, JPG, JPEG, PNG, GIF files are allowed.");
            }

            $newfile = $hash . "." . $extension;
            $targetPath = UPLOADDIR . "/attachment/" . $hash . ".data"; // Target path where file is to be stored
            $thumbPath = "uploads/thumbnail/" . $hash . ".jpg"; // Target path where attachment as jpg is to be stored

            if ($extension == 'zip') {
                move_uploaded_file($sourcePath, $targetPath); // Moving Uploaded file
                DB::run("INSERT INTO attachments (content_id, user_id, upload_date, filename, file_size, file_hash, topicid)
                        VALUES (?,?,?,?,?,?,?)",
                        [$postid, Users::get('id'), TimeDate::gmtime(), $fileName, $fileSize, $hash, $topicid]);
            } else {
                if (move_uploaded_file($sourcePath, $targetPath)) { // Moving Uploaded file
                    SimpleThumbnail::create()->image($targetPath)->thumbnail(100)->to($thumbPath);
                    DB::run("INSERT INTO attachments (content_id, user_id, upload_date, filename, file_size, file_hash, topicid)
                            VALUES (?,?,?,?,?,?,?)",
                            [$postid, Users::get('id'), TimeDate::gmtime(), $fileName, $fileSize, $hash, $topicid]);
                }
            }
        endforeach;
    endif;
}

//convert multi to single array
function array_flatten($array) {
    if (!is_array($array)) {
      return FALSE;
    }
    $result = array();
    foreach ($array as $key => $value) {
      if (is_array($value)) {
        $result = array_merge($result, array_flatten($value));
      }
      else {
        $result[$key] = $value;
      }
    }
    return $result;
}

function userpostdetails($row, $type = null)
{
    $postername = Users::coloredname($row["id"]);
    if ($postername == "") {
        $postername = Lang::T("DELUSER");
        $title = Lang::T("DELETED_ACCOUNT");
        $avatar = "";
        $usersignature = "";
        $userdownloaded = "";
        $useruploaded = "";
    } else {
        $privacylevel = $row["privacy"];
        $avatar = htmlspecialchars($row["avatar"]);
        $title = format_comment($row["title"]);
        $usersignature = stripslashes(format_comment($row["signature"]));
        $userdownloaded = mksize($row["downloaded"]);
        $useruploaded = mksize($row["uploaded"]);
    }
    $userratio = $row["downloaded"] > 0 ? number_format($row["uploaded"] / $row["downloaded"], 1) : "---";
    
    $commenttext = $row["body"] != '' ? format_comment($row["body"]) : format_comment($row["text"]);

    if (!$avatar) {
        $avatar = URLROOT . "/assets/images/misc/default_avatar.png";
    }

    $edit = null;
    if (Users::get("edit_torrents") == "yes" || Users::get("edit_news") == "yes" || Users::get('id') == $row['user']) {
        $edit = '<a href="' . URLROOT . '/post/edit&amp;postid=' . $row['id'] . '"><i class="fa fa-pencil" title="Edit" aria-hidden="true"></i></a>&nbsp;';
    }

    $delete = null;
    if (Users::get("delete_torrents") == "yes" || Users::get("delete_news") == "yes") {
        $delete = '<a href="' . URLROOT . '/post/delete&amp;postid=' . $row['id'] . '&sure=0"><i class="fa fa-trash" title="Delete" aria-hidden="true"></i></a>&nbsp;';
    }

    $editcomment = null;
    if ($type == "torrent" && Users::get("edit_torrents") == "yes" || $type == "news"  || $type == "req" && Users::get("edit_news") == "yes" || Users::get('id') == $row['user']) {
        $editcomment = '<a href="' . URLROOT . '/comment/edit?id=' . $row["id"] . '&amp;type=' . $type . '"><i class="fa fa-pencil" title="Edit" aria-hidden="true"></i></a>&nbsp;';
    }

    $deletecomment = null;
    if ($type == "torrent" && Users::get("delete_torrents") == "yes" || $type == "req" || $type == "news" && Users::get("delete_news") == "yes") {
        $deletecomment = '<a href="' . URLROOT . '/comment/delete?id=' . $row["id"] . '&amp;type=' . $type . '"><i class="fa fa-trash" title="Delete" aria-hidden="true"></i></a>&nbsp;';
    }

    return $data = [
        'postername' => $postername,
        'title' => $title,
        'avatar' => $avatar,
        'usersignature' => $usersignature,
        'userdownloaded' => $userdownloaded,
        'useruploaded' => $useruploaded,
        'privacylevel' => $privacylevel,
        'userratio' => $userratio,
        'commenttext' => $commenttext,
        'edit' => $edit,
        'delete' => $delete,
        'editcomment' => $editcomment,
        'deletecomment' => $deletecomment
    ];

}

function commentlinktitle($row)
{
    if ($row['type'] == 'torrent' && $row['torrent'] > 0) {
        $link = 'Torrent: <a href="'.URLROOT.'/torrent?id=' . $row['torrent'] . '">' . $row['torrenttitle'] . '</a>&nbsp;';

    } elseif ($row['type'] == 'news' && $row['news'] > 0) {
        $link = 'News: <a href="'.URLROOT.'/comment?id=' . $row['news'] . '&amp;type=news">' . $row['newstitle'] . '</a>&nbsp;';
                
    } 
    return $link;
}