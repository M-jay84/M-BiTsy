<?php
usermenu($data['id'], 'warning');
if ($data['res']->rowCount() > 0) {
    ?>
	<br><center><b>Warnings:</b></center><br>
    <div class='table-responsive'><table class='table table-striped'>
    <thead><tr>
	<th><?php echo Lang::T("ADDED"); ?></th>
	<th><?php echo Lang::T("EXPIRE"); ?></th>
	<th><?php echo Lang::T("REASON"); ?></th>
	<th><?php echo Lang::T("WARNED_BY"); ?></th>
	<th><?php echo Lang::T("TYPE"); ?></th>
	</tr></thead>
    <?php
    while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
        if ($arr["warnedby"] == 0) {
            $wusername = Lang::T("SYSTEM");
        } else {
            $res2 = DB::raw('users', 'id,username', ['id'=>$arr['warnedby']]);
            $arr2 = $res2->fetch();
            $wusername = Users::coloredname($arr['warnedby']);
        }
        $arr['added'] = TimeDate::utc_to_tz($arr['added']);
        $arr['expiry'] = TimeDate::utc_to_tz($arr['expiry']);
        $addeddate = substr($arr['added'], 0, strpos($arr['added'], " "));
        $expirydate = substr($arr['expiry'], 0, strpos($arr['expiry'], " "));
        ?>
        <tbody><tr>
        <td><?php echo $addeddate; ?></td>
        <td><?php echo $expirydate; ?></td>
        <td><?php echo  format_comment($arr['reason']) ; ?></td>
        <td><a href='<?php echo URLROOT ; ?>/profile?id=<?php echo  $arr2['id']; ?>'><?php echo  $wusername; ?></a></td>
        <td><?php echo  $arr['type']; ?></td>
        </tr></tbody>
        <?php
    }
    ?>
    </table></div>
    <?php
} else {
    ?>
    <br><center><b><?php echo   Lang::T("NO_WARNINGS") ;?></b><center><br>
    <?php
}

if (Users::get("edit_users") == "yes" && Users::get("control_panel") == "yes") {
    ?>
    <p class='text-center'><b>Warn User</b></p>
    <div class="ttform">
    <form method='post' action='<?php echo URLROOT; ?>/warning/submit'>
    <input type='hidden' name='userid' value='<?php echo $data['id']; ?>'>
    <div class='text-center'>
    <b><?php echo Lang::T("REASON"); ?>:</b><textarea class="form-control" cols='40' rows='5' name='reason'></textarea>
    <b><?php echo Lang::T("EXPIRE"); ?>:</b><input class="form-control" type='text' size='4' name='expiry' />(days)
    <b><?php echo Lang::T("TYPE"); ?>:</b><input class="form-control" type='text' size='10' name='type' />
    <button type='submit' class='btn btn-sm ttbtn'><b><?php echo Lang::T("ADD_WARNING"); ?></b></button>
    </div>
    </form>
    </div><br>
    <?php
}
if (Users::get("delete_users") == "yes" && Users::get("control_panel") == "yes") {
    ?>
    <p class='text-center'><b>Delete User</b></p>
    <div class="ttform">
    <form method='post' action='<?php echo URLROOT; ?>/profile/delete'>
    <input type='hidden' name='userid' value='<?php echo $data['id']; ?>' />
    <input type='hidden' name='username' value='<?php echo $data["username"]; ?>' />
    <div class='text-center'>
    <b><?php echo Lang::T("REASON"); ?>:</b><input class="form-control" type='text' size='30' name='delreason' />
    <button type='submit' class='btn btn-sm ttbtn'><b><?php echo Lang::T("DELETE_ACCOUNT"); ?></b></button>
    </div>
    </form>
    </div> <br>
    <center><a href='<?php echo URLROOT; ?>/profile?id=<?php echo $data['id']; ?>'><button type='submit' class='btn btn-sm'><b>Back To Account</b></button></a></center>
    <?php
}