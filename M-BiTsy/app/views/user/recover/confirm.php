<form method="post" action="<?php echo URLROOT; ?>/recover/ok">
<input type="hidden" name="csrf_token" value="<?php echo Cookie::csrf_token() ?>" />
<input type="hidden" name="secret" value="<?php echo $_GET['secret']; ?>" />
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />

<div class="ttform">
<div class="text-center">
	<label for="name"><?php echo Lang::T("NEW_PASSWORD"); ?>:</label>
    <input id="name" type="text" class="form-control" name="password" minlength="3" maxlength="35" required autofocus>
</div>
<div class="text-center">
	<label for="name"><?php echo Lang::T("REPEAT"); ?>:</label>
    <input id="name" type="text" class="form-control" name="password1" minlength="3" maxlength="35" required autofocus>
</div>
<div class="text-center">
	<button type="submit" class="btn ttbtn"><?php echo Lang::T("Submit"); ?></button>
</div>

</div>
</form>