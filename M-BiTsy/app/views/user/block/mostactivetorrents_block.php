<?php
if ($_SESSION['loggedin'] == true) {
    Style::block_begin(Lang::T("MOST_ACTIVE"));
    $TTCache = new Cache();
    $expires = 600; // Cache time in seconds
    if (($rows = $TTCache->Get("block/mostactivetorrents_block", $expires)) === false) {
        $res = DB::run("SELECT id, name, seeders, leechers FROM torrents WHERE banned = 'no' AND visible = 'yes' ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT 10");
        $catsquery = DB::run("SELECT distinct parent_cat FROM categories ORDER BY parent_cat");
        $rows = array();
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
        $TTCache->Set("block/mostactivetorrents_block", $rows, $expires);
    }
    if ($rows) {
        foreach ($rows as $row) {
            $smallname = htmlspecialchars(CutName($row["name"], 20)) . "..."; ?>
            <a href='<?php echo URLROOT; ?>/torrent?id=<?php echo $row["id"]; ?>' title='<?php echo htmlspecialchars($row["name"]); ?>'><?php echo $smallname; ?></a><br>
                <span class="label label-success"> S: <?php echo number_format($row['seeders']); ?></span>
                <span class="label label-warning"> L: <?php echo number_format($row['leechers']); ?></span>
            <br><?php
            }
    } else {
        ?>
	    <p><?php echo Lang::T("NOTHING_FOUND"); ?></p>
        <?php
    }
    Style::block_end();
}