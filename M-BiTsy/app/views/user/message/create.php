<?php usermenu(Users::get('id'), 'messages');
include APPROOT.'/views/user/message/messagenavbar.php'; ?><br>
<form name="form" action="<?php echo URLROOT; ?>/message/submit" method="post">
<center> <?php
if ($data['id']) { ?>
    <input type="hidden" name="receiver" value="<?php echo $data['id']; ?>" />
    <label for="receiver">Receiver:</label>&nbsp; <?php echo Users::coloredname($data['id']); ?><br><?php
} else { ?>
    <label for="receiver">Receiver:</label>&nbsp;
    <input type="text" id="search-box" name="receiver" autocomplete="off" placeholder="User Name" />
    <div id="suggesstion-box"></div><br> <?php
} ?>
    <label for="template">Template:</label>&nbsp;
    <select name="template">
    <?php  Messages::echotemplates(); ?>
    </select><br>
    
    <label for="subject">Subject:</label>&nbsp;
    <input type="text" name="subject" size="50" placeholder="Subject" id="subject"><br>
    </center>
    <?php print textbbcode("form", "body");?><br>
<center>
    <button type="submit" class="btn-sm ttbtn" name="Update" value="create">Create</button>&nbsp;
    <label>Save Copy In Outbox</label>
    <input type="checkbox" name="save" checked='Checked'>&nbsp;
    <button type="submit" class="btn btn-sm ttbtn" name="Update" value="draft">Draft</button>
    <button type="submit" class="btn btn-sm ttbtn" name="Update" value="template">Template</button>
    </center>
    </form>
