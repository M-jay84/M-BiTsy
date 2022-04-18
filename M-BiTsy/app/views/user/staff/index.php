<div class="table-responsive"><table class="table">
<?php
while ($row = $data['sql']->fetch(PDO::FETCH_ASSOC)): 
    if (!isset($data['table'][$row["group_id"]])) {
        continue;
    } ?>
<tr><td colspan="14" class="ttblend"><center><b><?php echo Lang::T($row["level"]); ?></b> <?php
if ($row["staff_public"] == "no") {
    echo ("<font color='#ff0000'>[" . Lang::T("HIDDEN FROM PUBLIC") . "]</font>");
} ?>
</td></tr>
<tr>
<?php echo $data['table'][$row["group_id"]]; ?>
</tr>
<?php
endwhile;?>
</table></div>