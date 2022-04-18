<?php
if (Users::get("edit_torrents") == "yes") {
    ?>
    <p class=text-center>
    <a href='<?php echo URLROOT ?>/nfo/edit?id=<?php echo $data['id'] ?>' class="btn ttbtn"><?php echo Lang::T("NFO_EDIT") ?></a>
    <a href="<?php echo URLROOT; ?>/nfo/delete?id=<?php echo $data['id']; ?>" class="btn ttbtn">Delete NFO</a>
    <a href="<?php echo URLROOT; ?>/torrent?id=<?php echo $data['id']; ?>" class="btn ttbtn">Go Back</a>
    </p>
    <?php
} ?>
<textarea class='nfo' style='width:98%;height:100%;' rows='50' cols='20' readonly='readonly'>
<?php echo stripslashes($data['nfo']); ?></textarea>