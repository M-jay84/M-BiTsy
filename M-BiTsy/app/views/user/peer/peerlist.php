<?php torrentmenu($data['id']); ?><br>
<table class='table table-striped table-bordered table-hover'><thead>
<tr>
<th class="table_head"><?php echo Lang::T("PORT"); ?></th>
<th class="table_head"><?php echo Lang::T("UPLOADED"); ?></th>
<th class="table_head"><?php echo Lang::T("DOWNLOADED"); ?></th>
<th class="table_head"><?php echo Lang::T("RATIO"); ?></th>
<th class="table_head"><?php echo Lang::T("_LEFT_"); ?></th>
<th class="table_head"><?php echo Lang::T("FINISHED_SHORT") . "%"; ?></th>
<th class="table_head"><?php echo Lang::T("SEED"); ?></th>
<th class="table_head"><?php echo Lang::T("CONNECTED_SHORT"); ?></th>
<th class="table_head"><?php echo Lang::T("CLIENT"); ?></th>
<th class="table_head"><?php echo Lang::T("USER_SHORT"); ?></th>
</tr></thead><tbody>
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
        print("<tr><td class='table_col2'>" . $row1["port"] . "</td><td class='table_col1'>" . mksize($row1["uploaded"]) . "</td><td class='table_col2'>" . mksize($row1["downloaded"]) . "</td><td class='table_col1'>" . get_ratio_color($userratio) . "</td><td class='table_col2'>" . mksize($row1["to_go"]) . "</td><td class='table_col1'>" . $percentcomp . "%</td><td class='table_col2'>$row1[seeder]</td><td class='table_col1'>$row1[connectable]</td><td class='table_col2'>" . htmlspecialchars($row1["client"]) . "</td><td class='table_col1'>$arr[username]</td></tr>");
    } else {
        print("<tr><td class='table_col2'>" . $row1["port"] . "</td><td class='table_col1'>" . mksize($row1["uploaded"]) . "</td><td class='table_col2'>" . mksize($row1["downloaded"]) . "</td><td class='table_col1'>" . get_ratio_color($userratio) . "</td><td class='table_col2'>" . mksize($row1["to_go"]) . "</td><td class='table_col1'>" . $percentcomp . "%</td><td class='table_col2'>$row1[seeder]</td><td class='table_col1'>$row1[connectable]</td><td class='table_col2'>" . htmlspecialchars($row1["client"]) . "</td><td class='table_col1'>Private</td></tr>");
    }
}
echo "</tbody></table>";