<?php
// setup the forum header
function forumheader($location, $subforum = '', $subforumid = 0)
{
    echo "<div>
    <i class='fa fa-question' title='Help'></i>&nbsp;<a href='" . URLROOT . "/faq'>" . Lang::T("FORUM_FAQ") . "</a>&nbsp; &nbsp;&nbsp;
    <i class='fa fa-search' title='Search'></i>&nbsp;<a href='" . URLROOT . "/forum/search'>" . Lang::T("SEARCH") . "</a>&nbsp; &nbsp;
    <b>" . Lang::T("FORUM_CONTROL") . "</b>
    &middot; <a href='" . URLROOT . "/forum/viewunread'>" . Lang::T("FORUM_NEW_POSTS") . "</a>
    &middot; <a href='" . URLROOT . "/forum?do=catchup'>" . Lang::T("FORUM_MARK_READ") . "</a>
    </div><br />";
    if ($subforum == '') {
        print("<div>" . Lang::T("YOU_ARE_IN") . ": &nbsp;<a href='" . URLROOT . "/forum'>" . Lang::T("FORUMS") . "</a> <b style='vertical-align:middle'>/ $location</b></div>");
    } else {
        print("<div>" . Lang::T("YOU_ARE_IN") . ": &nbsp;<a href='" . URLROOT . "/forum'>" . Lang::T("FORUMS") . "</a>/<a href='" . URLROOT . "/forum/view&forumid=$subforumid'>$subforum</a><b style='vertical-align:middle'>/ $location</b></div>");
    }
}

// Mark all forums as read
function catch_up()
{
    if (!$_SESSION['loggedin'] == true) {
        return;
    }
    $userid = Users::get("id");
    $res = DB::run("SELECT id, lastpost FROM forum_topics");
    while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
        $topicid = $arr["id"];
        $postid = $arr["lastpost"];
        $r = DB::run("SELECT id,lastpostread FROM forum_readposts WHERE userid=? and topicid=?", [$userid, $topicid]);
        if ($r->rowCount() == 0) {
            DB::insert('forum_readposts', [ 'userid'=>$userid,'topicid'=>$topicid,'lastpostread'=>$postid]);
        } else {
            $a = $r->fetch(PDO::FETCH_ASSOC);
            if ($a["lastpostread"] < $postid) {
                DB::update('forum_readposts', ['lastpostread' =>$postid], ['id' => $a["id"]]);
            }
        }
    }
}

// Returns the minimum read/write class levels of a forum
function get_forum_access_levels($forumid)
{
    $res = DB::run("SELECT minclassread, minclasswrite FROM forum_forums WHERE id=?", [$forumid]);
    if ($res->rowCount() != 1) {
        return false;
    }
    $arr = $res->fetch(PDO::FETCH_ASSOC);
    return array("read" => $arr["minclassread"], "write" => $arr["minclasswrite"]);
}

// Returns the forum ID of a topic, or false on error
function get_topic_forum($topicid)
{
    $res = DB::run("SELECT forumid FROM forum_topics WHERE id=?", [$topicid]);
    if ($res->rowCount() != 1) {
        return false;
    }
    $arr = $res->fetch(PDO::FETCH_LAZY);
    return $arr[0];
}

// Returns the ID of the last post of a forum
function update_topic_last_post($topicid)
{
    $res = DB::run("SELECT id FROM forum_posts WHERE topicid=? ORDER BY id DESC LIMIT 1", [$topicid]);
    $arr = $res->fetch(PDO::FETCH_LAZY) or Redirect::autolink(URLROOT . '/forum', 'No post found');
    $postid = $arr[0];
    DB::update('forum_topics', ['lastpost'=>$postid], ['id'=>$topicid]);
}

// Returns The ID Of A Last Post In A Forum Or Otherwise 0
function get_forum_last_post($forumid)
{
    $res = DB::run("SELECT lastpost FROM forum_topics WHERE forumid=? ORDER BY lastpost DESC LIMIT 1", [$forumid]);
    $arr = $res->fetch(PDO::FETCH_LAZY);
    $postid = $arr[0];
    if ($postid) {
        return $postid;
    } else {
        return 0;
    }
}

