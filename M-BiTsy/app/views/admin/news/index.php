<center><a href='<?php echo URLROOT; ?>/adminnews/add'><b><?php echo Lang::T("CP_NEWS_ADD_ITEM"); ?></b></a></center><br />
<?php
if ($data['sql']->rowCount() > 0) {
    while ($arr = $data['sql']->fetch(PDO::FETCH_ASSOC)) {
        $body = format_comment($arr["body"]);
        $added = $arr["added"] . " GMT (" . (TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";
        ?>
        <div class="row">
            <div class="col">
            <?php echo $added; ?>&nbsp;---&nbsp;by&nbsp;<?php echo Users::coloredname($arr["userid"]); ?>
             - [<a href='<?php echo  URLROOT; ?>/adminnews/edit?newsid=<?php echo $arr["id"]; ?>'><b><?php echo Lang::T("EDIT"); ?></b></a>]
             - [<a href='<?php echo URLROOT; ?>/adminnews/newsdelete?newsid=<?php echo $arr["id"]; ?>'><b><?php echo Lang::T("DEL"); ?></b></a>]
            <br>
            <b><?php echo $arr["title"]; ?></b><br /><?php echo $body; ?>
            </div>
        </div>
        <?php
        }
} else {
    echo "No News Posted";
}