<?php
torrentmenu($data['id']);
?>
<div class='table-responsive'>
<table class='table table-striped'><thead><tr>
    <th> <b><?php echo Lang::T("USERNAME"); ?></b> | <b><?php echo Lang::T("RATIO"); ?></b> </th>
    <th> <b><?php echo Lang::T("STARTED"); ?></b> </th>
    <th> <b><?php echo Lang::T("COMPLETED"); ?></b> </th>
    <th> <b><?php echo Lang::T("LAST_ACTION"); ?></b> </th>
    <th> <b><?php echo Lang::T("UPLOADED"); ?></b> </th>
    <th> <b><?php echo Lang::T("DOWNLOADED"); ?></b> </th>
    <th> <b><?php echo Lang::T("RATIO"); ?></b> </th>
    <th> <b><?php echo Lang::T("SEED_TIME"); ?></b> </th>
    <th> <b><?php echo Lang::T("SEEDING"); ?></b> </th>
    <th> <font color="#FF1200"><b>H</b><small>&</small><b>R</b></font> </th>
</tr></thead><tbody>
<?php
while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)) {

    if (($row["privacy"] == "strong") && (Users::get("edit_users") == "no")) {
    continue;
    }

    $userratio = $row["downloaded"] > 0 ? number_format($row["uploaded"] / $row["downloaded"], 1) : "---";
    
    $comdate = date("d.M.Y<\\b\\r><\\s\\m\\a\\l\\l>H:i</\\s\\m\\a\\l\\l>", TimeDate::utc_to_tz_time($row["date"]));
    $peers = (get_row_count("peers", "WHERE torrent = '$id' AND userid = '$row[id]'")) ? "<font color='#27B500'><b>".Lang::T("YES")."</b></font>" : "<font color='#FF1200'><b>".Lang::T("NO")."</b></font>";
    // todo move sql
    $res2 =  DB::raw('snatched', 'uload, dload, stime, utime, ltime, hnr', ['tid'=>$id,'uid'=>$row['id']]);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $tratio = $row2['dload'] > 0 ? number_format($row2['uload'] / $row2['dload'], 1) : "---";

    $startdate = TimeDate::utc_to_tz(TimeDate::get_date_time($row2['stime']));
    $lastaction = TimeDate::utc_to_tz(TimeDate::get_date_time($row2['utime']));
    $upload = "<font color='#27B500'><b>".mksize($row2["uload"])."</b></font>";
    $download = "<font color='#FF1200'><b>".mksize($row2["dload"])."</b></font>";
    $seedtime = $row2['ltime'] ? TimeDate::mkprettytime($row2['ltime']) : '---';
    if ($row2['hnr'] != "yes") {
        $hnr = "<font color='#27B500'><b>".Lang::T("NO")."</b></font>";
    } else {
        $hnr = "<font color='#FF1200'><b>".Lang::T("YES")."</b></font>";
    } ?>

    <tr>
    <td><a href="<?php URLROOT ?>/profile?id=<?php echo $row["id"]; ?>"><b><?php echo $row["username"]; ?></b></a> | <b><?php echo get_ratio_color($userratio); ?></b></td>
    <td><?php echo date('d.M.Y<\\b\\r>H:i', TimeDate::sql_timestamp_to_unix_timestamp($startdate));?></td>
    <td><?php echo $comdate; ?></td>
    <td><?php echo date('d.M.Y<\\b\\r>H:i', TimeDate::sql_timestamp_to_unix_timestamp($lastaction));?></td>
    <td><?php echo $upload; ?></td>
    <td><?php echo $download; ?></td>
    <td><b><?php echo $tratio; ?></b></td>
    <td><?php echo $seedtime; ?></td>
    <td><?php echo $peers; ?></td>
    <td><b><?php echo $hnr; ?></b></td> <?php
} ?>
</tr></tbody>
</table>
</div>
<div class="text-center">
    <a href="<?php echo URLROOT; ?>/torrent?id=<?php echo $data['id']; ?>" class='btn btn-sm ttbtn'><?php echo Lang::T("BACK_TO_DETAILS"); ?></a>
</div>