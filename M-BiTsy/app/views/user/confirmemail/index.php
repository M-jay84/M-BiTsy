<?php usermenu($data['id']); ?>
<div class="ttform">
<form action="<?php echo URLROOT; ?>/confirmemail?id=<?php echo $data['id']; ?>" method="post">
    <div class="text-center">
	    <label for="name"><?php echo Lang::T("ENTER_EMAIL"); ?>:</label><br><br>
        <input id="name" type="email" class="form-control" name="email" minlength="3" maxlength="25" required autofocus><br>
    </div>
    <div class="text-center">
	    <button type="submit" class="btn ttbtn"><?php echo Lang::T("Submit"); ?></button>
    </div>
</form>
</div>