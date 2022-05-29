<form id='addtag' action='<?php echo URLROOT; ?>/admintag/action' method='post'>
<table class='table table-striped table-bordered table-hover'><thead>
<tr>
    <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id)" /></th>
    <th>Name</th>
</tr></thead><tbody> <?php

foreach($data['supertag'] as $tag) { ?>
    <tr>
    <td><input type='checkbox' name='del[]' value='<?php echo $tag['id']; ?>' /></td>
    <td><?php echo $tag["name"]; ?></td>
    <td><?php echo $tag["colour"]; ?></td>
    </tr> <?php
} ?>
</tbody></table>

<div class="text-center">
    <input type='submit' class='btn btn-sm ttbtn' value='Delete' name='delete' />
</div><br><br> 

<div class="text-center">
    Add A New Tag<br>
	<label for="name"><?php echo Lang::T("NAME"); ?>: </label>
	<input id="name" type="text" class="form-control-md" name="name" minlength="3" maxlength="200"><br><br>
	
	<label for="colour"><?php echo Lang::T("Colour"); ?>: </label>
	<input id="colour" type="text" class="form-control-md" name="colour" minlength="3" maxlength="200">
</div><br>

<div class="text-center">
    <button type="submit" class="btn-sm ttbtn" name="addtag" value="addtag">Add Tag</button><br>
</div>

</form>