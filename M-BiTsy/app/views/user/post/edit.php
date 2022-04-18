<center><b>Edit Message</b></center>
<div>
<form name='Form' method='post' action='<?php echo URLROOT; ?>/post/submit?postid=<?php echo $data['postid']; ?>' enctype='multipart/form-data'>
<input type='hidden' name='page' value='<?php echo  $_GET['page']; ?>' />
<div class='row justify-content-md-center'>
    <div>
        <?php
        textbbcode("Form", "body", $data['body']);
        echo '<p class="text-center"><b>Add attachment</b><br>';
        echo '<input type="file" name="upfile[]" multiple><br>';
        ?>
    </div>
</div>
<center><button type='submit' class='btn btn-sm ttbtn'><?php echo Lang::T("SUBMIT"); ?></button></center>
</form>
</div>