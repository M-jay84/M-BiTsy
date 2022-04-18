<p class=text-center>
    <a href='<?php echo URLROOT ?>/nfo/edit?id=<?php echo $data['id'] ?>' class="btn ttbtn"><?php echo Lang::T("NFO_EDIT") ?></a>
    <a href="<?php echo URLROOT; ?>/nfo/delete?id=<?php echo $data['id']; ?>" class="btn ttbtn">Delete NFO</a>
    <a href="<?php echo URLROOT; ?>/nfo?id=<?php echo $data['id']; ?>" class="btn ttbtn">View</a>
    </p>
<div class="row justify-content-md-center">
    <div class="col-md-8">

<form method="post" action="<?php echo URLROOT; ?>/nfo/submit">
    <input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
    <input type="hidden" name="do" value="update" />
    <textarea class="nfo" name="content" cols="100%" rows="30"><?php echo stripslashes($data['nfo']); ?></textarea><br />
    <center>
    <input type="reset"  class='btn btn-sm ttbtn' value="<?php echo Lang::T("RESET"); ?>" />
    <button type='submit' class='btn btn-sm ttbtn'><?php echo Lang::T("SAVE"); ?></button>
    </center>
</form><br>

    <div class="row justify-content-md-center">
    <div class="col-8 border ttborder">
<form method="post" action="<?php echo URLROOT; ?>/nfo/delete">
    <input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
    <input type="hidden" name="do" value="delete" />
    <center><b>Delete NFO</b></center>
    <center>
    <b><?php echo Lang::T("NFO_REASON"); ?>:</b> <input type="text" name="reason" size="40" />
    <button type='submit' class='btn btn-sm ttbtn'><?php echo Lang::T("DEL"); ?></button><br>
    </center><br>
</form>
    </div>
    </div>

    </div>
</div>