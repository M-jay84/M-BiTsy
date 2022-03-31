<?php echo Lang::T("TORRENT_NEED_SEED_MSG"); ?>
<div class='table-responsive'>
<table class='table table-striped'><thead><tr>
<th><?php echo Lang::T("TORRENT_NAME"); ?></th>
<th><?php echo Lang::T("UPLOADER"); ?></th>
<th><?php echo Lang::T("LOCAL_EXTERNAL"); ?></th>
<th><?php echo Lang::T("SIZE"); ?></th>
<th><?php echo Lang::T("SEEDS"); ?></th>
<th><?php echo Lang::T("LEECHERS"); ?></th>
<th><?php echo Lang::T("COMPLETE"); ?></th>
<th><?php echo Lang::T("ADDED"); ?></th>
</tr></thead> <?php

while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)) {
    $type = ($row["external"] == "yes") ? Lang::T("EXTERNAL") : Lang::T("LOCAL");
    if ($row["anon"] == "yes" && (Users::get("edit_torrents") == "no" || Users::get("id") != $row["owner"])) {
        $owner = Lang::T("ANONYMOUS");
    } elseif ($row["username"]) {
        $owner = "<a href='".URLROOT."/profile?id=" . $row["owner"] . "'>" . Users::coloredname($row["owner"]) . "</a>";
    } else {
        $owner = Lang::T("UNKNOWN_USER");
    } ?>
    
    <tbody><tr>
    <td><a href="<?php echo URLROOT ?>/torrent?id=<?php echo $row["id"]; ?>"><?php echo CutName(htmlspecialchars($row["name"]), 40) ?></a></td>
    <td><?php echo $owner; ?></td>
    <td><?php echo $type; ?></td>
    <td><?php echo mksize($row["size"]); ?></td>
    <td><?php echo number_format($row["seeders"]); ?></td>
    <td><?php echo number_format($row["leechers"]); ?></td>
    <td><?php echo number_format($row["times_completed"]); ?></td>
    <td><?php echo TimeDate::utc_to_tz($row["added"]); ?></td>
    </tr></tbody><?php
} ?>
</table>
</div>