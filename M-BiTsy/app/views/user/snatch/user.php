<?php
usermenu($data['uid']);
print("<div style='margin-top:20px; margin-bottom:20px' align='center'><font size='2'>" . Lang::T("SNATCHED_MESSAGE") . "</font></div>");
?>
<div class='table-responsive'> <table class='table table-striped'>
<thead><tr>
<th><?php echo Lang::T("TORRENT_NAME"); ?></th> <?php
if (Config::get('ALLOWEXTERNAL')) { ?>
	<th><?php echo Lang::T("T_L_OR_E"); ?></th> <?php
} ?>
<th><?php echo Lang::T("UPLOADED"); ?></th>
<th><?php echo Lang::T("DOWNLOADED"); ?></th>
<th><?php echo Lang::T("RATIO"); ?></th>
<th><?php echo Lang::T("ADDED"); ?></th>
<th><?php echo Lang::T("LAST_ACTION"); ?></th>
<th><?php echo Lang::T("SEED_TIME"); ?></th>
<th><?php echo Lang::T("COMPLETED"); ?></th>
<th><?php echo Lang::T("SEEDING"); ?></th>
<th><?php echo Lang::T("HNR"); ?></th>
</tr></thead><tbody>
<?php
while ($row = $data['res']->fetch(PDO::FETCH_LAZY)):
	$startdate = TimeDate::utc_to_tz(TimeDate::get_date_time($row[4]));
    $lastaction = TimeDate::utc_to_tz(TimeDate::get_date_time($row[5]));
	$query = DB::raw('torrents', 'external, freeleech', ['id' =>$row[0]]);
     $result = $query->fetch();
    if ($result[0] == "yes") {$type = "" . Lang::T("EXTERNAL_TORRENT") . "";} else { $type = "" . Lang::T("LOCAL_TORRENT") . "";}
    if ($result[1] == 1) {$freeleech = "" . Lang::T("FREE") . "";} else { $freeleech = "";}

	$userratio = $row[3] > 0 ? number_format($row[2] / $row[3], 1) : "---";

	if ($row[8] != "yes") {$hnr = "<font color=#27B500><b>" . Lang::T("NO") . "</b></font>";} else { $hnr = "<font color=#FF1200><b>" . Lang::T("YES") . "</b></font>";}
	if ($row[9] != "yes") {$seed = "<font color=#FF1200><b>" . Lang::T("NO") . "</b></font>";} else { $seed = "<font color=#27B500><b>" . Lang::T("YES") . "</b></font>";}
    $maxchar = 30; //===| cut name length
    $smallname = htmlspecialchars(CutName($row[1], $maxchar));
    ?>
	<tr><!-- below was ".(count($expandrows)?" -->
	<?php echo ("<td class='ttable_col1' align='left' nowrap='nowrap'>" . ($expandrows ? "<a href=\"javascript: klappe_torrent('t" . $row['0'] . "')\"><img border=\"0\" src=\"" . URLROOT . "/assets/images/plus.gif\" id=\"pict" . $row['0'] . "\" alt=\"Show/Hide\" class=\"showthecross\" /></a>" : "") . "<a title=\"" . $row["1"] . "\" href=\"/torrent?id=" . $row['0'] . "&amp;hit=1\"><b>$smallname</b></a> $freeleech</td>"); ?>
	<?php
	if (Config::get('ALLOWEXTERNAL')) { ?>
		<td><?php echo $type; ?></td> <?php
	} ?>
	<td><font color="#27B500"><?php echo mksize($row[2]); ?></font></td>
	<td><font color="#FF1200"><?php echo mksize($row[3]); ?></font></td>
	<td><?php echo get_ratio_color($userratio); ?></td>
	<td><?php echo date('d.M.Y H:i', TimeDate::sql_timestamp_to_unix_timestamp($startdate)); ?></td>
	<td><?php echo date('d.M.Y H:i', TimeDate::sql_timestamp_to_unix_timestamp($lastaction)); ?></td>
	<td><?php echo ($row[6]) ? TimeDate::mkprettytime($row[6]) : '---'; ?></td>
	<td><?php echo ($row[7]) ? "<font color=#0080FF><b>" . Lang::T("YES") . "</b></font>" : "<b>" . Lang::T("NO") . "</b>"; ?></td>
	<td><?php echo $seed; ?></td>
	<td><?php echo $hnr; ?></td>
	</tr>
	<?php
endwhile;
?>
</tbody></table></div>
<?php
//if ($count_uid > $perpage) {echo $pagerbuttons;}
if ($data['uid'] != Users::get('id')) {
    print("<div class='text-center'><a href=".URLROOT."/profile?id=$uid><b><input type='submit' class='btn btn-sm ttbtn' value='" . Lang::T("GO_TO_USER_ACCOUNT") . "'></b></a></div>");
}