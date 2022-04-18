<div class="ttform">
<form method="post" class="form-horizontal" action="<?php echo URLROOT; ?>/signup/submit">

<input type="hidden" name="csrf_token" value="<?php echo Cookie::csrf_token() ?>" /> <?php
if ($data['invite'] != 0) { ?>
    <input type="hidden" name="invite" value="<?php echo $_GET["invite"]; ?>" />
    <input type="hidden" name="secret" value="<?php echo htmlspecialchars($_GET["secret"]); ?>" />
    <?php
} ?>

<p class="text-center"><?php echo Lang::T("COOKIES"); ?></p>

<div class="form-group row">
    <label for="wantusername" class="col-form-label col-4"><?php echo Lang::T("USERNAME"); ?>:</label>
    <div class="col-8">
        <input id="wantusername" type="text" class="form-control" name="wantusername" minlength="3" maxlength="25" required autofocus>
    </div>
</div><br>

<div class="form-group row">
    <label for="wantpassword" class="col-form-label col-4"><?php echo Lang::T("PASSWORD"); ?>:</label>
    <div class="col-8">
        <input id="wantpassword" type="password" class="form-control" name="wantpassword" minlength="6" maxlength="25" required autofocus>
    </div>
</div><br>

<div class="form-group row">
    <label for="passagain" class="col-form-label col-4"><?php echo Lang::T("CONFIRM"); ?>:</label>
    <div class="col-8">
        <input id="passagain" type="password" class="form-control" name="passagain" minlength="6" maxlength="25" required autofocus>
    </div>
</div><br> <?php

if ($data['invite'] == 0) { ?>
    <div class="form-group row">
        <label for="email" class="col-form-label col-4"><?php echo Lang::T("EMAIL"); ?>:</label>
        <div class="col-8">
            <input id="email" type="email" class="form-control" name="email" minlength="3" maxlength="25" required autofocus>
        </div>
    </div><br> <?php
} ?>

<div class="form-group row">
    <label for="age" class="col-form-label col-4"><?php echo Lang::T("AGE"); ?>:</label>
    <div class="col-8">
        <input id="age" type="text" class="form-control" name="age" minlength="2" required autofocus>
    </div>
</div><br>

<div class="form-group row">
    <label for="country" class="col-form-label col-4"><?php echo Lang::T("COUNTRY"); ?>:</label><br>
    <div class="col-8">
        <select name="country" size="1">
            <?php Countries::echoCountry(); ?>
        </select>
    </div>
</div><br>

<div class="form-group row">
    <label for="gender" class="col-form-label col-4"><?php echo Lang::T("GENDER"); ?>:</label><br>
    <div class="col-8">
        <input class="form-check-input" type="checkbox" name="gender" id="Male" value="Male">
        <label class="form-check-label" for="gender">Male</label>&nbsp;&nbsp;
        <input class="form-check-input" type="checkbox" name="gender" id="Female" value="Female">
        <label class="form-check-label" for="gender">Female</label>
    </div>
</div><br>

<div class="form-group row">
    <label for="client" class="col-form-label col-4"><?php echo Lang::T("PREF_BITTORRENT_CLIENT"); ?>:</label>
    <div class="col-8">
        <input id="client" type="text" class="form-control" name="client" minlength="3" maxlength="25" required autofocus>
    </div>
</div><br>

<div class="text-center">
    <?php (new Captcha)->html(); ?>
    <button type="submit" class="btn ttbtn btn-block"><?php echo Lang::T("SIGNUP"); ?></button>
</div>

<div class="margin-top20 text-center">
    <a href="<?php echo URLROOT; ?>/login"><?php echo Lang::T("LOGIN"); ?></a> |
    <a href="<?php echo URLROOT; ?>/recover"><?php echo Lang::T("RECOVER_ACCOUNT"); ?></a> |
    <a href="<?php echo URLROOT ?>/contact"><?php echo Lang::T("Contact Us"); ?></a>
</div><br>

</form>
</div>