<?php usermenu($data['id']); ?>
<div class="ttform">
<form action='<?php echo URLROOT ?>/avatar?id=<?php echo $data['id'] ?>' method='post' enctype='multipart/form-data'>
    <div class="text-center">
        <b><?php echo Lang::T("AVATAR_UPLOAD") ?>:</b> &nbsp;<br><br>
        <input type='file' name='upfile'><br><br>
        <button type='submit' class='btn btn-sm ttbtn' value='<?php echo Lang::T("SUBMIT") ?>'>Submit</button>
    </div>
</form>
</div>