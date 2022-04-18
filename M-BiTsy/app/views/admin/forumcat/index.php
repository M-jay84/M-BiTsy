<p class=text-center><a href='<?php echo URLROOT ?>/adminforum'><?php echo Lang::T("Add Forum")  ?></a></p>
<p class=text-center><b><?php echo Lang::T("FORUM_CURRENT_CATS") ?>:</b></p>
<div class='table-responsive'>
<table class='table'>
<thead>
<tr>
    <th><b><?php echo Lang::T("ID") ?></b></font></th>
    <th><?php echo Lang::T("NAME") ?></th>
    <th><?php echo Lang::T("SORT") ?></th>
    <th><?php echo Lang::T("EDIT") ?></th>
    <th><?php echo Lang::T("DEL") ?></th>
</tr>
</thead>
<tbody>
<?php
foreach ($data['forumcat'] as $row) { ?>
    <tr>
    <td><font size='2'><b>ID(<?php echo $row['id'] ?>)</b></font></td>
    <td><?php echo $row['name'] ?></td>
    <td><?php echo $row['sort'] ?></td>
    <td><a href='<?php echo URLROOT ?>/adminforumcat/editcat?id=<?php echo $row['id'] ?>'>[<?php echo Lang::T("EDIT")  ?>]</a></td>
    <td><a href='<?php echo URLROOT ?>/adminforumcat/delcat?id=<?php echo $row['id'] ?>'><i class='fa fa-trash-o tticon-red' title='<?php echo Lang::T("FORUM_DELETE_CATEGORY")  ?>'></i></a></td>
    </tr> <?php
}
?>
</tbody>
</table>
</div>

<p class=text-center><b><?php echo Lang::T("Add New Catagorie") ?>:</b></p>
<form action='<?php echo URLROOT ?>/adminforumcat/addcat' method='post'>
<div class='table-responsive'>
<div class='border ttborder'>
<table class='table'>
<tr>
<td class='table_col1'><?php echo Lang::T("FORUM_NAME_OF_NEW_CAT") ?>:</td>
<td class='table_col2' align='right' class='f-form'><input type='text' name='new_forumcat_name' size='60' maxlength='30' /></td>
</tr>
<tr>
<td class='table_col1'><?php echo Lang::T("FORUM_CAT_SORT_ORDER") ?>:</td>
<td class='table_col2' align='right'><input type='text' name='new_forumcat_sort' size='20' maxlength='10' /></td>
</tr>
</table>
</div>
</div><br>
<p class='text-center'>
<input type='submit' class='btn btn-sm ttbtn' value='<?php echo Lang::T("FORUM_ADD_NEW_CAT") ?>' />
<input type='reset' class='btn btn-sm ttbtn' value='<?php echo Lang::T("RESET") ?>' />
</p>
</form>