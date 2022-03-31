<?php
$test = DB::raw('forum_forums', 'sub', ['id'=>$data['forumid']])->fetch(); // sub forum mod
$test1 = DB::raw('forum_forums', 'name,id', ['id'=>$test['sub']])->fetch(); // sub forum mod

forumheader($data['forumname'], $test1['name'], $test1['id']);

$testz = DB::all('forum_forums', '*', ['sub'=>$data['forumid']]); // sub forum mod
if ($testz) { 
?>
<div class="row frame-header">
<div class="col-md-8">
Sub Forums
</div>
<div class="col-md-1 d-none d-sm-block">
    Topics
</div>
<div class="col-md-1 d-none d-sm-block">
    Posts
</div>
<div class="col-md-2 d-none d-sm-block">
    Last post
</div>
</div>
<?php foreach ($testz as $testy) { ?>
<div class="row border ttborder">
    <div class="col-md-8">
    <a href='<?php echo URLROOT ?>/forum/view&amp;forumid=<?php echo $testy['id'] ?>'><b><?php echo $testy['name'] ?></b></a>
    </div>
    <div class="col-md-1 d-none d-sm-block">
        <?php
    $topiccount = number_format(get_row_count("forum_topics", "WHERE forumid = $testy[id]"));
    echo $topiccount;
        ?>
    </div>
    <div class="col-md-1 d-none d-sm-block">
    <?php
    $postcount = number_format(get_row_count("forum_posts", "WHERE topicid IN (SELECT id FROM forum_topics WHERE forumid=$testy[id])"));
    echo $postcount;
        ?>
    </div>
    <div class="col-md-2 d-none d-sm-block">
    <?php
    $lastpostid = get_forum_last_post($testy['id']);
    // Get last post info in a array return img & lastpost
    $detail = lastpostdetails($lastpostid);
    echo $detail['lastpost'];
        ?>
    </div>
</div>
<?php } ?><br><?php

latestforumposts($data['forumid']); // mod

}

