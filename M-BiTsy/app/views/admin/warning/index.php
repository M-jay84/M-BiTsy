<center>
This page displays all users which are enabled and have active warnings, they can be mass deleted or deleted per user.
 Please note that if you delete a warning which was for poor ratio then
this is extending the time user has left to expire.
<?php echo number_format($data['count']); ?> users are warned;
</center><br />
<?php
if ($data['count'] > 0) { ?>
    <br />
    <form id="warned" method="post" action="<?php echo URLROOT; ?>/adminwarning/submit">
    <table class='table table-striped table-bordered table-hover'><thead>
    <tr>
        <th>Username</th>
        <th><?php echo Lang::T("CLASS"); ?></th>
        <th>Added</th>
        <th>Last Access</th>
        <th>Warnings</th>
        <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr></thead><tbody>
    <?php
    while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)): ?>
    <tr>
        <td><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $row["id"]; ?>"><?php echo Users::coloredname($row["id"]); ?></a></td>
        <td><?php echo Groups::get_user_class_name($row["class"]); ?></td>
        <td><?php echo TimeDate::utc_to_tz($row["added"]); ?></td>
        <td><?php echo TimeDate::utc_to_tz($row["last_access"]); ?></td>
        <td><a href="<?php echo URLROOT; ?>/warning?id=<?php echo $row["id"]; ?>"><?php echo number_format(get_row_count("warnings", "WHERE userid = '$row[id]' AND active = 'yes'")); ?></a></td>
        <td><input type="checkbox" name="warned[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php
    endwhile; ?>
    </tbody></table>
    <div class="margin-top20 text-center">
        <input type="submit" class="btn ttbtn" value="Remove Checked" />
        <input type="submit" class="btn ttbtn" name="removeall" value="Remove All" />
	</div>
    </form>
<?php
} else { ?>
    <center><b>No Warned Users...</b></center>
    <?php
}
if ($data['count'] > 25) {
    echo $data['pagerbuttons'];
}