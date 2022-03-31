<p align="justify">This page allows you to prevent individual users or groups of users from accessing your tracker by placing a block on their IP or IP range.<br />
If you wish to temporarily disable an account, but still wish a user to be able to view your tracker, you can use the 'Disable Account' option which is found in the user's profile page.</p><br />
<?php
if ($data['count'] == 0) {
    print("<b>No Bans Found</b><br />\n");
} else { 
    echo $data['pagerbuttons']; ?>
    <form id='ipbans' action='<?php echo URLROOT ?>/adminban/ip?do=del' method='post'>
    <div class='table-responsive'> <table class='table table-striped'><thead><tr>
    <th><?php echo Lang::T("DATE_ADDED") ?></th>
    <th>First IP</th>
    <th>Last IP</th>
    <th><?php echo Lang::T("ADDED_BY") ?></th>
    <th>Comment</th>
    <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
    </tr></thead>
    <?php
    while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) { ?>
        <tbody><tr>
        <td><?php echo date('d/m/Y H:i:s', TimeDate::utc_to_tz_time($arr["added"])) ?></td>
        <td><?php echo $arr['first'] ?></td>
        <td><?php echo $arr['last'] ?></td>
        <td><a href='<?php echo URLROOT ?>/profile?id=<?php echo $arr['addedby'] ?>'><?php echo Users::coloredname($arr['addedby']) ?></a></td>
        <td><?php echo $arr['comment'] ?></td>
        <td><input type='checkbox' name='delids[]' value='<?php echo $arr['id'] ?>' /></td>
        </tr></tbody>
        <?php
    }
    ?>
    </table></div><br />
    <center><input type='submit'  class='btn btn-sm ttbtn' value='Delete Checked' /></center>
    </form>
    <?php
    echo $data['pagerbuttons'];
} ?>
<br />
<div class="ttform">
<form method='post' action='<?php echo URLROOT ?>/adminban/ip?do=add'>
<div class="text-center">
    Ban
    <label for="first">First IP:</label>
	    <input id="first" type="text" class="form-control" name="first" >
    <label for="last">Last IP:</label>
	    <input id="last" type="text" class="form-control" name="last">
    <label for="comment">Comment: </label>
    	<input id="comment" type="text" class="form-control" name="comment">
        <input type='submit'  class='btn btn-sm ttbtn' value='Okay' />
</div>
</form>
</div>