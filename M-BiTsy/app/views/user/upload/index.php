<div class="ttform">
	<p class='text-center'><b><?php echo stripslashes("Upload Rules"); ?></b><br>
	<b><?php echo stripslashes(Config::get('UPLOADRULES')); ?></b></p>
</div><br>

<form name="upload" enctype="multipart/form-data" action="<?php echo URLROOT; ?>/upload/submit" method="post">
<input type="hidden" name="takeupload" value="yes" />
<div class="ttform"> <?php

echo Lang::T("ANNOUNCE_URL"); ?>: <?php
foreach($data['announce_urls'] as $key => $value) { ?>
    <b><?php echo $value; ?></b><br /> <?php
}
if (Config::get('ALLOWEXTERNAL')) { ?>
    <br /><p class="text-center"><b><?php echo Lang::T("THIS_SITE_ACCEPTS_EXTERNAL"); ?></b></p> <?php
} ?>

<div class="mb-6 row">
  <label for="torrent" class="col-sm-2 col-form-label"><?php echo Lang::T("TORRENT_FILE"); ?>:</label>
  <div class="col-sm-6">
  <input class="form-control" type="file" name="torrent" value='<?php echo $_FILES['torrent']['name']; ?>'>
  </div>
</div><br>

<div class="mb-6 row">
    <label for="nfo" class="col-sm-2 col-form-label"><?php echo Lang::T("NFO"); ?>:</label>
    <div class="col-sm-6">
    <input class="form-control" type="file" name="nfo" value='<?php echo $_FILES['nfo']['name']; ?>'>
    </div>
</div><br>

<div class="mb-6 row">
    <label for="name" class="col-sm-2 col-form-label"><?php echo Lang::T("TORRENT_NAME"); ?>:</label>
    <div class="col-sm-6">
	<input class="form-control" type='text' name='name'><?php echo Lang::T("THIS_WILL_BE_TAKEN_TORRENT"); ?>
    </div>
</div><br>

<div class="mb-6 row">
    <label for="tmdb" class="col-sm-2 col-form-label"><a href="https://www.themoviedb.org/" target='_blank'><img border='0' src='assets/images/misc/tmdb.png' width='30' height='30' title='Click here to go to TMDB'></a></label>
    <div class="col-sm-8">
	<input class="form-control" type='text' name='tmdb'>Example https://www.themoviedb.org/movie/399566-godzilla-vs-kong
    </div>
</div><br>

<div class="mb-6 row">
    <label for="imdb" class="col-sm-2 col-form-label"><a href="https://www.imdb.com/?ref_=nv_home" target='_blank'><i class="fa fa-imdb" width='30' height='30' aria-hidden="true"></i></a></label>
    <div class="col-sm-8">
	<input class="form-control" type='text' name='imdb'>Example https://www.imdb.com/title/tt5034838/
    </div>
</div><br><?php ?>

 
<?php
if (Config::get('YOU_TUBE')) { ?>
<div class="mb-6 row">
	<label for="tube" class="col-sm-2 col-form-label"><a href=\"http://www.youtube.com\" target='_blank'><img border='0' src='assets/images/misc/youtube.png' width='30' height='30' title='Click here to go to Youtube'></a></label>
	<div class="col-sm-8">
	<input class="form-control" type='text' name='tube'>Example https://www.youtube.com/watch?v=aYzVrjB-CWs
	</div>
</div><br><?php
} ?>

<div class="mb-6 row">
    <label for="image0" class="col-sm-2 col-form-label"><?php echo Lang::T("IMAGE"); ?> 1:</label>
    <div class="col-sm-6">
    <input class="form-control" type="file" name="image0" value='<?php echo $_FILES['nfo']['name']; ?>'>
    </div>
</div><br>

<div class="mb-6 row">
    <label for="image1" class="col-sm-2 col-form-label"><?php echo Lang::T("IMAGE"); ?> 2:</label>
    <div class="col-sm-6">
    <input class="form-control" type="file" name="image1"> <?php
    echo Lang::T("MAX_FILE_SIZE"); ?>: <?php echo mksize(IMAGEMAXFILESIZE);?><br><?php
    echo Lang::T("ACCEPTED_FORMATS"); ?>: <?php echo implode(", ", array_unique(ALLOWEDIMAGETYPES)); ?>
    </div>
</div><br><?php

$category = "<select name=\"type\">\n<option value=\"0\">" . Lang::T("CHOOSE_ONE") . "</option>\n";
$cats = Catagories::genrelist();
foreach ($cats as $row) {
    $category .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["parent_cat"]) . ": " . htmlspecialchars($row["name"]) . "</option>\n";
}
$category .= "</select>\n"; ?>
<div class="mb-6 row">
    <label for="type" class="col-sm-2"><?php echo Lang::T("CATEGORY") ?></label>
    <div class="col-sm-6">
    <?php echo $category ?>
    </div>
</div><br><?php

// Tags Mod
$stmt = Tags::getAll(); ?>
<div class="mb-6 row">
    <label for="tag" class="col-sm-2"><?php echo Lang::T("Tags") ?></label>
    <div class="col-sm-6">
    <select class="form-select" id="validationTags" name="tags[]" multiple data-allow-clear="true" data-suggestions-threshold="0">
        <option selected disabled hidden value="">Choose a tag...</option> <?php
        foreach ($stmt as $tag) {
          echo "<option value=" . $tag['name'] . ">" . $tag['name'] . "</option>";
        }?>
    </select>
    </div>
</div><br> <?php

$language = Torrentlang::select(); ?>
<div class="mb-6 row">
    <label for="type" class="col-sm-2"><?php echo Lang::T("LANGUAGE") ?></label>
    <div class="col-sm-6">
    <?php echo  $language ?>
    </div>
</div><br><?php

if (Users::get("class") > _VIP) { ?>
<div class="mb-6 row">
	<label for="free" class="col-sm-2 form-check-label">Freeleech:</label>
	<div class="col-sm-7">
	<input class="form-check-input" type='checkbox' name='free' value=1>&nbsp;Check this box if you want the torrent freeleech.
	</div>
</div><br><?php
}

if (Users::get("class") > _VIP) { ?>
<div class="mb-6 row">
	<label for="vip" class="col-sm-2 form-check-label">VIP:</label>
	<div class="col-sm-7">
	<input class="form-check-input" type='checkbox' name='vip'  value='yes'>&nbsp;Check this box if you want the torrent VIP only.
	</div>
</div> <br><?php
}

if (Config::get('ANONYMOUSUPLOAD')) { ?>
<div class="mb-6 row">
      <label for="anonycheck" class="col-sm-2 form-check-label">Anon :</label>
      <div class="col-sm-7">
	  <input class="form-check-input" type="checkbox" name="anonycheck" value='yes'>&nbsp;<?php echo Lang::T("UPLOAD_ANONY"); ?>
      </div>
</div><br><?php
} ?>

</div>

<p class="text-center"><?php echo Lang::T("DESCRIPTION"); ?></p><?php
print textbbcode("upload", "descr", "$descr"); ?><br>
<p class="text-center"><input type="submit" class="btn btn-sm ttbtn" value="<?php echo Lang::T("UPLOAD_TORRENT"); ?>" /><br />
<i><?php echo Lang::T("CLICK_ONCE_IMAGE"); ?></i>
</p>
</form>