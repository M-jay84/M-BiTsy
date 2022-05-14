<?php
Style::begin(Lang::T("Forum "));
?>
<div class="row">
<div class="col-lg-12">
<?php
$fcid = 0;
while ($forums_arr = $data['mainquery']->fetch(PDO::FETCH_ASSOC)) {

if (Users::get('class') < $forums_arr["minclassread"] && $forums_arr["guest_read"] == "no") {
        continue;
    }
    if ($forums_arr['fcid'] != $fcid ) {
        ?><br>
        <div class="row frame-header">
        <div class="col-md-8">
        <?php echo htmlspecialchars($forums_arr['fcname']); ?>
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
    <?php
    $fcid = $forums_arr['fcid'];
    }
    $forumid = 0 + $forums_arr["id"];
    $forumname = htmlspecialchars($forums_arr["name"]);
    $forumdescription = htmlspecialchars($forums_arr["description"]);

    // Does Forum have Sub-Forum
    $is_sub = DB::all('forum_forums', '*', ['sub'=>$forumid]); // sub forum mod
    if ($is_sub) {
        // Is Sub so lets count topics
        $topic = number_format(get_row_count("forum_topics", "WHERE forumid IN (SELECT id FROM forum_forums WHERE forumid=$forumid)"));
        if ($topic == 0) {
            // No topics so lets get count from sub-forums
            $newest = 0;
            foreach ($is_sub as $subforum) {
               $countall[] = number_format(get_row_count("forum_posts", "WHERE topicid IN (SELECT id FROM forum_topics WHERE forumid=$subforum[id])"));
               $forumidarr[] = $subforum['id'];
               $test = DB::run("SELECT forum_posts.added, forum_topics.forumid
                                FROM forum_topics 
                                INNER JOIN forum_posts 
                                ON forum_topics.id = forum_posts.topicid
                                WHERE forumid =$subforum[id] 
                                ORDER BY added DESC 
                                LIMIT 1")->fetchAll(PDO::FETCH_ASSOC);
                $max = $test[0]['added'];
                
                if ($max > $newest) {
                  $newest = $max;
                  $getforumid = $test[0]['forumid'];
                  $lastpostid = get_forum_last_post($getforumid);
                }
            }
            $postcount = array_sum($countall);
            $topiccount = number_format(get_row_count("forum_topics", "WHERE forumid IN (SELECT id FROM forum_forums WHERE sub=$forumid)"));
        } else {
            // Topics so count them only
            $postcount = number_format(get_row_count("forum_posts", "WHERE topicid IN (SELECT id FROM forum_topics WHERE forumid=$forumid)"));
            $topiccount = number_format(get_row_count("forum_topics", "WHERE forumid = $forumid"));
            $lastpostid = get_forum_last_post($forumid);
        }
    } else {
        // Does not contain sub-forum so count for each forum
        $postcount = number_format(get_row_count("forum_posts", "WHERE topicid IN (SELECT id FROM forum_topics WHERE forumid=$forumid)"));
        $topiccount = number_format(get_row_count("forum_topics", "WHERE forumid = $forumid"));
        $lastpostid = get_forum_last_post($forumid);
    }
    
    // Get last post info in a array return img & lastpost
    $detail = lastpostdetails($lastpostid); ?>

    <div class="row border ttborder">
    <div class="col-md-8">
        <?php echo $detail['img']; ?>&nbsp;
        <a href='<?php echo URLROOT; ?>/forum/view&amp;forumid=<?php echo $forumid; ?>'><b><?php echo $forumname; ?></b></a><br>
        <small>- <?php echo format_comment($forumdescription); ?></small>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $topiccount; ?>
        </div>
        <div class="col-md-1 d-none d-sm-block">
        <?php echo $postcount; ?>
        </div>
        <div class="col-md-2 d-none d-sm-block">
        <?php echo $detail['lastpost']; ?>
    </div>

<div class="col-md-8">
<?php
$testz = DB::all('forum_forums', '*', ['sub'=>$forumid]); // sub forum mod
if ($testz) {
foreach ($testz as $testy) {
    echo "<small>*<a href=".URLROOT."/forum/view&amp;forumid=$testy[id]><b>$testy[name]</b></a></small>&nbsp;&nbsp;";
}
}
?> 
</div>
    </div>
    <?php
} ?>
</div>
</div> <?php
Style::end();