// Top forum posts
function forumpostertable($res)
{
    print("<br /><div>");
    ?>
    <font><?php echo Lang::T("FORUM_RANK"); ?></font>
    <font><?php echo Lang::T("FORUM_USER"); ?></font>
    <font><?php echo Lang::T("FORUM_POST"); ?></font>
    <br>
    <?php
    $num = 0;
    while ($a = $res->fetch(PDO::FETCH_ASSOC)) {
        ++$num;
        print("$num &nbsp; <a href='" . URLROOT . "/profile?id=$a[id]'><b>$a[username]</b></a> $a[num]");
    }
    if ($num == 0) {
        print("<b>No Forum Posters</b>");
    }
    print("</div>");
}

// Inserts a quick jump menu
function insert_quick_jump_menu($currentforum = 0)
{
    print("<div style='text-align:right'><form method='get' action='?' name='jump'>\n");
    print("<input type='hidden' name='action' value='" . URLROOT . "/forum/view' />\n");
    $res = DB::raw('forum_forums', '*', '', 'ORDER BY name');
    if ($res->rowCount() > 0) {
        print(Lang::T("FORUM_JUMP") . ": ");
        print("<select class='styled' name='forumid' onchange='if(this.options[this.selectedIndex].value != -1){ forms[jump].submit() }'>\n");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            if (Users::get("class") >= $arr["minclassread"] || (!$_SESSION && $arr["guest_read"] == "yes")) {
                print("<option value='" . $arr["id"] . "'" . ($currentforum == $arr["id"] ? " selected='selected'>" : ">") . $arr["name"] . "</option>\n");
            }

        }
        print("</select>\n");
        print("<button type='submit' class='btn btn-sm ttbtn'>" . Lang::T("GO") . "</button>\n");
    }
    print("</form>\n</div>");
}

// LASTEST FORUM POSTS
function latestforumposts($sub_id = 0)
{
    ?>
    <div class="row">
    <div class="col-lg-12">
    <div class="wrapper wrapper-content animated fadeInRight">

    <div class="row frame-header">
    <div class="col-md-5">
    Latest Topic Title
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Replies
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Views
    </div>
    <div class="col-md-2 d-none d-sm-block">
    Author
    </div>
    <div class="col-md-3 d-none d-sm-block">
    Last Post
    </div>
    </div>
    <?php
    // HERE GOES THE QUERY TO RETRIEVE DATA FROM THE DATABASE AND WE START LOOPING ///
    if ($sub_id != 0) {
        $for = DB::run("SELECT * FROM forum_topics WHERE forumid IN (SELECT id FROM forum_forums WHERE sub=$sub_id) ORDER BY lastpost DESC LIMIT 10");
    } else {
        $for = DB::raw('forum_topics', '*', '', 'ORDER BY lastpost DESC LIMIT 8');
    }
    if ($for->rowCount() == 0) {
        print("<b>No Latest Topics</b>");
    }
    while ($topicarr = $for->fetch(PDO::FETCH_ASSOC)) {
        // Set minclass
        $forum = DB::select('forum_forums', 'name,minclassread,guest_read', ['id'=>$topicarr['forumid']]);
        if ($forum && Users::get("class") >= $forum["minclassread"] || $forum["guest_read"] == "yes") {
            $forumname = "<a href='" . URLROOT . "/forum/view&amp;forumid=$topicarr[forumid]'><b>" . htmlspecialchars($forum["name"]) . "</b></a>";
            $topicid = $topicarr["id"];
            $topic_title = stripslashes($topicarr["subject"]);
            $topic_userid = $topicarr["userid"];
            // Topic Views
            $views = $topicarr["views"];
            // GETTING TOTAL NUMBER OF POSTS ///
            $res = DB::run("SELECT COUNT(*) FROM forum_posts WHERE topicid=?", [$topicid]);
            $arr = $res->fetch(PDO::FETCH_LAZY);
            $posts = $arr[0];
            $replies = max(0, $posts - 1);
            // GETTING USERID AND DATE OF LAST POST ///
            $arr = DB::select('forum_posts', '*', ['topicid'=>$topicid], 'ORDER BY id DESC LIMIT 1');
            $postid = 0 + $arr["id"];
            $userid = 0 + $arr["userid"];
            $added = TimeDate::utc_to_tz($arr["added"]);
            // GET NAME OF LAST POSTER ///
            $res = DB::raw('users', 'id,username', ['id'=>$userid]);
            if ($res->rowCount() == 1) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $username = "<a href='" . URLROOT . "/profile?id=$userid'>" . Users::coloredname($userid) . "</a>";
            } else {
                $username = "Unknown[$topic_userid]";
            }
            // GET NAME OF THE AUTHOR ///
            $res = DB::raw('users', 'username', ['id'=>$topic_userid]);
            if ($res->rowCount() == 1) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $author = "<a href='" . URLROOT . "/profile?id=$topic_userid'>" . Users::coloredname($topic_userid) . "</a>";
            } else {
                $author = "Unknown[$topic_userid]";
            }
            // GETTING THE LAST INFO AND MAKE THE TABLE ROWS ///
            $r = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=$userid AND topicid=$topicid");
            $a = $r->fetch(PDO::FETCH_LAZY);
            $new = !$a || $postid > $a[0];
            $subject = "<a href='" . URLROOT . "/topic?topicid=$topicid&amp;page=last#last'>" . stripslashes(encodehtml($topicarr["subject"])) . "</a>";
            ?>
            <div class="row border  ttborder">
            <div class="col-md-5 d-none d-sm-block">
            <b><?php echo $subject; ?></b>
            </div>
            <div class="col-md-1 d-none d-sm-block">
            <?php echo $replies; ?>
            </div>
            <div class="col-md-1 d-none d-sm-block">
            <?php echo $views; ?>
            </div>
            <div class="col-md-2 d-none d-sm-block">
            <b><center><?php echo $author; ?></center></b>
            </div>
            <div class="col-md-3">
            <small><b><?php echo $subject; ?></b>&nbsp;
            by&nbsp;<b><?php echo $username; ?></b></small><br><small style='white-space: nowrap'><b>
            <?php echo $added; ?></b></small>
            </div>
            </div>
            <?php
        }
    }
    print("</div></div></div><br>");
}

