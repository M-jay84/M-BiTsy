<?php
usermenu($data['id']);
foreach ($data['selectuser'] as $selectedid):
$acceptpms = $selectedid['acceptpms'] == "yes";
?>
<div class="jumbotron">
<form action="<?php echo URLROOT; ?>/profile/submit?id=<?php echo $data['id']; ?>" method="post">
<div class="row">
<div class="col-6"><br>
    <?php 
    $acceptpms = $selectedid['acceptpms'] == "yes";
    print("&nbsp;&nbsp;<b>" . Lang::T("ACCEPT_PMS") . ":</b> <br>&nbsp;<input type='radio' name='acceptpms'" . ($acceptpms ? " checked='checked'" : "") .
    " value='yes' />&nbsp;&nbsp;<b>" . Lang::T("FROM_ALL") . "</b> <input type='radio' name='acceptpms'" .
    ($acceptpms ? "" : " checked='checked'") . " value='no' /><b>" . Lang::T("FROM_STAFF_ONLY") . "</b><br />&nbsp;&nbsp;<i>" . Lang::T("ACCEPTPM_WHICH_USERS") . "</i><br>");
    print("<br>&nbsp;&nbsp;<b>" . Lang::T("ACCOUNT_PRIVACY_LVL") . ":</b> <br>&nbsp;&nbsp;" . Security::priv("normal", "<b>" . Lang::T("NORMAL") . "</b>") . " " . Security::priv("low", "<b>" . Lang::T("LOW") . "</b>") . " " . Security::priv("strong", "<b>" . Lang::T("STRONG") . "</b>") . "<br />&nbsp;&nbsp;<i>" . Lang::T("ACCOUNT_PRIVACY_LVL_MSG") . "</i><br>");
    print("<br>&nbsp;&nbsp;<b>" . Lang::T("EMAIL_NOTIFICATION") . ":</b><br>&nbsp;&nbsp;<input type='checkbox' name='pmnotif' " . (strpos($selectedid['notifs'], "[pm]") !== false ? " checked='checked'" : "") .
    " value='yes' /><b>" . Lang::T("PM_NOTIFY_ME") . "</b><br />&nbsp;&nbsp;<i>" . Lang::T("EMAIL_WHEN_PM") . "</i><br>");
    print("<br>&nbsp;&nbsp;<b>" . Lang::T("THEME") . ":</b><br>&nbsp;&nbsp;<select name='stylesheet'>$data[stylesheets]</select><br>");
    print("<br>&nbsp;&nbsp;<b>" . Lang::T("PREFERRED_CLIENT") . ":</b><br>&nbsp;&nbsp;<input type='text' size='20' maxlength='20' name='client' value=\"" . htmlspecialchars($selectedid['client']) . "\" /><br>");
    print("<br>&nbsp;&nbsp;<b>" . Lang::T("AGE") . ":</b><br>&nbsp;&nbsp;<input type='text' size='3' maxlength='2' name='age' value=\"" . htmlspecialchars($selectedid['age']) . "\" /><br>");
    print("<br>&nbsp;&nbsp;<b>" . Lang::T("GENDER") . ":</b><br> &nbsp;&nbsp;<select size='1' name='gender'>\n$data[gender]\n</select><br>");
    print("<br>&nbsp;&nbsp;<b>" . Lang::T("COUNTRY") . ":</b><br> &nbsp;&nbsp;<select name='country'>\n$data[countries]\n</select><br>");
    print("<br>&nbsp;&nbsp;<b>" . Lang::T("TEAM") . ":</b><br>&nbsp;&nbsp;<select name='teams'>\n$data[teams]\n</select><br>");
   ?>
</div>

<div class="col-6"><br>
    <?php
    print("<b>" . Lang::T("AVATAR_LINK") . ":</b> &nbsp;<input type='text' class='form-control' name='avatar' size='50' value=\"" . htmlspecialchars($selectedid["avatar"]) .
    "\" />&nbsp;&nbsp;\n<a href=" . URLROOT . "/avatar?id=".$data['id']."><b>Or upload file (90px x 90px)</b></a><br />");
    print("<br><b>" . Lang::T("CUSTOM_TITLE") . ":</b><input type='text' class='form-control' name='title' size='50' value=\"" . strip_tags($selectedid["title"]) .
    "\" />&nbsp;&nbsp;\n <i>" . Lang::T("HTML_NOT_ALLOWED") . "</i><br>");
    print("<br><b>" . Lang::T("SIGNATURE") . ":</b><textarea name='signature'  class='form-control' >" . htmlspecialchars($selectedid["signature"]) .
    "</textarea>&nbsp;&nbsp;\n <i>" . sprintf(Lang::T("MAX_CHARS"), 150) . ", " . Lang::T("HTML_NOT_ALLOWED") . "</i>");
    ?>
    <br><br><b><?php echo Lang::T("RESET_PASSKEY"); ?>:</b><br> &nbsp;<input type='checkbox' name='resetpasskey' value='1' />&nbsp;<i><?php echo Lang::T("RESET_PASSKEY_MSG"); ?></i><br>
    <?php
    if (Config::get('SHOUTBOX')) {
        print("<br><b>".Lang::T("HIDE_SHOUT").":</b><br>
        <input type='checkbox' name='hideshoutbox' value='yes' ".($selectedid['hideshoutbox'] == 'yes' ? 'checked="checked"' : '')." />
        &nbsp;".Lang::T("HIDE_SHOUT")."<br> ");
    } ?>
    <br><b><?php echo Lang::T("TIMEZONE"); ?>:</b><select class="form-control" name='tzoffset'' ><?php echo $data['tz']; ?></select>&nbsp;&nbsp;

</div>
</div>
<?php
print("<br><center><button type='submit' class='btn btn-sm ttbtn' value='" . Lang::T("SUBMIT") . "' />Submit</button></center>");
endforeach;
?>
</form>
</div>