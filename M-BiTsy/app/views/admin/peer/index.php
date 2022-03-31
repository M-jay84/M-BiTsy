<p class="text-center">We have <?php echo $data['count1'] ?>peers</p>
<p class="text-center"><a href='<?php echo URLROOT; ?>/admintorrent/dead'>All Dead Torrents</a></p>
<?php echo $data['pagerbuttons'];
if ($data['result']->rowCount() != 0) { ?>
    <table class='table table-striped table-bordered table-hover'><thead><tr>
    <th>User</th>
    <th>Torrent</th>
    <th>IP</th>
    <th>Port</th>
    <th>Upl.</th>
    <th>Downl.</th>
    <th>Peer-ID</th>
    <th>Conn.</th>
    <th>Seeding</th>
    <th>Started</th>
    <th>Last<br />Action</th>
    </tr><thead>
    </tr> <?php
    while ($row = $data['result']->fetch(PDO::FETCH_ASSOC)) {
        if (Config::get('MEMBERSONLY')) {
            $row1 = DB::raw('users', 'id, username', ['id'=>$row['userid']])->fetch();
        }
        if ($row1['username']) {
            print '<tr><td class="table_col1"><a href="' . URLROOT . '/profile?id=' . $row['userid'] . '">' . Users::coloredname($row['userid']) . '</a></td>';
        } else {
            print '<tr><td class="table_col1">' . $row["ip"] . '</td>';
        }
        $result2 = DB::raw('torrents', 'id, name', ['id'=>$row['torrent']]);
        while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
            $smallname = CutName(htmlspecialchars($row2["name"]), 40);
            print '<td class="table_col2"><a href="torrent?id=' . $row['torrent'] . '">' . $smallname . '</a></td>';
            print '<td align="center" class="table_col1">' . $row['ip'] . '</td>';
            print '<td align="center" class="table_col2">' . $row['port'] . '</td>';
            if ($row['uploaded'] < $row['downloaded']) {
                print '<td align="center" class="table_col1"><font class="error">' . mksize($row['uploaded']) . '</font></td>';
            } elseif ($row['uploaded'] == '0') {
                print '<td align="center" class="table_col1">' . mksize($row['uploaded']) . '</td>';
            } else {
                print '<td align="center" class="table_col1"><font color="green">' . mksize($row['uploaded']) . '</font></td>';
            }
            print '<td align="center" class="table_col2">' . mksize($row['downloaded']) . '</td>';
            print '<td align="center" class="table_col1">' . substr($row["peer_id"], 0, 8) . '</td>';
            if ($row['connectable'] == 'yes') {
                print '<td align="center" class="table_col2"><font color="green">' . $row['connectable'] . '</font></td>';
            } else {
                print '<td align="center" class="table_col2"><font class="error">' . $row['connectable'] . '</font></td>';
            }
            if ($row['seeder'] == 'yes') {
                print '<td align="center" class="table_col1"><font color="green">' . $row['seeder'] . '</font></td>';
            } else {
                print '<td align="center" class="table_col1"><font class="error">' . $row['seeder'] . '</font></td>';
            }
                print '<td align="center" class="table_col2">' . TimeDate::utc_to_tz($row['started']) . '</td>';
                print '<td align="center" class="table_col1">' . TimeDate::utc_to_tz($row['last_action']) . '</td>';
                print '</tr>';
        }
    }
    print '</table>';
    print("$data[pagerbuttons]</center>");
} else {
    print '<center><b>No Peers</b></center><br />';
}