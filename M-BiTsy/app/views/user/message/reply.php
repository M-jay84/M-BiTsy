<?php usermenu(Users::get('id'), 'messages');
 include APPROOT.'/views/user/message/messagenavbar.php'; ?><br>
<form name="form" action="<?php echo URLROOT; ?>/message/submit" method="post">
<input type="hidden" name="receiver" value="<?php echo $data['userid']; ?>" />
<input type="hidden" name="subject" value="<?php echo $data['subject']; ?>" />
 
<div class="row justify-content-md-center">
<div class="col-8 border ttborder"><center>
    <label>To</label>&nbsp;
        <?php echo Users::coloredname($data['userid']); ?><br>
    <label for="template">Template:</label>&nbsp;
        <select name="template">
        <option name='0'>---</option>
         <?php  Messages::echotemplates(); ?>
        </select><br>
    <label>Subject</label>&nbsp;
        <?php echo $data['subject']; ?></center>
</div>
</div><br>
<?php print textbbcode("form", "body");  // , $data['msg'] ?>
<center><button type="submit" class="btn-sm ttbtn" name="Update" value="create">Create</button>&nbsp;
    <label>Save Copy In Outbox</label>
    <input type="checkbox" name="save" checked='Checked'>&nbsp;
</center>
</form>