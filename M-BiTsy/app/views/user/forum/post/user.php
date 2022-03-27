<p class="text-center"><a href='<?php echo URLROOT  ?>/profile?id=<?php echo $data['id']  ?>'>Go To Profile</a></p>
<?php
foreach ($data['res'] as $row) {
    // Quick Function To Tidy Results
    $user = userpostdetails($row); ?>
    
    <div class="table">
        <table class="table table-striped ttborder">
        <tr valign="top"> <?php
        if (Users::get('edit_users') == 'no' && $user['privacylevel'] == 'strong') {
           print('<td align="left" width="180"><center><small><a href="' . URLROOT . '/profile?id=' . $row['id'] . '"><b>' . $user['postername'] . '</b></a><br /><i>' . $user['title'] . '</i><img width="80" height="80" src="' . $user['avatar'] . '" alt="" /><br />Uploaded: ---<br />Downloaded: ---<br />Ratio: ' . get_ratio_color('---').'</small><br /><br /><a href="' . URLROOT . '/profile?id=' . $row["user"] . '"><i class="fa fa-user tticon" title="Profile"></i></a> <a href="' . URLROOT . '/message/create?id=' . $row["user"] . '"><i class="fa fa-comment" title="Send PM"></i></a></center></td>');
        } else {
           print('<td align="left" width="180"><center><small><a href="' . URLROOT . '/profile?id=' . $row['id'] . '"><b>' . $user['postername'] . '</b></a><br /><i>' . $user['title'] . '</i><img width="80" height="80" src="' . $user['avatar'] . '" alt="" /><br />Uploaded: ' . $user['useruploaded'] . '<br />Downloaded: ' . $user['userdownloaded'] . '<br />Ratio: ' . get_ratio_color($user['userratio']) . '</small><br /><a href="' . URLROOT . '/profile?id=' . $row["user"] . '"><i class="fa fa-user" title="Profile"></i></a> <a href="/message/create?id=' . $row["user"] . '"><i class="fa fa-comment" title="Send PM"></i></a></center></td>');
        } ?>
       <td>
       <a href="<?php echo URLROOT ?>/topic?topicid=<?php echo $row['topicid']  ?>"><b><?php echo $row['subject'] ?></b></a>  Posted: <?php echo date("d-m-Y \\a\\t H:i:s", TimeDate::utc_to_tz_time($row["added"]))  ?><a id="comment<?php echo $row["id"]  ?>"></a> in <a href="<?php echo URLROOT ?>/forum/view&forumid=<?php echo $row['forumid'] ?>"><b><?php echo $row['name'] ?></b></a>
       <p class="float-end"><?php echo $user['edit'] . $user['delete']  ?><a href="<?php echo URLROOT ?>/report/forum?forumid=<?php echo $row['topicid']  ?>&forumpost=<?php echo $row['id']  ?>"><i class="fa fa-flag" title='Report' aria-hidden="true"></i></a></p><hr />
       <?php echo $user['commenttext']  ?><hr />
       <?php echo $user['usersignature']  ?>
      </td>
      </tr>
    </table>
</div> <?php
}
print($data['pager']);