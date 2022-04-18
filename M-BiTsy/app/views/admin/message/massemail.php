<p class='text-center'>This page allows you to send a mass-email to all members, in the usergroups you choose.</p>
    
<div class=ttform>
    <form name="Form" method="post" action="<?php echo URLROOT; ?>/adminemail">
    <p class='text-center'><strong>Subject:</strong>&nbsp;
    <input type='text' size='30%' maxlength='200' name='subject' /></p>
    
    <div class="form-group row">  <?php
    while ($row = $data['res']->fetch(PDO::FETCH_LAZY)) { ?>
        <label for="clases" class="col-form-label col-3">Send To:</label>
        <div class="col-9">
            <input type="checkbox" name="groups[]" value="<?php echo $row["group_id"]; ?>" /> <?php echo $row["level"]; ?><br>
        </div> <?php
    } ?>
    <input type="checkbox" name="checkall" onclick="checkAll(this.form.id)" /> All
    </div><br>  <?php

    echo textbbcode("Form", "body"); ?>
    <p class='text-center'><input type="submit" class='btn btn-sm ttbtn' value="Send" /></p>
    <p class='text-center'><input type="reset" class='btn btn-sm ttbtn' value="Reset" /></p>

    </form>
</div>