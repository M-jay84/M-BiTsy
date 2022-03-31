<center><a href='<?php echo URLROOT; ?>/adminnews/add'><b><?php echo Lang::T("CP_NEWS_ADD_ITEM"); ?></b></a></center><br />
<?php
if ($data['sql']->rowCount() > 0) {
    while ($arr = $data['sql']->fetch(PDO::FETCH_ASSOC)) {
        $newsid = $arr["id"];
        $body = format_comment($arr["body"]);
        $title = $arr["title"];
        $userid = $arr["userid"];
        $added = $arr["added"] . " GMT (" . (TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";
        $arr2 = DB::raw('users', 'username', ['id'=>$userid])->fetch();
        $postername = Users::coloredname($userid);
        if ($postername == "") {
            $by = "Unknown";
        } else {
            $by = "<a href='" . URLROOT . "/profile?id=$userid'><b>$postername</b></a>";
        }
        ?>
        <div class="row">
            <div class="col-4">
            <?php echo $added; ?>&nbsp;---&nbsp;by&nbsp;<?php echo $by; ?>
             - [<a href='<?php echo  URLROOT; ?>/adminnews/edit?newsid=<?php echo $newsid; ?>'><b><?php echo Lang::T("EDIT"); ?></b></a>]
             - [<a href='<?php echo URLROOT; ?>/adminnews/newsdelete?newsid=<?php echo $newsid; ?>'><b><?php echo Lang::T("DEL"); ?></b></a>]
            </div>
            <div class="col-8">
            <b><?php echo $title; ?></b><br /><?php echo $body; ?>
            </div>
        </div>
        <?php
        }
} else {
    echo "No News Posted";
}