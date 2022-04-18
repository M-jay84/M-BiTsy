<?php
forumheader('Compose New Thread');
$forumname = stripslashes($data["name"]); ?>
<p class='text-center'><?php echo Lang::T("FORUM_NEW_TOPIC") ?>  <a href='<?php echo URLROOT ?>/forum/view&amp;forumid=<?php echo $data['id'] ?>'><?php echo $forumname ?></a></p>
    
<div class=table>
    <form name='Form' method='post' action='<?php echo URLROOT ?>/topic/submit' enctype='multipart/form-data'>
    <div>
        <p class='text-center'><strong>Subject:</strong>&nbsp;<input type='text' size='30%' maxlength='200' name='subject' /></p>
        <input type='hidden' name='forumid' value='<?php echo $data['id'] ?>' />
        <?php textbbcode("Form", "body"); ?>
        <p class='text-center'><input type="file" name="upfile[]" multiple></p>
        <p class='text-center'><button type='submit' class='btn btn-sm ttbtn'><?php echo Lang::T("SUBMIT") ?></button></p>
    </div>
    </form>
</div> <?php
insert_quick_jump_menu();