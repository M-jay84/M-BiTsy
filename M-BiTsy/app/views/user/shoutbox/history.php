<div><table class='table table-striped'>
<tr>
<?php
while ($row = $data['sql']->fetch(PDO::FETCH_LAZY)) {
    $ol3 = DB::select('users', 'avatar', ['id'=>$row["userid"]]);
    $av = $ol3['avatar'];
    if (!empty($av)) {
        $av = "<img src='" . $ol3['avatar'] . "' alt='my_avatar' width='20' height='20'>";
    } else {
        $av = "<img src='" . URLROOT . "/assets/images/misc/default_avatar.png' alt='my_avatar' width='20' height='20'>";
    }
    if ($row['userid'] == 0) {
        $av = "<img src='" . URLROOT . "/assets/images/misc/default_avatar.png' alt='default_avatar' width='20' height='20'>";
    }
    ?>

    <tr>
    <td class="shouttable">
    <small class="pull-left time d-none d-sm-block" style="width:99px;font-size:11px"><i class="fa fa-clock-o tticon"></i>&nbsp;<?php echo date('jS M,  g:ia', TimeDate::utc_to_tz_time($row['date'])); ?></small>
    </td>
    <td class="shouttable">
    <a class="pull-left d-none d-sm-block" href="#"><?php echo $av ?></a>
    </td>
    <td class="shouttable">
    <a class="pull-left" href="<?php echo URLROOT ?>/profile?id=<?php echo $row['userid'] ?>" target="_parent">
    <b><?php echo Users::coloredname($row['userid']) ?>:</b></a>&nbsp;
    <?php echo nl2br(format_comment($row['message'])); ?>
    <?php
    if (Users::get("edit_users") == "yes") {
        echo "&nbsp<a href='" . URLROOT . "/shoutbox/delete?id=" . $row['msgid'] . "''><i class='fa fa-remove tticon' ></i></a>&nbsp";
        echo "&nbsp<a href='" . URLROOT . "/shoutbox/edit?id=" . $row['msgid'] . "''><i class='fa fa-pencil tticon' ></i></a>&nbsp";
    }
    if (Users::get("edit_users") == "no" && Users::get('username') == $row['user']) {
        $ts = TimeDate::modify('date', $row['date'], "+1 day");
        if ($ts > TT_DATE) {
            echo "&nbsp<a href='" . URLROOT . "/shoutbox/edit?id=$row[msgid]&user=$row[userid]'><i class='fa fa-pencil tticon' ></i></a>&nbsp";
        }
    }?>
    </td>
    </tr>

    <?php
} ?>

</table></div>