<form method="post" action="<?php echo URLROOT; ?>/invite/submit">
<div class="ttform">
<div class="text-center">
	<label for="email"><?php echo Lang::T("EMAIL_ADDRESS"); ?>:</label>
	<input id="email" type="text" class="form-control" name="email" minlength="7" required autofocus>
</div>
<?php echo Lang::T("EMAIL_ADDRESS_VALID_MSG"); ?><br><br>
<div class="text-center">
	<label for="mess"><?php echo Lang::T("MESSAGE"); ?>:</label><br>
	<textarea name="mess" class="form-control" id="mess" rows="7"></textarea>
</div> 
<div class="text-center">
	<button type="submit" class="btn ttbtn btn-sm"><?php echo Lang::T("SEND_AN_INVITE"); ?></button>
</div>
</div>
</form>