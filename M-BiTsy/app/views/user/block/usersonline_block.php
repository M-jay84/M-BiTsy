<?php
if ($_SESSION['loggedin'] == true) {
    $TTCache = new Cache();
    $expires = 120; // Cache time in seconds 2 mins
    Style::block_begin(Lang::T("ONLINE_USERS"));
    if (($rows = $TTCache->Get("usersonline_block", $expires)) === false) {
        $res = DB::run("SELECT id, username FROM users WHERE enabled = 'yes' AND status = 'confirmed' AND privacy !='strong' AND UNIX_TIMESTAMP('" . TimeDate::get_date_time() . "') - UNIX_TIMESTAMP(users.last_access) <= 900");
        $rows = array();
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
        $TTCache->Set("usersonline_block", $rows, $expires);
    }

    if (!$rows) { ?>
	   <p class="text-center"><?php echo Lang::T("NO_USERS_ONLINE"); ?></p>
       <?php
    } else {
        for ($i = 0, $cnt = count($rows), $n = $cnt - 1; $i < $cnt; $i++) {
        $row = &$rows[$i];?>
        <a href='<?php echo URLROOT; ?>/profile?id=<?php echo $row["id"]; ?>'><b><?php echo Users::coloredname($row["id"]); ?></b></a><?php echo ($i < $n ? ", " : ""); ?>
	    <?php
        }
    }
    Style::block_end();
}