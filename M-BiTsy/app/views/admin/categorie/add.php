<div class="ttform">
<form method='post' action='<?php echo  URLROOT ?>/admincategorie/takeadd'>

<div class="text-center">
    <b>Parent Category:</b>
    <input type='text' name='parent_cat' />
</div><br>

<div class="text-center">
    <b>Sub Category:</b>
    <input type='text' name='name' />
</div><br>

<div class="text-center">
    <b>Sort:</b>
    <input type='text' name='sort_index' />
</div><br>

<div class="text-center">
    <b>Image:</b>
    <input type='text' name='image' />
</div><br>

<div class="text-center">
    <input type='submit' class="btn btn-sm ttbtn" value='<?php echo Lang::T("SUBMIT") ?>' />
</div>

</form>
</div>