<br />
<div class='table-responsive'> <table class='table table-striped'><thead><tr>
<th><b>Invited&nbsp;Members</b></th>
<th><b>Class</b></th>
<th><b>Registered</b></th>
<th><b>Last&nbsp;access</b></th>
<th><b>Downloaded</b></th>
<th><b>Uploaded<b></th>
<th><b>Ratio</b></th>
<th><b>Warned</b></th>
</tr></thead> <?php
for ($i = 1; $i <= $data['num']; $i++) {
    $arr = $data['res']->fetch(PDO::FETCH_ASSOC);
    if ($arr["invited_by"] != Users::get('id') && Users::get("class") < 5) {
            Redirect::autolink(URLROOT . "/profile?id=$data[id]", "<b>Access Denied</b>");
    }
    if ($arr['added'] == '0000-00-00 00:00:00') {
        $arr['added'] = '---';
    }
    if ($arr['last_access'] == '0000-00-00 00:00:00') {
        $arr['last_access'] = '---';
    }
    $userratio = $arr["downloaded"] > 0 ? number_format($arr["uploaded"] / $arr["downloaded"], 1) : "---";
    if ($arr["warned"] !== "yes") {
        $warned = "<font color=limegreen><b>No</b></font>";
    } else {
        $warned = "<font color=red><b>Yes</b></font>";
    }
    $class = Groups::get_user_class_name($arr["class"]);
    $added = substr($arr['added'], 0, 10);
    $last_access = substr($arr['last_access'], 0, 10);
    $downloaded = mksize($arr["downloaded"]);
    $uploaded = mksize($arr["uploaded"]);
    ?>
    <tbody><tr>
    <td><a href='<?php echo URLROOT; ?>/profile?id=<?php echo $arr['id'] ?>'><b><?php echo Users::coloredname($arr['id']); ?></b></a></td>
	<td><?php echo $class; ?></td>
	<td><?php echo $added; ?></td>
	<td><?php echo $last_access; ?></td>
	<td><?php echo $downloaded; ?></font></td>
	<td><?php echo $uploaded; ?></font></td>
	<td><?php echo get_ratio_color($userratio); ?></td>
	<td><?php echo $warned; ?></td>
    </tr></tbody>
    </table></div>
    <?php
}

if ($arr["invited_by"] != Users::get('id')) {
    print("<div style='margin-top:10px' align='center'>[<a href=" . URLROOT . "/profile?id=$data[id]><b>Go Back to User Profile</b></a>]</div>");
}
print("<br />");