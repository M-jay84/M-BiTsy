<form method=post action="<?php echo URLROOT; ?>/admincontact/takecontactanswered">
<div class='table-responsive'><table class='table table-striped'>
<thead>
    <tr>
    <th>Subject</th>
    <th>Sender</th>
    <th>Added</th>
    <th>Answered</th>
    <th>Set Answered</th>
    <th>Del</th>
    </tr>
</thead><tbody>
<?php
while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
    if ($arr['answered']) {
        $answered = "<font color=green><b>Yes - <a href=".URLROOT."/profile?id=$arr[answeredby]><b>" . Users::coloredname($arr['answeredby']) . "</b></a> (<a href=".URLROOT."/admincontact/viewanswer?id=$arr[id]>View Answer</a>)</b></font>";
    } else {
        $answered = "<font color=red><b>No</b></font>";
    } ?>
    <tr>
    <td><a href='<?php echo URLROOT; ?>/admincontact/viewpm?id=<?php echo $arr["id"]; ?>'><b><?php echo $arr['subject']; ?></b></td>
    <td><a href='<?php echo URLROOT; ?>/profile?id=<?php echo $arr['sender'] ?>'><b><?php echo Users::coloredname($arr['sender']); ?></b></a></td>
    <td><?php echo $arr['added']; ?></td><td align=left><?php echo $answered; ?></td>
    <td><input type="checkbox" name="setanswered[]" value="<?php echo $arr['id']; ?>"></td>
    <td><a href='<?php echo URLROOT; ?>/admincontact/deletestaffmessage?id=<?php echo $arr['id']; ?>''>Del</a></td>
    </tr></tbody>
    <?php
} ?>
</table>
</div>

<div class="text-center">
    <button type="submit" class="btn ttbtn btn-sm"><?php echo Lang::T("Confirm"); ?></button>
</div>
</form>