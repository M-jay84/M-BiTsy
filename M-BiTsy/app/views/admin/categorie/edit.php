<form method='post' action='<?php echo URLROOT ?>/admincategorie/edit?id=<?php echo $data['id'] ?>&amp;save=1'>
<div class="ttform">
<?php
foreach ($data['res'] as $sql){
?>
<div class="text-center">
<b>Parent Category: </b><br>
<input type='text' name='parent_cat' value="<?php echo $sql['parent_cat'] ?>" /><br>All Subcats with EXACTLY the same parent cat are grouped<br>
<b>Sub Category: </b><br>
<input type='text' name='name' value="<?php echo $sql['name'] ?>"><br>
<b>Sort: </b><br>
<input type='text' name='sort_index' value="<?php echo $sql['sort_index'] ?>" /><br>
<b>Image: </b><br>
<input type='text' name='image' value="<?php echo $sql['image'] ?>" ><br>single filename
</div>
<?php
}
?>
    <div class="text-center">
        <input type='submit' class="btn btn-sm ttbtn" value='<?php echo Lang::T("SUBMIT") ?>' />
    </div>

</div>
</form>