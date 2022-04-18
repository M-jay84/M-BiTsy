<div class="ttform">
<form method="post" action="<?php echo URLROOT; ?>/adminbonus/edit"> <?php
if ($data['row'] != null): ?>
    <input type="hidden" name="id" value="<?php echo $data['row']->id; ?>" /> <?php
endif;?>

<div class="text-center">
    <b>Title:</b>
    <input type='text' name="title" value="<?php echo ($data['row'] != null ? $data['row']->title : null); ?>" size="50" />
</div><br>

<div class="text-center">
    <b>Points:</b>
    <input type='text' name="cost" value="<?php echo ($data['row'] != null ? $data['row']->cost : null); ?>" size="5" />
</div><br>

<div class="text-center">
    <b>Type:</b>
    <select name="type">
        <?php foreach (array('invite', 'traffic', 'VIP', 'other', 'HnR') as $type): ?>
        <option value="<?php echo $type; ?>" <?php echo ($data['row'] != null && $data['row']->type == $type ? 'selected="selected"' : null); ?>><?php echo $type; ?></option>
        <?php endforeach;?>
    </select>
</div><br>

<div class="text-center">
    <b>Value:</b>
    <input type='text' name="value" value="<?php echo ($data['row'] != null ? $data['row']->value : null); ?>" size="10" />
</div><br>

<div class="text-center">
    <b>Description:</b><br>
    <textarea name="descr" rows="5" cols="38"><?php echo ($data['row'] != null ? $data['row']->descr : null); ?></textarea>
</div><br>

<div class="text-center">
    <a href="<?php echo URLROOT; ?>/adminbonus" class="btn btn-sm ttbtn">Cancel</a>
    <input type="submit" class="btn btn-sm ttbtn" value="Submit" />
</div>

</form>
</div>