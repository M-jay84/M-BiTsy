<form method="post" action="<?php echo URLROOT ?>/adminconfig/submit">

<p class='text-center'>To Allow User, add username to _OWNERS setting manually at app/config/settings.php</p>
<p class='text-center'>Like your Settings ? Make a backup before changing <a href='<?php echo URLROOT; ?>/adminconfig/backup ?>'> Here</a></p>

<div class="jumbotron"><br>
<p class="text-center"><b><?php echo Lang::T("Settings"); ?></b></p>
  <div class="row">
    <div class="col-md-2">
    <label for="SITENAME">Site Name:</label><br>
    <input type="text"  class="form-control" id="SITENAME" name="SITENAME" value="<?php echo Config::get('SITENAME') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="_SITEDESC">Site Description:</label><br>
    <input type="text"  class="form-control" id="_SITEDESC" name="_SITEDESC" value="<?php echo Config::get('_SITEDESC') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="SITEEMAIL">Site Email:</label><br>
    <input type="text"  class="form-control" id="SITEEMAIL" name="SITEEMAIL" value="<?php echo Config::get('SITEEMAIL') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="SITENOTICEON">Site Notice on/off:</label><br>
    <?php $checked = Config::get('SITENOTICEON') == true;
    print("<input name='SITENOTICEON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='SITENOTICEON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-4">
    <label for="SITENOTICE">Site Notice:</label><br>
    <input type="text"  class="form-control" id="SITENOTICE" name="SITENOTICE" value="<?php echo Config::get('SITENOTICE') ?>"><br>
    </div>
  </div><br>
    
  <p class="text-center"><b><?php echo Lang::T("Online & Welcome"); ?></b></p>
  <div class="row">
    <div class="col-md-2">
    <label for="OFFLINEMSG">Site Online:</label><br>
    <?php $checked = Config::get('SITE_ONLINE') == 1;
    print("<input name='SITE_ONLINE' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='SITE_ONLINE' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    <label for="WELCOMEPM_ON">Welcome PM On:</label><br>
    <?php $checked = Config::get('WELCOMEPM_ON') == 1;
    print("<input name='WELCOMEPM_ON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='WELCOMEPM_ON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="OFFLINEMSG">Offline Messages:</label><br>
    <input type="text"  class="form-control" id="OFFLINEMSG" name="OFFLINEMSG" value="<?php echo Config::get('OFFLINEMSG') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="WELCOMEPM_MSG">Welcome PM Message:</label><br>
    <textarea  class="form-control" name="WELCOMEPM_MSG" rows="3"><?php echo Config::get('WELCOMEPM_MSG') ?></textarea><br>
    </div>
    <div class="col-md-2">
    <label for="UPLOADRULES">Upload Rules:</label><br>
    <textarea  class="form-control" name="UPLOADRULES" rows="3"><?php echo Config::get('UPLOADRULES') ?></textarea><br>
    </div>
    <div class="col-md-4">
    <label for="TORRENTTABLE_COLUMNS">Torrent Table Columns:</label><br>
    <input type="text"  class="form-control" id="TORRENTTABLE_COLUMNS" name="TORRENTTABLE_COLUMNS" value="<?php echo Config::get('TORRENTTABLE_COLUMNS') ?>"><br>
    </div>
  </div><br>
    
  <p class="text-center"><b><?php echo Lang::T("Capatcha, Defaults"); ?></b></p>
  <div class="row">
    <div class="col-md-2">
    <label for="CAPTCHA_ON">Google Captcha on/off :</label><br>
    <?php $checked = Config::get('CAPTCHA_ON') == 1;
    print("<input name='CAPTCHA_ON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='CAPTCHA_ON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="CAPTCHA_KEY">Captcha Key:</label><br>
    <input type="text"  class="form-control" id="CAPTCHA_KEY" name="CAPTCHA_KEY" value="<?php echo Config::get('CAPTCHA_KEY') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="CAPTCHA_SECRET">Captcha Secret:</label><br>
    <input type="text"  class="form-control" id="CAPTCHA_SECRET" name="CAPTCHA_SECRET" value="<?php echo Config::get('CAPTCHA_SECRET') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="DEFAULTLANG">Default Language:</label><br>
    <input type="text"  class="form-control" id="DEFAULTLANG" name="DEFAULTLANG" value="<?php echo Config::get('DEFAULTLANG') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="DEFAULTTHEME">Default Theme:</label><br>
    <input type="text"  class="form-control" id="DEFAULTTHEME" name="DEFAULTTHEME" value="<?php echo Config::get('DEFAULTTHEME') ?>"><br>
    </div>
  </div><br>

  <p class="text-center"><b><?php echo Lang::T("Optons"); ?></b></p>
  <div class="row">
    <div class="col-md-2">
    <label for="MEMBERSONLY">Members Only:</label><br>
    <?php $checked = Config::get('MEMBERSONLY') == 1;
    print("<input name='MEMBERSONLY' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='MEMBERSONLY' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="MEMBERSONLY_WAIT">Members Wait:</label><br>
    <?php $checked = Config::get('MEMBERSONLY_WAIT') == 1;
    print("<input name='MEMBERSONLY_WAIT' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='MEMBERSONLY_WAIT' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="ALLOWEXTERNAL">Allow External:</label><br>
    <?php $checked = Config::get('ALLOWEXTERNAL') == 1;
    print("<input name='ALLOWEXTERNAL' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ALLOWEXTERNAL' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="UPLOADERSONLY">Uploader Only:</label><br>
    <?php $checked = Config::get('UPLOADERSONLY') == 1;
    print("<input name='UPLOADERSONLY' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='UPLOADERSONLY' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="INVITEONLY">Invite Only:</label><br>
    <?php $checked = Config::get('INVITEONLY') == 1;
    print("<input name='INVITEONLY' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='INVITEONLY' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="ENABLEINVITES">Enable Invites:</label><br>
    <?php $checked = Config::get('ENABLEINVITES') == 1;
    print("<input name='ENABLEINVITES' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ENABLEINVITES' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="CONFIRMEMAIL">Confirm Email:</label><br>
    <?php $checked = Config::get('CONFIRMEMAIL') == 1;
    print("<input name='CONFIRMEMAIL' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='CONFIRMEMAIL' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="ACONFIRM">Admin Confirm:</label><br>
    <?php $checked = Config::get('ACONFIRM') == 1;
    print("<input name='ACONFIRM' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ACONFIRM' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="ANONYMOUSUPLOAD">Anon Uploads:</label><br>
    <?php $checked = Config::get('ANONYMOUSUPLOAD') == 1;
    print("<input name='ANONYMOUSUPLOAD' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ANONYMOUSUPLOAD' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="UPLOADSCRAPE">Upload Scrape:</label><br>
    <?php $checked = Config::get('UPLOADSCRAPE') == 1;
    print("<input name='UPLOADSCRAPE' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='UPLOADSCRAPE' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="FORUMS">Forums on/off:</label><br>
    <?php $checked = Config::get('FORUMS') == 1;
    print("<input name='FORUMS' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORUMS' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="FORUMS_GUESTREAD">Forum Guest Read:</label><br>
    <?php $checked = Config::get('FORUMS_GUESTREAD') == 1;
    print("<input name='FORUMS_GUESTREAD' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORUMS_GUESTREAD' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="OLD_CENSOR">Old Censor:</label><br>
    <?php $checked = Config::get('OLD_CENSOR') == 1;
    print("<input name='OLD_CENSOR' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='OLD_CENSOR' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="FORCETHANKS">Force Thanks:</label><br>
    <?php $checked = Config::get('FORCETHANKS') == 1;
    print("<input name='FORCETHANKS' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORCETHANKS' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="ALLOWLIKES">Allow Likes:</label><br>
    <?php $checked = Config::get('ALLOWLIKES') == 1;
    print("<input name='ALLOWLIKES' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ALLOWLIKES' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="REQUESTSON">Request On:</label><br>
    <?php $checked = Config::get('REQUESTSON') == 1;
    print("<input name='REQUESTSON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='REQUESTSON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
  </div><br>

  <p class="text-center"><b><?php echo Lang::T("More Options"); ?></b></p>
  <div class="row">
    <div class="col-md-2">
    <label for="LEFTNAV">Left Blocks on/off:</label><br>
    <?php $checked = Config::get('LEFTNAV') == 1;
    print("<input name='LEFTNAV' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='LEFTNAV' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="RIGHTNAV">Right Blocks on/off:</label><br>
    <?php $checked = Config::get('RIGHTNAV') == 1;
    print("<input name='RIGHTNAV' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='RIGHTNAV' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="MIDDLENAV">Middle Blocks on/off:</label><br>
    <?php $checked = Config::get('MIDDLENAV') == 1;
    print("<input name='MIDDLENAV' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='MIDDLENAV' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="SHOUTBOX">Shoutbox on/off:</label><br>
    <?php $checked = Config::get('SHOUTBOX') == 1;
    print("<input name='SHOUTBOX' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='SHOUTBOX' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="NEWSON">News on/off:</label><br>
    <?php $checked = Config::get('NEWSON') == 1;
    print("<input name='NEWSON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='NEWSON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="DONATEON">Donate on/off:</label><br>
    <?php $checked = Config::get('DONATEON') == 1;
    print("<input name='DONATEON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='DONATEON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="DISCLAIMERON">Discliamer on/off:</label><br>
    <?php $checked = Config::get('DISCLAIMERON') == 1;
    print("<input name='DISCLAIMERON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='DISCLAIMERON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="FORUMONINDEX">Forum On Index:</label><br>
    <?php $checked = Config::get('FORUMONINDEX') == 1;
    print("<input name='FORUMONINDEX' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORUMONINDEX' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="FORUMONINDEX">Latest Forum Post On Index:</label><br>
    <?php $checked = Config::get('LATESTFORUMPOSTONINDEX') == 1;
    print("<input name='LATESTFORUMPOSTONINDEX' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='LATESTFORUMPOSTONINDEX' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="IPCHECK">IP Check:</label><br>
    <?php $checked = Config::get('IPCHECK') == 1;
    print("<input name='IPCHECK' value='true' type='radio' " . ($checked? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='IPCHECK' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="YOU_TUBE">Youtube:</label><br>
    <?php $checked = Config::get('YOU_TUBE') == 1;
    print("<input name='YOU_TUBE' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='YOU_TUBE' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="FREELEECHGBON">Free Leech On:</label><br>
    <?php $checked = Config::get('FREELEECHGBON') == 1;
    print("<input name='FREELEECHGBON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FREELEECHGBON' value='false' type='radio' " . (!$checked? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="FREELEECHGB">Free Leech GB:</label><br>
    <input type="text"  class="form-control" id="FREELEECHGB" name="FREELEECHGB" value="<?php echo Config::get('FREELEECHGB') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="HIDEBBCODE">Hide BBcode:</label><br>
    <?php $checked = Config::get('HIDEBBCODE') == 1;
    print("<input name='HIDEBBCODE' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='HIDEBBCODE' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
  </div><br>

  <p class="text-center"><b><?php echo Lang::T("Max User & Bonus"); ?></b></p>
  <div class="row">
    <div class="col-md-2">
    <label for="ACCOUNTMAX">Account Max:</label><br>
    <input type="text"  class="form-control" id="ACCOUNTMAX" name="ACCOUNTMAX" value="<?php echo Config::get('ACCOUNTMAX') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="MAXUSERS">Max Users:</label><br>
    <input type="text"  class="form-control" id="MAXUSERS" name="MAXUSERS" value="<?php echo Config::get('MAXUSERS') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="MAXUSERSINVITE">Max Users Invited:</label><br>
    <input type="text"  class="form-control" id="MAXUSERSINVITE" name="MAXUSERSINVITE" value="<?php echo Config::get('MAXUSERSINVITE') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="CURRENCYSYMBOL">Currency Symbol:</label><br>
    <input type="text"  class="form-control" id="CURRENCYSYMBOL" name="CURRENCYSYMBOL" value="<?php echo Config::get('CURRENCYSYMBOL') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="BONUSPERTIME">Bonus Time:</label><br>
    <input type="text"  class="form-control" id="BONUSPERTIME" name="BONUSPERTIME" value="<?php echo Config::get('BONUSPERTIME') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="ADDBONUS">Add Bonus:</label><br>
    <input type="text"  class="form-control" id="ADDBONUS" name="ADDBONUS" value="<?php echo Config::get('ADDBONUS') ?>"><br>
    </div>
  </div><br>

  <p class="text-center"><b><?php echo Lang::T("Cache"); ?></b></p>
  <div class="row">
    <div class="col-md-4">
    <label for="CACHE_TYPE">Cache Type:</label><br>
    <input type="text"  class="form-control" id="CACHE_TYPE" name="CACHE_TYPE" value="<?php echo Config::get('CACHE_TYPE') ?>"><br>
    </div>
    <div class="col-md-4">
    <label for="MEMCACHE_HOST">Memcache Host:</label><br>
    <input type="text"  class="form-control" id="MEMCACHE_HOST" name="MEMCACHE_HOST" value="<?php echo Config::get('MEMCACHE_HOST') ?>"><br>
    </div>
    <div class="col-md-4">
    <label for="MEMCACHE_PORT">Memcache Port:</label><br>
    <input type="text"  class="form-control" id="MEMCACHE_PORT" name="MEMCACHE_PORT" value="<?php echo Config::get('MEMCACHE_PORT') ?>"><br>
    </div>
  </div><br>

  <p class="text-center"><b><?php echo Lang::T("Limits"); ?></b></p>
  <div class="row">
    <div class="col-md-2">
    <label for="PEERLIMIT">Peer Limit:</label><br>
    <input type="text"  class="form-control" id="PEERLIMIT" name="PEERLIMIT" value="<?php echo Config::get('PEERLIMIT') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="AUTOCLEANINTERVAL">Autoclean Interval:</label><br>
    <input type="text"  class="form-control" id="AUTOCLEANINTERVAL" name="AUTOCLEANINTERVAL" value="<?php echo Config::get('AUTOCLEANINTERVAL') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="ANNOUNCEINTERVAL">Announce Interval:</label><br>
    <input type="text"  class="form-control" id="ANNOUNCEINTERVAL" name="ANNOUNCEINTERVAL" value="<?php echo Config::get('ANNOUNCEINTERVAL') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="SIGNUPTIMEOUT">Signup Timeout:</label><br>
    <input type="text"  class="form-control" id="SIGNUPTIMEOUT" name="SIGNUPTIMEOUT" value="<?php echo Config::get('SIGNUPTIMEOUT') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="MAXDEADTORRENTTIMEOUT">Dead Torrent Timeout:</label><br>
    <input type="text"  class="form-control" id="MAXDEADTORRENTTIMEOUT" name="MAXDEADTORRENTTIMEOUT" value="<?php echo Config::get('MAXDEADTORRENTTIMEOUT') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="LOGCLEAN">Log Clean:</label><br>
    <input type="text"  class="form-control" id="LOGCLEAN" name="LOGCLEAN" value="<?php echo Config::get('LOGCLEAN') ?>"><br>
    </div>
  </div><br>

  <p class="text-center"><b><?php echo Lang::T("Email"); ?></b></p>
  <div class="row">
    <div class="col-md-2">
    <label for="mail_type">Mail Type:</label><br>
    <input type="text"  class="form-control" id="mail_type" name="mail_type" value="<?php echo Config::get('mail_type') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="mail_smtp_host">SMTP Host:</label><br>
    <input type="text"  class="form-control" id="mail_smtp_host" name="mail_smtp_host" value="<?php echo Config::get('mail_smtp_host') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="mail_smtp_port">SMTP Port:</label><br>
    <input type="text"  class="form-control" id="mail_smtp_port" name="mail_smtp_port" value="<?php echo Config::get('mail_smtp_port') ?>"><br>
    </div>
    <div class="col-md-1">
    <label for="mail_smtp_ssl">SMTP SSL:</label><br>
    <?php $checked = Config::get('mail_smtp_ssl') == 1;
    print("<input name='mail_smtp_ssl' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='mail_smtp_ssl' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-1">
    <label for="mail_smtp_auth">SMTP Auth:</label><br>
    <?php $checked = Config::get('mail_smtp_auth') == 1;
    print("<input name='mail_smtp_auth' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='mail_smtp_auth' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-2">
    <label for="mail_smtp_user">SMTP User:</label><br>
    <input type="text"  class="form-control" id="mail_smtp_user" name="mail_smtp_user" value="<?php echo Config::get('mail_smtp_user') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="mail_smtp_pass">SMTP Pass:</label><br>
    <input type="text"  class="form-control" id="mail_smtp_pass" name="mail_smtp_pass" value="<?php echo Config::get('mail_smtp_pass') ?>"><br>
    </div>
  </div><br>

  <p class="text-center"><b><?php echo Lang::T("Ratio Warn"); ?></b></p>
  <div class="row">
    <div class="col-md-3">
    <label for="RATIOWARNENABLE">Ratio Warn Enable:</label><br>
    <?php $checked = Config::get('RATIOWARNENABLE') == 1;
    print("<input name='RATIOWARNENABLE' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='RATIOWARNENABLE' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
    </div>
    <div class="col-md-3">
    <label for="RATIOWARNMINRATIO">Warn Min Ration:</label><br>
    <input type="text"  class="form-control" id="RATIOWARNMINRATIO" name="RATIOWARNMINRATIO" value="<?php echo Config::get('RATIOWARNMINRATIO') ?>"><br>
    </div>
    <div class="col-md-3">
    <label for="RATIOWARN_MINGIGS">Warn Min Gigs:</label><br>
    <input type="text"  class="form-control" id="RATIOWARN_MINGIGS" name="RATIOWARN_MINGIGS" value="<?php echo Config::get('RATIOWARN_MINGIGS') ?>"><br>
    </div>
    <div class="col-md-3">
      <label for="RATIOWARN_DAYSTOWARN">Days To Warn:</label><br>
    <input type="text"  class="form-control" id="RATIOWARN_DAYSTOWARN" name="RATIOWARN_DAYSTOWARN" value="<?php echo Config::get('RATIOWARN_DAYSTOWARN') ?>"><br>
    </div>
  </div><br>

  <p class="text-center"><b><?php echo Lang::T("Waiting Times"); ?></b></p>
  <div class="row">
    <div class="col-md-3">
    <label for="CLASS_WAIT">Class Wait:</label><br>
    <input type="text"  class="form-control" id="CLASS_WAIT" name="CLASS_WAIT" value="<?php echo Config::get('CLASS_WAIT') ?>"><br>
    <label for="GIGSA">A Gigs:</label><br>
    <input type="text"  class="form-control" id="GIGSA" name="GIGSA" value="<?php echo Config::get('GIGSA') ?>"><br>
	  <label for="RATIOA">A Ratio:</label><br>
    <input type="text"  class="form-control" id="RATIOA" name="RATIOA" value="<?php echo Config::get('RATIOA') ?>"><br>
    <label for="A_WAIT">A Wait:</label><br>
    <input type="text"  class="form-control" id="A_WAIT" name="A_WAIT" value="<?php echo Config::get('A_WAIT') ?>"><br>
  	</div>
    <div class="col-md-3">
    <label for="GIGSB">B Gigs:</label><br>
    <input type="text"  class="form-control" id="GIGSB" name="GIGSB" value="<?php echo Config::get('GIGSB') ?>"><br>
   	<label for="RATIOB">B Ratio:</label><br>
    <input type="text"  class="form-control" id="RATIOB" name="RATIOB" value="<?php echo Config::get('RATIOB') ?>"><br>
  	<label for="B_WAIT">B Wait:</label><br>
    <input type="text"  class="form-control" id="B_WAIT" name="B_WAIT" value="<?php echo Config::get('B_WAIT') ?>"><br>
    </div>
    <div class="col-md-3">
    <label for="GIGSC">C Gigs:</label><br>
    <input type="text"  class="form-control" id="GIGSC" name="GIGSC" value="<?php echo Config::get('GIGSC') ?>"><br>
  	<label for="RATIOC">C Ratio:</label><br>
    <input type="text"  class="form-control" id="RATIOC" name="RATIOC" value="<?php echo Config::get('RATIOC') ?>"><br>
  	<label for="C_WAIT">C Wait:</label><br>
    <input type="text"  class="form-control" id="C_WAIT" name="C_WAIT" value="<?php echo Config::get('C_WAIT') ?>"><br>
    </div>
    <div class="col-md-3">
    <label for="GIGSD">D Gigs:</label><br>
    <input type="text"  class="form-control" id="GIGSD" name="GIGSD" value="<?php echo Config::get('GIGSD') ?>"><br>
	  <label for="RATIOD">D Ratio:</label><br>
    <input type="text"  class="form-control" id="RATIOD" name="RATIOD" value="<?php echo Config::get('RATIOD') ?>"><br>
  	<label for="D_WAIT">D Wait:</label><br>
    <input type="text"  class="form-control" id="D_WAIT" name="D_WAIT" value="<?php echo Config::get('D_WAIT') ?>"><br>
    </div>
  </div><br>

  <p class="text-center"><b><?php echo Lang::T("Hit & Run"); ?></b></p>
  <div class="row">
    <div class="col-md-2">
    <label for="HNR_ON">Hit & Run on/off:</label><br>
    <?php $checked = Config::get('HNR_ON') == 1;
    print("<input name='HNR_ON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='HNR_ON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>");?>
  	</div>
    <div class="col-md-2">
    <label for="HNR_DEADLINE">Deadline:</label><br>
    <input type="text"  class="form-control" id="HNR_DEADLINE" name="HNR_DEADLINE" value="<?php echo Config::get('HNR_DEADLINE') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="HNR_SEEDTIME">Seed Time:</label><br>
    <input type="text"  class="form-control" id="HNR_SEEDTIME" name="HNR_SEEDTIME" value="<?php echo Config::get('HNR_SEEDTIME') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="HNR_WARN">HNR Warn:</label><br>
    <input type="text"  class="form-control" id="HNR_WARN" name="HNR_WARN" value="<?php echo Config::get('HNR_WARN') ?>"><br>
    </div>
    <div class="col-md-2">
    <label for="HNR_STOP_DL">Ban DL:</label><br>
    <input type="text"  class="form-control" id="HNR_STOP_DL" name="HNR_STOP_DL" value="<?php echo Config::get('HNR_STOP_DL') ?>"><br>
    </div>
    <div class="col-md-2"><label for="HNR_BAN">Ban User:</label><br>
    <input type="text"  class="form-control" id="HNR_BAN" name="HNR_BAN" value="<?php echo Config::get('HNR_BAN') ?>"><br>
    </div>
  </div>
</div>

<center><input type="submit"  class="btn btn-sm ttbtn" value="Submit"></center>

</form>