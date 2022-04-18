<?php usermenu($data['id']); ?>
<div class="ttform">
    <form method="post" action="<?php echo URLROOT; ?>/changepass/submit?id=<?php echo $data['id']; ?>">
    <div class="text-center">
	    <label for="name"><?php echo Lang::T("NEW_PASSWORD"); ?>:</label><br>
        <input id="name" type="password" class="form-control" name="chpassword" minlength="3" maxlength="25" required autofocus><br>
    </div>
    <div class="text-center">
        <label for="name"><?php echo Lang::T("REPEAT"); ?>:</label><br>
        <input id="name" type="password" class="form-control" name="passagain" minlength="3" maxlength="25" required autofocus><br>
    </div>
    <div class="text-center">
	    <button type="submit" class="btn ttbtn"><?php echo Lang::T("Submit"); ?></button>
    </div>
    </form>
</div>