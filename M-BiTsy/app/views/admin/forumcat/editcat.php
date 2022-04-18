<div class='ttform'>
<form action="<?php echo URLROOT; ?>/adminforumcat/saveeditcat" method="post">
<input type="hidden" name="id" value="<?php echo $data['id']; ?>" />

    <div class="form-group row">
        <label for="changed_forumcat" class="col-form-label col-3"><?php echo Lang::T("CP_FORUM_NEW_NAME_CAT"); ?>:</label>
        <div class="col-9">
        <input class="form-control" type="text" name="changed_forumcat" value="<?php echo $data["name"]; ?>" />
        </div>
    </div><br>  

    <div class="form-group row">
        <label for="changed_sortcat" class="col-form-label col-3"><?php echo Lang::T("CP_FORUM_NEW_SORT_ORDER"); ?>:</label>
        <div class="col-9">
        <input class="form-control" type="text" name="changed_sortcat"  value="<?php echo $data["sort"]; ?>"/>
        </div>
    </div><br>

    <div class="text-center">
        <input type='submit' class='btn btn-sm ttbtn'  value="Change" />&nbsp;&nbsp;
	</div>

</form>
</div>