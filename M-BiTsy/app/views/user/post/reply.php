<?php
foreach ($data['arr'] as $arr) {
    $subject = stripslashes($arr["subject"]); ?>
    <p class='text-center'><?php echo Lang::T("FORUM_REPLY_TOPIC") ?>: <a href='<?php echo URLROOT ?>/topic?topicid=<?php echo $data['topicid'] ?>'><?php echo $subject ?></a></p>
    <p class='text-center'><?php echo Lang::T("FORUM_RULES") ?><br /><?php echo Lang::T("FORUM_RULES2") ?></p>
    <p class='text-center'><b>Compose Message</b></p>
    <div class=table>
        <form name='Form' method='post' action='<?php echo URLROOT ?>/topic/submit' enctype='multipart/form-data'>
        <div>
            <input type='hidden' name='topicid' value='<?php echo $data['topicid'] ?>' />
            <?php textbbcode("Form", "body"); ?>
            <br><center><input type="file" name="upfile[]" multiple></center><br>
            <center><button type='submit' class='btn btn-sm ttbtn'><?php echo Lang::T("SUBMIT") ?></button></center>
        </div>
        </form>
    </div> <?php
}
insert_quick_jump_menu();