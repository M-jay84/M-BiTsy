<div class='ttform'>
<b>Are you sure you would like to report user ?</b><br /><a href='<?php echo URLROOT; ?>/profile?id=<?php echo $data['user']; ?>'><b><?php echo Users::coloredname($data['user']); ?></b></a><br />
<p>Please note, this is <b>not</b> to be used to report leechers, we have scripts in place to deal with them</p>
<b>Reason</b> (required): 
<form method='post' action='<?php echo URLROOT; ?>/report/user'>
<input type='hidden' name='user' value='<?php echo $data['user']; ?>' />
<input class="form-control" type='text' size='100' name='reason' /><br>
<p class='text-center'><input class="btn btn-sm ttbtn" type='submit' value='Confirm' /></p>
</form>
</div>