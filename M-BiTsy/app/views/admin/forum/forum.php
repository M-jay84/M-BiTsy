<p class=text-center><a href='<?php echo URLROOT ?>/adminforumcat'><?php echo Lang::T("BACK")  ?></a></p>
<p class=text-center>Current Forum</p>
<div class='table-responsive'>
<table class='table'>
<thead>
<tr>
<th><font size='2'><b><?php echo Lang::T("ID") ?></b></font></th>
<th><?php echo Lang::T("NAME") ?></th>
<th>DESC</th>
<th><?php echo Lang::T("SORT") ?></th>
<th>SubForum</th>
<th>CATEGORY</th>
<th><?php echo Lang::T("EDIT") ?></th>
<th><?php echo Lang::T("DEL") ?></th></tr>
</thead><tbody>
<?php
while ($row = $data['query']->fetch()) {
    foreach ($data['forumcat'] as $cat) {
        if ($cat['id'] == $row['category']) {
            $category = $cat['name'];
        }
    }
    if ($row['sub'] != 0) {
        $getsub = DB::raw('forum_forums', 'name', ['id'=>$row["sub"]])->fetch();
        $row['sub'] = $getsub['name'];
    } else {
        $row['sub'] = 'Parent';
    }
?>
<tr>
<td><font size='2'><b>ID(<?php echo $row['id'] ?>)</b></font></td>
<td><?php echo  $row['name'] ?></td>
<td><?php echo $row['description'] ?></td>
<td><?php echo $row['sort'] ?></td>
<td><?php echo $row['sub'] ?></td>
<td><?php echo $category ?></td>
<td><a href='<?php echo URLROOT ?>/adminforum/editforum?id=<?php echo $row['id'] ?>'>[<?php echo Lang::T("EDIT") ?>]</a></td>
<td><a href='<?php echo URLROOT ?>/adminforum/deleteforum?id=<?php echo $row['id'] ?>'><i class='fa fa-trash-o tticon-red' title='<?php echo Lang::T("FORUM_DELETE_CATEGORY") ?>'></i></a></td>
</tr>
<?php
} ?>
</tbody></table>
</div>

<p class=text-center>Add Forum</p>
<div class='border ttborder'>
<form action='<?php echo URLROOT ?>/adminforum/addforum' method='post'>
<input type='hidden' name='sid' value='<?php echo $sid ?>' />
<input type='hidden' name='action' value='forum' />
<div class='table-responsive'>
<table class='table'>
<tr>
<td class='table_col1'><?php echo Lang::T("CP_FORUM_NEW_NAME") ?>:</td>
<td class='table_col2' align='right'><input type='text' name='new_forum_name' size='90' maxlength='30' /></td>
</tr>
<tr>
<td class='table_col1'><?php echo  Lang::T("CP_FORUM_SORT_ORDER") ?>:</td>
<td class='table_col2' align='right'><input type='text' name='new_forum_sort' size='30' maxlength='10' /></td>
</tr>
<tr>
<td class='table_col1'><?php echo Lang::T("CP_FORUM_NEW_DESC") ?>:</td>
<td class='table_col2' align='right'><textarea cols='50%' rows='5' name='new_desc'></textarea></td>
</tr>
<tr>
<td class='table_col1'><?php echo Lang::T("CP_FORUM_NEW_CAT") ?>:</td>
<td class='table_col2' align='right'>
    <select name='new_forum_cat'> <?php
    foreach ($data['forumcat'] as $row) {
        echo "<option value='{$row['id']}'>{$row['name']}</option>";
    }?>
    </select>
</td>
</tr>
<tr>
<td class='table_col1'><?php echo Lang::T("Sub Forum To (If not sub Forum Leave as none)") ?>:</td>
<td class='table_col2' align='right'>
    <select name='new_forum_forum'> <?php
    echo "<option value=0>None</option>";
    foreach ($data['forumforum'] as $row) {
        echo "<option value='{$row['id']}'>{$row['name']}</option>"; // sub forum mod
    } ?>
    </select>
</td>
</tr>
<tr>
<td class='table_col1'>Mininum Class Needed to Read:</td>
<td class='table_col2' align='right'>
    <select name='minclassread'>
        <option value='<?php echo _USER; ?>'>User</option>
        <option value='<?php echo _POWERUSER; ?>'>Power User</option>
        <option value='<?php echo _VIP; ?>'>VIP</option>
        <option value='<?php echo _UPLOADER; ?>'>Uploader</option>
        <option value='<?php echo _MODERATOR; ?>'>Moderator</option>
        <option value='<?php echo _SUPERMODERATOR; ?>'>Super Moderator</option>
        <option value='<?php echo _ADMINISTRATOR; ?>'>Administrator</option>
    </select>
</td>
</tr>
<tr>
<td class='table_col1'>Mininum Class Needed to Post:</td>
<td class='table_col2' align='right'>
    <select name='minclasswrite'>
        <option value='<?php echo _USER; ?>'>User</option>
        <option value='<?php echo _POWERUSER; ?>'>Power User</option>
        <option value='<?php echo _VIP; ?>'>VIP</option>
        <option value='<?php echo _UPLOADER; ?>'>Uploader</option>
        <option value='<?php echo _MODERATOR; ?>'>Moderator</option>
        <option value='<?php echo _SUPERMODERATOR; ?>'>Super Moderator</option>
        <option value='<?php echo _ADMINISTRATOR; ?>'>Administrator</option>
    </select>
</td>
</tr>
<tr>
<td class='table_col1'><?php echo Lang::T("FORUM_ALLOW_GUEST_READ") ?>:</td>
<td class='table_col2' align='right'><input type="radio" name="guest_read" value="yes" checked='checked' />Yes, <input type="radio" name="guest_read" value="no" />No</td></tr>
<tr>
<th class='table_head' colspan='2' align='center'>
<input type='submit' class='btn btn-sm ttbtn' value='Add new forum' />
<input type='reset' class='btn btn-sm ttbtn' value='<?php echo Lang::T("RESET") ?>' />
</th>
</tr>
</table>
</form>
</div>
</div>