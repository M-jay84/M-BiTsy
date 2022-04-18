<?php
forumheader("<a href='" . URLROOT . "/forum/view&amp;forumid=$data[forumid]'>$data[forum]</a> <b style='font-size:16px; vertical-align:middle'>/</b>$data[title]");
if (!$data['locked'] && $data['maypost']) {
    print("<div align='right'><a href='".URLROOT."/post/reply?topicid=$data[topicid]'><button type='button' class='btn btn-sm ttbtn'><b>Reply</b></button></a></div><br>");
} else {
    print("<div align='right'><b>" . Lang::T("FORUMS_LOCKED") . "</b></div><br>");
}

print($data['pagerbuttons']);
       
$pn = 0;
while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
    ++$pn;
    $added = TimeDate::utc_to_tz($arr["added"]) . "(" . (TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";
    // Get poster details
    $posterdetails = posterdetails($arr['userid']);
    // Hyper Link
    print("<a id='post$arr[id]'></a>");
    $pc = $data['res']->rowCount();
    if ($pn == $pc) {
        print("<a name='last'></a>\n");
        if ($arr['id'] > $data['lpr'] && $_SESSION['loggedin'] == true) {
            DB::update('forum_readposts', ['lastpostread' =>$arr['id']], ['userid' =>Users::get('id'), 'topicid' => $data['topicid']]);
        }
    }

    // Post Top
    ?>
    <div class="row frame-header">
        <div class="col-md-2">
            <?php echo $posterdetails['by']; ?>
        </div>
        <div class="col-md-10">
            Posted at <?php echo $added; ?>
        </div>
    </div> <?php

    // Post Middle
    $body = format_comment($arr["body"]);
    if ($arr['editedby'] != 0) {
        $body .= "<br /><br /><small><i>Last edited by <a href='" . URLROOT . "/profile?id=$arr[editedby]'>$posterdetails[posterdetails]</b></a> on " . TimeDate::utc_to_tz($arr["editedat"]) . "</i></small><br />\n";
    }
    $quote = htmlspecialchars($arr["body"]); ?>
    <div class="row">
        <div class="col-md-2 d-none d-sm-block border ttborder">
            <center><i><?php echo $posterdetails['title']; ?></i></center><br>
            <center><img width='80' height='80' src='<?php echo $posterdetails['avatar'] ?>' alt='' /></center><br>
            Uploaded: <?php echo $posterdetails['useruploaded']; ?><br>
            Downloaded: <?php echo $posterdetails['userdownloaded']; ?><br>
            Posts: <?php echo $posterdetails['forumposts']; ?><br>
            Ratio: <?php echo $posterdetails['userratio']; ?><br>
            Location: <?php echo $posterdetails['usercountry']; ?><br>
        </div>
        <div class="col-md-10 border ttborder"><br>
            <?php echo $body;
            // attachments
            get_attachment($arr['id']);
            if (!$posterdetails['usersignature']) {
                print("<br />\n");
            } else {
               print("<br /><hr /><br /><div class='f-sig' align='center'>$posterdetails[usersignature]</div>\n");
            }
            $stmt = DB::run("SELECT user FROM thanks WHERE thanked = $arr[id] AND type = 'forum'")->fetchAll();
            if ($stmt){
                echo '<br>Thanks By<br>';
            }
            foreach ($stmt as $row) {
                $test = Users::coloredname($row['user']);
                $thankedname = $test == 'System' ? '' : "$test &nbsp;";
                echo $thankedname;
            }
            ?>
        </div>
    </div><?php
    
    // Post Bottom
    if ($_SESSION['loggedin']) { ?>
        <div class="row ttblend">
            <div class="col-md-3 d-none d-sm-block">
                <a href='<?php echo URLROOT; ?>/profile?id=<?php echo $arr["userid"]; ?>'><i class='fa fa-user tticon' title='Profile'></i></a>
                <a href='<?php echo URLROOT; ?>/message/create?id=<?php echo $arr["userid"]; ?>'><i class='fa fa-comment tticon' title='Send PM'></i></a>
                <a href='<?php echo URLROOT; ?>/report/forum?forumid=<?php echo $data['topicid'] ?>&amp;forumpost=<?php echo $arr["id"] ?>'><i class="fa fa-flag tticon" title='<?php echo Lang::T("FORUMS_REPORT_POST") ?>'></i></a>&nbsp;
                <a href='javascript:scroll(0,0);'><i class='fa fa-arrow-up tticon' title='<?php echo Lang::T("FORUMS_GOTO_TOP_PAGE"); ?>'></i></a>
                <a href='<?php echo URLROOT; ?>/topic?topicid=<?php echo $data['topicid']; ?>&amp;page=<?php echo $_GET['page'] ?>#post<?php echo $arr["id"]; ?>'><i class='fa fa-anchor tticon' title='Direct Post Link'></i></a>
            </div>
            <div class="col-md-9 d-none d-sm-block"> <?php
                // Thanks Mod
                if (Users::get("id") !== $arr["userid"] && !$arr['thanked'] || $arr['user'] !== Users::get("id")) {
                    print("<a href='" . URLROOT . "/like/thanks?id=$arr[id]&type=thanksforum'><i class='fa fa-thumbs-up tticon' title='Thanks'></i></a>&nbsp;");
                }
                //define buttons and who can use them
                if (Users::get("id") == $arr["userid"] || Users::get("edit_forum") == "yes" || Users::get("delete_forum") == "yes") {
                    print("<a href='" . URLROOT . "/post/edit?postid=$arr[id]&page=$_GET[page]'><i class='fa fa-pencil tticon' title=" . Lang::T("EDIT") . "></i></a>&nbsp;");
                }
                if (Users::get("delete_forum") == "yes") {
                    print("<a href='" . URLROOT . "/post/delete?postid=$arr[id]&sure=0'><i class='fa fa-trash-o tticon-red' title='Delete'></i></a>&nbsp;");
                }
                if (!$data['locked'] && $data['maypost']) {
                    print("<a href=\"javascript:SmileIT('[quote=$posterdetails[quotename]] $quote [/quote]', 'Form', 'body');\"><i class='fa fa-quote-right tticon' title='". Lang::T("QUOTE") ."'></i></a>&nbsp;");
                    print("<a href='#bottom'><i class='fa fa-reply tticon' title='reply'></i></a>");
               } ?>
            </div>
        </div> <?php
    } ?><br><?php

}