if (!$testz) {
    ?>
    <div class="d-flex flex-row-reverse"><a href='<?php echo URLROOT; ?>/topic/add?forumid=<?php echo $data['forumid']; ?>'  class='btn btn-sm ttbtn'>New Post</a></div><br>
    <?php
}
if ($data['topicsres'] > 0) {
    ?>
    <div class="row">
    <div class="col-lg-12">
    <div class="wrapper wrapper-content animated fadeInRight">

    <div class="row frame-header">
    <div class="col-md-1">
    Read
    </div>
    <div class="col-md-4">
    Topic
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Replies
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Views
    </div>
    <div class="col-md-1 d-none d-sm-block">
    Author
    </div>
    <div class="col-md-2 d-none d-sm-block">
    Last Post
    </div>
    <?php
    if (Users::get("edit_forum") == "yes" || Users::get("delete_forum") == "yes") {
        ?>
        <div class="col-md-2 d-none d-sm-block">
        Moderate
        </div>
        <?php
    }
    print("</div>");

    foreach ($data['topicsres'] as $topicarr) {
        $topicid = $topicarr["id"];
        $topic_userid = $topicarr["userid"];
        $locked = $topicarr["locked"] == "yes";
        $moved = $topicarr["moved"] == "yes";
        $sticky = $topicarr["sticky"] == "yes";
        //---- Get reply count
        $res = DB::raw('forum_posts', 'count(*)', ['topicid'=>$topicid]);
        $arr = $res->fetch(PDO::FETCH_LAZY);
        $posts = $arr[0];
        $replies = max(0, $posts - 1);
        //---- Get userID and date of last post
        $res = DB::raw('forum_posts', '*', ['topicid'=>$topicid], 'ORDER BY id DESC LIMIT 1');
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        $lppostid = $arr["id"];
        $lpuserid = (int) $arr["userid"];
        $lpadded = TimeDate::utc_to_tz($arr["added"]);
        //------ Get name of last poster
        if ($lpuserid > 0) {
            $res = DB::raw('users', '*', ['id' =>$lpuserid]);
            if ($res->rowCount() == 1) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $lpusername = "<a href='" . URLROOT . "/profile?id=$lpuserid'>" . Users::coloredname($lpuserid) . "</a>";
            } else {
                $lpusername = "Deluser";
            }
        } else {
            $lpusername = "Deluser";
        }
        //------ Get author
        if ($topic_userid > 0) {
            $res = DB::raw('users', 'username', ['id' =>$topic_userid]);
            if ($res->rowCount() == 1) {
                $arr = $res->fetch(PDO::FETCH_ASSOC);
                $lpauthor = "<a href='" . URLROOT . "/profile?id=$topic_userid'>" . Users::coloredname($topic_userid) . "</a>";
            } else {
                $lpauthor = "Deluser";
            }
        } else {
            $lpauthor = "Deluser";
        }
        // Topic Views
        $viewsq = DB::raw('forum_topics', 'views', ['id' =>$topicid]);
        $viewsa = $viewsq->fetch(PDO::FETCH_LAZY);
        $views = $viewsa[0];
        // End
        //---- Print row
        if ($_SESSION) {
            $r = DB::raw('forum_readposts', 'lastpostread', ['userid' =>Users::get('id'), 'topicid'=>$topicid]);
            $a = $r->fetch(PDO::FETCH_LAZY);
        }
        if ($new = !$a || $lppostid > $a[0]) {
            $img = "<i class='fa fa-file-text tticon-red' title='Read'></i>";
        }
        $topicpic = $new ? "<i class='fa fa-file-text tticon-red' title='Read'></i>" : "<i class='fa fa-file-text' title='Read'></i>";

        $subject = ($sticky ? "<b>" . Lang::T("FORUMS_STICKY") . ": </b>" : "") . "<a href='" . URLROOT . "/topic?topicid=$topicid'><b>" .
        encodehtml(stripslashes($topicarr["subject"])) . "</b></a>$topicpages";
        ?>
        <div class="row border ttborder">
        <div class="col-md-1 d-none d-sm-block">
           <?php echo $topicpic ?> 
        </div>
        <div class="col-md-4">
        <?php echo $subject; ?>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $replies; ?>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $views; ?>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $lpauthor; ?>
        </div>
        <div class="col-md-2">
        <span class='small'>by&nbsp;<?php echo $lpusername; ?><br /><span style='white-space: nowrap'><?php echo $lpadded; ?></span></span>
        </div>
        <?php
    
        if (Users::get("edit_forum") == "yes" || Users::get("delete_forum") == "yes") {
            print("<div class='col-md-2 d-none d-sm-block'>");
            if ($locked) {
                print("<a href='" . URLROOT . "/topic/unlock?forumid=$data[forumid]&topicid=$topicid' title='Unlock'><i class='fa fa-unlock tticon-red' title='UnLock Topic'></i></a>\n");
            } else {
                print("<a href='" . URLROOT . "/topic/lock?forumid=$data[forumid]&topicid=$topicid' title='Lock'><i class='fa fa-lock' title='Lock Topic'></i></a>\n");
            }
            print("<a href='" . URLROOT . "/topic/delete?topicid=$topicid&sure=0' title='Delete'><i class='fa fa-trash-o tticon-red' title='Delete Topic'></i></a>\n");
            if ($sticky) {
                print("<a href='" . URLROOT . "/topic/unsetsticky?forumid=$data[forumid]&topicid=$topicid' title='UnStick'><i class='fa fa-exclamation tticon-red' title='Unstick Topic'></i></a>\n");
            } else {
                print("<a href='" . URLROOT . "/topic/setsticky?forumid=$data[forumid]&topicid=$topicid' title='Stick'><i class='fa fa-exclamation' title='". Lang::T("FORUMS_STICKY") ."'></i>
                </a>\n");
            }
            print("</div>");
        }
        print("</div>");
    }

    print("</div></div></div>");
    print ($data['pagerbuttons']);
} else {
    print("<p align='center'>No topics found</p>\n");
}

print("<table cellspacing='5' cellpadding='0'><tr valign='middle'>\n");
print("<td><i class='fa fa-file-text tticon-red' title='UnRead'></td><td >New posts&nbsp;&nbsp;</td>\n");
print("<td><i class='fa fa-file-text' title='Read'>&nbsp;&nbsp;" .
    "</td><td>No New posts&nbsp;&nbsp;</td>\n");
print("<td><i class='fa fa-lock' title='Lock'></i>&nbsp;&nbsp;
</td><td>" . Lang::T("FORUMS_LOCKED") . " </td></tr></tbody></table>\n");
print("<table cellspacing='0' cellpadding='0'><tr>\n");
print("</tr></table>\n");
insert_quick_jump_menu($data['forumid']);