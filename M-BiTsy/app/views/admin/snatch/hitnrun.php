<?php
print("<div style='margin-top:4px; margin-bottom:4px' align='center'><font size=2>We have <font color=red><b>$data[count]</b></font> User" . ($data['count'] > 1 ? "s" : "") . " with Hit and Run</font></div>");

if ($data['res']->rowCount() != 0) {
    ?>
   <form id="snatched" method="post" action="<?php echo URLROOT; ?>/Adminsnatch">
   <input type="hidden" name="do" value="delete" />
   
   <div><table class="table table-striped table-bordered table-hover">
       <thead><tr>
       <th><b>User</b></th>
       <th><b>Torrent</b></th>
       <th><b>Uploaded</b></th>
       <th><b>Downloaded</b></th>
       <th><b>Seed&nbsp;Time</b></th>
       <th><b>Started</b></th>
       <th><b><b>Last&nbsp;Action</b></th>
       <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id)" /></th>
       </tr></thead><tbody> <?php
    while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)) {
        
        if ($row1['username']) {
            print '<tr><td><a href="' . URLROOT . '/profile?id=' . $row['uid'] . '"><b>' . Users::coloredname($row['uid']) . '</b></a></td>';
        } else {
            print '<tr><td>' . $row['ip'] . '</td>';
        }
        $result2 = DB::raw('torrents', 'name', ['id'=>$row['tid']]);
        while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
            $smallname = substr(htmlspecialchars($row2["name"]), 0, 35);
            if ($smallname != htmlspecialchars($row2["name"])) {$smallname .= '...';}
            $stime = TimeDate::mkprettytime($row['last_time']);
            $startdate = TimeDate::utc_to_tz(TimeDate::get_date_time($row['start_time']));
            $lastaction = TimeDate::utc_to_tz(TimeDate::get_date_time($row['upload_time']));
            print '<td><a href="' . $config['SITEURL'] . '/torrent?id=' . $row['tid'] . '">' . $smallname . '</td>';
            print '<td><font color=limegreen>' . mksize($row['upload']) . '</font></td>';
            print '<td><font color=red>' . mksize($row['download']) . '</font></td>';
            print '<td>' . (is_null($stime) ? '0' : $stime) . '</td>';
            print '<td>' . date('d.M.Y H:i', TimeDate::sql_timestamp_to_unix_timestamp($startdate)) . '</td>';
            print '<td>' . date('d.M.Y H:i', TimeDate::sql_timestamp_to_unix_timestamp($lastaction)) . '</td>';
            print '<td><input type=checkbox name=ids[] value=' . mksize($row['sid']) . '/></td>';
        }
    }
    print '</tr></tbody></table></div><br>';
    echo "<center><input type='submit' value='Delete' /></center>";
    print("$data[pagerbuttons]");
} else {
        print '<b><center>No recordings of Hit and Run</center></b>';
}