<?php
echo "<div style='margin-top:10px' align='center'><font size='4'>List of dead torrents without being shared and sorted by date of sharing </font></div>";
echo "<hr class='barre'/><div style='margin-top:16px; margin-bottom:20px' align='center'><font size='4'>We have<font color='IndianRed'><b>$data[count]</b></font> DEAD" . ($data['count'] != 1 ? "s" : "") . " torrent" . ($data['count'] != 1 ? "s" : "") . "</font></div>";
if ($data['count'] > $data['perpage']) {
    print($data['pagerbuttons']);
    print("<br />");
}
?>
<form id="myform" method='post' action='<?php echo URLROOT; ?>/admintorrent/dead.php'>
<input type='hidden' name='do' value='delete' />
<div class='table-responsive'><table class='table table-striped'>
    <thead><tr>
		<th class="table_head" align="left"><?php echo Lang::T("TORRENT_NAME"); ?></th>
		<th class="table_head" align="left"><?php echo Lang::T("UPLOADER"); ?></th>
		<th class="table_head"><?php echo Lang::T("SIZE"); ?></th>
		<th class="table_head"><i class='fa fa-upload tticon' title='<?php echo Lang::T("SEEDING"); ?>'></th>
		<th class="table_head"><i class='fa fa-downloadload tticon' title='<?php echo Lang::T("LEECHERS"); ?>'></i></th>
		<th class="table_head"><i class='fa fa-check tticon' title='<?php echo Lang::T("COMPLETED"); ?>'></i></th>
		<th class="table_head" width="1%"><?php echo Lang::T("ADDED"); ?></th>
		<th class="table_head" width="1%"><?php echo Lang::T("LAST_ACTION"); ?></th>
<?php if (Users::get("class") >= 6) {?>
		<th class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
<?php }?>
	</tr></thead>
<?php
while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)) { ?>
    <tbody>
	<tr>
		<td class="table_col1"><a href="<?php echo URLROOT; ?>/torrent?id=<?php echo $row["id"]; ?>"><?php echo CutName(htmlspecialchars($row["name"]), 50) ?></a></td>
		<td class="table_col2"><?php echo Users::coloredname($row["username"]); ?></td>
		<td class="table_col2" align="center"><?php echo mksize($row["size"]); ?></td>
		<td class="table_col1" align="center"><font color="limegreen"><b><?php echo number_format($row["seeders"]); ?></b></font></td>
		<td class="table_col2" align="center"><font color="red"><b><?php echo number_format($row["leechers"]); ?></b></font></td>
		<td class="table_col1" align="center"><font color="#0080FF"><b><?php echo number_format($row["times_completed"]); ?></b></font></td>
		<td class="table_col2" align="center"><?php echo date("d.M.Y H:i", TimeDate::utc_to_tz_time($row["added"])) ?></td>
		<td class="table_col2" align="center"><?php echo date("d.M.Y H:i", TimeDate::utc_to_tz_time($row["last_action"])) ?></td>
<?php if (Users::get("class") >= 6) {?>
		<td class='table_col2' align='center'><input type='checkbox' name='torrentids[]' value='<?php echo $row["id"]; ?>' /></td>
<?php }?>
    </tr>
<?php
}
?>
</tbody></table></div>
<?php if (Users::get("class") >= 6) {?>
    <button type="submit" class="btn btn-sm ttbtn" value='Remove The Checks '>Delete Checked</button>
<?php }?>
</form>
 <?php
if ($data['count'] > $data['perpage']) {
    print($data['pagerbuttons']);
} else {
    print("<br />");
}