<?php
if ($_SESSION['loggedin'] == true) {
    Style::block_begin("Latest Requests");
    $TTCache = new Cache();
    $expires = 600; // Cache time in seconds 10 mins
    if (($rows = $TTCache->Get("block/request_block", $expires)) === false) {
        $latestrequestsquery = DB::run("SELECT requests.id, requests.request, categories.name AS cat, categories.id AS catid,
		categories.parent_cat AS parent_cat FROM requests INNER JOIN categories ON requests.cat = categories.id ORDER BY
		requests.id DESC LIMIT 5");
        while ($latestrequestsrecord = $latestrequestsquery->fetch(PDO::FETCH_ASSOC)) {
            $latestrequestsrecords[] = $latestrequestsrecord;
        }
        $TTCache->Set("block/request_block", $rows, $expires);
    }

    if (isset($latestrequestsrecords)) {
        foreach ($latestrequestsrecords as $row) {
            $smallname = htmlspecialchars(CutName($row["request"], 12));
            $smallnamereq = htmlspecialchars(CutName($row["cat"], 4));
            echo "<table cellspacing='0' cellpadding='3' width='100%' border='0'><tr><td width='55%'><small><a style='text-decoration: none;' title='" . $row["parent_cat"] . " : " . $row["cat"] . "'>" . $row["parent_cat"] . " : $smallnamereq </a></small></td><td width='45%'><a style='text-decoration: none;' title='" . $row["request"] . "' href='".URLROOT."/request/index?Section=Request_Details&id=$row[id]'>$smallname</a></td></tr></table> \n";
        }
    } else {
        print("<center>No requests</center> \n");
    }
    Style::block_end();
}