<?php usermenu(Users::get('id'), 'messages');
include APPROOT.'/views/user/message/messagenavbar.php'; ?>

<form id='messages' method='post' action='<?php echo URLROOT; ?>/message/delete?type=<?php echo $_GET['type']; ?>'>

 <div class='table-responsive'><table class='table table-striped'><thead><tr>
    <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
    <th>Read</th>
    <th>Sender</th>
    <th>Receiver</th>
    <th>Subject</th>
    <th>Date</th></tr></thead>
<?php
while ($arr = $data['mainsql']->fetch(PDO::FETCH_ASSOC)) {
    $msgdetails = Mailbox::msgdetails($_GET['type'], $arr); ?>
    <tbody><tr>
    <td><input type='checkbox' name='del[]' value='<?php echo $arr['id']; ?>' /></td>
    <td><?php echo $msgdetails['3']; ?></td>
    <td><?php echo $msgdetails['0']; ?></td>
    <td><?php echo $msgdetails['1']; ?></td>
    <td><?php echo $msgdetails['2']; ?></td>
    <td><?php echo $msgdetails['4']; ?></td>
<?php } ?>

</tr><tbody></table></div>

<div style="float: left;">
read&nbsp;<i class='fa fa-file-text' title='Read'></i>&nbsp;
unread&nbsp;<i class='fa fa-file-text tticon-red' title='UnRead'></i>
</div>

<center>
<button type="submit" class="btn ttbtn" value='Delete Checked' name='delete' />Delete Checked</button>
<?php if ($_GET['type'] == 'inbox') { ?>
<button type="submit" class="btn ttbtn" value='Read Checked' name='read' />Read Checked</button>
<?php } ?>
</center>
</form>

<?php echo $data['pagerbuttons'];