<?php
foreach ($data['selecttor'] as $torr):
torrentmenu($data['id'], $torr['external']); ?>

<div class="jumbotron">
<form method='post' name='bbform' enctype='multipart/form-data' action='submit?id=<?php echo $torr['id']; ?>'>
<input type="hidden" name="id" value="<?php echo $data[$id]; ?>" />
<div class="row">
<div class="col">
    <b><?php echo Lang::T("NAME") ?>: </b><br>
    <input class="form-control" type="text" name="name" value="<?php echo htmlspecialchars($torr["name"])?>"" size="60" /><br><br>
    <b><?php echo Lang::T("IMAGE") ?> 1:</b><br>
    <input type='radio' name='img1action' value='keep' checked='checked' /><?php echo Lang::T("KEEP_IMAGE") ?>&nbsp;&nbsp;
    <input type='radio' name='img1action' value='delete' /><?php echo Lang::T("DELETE_IMAGE") ?>&nbsp;&nbsp;
    <input type='radio' name='img1action' value='update' /><?php echo Lang::T("UPDATE_IMAGE") ?><br />
    <input type='file' name='image0' size='60' /> <br /><br /> <b><?php echo Lang::T("IMAGE") ?> 2:</b>&nbsp;&nbsp;<br>
    <input type='radio' name='img2action' value='keep' checked='checked' /><?php echo Lang::T("KEEP_IMAGE") ?>&nbsp;&nbsp;
    <input type='radio' name='img2action' value='delete' /><?php echo Lang::T("DELETE_IMAGE") ?>&nbsp;&nbsp;
    <input type='radio' name='img2action' value='update' /><?php echo Lang::T("UPDATE_IMAGE") ?><br />
    <input type='file' name='image1' size='60' /><br><br>
    <b><?php echo Lang::T("NFO") ?>: </b><br />
    <input type='radio' name='nfoaction' value='keep' checked='checked' />Keep NFO &nbsp;
    <input type='radio' name='nfoaction' value='delete' />Delete NFO &nbsp;
    <input type='radio' name='nfoaction' value='update' />Update NFO:<?php
    if ($torr["nfo"] == "yes") { ?>
        &nbsp;&nbsp;<a href='<?php echo URLROOT  ?>/nfo?id=<?php echo $torr["id"]  ?>'>[<?php echo Lang::T("VIEW_CURRENT_NFO")  ?>]</a><?php
    } else { ?>
       &nbsp;&nbsp;<font color='#ff0000'><?php echo Lang::T("NO_NFO_UPLOADED")  ?></font><?php
    } ?>
    <br /><input type='file' name='nfofile' size='60'><br><br>
    <b><?php echo Lang::T("CATEGORIES")  ?>: </b><br><?php echo $data['catdrop']  ?><br><br>
    <b><?php echo Lang::T("Tags"); ?>: </b><?php echo $data["tags"]; ?><br> <?php
    $stmt = Tags::getAll(); ?>
    <select class="form-select" id="validationTags" name="tags[]" multiple data-allow-clear="true" data-suggestions-threshold="0">
        <option selected disabled hidden value="">Add All Tags</option> <?php
        foreach ($stmt as $tag) {
          echo "<option value=" . $tag['name'] . ">" . $tag['name'] . "</option>";
        }?>
    </select><br>

    <b><?php echo Lang::T("LANG")  ?>: </b><br>
    <?php echo $data['langdrop'] ?><br><br>
</div>

<div class="col">
    <b><?php echo Lang::T("TMDB") ?></b><br>
    <input class="form-control" type="text" name="tmdb" value="<?php echo htmlspecialchars($torr["tmdb"])  ?>" size="60"><br><br>
    <b><?php echo Lang::T("IMDB") ?></b><br>
    <input class="form-control" type="text" name="imdb" value="<?php echo htmlspecialchars($torr["imdb"])  ?>" size="60"><br><br><?php
if (Config::get('YOU_TUBE')) { ?>
    <b><?php echo Lang::T("VIDEOTUBE") ?>: </b><br>
    <input class="form-control" type='text' name='tube' value='<?php echo htmlspecialchars($torr["tube"]) ?>' size='60' /><br>&nbsp;<i><?php echo Lang::T("FORMAT") ?>: </i> <span style='color:#FF0000'><b>https://www.youtube.com/watch?v=aYzVrjB-CWs</b></span><br><br><?php
}
if (Users::get("edit_torrents") == "yes") { ?>
    <b><?php echo Lang::T("BANNED")  ?>: </b><br>
    <input type='checkbox' name='banned'<?php echo (($torr["banned"] == "yes") ? " checked='checked'" : "") ?> value='yes'> <?php echo Lang::T("BANNED") ?><br /><br><?php
}
if (Users::get("class") >= 5) { ?>
    <b><?php echo Lang::T("STICKY") ?>: </b><br>
    <input type="checkbox" name="sticky"<?php echo (($torr["sticky"] == "yes") ? " checked='checked'" : "")  ?> value="yes" />Set sticky this torrent!<br><br><?php
} ?>
   <b><?php echo Lang::T("VISIBLE")  ?>: </b><br>
   <input type="checkbox" name="visible"<?php echo (($torr["visible"] == "yes") ? " checked='checked'" : "") ?> value="yes"> <?php echo Lang::T("VISIBLEONMAIN") ?><br /><br><?php
if ($torr["external"] != "yes" && Users::get("edit_torrents") == "yes") { ?>
    <b><?php echo Lang::T("FREE_LEECH") ?>: </b><br>
    <input type="checkbox" name="freeleech"<?php echo (($torr["freeleech"] == "1") ? " checked=\"checked\"" : "") ?> value="1"><?php echo Lang::T("FREE_LEECH_MSG") ?><br /><br><?php
}
if ($torr["external"] != "yes" && Users::get("edit_torrents") == "yes") { ?>
    <b>VIP:</b><br>
    <input name=vip type='checkbox'<?php echo (($torr["vip"] == "yes") ? " checked='checked'" : "")  ?> value='yes' /> Check the box to make the torrent VIP only.<br><?php
} 
if (Config::get('ANONYMOUSUPLOAD')) { ?>
    <b><?php echo Lang::T("ANONYMOUS_UPLOAD")  ?>: </b><br>
    <input type="checkbox" name="anon"<?php echo (($torr["anon"] == "yes") ? " checked=\"checked\"" : "")  ?> value="1" /><br>(<?php echo Lang::T("ANONYMOUS_UPLOAD_MSG") ?>)<br /><?php
}  ?>
</div>

</div><br>
    <center><b><?php echo Lang::T("DESCRIPTION"); ?>:</b></center><br>
    <?php print textbbcode("bbform", "descr", htmlspecialchars($torr["descr"])); ?>
    <center><input class="btn btn-sm ttbtn"  type="submit" value='<?php echo Lang::T("SUBMIT"); ?>'>
    <input class="btn btn-sm ttbtn" type='reset' value='<?php echo Lang::T("UNDO"); ?>'></center>
</form>
</div>

<?php
endforeach;