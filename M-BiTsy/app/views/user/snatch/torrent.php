<?php
torrentmenu($data['tid']);
?>
<div class='table-responsive'> <table class='table table-striped'><thead><tr>
<th><?php echo Lang::T("USERNAME"); ?></th>
<th><?php echo Lang::T("UPLOADED"); ?></th>
<th><?php echo Lang::T("DOWNLOADED"); ?></th>
<th><?php echo Lang::T("RATIO"); ?></th>
<th><?php echo Lang::T("ADDED"); ?></th>
<th><?php echo Lang::T("LAST_ACTION"); ?></th>
<th><?php echo Lang::T("SEED_TIME"); ?></th>
<th><i class='fa fa-check tticon' title='<?php echo Lang::T("COMPLETED"); ?>'></i></th>
<th><i class='fa fa-upload tticon' title='<?php echo Lang::T("SEEDING"); ?>'></i></th>
<th><?php echo Lang::T("HNR"); ?></th>
</tr></thead><tbody>
<?php
while ($row = $data['res']->fetch(PDO::FETCH_LAZY)):

	$userratio = $row[6] > 0 ? number_format($row[5] / $row[6], 1) : "---";
    $startdate = TimeDate::utc_to_tz(TimeDate::get_date_time($row[7]));
    $lastaction = TimeDate::utc_to_tz(TimeDate::get_date_time($row[8]));
    if ($row[11] != "yes") {$hnr = "<font color=#27B500><b>" . Lang::T("NO") . "</b></font>";} else { $hnr = "<font color=#FF1200><b>" . Lang::T("YES") . "</b></font>";}
    if ($row[12] != "yes") {$seed = "<font color=#FF1200><b>" . Lang::T("NO") . "</b></font>";} else { $seed = "<font color=#27B500><b>" . Lang::T("YES") . "</b></font>";}
    ?>
	<tr>
	<td><a href="<?php echo URLROOT ?>/profile?id=<?php echo $row[0]; ?>"><?php echo "<b>" . Users::coloredname($row[0]) . "</b>"; ?></a></td>
	<td><font color="#27B500"><?php echo mksize($row[5]); ?></font></td>
	<td><font color="#FF1200"><?php echo mksize($row[6]); ?></font></td>
	<td><?php echo get_ratio_color($userratio); ?></td>
	<td><?php echo date('d.M.Y H:i', TimeDate::sql_timestamp_to_unix_timestamp($startdate)); ?></td>
	<td><?php echo date('d.M.Y H:i', TimeDate::sql_timestamp_to_unix_timestamp($lastaction)); ?></td>
	<td><?php echo ($row[9]) ? TimeDate::mkprettytime($row[9]) : '---'; ?></td>
	<td><?php echo ($row[10]) ? "<font color=#0080FF><b>" . Lang::T("YES") . "</b></font>" : "<b>" . Lang::T("NO") . "</b>"; ?></td>
	<td><?php echo $seed; ?></td>
	<td><?php echo $hnr; ?></td>
	</tr>
	<?php
endwhile;
?>
</tbody></table></div>
<?php
//if ($count_tid > $perpage) {echo ($pagerbuttons);}
print("<div class='text-center'><a href=" . URLROOT . "/torrent?id=$data[tid]><b><input type='submit' class='btn btn-sm ttbtn' value='" . Lang::T("BACK_TO_TORRENT") . "'></b></a></div>");
