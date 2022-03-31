<?php
if ($_SESSION['loggedin'] == true) {
	Style::block_begin(Lang::T("NEWEST_MEMBERS"));
    $TTCache = new Cache();
    $expire = 600; // time in seconds
    if (($rows = $TTCache->Get("newestmember_block", $expire)) === false) {
        $res = DB::run("SELECT id, username FROM users WHERE enabled =?  AND status=? AND privacy !=?  ORDER BY id DESC LIMIT 5", ['yes', 'confirmed', 'strong']);
        $rows = array();
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
        $TTCache->Set("newestmember_block", $rows, $expire);
    }

    if (!$rows) { ?>
	   <p class="text-center"><?php echo Lang::T("NOTHING_FOUND"); ?></p>
       <?php
    } else { ?>
		<div>
	    <?php
        foreach ($rows as $row) {?>
			<a href='<?php echo URLROOT; ?>/profile?id=<?php echo $row["id"]; ?>'><?php echo Users::coloredname($row["id"]); ?></a>
	        <?php
        } ?>
		</div>
    <?php
    } 
    Style::block_end();
}