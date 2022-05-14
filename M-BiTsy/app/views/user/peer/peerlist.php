<?php torrentmenu($data['id']); ?><br>
<div class="table-responsive">
<table class='table table-striped table-bordered table-hover'><thead><tr>
<th><?php echo Lang::T("PORT"); ?></th>
<th><?php echo Lang::T("UPLOADED"); ?></th>
<th><?php echo Lang::T("DOWNLOADED"); ?></th>
<th><?php echo Lang::T("RATIO"); ?></th>
<th><?php echo Lang::T("_LEFT_"); ?></th>
<th><?php echo Lang::T("FINISHED_SHORT") . "%"; ?></th>
<th><?php echo Lang::T("SEED"); ?></th>
<th><?php echo Lang::T("CONNECTED_SHORT"); ?></th>
<th><?php echo Lang::T("CLIENT"); ?></th>
<th><?php echo Lang::T("USER_SHORT"); ?></th>
</tr></thead>
<?php
while ($row1 = $data['query']->fetch(PDO::FETCH_ASSOC)) {
    $userratio = $row1["downloaded"] > 0 ? number_format($row1["uploaded"] / $row1["downloaded"], 1) : "---";
    $percentcomp = sprintf("%.2f", 100 * (1 - ($row1["to_go"] / $data["size"])));
    if (Config::get('MEMBERSONLY')) {
        $arr = DB::select('users', 'id, username, privacy', ['id'=>$row1["userid"]]);
        $arr["username"] = "<a href='".URLROOT."/profile?id=$arr[id]'>" . Users::coloredname($arr['id']) . "</a>";
    }
    # With Config::get('MEMBERSONLY') off this will be shown.
    if (!$arr["username"]) {
        $arr["username"] = "Unknown User";
    }
    if ($arr["privacy"] != "strong" || (Users::get("control_panel") == "yes")) {
        print("<tbody><tr><td>" . $row1["port"] . "</td><td>" . mksize($row1["uploaded"]) . "</td><td>" . mksize($row1["downloaded"]) . "</td><td>" . get_ratio_color($userratio) . "</td><td>" . mksize($row1["to_go"]) . "</td><td>" . $percentcomp . "%</td><td>$row1[seeder]</td><td>$row1[connectable]</td><td>" . htmlspecialchars($row1["client"]) . "</td><td>$arr[username]</td></tr>");
    } else {
        print("<tbody><tr><td>" . $row1["port"] . "</td><td>" . mksize($row1["uploaded"]) . "</td><td>" . mksize($row1["downloaded"]) . "</td><td>" . get_ratio_color($userratio) . "</td><td>" . mksize($row1["to_go"]) . "</td><td>" . $percentcomp . "%</td><td>$row1[seeder]</td><td>$row1[connectable]</td><td>" . htmlspecialchars($row1["client"]) . "</td><td>Private</td></tr>");
    }
}
echo "</tbody></table></div>";