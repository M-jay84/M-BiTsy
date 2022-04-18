<?php
forumheader('New Topics');
$n = 0;
foreach ($data['res'] as $arr) {
    $topicid = $arr['id'];
    $forumid = $arr['forumid'];
    // Check if post is read
    if ($_SESSION['loggedin'] == true) {
        $a = DB::run("SELECT lastpostread FROM forum_readposts WHERE userid=? AND topicid=?", [Users::get('id'), $topicid])->fetch();
    }
    if ($a && $a['lastpostread'] == $arr['lastpost']) {
        continue;
    }
    // Check access & get forum name // todo move sql
    $a = DB::run("SELECT name, minclassread, guest_read FROM forum_forums WHERE id=$forumid")->fetch();
    if (Users::get("class") < $a['minclassread'] && $a["guest_read"] == "no") {
        continue;
    }
    ++$n;
    if ($n > 25) {
        break;
    }
    $forumname = $a['name'];
    if ($n == 1) {
        ?>
        <div class='table'><table class='table table-striped' >
        <thead>
        <tr><th></th><th align='left'>Topic</th><th align='left' colspan='2'>Forum</th></tr>
        </thead><tbody>
        <?php
    } ?>
    <tr><td valign='middle'>
    <i class='fa fa-file-text tticon-red' title='UnRead'></td>
    <td>
    <a href='<?php echo URLROOT ?>/topic?topicid=<?php echo $topicid ?>&amp;page=last#last'><b><?php echo stripslashes(htmlspecialchars($arr["subject"])) ?></b></a></td>
    <td align='left'><a href='<?php echo URLROOT ?>/forum/view&amp;forumid=<?php echo $forumid ?>'><b><?php echo $forumname ?></b></a></td></tr>
    <?php
}
if ($n > 0) {
    print("</tbody></table></div>\n");
    if ($n > $n) {
        print("<p>More than 25 items found, displaying first 25.</p>\n");
    }
    print("<center><a href='" . URLROOT . "/forum?do=catchup'><b>Mark All Forums Read.</b></a></center>\n");
} else {
    print("<br><center><b>Nothing found</b></center>");
}