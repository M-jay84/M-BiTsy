<div class="ttform">
<form method="post" action="<?php echo URLROOT; ?>/recover/submit"><br>
<input type="hidden" name="csrf_token" value="<?php echo Cookie::csrf_token() ?>" />
    <p class='text-center'><?php echo Lang::T("USE_FORM_FOR_ACCOUNT_DETAILS"); ?></p>
    <div class="form-group row">
        <label for="name" class="col-form-label col-3"><?php echo Lang::T("EMAIL_ADDRESS"); ?>:</label>
        <div class="col-9">
            <input type="email" id="name" class="form-control" aria-describedby="email">
        </div>
    </div><br>
    <div class="text-center">
        <?php (new Captcha)->html(); ?>
        <button type="submit" class="btn ttbtn"><?php echo Lang::T("Submit"); ?></button>
    </div><br>

    <div class="margin-top20 text-center">
        <a href="<?php echo URLROOT; ?>/login"><?php echo Lang::T("LOGIN"); ?></a> |
        <a href="<?php echo URLROOT; ?>/signup"><?php echo Lang::T("SIGNUP"); ?></a> |
        <a href="<?php echo URLROOT ?>/contact"><?php echo Lang::T("Contact Us"); ?></a>
    </div><br>

</form>
</div>