// Get last post info in a array return img & lastpost
function lastpostdetails($lastpostid)
{
    $post_res = DB::raw('forum_posts', 'added,topicid,userid', ['id'=>$lastpostid]);
    if ($post_res->rowCount() == 1) {
        $post_arr = $post_res->fetch(PDO::FETCH_ASSOC) or Redirect::autolink(URLROOT . '/forum', "Bad forum last_post");
        $lastposterid = $post_arr["userid"];
        $lastpostdate = TimeDate::utc_to_tz($post_arr["added"]);
        $lasttopicid = $post_arr["topicid"];
        $user_arr = DB::select('users', 'username', ['id'=>$lastposterid]);
        $lastposter = Users::coloredname($lastposterid);
        $topic_arr = DB::select('forum_topics', 'subject', ['id'=>$lasttopicid]);
        $lasttopic = stripslashes(htmlspecialchars($topic_arr['subject']));
        //cut last topic
        $latestleng = 10;
        $lastpost = "<small><a href='" . URLROOT . "/topic?topicid=$lasttopicid&amp;page=last#last'><b>" . CutName($lasttopic, $latestleng) . "</b></a> by <a href='" . URLROOT . "/profile?id=$lastposterid'><b>$lastposter</b></a><br />$lastpostdate</small>";
        if ($_SESSION['loggedin'] == true) {
            $a = DB::raw('forum_readposts', 'lastpostread', ['userid'=>Users::get('id'), 'topicid'=>$lasttopicid])->fetch();
        }
        //define the images for new posts or not on index
        if ($a && $a['lastpostread'] == $lastpostid) {
            $img = "<i class='fa fa-file-text' title='Read'></i>";
        } else {
            $img = "<i class='fa fa-file-text tticon-red' title='UnRead'></i>";
        }
    } else {
        $lastpost = "<span class='small'>No Posts</span>";
        $img = "folder";
    }

    $detail = [
        'img' => $img,
        'lastpost' => $lastpost,
    ];
    return $detail;
}

