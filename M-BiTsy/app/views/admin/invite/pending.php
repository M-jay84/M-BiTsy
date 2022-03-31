<center>
This page displays all invited users which have been sent invites but haven't yet activated there account. By deleting a user the inviter will recieve their invite back and any data associated with the invitee will be deleted. <?php echo number_format($data['count']); ?> members are pending;
</center>
<?php
if ($data['count'] > 0): ?>
    <br />
    <form method="post" action="<?php echo URLROOT; ?>/Admininvite/submit">
    <input type="hidden" name="pending" value="1" />
    <table class='table table-striped table-bordered table-hover'><thead>
    <tr>
    <th class="table_head">Username</th>
    <th class="table_head">E-mail</th>
    <th class="table_head">Invited</th>
    <th class="table_head">Invited By</th>
    <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr>
    </thead><tbody>
    <?php
    while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
        <td class="table_col1" align="center"><?php echo Users::coloredname($row["id"]); ?></td>
        <td class="table_col2" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col1" align="center"><?php echo TimeDate::utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $row["invited_by"]; ?>"><?php echo Users::coloredname($row["invited_by"]); ?></a></td>
        <td class="table_col1" align="center"><input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" /></td>
        </tr>
        <?php
    endwhile;?>
    </tbody></table>
    <div class="text-center">
    <input type="submit" class='btn btn-sm ttbtn' value="Delete Checked" />
    </div>
    </form>
    <?php
endif;
if ($data['count'] > 25) {
    echo $data['pagerbuttons'];
}