print($data['pagerbuttons']);

//quick reply
if (!$data['locked'] && $_SESSION['loggedin'] == true) {
    print("<p class='text-center'><b>" . Lang::T("FORUMS_POST_REPLY") . "</b></p>");
    print("<a name='bottom'></a>");
    print("<form name='Form' method='post' action='" . URLROOT . "/topic/submit' enctype='multipart/form-data'>\n");
    print("<input type='hidden' name='topicid' value='$data[topicid]' />\n"); 
    //print("<center><input type='text' name='subject' style='border: 0px; height: 19px' /></center>");
    textbbcode("Form", "body");
    echo '<div class="ttform">';
    echo '<p class="text-center"><b>Add attachment</b><br>';
    echo '<input type="file" name="upfile[]" multiple></center><br><br>';
    print("</div>\n");
    print("<p class='text-center'><button class='btn btn-sm ttbtn'>Reply</button></p>");
    print("</form><br>\n");
} else {
    print("" . Lang::T("FORUMS_LOCKED") . "<br />");
}

if ($data['locked']) {
    print(Lang::T("FORUMS_TOPIC_LOCKED") . "<br /><br />\n");
} elseif (!$data['maypost']) {
    print("<i>" . Lang::T("FORUMS_YOU_NOT_PERM_POST_FORUM") . "</i><br /><br />\n");
}

// MODERATOR OPTIONS
if (Users::get("delete_forum") == "yes" || Users::get("edit_forum") == "yes") {
    modoptions($data['topicid'], $data['subject'], $data['forumid'], $data['locked'], $data['sticky']);
}