<?php
if (Config::get('MEMBERSONLY')) { ?>
    <p class='text-center'><b><?php echo Lang::T("MEMBERS_ONLY"); ?></b></p> <?php
} ?>

<div class="ttform">

<form method="post" class="form-horizontal" action="<?php echo URLROOT; ?>/login/submit" autocomplete="off"><br>
    <input type="hidden" name="csrf_token" value="<?php echo Cookie::csrf_token() ?>" />

    <div class="form-group row">
        <label for="username" class="col-form-label col-3"><?php echo Lang::T("USERNAME"); ?>:</label>
        <div class="col-9">
            <input id="username" type="text" class="form-control" name="username" minlength="3" maxlength="25" required autofocus placeholder="">
        </div>
    </div><br>

    <div class="form-group row">
        <label for="password" class="col-form-label col-3"><?php echo Lang::T("PASSWORD"); ?>:</label>
        <div class="col-9">
            <input id="password" type="password" class="form-control" name="password" minlength="6" maxlength="16" required data-eye>
        </div>
    </div><br>

    <div class="text-center">
        <?php (new Captcha)->html(); ?>
        <button type="submit" class="btn ttbtn "><?php echo Lang::T("LOGIN"); ?></button><br><br>
        <p class='text-center'><i><?php echo Lang::T("COOKIES"); ?></i></p>
    </div>

    <div class="margin-top20 text-center">
        <a href="<?php echo URLROOT; ?>/signup"><?php echo Lang::T("SIGNUP"); ?></a> |
        <a href="<?php echo URLROOT; ?>/recover"><?php echo Lang::T("RECOVER_ACCOUNT"); ?></a> |
        <a href="<?php echo URLROOT ?>/contact"><?php echo Lang::T("Contact Us"); ?></a>
    </div><br>
    
</form>

</div>