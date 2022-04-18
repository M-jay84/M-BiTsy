<form method='post' action='<?php echo  URLROOT ?>/admincategorie/delete?id=<?php echo $data['id'] ?>&amp;sure=1'>
<div class="ttform">
<b>Category ID to move all Torrents To: </b>
<input type='text' name='newcat' /> (Cat ID)<br><br>
    <div class="text-center">
        <input type='submit' class="btn btn-sm ttbtn" value='<?php echo Lang::T("SUBMIT") ?>' />
    </div>
</div>
</form>