function modoptions($topicid, $subject, $forumid, $locked, $sticky) {
    print("<div class='f-border f-mod_options' align='center'><table width='100%' cellspacing='0'><tr class='f-title'><th><center>" . Lang::T("FORUMS_MOD_OPTIONS") . "</center></th></tr>\n");
    print("<tr><td class='ttable_col2'>\n");
    print("<form method='post' action='" . URLROOT . "/topic/rename'>\n");
    print("<input type='hidden' name='topicid' value='$topicid' />\n");
    print("<input type='hidden' name='returnto' value='topic?topicid=$topicid' />\n");

            print("<div align='center'  style='padding:3px'>Rename topic:
            <div class='row justify-content-md-center'>
            <div class='col col-lg-4'>
            <input class='form-control' type='text' name='subject' value='" . stripslashes(htmlspecialchars($subject)) . "' />
            </div>
            </div>
            \n");
            print("<input type='submit' value='Apply' />");
            print("</div></form>\n");

            print("<form method='post' action='" . URLROOT . "/topic/move?topicid=$topicid'>\n");
            print("<div align='center' style='padding:3px'>");
            print("Move this thread to: <select name='forumid'>");
            $res = DB::raw('forum_forums', 'id,name,minclasswrite', '', 'ORDER BY name');
            while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                if ($arr["id"] != $forumid && Users::get("class") >= $arr["minclasswrite"]) {
                    print("<option value='" . $arr["id"] . "'>" . $arr["name"] . "</option>\n");
                }
            }
            print("</select>");
            print("<input type='submit' value='Apply' /></div></form>\n");
            
    print("<p class='text-center'>\n");
    if ($locked) {
                print(Lang::T("FORUMS_LOCKED") . ": <a href='" . URLROOT . "/topic/unlock?forumid=$forumid&topicid=$topicid' title='Unlock'><i class='fa fa-unlock tticon-red' title='UnLock Topic'></i></a>\n");
    } else {
                print(Lang::T("FORUMS_LOCKED") . ": <a href='" . URLROOT . "/topic/lock?forumid=$forumid&topicid=$topicid' title='Lock'><i class='fa fa-lock tticon' title='Lock Topic'></i></a>\n");
    }
    print("Delete Entire Topic: <a href='" . URLROOT . "/topic/delete?topicid=$topicid?sure=0' title='Delete'><i class='fa fa-trash-o tticon-red' title='Delete Topic'></i></a>\n");
    if ($sticky) {
        print(Lang::T("FORUMS_STICKY") . ": <a href='" . URLROOT . "/topic/unsetsticky?forumid=$forumid&topicid=$topicid' title='UnStick'><i class='fa fa-exclamation tticon-red' title='Unstick Topic'></i></a>\n");
    } else {
        print(Lang::T("FORUMS_STICKY") . ": <a href='" . URLROOT . "/topic/setsticky?forumid=$forumid&topicid=$topicid' title='Stick'><i class='fa fa-exclamation tticon' title='stick Topic'></i></a>\n");
    }
    print("</p></td></tr></table></div>\n");
}

function posterdetails($userid) {
    $forumposts = DB::column('forum_posts', 'COUNT(*)', ['userid' =>$userid]);
    $arr2 = DB::select('users', '*', ['id' =>$userid]);
    $postername = Users::coloredname($userid);
    $quotename = $arr2["username"];
    if ($postername == "") {
        $by = "Deluser";
        $title = "Deleted Account";
        $privacylevel = "strong";
        $usersignature = "";
        $userdownloaded = "0";
        $useruploaded = "0";
        $avatar = "";
        $nposts = "-";
        $tposts = "-";
    } else {
        $avatar = htmlspecialchars($arr2["avatar"]);
        $userdownloaded = mksize($arr2["downloaded"]);
        $useruploaded = mksize($arr2["uploaded"]);
        $privacylevel = $arr2["privacy"];
        $usersignature = stripslashes(format_comment($arr2["signature"]));
        $userratio = $arr2["downloaded"] > 0 ? number_format($arr2["uploaded"] / $arr2["downloaded"], 1) : "---";
        if (!$arr2["country"]) {
            $usercountry = "unknown";
        } else {
            $arr4 = DB::select('countries', 'name,flagpic', ['id' =>$arr2['country']], 'LIMIT 1');
            $usercountry = $arr4["name"];
        }
        $title = format_comment($arr2["title"]);
        $donated = $arr2['donated'];
        $by = "<a href='" . URLROOT . "/profile?id=$userid'>$postername</a>" . ($donated > 0 ? "<i class='fa fa-star' aria-hidden='true' style='color:orange' title='Donated'></i>" : "") . "";
        //hide stats, but not from staff
        if ($privacylevel == "strong" && Users::get("control_panel") != "yes") {
            $useruploaded = "---";
            $userdownloaded = "---";
            $userratio = "---";
            $nposts = "-";
            $tposts = "-";
        }
    }
    if (!$avatar) {
        $avatar = URLROOT . "/assets/images/misc/default_avatar.png";
    }

    $details = [
        'by' => $by,
        'title' => $title,
        'privacylevel' => $privacylevel,
        'usersignature' => $usersignature,
        'userdownloaded' => $userdownloaded,
        'useruploaded' => $useruploaded,
        'avatar' => $avatar,
        'nposts' => $nposts,
        'tposts' => $tposts,
        'forumposts' => $forumposts,
        'userratio' => $userratio,
        'usercountry' => $usercountry,
        'quotename' => $quotename
    ];

    return $details;
}