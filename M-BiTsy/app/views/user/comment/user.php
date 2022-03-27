<p class="text-center"><a href='<?php echo URLROOT  ?>/profile?id=<?php echo $data['id']  ?>'>Go To Profile</a></p>
<?php
foreach ($data['res'] as $row) {
    // Quick Function To Tidy Results
    $link = commentlinktitle($row);
    $user = userpostdetails($row, $row['type']); ?>
   <div class="container">
      <div class="row ttborder">
         <div class="col-2 d-none d-sm-block"> <?php
            if (Users::get('edit_users') == 'no' && $user['privacylevel'] == 'strong') {
               print('<td align="left" width="180"><center><small><a href="' . URLROOT . '/profile?id=' . $row['id'] . '"><b>' . $user['postername'] . '</b></a><br /><i>' . $user['title'] . '</i><img width="80" height="80" src="' . $user['avatar'] . '" alt="" /><br />Uploaded: ---<br />Downloaded: ---<br />Ratio: ' . get_ratio_color('---').'</small><br /><br /><a href="' . URLROOT . '/profile?id=' . $row["user"] . '"><i class="fa fa-user tticon" title="Profile"></i></a> <a href="' . URLROOT . '/message/create?id=' . $row["user"] . '"><i class="fa fa-comment" title="Send PM"></i></a></center></td>');
            } else {
               print('<td align="left" width="180"><center><small><a href="' . URLROOT . '/profile?id=' . $row['id'] . '"><b>' . $user['postername'] . '</b></a><br /><i>' . $user['title'] . '</i><img width="80" height="80" src="' . $user['avatar'] . '" alt="" /><br />Uploaded: ' . $user['useruploaded'] . '<br />Downloaded: ' . $user['userdownloaded'] . '<br />Ratio: ' . get_ratio_color($user['userratio']) . '</small><br /><a href="' . URLROOT . '/profile?id=' . $row["user"] . '"><i class="fa fa-user" title="Profile"></i></a> <a href="/message/create?id=' . $row["user"] . '"><i class="fa fa-comment" title="Send PM"></i></a></center></td>');
            } ?>
         </div>
         <div class="col-10">
            <?php echo $link ?>  Posted: <?php echo date("d-m-Y \\a\\t H:i:s", TimeDate::utc_to_tz_time($row["added"]))  ?><a id="comment<?php echo $row["id"]  ?>"></a>
            <p class="float-end">
            <?php echo $user['editcomment'] . $user['deletecomment']  ?></p>
            <hr />
            <?php echo $user['commenttext']  ?>
            <hr />
            <?php echo $user['usersignature']  ?>
         </div>
      </div><br>
   </div> <?php
}
print($data['pager']);