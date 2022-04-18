<?php echo $data['pagerbuttons']; ?>
<form id='messagespy' method='post' action='adminmessage/delete'>
<table class='table table-striped table-bordered table-hover'><thead>
<tr><th class='table_head' align='left'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
<th class='table_head' align='left'>Sender</th>
<th class='table_head' align='left'>Receiver</th>
<th class='table_head' align='left'>Text</th><th class='table_head' align='left'>Date</th>
</tr></thead><tbody> <?php
while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
    $msg = format_comment($arr["msg"]);
    $added = TimeDate::utc_to_tz($arr["added"]);
    print("<tr><td class='table_col2'><input type='checkbox' name='del[]' value='$arr[id]' /></td><td align='left' class='table_col1'>".Users::coloredname($arr["sender"])."</td><td align='left' class='table_col2'>".Users::coloredname($arr["receiver"])."</td><td align='left' class='table_col1'>$msg</td><td align='left' class='table_col2'>$added</td></tr>");
} ?>
</tbody></table><br />
<center>
<input type='submit' class='btn btn-sm ttbtn' value='Delete Checked' /> 
<input type='submit' class='btn btn-sm ttbtn' value='Delete All' name='delall' />
</center>
<?php echo $data['pagerbuttons']; ?>