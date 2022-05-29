<?php
usermenu($data['id']);
foreach ($data['selectuser'] as $selectedid):
if ($selectedid["privacy"] != "strong" || (Users::get("control_panel") == "yes") || (Users::get("id") == $data["id"])) {
?>

<div class="jumbotron">
<div class="row">
    <div class="col-md-2 text-center">
        <img class="embed-responsive" src="<?php echo $data['avatar']; ?>" width="70%" height="200px"><br><br>
        <a href="<?php echo URLROOT ?>/message/create?&amp;id=<?php echo $selectedid["id"] ?>"><button type='button' class='btn btn-sm ttbtn'><?php echo Lang::T("PM") ?></button></a>&nbsp;
		<a href="<?php echo URLROOT ?>/report/user?id=<?php echo $selectedid["id"] ?>"><button type='button' class='btn btn-sm ttbtn'><?php echo Lang::T("REPORT") ?></button></a>&nbsp;
        <?php if (Users::get("edit_users") == "yes") {?>
		<a href="<?php echo URLROOT; ?>/snatch/user?id=<?php echo $selectedid["id"] ?>"><button type='button' class='btn btn-sm ttbtn'><?php echo Lang::T("SNATCHLIST") ?></button></a>
        <?php } ?>
    </div>
    <div class="col-md-4">
        <b><?php echo Lang::T("PROFILE"); ?></b><br>
        <?php echo Lang::T("USERNAME"); ?>: <a href="<?php echo URLROOT ?>/profile?id=<?php echo $selectedid['id'] ?>"><?php echo Users::coloredname($selectedid["id"]) ?></a><br />
	    <?php if (Users::get('class')  > _MODERATOR) { ?>
	    <?php echo Lang::T("EMAIL"); ?>: <?php echo $selectedid["email"]; ?><br />
		<?php echo Lang::T("PASSKEY"); ?>: <?php echo $selectedid["passkey"]; ?><br />
	    <?php echo Lang::T("IP"); ?>: <?php echo $selectedid["ip"]; ?><br />
	    <?php } ?>
		<?php echo Lang::T("USERCLASS"); ?>: <?php echo Groups::get_user_class_name($selectedid["class"]) ?><br />
		<?php echo Lang::T("THEME_NAME"); ?>: <?php echo $selectedid["stylesheet"] ?><br />
		<?php echo Lang::T("TITLE"); ?>: <i><?php echo format_comment($selectedid["title"]) ?></i><br />
		<?php echo Lang::T("JOINED"); ?>: <?php echo htmlspecialchars(TimeDate::utc_to_tz($selectedid["added"])) ?><br />
		<?php echo Lang::T("LAST_VISIT"); ?>: <?php echo htmlspecialchars(TimeDate::utc_to_tz($selectedid["last_access"])) ?><br />
        <?php echo Lang::T("LAST_SEEN"); ?>: <?php echo htmlspecialchars($selectedid["page"]); ?><br />
        <?php
        $res = DB::raw('teams', 'name,image', ['id' =>$selectedid['team']], 'LIMIT 1');
        if ($res->rowCount() == 1) {
             $arr = $res->fetch();
            echo "<br><b>Team Member Of:</b>";
            echo "<img src='" . htmlspecialchars($arr["image"]) . "' alt='' /><br>" . $arr["name"] . "<br /><br /><a href='" . URLROOT . "/team'>[View " . Lang::T("TEAMS") . "]</a>";
        }
        ?>
    </div>
    <div class="col-md-3">
        <b><?php echo Lang::T("ADDITIONAL_INFO"); ?></b><br>
	    <?php echo Lang::T("AGE"); ?>: <?php echo htmlspecialchars($selectedid["age"]) ?><br />
	    <?php echo Lang::T("GENDER"); ?>: <?php echo Lang::T($selectedid["gender"]); ?><br />
		<?php echo Lang::T("CLIENT"); ?>: <?php echo htmlspecialchars($selectedid["client"]) ?><br />
		<?php echo Lang::T("COUNTRY"); ?>: <?php echo $data['country'] ?><br />
		<?php echo Lang::T("DONATED"); ?>  <?php echo Config::get('CURRENCYSYMBOL'); ?><?php echo number_format($selectedid["donated"], 2); ?><br />
		<?php echo Lang::T("WARNINGS"); ?>: <?php echo htmlspecialchars($selectedid["warned"]) ?><br />
        
        <?php if (Users::get("edit_users") == "yes") {
            echo Lang::T("ACCOUNT_PRIVACY_LVL") . ": <b>" . Lang::T($selectedid["privacy"]) . "</b><br />";
        }
        echo Lang::T("SIGNATURE"); ?>:</b> <?php echo format_comment($data['usersignature']); ?><br><br>
        <?php
        if ($selectedid["invited_by"]) {
            $invited = $selectedid['invited_by'];
            $row = DB::raw('users', 'username', ['id' =>$invited])->fetch();
            echo "<b>" . Lang::T("INVITED_BY") . ":</b> <a href=\"" . URLROOT . "/profile?id=$selectedid[invited_by]\">" . Users::coloredname($selectedid['invited_by']) . "</a><br />";
        }
        echo "<b>" . Lang::T("INVITES") . ":</b> " . number_format($selectedid["invites"]) . "<br />";
        $invitees = array_reverse(explode(" ", $selectedid["invitees"]));
        $rows = array();
        foreach ($invitees as $invitee) {
            $res = DB::raw('users', 'id, username', ['id' =>$invitee, 'status'=>'confirmed']);
            if ($row = $res->fetch()) {
                $rows[] = "<a href=\"" . URLROOT . "/profile?id=$row[id]\">" . Users::coloredname($row['id']) . "</a>";
            }
        }
        if ($rows) {
            echo "<b>" . Lang::T("INVITEES") . ":</b> " . implode(", ", $rows) . "<br />";
        } ?>
        [<a href="<?php echo URLROOT ?>/invite/invitetree?id=<?php echo $selectedid["id"] ?>">Invite Tree</a>]<br>

    </div>
    <div class="col-md-3">
        <b><?php echo Lang::T("STATISTICS"); ?></b><br>
        <?php echo Lang::T("UPLOADED"); ?>: <?php echo mksize($selectedid["uploaded"]); ?><br />
		<?php echo Lang::T("DOWNLOADED"); ?>: <?php echo mksize($selectedid["downloaded"]); ?><br />
		<?php echo Lang::T("RATIO"); ?>: <?php echo get_ratio_color($data['userratio']); ?><br />
		<?php echo Lang::T("Seed Bonus"); ?>: <?php echo get_ratio_color($selectedid['seedbonus']); ?><br />
		<?php echo Lang::T("Hit & Run"); ?>: <?php echo $data['numhnr']; ?><br />
		<?php echo Lang::T("AVG_DAILY_DL"); ?>: <?php echo mksize($selectedid["downloaded"] / (TimeDate::DateDiff($selectedid["added"], time()) / 86400)); ?><br />
		<?php echo Lang::T("AVG_DAILY_UL"); ?>: <?php echo mksize($selectedid["uploaded"] / (TimeDate::DateDiff($selectedid["added"], time()) / 86400)); ?><br />

	    <?php echo Lang::T("TORRENTS_POSTED"); ?>: <a href="<?php echo URLROOT; ?>/peer/uploaded?id=<?php echo $selectedid["id"] ?>">
        <?php echo number_format($data['numtorrents']); ?></a><br>

        <?php echo Lang::T("COMMENTS_POSTED"); ?>:  <a href="<?php echo URLROOT; ?>/comment/user?id=<?php echo $selectedid["id"] ?>">
        <?php echo number_format($data['numcomments']); ?></a><br>

	    <?php echo Lang::T("Forum Posts"); ?>: <a href="<?php echo URLROOT; ?>/post/user?id=<?php echo $selectedid["id"] ?>">
        <?php echo number_format($data['numforumposts']); ?></a><br>

        <?php
        if (Users::get("id") != $selectedid["id"]) {
            if ($data['friend'] > 0) {
                print("[<a href=" . URLROOT . "/friend/delete?type=friend&targetid=$selectedid[id]>Remove from Friends</a>]");
            } elseif ($data['block'] > 0) {
                print("&nbsp;[<a href=" . URLROOT . "/friend/delete?type=block&targetid=$selectedid[id]>Remove from Blocked</a>]");
            } else {
                print("[<a href=" . URLROOT . "/friend/add?type=friend&targetid=$selectedid[id]><b>Add to Friends</b></a>]&nbsp;");
                print("&nbsp;[<a href=" . URLROOT . "/friend/add?type=block&targetid=$selectedid[id]><b>Add to Blocked</b></a>]");
            }
        }
        ?>
    </div>
</div>
</div>
<?php
} else {
        Redirect::autolink(URLROOT, "This member has elected to keep their details private!");
}
endforeach;