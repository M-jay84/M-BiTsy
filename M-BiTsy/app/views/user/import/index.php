<form name="upload" enctype="multipart/form-data" action="<?php echo URLROOT; ?>/import/submit" method="post">
<input type="hidden" name="takeupload" value="yes" />
<div class="row justify-content-center ttborder">
<div class="col-4">

    <div><b>File List:</b></br><?php
    if (!count($data['files'])) {
       echo Lang::T("NOTHING_TO_SHOW_FILES") .  UPLOADDIR."/import";
    } else {
        foreach ($data['files'] as $f) {
           echo htmlspecialchars($f) . "<br />";
        }
        echo "<br />Total files: " . count($data['files']);
    } ?></div>
    <?php
    $category = "<select name='type'  style='width: 95%' >
                 <option value='0'>(". Lang::T("CHOOSE_ONE") .")</option>". Catagories::dropdown() ."
                 </select><br />";
    print("<br><div>" . Lang::T("CATEGORY") . ": </div><div align='left'>" . $category . "</div>");

    $language = Torrentlang::select();
    print("<br><div>Language: </div><div>" . $language . "</div>");
    
    $anonycheck = '';
    if (Config::get('ANONYMOUSUPLOAD')) {?>
        <div><?php echo Lang::T("UPLOAD_ANONY"); ?>: </div><div><?php printf("<input name='anonycheck' value='yes' type='radio' " . ($anonycheck ? " checked='checked'" : "") . " />Yes <input name='anonycheck' value='no' type='radio' " . (!$anonycheck ? " checked='checked'" : "") . " />No");?> &nbsp;<?php echo Lang::T("UPLOAD_ANONY_MSG"); ?></div>
        <?php
    } ?>
    <br><div><button type="submit" class="btn ttbtn btn-sm"><?php echo Lang::T("UPLOAD"); ?></button></div>
</div>
</div>
</form>