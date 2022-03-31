<?php
if ($_SESSION['loggedin']) {
    $avatar = htmlspecialchars(Users::get("avatar"));
    if (!$avatar) {
        $avatar = URLROOT . "/assets/images/misc/default_avatar.png";
    }
    $userdownloaded = mksize(Users::get("downloaded"));
    $useruploaded = mksize(Users::get("uploaded"));
    $privacylevel = Lang::T(Users::get("privacy"));
    $countslot = DB::run("SELECT DISTINCT torrent FROM peers WHERE userid =?  AND seeder=?", [Users::get('id'), 'yes']);
    $maxslotdownload = $countslot->rowCount();
    $slots = number_format(Users::get("maxslots")) . "/" . number_format($maxslotdownload);
    $userratio = Users::get("downloaded") > 0 ? number_format(Users::get("uploaded") / Users::get("downloaded"), 1) : "---";

    Style::block_begin("<a href=". URLROOT ."/profile?id=".Users::get('id').">". Users::coloredname(Users::get('id'))."</b></a>");
    ?>
    <center><img src="<?php echo $avatar; ?>" alt="Avatar" width="170px" height="170px"/></center>
	<ul class="list-group">
		<li class="list-group-item"><?php echo Lang::T("DOWNLOADED"); ?> : <span class="label label-danger pull-right"><?php echo $userdownloaded; ?></span></li>
		<li class="list-group-item"><?php echo Lang::T("UPLOADED"); ?>: <span class="label label-success pull-right"><?php echo $useruploaded; ?></span></li>
		<li class="list-group-item"><?php echo Lang::T("CLASS"); ?>: <div class="pull-right"><?php echo Lang::T(Users::get("level")); ?></div></li>
		<li class="list-group-item"><?php echo Lang::T("ACCOUNT_PRIVACY_LVL"); ?>: <div class="pull-right"><?php echo $privacylevel; ?></div></li>
		<li class="list-group-item"><?php echo Lang::T("Seed Bonus"); ?>: <div class="pull-right"><a href="<?php echo URLROOT; ?>/bonus"><?php echo Users::get('seedbonus'); ?></div></a></span></li>
		<li class="list-group-item"><?php echo Lang::T("RATIO"); ?>: <div class="pull-right"><?php echo get_ratio_color($userratio); ?></div></span></li>
		<li class="list-group-item"><?php echo Lang::T("Available Slots"); ?>: <div class="pull-right"><?php echo $slots; ?></div></span></li>
    </ul>
    <br />
	<div class="text-center">
	<a href='<?php echo URLROOT; ?>/profile?id=<?php echo Users::get("id"); ?>'><button class="btn ttbtn"><?php echo Lang::T("ACCOUNT"); ?></button></a>
	<?php
    if (Users::get("control_panel") == "yes") {?>
		<a href="<?php echo URLROOT; ?>/admincp" class="btn ttbtn"><?php echo Lang::T("STAFFCP"); ?></a>
		<?php
    } ?>
	</div>
    <?php
    Style::block_end();
} elseif (!Config::get('MEMBERSONLY')) {
    Style::block_begin('Login');
    ?>
    <form method="post" action="<?php echo URLROOT ?>/login/submit">
    <div class="justify-content-md-center">
    <input type="hidden" name="csrf_token" value="<?php echo Cookie::csrf_token(); ?>" />
    <font face="verdana" size="1"><b><?php echo Lang::T("USERNAME"); ?>:</b></font>
    <input type="text" class="form-control" name="username" />
	<font face="verdana" size="1"><b><?php echo Lang::T("PASSWORD"); ?>:</b></font>
    <input type="password"  class="form-control"  name="password"  />
       <div class="text-center">
	   <?php (new Captcha)->html(); ?>
	   <button type='submit' class='btn btn-sm ttbtn' value='Login'><?php echo Lang::T("LOGIN"); ?></button>
       </div>
    <p class="text-center">[<a href="<?php echo URLROOT ?>/signup"><?php echo Lang::T("SIGNUP");?></a>] / [<a href="<?php echo URLROOT ?>/recover"><?php echo Lang::T("RECOVER_ACCOUNT");?></a>]
    </div>
    </form> 
    <?php
    Style::block_end();
}